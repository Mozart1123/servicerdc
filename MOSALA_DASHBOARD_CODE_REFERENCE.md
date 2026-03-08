# MOSALA+ Dashboard - Complete Code Reference

## 📄 File Structure Overview

This document provides the complete, production-ready code for the MOSALA+ dashboard reconstruction.

### Key Files
1. `resources/views/layouts/app.blade.php` - Master template
2. `resources/views/components/sidebar.blade.php` - Left navigation
3. `resources/views/components/navbar.blade.php` - Top navbar
4. `resources/views/user/dashboard.blade.php` - Main dashboard
5. `app/Http/Controllers/User/DashboardController.php` - Backend controller

---

## 1️⃣ Master Layout: `app.blade.php`

**Key Features:**
- Tailwind CSS via CDN with custom configuration
- Alpine.js 3.x for interactivity
- Font Awesome 6.4.0 for icons
- Google Fonts (Poppins, Inter)
- Sidebar + Navbar + Content layout
- Global error/success alert display

**Configuration:**
```javascript
tailwind.config = {
    theme: {
        extend: {
            colors: {
                'congo-blue': '#007FFF',
                'congo-yellow': '#F7D000',
                'congo-red': '#CE1021',
                'mosala-light': '#F0F4F5',
            }
        }
    }
}
```

**Body Structure:**
```
<body>
  <div class="flex h-screen"> <!-- Full-height flex container -->
    @include('components.sidebar') <!-- Left fixed/drawer sidebar -->
    <div class="flex-1"> <!-- Main area (grows to fill space) -->
      @include('components.navbar') <!-- Top sticky navbar -->
      <main> <!-- Scrollable content -->
        @yield('content')
      </main>
    </div>
  </div>
</body>
```

---

## 2️⃣ Sidebar: `sidebar.blade.php`

**Size & Layout:**
- Width: `w-64` (256px)
- Position: `lg:static` (permanent) / `fixed` (mobile drawer)
- Transform: `lg:translate-x-0` (permanent) / `:class` toggle
- Background: `#F0F4F5`
- Border: Right border, gray-200

**Structure:**
```
├── Logo Section (h-20)
│   └── MOSALA+ with icon
├── Navigation Menu (flex-1, scrollable)
│   ├── Main Menu
│   │   ├── Tableau de bord
│   │   ├── [Admin: Manage...]
│   │   ├── [Artisan: My Services]
│   │   └── Mon Profil
│   └── Support
│       └── Retour au site
└── Logout Section (mt-auto)
    └── Red "Déconnexion" button
```

**Active State:**
- Border-left: 4px solid Congo Blue
- Background: Congo Blue 5% opacity
- Text: Congo Blue color

---

## 3️⃣ Navbar: `navbar.blade.php`

**Layout (Flex Container):**
```
[Menu Toggle] [Logo Mobile] [Breadcrumb] | [Search Bar] | [Bell] [User Avatar]
```

**Heights & Positioning:**
- Height: `h-20` (80px)
- Position: `sticky top-0 z-40`
- Background: `bg-white`
- Border: Bottom border-gray-200

**Components:**
1. **Left**: Menu toggle (mobile), logo (mobile), breadcrumb (desktop)
2. **Center**: Search input with icon
3. **Right**: Notification bell, user profile

**Search Bar:**
- Placeholder: "Rechercher un service ou un artisan..."
- Background: #F0F4F5 default, white on focus
- Icon: Left-aligned, gray-400, changes to Congo Blue on focus
- Focus ring: Congo Blue

---

## 4️⃣ Dashboard: `dashboard.blade.php`

### Hero Section
```blade
<div class="bg-gradient-to-r from-congo-blue to-blue-600 rounded-2xl p-8 text-white">
    <h1 class="text-4xl font-black">Bonjour, {{ Auth::user()->name }}! 👋</h1>
    <p class="text-blue-100">Bienvenue sur votre tableau de bord MOSALA+</p>
</div>
```

### Statistics Grid
```blade
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- 4 cards: Candidatures, Services, Travaux, Notifications -->
    <!-- Each: bg-white, rounded-2xl, shadow-sm, p-6 -->
    <!-- Left: h1 stat, p subtitle -->
    <!-- Right: w-14 h-14 icon container -->
</div>
```

### Tabs Navigation
```blade
<div class="bg-white rounded-t-2xl border-b sticky top-20">
    <div class="flex px-6 py-0">
        <!-- 4 tab buttons -->
        <!-- Active: border-congo-blue text-congo-blue -->
    </div>
</div>
```

### Tab Contents
- **Overview**: Jobs list + sidebar actions
- **Services**: Services browsing
- **Emplois**: All job offers
- **Candidatures**: User applications

---

## 5️⃣ Controller: `DashboardController.php`

### Data Passed to View

