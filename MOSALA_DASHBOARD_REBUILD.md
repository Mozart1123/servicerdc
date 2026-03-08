# MOSALA+ Dashboard Reconstruction - Complete Guide

## 📋 Overview

Le dashboard MOSALA+ a été complètement reconstruit avec une architecture moderne, responsive et professionnelle. Voici les détails techniques et visuels.

---

## 🎨 Design Architecture

### Palette Nationale
- **Congo Blue** (#007FFF): Actions primaires, icônes, éléments actifs
- **Congo Yellow** (#F7D000): Accents, icônes statistiques, badges
- **Congo Red** (#CE1021): Boutons destructifs (Logout, Delete)
- **Background Global** (#F0F4F5): Arrière-plan page entière et sidebar

### Typography
- **Headers**: Poppins (Semibold, Bold, Black)
- **Body**: Inter (Regular, Medium, Semibold)
- **Font sizes**: Hiérarchie complète de H1 à H6

---

## 🏗️ Structure de Fichiers

```
resources/views/
├── layouts/
│   └── app.blade.php          (Master layout avec Sidebar + Navbar)
├── components/
│   ├── sidebar.blade.php      (Navigation gauche)
│   ├── navbar.blade.php       (Navbar supérieure avec search)
│   └── ...
├── user/
│   ├── dashboard.blade.php    (Page principale du dashboard)
│   └── partials/
│       ├── services.blade.php
│       ├── jobs.blade.php
│       └── applications.blade.php
```

---

## 🎯 Composants Principaux

### 1. Sidebar (Composant: `sidebar.blade.php`)

**Caractéristiques:**
- Background: Solid #F0F4F5 avec border droit gris clair
- Logo MOSALA+ en haut avec icône hammer bleu
- Navigation primaire:
  - Tableau de bord
  - (Admin) Gérer les Jobs, Utilisateurs, Catégories
  - (Artisan) Mes Services
  - Mon Profil
- Bouton "Retour au site" en bas
- Bouton "Déconnexion" (Congo Red) au footer
- **Active State**: Lien actif avec background Congo Blue, texte blanc, border-left épais

**Responsive:**
- Desktop (lg): Visible en permanence
- Mobile/Tablet: Drawer collapsible, toggle en navbar

### 2. Navbar Supérieure (Composant: `navbar.blade.php`)

**Layout:**
```
[Menu Toggle] [Logo Mobile] [Breadcrumb]  |  [Search Bar]  |  [Bell Icon] [User Avatar]
```

**Éléments:**
- Bouton Menu mobile (toggle sidebar)
- Search bar centré: "Rechercher un service ou un artisan..."
- Notification bell avec dot rouge
- User avatar avec nom et email

**Sticky:** Position fixed en haut avec z-index 40

---

## 📊 Dashboard Vue d'Ensemble

### Hero Section (Welcome)
```blade
Bonjour, [Nom]! 👋
Bienvenue sur votre tableau de bord MOSALA+
```

**Style:**
- Gradient: Congo Blue → Blue-600
- Texte blanc
- Icône chart-line jaune (Congo Yellow)
- Padding 8 (2rem)
- Rounded-2xl

### Statistics Grid (4 Colonnes)

#### Card 1: Candidatures
- **Icon**: File-alt (Congo Blue)
- **Stats**: Nombre total de candidatures
- **Sous-texte**: "{total} offres disponibles"

#### Card 2: Services
- **Icon**: Star (Congo Yellow)
- **Stats**: Nombre de services disponibles
- **Sous-texte**: "Vérifiés et actifs"

#### Card 3: Travaux en Cours
- **Icon**: Hammer (Purple-600)
- **Stats**: Nombre de missions actives
- **Sous-texte**: "En cours"

#### Card 4: Notifications
- **Icon**: Bell (Orange-600)
- **Stats**: Notifications non lues
- **Sous-texte**: "À consulter"

**Style des Cards:**
- Background blanc
- Border gris-100
- Rounded-2xl
- Shadow-sm
- Hover: Shadow-md, Border Congo Blue

### Tabs Navigation

**Onglets:**
1. **Vue d'ensemble** (📊)
2. **Services & Artisans** (⭐)
3. **Hub d'Emplois** (💼)
4. **Mes Candidatures** (📄)

**Active State:**
- Border-bottom Congo Blue 2px
- Text Congo Blue (semibold)

**Position:** Sticky top-20 (sous navbar)

### Tab Content: Vue d'Ensemble

**Layout 3-colonnes (lg) / 1-colonne (mobile):**

**Colonne 1-2: Dernières Offres d'Emploi**
- Titre: "Dernières Offres d'Emploi" avec lien "Voir tout →"
- Cartes emploi:
  ```
  [Avatar Initials] | Titre Emploi
                    | Recruteur
                    | [Localisation] [Type Contrat (badge Congo Blue)]
                    | [Prix en Congo Blue]
  ```

**Colonne 3: Sidebar Actions**

**Section 1: Actions Rapides**
- Gradient Congo Blue clair
- 3 boutons:
  - "Parcourir Services" → route user.services.index
  - "Voir Tous Emplois" → route user.jobs.index
  - "Notifications" → route user.notifications.index
- Hover: Background Congo Blue, text blanc

**Section 2: Activité Récente**
- Points colorés (Congo Blue, Yellow, Purple)
- 3 lignes d'activité exemple

---

## ⚙️ Technologie

### Frontend Stack
- **Framework**: Laravel Blade + Alpine.js
- **Styling**: Tailwind CSS 3+ (via CDN)
- **Icons**: Font Awesome 6.4.0 (via CDN)
- **Fonts**: Google Fonts (Poppins, Inter)

### Configuration Tailwind (dans `<head>`)
```javascript
tailwind.config = {
    theme: {
        extend: {
            colors: {
                'congo-blue': '#007FFF',
                'congo-yellow': '#F7D000',
                'congo-red': '#CE1021',
                'mosala-light': '#F0F4F5',
            },
            fontFamily: {
                'inter': ['Inter', 'sans-serif'],
                'poppins': ['Poppins', 'sans-serif'],
            },
            boxShadow: {
                'soft': '0 4px 20px rgba(0, 0, 0, 0.05)',
            }
        }
    }
}
```

### Alpine.js Data
```javascript
function dashboardApp() {
    return {
        activeTab: 'vue-ensemble',
        init() {
            const urlTab = new URLSearchParams(window.location.search).get('tab');
            if (urlTab) this.activeTab = urlTab;
        }
    };
}
```

---

## 📱 Responsive Breakpoints

| Device | Layout | Sidebar | Grid |
|--------|--------|---------|------|
| Mobile (< 640px) | 1 column | Drawer | 1 col stats |
| Tablet (640-1024px) | 1.5 column | Drawer | 2 col stats |
| Desktop (> 1024px) | Full | Permanent | 4 col stats, 3 col tabs |

---

## 🎨 Spacing & Border Radius

- **p-6**: Padding standard pour cartes (24px)
- **p-8**: Padding large sections (32px)
- **gap-6**: Espacement grille (24px)
- **rounded-xl**: Cards (12px)
- **rounded-2xl**: Sections principales (16px)
- **rounded-full**: Avatars circulaires

---

## 🔄 Interactive Elements

### Tab Switching (Alpine.js)
```alpine
@click="activeTab = 'vue-ensemble'"
:class="activeTab === 'vue-ensemble' ? 'border-congo-blue text-congo-blue' : 'text-gray-600'"
```

### Hover Effects
- Cards: `hover:shadow-md hover:border-congo-blue transition-all`
- Buttons: `hover:bg-congo-blue hover:text-white transition-all`
- Links: `hover:text-congo-blue transition-colors`

---

## 📥 Data Flow from Controller

### DashboardController.php

```php
$stats = [
    'applied_jobs_count' => 0,       // Candidatures
    'total_jobs' => 0,               // Emplois disponibles
    'total_services' => 0,           // Services disponibles
    'active_missions' => 0,          // Travaux en cours
    'unread_notifications' => 0,     // Notifications
];

$recentJobs;      // 6 derniers emplois
$allJobs;         // Tous les emplois
$categories;      // Catégories services
$myApplications;  // Candidatures utilisateur
```

Passés via `compact()` à la vue.

---

## ✅ Checklist d'Implémentation

- [x] Master layout (app.blade.php) avec sidebar + navbar
- [x] Composants sidebar et navbar
- [x] Dashboard avec hero section
- [x] Statistics grid (4 cartes)
- [x] Tabs navigation sticky
- [x] Tab content "Vue d'ensemble"
- [x] Jobs list avec avatars initials
- [x] Actions rapides sidebar
- [x] Activité récente
- [x] Tailwind CDN + configuration
- [x] Alpine.js intégration
- [x] Responsive design
- [x] Color palette nationale

---

## 🚀 Utilisation

1. **Naviguer au Dashboard**: `/user/dashboard`
2. **Changer d'onglet**: Cliquer sur les onglets ou passer `?tab=emplois` en URL
3. **Sidebar**: Cliquer sur les liens pour naviguer
4. **Search**: Barre de recherche en navbar
5. **Actions Rapides**: Utiliser les boutons du sidebar

---

## 📝 Notes de Développement

- Tous les composants utilisent **flex** ou **grid** pour alignement
- Shadows toutes "shadow-sm" sauf hover "shadow-md"
- Z-index: navbar=40, sidebar=50, modal=100
- Transitions fluides 300ms
- Scrollbar personnalisée (bleu Congo)
- Support complet RTL/LTR (structure HTML neutre)

---

## 🔗 Fichiers Liés

- `/resources/views/layouts/app.blade.php` - Master layout
- `/resources/views/components/sidebar.blade.php` - Sidebar navigation
- `/resources/views/components/navbar.blade.php` - Top navbar
- `/resources/views/user/dashboard.blade.php` - Main dashboard
- `/app/Http/Controllers/User/DashboardController.php` - Backend logic
