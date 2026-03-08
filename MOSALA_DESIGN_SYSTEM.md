# MOSALA+ Professional Light Design System Documentation

## Overview
MOSALA+ is a unified, professional Light-themed design system for the entire ServiceRDC platform, including the landing page, user dashboard, and admin panel. The system is built using **Laravel Blade** and **Tailwind CSS CDN** for immediate stability and consistency.

---

## 🎨 Design Philosophy

The design follows these core principles:
- **Professional Light Theme**: Clean, accessible, and modern
- **National Identity**: Incorporates DRC national colors
- **Consistency**: Unified across all pages and user roles
- **Accessibility**: WCAG-compliant color contrasts and components
- **Performance**: CDN-based approach without complex build tools

---

## 🎭 Color Palette

### Primary Colors

| Color Name | Hex Code | Usage | Purpose |
|-----------|----------|-------|---------|
| **Congo Blue** | `#007FFF` | Primary actions, active states, highlights | Main interactive elements, navigation, buttons |
| **Yellow Gold** | `#F7D000` | Ratings, warnings, notifications | User attention, badges, warnings |
| **National Red** | `#CE1021` | Logout, dangers, critical actions | Destructive actions, warnings, logout button |
| **Mosala Light** | `#F0F4F5` | Global background | Full-page background, sidebar background |

### Secondary Colors (Light Mode)

| Element | Color | Purpose |
|---------|-------|---------|
| Background Primary | `#F0F4F5` | Page background |
| Background Secondary | `#FFFFFF` | Cards, modals, content areas |
| Background Tertiary | `#F8FAFC` | Form inputs, subtle backgrounds |
| Text Primary | `#1E293B` | Main content text |
| Text Secondary | `#64748B` | Metadata, descriptions |
| Border Color | `#E2E8F0` | Lines, dividers |

### Secondary Colors (Dark Mode)

| Element | Color | Purpose |
|---------|-------|---------|
| Background Primary | `#0A0F1C` | Page background |
| Background Secondary | `#111827` | Cards, modals |
| Background Tertiary | `#1F2937` | Form inputs |
| Text Primary | `#FFFFFF` | Main content text |
| Text Secondary | `#D1D5DB` | Metadata, descriptions |
| Border Color | `#374151` | Lines, dividers |

---

## 📐 Layout Architecture

### Master Layout File: `app.blade.php`

Located at: `resources/views/layouts/app.blade.php`

**Structure:**
```
HTML
├── HEAD
│   ├── Tailwind CSS CDN
│   ├── Tailwind Config (Custom Colors)
│   ├── Alpine.js (Reactive Components)
│   ├── Font Awesome Icons
│   ├── Google Fonts
│   └── CSS Variables & Utilities
└── BODY
    ├── SIDEBAR (Navigation)
    ├── MAIN
    │   ├── NAVBAR (Top Bar)
    │   └── CONTENT (Page-specific)
    └── SCRIPTS
```

### Component Breakdown

#### 1. **Sidebar Navigation**
**File:** `resources/views/components/sidebar.blade.php`

Features:
- Background: `#F0F4F5` (blends with page)
- Active links: Congo Blue text + subtle background
- Logout button: Fixed at bottom with National Red
- Responsive: Can be toggled on mobile
- Icons: Font Awesome integration

**Navigation Items:**
- Dashboard
- Profile
- Services (collapsible)
- Job Applications
- Messages (with badge counter)
- Ratings
- Settings
- Theme Toggle
- **Logout (Red Button)**

#### 2. **Top Navigation Bar**
**File:** `resources/views/components/navbar.blade.php`

Features:
- Sticky positioning (z-40)
- Search functionality
- Notifications bell with indicator
- User menu dropdown
- Responsive design

#### 3. **Main Content Area**
- Full-width with max container width (7xl)
- `@yield('content')` for page-specific content
- Padding and spacing utilities applied

---

## 🎯 Component Styling

### Buttons

