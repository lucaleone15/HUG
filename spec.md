# Spec backend Laravel — Architecture finale
### Campagne Don du Sang · HUG x Partenaires CTS

---

## Architecture générale

Architecture hybride :

- Les pages publiques sont servies par des **controllers Web classiques** → vues Blade qui montent Vue
- Le back-office admin utilise des **endpoints API protégés par Sanctum**
- `routes/web.php` → pages publiques + catch-all Vue
- `routes/api.php` → auth, analytics, stats, admin

---

## Flux global

```
Page co-brandée (/c/{slug})
        ↓
Quiz d'éligibilité (anonyme — session_token)
Quiz chargé depuis resources/quiz/quiz.json
        ↓
    Résultat
   ↙        ↘
NON          OUI
 ↓            ↓
Message      Prise de RDV (lien externe CTS)
bienveillant
```

```
Page admin (/admin)
        ↓
Login Sanctum (email + password + is_admin)
        ↓
Dashboard → Entreprises / Soumissions / KPI / Rapport
```

---

## Quiz — Fichier JSON

Le quiz est **identique pour toutes les entreprises**.
Pas de table en base de données — stocké dans `resources/quiz/quiz.json`.

```php
// QuizController — chargement avec cache permanent
$quiz = Cache::rememberForever('quiz', fn() =>
    json_decode(file_get_contents(resource_path('quiz/quiz.json')), true)
);
```

Structure du fichier :

```json
{
  "title": "Quiz d'éligibilité don du sang",
  "questions": [
    {
      "id": "q1",
      "content": "Avez-vous plus de 18 ans ?",
      "type": "yes_no",
      "order": 1,
      "options": [
        { "id": "q1_oui", "label": "Oui", "is_disqualifying": false },
        { "id": "q1_non", "label": "Non", "is_disqualifying": true }
      ],
      "conditions": []
    },
    {
      "id": "q2",
      "content": "Avez-vous donné du sang il y a moins de 8 semaines ?",
      "type": "yes_no",
      "order": 2,
      "options": [
        { "id": "q2_oui", "label": "Oui", "is_disqualifying": true },
        { "id": "q2_non", "label": "Non", "is_disqualifying": false }
      ],
      "conditions": [
        { "depends_on": "q1", "expects": "q1_oui" }
      ]
    }
  ]
}
```

> `is_disqualifying` n'est jamais exposé au front — le calcul d'éligibilité
> se fait **uniquement côté serveur** dans `QuizController@store`.

---

## Modèles — 6 tables

### 1. `User` — Admin HUG uniquement

```
Champs :
- id              id primary
- name            string
- email           string unique
- password        string (hashed)
- is_admin        boolean default true
- remember_token  string nullable
- timestamps

Traits : HasApiTokens (Sanctum), HasFactory
Casts  : is_admin → boolean
```

Relations :
- `hasMany` ContactRequest (via `processed_by`)
- `hasOne` CampaignStats (via `updated_by`)

---

### 2. `Entreprise`

```
Champs :
- id               id primary
- name             string
- slug             string unique          → URL /c/{slug}
- logo_url         string nullable
- primary_color    string default #E30613
- secondary_color  string nullable
- contact_name     string nullable        → RH référente (Christiane)
- contact_email    string nullable
- employee_count   unsignedInt nullable   → taux de participation
- is_active        boolean default true   → page co-brandée accessible
- is_labelled      boolean default false  → page /label
- trophy_rank      unsignedTinyInt nullable → 1/2/3 = podium, null = non classée
- timestamps

Casts  : is_active → boolean, is_labelled → boolean
Méthode: getRouteKeyName() → 'slug'
```

Relations :
- `hasMany` Submission
- `hasMany` AnalyticsEvent

> **Trophée & Label** : pas de table séparée.
> `trophy_rank` et `is_labelled` mis à jour manuellement par l'admin.
> Pages `/trophee` et `/label` = SELECT filtré sur ces champs.

---

### 3. `Submission` — Événement de conversion central

Aucune donnée personnelle. Aucune réponse stockée. Juste l'événement brut.

```
Champs :
- id              id primary
- session_token   string unique
- entreprise_id   foreignId → cascadeOnDelete
- is_eligible     boolean nullable  → null = quiz non complété
- completed_at    timestamp nullable
- timestamps

Casts  : is_eligible → boolean, completed_at → datetime
Index  : session_token (unique), [entreprise_id, is_eligible] (composé)
```

Relations :
- `belongsTo` Entreprise

