# ServiceRDC - Quick Reference Card
**Backend Architecture | Laravel 12 | Version 1.0**

---

## 🚀 Quick Start Commands

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Clear all caches
php artisan config:clear && php artisan route:clear && php artisan view:clear

# Create admin user (Tinker)
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@servicerdc.com', 'password' => Hash::make('password'), 'role' => 'admin']);
```

---

## 🔐 User Roles
- **User**: Browse services/jobs, apply to jobs, submit requests
- **Admin**: Full CRUD on services/jobs, manage applications, respond to requests
- **Super Admin**: Full system access + admin management

---

## 📍 Key Routes

### Admin Panel Routes
| Method | URL | Controller@Method | Purpose |
|--------|-----|-------------------|---------|
| GET | `/admin/dashboard` | AdminDashboardController@index | Admin overview |
| GET | `/admin/services` | AdminServiceController@index | List all services |
| POST | `/admin/services` | AdminServiceController@store | Create service |
| GET | `/admin/jobs` | AdminJobController@index | List all jobs |
| POST | `/admin/jobs` | AdminJobController@store | Create job |
| GET | `/admin/job-applications` | AdminJobApplicationController@index | View applications |
| PATCH | `/admin/job-applications/{id}/status` | AdminJobApplicationController@updateStatus | Update application status |
| GET | `/admin/service-requests` | UserServiceRequestController@adminIndex | View user requests |
| PATCH | `/admin/service-requests/{id}` | UserServiceRequestController@adminRespond | Respond to request |

### User Dashboard Routes
| Method | URL | Controller@Method | Purpose |
|--------|-----|-------------------|---------|
| GET | `/user/dashboard` | UserDashboardController@index | User overview |
| GET | `/user/services` | UserServiceController@index | Browse services |
| GET | `/user/jobs` | UserJobController@index | Browse jobs |
| GET | `/user/jobs/{id}` | UserJobController@show | View job details |
| POST | `/user/jobs/{id}/apply` | UserJobController@apply | Apply to job |
| GET | `/user/my-applications` | UserJobController@myApplications | My applications |
| POST | `/user/service-requests` | UserServiceRequestController@store | Submit request |

---

## 📊 Database Tables

### Core Tables
- **services**: Service providers database
- **job_offers**: Job postings
- **job_applications**: User applications (status: pending → reviewed → accepted/rejected)
- **service_requests**: User feedback/requests (status: pending → addressed)
- **users**: User authentication (role: user, admin, super_admin)
- **categories**: Service categories
- **notifications**: System notifications

### Key Relationships
```
User (1) → (many) Services
User (1) → (many) JobOffers
User (1) → (many) JobApplications
JobOffer (1) → (many) JobApplications
Service (1) → (many) ServiceRequests
Category (1) → (many) Services
```

---

## 🔄 Cross-System Flows

### Admin Creates Service → User Sees It
1. Admin: `POST /admin/services` → Service saved
2. User: `GET /user/services` → Service fetched from database
3. **Result**: Immediate synchronization via database query

### User Applies to Job → Admin Sees It
1. User: `POST /user/jobs/{id}/apply` → JobApplication created
2. Admin: `GET /admin/job-applications` → Application fetched
3. Notification created for Admin
4. **Result**: Admin sees new application instantly

### Admin Updates Status → User Notified
1. Admin: `PATCH /admin/job-applications/{id}/status` → Status updated
2. Notification created for User
3. User: `GET /user/my-applications` → Sees updated status
4. **Result**: Real-time status sync

---

## 🛡️ Security Implementation

### RoleMiddleware
**File**: `app/Http/Middleware/RoleMiddleware.php`

**Usage in routes**:
```php
Route::middleware(['auth', 'role:admin,super_admin'])->group(...);
```

**Protection**:
- Checks if user is authenticated
- Verifies user has required role(s)
- Logs unauthorized access attempts
- Returns 403 for unauthorized users

**URL Manipulation Prevention**:
- User tries `/admin/dashboard` → Middleware checks role → 403 if not admin
- Even manual URL changes are blocked

---

## 📁 Key Files Reference

### Controllers
```
app/Http/Controllers/
├── Admin/
│   ├── DashboardController.php
│   ├── ServiceController.php
│   ├── JobController.php
│   ├── JobApplicationController.php
│   ├── UserController.php
│   └── CategoryController.php
└── User/
    ├── DashboardController.php
    ├── ServiceController.php
    ├── JobController.php
    └── ServiceRequestController.php
```

### Models
```
app/Models/
├── Service.php (with scopes: active, verified, byCategory, search)
├── JobOffer.php (with scopes: active, notExpired, byLocation)
├── JobApplication.php (with scopes: pending, accepted, rejected)
├── ServiceRequest.php (with scopes: pending, addressed)
└── User.php
```

### Views
```
resources/views/
├── layouts/
│   ├── admin.blade.php
│   └── user.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── services/
│   ├── jobs/
│   └── job-applications/index.blade.php ✨ NEW
└── user/
    ├── dashboard.blade.php
    ├── services/
    ├── jobs/
    │   ├── index.blade.php
    │   └── show.blade.php ✨ NEW (with "Postuler" form)
    └── applications/index.blade.php
