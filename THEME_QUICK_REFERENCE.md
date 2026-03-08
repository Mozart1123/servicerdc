# 🌓 Mosala+ Theme System - Quick Reference Card

## 📋 Theme Detection Flow

```
Page Load
    ↓
FOUC Prevention Script Runs
    ↓
Check localStorage['mosala_theme']
    ↓
    ├─ Found? Apply theme immediately → ✅ No Flash
    │
    └─ Not Found?
            ↓
        Check System Preference (prefers-color-scheme)
            ↓
            ├─ Dark? Save 'dark' → Apply dark class
            │
            └─ Light? Save 'light' → Use default (no class)
    
DOM Renders → Page visible with correct theme
```

---

## 🎨 Color Reference

### Light Theme
| Element | Color | Hex |
|---------|-------|-----|
| Background | Slate 50 | #F8FAFC |
| Sidebar | White | #FFFFFF |
| Border | Slate 200 | #E2E8F0 |
| Text | Slate 800 | #1E293B |
| Subtext | Slate 500 | #64748B |

### Dark Theme
| Element | Color | Hex |
|---------|-------|-----|
| Background | Congo Dark | #0A0F1C |
| Sidebar | Gray 900 | #111827 |
| Border | Gray 700 | #374151 |
| Text | White | #FFFFFF |
| Subtext | Gray 400 | #9CA3AF |

### Brand (Both Themes)
| Element | Color | Hex |
|---------|-------|-----|
| Primary | Congo Blue | #007FFF |
| Accent | Gold | #F7D000 |

---

## 🚀 Essential Classes

### Apply Theme
```html
<!-- Light/Dark Background -->
class="bg-white dark:bg-gray-800"

<!-- Light/Dark Text -->
class="text-gray-900 dark:text-white"

<!-- Light/Dark Border -->
class="border-light-border dark:border-dark-border"

<!-- Using Custom Variables -->
class="bg-light-bg dark:bg-dark-bg"

<!-- Smooth Transition -->
class="transition-colors duration-300"

<!-- Brand Colors (Always Same) -->
class="bg-congo-blue text-gold"
```

---

## 🎯 Component Classes

### Card
```html
<div class="card">
    <div class="card-header"><h2>Title</h2></div>
    <div class="card-body">Content</div>
    <div class="card-footer">Footer</div>
</div>
```

### Alert
```html
<div class="alert alert-success">
    <i class="alert-icon fas fa-check-circle"></i>
    <div class="alert-content">Message</div>
</div>

<!-- Variants: alert-success, alert-error, alert-warning, alert-info -->
```

### Table
```html
<div class="table-wrapper">
    <table>
        <thead>...</thead>
        <tbody><!-- Auto zebra striping --></tbody>
    </table>
</div>
```

### Button
```html
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-danger">Delete</button>
<button class="btn btn-ghost">Cancel</button>
```

### Form
```html
<div class="form-group">
    <label>Field Name</label>
    <input type="text" placeholder="Enter value">
</div>
```

### Badge
```html
<span class="badge badge-primary">Primary</span>
<span class="badge badge-success">Success</span>
<span class="badge badge-danger">Danger</span>
<span class="badge badge-warning">Warning</span>
```

### Modal
```html
<div class="modal">
    <div class="modal-content">
        <div class="modal-header"><h2>Title</h2></div>
        <div class="modal-body">Content</div>
        <div class="modal-footer">
            <button class="btn btn-secondary">Cancel</button>
            <button class="btn btn-primary">Confirm</button>
        </div>
    </div>
</div>
```

---

## 📍 Location Reference

### Configuration
| File | Purpose |
|------|---------|
| `tailwind.config.js` | Theme config |
| `resources/css/theme.css` | Theme utilities |

### Layouts
| File | Use For |
|------|---------|
| `resources/views/layouts/user.blade.php` | User pages |
| `resources/views/layouts/admin.blade.php` | Admin panel |

### Components
| File | Purpose |
|------|---------|
| `resources/views/components/theme-toggle.blade.php` | Theme toggle button |
| `resources/views/components/navigation.blade.php` | Top navigation |
| `resources/views/components/admin-sidebar.blade.php` | Admin sidebar |

### Documentation
| File | Contains |
|------|----------|
| `LIGHT_DARK_MODE_GUIDE.md` | Complete guide |
| `IMPLEMENTATION_SUMMARY.md` | Summary & checklist |

