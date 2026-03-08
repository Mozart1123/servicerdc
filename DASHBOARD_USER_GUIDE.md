# 📚 GUIDE D'UTILISATION - DASHBOARD SERVICERDC

## 🎯 Vue d'ensemble

Le dashboard ServiceRDC est conçu pour offrir une **expérience utilisateur complète** en permettant aux utilisateurs de gérer:
- **Services offerts** (pour les artisans)
- **Candidatures** (pour les chercheurs d'emploi)
- **Missions** (travaux en cours)
- **Demandes personnalisées** (nouveaux besoins)

---

## 🚀 Accès au Dashboard

### URL
```
http://localhost:8000/user/dashboard
```

### Authentification Requise
- L'utilisateur doit être connecté
- Les rôles supportés: `user`, `admin`, `super_admin`
- Les types d'utilisateurs: `client`, `artisan`, `job_seeker`

---

## 📊 Section 1: APERÇU (Overview)

**Accessible via**: Onglet "📊 Aperçu"

### Contenu
```
┌─────────────────────────────────────────┐
│ Statistiques en Temps Réel               │
├─────────────────────────────────────────┤
│ • Services disponibles: X                │
│ • Emplois disponibles: X                 │
│ • Candidatures envoyées: X               │
│ • Travaux en cours: X                    │
│ • Notifications non lues: X              │
└─────────────────────────────────────────┘
```

### Données Affichées
- **Offres récentes**: 6 dernières offres d'emploi
- **Services populaires**: 6 services les plus actifs
- **Notifications**: 5 notifications les plus récentes
- **Statut utilisateur**: Type et rôle

### Interaction
- Cliquer sur une offre pour voir détails
- Cliquer sur un service pour consulter
- Cliquer sur notification pour lire

---

## 🛠️ Section 2: MES TRAVAUX (Missions)

**Accessible via**: Onglet "🛠️ Mes Travaux"

### Pour les CLIENTS
**Rôle**: `user` avec `user_type = client`

#### Affichage
```
Mes Travaux en tant que CLIENT
├── Travaux en cours (3)
│   ├── [Réparation climatiseur]
│   │   ├── Artisan: Jean Dupont
│   │   ├── Statut: IN_PROGRESS
│   │   ├── Deadline: 15/01/2026
│   │   └── Actions: [Voir détails] [Evaluer]
│   └── ...
├── Travaux terminés (5)
│   ├── [Peinture maison]
│   │   ├── Évaluation: ⭐⭐⭐⭐⭐
│   │   ├── Feedback: "Excellent travail!"
│   │   └── Date: 10/01/2026
│   └── ...
└── Travaux annulés (0)
```

#### Actions Possibles
- **Voir Détails**: Consulter description complète
- **Évaluer**: Laisser note et commentaire
- **Contacter**: Envoyer message à l'artisan
- **Annuler**: Annuler mission (si pas commencée)

### Pour les ARTISANS
**Rôle**: `user` avec `user_type = artisan`

#### Affichage
```
Mes Travaux en tant qu'ARTISAN
├── Travaux en attente (2)
│   ├── [Installation électrique]
│   │   ├── Client: Marie Martin
│   │   ├── Ville: Kinshasa
│   │   ├── Budget: 150 000 - 200 000 FC
│   │   └── Actions: [Accepter] [Refuser]
│   └── ...
├── Travaux en cours (4)
│   ├── [Réparation plomberie]
│   │   ├── Client: Pierre Dubois
│   │   ├── Statut: IN_PROGRESS
│   │   ├── Deadline: 14/01/2026
│   │   └── Actions: [Marquer complét] [Contacter client]
│   └── ...
└── Travaux complétés (10)
```

#### Actions Possibles
- **Accepter**: Accepter mission (change statut pending → in_progress)
- **Refuser**: Rejeter mission (change statut pending → cancelled)
- **Marquer Complété**: Terminer travail (change statut → completed)
- **Contacter Client**: Envoyer message

---

## ⭐ Section 3: SERVICES DISPONIBLES

**Accessible via**: Onglet "⭐ Services"

### Fonctionnalités

#### Filtres Disponibles
```
┌─ Filtres Actifs ─────────────┐
│ • Catégorie: [Sélectionner▼]  │
│ • Localisation: [Saisir...]   │
│ • Prix: Min [____] Max [____]  │
│ • Recherche: [_____________]   │
│         [Appliquer filtres]    │
└───────────────────────────────┘
```

#### Affichage des Services
```
Résultats: 24 services trouvés

[Service Card 1]
├── Titre: Réparation climatiseur
├── Artisan: Jean Dupont
├── Catégorie: Maintenance
├── Prix: 50 000 FC
├── Localisation: Kinshasa
├── Évaluation: ⭐⭐⭐⭐ (4.5/5)
├── Disponible: Oui ✓
└── [Voir détails] [Demander service]

[Service Card 2]
...

[Pagination: << 1 2 3 >> ]
```

#### Actions
- **Voir Détails**: Affiche la page complète du service
- **Demander Service**: Formulaire pour demander le service
- **Contacter Artisan**: Chat direct avec prestataire

### Filtrage Avancé
```php
// Exemple de requête appliquée
$services = Service::active()
    ->verified()
    ->byCategory('plomberie')
    ->byLocation('Kinshasa')
    ->whereBetween('price', [10000, 100000])
    ->search('installation')
    ->paginate(12);
```

---

## 💼 Section 4: EMPLOIS ET CANDIDATURES

**Accessible via**: Onglet "💼 Emplois"

### Visualisation des Offres

#### Interface
```
EMPLOIS DISPONIBLES

[Filtres]
├── Catégorie: [▼]
├── Type Contrat: [CDI▼] [CDD▼] [Freelance▼]
├── Localisation: [Saisir...]
└── Recherche: [____________]

[OFFRES]
├── [Offre 1]
│   ├── Titre: Développeur PHP Senior
│   ├── Entreprise: TechCorp RDC
│   ├── Type: CDI
│   ├── Localisation: Kinshasa
│   ├── Salaire: 100 000 - 150 000 FC/mois
│   ├── Deadline: 30/01/2026
│   ├── Status candidature: 🟡 Envoyée (pending)
│   └── [Voir détails] [Retirer candidature]
│
├── [Offre 2]
│   ├── Titre: Responsable Marketing
│   ├── Status candidature: ❌ Refusée
│   └── ...
│
└── [Offre 3]
    ├── Titre: Technicien Maintenance
    ├── Status candidature: ✅ Acceptée
    └── ...
```

### Processus de Candidature

#### Étape 1: Sélectionner l'Offre
```
Cliquer sur "[Voir détails]"
```

#### Étape 2: Voir Détails
```
Page d'offre complète
├── Description complète
├── Exigences
├── Avantages
├── Profil du recruteur
└── [Postuler maintenant] [Partager]
```

#### Étape 3: Postuler
```
Modal de candidature
├── Titre offre: [pré-rempli]
├── CV: [Auto-attaché depuis profil]
├── Lettre de motivation: [Optionnel]
│   "Pourquoi êtes-vous un bon candidat?"
│   [___________________]
├── [Annuler] [Envoyer Candidature]
└── ✅ Confirmé → Notification admin
```

### Suivi des Candidatures

**Route**: `/user/my-applications`

#### Tableau de Bord
```
MES CANDIDATURES

Stats rapides:
├── En attente: 3
├── Acceptées: 2
└── Refusées: 1

Historique détaillé:
├── [Candidature 1]
│   ├── Offre: Développeur PHP Senior
│   ├── Statut: 🟡 En attente
│   ├── Date: 10/01/2026
│   ├── Notes admin: -
│   └── [Voir offre] [Retirer]
│
├── [Candidature 2]
│   ├── Offre: Responsable Marketing
│   ├── Statut: ✅ Acceptée
│   ├── Date: 08/01/2026
│   ├── Notes: "Entretien prévu 20/01/2026"
│   └── [Contacter employeur]
│
└── [Candidature 3]
    ├── Offre: Technicien Support
    ├── Statut: ❌ Refusée
    ├── Date: 05/01/2026
    └── [Voir autres offres]
```

---

## 📝 Section 5: DEMANDES DE SERVICES PERSONNALISÉS (NOUVEAU!)

**Accessible via**: Onglet "📝 Demandes" ou `/user/service-requests`

### ✨ Nouvelle Fonctionnalité Complète

#### Cas d'Usage
```
Scénario: Utilisateur cherche service non répertorié

1. Utilisateur visite dashboard
2. Va à onglet "📝 Demandes"
3. Voit formulaire "Je n'ai pas trouvé ce que je cherche"
4. Remplit informations détaillées
5. Soumet demande
6. ✉️ Notification envoyée automatiquement aux ADMINS
7. Admin traite demande (24-48h généralement)
8. Admin envoie réponse personnalisée
9. Utilisateur reçoit notification + voir réponse
10. ✅ Statut change: "Traitée"
```

### Formulaire de Demande

#### Champs Disponibles
```
📝 NOUVELLE DEMANDE DE SERVICE

[1] Quel service cherchez-vous? * (Obligatoire)
    [______________________________]
    Exemple: "Réparation de climatisation", "Nettoyage professionnel"

[2] Catégorie (Optionnel)
    [______________________________]
    Exemple: "Maintenance", "Construction", "Services ménagers"

[3] Description détaillée (Optionnel)
    [_________________________________
     _________________________________
     _________________________________]
    Décrivez vos besoins en détail pour que nous puissions mieux vous aider

[4] Localisation (Optionnel)
    [____________________]
    Exemple: "Kinshasa", "Goma", "Bukavu"

[5] Numéro de téléphone (Optionnel)
    [____________________]
    Format: +243... ou +33...

[6] Budget minimum (FC) (Optionnel)
    [____________________]

[7] Budget maximum (FC) (Optionnel)
    [____________________]

[8] Niveau d'urgence *
    ⭕ 🟢 Basse (Pas de limite de temps)
    ⭕ 🟡 Moyenne (Quelques jours) [Sélectionné par défaut]
    ⭕ 🔴 Élevée (Cette semaine)
    ⭕ 🔴🔴 Urgente (Aujourd'hui/Demain)

    [Envoyer la Demande]
```

### Notification Admin

**Les administrateurs reçoivent**:
```
🔔 NOTIFICATION
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Titre: Nouvelle demande de service personnalisée

Contenu:
├── Utilisateur: Marie Martin
├── Service demandé: Réparation climatiseur
├── Localisation: Kinshasa
├── Urgence: Élevée 🔴
├── Budget: 150 000 - 250 000 FC
├── Description: [Description utilisateur]
├── Téléphone: +243 85 123 4567
└── Actions: [Voir demande complète] [Répondre]

La demande attend votre réponse!
```

### Historique des Demandes

**Route**: `/user/service-requests`

#### Affichage
```
📋 HISTORIQUE DE VOS DEMANDES

Demande 1 - Réparation climatiseur
├── Statut: ✅ Traitée
├── Soumis le: 10/01/2026
├── Catégorie: Maintenance
├── Localisation: Kinshasa
├── Urgence: Élevée 🔴
├── Budget: 150 000 - 250 000 FC
├── Description: "J'ai un climatiseur qui ne refroidit plus..."
│
└── ✅ RÉPONSE DE L'ADMINISTRATEUR:
    │
    ├── Message: "Nous avons trouvé un excellent artisan pour vous!
    │             Voici contact: Jean Dupont - Tél: +243 81..."
    │
    ├── Répondu par: Admin System
    └── Date: 11/01/2026 à 14:30

Demande 2 - Installation panneau solaire
├── Statut: 🟡 En attente
├── Soumis le: 12/01/2026
├── Catégorie: Énergie verte
├── Localisation: Bukavu
├── Urgence: Moyenne 🟡
├── Budget: 2 000 000 - 5 000 000 FC
└── Description: "Veux installer système solaire résidentiel..."

Demande 3 - Consultant IT
├── Statut: 🟡 En attente
├── Soumis le: 09/01/2026
├── ...
```

### Consultation d'une Demande

**Route**: `/user/service-requests/{id}`

#### Page Détaillée
```
← Retour aux Demandes

DEMANDE #1: Réparation climatiseur          [✅ Traitée]

Soumis le: 10/01/2026 à 09:15

📋 DÉTAILS DE LA DEMANDE
├── Catégorie: Maintenance
├── Localisation: Kinshasa
├── Urgence: Élevée 🔴
└── Budget: 150 000 - 250 000 FC

📝 DESCRIPTION
"J'ai un climatiseur LG qui ne refroidit plus depuis une semaine.
 Il fait un bruit inhabituel au démarrage. Besoin d'une réparation
 ou remplacement urgent."

──────────────────────────────────────────

✅ RÉPONSE DE L'ADMINISTRATEUR (11/01/2026)

Répondu par: Admin System

Message:
"Bonjour Marie,

Nous avons bien traité votre demande et trouvé un artisan
spécialisé pour vous!

Artisan recommandé:
├── Nom: Jean Dupont
├── Spécialité: Climatisation & Réfrigération
├── Expérience: 8 ans
├── Évaluation: ⭐⭐⭐⭐⭐ (4.8/5)
├── Téléphone: +243 81 123 4567
└── Disponibilité: Dès demain!

Veuillez le contacter directement à ce numéro ou utiliser
notre plateforme pour lui envoyer un message.

Cordialement,
Équipe ServiceRDC"

📞 INFORMATIONS DE CONTACT
├── Votre téléphone: +243 85 234 5678
└── Votre email: marie.martin@gmail.com

📅 HISTORIQUE
├── 🔵 Demande soumise - 10/01/2026 09:15
└── ✅ Demande traitée - 11/01/2026 14:30

[Contacter le support]
```

---

## 🎨 Interactions & Transitions

### Navigation entre Onglets
```
Utilisateur clique sur "⭐ Services"
    ↓
L'onglet s'active (highlight blue)
    ↓
Contenu transition (fade in 300ms)
    ↓
Historique URL mis à jour (?tab=services)
```

### Formulaires
```
Utilisateur saisit données
    ↓
Validation en temps réel (Alpine.js)
    ↓
Bouton "[Envoyer]" activé
    ↓
Submit → Loading spinner
    ↓
Réponse serveur
    ↓
Succès → Message vert + Refresh page
    ↓
Erreur → Message rouge + Instructions
```

---

## 🔔 Système de Notifications

### Types de Notifications
```
1. 📋 Nouvelle candidature reçue
   "Marie Martin a postulé à 'Développeur PHP Senior'"

2. 📋 Réponse à candidature
   "Votre candidature pour 'Manager Marketing' a été ACCEPTÉE"

3. 🛠️ Mise à jour mission
   "Jean Dupont a terminé la réparation climatiseur"

4. 📝 Réponse demande personnalisée
   "Votre demande 'Réparation clim' a reçu une réponse"

5. 💬 Nouveau message
   "Vous avez un nouveau message de Jean Dupont"
```

### Notification Badge
```
En haut du dashboard:
┌──────────────────────────────┐
│ 🔔 Notifications (3 non lues) │
│                              │
│ ✓ Nouvelle candidature       │
│ ✓ Réponse à candidature      │
│ ✓ Mise à jour mission        │
│                              │
│ [Voir tout]                  │
└──────────────────────────────┘
```

---

## ⚙️ Paramètres & Profil

**Route**: `/user/profile`

### Actions Possibles
- 👤 Modifier profil (nom, téléphone, bio)
- 🔐 Changer mot de passe
- 📧 Mettre à jour email
- 🔔 Gérer préférences notifications
- 🌙 Mode sombre/clair

---

## 💡 Tips & Bonnes Pratiques

### Pour les Clients
1. **Descriptions précises**: Plus vous décrivez votre besoin, meilleure sera la réponse
2. **Budget réaliste**: Indiquez votre budget pour obtenir des propositions adéquates
3. **Localisation exacte**: Mentionnez précisément votre ville pour accélérer traitement
4. **Suivi**: Vérifiez notifications régulièrement pour réponses admins

### Pour les Artisans
1. **Profil complet**: Complétez votre profil pour attraire plus de clients
2. **Services clairs**: Décrivez vos services en détail
3. **Prix compétitifs**: Consultez les offres similaires
4. **Disponibilité**: Mettez à jour votre statut disponibilité régulièrement

### Pour Tous
1. **Messages professionnels**: Utilisez langage courtois et professionnel
2. **Réactivité**: Répondez rapidement aux demandes/messages
3. **Évaluations**: Laissez des retours honnêtes et constructifs
4. **Sécurité**: Ne partagez pas d'infos sensibles par chat

---

## 🆘 Dépannage Commun

### Je ne vois pas mon dashboard
```
❌ Problème: Page blanche/erreur 404
✅ Solution: 
  1. Vérifiez que vous êtes connecté
  2. Essayez /user/dashboard directement
  3. Videz le cache du navigateur (Ctrl+Shift+Delete)
```

### Mes filtres ne fonctionnent pas
```
❌ Problème: Les filtres n'affichent pas les résultats
✅ Solution:
  1. Vérifiez votre connexion réseau
  2. Assurez-vous qu'il y a des données (services/emplois)
  3. Réduisez les filtres (enlever certains)
  4. Rafraîchissez la page (F5)
```

### Je ne reçois pas de notification
```
❌ Problème: Les notifications n'arrivent pas
✅ Solution:
  1. Vérifiez les paramètres de notification
  2. Vérifiez que vous n'êtes pas en "mode Ne pas déranger"
  3. Consultez `/user/notifications` manuellement
  4. Contactez le support si problème persiste
```

---

## 📞 Support

- **Email**: support@servicerdc.cd
- **Chat**: Disponible sur la plateforme
- **Téléphone**: +243 (0) 81 123 4567
- **Horaires**: Lundi-Vendredi 8h-17h (Fuseau horaire RDC)

---

**Version**: 1.0.0  
**Dernière mise à jour**: 13 Janvier 2026  
**Statut**: ✅ Complet et Fonctionnel
