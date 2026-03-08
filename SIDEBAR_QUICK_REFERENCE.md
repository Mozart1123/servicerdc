# MOSALA+ Sidebar Navigation - Quick Reference Guide

## Visual Layout

```
┌─────────────────────────────────────────────────────────────────────────┐
│                                                                         │
│  ┌──────────────┐  ┌────────────────────────────────────────────────┐  │
│  │              │  │                                                │  │
│  │   SIDEBAR    │  │              NAVBAR (h-20)                    │  │
│  │   (w-64)     │  │  [☰] [Breadcrumb] [Search] [🔔] [👤]         │  │
│  │              │  │                                                │  │
│  │              │  ├────────────────────────────────────────────────┤  │
│  │  MOSALA+     │  │                                                │  │
│  │  ───────────  │  │                                                │  │
│  │              │  │         MAIN CONTENT AREA                     │  │
│  │ 📊 Vue d'en  │  │         (flex-1, overflow-y-auto)            │  │
│  │ 🛠️  Services  │  │                                                │  │
│  │ 💼 Emplois   │  │  ┌─────────────────────────────────────────┐  │  │
│  │ 📄 Candidat  │  │  │  Bonjour! Welcome Hero Section         │  │  │
│  │              │  │  │  (gradient: Congo Blue → Blue 600)     │  │  │
│  │ ────────────  │  │  └─────────────────────────────────────────┘  │  │
│  │ 👤 Mon Profil│  │                                                │  │
│  │              │  │  ┌──────────┬──────────┬──────────┬────────┐  │  │
│  │              │  │  │Candidat. │ Services │ Travaux  │  Notif │  │  │
│  │              │  │  │ (Stats)  │ (Stats)  │ (Stats)  │(Stats) │  │  │
│  │              │  │  └──────────┴──────────┴──────────┴────────┘  │  │
│  │ ────────────  │  │                                                │  │
│  │ 🚪 Déconnex. │  │  ┌──────────────────────────────────────────┐  │  │
│  │   (Red)      │  │  │     Recent Jobs & Activity              │  │  │
│  │              │  │  │     (Overview Content)                  │  │  │
│  │              │  │  │     Scrolls independently               │  │  │
│  │              │  │  │     Sidebar stays fixed                 │  │  │
│  │              │  │  │     Navbar stays sticky                 │  │  │
│  │              │  │  └──────────────────────────────────────────┘  │  │
│  │              │  │                                                │  │
│  │              │  │  (Content continues...)                       │  │
│  │              │  │                                                │  │
│  └──────────────┘  └────────────────────────────────────────────────┘  │
│                                                                         │
│  Fixed: w-64        Sticky: h-20            Scrollable: flex-1        │
│  Full Height        Top Navbar              Remaining Space           │
│  Left Column        Stays Visible           Main Content              │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

## Sidebar Navigation Map

```
SIDEBAR (Fixed, Left, w-64)
├── Logo Section (h-20)
│   ├── Icon: ⚒️
│   ├── Text: "MOSALA+"
│   └── Background: White
│
├── TABLEAU DE BORD
│   ├── 📊 Vue d'ensemble → user.dashboard
│   │   └── Active: Congo Blue (#007FFF)
│   ├── 🛠️  Services & Artisans → user.services.index
│   │   └── Icon: Congo Yellow accent
│   ├── 💼 Hub d'Emplois → user.jobs.index
│   │   └── Icon: Congo Blue
│   └── 📄 Mes Candidatures → user.applications.index
│       └── Icon: Congo Blue
│
├── ADMINISTRATION (Conditional - Admin Only)
│   ├── 📋 Tableau d'Admin → admin.dashboard
│   ├── 💼 Gérer les Jobs → admin.jobs.index
│   └── 👥 Utilisateurs → admin.users.index
│
├── MON COMPTE
│   └── 👤 Mon Profil → user.profile.edit
│
└── Déconnexion (Bottom)
    ├── Button Style: Congo Red (#CE1021)
    ├── Icon: 🚪
    └── Action: POST logout
```

## Color Usage Guide

| Component | Color | Hex Code | Usage |
|-----------|-------|----------|-------|
| **Active Nav Item** | Congo Blue | #007FFF | Text + Border-left |
| **Nav Icon (Hover)** | Congo Blue | #007FFF | Icon color on hover |
| **Accent Icon** | Congo Yellow | #F7D000 | Star, warning badges |
| **Logout Button** | Congo Red | #CE1021 | Background + Text |
| **Sidebar BG** | MOSALA Light | #F0F4F5 | Sidebar background |
| **Page BG** | MOSALA Light | #F0F4F5 | Body background |
| **Card BG** | White | #FFFFFF | Content cards |
| **Text Primary** | Gray 900 | #111827 | Main text |
| **Text Secondary** | Gray 600 | #475569 | Nav items |
| **Border** | Gray 200 | #E5E7EB | Card borders |

## Active State Styling

```css
.active-nav-link {
    border-left: 4px solid #007FFF;      /* Left accent border */
    background-color: rgba(0, 127, 255, 0.05);  /* Light blue tint */
    color: #007FFF !important;           /* Congo Blue text */
}

/* Combined with white background */
.active-nav-link.bg-white {
    background: white;                   /* White card background */
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
```

## Responsive Breakpoints

```
Mobile (< 1024px)
├── Sidebar: Hidden (transform: -translate-x-full)
├── Navbar: Has hamburger ☰
├── Toggle: Sidebar slide-in from left
├── Overlay: Semi-transparent backdrop
└── Animation: transition-transform duration-300

Tablet (1024px - 1280px)
├── Sidebar: lg:static (becomes part of layout)
├── Sidebar: lg:translate-x-0 (visible)
├── Width: Still w-64
└── No hamburger needed

Desktop (> 1280px)
├── Sidebar: Always visible
├── Full layout: Sidebar + Content
├── Navbar: Full width
└── Content: Responsive to width
```

## Navigation Item Structure (Blade)

```blade
<a href="{{ route('route.name') }}"
   class="flex items-center px-4 py-3 text-gray-600 rounded-xl 
           hover:bg-white hover:text-congo-blue transition-all group
           {{ request()->routeIs('route.*') ? 'active-nav-link text-congo-blue bg-white shadow-sm' : '' }}">
    
    <i class="fas fa-icon w-5 h-5 mr-3 
              {{ request()->routeIs('route.*') ? 'text-congo-blue' : 'text-gray-400 group-hover:text-congo-blue' }}"></i>
    
    <span class="font-semibold text-sm">Label</span>
</a>
```

## Key CSS Classes

| Class | Purpose |
|-------|---------|
| `flex` | Flexbox layout |
| `fixed` | Fixed positioning (sidebar) |
| `sticky` | Sticky positioning (navbar) |
| `w-64` | Width 256px (sidebar) |
| `h-20` | Height 80px (navbar, logo) |
| `h-screen` | Full viewport height |
| `flex-1` | Fill remaining space |
| `overflow-y-auto` | Vertical scrolling |
| `overflow-hidden` | Hide overflow |
| `z-50` | High z-index (sidebar) |
| `z-40` | Navbar z-index |
| `rounded-xl` | Border radius 12px |
| `rounded-2xl` | Border radius 16px |
| `shadow-sm` | Small shadow |
| `shadow-soft` | Custom soft shadow |
| `transition-all` | Smooth animation |
| `duration-300` | 300ms animation |
| `group-hover:` | Group hover state |
| `hover:bg-white` | Hover background |
| `hover:text-congo-blue` | Hover text color |

## Typography Sizes

| Element | Size | Weight | Font |
|---------|------|--------|------|
| Section Header | `text-[10px]` | bold | Inter |
| Nav Item | `text-sm` | semibold | Inter |
| Card Title | `text-lg` | bold | Poppins |
| Page Title | `text-2xl` | black | Poppins |
| Hero Title | `text-4xl` | black | Poppins |
| Body Text | `text-base` | regular | Inter |

## Spacing Reference

```
Sidebar
├── Logo height: h-20 (80px)
├── Logo padding: px-8
├── Nav section: px-4 py-8
├── Items gap: space-y-1 (4px)
├── Item height: py-3 (~48px)
├── Bottom button: p-6

Navbar
├── Height: h-20 (80px)
├── Padding: px-4 sm:px-6 lg:px-8

Content
├── Max width: max-w-7xl (1280px)
├── Padding: px-4 sm:px-6 lg:px-8 py-6
├── Grid gap: gap-6 (24px)
└── Card padding: p-6 or p-8
```

## Interactive States

```
Link (Default)
├── Text: Gray 600
├── Icon: Gray 400
├── Background: Transparent
└── Border: None

Link (Hover)
├── Text: Congo Blue (#007FFF)
├── Icon: Congo Blue (#007FFF)
├── Background: White
├── Border: None
└── Transition: 300ms smooth

Link (Active)
├── Text: Congo Blue (#007FFF)
├── Icon: Congo Blue (#007FFF)
├── Background: White (with blue tint)
├── Border-left: 4px Congo Blue
└── Shadow: Subtle (shadow-sm)
```

## Mobile Navigation

```
MOBILE LAYOUT

┌─────────────┐
│ ☰ [Search]  │ (Navbar)
│ [🔔] [👤]   │
├─────────────┤
│             │
│   Main      │ (Content - Full Width)
│   Content   │
│             │
└─────────────┘

When Sidebar Opened:
┌─────────────┐
│ ☰ [Search]  │ (Navbar - On Top)
│ [🔔] [👤]   │
├──────────────────────────┐
│         │                │
│ Sidebar │  Main Content  │
│ (Overlay) │ (Dimmed 50%)  │
│         │                │
├──────────────────────────┤
│ (Still Scrollable)       │
└──────────────────────────┘
```

## File Locations

```
resources/views/
├── layouts/
│   └── app.blade.php ............. Master layout (flex structure)
├── components/
│   ├── sidebar.blade.php ......... Sidebar navigation
│   └── navbar.blade.php .......... Top navbar
└── user/
    └── dashboard.blade.php ....... Main dashboard (content)
```

## Quick Navigation

**To navigate dashboard sections:**
1. User clicks sidebar item
2. Route processes (e.g., `user.services.index`)
3. Page content loads in main area
4. Sidebar stays fixed on left
5. Active state highlights current page

**To toggle mobile sidebar:**
1. User clicks hamburger ☰ in navbar
2. `sidebarOpen` Alpine.js state toggles
3. Sidebar slides in from left
4. Overlay dims background
5. Click overlay or another nav item to close

---

## Summary

**The Perfect Dashboard Navigation:**
- ✅ Professional appearance
- ✅ Fixed sidebar (w-64)
- ✅ Unified navigation source
- ✅ MOSALA+ colors applied
- ✅ Responsive design
- ✅ Smooth interactions
- ✅ Clear active states
- ✅ Mobile-friendly

**Status**: 🟢 Production Ready
