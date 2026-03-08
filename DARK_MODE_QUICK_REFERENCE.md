# MOSALA+ Dark Mode Quick Reference

## Color Palette Quick Reference

```
Congo Blue:    #007FFF  ← Primary (buttons, links, active states)
Congo Gold:    #F7D000  ← Warnings (badges, ratings, attention)
Congo Red:     #CE1021  ← Danger (logout, rejection)
Page BG:       #0A0F1C  ← Deep dark blue
Surface BG:    #111827  ← Cards, sidebars, modals
Text Primary:  #FFFFFF  ← Headlines, primary text
Text Sec:      #94A3B8  ← Supporting text, labels
```

## Essential Components

### 1. Include in Every Layout Head
```blade
<!-- Must be FIRST thing in <head> -->
@include('components.permanent-dark-head')
```

### 2. Add Dark Class to HTML
```html
<html class="dark">
```

### 3. Sidebar Component
```blade
@include('components.permanent-dark-sidebar')
```

### 4. Dashboard Component
```blade
@include('components.user-dashboard-dark')
```

### 5. Admin Table Component
```blade
@include('components.admin-job-applications')
```

## Tailwind Classes

### Backgrounds
```
bg-[#0A0F1C]           ← Page background
bg-[#111827]           ← Surface (cards, sidebar)
bg-glass-xs            ← Minimal glass transparency
bg-glass-sm            ← Subtle glass
bg-glass-md            ← Standard glass
bg-glass-lg            ← Higher glass
```

### Text Colors
```
text-white             ← Primary text
text-[#94A3B8]        ← Secondary text
text-[#007FFF]        ← Congo Blue (links, highlights)
text-[#F7D000]        ← Congo Gold (warnings)
text-[#CE1021]        ← Congo Red (danger)
```

### Borders
```
border-white/5         ← Subtle glass border
border-white/10        ← Lighter glass border
border-[#007FFF]      ← Congo Blue border
```

### Shadows (Glow Effects)
```
shadow-[0_0_20px_rgba(0,127,255,0.2)]  ← Congo Blue glow
shadow-[0_0_40px_rgba(0,127,255,0.3)]  ← Large glow
```

### Backdrop
```
backdrop-blur-[6px]    ← Glass blur
backdrop-blur-[10px]   ← Medium blur
backdrop-blur-[16px]   ← Large blur
```

## Common Patterns

### Glassmorphism Card
```html
<div class="rounded-xl p-6" style="background: rgba(255,255,255,0.03); backdrop-filter: blur(6px); border: 1px solid rgba(255,255,255,0.04);">
    Content
</div>
```

### Button with Glow
```html
<button 
    style="background-color: #007FFF; box-shadow: 0 0 20px rgba(0,127,255,0.2);"
    onmouseover="this.style.boxShadow='0 0 24px rgba(0,127,255,0.3)'"
    onmouseout="this.style.boxShadow='0 0 20px rgba(0,127,255,0.2)'">
    Action Button
</button>
```

### Status Badge - Approved
```html
<span style="background-color: #007FFF; color: #FFFFFF; padding: 0.375rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; box-shadow: 0 0 12px rgba(0,127,255,0.2);">
    <i class="fas fa-check-circle mr-1.5"></i> Approved
</span>
```

### Status Badge - Pending
```html
<span style="background: rgba(247,208,0,0.08); color: #F7D000; border: 1.5px solid rgba(247,208,0,0.4); padding: 0.375rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
    <i class="fas fa-clock mr-1.5"></i> Pending
</span>
```

### Status Badge - Rejected
```html
<span style="background: rgba(206,16,33,0.08); color: #CE1021; border: 1.5px solid rgba(206,16,33,0.4); padding: 0.375rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
    <i class="fas fa-times-circle mr-1.5"></i> Rejected
</span>
```

### Form Input
```html
<input 
    type="text" 
    placeholder="Search..."
    style="background-color: rgba(31,41,55,0.5); border: 1px solid rgba(255,255,255,0.1); color: #FFFFFF; border-radius: 0.5rem;"
    onfocus="this.style.borderColor='#007FFF'; this.style.boxShadow='0 0 16px rgba(0,127,255,0.2)'"
    onblur="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.boxShadow='none'">
```

### Table Zebra Striping
```html
<tbody>
    <!-- Odd rows: lighter background -->
    <tr style="background: rgba(255,255,255,0.01);">
        <td>Content</td>
    </tr>
    
    <!-- Even rows: slightly darker -->
    <tr style="background: rgba(255,255,255,0.02);">
        <td>Content</td>
    </tr>
</tbody>
```

### Active Link (Sidebar)
```html
<a href="#" style="background-color: rgba(0,127,255,0.15); color: #FFFFFF; box-shadow: 0 0 20px rgba(0,127,255,0.1);">
    <span style="background-color: #007FFF; box-shadow: 0 0 12px rgba(0,127,255,0.5);"></span>
    Dashboard
</a>
```

## CSS Variables Usage

```css
:root {
    --bg-page: #0A0F1C;
    --bg-content: #111827;
    --congo-blue: #007FFF;
    --congo-gold: #F7D000;
    --congo-red: #CE1021;
    --text-secondary: #94A3B8;
}

/* Use in CSS */
.my-component {
    background-color: var(--bg-content);
    color: var(--text-secondary);
    border: 1px solid rgba(255,255,255,0.05);
}

.my-button {
    background-color: var(--congo-blue);
}
```

## Inline Style Usage (Blade)

```blade
<div style="background-color: #0A0F1C; color: #FFFFFF;">
    Content
</div>

<!-- Using color variables from config -->
<button style="background-color: #007FFF; box-shadow: 0 0 20px rgba(0,127,255,0.2);">
    Action
</button>
```

## File Locations

- **Layout Templates**: `resources/views/layouts/`
- **Component Library**: `resources/views/components/`
- **CSS Stylesheet**: `resources/css/app.css`
- **Tailwind Config**: `tailwind.config.js`
- **Documentation**: `DARK_MODE_IMPLEMENTATION_GUIDE.md`

## Checklist for New Pages

- [ ] Add `class="dark"` to `<html>`
- [ ] Include `@include('components.permanent-dark-head')` in head
- [ ] Use `#0A0F1C` for page background
- [ ] Use `#111827` for cards/containers or glassmorphism
- [ ] Use `#FFFFFF` for primary text
- [ ] Use `#94A3B8` for secondary text
- [ ] Use `#007FFF` for interactive elements
- [ ] Test text contrast (WCAG AA minimum)
- [ ] Add hover effects to buttons/links
- [ ] Apply glassmorphism to containers
- [ ] Test on dark background (no white flash)

## Troubleshooting

**White flash on page load?**
- Ensure `@include('components.permanent-dark-head')` is in `<head>` BEFORE other assets
- Check that `<html class="dark">` is set

**Text not visible?**
- Check contrast: Use white (#FFFFFF) or light gray (#94A3B8) on dark backgrounds
- Avoid pure black text

**Links too bright?**
- Use Congo Blue (#007FFF) instead of default blue
- Add hover effects

**Buttons don't stand out?**
- Add Congo Blue glow: `box-shadow: 0 0 20px rgba(0,127,255,0.2)`
- Add hover transitions

## Support
For questions or issues, refer to:
- `DARK_MODE_IMPLEMENTATION_GUIDE.md` - Full documentation
- `resources/views/components/` - Component examples
- `resources/css/app.css` - CSS framework
