# ⚡ Commandes Rapides - Système d'Authentification ServiceRDC

**Toutes les commandes utiles en un seul endroit**

---

## 🚀 DÉMARRAGE RAPIDE

### Installation Initiale

```bash
# 1. Aller dans le dossier
cd c:\xampp\htdocs\rdc\rdc

# 2. Configurer .env (si nécessaire)
cp .env.example .env

# 3. Générer clé app
php artisan key:generate

# 4. Migrer la base de données
php artisan migrate

# 5. Démarrer le serveur
php artisan serve

# ✅ Accédez à : http://localhost:8000/login
```

---

## 📊 COMMANDES ESSENTIELLES

### Database

```bash
# Voir statut migrations
php artisan migrate:status

# Exécuter migrations
php artisan migrate

# Rollback dernier batch
php artisan migrate:rollback

# Rollback tout et re-migrate
php artisan migrate:refresh

# Seed la base (si seeders existent)
php artisan db:seed

# Spécifique seeder
php artisan db:seed --class=AdminUsersSeeder
```

### Cache & Config

```bash
# Effacer tous les caches
php artisan cache:clear

# Effacer config cache
php artisan config:clear

# Effacer view cache
php artisan view:clear

# Effacer tout
php artisan cache:clear && php artisan config:clear && php artisan view:clear

# Recacher config
php artisan config:cache

# Recacher routes
php artisan route:cache
```

### Laravel Tinker (Debugging)

```bash
# Lancer tinker
php artisan tinker

# Dans tinker:
> User::all();                                  # Voir tous les users
> User::where('email', 'test@test.com')->first();  # Chercher user
> Hash::check('password', $user->password);    # Vérifier mot de passe
> exit                                          # Quitter tinker
```

---

## 🧪 TESTS & VÉRIFICATIONS

### Artisan Commands

```bash
# Voir toutes les routes
php artisan route:list

# Filtrer par URI
php artisan route:list --uri=login

# Voir les middlewares
php artisan route:list --only=middleware

# Voir code HTTP des routes
php artisan route:list --method=POST

# Voir tous les middleware
php artisan middleware:list
```

### Vérifier Configuration

```bash
# Voir env actuel
php artisan env

# Voir config app
php artisan config:show app

# Voir config auth
php artisan config:show auth

# Voir config mail
php artisan config:show mail

# Tous les configs
php artisan config:show
```

### Tests Laravel (si PHPUnit configuré)

```bash
# Exécuter tous les tests
php artisan test

# Tester fichier spécifique
php artisan test tests/Feature/AuthTest.php

# Avec output verbeux
php artisan test --verbose

# Voir coverage
php artisan test --coverage
```

---

## 📧 COMMANDES EMAIL

### Tester Email (Tinker)

```bash
# Ouvrir tinker
php artisan tinker

# Dans tinker, envoyer test email
> Mail::raw('Test email', function($m) {
    $m->to('test@example.com')
      ->subject('Test');
});

# Ou avec Mailable
> Mail::to('test@example.com')->send(new MyMailable());

> exit
```

### Voir emails en dev

```bash
# Utiliser Mailhog ou Mailtrap
# Mailtrap : https://mailtrap.io
# Mailhog : http://localhost:1025 (interface)
```

---

## 🔐 COMMANDES SÉCURITÉ

### Hash & Encryption

```bash
# Ouvrir tinker
php artisan tinker

# Dans tinker:
> Hash::make('password123')         # Hacher un mot de passe
> Hash::check('plain', $hashed)     # Vérifier mot de passe
> Str::random(32)                   # Générer token aléatoire
> exit
```

### Sessions & Tokens

```bash
# Effacer toutes les sessions
php artisan session:clear

# Tester CSRF token
# Vérifier présence @csrf dans formulaires

# Générer clé app (si perdue)
php artisan key:generate
```

---

## 🎨 COMMANDES FRONTEND

### Tailwind CSS

```bash
# Compiler CSS en dev
npm run dev

# Compiler CSS pour production
npm run build

# Watcher dev continu
npm run watch
```

