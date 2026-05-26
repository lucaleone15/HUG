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
bienveillant  + date de collecte (rdv_date)
 ↓
Bloc parrainage :
  - copier le lien /c/{slug}
  - copier le message (email)
  - partager sur WhatsApp
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

> `is_disqualifying` n'est jamais exposé au front — le calcul d'éligibilité
> se fait **uniquement côté serveur** dans `QuizController@store`.

---

## Modèles — 5 tables

### 1. `User` — Admin HUG uniquement

```
Champs :
- id                  bigInt primary (auto-increment)
- name                string
- email               string unique
- password            string (hashed)
- email_verified_at   timestamp nullable
- is_admin            boolean default false
- remember_token      string nullable
- timestamps

Traits : HasApiTokens (Sanctum), HasFactory, Notifiable
Casts  : is_admin → boolean, password → hashed, email_verified_at → datetime
```

Relations :

- `hasOne` CampaignStats (via `updated_by`)

---

### 2. `Entreprise`

```
Champs :
- id               bigInt primary (auto-increment)
- name             string
- slug             string unique          → URL /c/{slug}
- logo_url         string nullable
- primary_color    string default #E30613
- secondary_color  string nullable
- contact_name     string nullable
- contact_email    string nullable
- employee_count   unsignedInt nullable
- is_active        boolean default true
- is_labelled      boolean default false
- is_validated     boolean default false
- trophy_rank      unsignedTinyInt nullable → 1/2/3 = podium, null = non classée
- wants_trophy     boolean default false  → participation volontaire au trophée
- rdv_url          string nullable        → lien de réservation CTS
- rdv_date         date nullable          → date de la collecte
- type             enum nullable → banque | assurance | industrie | commerce | service | technologie | sante | education | autre
- timestamps

Casts  : is_active → boolean, is_labelled → boolean, is_validated → boolean,
         wants_trophy → boolean, trophy_rank → integer, rdv_date → date:Y-m-d
Méthode: getRouteKeyName() → 'slug'
         participatesInTrophy() → bool  (trophy_rank !== null && > 0)
         isLauréat() → bool             (trophy_rank in [1, 2, 3])
         getTrophyNameAttribute() → ?string ('Or' | 'Argent' | 'Bronze' | null)
```

Relations :

- `hasMany` Submission
- `hasMany` AnalyticsEvent

---

### 3. `Submission` — Événement de conversion central

Aucune donnée personnelle. Aucune réponse stockée. Juste l'événement brut.

```
Champs :
- id              bigInt primary (auto-increment)
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
- id              bigInt primary (auto-increment)
- type            enum (voir ci-dessous)
- entreprise_id   foreignId nullable → nullOnDelete
- session_token   string nullable
- metadata        json nullable
- timestamps

Index  : [type, entreprise_id], session_token
```

**Types d'événements :**

| Type                | Déclenchement                         | Metadata                                                     |
| ------------------- | ------------------------------------- | ------------------------------------------------------------ |
| `page_viewed`       | Arrivée sur /c/{slug}                 | `{referrer, device}`                                         |
| `quiz_started`      | Clic "Démarrer le test"               | `{}`                                                         |
| `question_answered` | À chaque question répondue            | `{question_index, session_duration_s}`                       |
| `quiz_abandoned`    | Fermeture / timeout                   | `{last_question_index, total_questions, session_duration_s}` |
| `quiz_completed`    | Soumission finale                     | `{is_eligible, duration_s}`                                  |
| `rdv_clicked`       | Clic lien CTS après résultat éligible | `{}`                                                         |
| `kit_downloaded`    | Téléchargement kit com                | `{}`                                                         |

Relations :

- `belongsTo` Entreprise

> Envoi **asynchrone et non bloquant** depuis le front (fire & forget → 204).

---

### 5. `CampaignStats` — Stats d'impact campagne

Une seule ligne en base. Chiffres réels CTS/HUG saisis manuellement.

```
Champs :
- id                   bigInt primary (auto-increment)
- donations_count      unsignedInt default 0
- lives_saved          unsignedInt default 0
- hug_hospitals_count  unsignedInt default 0
- updated_by           foreignId nullable → nullOnDelete (ref users)
- timestamps

Casts  : donations_count → integer, lives_saved → integer, hug_hospitals_count → integer
```

Relations :

- `belongsTo` User (via `updated_by`, nullable)

> Utiliser `CampaignStats::getInstance()` — jamais `::first()` directement.

---

## Ordre des migrations

