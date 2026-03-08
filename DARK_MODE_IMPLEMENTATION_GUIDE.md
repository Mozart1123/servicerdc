# MOSALA+ Permanent Dark Mode Implementation Guide

## Overview
The MOSALA+ platform has been fully refactored to use a **Permanent Dark Mode** ecosystem with glassmorphism effects and the national color palette of the Democratic Republic of Congo.

## Color Palette

### Primary Colors
- **Congo Blue (Primary)**: `#007FFF` - For buttons, active states, and primary highlights
- **Congo Gold (Attention/Warning)**: `#F7D000` - For star ratings, badges, and critical statistics
- **Congo Red (Danger)**: `#CE1021` - For logout buttons and rejection actions

### Background Colors
- **Page Background**: `#0A0F1C` (Deep Midnight Blue) - Primary page background
- **Surface/Sidebar**: `#111827` (Rich Navy Gray) - Cards, sidebars, modals
- **Border**: `rgba(255,255,255,0.05)` - Subtle glassmorphism borders

### Text Colors
- **Primary Text**: `#FFFFFF` (Pure White) - Headlines and primary content
- **Secondary Text**: `#94A3B8` (Slate-400) - Supporting text and labels

## CSS Variables

Access these variables throughout your styles:

```css
:root {
    --bg-page: #0A0F1C;
    --bg-content: #111827;
    --bg-sidebar: #111827;
    --border-color: #1F2937;
    --text-primary: #FFFFFF;
    --text-secondary: #94A3B8;
    --congo-blue: #007FFF;
    --congo-gold: #F7D000;
    --congo-red: #CE1021;
}
```

## Tailwind Configuration

The `tailwind.config.js` file includes extended color definitions:

```javascript
colors: {
    congo: {
        blue: '#007FFF',
        gold: '#F7D000',
        red: '#CE1021',
    },
    dark: {
        page: '#0A0F1C',
        surface: '#111827',
        border: 'rgba(255,255,255,0.05)',
        border-light: 'rgba(255,255,255,0.10)',
    },
}
```

## Glassmorphism Implementation

All cards and containers use the glassmorphism effect:

```html
<div style="background: rgba(255,255,255,0.03); backdrop-filter: blur(6px); border: 1px solid rgba(255,255,255,0.04);">
    Content
</div>
```

### Glassmorphism Layers
- **Glass XS** (`bg-glass-xs`): `rgba(255, 255, 255, 0.01)` - Minimal transparency
- **Glass SM** (`bg-glass-sm`): `rgba(255, 255, 255, 0.02)` - Subtle transparency
- **Glass MD** (`bg-glass-md`): `rgba(255, 255, 255, 0.03)` - Standard transparency
- **Glass LG** (`bg-glass-lg`): `rgba(255, 255, 255, 0.05)` - Higher transparency
- **Glass XL** (`bg-glass-xl`): `rgba(255, 255, 255, 0.08)` - Maximum transparency

### Backdrop Blur Values
- `backdrop-blur-glass`: `6px`
- `backdrop-blur-md-glass`: `10px`
- `backdrop-blur-lg-glass`: `16px`

## Components

### 1. Permanent Dark Sidebar
**File**: `resources/views/components/permanent-dark-sidebar.blade.php`