```php
public function index(): View
{
    $user = Auth::user();

    $stats = [
        'applied_jobs_count' => $user->jobApplications()->count(),
        'total_jobs' => JobOffer::active()->count(),
        'total_services' => Service::active()->verified()->count(),
        'active_missions' => $user->missionsAsArtisan()->inProgress()->count() + 
                            $user->missionsAsClient()->inProgress()->count(),
        'unread_notifications' => $user->notifications()->unread()->count(),
    ];

    $recentJobs = JobOffer::active()->notExpired()->latest()->take(6)->get();
    $allJobs = JobOffer::active()->notExpired()->latest()->get();
    $recentServices = Service::active()->verified()->latest()->take(6)->get();
    $categories = Category::all();
    $myApplications = $user->jobApplications()->with('jobOffer')->latest()->get();
    $notifications = $user->notifications()->latest()->take(5)->get();

    return view('user.dashboard', compact(
        'stats',
        'recentJobs',
        'allJobs',
        'recentServices',
        'categories',
        'myApplications',
        'notifications'
    ));
}
```

### Data Requirements

```php
// $stats: Array with 5 keys
$stats = [
    'applied_jobs_count' => int,
    'total_jobs' => int,
    'total_services' => int,
    'active_missions' => int,
    'unread_notifications' => int,
];

// $recentJobs: Collection of JobOffer
JobOffer {
    ->title: string,
    ->user->name: string,
    ->location: string,
    ->contract_type: string,
    ->salary_min: int
}

// Other data types similar...
```

---

## 🎨 Tailwind Classes Reference

### Colors
```
bg-congo-blue       → Background Congo Blue
text-congo-blue     → Text Congo Blue
border-congo-blue   → Border Congo Blue
hover:bg-congo-blue → On hover, background Congo Blue
```

### Spacing (in px)
```
p-4  → 16px padding
p-6  → 24px padding
p-8  → 32px padding
gap-4 → 16px gap
gap-6 → 24px gap
```

### Border Radius
```
rounded-xl  → 12px
rounded-2xl → 16px
rounded-lg  → 8px
rounded-full → 50%
```

### Shadows
```
shadow-sm   → Small shadow (0 1px 2px 0 rgba(0,0,0,0.05))
shadow-md   → Medium shadow
shadow-lg   → Large shadow
```

### Responsive Prefixes
```
sm:  → 640px (small screens/tablets)
md:  → 768px (medium screens)
lg:  → 1024px (large screens/desktop)
```

---

## 🔄 Alpine.js Data & Methods

### Dashboard App Function
```javascript
function dashboardApp() {
    return {
        activeTab: 'vue-ensemble',  // Current active tab
        init() {
            // Restore tab from URL query parameter
            const urlTab = new URLSearchParams(window.location.search).get('tab');
            if (urlTab) this.activeTab = urlTab;
        }
    };
}
```

### Usage in HTML
```blade
<div x-data="dashboardApp()">
    <!-- Use activeTab variable -->
    <button @click="activeTab = 'services'">Click to switch</button>
    
    <!-- Show/hide based on activeTab -->
    <div x-show="activeTab === 'services'" x-transition>
        Services content here
    </div>
</div>
```

---

## 📱 Responsive Behavior

### Desktop (lg: 1024px+)
```
[Sidebar (256px fixed)] [Main Content (flex)]
├── Navbar (full width)
├── Hero + Stats (4 columns)
├── Tabs (sticky)
└── Content (3 columns: 2 jobs + 1 sidebar)
```

### Tablet (md: 768px - 1023px)
```
[Drawer Sidebar (toggle)] [Main Content]
├── Navbar (full width)
├── Stats (2 columns)
├── Tabs
└── Content (1 column)
```

### Mobile (< 768px)
```
[Drawer Sidebar] [Main Content]
├── Navbar with toggle
├── Stats (1 column)
├── Tabs (horizontal scroll)
└── Content (single column)
```

---

## 🎯 Common Customizations

### Change Primary Color
Edit in `app.blade.php`:
```javascript
'congo-blue': '#007FFF'  // Change this hex value
```

### Add New Tab
1. Add button in tabs section
2. Add `<div x-show="activeTab === 'new-tab'">` block
3. Pass data from controller

### Modify Grid Columns
```blade
<!-- From 4 columns to 3 -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
```

### Increase Card Padding
```blade
<!-- From p-6 to p-8 -->
<div class="...p-8...">
```

---

## ✅ Verification Checklist

After implementation, verify:

- [ ] Dashboard loads at `/user/dashboard`
- [ ] All statistics cards display correctly
- [ ] Sidebar visible on desktop, drawer on mobile
- [ ] Navbar search bar focused on click
- [ ] Tab switching works smoothly
- [ ] Colors match national palette
- [ ] No console errors
- [ ] Responsive on mobile (< 640px)
- [ ] Responsive on tablet (640-1024px)
- [ ] Responsive on desktop (> 1024px)
- [ ] Logout button redirects correctly
- [ ] Profile avatar displays
- [ ] All icons render (Font Awesome)

---

## 🔗 Related Files

- Database Controller: `/app/Http/Controllers/User/DashboardController.php`
- Models: `/app/Models/JobOffer.php`, `/app/Models/Service.php`, etc.
- Migrations: `/database/migrations/*`
- Routes: `/routes/web.php` (user.dashboard route)

---

## 📞 Support & Updates

**Last Updated**: January 13, 2026

**Tech Stack**:
- Laravel 12.46.0
- Tailwind CSS 3+
- Alpine.js 3.x
- Font Awesome 6.4.0

For issues or updates, refer to the main MOSALA_DASHBOARD_REBUILD.md file.