```
1. create_users_table                        ✅
2. add_is_admin_to_users_table               ✅
3. create_personal_access_tokens_table       ✅
4. create_entreprises_table                  ✅
5. create_submissions_table                  ✅
6. create_analytics_events_table             ✅
7. create_campaign_stats_table               ✅
8. add_trophy_and_rdv_to_entreprises_table   ✅  (wants_trophy, rdv_url, rdv_date)
```

---

## KPI & Dashboard

### Entonnoir principal

| KPI                      | Calcul                                             |
| ------------------------ | -------------------------------------------------- |
| Visiteurs landing        | `COUNT AnalyticsEvent WHERE type = page_viewed`    |
| Quiz démarrés            | `COUNT AnalyticsEvent WHERE type = quiz_started`   |
| Quiz complétés           | `COUNT AnalyticsEvent WHERE type = quiz_completed` |
| Taux de complétion       | `quiz_completed / quiz_started`                    |
| Éligibles                | `COUNT Submission WHERE is_eligible = true`        |
| Taux d'éligibilité       | `eligible / quiz_completed`                        |
| RDV cliqués              | `COUNT AnalyticsEvent WHERE type = rdv_clicked`    |
| Taux de conversion final | `rdv_clicked / quiz_completed éligibles`           |

### Comportement utilisateur

| KPI                    | Calcul                                            |
| ---------------------- | ------------------------------------------------- |
| Taux de rebond landing | `page_viewed` sans `quiz_started` / `page_viewed` |
| Abandon par question   | `quiz_abandoned GROUP BY last_question_index`     |
| Temps moyen quiz       | `AVG duration_s` sur `quiz_completed`             |
| Canal d'acquisition    | `page_viewed GROUP BY metadata.referrer`          |
| Device                 | `page_viewed GROUP BY metadata.device`            |

### Par entreprise

| KPI                   | Calcul                                   |
| --------------------- | ---------------------------------------- |
| Taux de participation | `quiz_started / employee_count`          |
| Taux d'éligibilité    | `Submission eligible / Submission total` |
| Classement            | `eligible COUNT DESC`                    |
| Progression J+J       | `quiz_started GROUP BY DATE(created_at)` |

---

## Controllers

### Web — Blade public

| Controller              | Méthodes                      | Description                                |
| ----------------------- | ----------------------------- | ------------------------------------------ |
| `HomeController`        | `index()`                     | Page d'accueil + stats globales            |
| `TropheeController`     | `index()`                     | Vainqueurs (`trophy_rank NOT NULL`)        |
| `LabelController`       | `index()`                     | Entreprises labelisées                     |
| `KitController`         | `index()`                     | Page kit promo                             |
| `EntrepriseController`  | `show(Entreprise $e)`         | Landing co-brandée — 404 si inactive       |
| `QuizController`        | `show()` `store()` `result()` | Quiz JSON + éligibilité serveur + résultat |
| `ContactController`     | `index()` `store()`           | Formulaire multi-type → envoie `ContactFormMail` |
| `InscriptionController` | `index()` `store()`           | Formulaire d'inscription entreprise partenaire   |

---

### API Publique — sans auth

| Controller                  | Méthodes                    | Description                                  |
| --------------------------- | --------------------------- | -------------------------------------------- |
| `Api\AuthController`        | `login()` `logout()` `me()` | Auth Sanctum admin                           |
| `Api\StatsController`       | `index()`                   | Stats globales (CampaignStats + Submissions) |
| `Api\LeaderboardController` | `index()`                   | Classement entreprises                       |
| `Api\AnalyticsController`   | `store()`                   | Enregistre un event (fire & forget, 204)     |

---

### API Admin — `[auth:sanctum + middleware:admin]`

| Controller                          | Méthodes            | Description                               |
| ----------------------------------- | ------------------- | ----------------------------------------- |
| `Api\Admin\DashboardController`     | `index()`           | KPI globaux + entonnoir                   |
| `Api\Admin\EntrepriseController`    | CRUD + `sendKit()` (TODO) + `sendLink()` | Gestion entreprises — `sendLink` envoie `CompanyConfirmationLink` lors de la validation |
| `Api\Admin\SubmissionController`    | `index()` `show()`  | Lecture seule                             |
| `Api\Admin\AnalyticsController`     | `index()`           | Dashboard métriques — entonnoir, abandons |
| `Api\Admin\CampaignStatsController` | `show()` `update()` | Stats d'impact                            |
| `Api\Admin\ReportController`        | `show()`            | Bilan par entreprise — JSON ou PDF (`?format=pdf` via dompdf) |

---

