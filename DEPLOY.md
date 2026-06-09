# Guide de mise en production — HUG Don du Sang

## Valeurs à remplacer avant d'utiliser ce guide

Toutes les occurrences de ces placeholders sont à adapter à ton environnement :

| Placeholder       | Ce qu'il faut mettre                                                       |
| ----------------- | -------------------------------------------------------------------------- |
| `<CLIENT_ID>`     | Identifiant Infomaniak ex. `e5818b3ef3…` (visible dans l'URL SSH du panel) |
| `<SSH_USER>`      | Utilisateur SSH Infomaniak                                                 |
| `<SSH_HOST>`      | Hôte SSH Infomaniak ex. `ssh-xxxx.infomaniak.com`                          |
| `<DOMAIN>`        | Nom de domaine ex. `donnez-votre-sang.ch`                                  |
| `<DB_DATABASE>`   | Nom de la base MySQL créée dans le manager                                 |
| `<DB_USERNAME>`   | Utilisateur MySQL                                                          |
| `<DB_PASSWORD>`   | Mot de passe MySQL                                                         |
| `<MAIL_USERNAME>` | Adresse email expéditeur                                                   |
| `<MAIL_PASSWORD>` | Mot de passe du compte mail                                                |

> Chemin de base sur le serveur : `/home/clients/<CLIENT_ID>/sites/<DOMAIN>/`

---

## Prérequis serveur

| Composant       | Version minimum                                                                                                 |
| --------------- | --------------------------------------------------------------------------------------------------------------- |
| PHP             | 8.3 avec extensions : `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` |
| MySQL / MariaDB | 8.0 / 10.6                                                                                                      |
| Composer        | 2.x                                                                                                             |

> Infomaniak mutualisé

---

## Fonctionnement du déploiement

Le déploiement se fait par **git push** depuis le poste local. Un hook `post-receive` sur le serveur met à jour les fichiers et rejoue les commandes automatiquement.

Deux entités coexistent sur le serveur :

- **Dépôt bare** — reçoit les pushs (`…/git/`), aucun fichier source visible
- **Dossier du site** — copie de travail servie au public (`…/web/`)

---

## Première mise en production (à faire une seule fois)

### 1. Créer la base de données MySQL

Dans le **manager Infomaniak** → Hébergement → Bases de données → Créer.  
Noter `<DB_DATABASE>`, `<DB_USERNAME>`, `<DB_PASSWORD>` et le host générés — ils serviront dans le `.env`.

### 2. Configurer le webroot

Dans le manager Infomaniak → Hébergement → Sites web → modifier le site → définir le répertoire racine sur **`web/public`** (et non `web/`).

### 3. Initialiser le dépôt bare sur le serveur (SSH)

```bash
ssh <SSH_USER>@<SSH_HOST>

git init --bare /home/clients/<CLIENT_ID>/sites/<DOMAIN>/git

git clone /home/clients/<CLIENT_ID>/sites/<DOMAIN>/git \
          /home/clients/<CLIENT_ID>/sites/<DOMAIN>/web
```

### 4. Ajouter le remote en local

```bash
# Le projet a deux remotes : origin (GitHub) + infomaniak (déploiement)
git remote add infomaniak ssh://<SSH_USER>@<SSH_HOST>/home/clients/<CLIENT_ID>/sites/<DOMAIN>/git
```

### 5. Écrire le hook post-receive sur le serveur

Créer `/home/clients/<CLIENT_ID>/sites/<DOMAIN>/git/hooks/post-receive` :

```bash
#!/bin/bash
set -e

TARGET="/home/clients/<CLIENT_ID>/sites/<DOMAIN>/web"
GIT_DIR="/home/clients/<CLIENT_ID>/sites/<DOMAIN>/git"
BRANCH="main"

while read oldrev newrev ref; do
  if [[ $ref = refs/heads/$BRANCH ]]; then
    echo ">>> Push recu — deploiement en cours..."

    git --work-tree=$TARGET --git-dir=$GIT_DIR checkout -f $BRANCH

    cd $TARGET

    composer install --no-dev --optimize-autoloader --quiet

    php artisan migrate --force

    php artisan storage:link --quiet 2>/dev/null || true

    php artisan optimize:clear --quiet

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache

    echo ">>> Deploiement termine !"
  fi
done
```

```bash
chmod +x /home/clients/<CLIENT_ID>/sites/<DOMAIN>/git/hooks/post-receive
```

### 6. Créer le `.env` sur le serveur (SSH)

```bash
cd /home/clients/<CLIENT_ID>/sites/<DOMAIN>/web
cp .env.example .env
```

Remplir avec les valeurs de production :

```dotenv
APP_NAME="Don du Sang HUG"
APP_ENV=production
APP_KEY=                          # généré à l'étape suivante
APP_DEBUG=false
APP_URL=https://<DOMAIN>

# Base de données — identifiants de l'étape 1
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<DB_DATABASE>
DB_USERNAME=<DB_USERNAME>
DB_PASSWORD=<DB_PASSWORD>

# Session & Cache — fichier (Redis non disponible sur mutualisé)
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
CACHE_STORE=file

# Queue — synchrone (pas de worker persistant sur mutualisé)
QUEUE_CONNECTION=sync

# Mail — Infomaniak SMTP
MAIL_MAILER=smtp
MAIL_HOST=mail.infomaniak.com
MAIL_PORT=587
MAIL_USERNAME=<MAIL_USERNAME>
MAIL_PASSWORD=<MAIL_PASSWORD>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=<MAIL_USERNAME>
MAIL_FROM_NAME="Don du Sang HUG"

# Sanctum — expiration tokens admin en minutes
SANCTUM_TOKEN_EXPIRATION=480
```

### 7. Commandes d'initialisation (SSH, une seule fois)

```bash
cd /home/clients/<CLIENT_ID>/sites/<DOMAIN>/web

php artisan key:generate
php artisan storage:link
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder --force
php artisan admin:create
chmod -R 775 storage/ bootstrap/cache/
```

### 8. Build frontend et premier push (en local)

Infomaniak ne dispose pas de Node.js — le build doit être fait en local et le dossier `public/build/` commité.

```bash
npm ci
npm run build
git add public/build/
git commit -m "build prod"
git push infomaniak main
```

---

## Déploiements suivants

```bash
# Builder si des fichiers JS/CSS/Vue ont changé
npm run build

git add .
git commit -m "..."
git push infomaniak main   # déclenche le hook automatiquement
```

Le `git push origin main` vers GitHub reste indépendant — les deux remotes sont séparés.

---

## Vérifications après déploiement

```bash
tail -f storage/logs/laravel.log

php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';"

php artisan tinker --execute="Mail::raw('Test', fn(\$m) => \$m->to('<ton@email.ch>')->subject('Test'));"
```

---

## Environnement de démo

```bash
php artisan db:seed --class=DemoSeeder --force   # charge les 21 entreprises demo
php artisan demo:reset                            # vide tout sauf les admins
php artisan demo:reset --seed                     # vide + recharge la démo
```

Compte admin par défaut après `DemoSeeder` : `admin@hug.ch` / `password`  
**Changer le mot de passe immédiatement en production.**

---

## Points d'attention

| Point                   | Détail                                                                                                                       |
| ----------------------- | ---------------------------------------------------------------------------------------------------------------------------- |
| **Build frontend**      | `public/build/` doit être commité — le hook ne lance pas `npm`. Builder en local avant chaque push qui touche du JS/CSS/Vue. |
| **Pas de queue worker** | Les mails sont envoyés en synchrone (`Mail::send`). Ne pas basculer sur `Mail::queue` sans worker.                           |
| **Cache quiz**          | Après modification d'un `quiz.{locale}.json` : `php artisan cache:forget quiz_fr` (idem `quiz_de`, `quiz_it`, `quiz_en`).    |
| **Logos uploadés**      | Stockés dans `storage/app/public/`. Le lien `public/storage` doit exister (`storage:link`).                                  |
| **`noindex, nofollow`** | Le site est volontairement non indexé. Ne pas retirer sans accord.                                                           |
| **`CampaignStats`**     | Une seule ligne en base, initialisée par `ProductionSeeder`. Ne jamais truncate sans réinitialiser.                          |
| **Tokens Sanctum**      | Expiration via `SANCTUM_TOKEN_EXPIRATION` (défaut : 480 min).                                                                |