> **Règle leaderboard** : ne compter que `is_eligible = true`.
> Les quiz non complétés (`is_eligible = null`) sont exclus.

---

### 4. `AnalyticsEvent` — Tracking KPI

```
Champs :
- id              id primary
- type            enum (voir ci-dessous)
- entreprise_id   foreignId nullable → nullOnDelete
- session_token   string nullable
- metadata        json nullable
- created_at      timestamp only (pas de updated_at)

Index  : [type, entreprise_id]
```

**Types d'événements :**

| Type | Déclenchement | Metadata |
|---|---|---|
| `page_viewed` | Arrivée sur /c/{slug} | `{referrer, device}` |
| `quiz_started` | Clic "Démarrer le test" | `{}` |
| `question_answered` | À chaque question répondue | `{question_index, session_duration_s}` |
| `quiz_abandoned` | Fermeture / timeout | `{last_question_index, total_questions, session_duration_s}` |
| `quiz_completed` | Soumission finale | `{is_eligible, duration_s}` |
| `rdv_clicked` | Clic lien CTS après résultat éligible | `{}` |
| `kit_downloaded` | Téléchargement kit com | `{}` |

**Exemples metadata :**

```json
// page_viewed
{ "referrer": "qr_code", "device": "mobile" }
// referrer : qr_code | email | intranet | direct

// question_answered
{ "question_index": 3, "session_duration_s": 47 }

// quiz_abandoned
{ "last_question_index": 4, "total_questions": 8, "session_duration_s": 63 }

// quiz_completed
{ "is_eligible": true, "duration_s": 120 }
```

Relations :
- `belongsTo` Entreprise

> Envoi **asynchrone et non bloquant** depuis le front (fire & forget → 204).

---

### 5. `ContactRequest` — Contact / Collecte / Candidature Trophée

```
Champs :
- id            id primary
- type          enum : contact | collecte_inscription | trophee_candidature
- name          string
- email         string
- company_name  string nullable
- subject       string nullable
- message       text
- status        enum : pending | processed | rejected  → default pending
- processed_by  foreignId nullable → nullOnDelete (ref users)
- processed_at  timestamp nullable
- timestamps
```

Relations :
- `belongsTo` User (via `processed_by`, nullable)

---

### 6. `CampaignStats` — Stats d'impact campagne

Une seule ligne en base. Chiffres réels CTS/HUG saisis manuellement par Jérémie.

```
Champs :
- id                   id primary
- donations_count      unsignedInt default 0   → dons réels
- lives_saved          unsignedInt default 0   → vies sauvées
- hug_hospitals_count  unsignedInt default 0   → hôpitaux HUG desservis
- updated_by           foreignId nullable → nullOnDelete (ref users)
- updated_at           timestamp
```

Relations :
- `belongsTo` User (via `updated_by`, nullable)

> Les stats calculées (participants, taux) viennent directement
> des Submissions et AnalyticsEvents — pas stockées ici.

---

## Ordre des migrations

```
1. create_users_table
2. create_entreprises_table
3. create_submissions_table
4. create_analytics_events_table
5. create_contact_requests_table
6. create_campaign_stats_table
```

---

## KPI & Dashboard

### Entonnoir principal

| KPI | Calcul |
|---|---|
| Visiteurs landing | `COUNT AnalyticsEvent WHERE type = page_viewed` |
| Quiz démarrés | `COUNT AnalyticsEvent WHERE type = quiz_started` |
| Quiz complétés | `COUNT AnalyticsEvent WHERE type = quiz_completed` |
| Taux de complétion | `quiz_completed / quiz_started` |
| Éligibles | `COUNT Submission WHERE is_eligible = true` |
| Taux d'éligibilité | `eligible / quiz_completed` |
| RDV cliqués | `COUNT AnalyticsEvent WHERE type = rdv_clicked` |
| Taux de conversion final | `rdv_clicked / quiz_completed éligibles` |

### Comportement utilisateur

| KPI | Calcul |
|---|---|
| Taux de rebond landing | `page_viewed` sans `quiz_started` / `page_viewed` |
| Abandon par question | `quiz_abandoned GROUP BY last_question_index` |
| Temps moyen quiz | `AVG duration_s` sur `quiz_completed` |
| Canal d'acquisition | `page_viewed GROUP BY metadata.referrer` |
| Device | `page_viewed GROUP BY metadata.device` |

### Par entreprise

| KPI | Calcul |
|---|---|
| Taux de participation | `quiz_started / employee_count` |
| Taux d'éligibilité | `Submission eligible / Submission total` |
| Classement | `eligible COUNT DESC` |
| Progression J+J | `quiz_started GROUP BY DATE(created_at)` |