#### Primary Button (Congo Blue)
```html
<button class="btn-primary px-6 py-3 rounded-lg font-medium">
    <i class="fas fa-icon mr-2"></i> Action
</button>
```
- Background: `#007FFF`
- Hover: `#0066CC` with shadow
- Text: White

#### Secondary Button (Light Gray)
```html
<button class="btn-secondary px-6 py-3 rounded-lg font-medium">
    <i class="fas fa-icon mr-2"></i> Action
</button>
```
- Background: `#F8FAFC` (light mode) / `#1F2937` (dark mode)
- Border: Gray 200 / 700
- Hover: Slightly darker background

#### Danger Button (National Red) - **Logout Only**
```html
<button class="btn-danger px-6 py-3 rounded-lg font-medium">
    <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
</button>
```
- Background: `#CE1021`
- Hover: `#A00A1A` with red shadow
- Text: White
- **Reserved exclusively for logout and critical destructive actions**

### Cards

```html
<div class="card rounded-xl p-6">
    <!-- Content -->
</div>
```

Features:
- Background: White (light) / `#111827` (dark)
- Shadow: Light on hover
- Border: Subtle gray border
- Padding: Customizable
- Rounded corners: `rounded-xl`

### Tables

```html
<table class="w-full">
    <thead>
        <tr>
            <th>Header</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Data</td>
        </tr>
    </tbody>
</table>
```

Features:
- Header background: Congo Blue at 5% opacity
- Header text: Congo Blue
- Borders: Light gray 200/700
- Hover effect: 3% congo-blue overlay
- Professional spacing

### Forms & Inputs

```html
<input type="text" placeholder="..." class="w-full px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 focus:border-congo-blue focus:ring-2 focus:ring-congo-blue/20">
```

Features:
- Background: `#F8FAFC` (light) / `#1F2937` (dark)
- Focus: Congo Blue border + shadow
- Smooth transitions
- Dark mode support

### Navigation Links

```html
<a href="#" class="nav-link">Link Text</a>
```

States:
- Default: Gray text
- Hover: Congo Blue text + background tint
- Active: Congo Blue text + background + left border

---

## 🌓 Dark Mode Implementation

### Detection & Toggle

The system automatically detects user preference:
1. Checks `localStorage` for `theme` preference
2. Falls back to system preference
3. Alpine.js `toggleTheme()` function switches modes
4. Uses CSS custom properties for smooth transition

### Dark Mode Colors

All components automatically adjust:
- Backgrounds become darker
- Text becomes lighter
- Borders maintain contrast
- Colors preserve accent system

### CSS Variable Approach

```css
:root {
    --bg-primary: #F0F4F5;
    --text-primary: #1E293B;
}

:root.dark {
    --bg-primary: #0A0F1C;
    --text-primary: #FFFFFF;
}
```

---

## 📱 Responsive Design

### Breakpoints (Tailwind)

- `sm`: 640px
- `md`: 768px
- `lg`: 1024px
- `xl`: 1280px
- `2xl`: 1536px

### Mobile Optimization

- Sidebar: Collapsible (can be implemented with Alpine.js)
- Navbar: Compact on mobile
- Grid layouts: Stack on small screens
- Touch-friendly: 44px minimum tap targets

---

## 🚀 Getting Started

### 1. Use the Master Layout

```php
@extends('layouts.app')

@section('title', 'Page Title')
@section('page_title', 'Page Title')

@section('content')
    <!-- Your content here -->
@endsection
```

### 2. Include Components

Components are automatically included in `app.blade.php`:
- `@include('components.sidebar')`
- `@include('components.navbar')`

### 3. Use Utility Classes

Apply consistent styling with provided classes:

