# ⚡ Guide de Démarrage Rapide - Authentification ServiceRDC

**Démarrez en 5 minutes !**

---

## 🚀 Démarrage Rapide

### Étape 1 : Vérifier la configuration Laravel

```bash
# Assurez-vous que vous êtes dans le dossier du projet
cd c:\xampp\htdocs\rdc\rdc

# Vérifier les migrations
php artisan migrate:status

# Exécuter les migrations si nécessaire
php artisan migrate
```

### Étape 2 : Accéder aux pages d'authentification

#### 🔓 **Connexion**
```
http://localhost:8000/login
```

**Identifiants de test :**
```
Email: test@example.com
Password: password
```

#### 📝 **Inscription**
```
http://localhost:8000/register
```

Créez un compte avec :
- Nom complet
- Email unique
- Téléphone
- Type utilisateur (Client/Artisan/Chercheur d'emploi)
- Mot de passe fort (min 8 caractères)

#### 🔐 **Mot de passe oublié**
```
http://localhost:8000/forgot-password
```

Entrez votre email pour recevoir un lien de réinitialisation.

---

## 🎨 Aperçu Visuel

### Page de Connexion

```
┌─────────────────────────────────────────┐
│  ServiceRDC                             │
│  Plateforme de services en RDC          │
│                                         │
│  Se connecter                           │
│  Bienvenue ! Connectez-vous à votre...  │
│                                         │
│  ✉️  Email: [votre@email.com]           │
│  🔒 Mot de passe: [••••••••]            │
│                                         │
│  ☐ Se souvenir de moi                   │
│  [Mot de passe oublié ?]                │
│                                         │
│  [🔵 Se connecter (Bleu)]               │
│  ─────────────────── OU ────────────────│
│  [⚪ Continuer avec Google]             │
│                                         │
│  Pas encore de compte? S'inscrire       │
│  ← Retour à l'accueil                   │
└─────────────────────────────────────────┘
```

### Page d'Inscription

```
┌─────────────────────────────────────────┐
│  ServiceRDC                             │
│                                         │
│  Créer un compte                        │
│  Rejoignez la communauté ServiceRDC     │
│                                         │
│  👤 Nom complet: [Jean Kabongo]         │
│  ✉️  Email: [votre@email.com]           │
│  📱 Téléphone: [+243 XXX XXX XXX]       │
│                                         │
│  🔒 Mot de passe: [••••••••]            │
│  ┌─────────────────────────────────────┐ │
│  │ ███████░░░░░░░░░░░░░░░░░░░░░░░░░░░│ │ ← Force
│  └─────────────────────────────────────┘ │
│                                         │
│  🔒 Confirmer: [••••••••]               │
│                                         │
│  Je suis un(e) *                        │
│  ☐ Client (Bleu)                        │
│    Je cherche des services              │
│  ☐ Artisan (Jaune) ← Sélectionné        │
│    Je propose des services              │
│  ☐ Chercheur d'emploi (Vert)           │
│    Je cherche un emploi                 │
│                                         │
│  ☐ J'accepte les conditions             │
│                                         │
│  [🟡 S'inscrire (Jaune)]                │
│                                         │
│  Déjà un compte? Se connecter           │
└─────────────────────────────────────────┘
```

---

## 📝 Formulaires Détaillés

### 1. Connexion - Champs

| Champ | Format | Exemple |
|-------|--------|---------|
| **Email** | email | jean@example.com |
| **Mot de passe** | texte | min 8 caractères |
| **Se souvenir** | checkbox | Optionnel |

**Boutons :**
- Bleu: `Se connecter` (#007FFF)
- Gris: `Continuer avec Google`

### 2. Inscription - Champs

| Champ | Format | Validation |
|-------|--------|-----------|
| **Nom** | texte | Requis, max 255 |
| **Email** | email | Requis, unique |
| **Téléphone** | tel | Requis, format RDC |
| **Mot de passe** | password | Min 8, fort |
| **Confirmation** | password | Doit correspondre |
| **Type** | radio | Client/Artisan/Job Seeker |
| **Conditions** | checkbox | Requis |

**Indicateurs :**
- Barre de force du mot de passe (Faible → Moyen → Bon → Fort)
- Afficher/masquer mot de passe
- Sélection type avec icônes colorées

### 3. Mot de passe oublié - Champs

| Champ | Format | Validation |
|-------|--------|-----------|
| **Email** | email | Requis, doit exister |

**Actions :**
- Envoie email avec lien reset
- Lien valide 60 minutes
- Message de confirmation

### 4. Réinitialisation - Champs

| Champ | Format | Validation |
|-------|--------|-----------|
| **Email** | email | Pré-rempli, lecture seule |
| **Nouveau mot de passe** | password | Min 8, fort |
| **Confirmation** | password | Doit correspondre |
| **Token** | hidden | Automatique |

---

## 🔑 Modèle de données User

```php
User {
    id              → INT (clé primaire)
    name            → VARCHAR(255)
    email           → VARCHAR(255) UNIQUE
    phone           → VARCHAR(20)
    password        → VARCHAR(255) (hashé)
    
    // Rôles et types
    role            → ENUM('user', 'admin', 'super_admin')
    user_type       → ENUM('client', 'artisan', 'job_seeker')
    
    // Gestion des comptes
    email_verified_at    → TIMESTAMP (nullable)
    terms_accepted_at    → TIMESTAMP (nullable)
    remember_token       → VARCHAR(100) (nullable)
    
    // Timestamps
    created_at      → TIMESTAMP
    updated_at      → TIMESTAMP
}
```

---

## 🛠️ Configuration Essentials

### `.env` - Configuration Email

```env
# Pour envoyer des emails de réinitialisation
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@servicerdc.com
MAIL_FROM_NAME="ServiceRDC Support"
```

### Routes disponibles

```
GET    /login                  → Affiche formulaire connexion
POST   /login                  → Soumet connexion
GET    /register               → Affiche formulaire inscription
POST   /register               → Soumet inscription
GET    /forgot-password        → Affiche récupération
POST   /forgot-password        → Envoie email reset
GET    /reset-password/{token} → Affiche formulaire reset
POST   /reset-password         → Soumet nouveau mot de passe
POST   /logout                 → Déconnexion
```

---

## ✅ Validations & Messages

### Messages d'erreur (Français)

```
Email
├─ "L'adresse email est requise."
├─ "Veuillez entrer une adresse email valide."
└─ "Cette adresse email est déjà utilisée."

Mot de passe
├─ "Le mot de passe est requis."
├─ "Le mot de passe doit contenir au moins 8 caractères."
└─ "Les mots de passe ne correspondent pas."

Type utilisateur
└─ "Veuillez sélectionner votre type de profil."

Conditions
└─ "Vous devez accepter les conditions d'utilisation."

Connexion
└─ "Ces identifiants ne correspondent pas à nos enregistrements."
```

### Validation en temps réel (JavaScript)

✅ **Force du mot de passe**
- Affichage de la barre de progression
- Couleur : Rouge → Orange → Vert

✅ **Confirmation de mot de passe**
- Mise en rouge si différent
- Mise à jour à chaque frappe

✅ **Email unique**
- Vérification côté serveur (AJAX optionnel)

---

## 🎯 Cas d'usage

### Cas 1 : Nouvel utilisateur

```
1. Visite site → Clique "S'inscrire"
2. Sélectionne type (Client)
3. Remplit formulaire
4. Clique "S'inscrire"
5. ✅ Automatiquement connecté
6. ➜ Redirigé vers dashboard
```

### Cas 2 : Utilisateur existant

```
1. Visite /login
2. Entre identifiants
3. Clique "Se connecter"
4. ✅ Vérification réussie
5. ➜ Redirigé selon rôle
   - Admin → /admin/dashboard
   - User → /user/dashboard
```

### Cas 3 : Mot de passe oublié

```
1. Sur /login → Clique "Mot de passe oublié?"
2. Entre email → Clique "Envoyer"
3. ✅ Email reçu en 2 minutes
4. Clique lien dans email
5. Nouveau mot de passe → Soumet
6. ✅ Mot de passe changé
7. Peut se connecter
```

---

## 🔐 Sécurité - Points Importants

### ✅ Protections intégrées

```
1. CSRF Protection      → @csrf dans tous les formulaires
2. Password Hashing     → SHA-256 + salt
3. Rate Limiting        → 60 req/min par IP
4. Email Validation     → Vérification format + unique
5. Token Expiration     → 60 minutes reset password
6. Session Security     → HttpOnly cookies
```

### ⚠️ À faire en production

```
1. Passer en HTTPS
2. Configurer CORS si API externe
3. Mettre en place 2FA (optionnel)
4. Activer logs d'authentification
5. Configurer email de secours
6. Tester rate limiting
```

---

## 🧪 Tests Rapides

### Test 1 : Inscription valide

```
Entrées :
  Nom: Jean Kabongo
  Email: jean@example.com
  Téléphone: +243 XXX XXX XXX
  Type: Artisan
  Mot de passe: MySecurePass123!
  
Résultat attendu:
  ✅ Compte créé
  ✅ Auto-connexion
  ✅ Redirection dashboard
```

### Test 2 : Email déjà utilisé

```
Entrées :
  Email: jean@example.com (existant)
  
Résultat attendu:
  ❌ Erreur: "Cette adresse email est déjà utilisée."
```

### Test 3 : Mot de passe faible

```
Entrées :
  Mot de passe: 123
  
Résultat attendu:
  ❌ Erreur: "Minimum 8 caractères"
  ❌ Barre : Rouge
```

### Test 4 : Mots de passe différents

```
Entrées :
  Mot de passe: MySecurePass123
  Confirmation: MySecurePass456
  
Résultat attendu:
  ❌ Erreur: "Mots de passe ne correspondent pas"
```

---

## 📊 Couleurs RDC Utilisées

```
┌──────────────────────────────────────┐
│ DRAPEAU CONGOLAIS                    │
├──────────────────────────────────────┤
│ 🔵 Bleu (#007FFF)  - 1/3 haut        │
│ 🟡 Jaune (#F0B800) - 1/3 milieu      │
│ 🔴 Rouge (#FF4757) - 1/3 bas         │
├──────────────────────────────────────┤
│ UTILISATION                          │
├──────────────────────────────────────┤
│ Bleu   → Login, liens, primaire      │
│ Jaune  → Register, accent            │
│ Rouge  → Erreurs, alertes            │
│ Gris   → Background, texte           │
└──────────────────────────────────────┘
```

---

## 💡 Conseils Pro

### 1. Mot de passe fort
```
❌ Faible : password123
✅ Fort   : My$ecureP@ss2024!
```

### 2. Email unique
```
✅ Utiliser une vraie adresse email
✅ Vous la recevrez après oubli
```

### 3. Type utilisateur important
```
Client   → Cherche services
Artisan  → Propose services
Emploi   → Cherche travail
```

### 4. Conditions d'utilisation
```
✅ Lire avant d'accepter
✅ Liens vers docs complètes
```

---

## 🆘 Besoin d'aide ?

### Problèmes courants

| Problème | Solution |
|----------|----------|
| Oubli identifiants | Cliquer "Mot de passe oublié" |
| Email non reçu | Vérifier spam, tester URL directe |
| Session expirée | Se reconnecter |
| Compte bloqué | Contacter support |

### Contacter le support

📧 **Email:** support@servicerdc.com  
📱 **Téléphone:** +243 XXX XXX XXX  
💬 **Chat:** Disponible sur le site  

---

## 📚 Ressources Utiles

- [Guide Complet](AUTHENTICATION_GUIDE.md)
- [Code Source Auth](app/Http/Controllers/Auth/)
- [Vues Blade](resources/views/auth/)
- [Documentation Laravel](https://laravel.com/docs/authentication)

---

**ServiceRDC - Plateforme de services en République Démocratique du Congo**

*Démarrage rapide réussi !* ✨
