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

## Modèles — 6 tables

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
- slug             string unique          → URL lisible, généré à l'inscription
- access_token     string unique          → clé de route publique /c/{access_token} (48 chars random)
- logo_url         string nullable
- primary_color    string default #E30613
- secondary_color  string nullable
- contact_name     string nullable
- contact_email    string nullable
- employee_count   unsignedInt nullable
- is_active        boolean default true
- is_labelled      boolean default false
- is_validated     boolean default false
- is_public        boolean default true   → visible dans trophée/leaderboard public
- trophy_rank      unsignedTinyInt nullable → 1/2/3 = podium, null = non classée
- wants_trophy     boolean default false  → participation volontaire au trophée
- rdv_url          string nullable        → lien de réservation CTS (legacy, voir Collecte)
- rdv_date         date nullable          → date collecte (legacy, voir Collecte)
- type             enum nullable → banque | assurance | industrie | commerce | service | technologie | sante | education | autre
- locale           string nullable        → locale email (fr | de | it | en)
- timestamps

Casts  : is_active → boolean, is_labelled → boolean, is_validated → boolean,
         is_public → boolean, wants_trophy → boolean, trophy_rank → integer,
         rdv_date → date:Y-m-d
Méthode: getRouteKeyName() → 'access_token'   ← route key = access_token, pas slug
         participatesInTrophy() → bool  (trophy_rank !== null && > 0)
         isLauréat() → bool             (trophy_rank in [1, 2, 3])
         getTrophyNameAttribute() → ?string ('Or' | 'Argent' | 'Bronze' | null)
         activeCollecte() → HasOne (Collecte active la plus récente)
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

### 6. `Collecte` — Campagne OnDoc par entreprise

Une entreprise peut avoir plusieurs collectes. Chaque collecte correspond à un lien OnDoc généré pour une campagne.

```
Champs :
- id              bigInt primary (auto-increment)
- entreprise_id   foreignId → cascadeOnDelete
- ondoc_url       string                 → lien de prise de RDV (OnDoc)
- rdv_date        date nullable          → date de la collecte
- label           string nullable        → ex : "Site Plan-les-Ouates"
- is_active       boolean default true   → une seule collecte active par entreprise en pratique
- timestamps

Index : [entreprise_id, is_active]
```

Relations :

- `belongsTo` Entreprise

> `Entreprise::activeCollecte()` retourne la collecte active la plus récente via `hasOne(...)->where('is_active', true)->latestOfMany()`.
> La page publique `/c/{slug}` utilise cette collecte ; sans collecte active, la section RDV est masquée (message "à venir").

---

## Ordre des migrations