---

## 🔧 JavaScript Snippets

### Check Current Theme
```javascript
const isDark = document.documentElement.classList.contains('dark');
console.log(isDark ? 'Dark Mode' : 'Light Mode');
```

### Get Saved Theme
```javascript
const savedTheme = localStorage.getItem('mosala_theme');
console.log(savedTheme); // 'light' or 'dark'
```

### Set Theme Programmatically
```javascript
function setTheme(theme) {
    const html = document.documentElement;
    if (theme === 'dark') {
        html.classList.add('dark');
    } else {
        html.classList.remove('dark');
    }
    localStorage.setItem('mosala_theme', theme);
}

// Usage
setTheme('dark');  // Switch to dark
setTheme('light'); // Switch to light
```

### Detect System Preference
```javascript
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
console.log(prefersDark ? 'System prefers dark' : 'System prefers light');
```

### Listen to System Preference Changes
```javascript
const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
darkModeQuery.addEventListener('change', (e) => {
    console.log(e.matches ? 'User switched to dark' : 'User switched to light');
});
```

---

## ⚙️ Alpine.js Theme Toggle

### Usage in HTML
```html
@include('components.theme-toggle')
```

### How It Works
```javascript
// Included in theme-toggle.blade.php
function themeToggle() {
    return {
        isDark: false,
        
        init() {
            this.isDark = document.documentElement.classList.contains('dark');
        },
        
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

## 🐛 Troubleshooting

### Problem: Theme flashes on load
**Solution:** Ensure blocking script is in `<head>` before other scripts

### Problem: Theme not saving
**Solution:** Check localStorage is enabled, verify key is `'mosala_theme'`

### Problem: Transitions not smooth
**Solution:** Add `transition-colors duration-300` to element

### Problem: Dark mode not applying
**Solution:** Verify `darkMode: 'class'` in tailwind.config.js

### Problem: Colors look wrong
**Solution:** Check CSS cascade, inspect computed styles in DevTools

---

## ✅ Testing Checklist

- [ ] Toggle theme button works
- [ ] Theme persists after refresh
- [ ] System preference respected on first visit
- [ ] No flash on page load
- [ ] All components themed (sidebars, forms, tables)
- [ ] Transitions smooth (300ms)
- [ ] Brand colors constant (Congo Blue, Gold)
- [ ] Good contrast in both themes
- [ ] Mobile looks good
- [ ] localStorage saving correctly
- [ ] No console errors

---

## 📱 Mobile Considerations

The theme system works seamlessly on mobile:
- Touch-friendly toggle button (40px target)
- Respects system dark mode preference
- Works offline (localStorage persistence)
- No performance impact on scroll/animations

---

## 🎓 Learning Path

1. **Start:** Read IMPLEMENTATION_SUMMARY.md
2. **Understand:** Review tailwind.config.js changes
3. **Explore:** Check theme.css for component styles
4. **Practice:** Add theme to a new component
5. **Reference:** Use this quick card for classes

---

## 🔗 Related Files

```
project/
├── tailwind.config.js ...................... Theme config
├── resources/
│   ├── css/
│   │   ├── app.css ......................... Imports theme.css
│   │   └── theme.css ....................... All theme utilities
│   └── views/
│       ├── layouts/
│       │   ├── user.blade.php .............. User layout
│       │   └── admin.blade.php ............. Admin layout
│       └── components/
│           ├── theme-toggle.blade.php ..... Toggle button
│           ├── navigation.blade.php ....... Top nav
│           └── admin-sidebar.blade.php .... Sidebar
├── LIGHT_DARK_MODE_GUIDE.md ............... Complete guide
├── IMPLEMENTATION_SUMMARY.md .............. Summary
└── (THIS FILE)
```

---

## 🎯 Key Takeaways

1. **Always use theme classes:** `dark:bg-color` not just `bg-color`
2. **Brand colors are constant:** No need for `dark:` modifier
3. **Use transition-colors:** Smooth visual experience
4. **localStorage is key:** Persists theme choice
5. **FOUC script prevents flash:** No unstyled content visible
6. **CSS variables available:** Use them for complex styling

---

**Last Updated:** January 11, 2026  
**Platform:** Mosala+ ServiceRDC  
**Status:** ✅ Production Ready
