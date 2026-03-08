# MOSALA+ Dashboard - Visual Structure Guide

## 📐 Overall Layout Structure

```
┌─────────────────────────────────────────────────────────────────────────┐
│                                                                         │
│                            TOP NAVBAR (h-20)                           │
│  [Menu ≡] [Logo] [Breadcrumb] │ [Search...] │ [🔔] [Avatar]          │
│                                                                         │
├──────────────────┬──────────────────────────────────────────────────────┤
│                  │                                                      │
│   SIDEBAR        │                    MAIN CONTENT                     │
│   (w-64)         │                                                      │
│                  │  ┌────────────────────────────────────────────────┐ │
│  [MOSALA+]       │  │                                                │ │
│  ─────────────   │  │  Hero Section (Gradient)                      │ │
│  Tableau de      │  │  "Bonjour, [Nom]! 👋"                        │ │
│  bord            │  │                                                │ │
│  Services &      │  ├────────────────────────────────────────────────┤ │
│  Artisans        │  │ [Card] [Card] [Card] [Card]  ← Stats Grid     │ │
│  Hub d'Emplois   │  │ (4 cols desktop, 2 tablet, 1 mobile)         │ │
│  Mes Candidat    │  │                                                │ │
│  Mon Profil      │  ├────────────────────────────────────────────────┤ │
│  ─────────────   │  │ [Tab 1] [Tab 2] [Tab 3] [Tab 4]              │ │
│  Retour au       │  │ ─────────────────────────────────────────── │ │
│  site            │  │                                                │ │
│  ═════════════   │  │ [Jobs List]        │ [Quick Actions]          │ │
│  🔴 Déconnexion  │  │ [Job 1]            │ [Parcourir Services]    │ │
│                  │  │ [Job 2]            │ [Voir Tous Emplois]     │ │
│                  │  │ [Job 3]            │ [Notifications]          │ │
│                  │  │ ...                │                          │ │
│                  │  │                    │ [Activité Récente]      │ │
│                  │  │                    │ • Vous avez postulé...   │ │
│                  │  │                    │ • 2 nouveaux messages    │ │
│                  │  │                    │ • 1 mission en cours     │ │
│                  │  │                                                │ │
│                  │  └────────────────────────────────────────────────┘ │
│                  │                                                      │
│                  │                                                      │
│                  │            [Footer / Scroll Area]                   │
│                  │                                                      │
└──────────────────┴──────────────────────────────────────────────────────┘
```

---

## 🎨 Color Palette Visualization

```
Congo Blue #007FFF       Congo Yellow #F7D000      Congo Red #CE1021
┌────────────────────┐  ┌────────────────────┐    ┌────────────────────┐
│  ███████████████   │  │  ███████████████   │    │  ███████████████   │
│  Primary Actions   │  │  Stats Icons       │    │  Logout/Delete     │
│  Active States     │  │  Warning Badges    │    │  Destructive       │
│  Navigation Links  │  │  Accents           │    │  Critical Actions  │
└────────────────────┘  └────────────────────┘    └────────────────────┘

Mosala Light #F0F4F5
┌────────────────────┐
│  ███████████████   │
│  Global Background │
│  Sidebar BG        │
│  Light Surfaces    │
└────────────────────┘
```

---

## 📊 Statistics Grid Layout

### Desktop (lg: ≥1024px) - 4 Columns
```
┌──────────────────┬──────────────────┬──────────────────┬──────────────────┐
│   Candidatures   │     Services     │     Travaux      │  Notifications   │
│      12          │       8          │       2          │        3         │
└──────────────────┴──────────────────┴──────────────────┴──────────────────┘
```

### Tablet (md: 768-1023px) - 2 Columns
```
┌──────────────────────────────┬──────────────────────────────┐
│      Candidatures            │         Services             │
│          12                  │             8                │
├──────────────────────────────┼──────────────────────────────┤
│       Travaux                │      Notifications           │
│          2                   │             3                │
└──────────────────────────────┴──────────────────────────────┘
```

### Mobile (< 768px) - 1 Column
```
┌──────────────────┐
│  Candidatures    │
│       12         │
├──────────────────┤
│   Services       │
│        8         │
├──────────────────┤
│    Travaux       │
│        2         │
├──────────────────┤
│ Notifications    │
│        3         │
└──────────────────┘
```

