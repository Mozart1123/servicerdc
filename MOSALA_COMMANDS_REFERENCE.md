# 🚀 MOSALA+ Dashboard - Commands & Workflows

Quick reference for common commands and development workflows.

---

## 🏃 Quick Start Commands

### Start Development Server
```bash
cd c:\xampp\htdocs\rdc\rdc
php artisan serve --port=8000
```
**Access**: http://localhost:8000/user/dashboard

### Stop Server
```bash
# Press Ctrl+C in terminal
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 📊 Database Commands

### Run Migrations
```bash
php artisan migrate
```

### Rollback Last Migration
```bash
php artisan migrate:rollback
```

### Rollback All & Re-run
```bash
php artisan migrate:reset
php artisan migrate
```

### Create New Migration
```bash
php artisan make:migration create_table_name
php artisan make:migration add_column_to_table
```

### Seed Database
```bash
php artisan db:seed
```

### Specific Seeder
```bash
php artisan db:seed --class=CategorySeeder
```

---

## 🎭 Artisan Commands for Development

### Create Model (with migration & controller)
```bash
php artisan make:model ModelName -mcr
# -m = migration
# -c = controller
# -r = resourceful
```

### Create Controller
```bash
php artisan make:controller ControllerName
php artisan make:controller Api/UserController
```

### Create Middleware
```bash
php artisan make:middleware MiddlewareName
```

### Create Service Class
```bash
php artisan make:service ServiceName
```

### List All Routes
```bash
php artisan route:list
```

### Check Route Details
```bash
php artisan route:list --name=user.dashboard
```

---

## 🧪 Testing Commands

### Run All Tests
```bash
php artisan test
```

### Run Specific Test
```bash
php artisan test --filter=DashboardControllerTest
```

### Run with Coverage
```bash
php artisan test --coverage
```

### Create Test
```bash
php artisan make:test DashboardTest --unit
php artisan make:test DashboardControllerTest --feature
```

---

## 🎨 Frontend Development

### View Compilation (if using Blade components)
```bash
# No compilation needed for Blade

# If using CSS preprocessor:
npm run dev      # Development
npm run build    # Production
```

### Watch for Changes
```bash
npm run watch
```

---

## 🔍 Debugging Commands

### Tinker (Interactive Shell)
```bash
php artisan tinker

# Inside tinker:
> User::all();
> User::first()->name;
> Route::list();
```

### Check Environment
```bash
php artisan --version
php -v
node -v
npm -v
```

### View Config
```bash
php artisan config:show
php artisan config:show app.name
```

---

## 📋 File Management Commands

### List Directory
```bash
# View file structure
ls -la
dir /s
Get-ChildItem -Recurse
```

### Create Directory
```bash
mkdir -p path/to/directory
```

### Copy Files
```bash
cp source.php dest.php
copy source.php dest.php
```

### Remove Files
```bash
rm file.php
del file.php
```

---

## 🔐 Security Commands

### Generate App Key
```bash
php artisan key:generate
```

### Create Superadmin User
```bash
php artisan tinker

# In tinker:
> App\Models\User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('password'), 'role' => 'super_admin']);
```

### Reset User Password
```bash
php artisan tinker

# In tinker:
> $user = App\Models\User::first();
> $user->update(['password' => bcrypt('newpassword')]);
```

---

## 📂 File Navigation Shortcuts

### Navigate to Project
```bash
cd c:\xampp\htdocs\rdc\rdc
```

### View Files
```bash
# Dashboard view
code resources/views/user/dashboard.blade.php

# Controller
code app/Http/Controllers/User/DashboardController.php

# Layout
code resources/views/layouts/app.blade.php

# Components
code resources/views/components/sidebar.blade.php
code resources/views/components/navbar.blade.php
```

### Open in VS Code
```bash
code .  # Current directory
code resources/views/  # Specific folder
```

---

## 🐛 Troubleshooting Commands

### Check Laravel Version
```bash
php artisan --version
```

### Check PHP Version
```bash
php -v
```

### View Error Logs
```bash
# Windows
type storage\logs\laravel.log

# Unix
tail -f storage/logs/laravel.log
```

### Clear Compiled Classes
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

### Reset Everything
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

## 🔗 Common Git Workflows

### Check Status
```bash
git status
```

### Add Changes
```bash
git add .
git add resources/views/
```

### Commit Changes
```bash
git commit -m "feat: reconstruct dashboard with new design"
```

### Create Branch
```bash
git checkout -b feature/dashboard-rebuild
```

### Switch Branch
```bash
git checkout main
```

### Push Changes
```bash
git push origin feature/dashboard-rebuild
```

---

## 💻 Development Workflow

### Daily Development Cycle

1. **Start Server**
   ```bash
   php artisan serve --port=8000
   ```

2. **Make Changes**
   - Edit view files in `resources/views/`
   - Update controller in `app/Http/Controllers/`
   - Modify config/routes as needed

3. **Test Changes**
   - Browse to http://localhost:8000
   - Test interactive features
   - Check responsive design

4. **Clear Cache (if needed)**
   ```bash
   php artisan view:clear
   ```

5. **Commit Changes**
   ```bash
   git add .
   git commit -m "Your message"
   ```

---

## 🎯 Dashboard-Specific Workflows

### Edit Dashboard View
```bash
code resources/views/user/dashboard.blade.php
```
Changes auto-refresh in browser (no recompilation needed).

### Edit Sidebar
```bash
code resources/views/components/sidebar.blade.php
```
Changes auto-refresh.

### Edit Navbar
```bash
code resources/views/components/navbar.blade.php
```
Changes auto-refresh.

### Edit Master Layout
```bash
code resources/views/layouts/app.blade.php
```
Changes affect all pages using this layout.

### Change Colors
Edit in `app.blade.php` → Tailwind config:
```javascript
'congo-blue': '#007FFF'  // Change hex code here
```

### Add New Tab
1. Edit `dashboard.blade.php`
2. Add button in tabs section
3. Add `<div x-show="activeTab === 'tab-name">` block
4. Pass data from controller

---

## 📝 Useful Blade Snippets

### Include Partial
```blade
@include('components.sidebar')
@include('components.navbar')
@include('user.partials.overview')
```

### Check Authentication
```blade
@auth
  <!-- Authenticated user content -->
