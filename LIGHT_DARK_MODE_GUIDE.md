# Mosala+ Light/Dark Mode Architecture Documentation

## Overview
Complete system-wide Light/Dark mode implementation for the Mosala+ platform using Laravel, Tailwind CSS, and Alpine.js. This architecture provides a seamless, theme-aware experience with persistent user preferences.

---

## 1. Core Configuration

### 1.1 Tailwind CSS Configuration
**File:** `tailwind.config.js`

```javascript
darkMode: 'class',  // Enables class-based dark mode
```

**Custom Color Variables:**
- **Light Mode:**
  - Background: `#F8FAFC` (slate-50)
  - Sidebar: `#FFFFFF`
  - Border: `#E2E8F0` (slate-200)
  - Text: `#1E293B` (slate-800)
  - Subtext: `#64748B` (slate-500)

- **Dark Mode:**
  - Background: `#0A0F1C` (Congo Dark)
  - Sidebar: `#111827` (gray-900)
  - Border: `#374151` (gray-700)
  - Text: `#FFFFFF`
  - Subtext: `#9CA3AF` (gray-400)

- **Brand Colors (Constant in Both Modes):**
  - Congo Blue: `#007FFF`
  - Gold: `#F7D000`

---

## 2. Theme Persistence & FOUC Prevention

### 2.1 Blocking JavaScript in Head
**Location:** `resources/views/layouts/*.blade.php` (user.blade.php, admin.blade.php)

```html
<script>
(function() {
    const storageKey = 'mosala_theme';
    const htmlElement = document.documentElement;
    
    // Check localStorage first
    let theme = localStorage.getItem(storageKey);
    
    if (!theme) {
        // Fall back to system preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            theme = 'dark';
        } else {
            theme = 'light';  // Default
        }
        localStorage.setItem(storageKey, theme);
    }
    
    // Apply BEFORE DOM renders
    if (theme === 'dark') {
        htmlElement.classList.add('dark');
    } else {
        htmlElement.classList.remove('dark');
    }
})();
</script>
```

**Key Features:**
- Executes synchronously in `<head>` before DOM renders
- Prevents Flash of Unstyled Content (FOUC)
- Checks localStorage key: `mosala_theme`
- Falls back to system `prefers-color-scheme` preference
- Defaults to Light Mode if no preference found

---

## 3. Theme Toggle Component

### 3.1 Alpine.js Theme Toggle Button
**File:** `resources/views/components/theme-toggle.blade.php`

**Features:**
- Sun icon (☀️) for Light Mode
- Moon icon (🌙) for Dark Mode
- Premium hover effects with scale animation
- Tooltip showing current mode
- Persists to localStorage via Alpine.js
- Smooth 300ms transitions

**Usage:**
```blade
@include('components.theme-toggle')
```

**HTML Structure:**
```html
<button @click="toggleTheme()">
    <i class="fas fa-sun"></i>  <!-- Light Mode -->
    <i class="fas fa-moon"></i> <!-- Dark Mode -->
</button>
```

**Alpine.js Logic:**
```javascript
function themeToggle() {
    return {
        isDark: false,
        toggleTheme() {
            const htmlElement = document.documentElement;
            const newTheme = this.isDark ? 'light' : 'dark';
            
            if (newTheme === 'dark') {
                htmlElement.classList.add('dark');
            } else {
                htmlElement.classList.remove('dark');
            }
            
            this.isDark = !this.isDark;
            localStorage.setItem('mosala_theme', newTheme);
        }
    }
}
```

---

## 4. Component Updates

### 4.1 Sidebar Component
**File:** `resources/views/components/admin-sidebar.blade.php`

**Theme-Aware Classes:**
- Background: `bg-white dark:bg-dark-sidebar`
- Text: `text-light-text dark:text-dark-text`
- Borders: `border-light-border dark:border-dark-border`
- Hover States: `hover:bg-light-border dark:hover:bg-white/10`

**Key Elements:**
- Logo with Congo Blue gradient
- User info section with role badges
- Navigation links with active states
- System section (Super Admin only)
- Logout button with red theme