### Vue dashboard Jérémie

```
Entonnoir global
─────────────────────────────────────────────────
Page visitée        1 240   100%
Quiz démarré          890    72%   ← taux d'entrée
Quiz complété         610    69%   ← taux de complétion
Éligible              420    69%   ← taux d'éligibilité
RDV cliqué            310    74%   ← taux de conversion final

Abandons par question
─────────────────────────────────────────────────
Q1   5%  ██
Q2   8%  ████
Q3  24%  ████████████   ← question problématique
Q4   6%  ███
```

---

## Controllers

### Web — Blade public

| Controller | Méthodes | Description |
|---|---|---|
| `HomeController` | `index()` | Page d'accueil + stats globales |
| `TropheeController` | `index()` | Vainqueurs (`trophy_rank NOT NULL`) |
| `LabelController` | `index()` | Entreprises labelisées |
| `KitController` | `index()` | Page kit promo |
| `EntrepriseController` | `show(Entreprise $e)` | Landing co-brandée — 404 si inactive |
| `QuizController` | `show()` `store()` `result()` | Quiz JSON + éligibilité serveur + résultat |
| `ContactController` | `index()` `store()` | Formulaire multi-type |

---

### API Publique — sans auth

| Controller | Méthodes | Description |
|---|---|---|
| `Api\AuthController` | `login()` `logout()` `me()` | Auth Sanctum admin |
| `Api\StatsController` | `index()` | Stats globales (CampaignStats + Submissions) |
| `Api\LeaderboardController` | `index()` | Classement entreprises |
| `Api\AnalyticsController` | `store()` | Enregistre un event (fire & forget, 204) |

---

### API Admin — `[auth:sanctum + middleware:admin]`

| Controller | Méthodes | Description |
|---|---|---|
| `Api\Admin\DashboardController` | `index()` | KPI globaux + entonnoir |
| `Api\Admin\EntrepriseController` | CRUD + `sendKit()` | Gestion entreprises + envoi kit |
| `Api\Admin\SubmissionController` | `index()` `show()` | Lecture seule |
| `Api\Admin\ContactRequestController` | `index()` `show()` `update()` `convert()` | Traitement demandes |
| `Api\Admin\AnalyticsController` | `index()` | Dashboard métriques — entonnoir, abandons |
| `Api\Admin\CampaignStatsController` | `show()` `update()` | Stats d'impact |
| `Api\Admin\ReportController` | `show()` | Bilan PDF par entreprise |

---

## API Resources

| Resource | Champs exposés |
|---|---|
| `UserResource` | `id`, `name`, `email` |
| `EntrepriseResource` | `id`, `name`, `slug`, `logo_url`, `primary_color`, `secondary_color`, `employee_count`, `is_labelled`, `trophy_rank` |
| `SubmissionResource` | `id`, `is_eligible`, `completed_at`, entreprise résumée |
| `LeaderboardResource` | classement avec nb éligibles + taux de participation |
| `CampaignStatsResource` | `donations_count`, `lives_saved`, `hug_hospitals_count` + stats calculées |

---

## Routes

### `web.php`

```php
Route::get('/',                          [HomeController::class, 'index']);
Route::get('/trophee',                   [TropheeController::class, 'index']);
Route::get('/label',                     [LabelController::class, 'index']);
Route::get('/kit-promo',                 [KitController::class, 'index']);
Route::get('/contact',                   [ContactController::class, 'index']);
Route::post('/contact',                  [ContactController::class, 'store']);

Route::get('/c/{entreprise}',            [EntrepriseController::class, 'show']);
Route::get('/c/{entreprise}/quiz',       [QuizController::class, 'show']);
Route::post('/c/{entreprise}/quiz',      [QuizController::class, 'store']);
Route::get('/c/{entreprise}/quiz/result',[QuizController::class, 'result']);

Route::get('/{any}', fn() => view('app'))->where('any', '.*');
```

### `api.php`

