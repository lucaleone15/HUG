# 🩸 HUG

> Projet d'intégration · Promotion du don de sang en entreprise  
> HEIG-VD · 2026

---

# Pré-requis

Afin de lancer ce projet, une stack compatible avec Laravel est nécessaire.

## Technologies requises

- PHP >= 8.4
- Composer
- Node.js et npm
- MySQL
- Un serveur web local (Laravel Herd recommandé)

---

# Installation du projet

## 1. Cloner le dépôt

```bash
git clone https://github.com/kisyaaaa/HUG.git
cd HUG
```

---

## 2. Installer les dépendances

**Dépendances PHP (Laravel) :**

```bash
composer install
```

**Dépendances front-end (Vue.js) :**

```bash
npm install
npm run build
```

### 3. Configurer l'environnement

Copier le fichier d'environnement :

```bash
cp .env.example .env
```

### 4. Créer la base de données MySQL

Si MySQL n'est pas encore installé :

```bash
# macOS
brew install mysql
brew services start mysql

# Ubuntu / Debian
sudo apt install mysql-server
sudo systemctl start mysql
```

Accéder à MySQL selon votre environnement :

**Via le terminal :**

```bash
mysql -u root -p
```

Une fois connecté, créer la base :

```sql
CREATE DATABASE hug;
```

### 5. Générer la clé d'application Laravel

```bash
php artisan key:generate
```

### 6. Créer le lien de stockage

```bash
php artisan storage:link
```

### 7. Exécuter les migrations

```bash
php artisan migrate
```

> **Besoin de repartir de zéro ?**
>
> ```bash
> php artisan migrate:reset
> php artisan migrate
> ```

### 8. (Optionnel) Charger des données fictives

```bash
php artisan db:seed
```

---

## Lancer le projet

```bash
composer run dev
```

L'application est disponible sur : **http://127.0.0.1:8000**