---

## 🔄 Tab Navigation Structure

```
Active Tab Example: "Vue d'ensemble"

┌─────────────────────────────────────────────────────────────┐
│ [Vue d'ensemble] [Services & Artisans] [Hub...] [Mes...]   │
│  ════════════════                                           │
│  (active: bottom border Congo Blue, text Congo Blue)        │
└─────────────────────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────────────────────┐
│                     Tab Content Here                        │
│                                                             │
│  [Jobs List]              │  [Quick Actions]               │
│  ─────────────────────── │  ─────────────────              │
│  [Job Card]              │  [Action 1]                     │
│  [Job Card]              │  [Action 2]                     │
│  [Job Card]              │  [Action 3]                     │
│                          │                                 │
│                          │  [Activity Feed]                │
└─────────────────────────────────────────────────────────────┘
```

---

## 💼 Job Card Component

```
┌───────────────────────────────────────────────────────────────────┐
│ ┌──────────┐  Titre du Poste                          Salaire:   │
│ │ A        │  Nom du Recruteur                       ┌────────┐  │
│ │ (Avatar) │  [Localisation Badge] [Type Badge]     │ 600 $  │  │
│ └──────────┘                                         └────────┘  │
└───────────────────────────────────────────────────────────────────┘
   ↑                                                    ↑
   Avatar with initials                         Salary display
   in white on Congo Blue                       (Congo Blue color)
```

---

## 📱 Responsive Behavior

### Desktop View (lg ≥ 1024px)
```
[Fixed Sidebar 256px] [Flexible Main Area]
     ↓
Full-width content, 4-column stats grid, 3-column tab layout
```

### Tablet View (md 768-1023px)
```
[Sidebar - Collapsed] [Flexible Main Area]
     ↓
2-column stats grid, 2-column tab layout
```

### Mobile View (< 768px)
```
[Drawer Sidebar - Hidden] [Full-width Main Area]
     ↓
1-column stats grid, 1-column tab layout, hamburger menu
```

---

## 🎨 Card Styling Hierarchy

### Base Card (Default)
```
┌─────────────────────────────────────┐
│ • Background: White                 │
│ • Border: Gray-100 (1px)            │
│ • Rounded: rounded-2xl (16px)       │
│ • Shadow: shadow-sm (subtle)        │
│ • Padding: p-6 (24px)               │
└─────────────────────────────────────┘
```

### Hover State
```
┌─────────────────────────────────────┐
│ • Background: Still white           │
│ • Border: Congo Blue (changes)      │
│ • Shadow: shadow-md (more prominent)│
│ • Transition: 300ms smooth          │
└─────────────────────────────────────┘
```

### Hero Card
```
┌─────────────────────────────────────┐
│ • Gradient: Congo Blue → Blue-600   │
│ • Text: White                       │
│ • Rounded: rounded-2xl (16px)       │
│ • Padding: p-8 (32px)               │
│ • Contains: Title + Subtitle        │
└─────────────────────────────────────┘
```

---

## 🧭 Navigation Hierarchy

### Sidebar Navigation
```
┌─ MAIN NAVIGATION ─────────────────────┐
│ ┌─────────────────────────────────┐   │
│ │ Tableau de bord                 │   │ (links to /user/dashboard)
│ └─────────────────────────────────┘   │
│ ┌─────────────────────────────────┐   │
│ │ Services & Artisans             │   │ (links to /user/services)
│ └─────────────────────────────────┘   │
│ ┌─────────────────────────────────┐   │
│ │ Hub d'Emplois                   │   │ (links to /user/jobs)
│ └─────────────────────────────────┘   │
│ ┌─────────────────────────────────┐   │
│ │ Mes Candidatures                │   │ (links to /user/applications)
│ └─────────────────────────────────┘   │
│ ┌─────────────────────────────────┐   │
│ │ Mon Profil                      │   │ (links to /user/profile)
│ └─────────────────────────────────┘   │
│                                        │
├─ SUPPORT ────────────────────────────  │
│ ┌─────────────────────────────────┐   │
│ │ Retour au site                  │   │ (links to /)
│ └─────────────────────────────────┘   │
│                                        │
├─ LOGOUT ─────────────────────────────  │
│ ┌─────────────────────────────────┐   │
│ │ 🔴 Déconnexion                  │   │ (POST /logout)
│ └─────────────────────────────────┘   │
└────────────────────────────────────────┘
```