```php
// Auth
Route::post('/auth/login', [Api\AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [Api\AuthController::class, 'logout']);
    Route::get('/auth/me',     [Api\AuthController::class, 'me']);
});

// Public
Route::get('/leaderboard', [Api\LeaderboardController::class, 'index']);
Route::get('/stats',       [Api\StatsController::class, 'index']);
Route::post('/analytics',  [Api\AnalyticsController::class, 'store']);

// Admin
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [Admin\DashboardController::class, 'index']);

    // Entreprises
    Route::apiResource('entreprises', Admin\EntrepriseController::class);
    Route::post('entreprises/{entreprise}/send-kit',
                [Admin\EntrepriseController::class, 'sendKit']);

    // Soumissions (lecture seule)
    Route::get('submissions',         [Admin\SubmissionController::class, 'index']);
    Route::get('submissions/{submission}', [Admin\SubmissionController::class, 'show']);

    // Demandes de contact
    Route::get('contact-requests',              [Admin\ContactRequestController::class, 'index']);
    Route::get('contact-requests/{cr}',         [Admin\ContactRequestController::class, 'show']);
    Route::put('contact-requests/{cr}',         [Admin\ContactRequestController::class, 'update']);
    Route::post('contact-requests/{cr}/convert',[Admin\ContactRequestController::class, 'convert']);

    // Analytics & KPI
    Route::get('analytics', [Admin\AnalyticsController::class, 'index']);

    // Stats d'impact
    Route::get('campaign-stats', [Admin\CampaignStatsController::class, 'show']);
    Route::put('campaign-stats', [Admin\CampaignStatsController::class, 'update']);

    // Rapport bilan
    Route::get('report', [Admin\ReportController::class, 'show']);
});
```

---

## Authentification Sanctum

```php
// User model
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable {
    use HasApiTokens, HasFactory;
}

// AuthController@login
$user = User::where('email', $request->email)->firstOrFail();
if (!Hash::check($request->password, $user->password)) {
    return response()->json(['message' => 'Identifiants invalides'], 401);
}
if (!$user->is_admin) {
    return response()->json(['message' => 'Accès réservé aux administrateurs'], 403);
}
return response()->json(['token' => $user->createToken('admin-token')->plainTextToken]);

// Middleware admin — app/Http/Middleware/EnsureUserIsAdmin.php
public function handle($request, $next) {
    if (!$request->user()?->is_admin) {
        return response()->json(['message' => 'Forbidden'], 403);
    }
    return $next($request);
}
```

---

## session_token — fonctionnement

```php
// QuizController@show — démarrage du quiz
$token = (string) Str::id();
session(['quiz_token' => $token]);

// QuizController@store — soumission
// 1. Charger le quiz depuis le cache
$quiz = Cache::rememberForever('quiz', fn() =>
    json_decode(file_get_contents(resource_path('quiz/quiz.json')), true)
);
// 2. Calculer l'éligibilité côté serveur uniquement
$isEligible = collect($quiz['questions'])->every(function($q) use ($answers) {
    $chosen = $answers[$q['id']] ?? null;
    return collect($q['options'])
        ->where('id', $chosen)
        ->where('is_disqualifying', false)
        ->isNotEmpty();
});
// 3. Créer la Submission — aucune réponse stockée
Submission::create([
    'session_token' => session('quiz_token'),
    'entreprise_id' => $entreprise->id,
    'is_eligible'   => $isEligible,
    'completed_at'  => now(),
]);

// QuizController@result — protection accès direct
$submission = Submission::where('session_token', session('quiz_token'))
                        ->firstOrFail();
```

---

## StatsController@index

```php
$stats = CampaignStats::first();

return response()->json([
    // Saisies par l'admin (données réelles CTS/HUG)
    'donations_count'   => $stats->donations_count,
    'lives_saved'       => $stats->lives_saved,
    'hug_hospitals'     => $stats->hug_hospitals_count,

    // Calculées depuis Submission
    'participants'      => Submission::where('is_eligible', true)->count(),

    // Calculées depuis Entreprise
    'entreprises_count' => Entreprise::where('is_active', true)->count(),
    'labelled_count'    => Entreprise::where('is_labelled', true)->count(),
]);
```

---

## Points d'attention

- `POST /api/analytics` répond toujours `204 No Content` — ne jamais bloquer le front en cas d'erreur
- Le calcul `is_eligible` se fait **uniquement dans `QuizController@store`** — jamais côté Vue
- Ne **jamais exposer `is_disqualifying`** au front (il reste dans le fichier JSON serveur uniquement)
- `QuizController@result` retourne 404 si pas de `session('quiz_token')` valide en session
- `ContactRequestController@convert` crée une Entreprise depuis une candidature collecte
- Vider `session('quiz_token')` après rattachement ou expiration
- Seeder : 1 User admin, 1 CampaignStats initialisée à zéro, 2-3 Entreprises de démo
- Installer Sanctum : `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
- Configurer CORS dans `config/cors.php` pour le domaine Vue admin