### Vite (Build tool)

```bash
# Démarrer dev server
npm run dev

# Build production
npm run build

# Preview build
npm run preview
```

---

## 📝 COMMANDES UTILES

### Créer Nouveaux Fichiers

```bash
# Créer controller
php artisan make:controller TestController

# Créer migration
php artisan make:migration create_table_name

# Créer modèle avec migration
php artisan make:model TestModel -m

# Créer seeder
php artisan make:seeder TestSeeder

# Créer test
php artisan make:test TestTest
```

### Lister/Info

```bash
# Voir toutes les commandes
php artisan list

# Aide commande spécifique
php artisan help migrate

# Voir version Laravel
php artisan --version

# Voir PHP version
php --version
```

---

## 🔍 DEBUGGING

### Logs

```bash
# Voir logs en temps réel (Unix/Mac)
tail -f storage/logs/laravel.log

# Voir logs (Windows)
type storage\logs\laravel.log

# Voir dernières 100 lignes
tail -100 storage/logs/laravel.log

# Voir avec grep (chercher erreurs)
grep -i error storage/logs/laravel.log
```

### Var Dump

```php
// Dans le code
dd($variable);              // Dump et die
dump($variable);            # Dump continue
ray($variable)->dump();     # Si Ray CLI installé
```

---

## 🌐 COMMANDES SERVEUR

### Démarrer Serveur

```bash
# Serveur par défaut (8000)
php artisan serve

# Port spécifique
php artisan serve --port=3000

# Host + port
php artisan serve --host=0.0.0.0 --port=8000

# Avec options
php artisan serve --ssl
```

### Queue Worker (si jobs)

```bash
# Écouter queue
php artisan queue:listen

# Avec delay
php artisan queue:listen --delay=5

# Retry failed
php artisan queue:retry
```

---

## ✅ VÉRIFICATION PRE-PRODUCTION

```bash
# 1. Migrer
php artisan migrate

# 2. Effacer cache
php artisan cache:clear
php artisan config:clear

# 3. Recacher config
php artisan config:cache

# 4. Recacher routes
php artisan route:cache

# 5. Vérifier env
php artisan env

# 6. Compiler assets
npm run build

# 7. Test (optionnel)
php artisan test

# ✅ Prêt pour production !
```

---

## 📋 CHECKLIST RAPIDE

### Avant de pusher

```bash
# 1. Vérifier logs d'erreur
tail -f storage/logs/laravel.log

# 2. Vérifier migrations
php artisan migrate:status

# 3. Vérifier config
php artisan config:show app

# 4. Tester routes
php artisan route:list

# 5. Tester email (optionnel)
# Utiliser tinker pour test

# 6. Vérifier permissions
chmod -R 755 storage bootstrap/cache

# 7. Vérifier .env (ne pas pusher!)
cat .env  # Vérifier présence secrets
```

### Avant production

```bash
# 1. APP_DEBUG=false
vi .env

# 2. Cache config
php artisan config:cache

# 3. Optimiser autoloader
composer install --optimize-autoloader --no-dev

# 4. Build frontend
npm run build

# 5. Vérifier permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# 6. Backuper base
# Via MySQL ou hosting panel

# ✅ Deployé !
```

---

## 🔧 DÉPANNAGE RAPIDE

### Erreur 404 Routes

```bash
php artisan cache:clear
php artisan route:cache
php artisan serve
```

### Erreur Database Connection

```bash
# Vérifier .env
vi .env

# Vérifier connection
php artisan tinker
> DB::connection()->getPdo();

# Si erreur, réessayer
exit
```

### Erreur Migra tion

```bash
# Voir statut
php artisan migrate:status

# Rollback et retry
php artisan migrate:rollback
php artisan migrate
```

### Session Expirée

```bash
# Effacer sessions
php artisan session:clear

# Vérifier config
php artisan config:show session
```

### Erreur Permission