Features:
- Rich Navy Gray background (#111827)
- Subtle right border with white/5 opacity
- Congo Blue active link highlighting with glow effect
- Red logout button (#CE1021)
- Smooth transitions and hover effects

```blade
@include('components.permanent-dark-sidebar')
```

### 2. Admin Job Applications Table
**File**: `resources/views/components/admin-job-applications.blade.php`

Features:
- Zebra-striping using bg-white/2 alternation
- Status badges:
  - **Approved**: Congo Blue fill with glow
  - **Pending**: Gold outline with subtle background
  - **Rejected**: Red outline with subtle background
- Glassmorphism container
- Hover effects on rows

### 3. User Dashboard
**File**: `resources/views/components/user-dashboard-dark.blade.php`

Features:
- Dark translucent search bar with glassmorphism
- Service category cards as glass containers
- Congo Blue icons with glow effect
- Grid layout (6 services)
- Hover expansion effects
- Recent activity section

### 4. Permanent Dark Head Script
**File**: `resources/views/components/permanent-dark-head.blade.php`

Must be included in the `<head>` of all layouts to force dark mode:

```blade
@include('components.permanent-dark-head')
```

## Layout Updates

### Auth Layout
- File: `resources/views/layouts/auth.blade.php`
- Forced dark mode with permanent-dark-head
- Dark form inputs with Congo Blue focus states
- Updated branding: "MOSALA+" with Gold accent

### Admin Layout
- File: `resources/views/layouts/admin.blade.php`
- Forced dark mode globally
- Dark sidebar (#111827)
- Updated header styling

### User Layout
- File: `resources/views/layouts/user.blade.php`
- Forced dark mode globally
- Support for user dashboard components

## Status Badge Implementation

### HTML Usage
```html
<!-- Approved Badge -->
<span class="badge-approved">
    <i class="fas fa-check-circle mr-1.5"></i> Approved
</span>

<!-- Pending Badge -->
<span class="badge-pending">
    <i class="fas fa-clock mr-1.5"></i> Pending
</span>

<!-- Rejected Badge -->
<span class="badge-rejected">
    <i class="fas fa-times-circle mr-1.5"></i> Rejected
</span>
```

## Form Styling

All input fields automatically inherit dark mode styling:

```html
<input type="text" placeholder="Search...">
```

Auto-applied styles:
- Background: `rgba(31, 41, 55, 0.5)`
- Border: `1px solid rgba(255, 255, 255, 0.1)`
- Text Color: `#FFFFFF`
- Focus Shadow: Congo Blue glow effect

## Glow Effects

### Congo Blue Glow
```css
.glow-blue {
    box-shadow: 0 0 20px rgba(0, 127, 255, 0.2);
}

.glow-blue-lg {
    box-shadow: 0 0 40px rgba(0, 127, 255, 0.3);
}
```

### Hover Glow
```html
<button onmouseover="this.style.boxShadow='0 0 24px rgba(0,127,255,0.3)'" 
        onmouseout="this.style.boxShadow='0 0 20px rgba(0,127,255,0.2)'">
    Action
</button>
```

## Transitions

All elements have smooth 200ms-500ms transitions for:
- Background color
- Border color
- Text color
- Box shadow
- Transform

## Best Practices

1. **Always use the color variables** instead of hardcoding values
2. **Apply glassmorphism to all containers** for visual consistency
3. **Use Congo Blue for interactive elements** (buttons, links, active states)
4. **Reserve Gold for attention-grabbing elements** (warnings, ratings, badges)
5. **Use Red sparingly** (logout, rejection, danger actions)
6. **Maintain contrast** - Ensure text is readable (white on dark backgrounds)
7. **Test hover states** - All interactive elements should have hover feedback

## File Structure

```
resources/
├── css/
│   └── app.css                          # Main dark mode stylesheet
├── views/
│   ├── components/
│   │   ├── permanent-dark-head.blade.php    # Dark mode enforcer script
│   │   ├── permanent-dark-sidebar.blade.php # Universal sidebar
│   │   ├── admin-job-applications.blade.php # Admin table component
│   │   └── user-dashboard-dark.blade.php    # User dashboard component
│   └── layouts/
│       ├── auth.blade.php               # Auth layout with dark mode
│       ├── admin.blade.php              # Admin layout with dark mode
│       └── user.blade.php               # User layout with dark mode
```

## Migration Notes

If you're updating existing pages:

1. Add `class="dark"` to the `<html>` element
2. Include `@include('components.permanent-dark-head')` in `<head>`
3. Remove any light mode classes or utilities
4. Update text colors to use white (#FFFFFF) or secondary (#94A3B8)
5. Replace light backgrounds with dark surface colors or glassmorphism containers
6. Apply Congo Blue glow effects to interactive elements

## Testing Checklist

- [ ] Dark mode loads without flash (white screen)
- [ ] All text is readable (sufficient contrast)
- [ ] Links are Congo Blue (#007FFF)
- [ ] Buttons have hover effects with glow
- [ ] Form inputs have dark backgrounds and Congo Blue focus states
- [ ] Tables have zebra-striping
- [ ] Cards have glassmorphism effects
- [ ] Status badges display correctly
- [ ] Logout button is Red (#CE1021)
- [ ] Sidebar active links have Congo Blue highlighting
- [ ] All pages maintain consistent dark theme

## Browser Support

This implementation uses:
- CSS Variables (IE 11+)
- Backdrop Filter (modern browsers, fallback to semi-transparent background)
- CSS Gradients (all modern browsers)

For older browsers, provide fallbacks for `backdrop-filter`.