```
1.  create_users_table                        ✅
2.  add_is_admin_to_users_table               ✅
3.  create_personal_access_tokens_table       ✅
4.  create_entreprises_table                  ✅
5.  create_submissions_table                  ✅
6.  create_analytics_events_table             ✅
7.  create_campaign_stats_table               ✅
8.  add_trophy_and_rdv_to_entreprises_table   ✅  (wants_trophy, rdv_url, rdv_date)
9.  add_locale_to_entreprises_table           ✅  (locale : fr | de | it | en)
10. add_is_public_to_entreprises_table        ✅  (is_public : boolean default true)
11. add_access_token_to_entreprises_table     ✅  (access_token : string unique, 48 chars)
12. create_collectes_table                    ✅  (ondoc_url, rdv_date, label, is_active)
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

| Controller                | Méthodes                      | Description                                                  |
| ------------------------- | ----------------------------- | ------------------------------------------------------------ |
| `HomeController`          | `index()`                     | Page d'accueil + stats globales                              |
| `TropheeController`       | `index()`                     | Vainqueurs (`trophy_rank NOT NULL`)                          |
| `LabelController`         | `index()`                     | Entreprises labelisées                                       |
| `KitController`           | `index()`                     | Page kit promo                                               |
| `EntrepriseController`    | `show(Entreprise $e)`         | Landing co-brandée — 404 si inactive. Résout par `access_token`. |
| `QuizController`          | `show()` `store()` `result()` | Quiz JSON multi-locale + éligibilité serveur + résultat. Gère `yes_no`, `checklist`, `travel_check`, `birth_check` avec conditions de dépendance. |
| `ContactController`       | `index()` `store()`           | Formulaire multi-type → envoie `ContactFormMail`             |
| `InscriptionController`   | `index()` `store()`           | Formulaire d'inscription → envoie `NewRegistrationNotification` à `info@donnez-votre-sang.ch` |
| `ReportPreviewController` | `show(string $token)`         | Preview rapport PDF via cache temporaire (`report_preview:{token}`) |

---

### API Publique — sans auth

| Controller                  | Méthodes  | Description                                  |
| --------------------------- | --------- | -------------------------------------------- |
| `Api\AuthController`        | `login()` | Authentification admin — retourne un token Sanctum |
| `Api\StatsController`       | `index()` | Stats globales (CampaignStats + Submissions) |
| `Api\LeaderboardController` | `index()` | Classement entreprises                       |
| `Api\AnalyticsController`   | `store()` | Enregistre un event (fire & forget, 204)     |

### API Auth — `[auth:sanctum]` uniquement

| Controller           | Méthodes           | Description                        |
| -------------------- | ------------------ | ---------------------------------- |
| `Api\AuthController` | `logout()` `me()`  | Révoque le token / retourne l'user |

---

### API Admin — `[auth:sanctum + middleware:admin]`

| Controller                          | Méthodes                          | Description                               |
| ----------------------------------- | --------------------------------- | ----------------------------------------- |
| `Api\Admin\DashboardController`     | `index()`                         | KPI globaux + entonnoir                   |
| `Api\Admin\EntrepriseController`    | `index()` `show()` `store()` `update()` `destroy()` `sendKit()` (TODO) `sendLink()` | Gestion entreprises. Update via `POST` multipart (logo). `sendLink` envoie `CompanyConfirmationLink`. |
| `Api\Admin\CollecteController`      | `index()` `store()` `update()` `destroy()` | Collectes OnDoc par entreprise          |
| `Api\Admin\TropheeController`       | `index()` `reorder()`             | Classement trophée — liste + réordonnancement |
| `Api\Admin\SubmissionController`    | `index()` `show()`                | Lecture seule                             |
| `Api\Admin\AnalyticsController`     | `index()`                         | Dashboard métriques — entonnoir, abandons |
| `Api\Admin\CampaignStatsController` | `show()` `update()`               | Stats d'impact                            |
| `Api\Admin\ReportController`        | `show()`                          | Bilan par entreprise — JSON ou PDF (`?format=pdf` via dompdf). Expose `logo_data_uri` (PNG base64 via Imagick). |
| `Api\Admin\UserController`          | `index()` `store()` `destroy()`   | Gestion des comptes admin. Suppression bloquée pour son propre compte. |

---

## API Resources

| Resource                  | Champs exposés                                                                                                                               |
| ------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------- |
| `UserResource`            | `id`, `name`, `email`                                                                                                                        |
| `EntrepriseResource`      | `id`, `name`, `slug`, `logo_url`, `primary_color`, `secondary_color`, `employee_count`, `is_labelled`, `is_validated`, `trophy_rank`, `type` |
| `AdminEntrepriseResource` | Hérite de `EntrepriseResource` + `contact_name`, `contact_email`, `is_public`, `access_token`, `wants_trophy`, `rdv_url`, `rdv_date`, `eligible_count`, `submission_count` |
| `SubmissionResource`      | `id`, `is_eligible`, `completed_at`, entreprise résumée                                                                                      |
| `LeaderboardResource`     | classement avec nb éligibles + taux de participation                                                                                         |
| `CampaignStatsResource`   | `donations_count`, `lives_saved`, `hug_hospitals_count` + stats calculées                                                                    |

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

// Résolution par access_token (getRouteKeyName = 'access_token')
Route::get('/c/{entreprise}',            [EntrepriseController::class, 'show']);
Route::get('/c/{entreprise}/quiz',       [QuizController::class, 'show']);
Route::post('/c/{entreprise}/quiz',      [QuizController::class, 'store']);
Route::get('/c/{entreprise}/quiz/result',[QuizController::class, 'result']);

// Preview rapport PDF (lien temporaire via cache)
Route::get('/report-preview/{token}',   [ReportPreviewController::class, 'show']);

Route::get('/{any}', fn() => view('app'))->where('any', '.*');
```

### `api.php`

```php
// Auth
Route::post('/auth/login', [Api\AuthController::class, 'login'])->middleware('throttle:10,1');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [Api\AuthController::class, 'logout']);
    Route::get('/auth/me',     [Api\AuthController::class, 'me']);
});

// Public
Route::get('/leaderboard', [Api\LeaderboardController::class, 'index'])->middleware('throttle:60,1');
Route::get('/stats',       [Api\StatsController::class, 'index'])->middleware('throttle:60,1');
Route::post('/analytics',  [Api\AnalyticsController::class, 'store'])->middleware('throttle:120,1');

// Admin
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', [Admin\DashboardController::class, 'index']);

    // Entreprises — liaison par ID (pas par access_token) dans l'admin
    Route::get('entreprises',         [Admin\EntrepriseController::class, 'index']);
    Route::post('entreprises',        [Admin\EntrepriseController::class, 'store']);
    Route::get('entreprises/{id}',    [Admin\EntrepriseController::class, 'show']);
    Route::post('entreprises/{id}',   [Admin\EntrepriseController::class, 'update']); // POST multipart pour logo
    Route::delete('entreprises/{id}', [Admin\EntrepriseController::class, 'destroy']);
    Route::post('entreprises/{id}/send-kit',  [Admin\EntrepriseController::class, 'sendKit']);  // TODO
    Route::post('entreprises/{id}/send-link', [Admin\EntrepriseController::class, 'sendLink']); // CompanyConfirmationLink

    // Collectes (campagnes OnDoc)
    Route::get('entreprises/{id}/collectes',  [Admin\CollecteController::class, 'index']);
    Route::post('entreprises/{id}/collectes', [Admin\CollecteController::class, 'store']);
    Route::put('collectes/{collecte}',         [Admin\CollecteController::class, 'update']);
    Route::delete('collectes/{collecte}',      [Admin\CollecteController::class, 'destroy']);

    // Classement trophée
    Route::get('trophees', [Admin\TropheeController::class, 'index']);
    Route::put('trophees', [Admin\TropheeController::class, 'reorder']);

    Route::get('submissions',              [Admin\SubmissionController::class, 'index']);
    Route::get('submissions/{submission}', [Admin\SubmissionController::class, 'show']);

    Route::get('analytics',      [Admin\AnalyticsController::class, 'index']);
    Route::get('campaign-stats', [Admin\CampaignStatsController::class, 'show']);
    Route::put('campaign-stats', [Admin\CampaignStatsController::class, 'update']);
    Route::get('report',         [Admin\ReportController::class, 'show']);

    // Gestion des comptes admin
    Route::get('users',         [Admin\UserController::class, 'index']);
    Route::post('users',        [Admin\UserController::class, 'store']);
    Route::delete('users/{id}', [Admin\UserController::class, 'destroy']);
});
```