```bash
# Fix permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 resources/views

# Si toujours erreur
sudo chown -R www-data:www-data storage bootstrap/cache
```

---

## 💡 COMMANDES PERSONNALISÉES

### Créer commande custom

```bash
php artisan make:command MyCommand
```

**Fichier:** `app/Console/Commands/MyCommand.php`

```php
<?php
namespace App\Console\Commands;

class MyCommand extends Command {
    protected $signature = 'custom:command';
    protected $description = 'Description';
    
    public function handle() {
        $this->info('Success!');
    }
}
```

### Exécuter commande custom

```bash
php artisan custom:command
```

---

## 📊 COMMANDES MONITORING

### Info Système

```bash
# Laravel info
php artisan about

# Check requirements
php artisan about

# Vérifier extensions PHP
php -m | grep -i pdo

# Vérifier MySQL connection
mysql -u root -p service_rdc -e "SELECT 1;"
```

### Health Check

```bash
# Dans browser
http://localhost:8000/

# Vérifier middleware
php artisan route:list | grep health
```

---

## 🎯 ALIAS UTILES

Ajouter à `~/.bashrc` ou `~/.zshrc`:

```bash
# Laravel shortcuts
alias art='php artisan'
alias serve='php artisan serve'
alias migrate='php artisan migrate'
alias refresh='php artisan migrate:refresh'
alias tinker='php artisan tinker'
alias cache='php artisan cache:clear && php artisan config:clear'

# Git shortcuts
alias gs='git status'
alias gp='git push'
alias gl='git log --oneline'
```

Utilisation :

```bash
art serve
art migrate
cache
```

---

## 📞 AIDE RAPIDE

```bash
# Help pour commande
php artisan help migrate

# Help pour option
php artisan migrate --help

# Liste toutes commandes
php artisan list
```

---

## 🚨 COMMANDES URGENCE

### Récupérer BD Corrompue

```bash
# Rollback tout
php artisan migrate:reset

# Re-migrate
php artisan migrate

# Re-seed si besoin
php artisan db:seed
```

### Reset Complete

```bash
# Effacer tout et restart
rm -rf storage/logs/*
php artisan migrate:refresh
php artisan db:seed
php artisan cache:clear
php artisan config:clear
php artisan serve
```

### Backup Quick

```bash
# Backup BD (Unix/Mac)
mysqldump -u root -p service_rdc > backup.sql

# Backup BD (Windows - XAMPP)
"C:\xampp\mysql\bin\mysqldump" -u root service_rdc > backup.sql
```

### Restore Backup

```bash
# Restaurer BD
mysql -u root -p service_rdc < backup.sql
```

---

## ✨ COMMANDES BONUS

### Générer Dummy Data

```bash
# Factory test data
php artisan tinker
> User::factory()->count(10)->create();
> exit
```

### Clear Everything

```bash
# Ultimate reset (attention!)
php artisan down                    # Maintenance mode
php artisan migrate:reset
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan up
```

---

## 📋 QUICK REFERENCE

| Commande | Fonction |
|----------|----------|
| `php artisan serve` | Démarrer serveur |
| `php artisan migrate` | Exécuter migrations |
| `php artisan tinker` | Debug console |
| `php artisan cache:clear` | Effacer cache |
| `php artisan route:list` | Voir routes |
| `php artisan make:*` | Créer fichiers |
| `npm run dev` | Build CSS dev |
| `npm run build` | Build CSS prod |

---

## 🎯 WORKFLOW TYPIQUE

```bash
# Morning startup
cd c:\xampp\htdocs\rdc\rdc
php artisan serve          # Terminal 1
npm run dev                 # Terminal 2 (optionnel)

# Dev during day
# ... code ...

# Before commit
php artisan cache:clear
php artisan test

# Before deploy
php artisan migrate
php artisan config:cache
npm run build

# Deployed!
```

---

**ServiceRDC - Plateforme de services en République Démocratique du Congo**

*Commandes rapides - Janvier 2026*

*Sauvegardez cette page comme favoris !* ⭐