## API Resources

| Resource                | Champs exposés                                                                                                                               |
| ----------------------- | -------------------------------------------------------------------------------------------------------------------------------------------- |
| `UserResource`          | `id`, `name`, `email`                                                                                                                        |
| `EntrepriseResource`    | `id`, `name`, `slug`, `logo_url`, `primary_color`, `secondary_color`, `employee_count`, `is_labelled`, `is_validated`, `trophy_rank`, `type` |
| `SubmissionResource`    | `id`, `is_eligible`, `completed_at`, entreprise résumée                                                                                      |
| `LeaderboardResource`   | classement avec nb éligibles + taux de participation                                                                                         |
| `CampaignStatsResource` | `donations_count`, `lives_saved`, `hug_hospitals_count` + stats calculées                                                                    |

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

Route::get('/inscription',               [InscriptionController::class, 'index']);
Route::post('/inscription',              [InscriptionController::class, 'store']);

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

    Route::apiResource('entreprises', Admin\EntrepriseController::class);
    Route::post('entreprises/{id}/send-kit',
                [Admin\EntrepriseController::class, 'sendKit']);   // TODO
    Route::post('entreprises/{id}/send-link',
                [Admin\EntrepriseController::class, 'sendLink']);  // Renvoie le lien de confirmation

    Route::get('submissions',              [Admin\SubmissionController::class, 'index']);
    Route::get('submissions/{submission}', [Admin\SubmissionController::class, 'show']);

    Route::get('analytics',      [Admin\AnalyticsController::class, 'index']);
    Route::get('campaign-stats', [Admin\CampaignStatsController::class, 'show']);
    Route::put('campaign-stats', [Admin\CampaignStatsController::class, 'update']);
    Route::get('report',         [Admin\ReportController::class, 'show']);
});
```

---

## Authentification Sanctum

```php
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
$token = (string) Str::uuid();
session(['quiz_token' => $token]);

// QuizController@store — soumission
$quiz = Cache::rememberForever('quiz', fn() =>
    json_decode(file_get_contents(resource_path('quiz/quiz.json')), true)
);
$isEligible = collect($quiz['questions'])->every(function($q) use ($answers) {
    $chosen = $answers[$q['id']] ?? null;
    return collect($q['options'])
        ->where('id', $chosen)
        ->where('is_disqualifying', false)
        ->isNotEmpty();
});
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
$stats = CampaignStats::getInstance();

return response()->json([
    'donations_count'   => $stats->donations_count,
    'lives_saved'       => $stats->lives_saved,
    'hug_hospitals'     => $stats->hug_hospitals_count,
    'participants'      => Submission::where('is_eligible', true)->count(),
    'entreprises_count' => Entreprise::where('is_active', true)->count(),
    'labelled_count'    => Entreprise::where('is_labelled', true)->count(),
]);
```

---

## Points d'attention

- `POST /api/analytics` répond toujours `204 No Content` — ne jamais bloquer le front en cas d'erreur
- Le calcul `is_eligible` se fait **uniquement dans `QuizController@store`** — jamais côté Vue
- Ne **jamais exposer `is_disqualifying`** au front
- `QuizController@result` retourne 404 si pas de `session('quiz_token')` valide
- Vider `session('quiz_token')` après rattachement ou expiration
- Utiliser `CampaignStats::getInstance()` — jamais `::first()` directement
- CORS : l'admin SPA est servi sur la même origine — aucune config CORS nécessaire en production
- **Emails** : utiliser `->locale($locale)` sur le Mailable (pas `app()->setLocale()`). Locales valides : `fr`, `de`, `it`, `en`. Envoi synchrone (`Mail::send`) car Infomaniak ne supporte pas les workers persistants.
- `sendKit()` → TODO (non implémenté) ; `sendLink()` → envoie `CompanyConfirmationLink` lors de la validation ou à la demande
- **Seeder dev** (`DatabaseSeeder`) : 12 entreprises (5 avec trophée + rdv, 3 labelisées, 2 validées, 2 en attente), données analytics réalistes. **Seeder prod** (`ProductionSeeder`) : uniquement `CampaignStats::getInstance()` — admin créé via `php artisan app:create-admin`
- **Cache quiz** : `Cache::rememberForever('quiz', ...)` → invalider manuellement avec `php artisan cache:forget quiz` après modification de `quiz.json`
- **PDF rapport** : `ReportController@show?format=pdf` → `barryvdh/laravel-dompdf`, template `resources/views/pdf/report.blade.php` (HTML tabulaire, pas de flexbox/grid)