### 4.2 Navigation Component
**File:** `resources/views/components/navigation.blade.php`

**Theme-Aware Features:**
- Integrated theme toggle button
- Color-coded navigation links
- User dropdown with theme support
- Mobile navigation with transitions
- Role-based badge styling

### 4.3 Layout Files
**Files:**
- `resources/views/layouts/user.blade.php`
- `resources/views/layouts/admin.blade.php`

**Key Updates:**
- Added FOUC prevention script
- Global transition-colors on body
- Theme-aware flash message styling
- Breadcrumb color updates
- Footer with dark mode support

---

## 5. Global Theme Styles

### 5.1 Theme CSS File
**File:** `resources/css/theme.css`

**Includes:**
- CSS Variables for light/dark modes
- Table styling with zebra striping
- Form input styling
- Card styling with glassmorphism for dark mode
- Button variants
- Modal styling
- Alert styling
- Badge styling
- Dropdown menus
- Pagination
- Toggle switches
- Skeleton loading animations
- Glassmorphism effects
- Custom scrollbar styling

**Import Statement:**
```css
/* In app.css */
@import 'theme.css';
```

---

## 6. Tailwind Utility Classes

### 6.1 Color Tokens
```html
<!-- Light/Dark Aware -->
<div class="bg-white dark:bg-dark-sidebar text-light-text dark:text-dark-text">
    
<!-- Brand Colors (Constant) -->
<button class="bg-congo-blue text-white">Action</button>
<span class="text-gold">Premium</span>

<!-- Component Specific -->
<table class="border-light-border dark:border-dark-border">
    <tbody>
        <tr class="even:bg-slate-50 dark:even:bg-gray-800/30"></tr>
    </tbody>
</table>
```

### 6.2 Transition Classes
```html
<!-- All theme-aware elements use -->
class="transition-colors duration-300"
```

---

## 7. Component Styling Examples

### 7.1 Card Styling
```html
<div class="card">
    <div class="card-header">
        <h2>Title</h2>
    </div>
    <div class="card-body">Content</div>
    <div class="card-footer">Footer</div>
</div>

<!-- CSS Styling -->
.card {
    background-color: var(--bg-secondary);
    border-color: var(--border-color);
    color: var(--text-primary);
    transition: all 300ms;
}
```

### 7.2 Table Styling
```html
<div class="table-wrapper">
    <table>
        <thead>
            <tr>...</tr>
        </thead>
        <tbody>
            <!-- Auto zebra striping via CSS -->
            <tr>...</tr>
        </tbody>
    </table>
</div>
```

### 7.3 Form Styling
```html
<div class="form-group">
    <label>Username</label>
    <input type="text" placeholder="Enter username">
</div>

<!-- Auto theme support -->
```

### 7.4 Alert Styling
```html
<!-- Flash messages auto-theme -->
<div class="alert alert-success">
    <i class="alert-icon fas fa-check-circle"></i>
    <div class="alert-content">Success message</div>
</div>
```

---

## 8. JavaScript Implementation Details

### 8.1 Theme Detection Order
1. **localStorage** - User's saved preference (`mosala_theme`)
2. **System Preference** - OS dark mode setting (`prefers-color-scheme`)
3. **Default** - Light Mode

### 8.2 DOM Manipulation
```javascript
// Add dark class to HTML element
document.documentElement.classList.add('dark');

// Remove dark class (back to light)
document.documentElement.classList.remove('dark');

// Check current theme
const isDark = document.documentElement.classList.contains('dark');
```

---

## 9. CSS Variable Architecture

### 9.1 Light Mode Variables
```css
:root:not(.dark) {
    --bg-primary: #F8FAFC;
    --bg-secondary: #FFFFFF;
    --bg-tertiary: #F1F5F9;
    --border-color: #E2E8F0;
    --text-primary: #1E293B;
    --text-secondary: #64748B;
    --text-tertiary: #94A3B8;
    --shadow-sm: 0 1px 2px 0 rgba(15, 23, 42, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.1);
}
```