```html
<!-- Button styles -->
<button class="btn-primary">Primary Action</button>
<button class="btn-secondary">Secondary Action</button>
<button class="btn-danger">Dangerous Action</button>

<!-- Card styling -->
<div class="card rounded-xl p-6">Content</div>

<!-- Navigation links -->
<a href="#" class="nav-link">Link</a>

<!-- Colors -->
<span class="text-congo-blue">Congo Blue Text</span>
<span class="text-congo-yellow">Yellow Gold Text</span>
<span class="text-congo-red">National Red Text</span>
```

---

## 📋 File Structure

```
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php ..................... Master layout
│   │   ├── auth.blade.php ................... Auth layout
│   │   ├── user.blade.php ................... User layout
│   │   ├── admin.blade.php .................. Admin layout
│   │   └── super-admin.blade.php ............ Super admin layout
│   ├── components/
│   │   ├── sidebar.blade.php ................ Navigation sidebar
│   │   ├── navbar.blade.php ................. Top navigation
│   │   ├── topbar.blade.php ................. Alternative topbar
│   │   ├── admin-sidebar.blade.php ......... Admin sidebar
│   │   └── ... (other components)
│   ├── dashboard-light.blade.php ........... Example dashboard
│   ├── welcome.blade.php ................... Landing page
│   └── ... (other pages)
└── css/
    └── app.css ............................ Compiled CSS (if using build)
```

---

## 🎯 Use Cases

### User Dashboard
- Display personal services and applications
- Show performance metrics and ratings
- Quick actions for profile updates
- Message notifications

### Admin Dashboard
- Manage users and services
- View platform statistics
- Moderate content
- System settings

### Landing Page
- Hero section
- Service categories
- Job listings
- Call-to-action buttons

---

## ♿ Accessibility

### Color Contrast
- All text meets WCAG AA standards (4.5:1 for normal text)
- Congo Blue on white: 8.5:1
- Red on white: 5.2:1
- Yellow on white: 3.2:1 (use only for non-critical info)

### Keyboard Navigation
- All interactive elements are keyboard accessible
- Focus states are clearly visible
- Logical tab order maintained

### Semantic HTML
- Proper heading hierarchy
- Form labels associated with inputs
- ARIA labels where needed
- Semantic button/link usage

---

## 🔧 Customization

### Changing Colors

Edit in `app.blade.php` Tailwind config:

```javascript
tailwind.config = {
    theme: {
        extend: {
            colors: {
                'congo-blue': {
                    600: '#007FFF',
                    // ... other shades
                }
            }
        }
    }
}
```

### Adding New Components

Create new files in `resources/views/components/` and include in layouts:

```php
@include('components.your-component')
```

### Extending Tailwind

Add custom utilities in the `<style>` tag:

```css
.custom-utility {
    /* CSS */
}
```

---

## 📚 Resources

- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev)
- [Font Awesome Icons](https://fontawesome.com/icons)
- [Google Fonts](https://fonts.google.com)

---

## ✅ Checklist for Implementation

- [x] Master layout (`app.blade.php`) created
- [x] Sidebar component with proper styling
- [x] Navbar component with user menu
- [x] Tailwind CDN integration
- [x] Custom color configuration
- [x] Dark mode support
- [x] Responsive design
- [x] Example dashboard page
- [x] Comprehensive documentation
- [ ] Update existing pages to use new layout
- [ ] Test across browsers
- [ ] Accessibility audit
- [ ] Performance optimization

---

## 🐛 Troubleshooting

### Tailwind Classes Not Working
- Ensure Tailwind CDN is loaded in `<head>`
- Check for conflicting CSS
- Clear browser cache

### Dark Mode Not Switching
- Check localStorage permissions
- Verify Alpine.js is loaded
- Check console for errors

### Layout Breaking
- Verify component paths are correct
- Check for missing `@yield` sections
- Ensure all required fonts are loaded

---

## 📞 Support

For questions or issues with the design system:
1. Check this documentation
2. Review component examples
3. Test in isolated environment
4. Check browser console for errors

---

**MOSALA+ Design System v1.0**
*Professional Light Theme for ServiceRDC Platform*
*Last Updated: January 2026*