---

## 🔍 Navbar Component Breakdown

```
LEFT SECTION          CENTER SECTION       RIGHT SECTION
┌──────────────────┐  ┌─────────────────┐  ┌──────────────┐
│ [≡] [Logo]       │  │ [🔍 Search...] │  │ [🔔] [Avatar]│
│  breadcrumb      │  │                 │  │              │
└──────────────────┘  └─────────────────┘  └──────────────┘
  Mobile menu       Search bar centered   Notifications &
  & branding       (Rechercher un         user profile
                    service...)
```

---

## 🎯 Tab Content Layout (Overview)

```
┌─────────────────────────────────────────────────────────────┐
│                                                             │
│  lg: 3-column layout (1fr 1fr 1fr)                        │
│  md: 1-column layout                                       │
│  sm: 1-column layout                                       │
│                                                             │
│  ┌───────────────────────┐  ┌──────────────────────┐      │
│  │                       │  │  [Quick Actions]     │      │
│  │  [Jobs List]          │  │                      │      │
│  │  ─────────────        │  │  [Parcourir...]      │      │
│  │                       │  │  [Voir Tous...]      │      │
│  │  [Job Item]           │  │  [Notifications...]  │      │
│  │  [Job Item]           │  │                      │      │
│  │  [Job Item]           │  │  [Activity Feed]     │      │
│  │  [Job Item]           │  │                      │      │
│  │  [Job Item]           │  │  • Activité 1        │      │
│  │  [Job Item]           │  │  • Activité 2        │      │
│  │                       │  │  • Activité 3        │      │
│  │  [See More...]        │  │                      │      │
│  │                       │  │                      │      │
│  └───────────────────────┘  └──────────────────────┘      │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎬 Animation & Transitions

### Tab Switching
```
Active Tab Content (opacity: 1)
            ↓ (300ms fade + slide)
Hidden Tab Content (opacity: 0)
            ↓ (user clicks another tab)
New Active Content (opacity: 1, x-transition)
```

### Hover Effects
```
Card Default State:
  Border: gray-200 (light)
  Shadow: shadow-sm (subtle)

  ↓ (300ms transition-all)

Card Hover State:
  Border: congo-blue (blue)
  Shadow: shadow-md (prominent)
  Text: congo-blue (on some elements)
```

---

## 📐 Spacing & Size Reference

### Font Sizes
```
H1: text-4xl  (36px)  - Hero title
H2: text-2xl  (24px)  - Section titles
H3: text-lg   (18px)  - Card titles
P:  text-sm   (14px)  - Body text
Small: text-xs (12px) - Labels
```

### Padding & Margins
```
p-4   = 16px   (small cards)
p-6   = 24px   (default cards)
p-8   = 32px   (hero sections)

gap-4 = 16px   (small grids)
gap-6 = 24px   (default grids)
```

### Icon Sizes
```
w-5 h-5      = 20px   (navbar icons)
w-12 h-12    = 48px   (avatar)
w-14 h-14    = 56px   (stat icons)
```

---

## ✅ Component Checklist

### ✅ Sidebar
- [x] Logo section (h-20)
- [x] Navigation menu (5 items)
- [x] Active state styling
- [x] Support section
- [x] Logout button (Congo Red)
- [x] Responsive drawer (mobile)

### ✅ Navbar
- [x] Menu toggle (mobile)
- [x] Logo/Breadcrumb (mobile/desktop)
- [x] Search bar (centered, icon)
- [x] Notification bell (with dot)
- [x] User avatar (with initials)
- [x] Sticky positioning

### ✅ Dashboard
- [x] Hero section (gradient)
- [x] Statistics grid (4 cards, responsive)
- [x] Tab navigation (4 tabs)
- [x] Tab content switching
- [x] Jobs list (card components)
- [x] Quick actions sidebar
- [x] Activity feed

### ✅ Design System
- [x] National colors (#007FFF, #F7D000, #CE1021)
- [x] Typography (Poppins, Inter)
- [x] Spacing consistency
- [x] Border radius standards
- [x] Shadow hierarchy
- [x] Responsive breakpoints

---

**Last Updated**: January 13, 2026  
**Status**: ✅ Complete & Production Ready