```

---

## 🧪 Testing Scenarios

### Admin Tests
```bash
# 1. Create Service
POST /admin/services
Data: {title, category_id, description, price, location}
Expected: 302 redirect + success message

# 2. View Applications
GET /admin/job-applications
Expected: 200 + list of applications with stats

# 3. Accept Application
PATCH /admin/job-applications/{id}/status
Data: {status: 'accepted'}
Expected: 302 redirect + user notification created
```

### User Tests
```bash
# 1. Browse Jobs
GET /user/jobs
Expected: 200 + paginated active jobs

# 2. View Job Details
GET /user/jobs/{id}
Expected: 200 + job details + "Postuler" form

# 3. Apply to Job
POST /user/jobs/{id}/apply
Data: {cover_letter, resume_url}
Expected: 302 redirect + application created + admin notified

# 4. Track Applications
GET /user/my-applications
Expected: 200 + list of my applications with statuses
```

### Security Tests
```bash
# 1. User tries Admin route
Login as: user@example.com (role: user)
Access: /admin/dashboard
Expected: 403 Forbidden

# 2. Admin tries User route
Login as: admin@example.com (role: admin)
Access: /user/dashboard
Expected: 200 OK (admins can access user routes)
```

---

## 📝 Common Tasks

### Create Admin User
```php
php artisan tinker
User::create([
    'name' => 'Admin Principal',
    'email' => 'admin@servicerdc.com',
    'password' => Hash::make('admin123'),
    'role' => 'admin'
]);
```

### Create Categories
```php
Category::create(['name' => 'Informatique']);
Category::create(['name' => 'Construction']);
Category::create(['name' => 'Santé']);
```

### Check Route List
```bash
php artisan route:list --name=admin
php artisan route:list --name=user
```

### View Logs
```bash
tail -f storage/logs/laravel.log
```

---

## 🎨 UI Design System

### Colors
- **Primary Blue**: `#007FFF` (rdc-blue)
- **Blue Dark**: `#0066CC` (rdc-blue-dark)
- **Yellow**: `#F0B800` (rdc-yellow)
- **Red**: `#FF4757` (rdc-red)
- **Background**: `#F0F4F5` (bg-slate-50 equivalent)

### Fonts
- **Body**: Inter (via Google Fonts)
- **Headings**: Plus Jakarta Sans (via Google Fonts)

### Components
- **Cards**: `rounded-2xl shadow-lg border border-slate-200`
- **Buttons**: `px-6 py-3 bg-rdc-blue text-white font-bold rounded-xl hover:bg-rdc-blue-dark`
- **Status Badges**: `px-3 py-1 rounded-full text-xs font-black uppercase`

---

## 🚨 Troubleshooting

### Issue: Routes not found
**Solution**: `php artisan route:clear`

### Issue: Views not updating
**Solution**: `php artisan view:clear`

### Issue: 403 error for admin
**Solution**: Check user role in database (`SELECT role FROM users WHERE email = 'admin@example.com'`)

### Issue: File upload fails
**Solution**: 
```bash
php artisan storage:link
chmod -R 775 storage
```

### Issue: Notifications not showing
**Solution**: Check `notifications` table exists: `php artisan migrate`

---

## 📦 Dependencies

### PHP Packages (via Composer)
- Laravel Framework 12
- Laravel Sanctum (API authentication)
- Laravel Socialite (OAuth, if using social login)

### Frontend (via CDN)
- Tailwind CSS Play CDN: `https://cdn.tailwindcss.com`
- Font Awesome 6: `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css`
- Alpine.js: `https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js`
- Google Fonts: Inter & Plus Jakarta Sans

---

## 📚 Documentation Files

| File | Description |
|------|-------------|
| `BACKEND_ARCHITECTURE_IMPLEMENTATION.md` | Implementation plan & architecture overview |
| `DELIVERABLES_SUMMARY.md` | Complete deliverables documentation |
| `COMPLETE_BACKEND_GUIDE.md` | Comprehensive user & technical guide |
| `QUICK_REFERENCE.md` | This file - Quick reference card |

---

## 🎯 Key Achievements

✅ Database schema with proper foreign key relationships  
✅ Strict route separation (Admin vs User)  
✅ Role-based middleware security  
✅ CRUD operations for Services and Jobs  
✅ Job application system with status tracking  
✅ Service request/feedback system  
✅ Real-time notifications  
✅ File upload for CVs (PDF/DOC/DOCX)  
✅ Responsive Tailwind CSS design  
✅ URL manipulation prevention  
✅ Cross-system synchronization

---

**Created**: 2026-02-15  
**Version**: 1.0  
**Author**: Senior Laravel Backend Engineer  
**Status**: ✅ Production Ready
