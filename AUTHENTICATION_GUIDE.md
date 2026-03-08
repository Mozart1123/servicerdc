# 🔐 Guide Complet du Système d'Authentification ServiceRDC

**Plateforme de services en République Démocratique du Congo**

---

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture](#architecture)
3. [Pages d'authentification](#pages-dauthentification)
4. [Flux utilisateur](#flux-utilisateur)
5. [Configuration](#configuration)
6. [Sécurité](#sécurité)
7. [Personnalisation](#personnalisation)
8. [Dépannage](#dépannage)

---

## 🎯 Vue d'ensemble

Le système d'authentification ServiceRDC fournit une solution complète pour :

✅ **Inscription** - Créer un nouveau compte avec type d'utilisateur  
✅ **Connexion** - Accès sécurisé au compte  
✅ **Mot de passe oublié** - Récupération de compte  
✅ **Réinitialisation** - Changement de mot de passe sécurisé  
✅ **Validation** - Contrôle des données côté serveur et client  

### Couleurs du drapeau congolais

```css
Bleu Congo:     #007FFF  (Connexion, lien, boutons primaires)
Jaune Congolais: #F0B800  (Inscription, boutons secondaires)
Rouge Congolais: #FF4757  (Erreurs, alertes)
Gris clair:     #F0F4F5  (Arrière-plan)
```

---

## 🏗️ Architecture

### Structure des fichiers

```
resources/
├── views/
│   ├── layouts/
│   │   └── auth.blade.php          ← Layout principal
│   └── auth/
│       ├── login.blade.php          ← Page de connexion
│       ├── register.blade.php       ← Page d'inscription
│       ├── forgot-password.blade.php ← Récupération mot de passe
│       └── reset-password.blade.php  ← Réinitialisation mot de passe
│
app/
└── Http/
    └── Controllers/
        └── Auth/
            ├── AuthenticatedSessionController.php
            ├── RegisteredUserController.php
            └── PasswordResetController.php
```

### Contrôleurs

| Contrôleur | Responsabilité |
|-----------|-----------------|
| `AuthenticatedSessionController` | Gère connexion/déconnexion |
| `RegisteredUserController` | Gère l'inscription |
| `PasswordResetController` | Gère mot de passe oublié/réinitialisation |

---

## 📄 Pages d'authentification

### 1. Page de Connexion (`/login`)

**URL:** `/login`  
**Méthode POST:** `{{ route('login') }}`  
**Authentification requise:** Non  

#### Champs du formulaire

| Champ | Type | Validation | Placeholder |
|-------|------|-----------|-------------|
| Email | email | Requis, unique | votre@email.com |
| Mot de passe | password | Requis, min:8 | •••••••• |
| Se souvenir | checkbox | Optionnel | - |

#### Messages

**Succès :** Redirection vers dashboard  
**Erreur :** "Ces identifiants ne correspondent pas à nos enregistrements."  

#### Fonctionnalités spéciales

- ✅ Afficher/masquer le mot de passe
- ✅ Lien "Mot de passe oublié"
- ✅ Lien d'inscription
- ✅ Bouton Google optionnel
- ✅ Case "Se souvenir de moi"

---

### 2. Page d'Inscription (`/register`)

**URL:** `/register`  
**Méthode POST:** `{{ route('register') }}`  
**Authentification requise:** Non  

#### Champs du formulaire

| Champ | Type | Validation | Requis |
|-------|------|-----------|--------|
| Nom complet | text | max:255 | ✓ |
| Email | email | unique | ✓ |
| Téléphone | tel | max:20 | ✓ |
| Mot de passe | password | min:8, confirmed | ✓ |
| Confirmation | password | confirme password | ✓ |
| Type utilisateur | radio | client/artisan/job_seeker | ✓ |
| Conditions | checkbox | Accepté | ✓ |

#### Types d'utilisateurs

```
┌─────────────────────────────────────────────────┐
│ Client  (Bleu)                                  │
│ Je cherche des services                        │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│ Artisan  (Jaune)                                │
│ Je propose des services                        │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│ Chercheur d'emploi  (Vert)                      │
│ Je cherche un emploi                           │
└─────────────────────────────────────────────────┘
```

#### Fonctionnalités spéciales

- ✅ Validation en temps réel
- ✅ Barre de force du mot de passe
- ✅ Sélecteur de type utilisateur avec icônes
- ✅ Vérification de confirmation de mot de passe
- ✅ Lien vers conditions d'utilisation

#### Messages de validation

```
Nom requis:           "Le nom complet est requis."
Email requis:         "L'adresse email est requise."
Email invalide:       "Veuillez entrer une adresse email valide."
Email déjà utilisé:   "Cette adresse email est déjà utilisée."
Téléphone requis:     "Le numéro de téléphone est requis."
Mot de passe requis:  "Le mot de passe est requis."
Mot de passe faible:  "Le mot de passe doit contenir au moins 8 caractères."
Mots de passe différents: "Les mots de passe ne correspondent pas."
Type requis:          "Veuillez sélectionner votre type de profil."
Conditions requises:  "Vous devez accepter les conditions d'utilisation."
```

---

### 3. Page Mot de Passe Oublié (`/forgot-password`)

**URL:** `/forgot-password`  
**Méthode POST:** `{{ route('password.email') }}`  
**Authentification requise:** Non  

#### Champs du formulaire

| Champ | Type | Validation |
|-------|------|-----------|
| Email | email | Requis, existe |

#### Processus

1. Utilisateur entre son email
2. Système envoie email avec lien reset
3. Message de confirmation affiché
4. Lien valide 60 minutes par défaut

#### Variables d'environnement

```env
MAIL_FROM_ADDRESS=noreply@servicerdc.com
MAIL_FROM_NAME="ServiceRDC Support"
```

---

### 4. Page Réinitialisation (`/reset-password/{token}`)

**URL:** `/reset-password/{token}`  
**Méthode POST:** `{{ route('password.update') }}`  
**Authentification requise:** Non  
**Token requis:** Oui (depuis email)  

#### Champs du formulaire

| Champ | Type | Validation |
|-------|------|-----------|
| Email | email | Lecture seule (pré-rempli) |
| Nouveau mot de passe | password | min:8, confirmed |
| Confirmation | password | Confirmation requise |
| Token | hidden | Depuis URL |

#### Fonctionnalités spéciales

- ✅ Email pré-rempli et en lecture seule
- ✅ Barre de force du mot de passe
- ✅ Afficher/masquer les mots de passe
- ✅ Validation de confirmation en temps réel
- ✅ Token sécurisé dans formulaire hidden

#### Messages

**Succès :** "Votre mot de passe a été réinitialisé avec succès."  
**Erreur token:** "Ce lien de réinitialisation est invalide ou a expiré."  
**Erreur email:** "Cet email n'existe pas dans nos enregistrements."  

---

## 🔄 Flux utilisateur

### Inscription → Connexion

```
1. Utilisateur visite /register
   ↓
2. Remplit le formulaire d'inscription
   - Choisit son type (Client/Artisan/Chercheur d'emploi)
   - Crée un mot de passe fort
   ↓
3. Soumet le formulaire
   ↓
4. Validation côté serveur
   - Vérification email unique
   - Hachage du mot de passe
   - Enregistrement en BDD
   ↓
5. Login automatique
   ↓
6. Redirection vers dashboard utilisateur
```

### Connexion simple

```
1. Utilisateur visite /login
   ↓
2. Entre email et mot de passe
   ↓
3. Soumet le formulaire
   ↓
4. Vérification des identifiants
   ↓
5. Redirection selon rôle:
   - Admin → /admin/dashboard
   - Utilisateur → /user/dashboard
   - Super Admin → /super-admin/dashboard
```

### Récupération mot de passe

```
1. Utilisateur visite /forgot-password
   ↓
2. Entre son email
   ↓
3. Email avec lien de reset reçu
   ↓
4. Clique sur lien (valide 60 min)
   ↓
5. Visite /reset-password/{token}
   ↓
6. Crée un nouveau mot de passe
   ↓
7. Message de succès
   ↓
8. Peut se connecter avec nouveau mot de passe
```

---

## ⚙️ Configuration

### 1. Fichier `.env`

```env
# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=service_rdc
DB_USERNAME=root
DB_PASSWORD=

# Email (pour récupération mot de passe)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=xxxx
MAIL_PASSWORD=xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@servicerdc.com
MAIL_FROM_NAME="ServiceRDC Support"

# App
APP_NAME="ServiceRDC"
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_KEY=base64:xxxxx
```

### 2. Configuration de la base de données

Les migrations nécessaires existent déjà :

```bash
php artisan migrate
```

Effectue les migrations :
- `create_users_table` - Tableau principal des utilisateurs
- `add_fields_to_users_table` - Champs additionnels (phone, etc.)
- `add_role_to_users_table` - Rôles et type d'utilisateur

### 3. Configuration des routes

Les routes sont déjà configurées dans `routes/web.php` :

```php
// Routes de guest (non authentifiés)
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

// Routes authentifiées
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    // Routes spécifiques au rôle...
});
```

---

## 🔒 Sécurité

### Mesures implémentées

#### 1. Protection CSRF
```php
@csrf // Présent dans tous les formulaires
```

#### 2. Hachage des mots de passe
```php
Hash::make($request->password) // SHA-256 par défaut
```

#### 3. Validation côté serveur
```php
$request->validate([
    'password' => ['required', 'string', 'min:8', 'confirmed'],
    // ...
]);
```

#### 4. Rate limiting (intégré à Laravel)
Les tentatives de login/reset sont limitées :
```php
60 requests par minute pour 1 IP
```

#### 5. Tokens sécurisés
- Tokens de réinitialisation : 60 minutes d'expiration
- Tokens CSRF : par session
- Remember token : crypté en base de données

#### 6. Validation email
```php
'email' => ['required', 'string', 'email', 'max:255', 'unique:users']
```

#### 7. Middleware d'authentification
```php
middleware('auth')      // Pour routes protégées
middleware('guest')     // Pour routes publiques uniquement
```

### Bonnes pratiques appliquées

✅ Pas de stockage d'emails en clair  
✅ Pas d'exposition de tokens en URL  
✅ Session sécurisée (httpOnly cookies)  
✅ HTTPS recommandé en production  
✅ Logs d'authentification  
✅ Validation côté serveur stricte  

---

## 🎨 Personnalisation

### Modifier les couleurs

**Fichier:** `resources/views/layouts/auth.blade.php`

```css
:root {
    --primary-blue: #007FFF;      /* Boutons login */
    --secondary-blue: #0066CC;    /* Hover states */
    --accent-yellow: #F0B800;     /* Bouton register */
    --accent-red: #FF4757;        /* Erreurs */
}
```

### Modifier les messages

**Fichier:** `app/Http/Controllers/Auth/RegisteredUserController.php`

```php
$request->validate([
    'name' => ['required'],
], [
    'name.required' => 'Votre message personnalisé ici',
]);
```

### Personnaliser le layout

**Fichier:** `resources/views/layouts/auth.blade.php`

Modifier :
- Fonts
- Couleurs
- Espacement
- Animations

### Ajouter des champs

**Exemple :** Ajouter "Entreprise"

1. Migration :
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('company')->nullable();
});
```

2. Modèle User :
```php
protected $fillable = ['name', 'email', 'company', ...];
```

3. Vue register :
```html
<input name="company" type="text" required>
```

4. Validation :
```php
'company' => ['required', 'string', 'max:255']
```

---

## 🐛 Dépannage

### Problème : "Email déjà utilisé"

**Solution :** Vérifier que l'email n'existe pas en BDD
```bash
SELECT * FROM users WHERE email = 'votre@email.com';
```

### Problème : Lien de réinitialisation invalide

**Causes possibles :**
- Token expiré (> 60 min)
- Token n'existe pas en BDD
- Utilisateur supprimé

**Solution :** Demander un nouveau lien

### Problème : Email non reçu

**Vérifier :**
1. Configuration mail dans `.env`
2. Serveur mail accessible
3. Logs Laravel : `storage/logs/`

```bash
tail -f storage/logs/laravel.log
```

### Problème : CSRF token missing

**Solution :** Vérifier présence `@csrf` dans formulaire

```html
<form method="POST">
    @csrf  ← Obligatoire
    <!-- autres champs -->
</form>
```

### Problème : Mot de passe oublié après reset

**Solution :** Utiliser la même URL `/reset-password/{token}` reçue par email

### Problème : Utilisateur reste logué après suppression

**Solution :** Effectuer logout via `/logout`

---

## 📱 Responsive Design

Tous les formulaires sont 100% responsifs :

- **Mobile :** Texte lisible, boutons grands
- **Tablette :** Layout optimisé
- **Desktop :** Espacé et professionnel

---

## ✉️ Templates Email

### Email de récupération de mot de passe

**Fichier :** Généré automatiquement par Laravel

**Contient :**
- Message d'accueil personnalisé
- Lien sécurisé `/reset-password/{token}`
- Délai d'expiration (60 minutes)
- Contacter support si problème

Personnaliser :
```bash
php artisan vendor:publish --tag=laravel-passwords
```

---

## 📊 Statistiques & Monitoring

### Informations utiles

- **Sessions actives :** Cache Redis
- **Failed logins :** Logs Laravel
- **Password resets :** Table `password_reset_tokens`
- **User registrations :** Table `users`

---

## 🔗 Liens rapides

| Action | URL |
|--------|-----|
| Se connecter | `/login` |
| S'inscrire | `/register` |
| Mot de passe oublié | `/forgot-password` |
| Réinitialiser | `/reset-password/{token}` |
| Se déconnecter | `/logout` |
| Dashboard | `/user/dashboard` |

---

## 📚 Ressources additionnelles

- [Documentation Laravel Authentication](https://laravel.com/docs/authentication)
- [Sécurité web OWASP](https://owasp.org)
- [RFC 2617 - HTTP Authentication](https://tools.ietf.org/html/rfc2617)

---

## 💬 Support

Pour questions ou problèmes :

📧 **Email :** support@servicerdc.com  
📱 **Téléphone :** +243 XXX XXX XXX  
💬 **Chat :** Disponible 24/7 sur le site  

---

**ServiceRDC - Plateforme de services en République Démocratique du Congo**  
*Dernière mise à jour : Janvier 2026*