---

## Authentification Sanctum

```php
// AuthController@login
// first() au lieu de firstOrFail() — évite l'énumération d'emails (404 vs 401)
$user = User::where('email', $request->email)->first();

if (!$user || !Hash::check($request->password, $user->password)) {
    return response()->json(['message' => 'Identifiants invalides'], 401);
}
if (!$user->is_admin) {
    return response()->json(['message' => 'Accès réservé aux administrateurs'], 403);
}
return response()->json([
    'token' => $user->createToken('admin-token')->plainTextToken,
    'user'  => new UserResource($user),
]);

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
- Ne **jamais exposer `is_disqualifying`** au front (strippé dans `QuizController@show`)
- `QuizController@result` retourne 404 si pas de `session('quiz_token')` valide
- `session('quiz_token')` est vidé dans `QuizController@result` après lecture, pas après `store`
- Utiliser `CampaignStats::getInstance()` — jamais `::first()` directement
- CORS : l'admin SPA est servi sur la même origine — aucune config CORS nécessaire en production
- **Emails** : utiliser `->locale($locale)` sur le Mailable (pas `app()->setLocale()`). Locales valides : `fr`, `de`, `it`, `en`. Envoi synchrone (`Mail::send`) car Infomaniak ne supporte pas les workers persistants.
- `sendKit()` → TODO (non implémenté) ; `sendLink()` → envoie `CompanyConfirmationLink` lors de la validation ou à la demande
- `InscriptionController@store` → envoie `NewRegistrationNotification` à `info@donnez-votre-sang.ch` à chaque nouvelle inscription
- **Route key Entreprise** : `getRouteKeyName()` retourne `'access_token'` — la route `/c/{entreprise}` résout par `access_token`, pas par `slug`
- **Admin update entreprise** : `POST entreprises/{id}` (multipart pour upload logo), pas `PUT` — `apiResource` n'est pas utilisé
- **Throttle** : `login` → `10/min`, `leaderboard`/`stats` → `60/min`, `analytics` → `120/min`
- **Quiz multi-locale** : `quiz.{locale}.json` — fallback sur `quiz.json`. Cache par locale : clé `quiz_{locale}`. Invalider avec `php artisan cache:forget quiz_fr` etc.
- **Types de questions** : `yes_no`, `checklist`, `travel_check`, `birth_check`. Questions conditionnelles via `conditions[]`. `birth_check` est non disqualifiant mais positionne `needs_evaluation = true` (flashed session).
- **Protection double-submit** : `Submission::firstOrCreate(['session_token' => $token], [...])` dans `QuizController@store`
- **Seeder dev** (`DatabaseSeeder`) : 12 entreprises (5 avec trophée + rdv, 3 labelisées, 2 validées, 2 en attente), données analytics réalistes. **Seeder prod** (`ProductionSeeder`) : uniquement `CampaignStats::getInstance()` — admin créé via `php artisan app:create-admin`
- **Cache quiz** : `Cache::rememberForever("quiz_{$locale}", ...)` → invalider manuellement par locale après modification de `quiz.json`
- **PDF rapport** : `ReportController@show?format=pdf` → `barryvdh/laravel-dompdf`, template `resources/views/pdf/report.blade.php` (HTML tabulaire, pas de flexbox/grid)
- **Preview rapport** : `ReportController` stocke les données dans le cache sous `report_preview:{token}`, `ReportPreviewController@show` les récupère via Blade (évite de repasser par l'auth admin)
- **Middleware** : `SetLocale` lit la locale depuis le cookie `locale` (priorité) ; `SecurityHeaders` ajoute X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, HSTS (si HTTPS)
- **is_public** : contrôle la visibilité dans le leaderboard et le trophée public. Si `is_public = false`, `trophy_rank` est forcé à `null` lors de la mise à jour.