### 9.2 Dark Mode Variables
```css
:root.dark {
    --bg-primary: #0A0F1C;
    --bg-secondary: #111827;
    --bg-tertiary: #1F2937;
    --border-color: #374151;
    --text-primary: #FFFFFF;
    --text-secondary: #D1D5DB;
    --text-tertiary: #9CA3AF;
    --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
```

---

## 10. Browser Support

- **Modern Browsers:** Full support for CSS variables and `prefers-color-scheme`
- **Fallback:** Light Mode as default for unsupported browsers
- **localStorage:** Supported in all modern browsers
- **Alpine.js 3.x:** Required for theme toggle functionality

---

## 11. Performance Considerations

### 11.1 Optimization
- Blocking script executes before DOM paint
- CSS Variables cached by browser
- No layout shift during theme switch
- GPU-accelerated transitions (transform preferred over color)
- Minimal JavaScript execution on toggle

### 11.2 Accessibility
- Respects system `prefers-color-scheme` setting
- High contrast ratios maintained in both themes
- Icon buttons have proper tooltips
- ARIA labels on theme toggle

---

## 12. Usage in Templates

### 12.1 Basic Dark Mode Support
```blade
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
    Content
</div>
```

### 12.2 Complex Components
```blade
<div class="bg-light-bg dark:bg-dark-bg transition-colors duration-300">
    <div class="border border-light-border dark:border-dark-border">
        <h1 class="text-light-text dark:text-dark-text">Title</h1>
    </div>
</div>
```

### 12.3 Conditional Rendering (if needed)
```blade
@if(/* Dark Mode Check */)
    <!-- Dark Mode Content -->
@else
    <!-- Light Mode Content -->
@endif
```

---

## 13. File Locations Summary

| File | Purpose |
|------|---------|
| `tailwind.config.js` | Core theme configuration |
| `resources/css/theme.css` | Theme utility styles |
| `resources/css/app.css` | Main CSS with imports |
| `resources/views/layouts/user.blade.php` | User layout with theme support |
| `resources/views/layouts/admin.blade.php` | Admin layout with theme support |
| `resources/views/components/theme-toggle.blade.php` | Theme toggle button |
| `resources/views/components/navigation.blade.php` | Navigation with theme support |
| `resources/views/components/admin-sidebar.blade.php` | Sidebar with theme support |

---

## 14. Future Enhancements

- [ ] Auto theme switch based on time of day
- [ ] System tray integration for theme sync
- [ ] Per-component theme overrides
- [ ] Theme customization panel for users
- [ ] Animation preferences (prefers-reduced-motion)
- [ ] High contrast mode option
- [ ] Theme preview before applying

---

## 15. Troubleshooting

### 15.1 FOUC Still Occurring
- Ensure blocking script is in `<head>` before other scripts
- Check that localStorage key matches: `mosala_theme`
- Verify Tailwind `darkMode: 'class'` is set

### 15.2 Theme Not Persisting
- Check browser localStorage is enabled
- Verify localStorage key: `mosala_theme`
- Check browser console for JavaScript errors

### 15.3 Transitions Not Smooth
- Ensure `transition-colors duration-300` is applied
- Check for conflicting CSS animations
- Verify GPU acceleration is enabled (use `transform` when possible)

---

## 16. Testing Checklist

- [ ] Light mode renders correctly
- [ ] Dark mode renders correctly
- [ ] Theme toggle button works
- [ ] Theme persists after refresh
- [ ] System preference is respected
- [ ] All components adapt to theme
- [ ] Transitions are smooth
- [ ] No FOUC on page load
- [ ] Brand colors remain constant
- [ ] Accessible contrast in both modes
- [ ] Mobile responsiveness maintained
- [ ] Tables zebra striping works
- [ ] Form inputs styled properly
- [ ] Modals and dropdowns themed
- [ ] Scrollbars match theme

---

**Version:** 1.0  
**Created:** January 11, 2026  
**Platform:** Mosala+ ServiceRDC  
**Framework:** Laravel 11 + Tailwind CSS 4 + Alpine.js 3