@endauth

@guest
  <!-- Guest content -->
@endguest

@if(Auth::user()->isAdmin())
  <!-- Admin content -->
@endif
```

### Loops
```blade
@foreach($items as $item)
  {{ $item->name }}
@endforeach

@forelse($items as $item)
  {{ $item->name }}
@empty
  No items found
@endforelse
```

### Conditionals
```blade
@if($condition)
@elseif($other)
@else
@endif
```

### Custom Classes
```blade
class="@if($active) active @endif"
:class="activeTab === 'tab' ? 'active' : ''"
```

---

## 🧬 Alpine.js Snippets

### Initialize Data
```javascript
x-data="dashboardApp()"
```

### Show/Hide Elements
```blade
<div x-show="activeTab === 'services'">Content</div>
```

### Click Handler
```blade
<button @click="activeTab = 'services'">Click me</button>
```

### Conditional Classes
```blade
:class="activeTab === 'tab' ? 'active' : ''"
```

### Transitions
```blade
<div x-show="activeTab === 'tab'" x-transition>
  Fades and slides in/out
</div>
```

---

## 🎨 Tailwind CSS Utilities

### Responsive Classes
```
sm:  640px breakpoint
md:  768px breakpoint
lg:  1024px breakpoint
xl:  1280px breakpoint
```

### Grid
```
grid grid-cols-1
sm:grid-cols-2
lg:grid-cols-4
```

### Flex
```
flex flex-col
md:flex-row
gap-4
```

### Colors
```
text-congo-blue
bg-congo-yellow
border-congo-red
hover:bg-congo-blue
```

### Spacing
```
p-4 p-6 p-8
m-4 m-6 m-8
gap-4 gap-6
```

### Rounded
```
rounded-lg      (8px)
rounded-xl      (12px)
rounded-2xl     (16px)
rounded-full    (50%)
```

---

## 📚 Documentation Files

| File | Purpose | Location |
|------|---------|----------|
| MOSALA_DASHBOARD_REBUILD.md | Architecture & design | Root |
| MOSALA_DASHBOARD_QUICKREF.md | Developer reference | Root |
| MOSALA_DASHBOARD_CODE_REFERENCE.md | Code examples | Root |
| MOSALA_VISUAL_STRUCTURE.md | Visual guide | Root |
| MOSALA_DASHBOARD_COMPLETION_SUMMARY.md | Project summary | Root |

---

## 🎓 Learning Resources

### Official Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Alpine.js](https://alpinejs.dev)
- [Font Awesome Icons](https://fontawesome.com/icons)

### Quick References
- [Tailwind Classes Cheat Sheet](https://tailwindcomponents.com)
- [Alpine.js Quick Start](https://alpinejs.dev/start-here)
- [Blade Template Syntax](https://laravel.com/docs/blade)

---

## 💡 Pro Tips

1. **Use `php artisan tinker`** for quick database queries
2. **Ctrl+/ in VS Code** to toggle comments
3. **F12** to open browser DevTools
4. **Ctrl+Shift+D** to debug in VS Code
5. **Use Chrome DevTools** for responsive testing
6. **Save frequently** and test in browser immediately
7. **Check console** for JavaScript errors
8. **Use browser cache disable** in DevTools (Application tab)

---

## 🆘 Getting Help

### If Something Breaks
1. Check error message in browser console
2. Check Laravel logs in `storage/logs/laravel.log`
3. Clear cache: `php artisan cache:clear`
4. Check documentation files
5. Review git diff: `git diff`

### Common Issues & Fixes
```bash
# View not found
- Check file path
- Verify route points to correct controller method
- Clear view cache: php artisan view:clear

# Undefined variable
- Check controller compact() includes variable
- Verify variable name spelling

# CSS not updating
- Clear view cache: php artisan view:clear
- Hard refresh browser: Ctrl+Shift+R

# JavaScript not working
- Check Alpine.js is loaded in head
- Check console for syntax errors
- Verify x-data and @click attributes
```

---

## 📞 Support

For additional help, refer to:
- **Documentation Index**: DOCUMENTATION_INDEX.md
- **Architecture Guide**: MOSALA_DASHBOARD_REBUILD.md
- **Quick Reference**: MOSALA_DASHBOARD_QUICKREF.md

---

**Last Updated**: January 13, 2026  
**Status**: Ready for Development ✨
