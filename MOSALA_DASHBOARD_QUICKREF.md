# MOSALA+ Dashboard - Quick Reference

## 🚀 Quick Start

### Start Development Server
```bash
cd c:\xampp\htdocs\rdc\rdc
php artisan serve --port=8000
```

Navigate to: `http://localhost:8000/user/dashboard`

---

## 🎨 Color System

```
congo-blue:   #007FFF  (Primary, Icons, Active states)
congo-yellow: #F7D000  (Stats icons, Badges)
congo-red:    #CE1021  (Delete, Logout)
mosala-light: #F0F4F5  (Global background)
```

### Usage in Blade
```blade
<div class="bg-congo-blue text-white">Primary action</div>
<div class="text-congo-yellow">Warning/Stats</div>
<button class="text-congo-red">Delete</button>
```

---

## 📊 Statistics Cards Component

**File**: `dashboard.blade.php` (lines 8-65)

**Data Required**:
```php
$stats = [
    'applied_jobs_count' => int,
    'total_jobs' => int,
    'total_services' => int,
    'active_missions' => int,
    'unread_notifications' => int,
];
```

**Template Structure**:
```blade
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Each card: bg-white, rounded-2xl, shadow-sm, p-6 -->
    <!-- Icon: w-14 h-14 flex items-center justify-center rounded-2xl -->
</div>
```

---

## 🔢 Responsive Grid System

| Breakpoint | Stats | Layout | Sidebar |
|------------|-------|--------|---------|
| `sm:` | 2 col | 1 col | Drawer |
| `md:` | 2 col | 2 col | Drawer |
| `lg:` | 4 col | 3 col | Fixed |

### Grid Classes
- `grid-cols-1` → 1 column
- `sm:grid-cols-2` → 2 columns on small screens
- `lg:grid-cols-4` → 4 columns on large screens
- `gap-6` → 24px spacing

---

## 📌 Sidebar Navigation

**File**: `/resources/views/components/sidebar.blade.php`

### Active Link Detection
```blade
{{ request()->routeIs('dashboard') ? 'active-nav-link' : '' }}
```

**CSS Class** (in app.blade.php):
```css
.active-nav-link {
    border-left: 4px solid #007FFF;
    background-color: rgba(0, 127, 255, 0.05);
    color: #007FFF !important;
}
```

---

## 🔍 Navbar Search Bar

**File**: `/resources/views/components/navbar.blade.php` (lines 26-36)

**Features**:
- Placeholder: "Rechercher un service ou un artisan..."
- Icon: `fas fa-search` (left)
- Background: #F0F4F5 default, white on focus
- Focus ring: Congo Blue

---

## 📑 Tabs Navigation

**Currently Active**:
```blade
<button @click="activeTab = 'vue-ensemble'"
    :class="activeTab === 'vue-ensemble' ? 'border-congo-blue text-congo-blue' : ''"
>
```

**Available Tabs**:
1. `vue-ensemble` - Overview
2. `services` - Services & Artisans
3. `emplois` - Job Hub
4. `candidatures` - My Applications

---

## 💼 Jobs List Card

**Component** (within Overview tab):
```blade
<div class="border border-gray-200 rounded-xl p-6 hover:border-congo-blue">
    <div class="w-12 h-12 rounded-xl bg-congo-blue text-white flex items-center justify-center">
        {{ substr($job->user->name ?? 'J', 0, 1) }}
    </div>
    <h3 class="text-lg font-bold">{{ $job->title }}</h3>
    <p class="text-2xl font-black text-congo-blue">{{ $job->salary_min }}$</p>
</div>
```

**Data Requirements**:
```php
$recentJobs = [
    {
        'title': string,
        'user.name': string,
        'location': string,
        'contract_type': string,
        'salary_min': int
    }
];
```

---

## 🎯 Alpine.js Data

**Initialization**:
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

**Usage in HTML**:
```blade
<div x-data="dashboardApp()">
    <button @click="activeTab = 'services'">Services</button>
    <div x-show="activeTab === 'services'" x-transition>Content</div>
</div>
```

---

## 🎨 Card Styling Standards

### Base Card
```blade
<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
    <!-- content -->
</div>
```

### Hoverable Card
```blade
<div class="...rounded-xl...hover:shadow-md hover:border-congo-blue transition-all">
```

### Hero/Gradient Card
```blade
<div class="bg-gradient-to-r from-congo-blue to-blue-600 rounded-2xl p-8 text-white">
```

---

## 🔄 Transitions

**Alpine.js Transitions**:
```blade
<div x-show="activeTab === 'services'" x-transition>
    <!-- Animates with fade + slide -->
</div>
```

**Tailwind Transitions**:
```blade
<button class="...transition-all duration-300...hover:...">
```

---

## 📋 Tailwind Classes Quick Reference

| Purpose | Classes |
|---------|---------|
| Button Primary | `px-6 py-3 bg-congo-blue text-white rounded-lg hover:bg-blue-700` |
| Button Secondary | `px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200` |
| Card | `bg-white rounded-2xl p-6 shadow-sm border border-gray-100` |
| Stats Icon | `w-14 h-14 rounded-2xl flex items-center justify-center` |
| Badge | `inline-block px-3 py-1 rounded-full text-sm font-semibold` |
| Grid | `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6` |

---

## 🛠️ Common Modifications

### Add New Tab
1. Add button in tabs section:
   ```blade
   <button @click="activeTab = 'new-tab'">Label</button>
   ```

2. Add content div:
   ```blade
   <div x-show="activeTab === 'new-tab'">Content</div>
   ```

3. Pass data from controller in compact()

### Change Colors
- Edit `/resources/views/layouts/app.blade.php` in `<script>tailwind.config`
- Update hex colors: `'congo-blue': '#NEWCOLOR'`

### Modify Spacing
- Global: Change values in `gap-6`, `p-6`, etc.
- Use Tailwind spacing scale: `p-4|p-6|p-8` (16px/24px/32px)

---

## 🚨 Common Issues

### Sidebar Not Showing on Mobile
→ Add `lg:` breakpoint classes properly

### Tabs Not Switching
→ Check Alpine.js is loaded: `<script defer src="...alpinejs..."></script>`

### Colors Not Appearing
→ Verify Tailwind config script in `<head>` of app.blade.php

### Icons Missing
→ Check Font Awesome CDN link in `<head>`

---

## 📞 Support

- **Dashboard File**: `/resources/views/user/dashboard.blade.php`
- **Layout File**: `/resources/views/layouts/app.blade.php`
- **Components**: `/resources/views/components/`
- **Controller**: `/app/Http/Controllers/User/DashboardController.php`

---

## ✅ Testing Checklist

- [ ] Dashboard loads without errors
- [ ] Sidebar visible on desktop, hidden/drawer on mobile
- [ ] Statistics cards show correct data
- [ ] Tabs switch content smoothly
- [ ] Navbar search bar functional
- [ ] Colors match design spec
- [ ] All responsive breakpoints working
- [ ] Hover effects present
- [ ] No console errors
- [ ] Navigation links working
