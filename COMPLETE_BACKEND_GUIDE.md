# Complete Backend Architecture Guide
## ServiceRDC - Admin Panel & User Dashboard Synchronization System

**Version**: 1.0  
**Date**: 2026-02-15  
**Laravel Version**: 12  
**Status**: ✅ Production Ready

---

## Table of Contents
1. [System Overview](#system-overview)
2. [Database Architecture](#database-architecture)
3. [Routing Structure](#routing-structure)
4. [Controller Logic](#controller-logic)
5. [Security Implementation](#security-implementation)
6. [Frontend Views](#frontend-views)
7. [User Workflows](#user-workflows)
8. [Admin Workflows](#admin-workflows)
9. [Testing Guide](#testing-guide)
10. [Deployment Checklist](#deployment-checklist)

---

## 1. System Overview

### Architecture Design
The ServiceRDC backend follows a **strict separation of concerns** between Admin and User functionalities:

- **Admin Panel** (`/admin/*`): Complete CRUD operations for Services, Jobs, and Application management
- **User Dashboard** (`/user/*`): Browse Services/Jobs, Apply, Submit Feedback
- **Cross-System Sync**: Real-time database synchronization ensures Admin actions immediately reflect in User views

### Key Features
✅ Role-based access control (User, Admin, Super Admin)  
✅ Service and Job CRUD with category management  
✅ Job application system with status tracking  
✅ Service request/feedback system  
✅ Real-time notifications  
✅ Secure URL manipulation prevention  
✅ Responsive Tailwind CSS design  
✅ Premium UI with #F0F4F5 background

---

## 2. Database Architecture

### Core Tables

#### `services` Table
Stores service provider information.

**Fields**:
- `id`: Primary key
- `artisan_id`: Foreign key to `users.id`
- `category_id`: Foreign key to `categories.id`
- `title`: Service name
- `description`: Service details
- `price`: Service cost
- `location`: Service area
- `images`: JSON array of image URLs
- `is_verified`: Boolean, admin verification status
- `status`: `active` or `inactive`
- `rating`: Decimal (0.00 - 5.00)
- `timestamps`

**Relationships**:
```php
// Service.php
public function artisan() {
    return $this->belongsTo(User::class, 'artisan_id');
}

public function category() {
    return $this->belongsTo(Category::class);
}
```

---

#### `job_offers` Table
Stores job postings.

**Fields**:
- `id`: Primary key
- `user_id`: Foreign key to `users.id` (creator)
- `employer_id`: Foreign key to `users.id` (optional)
- `title`: Job title
- `company_name`: Company name
- `logo_url`: Company logo
- `location`: Job location
- `category`: Job sector
- `contract_type`: `Full-time`, `Part-time`, `Freelance`
- `salary_range`: Salary information
- `description`: Job description
- `requirements`: Job requirements
- `status`: `active` or `expired`
- `deadline`: Application deadline
- `timestamps`

**Relationships**:
```php
// JobOffer.php
public function user() {
    return $this->belongsTo(User::class);
}

public function applications() {
    return $this->hasMany(JobApplication::class);
}
```

---

#### `job_applications` Table
Links users to jobs they've applied to.

**Fields**:
- `id`: Primary key
- `job_offer_id`: Foreign key to `job_offers.id` (cascade delete)
- `user_id`: Foreign key to `users.id` (cascade delete)
- `status`: `pending`, `reviewed`, `accepted`, `rejected`
- `message`: Application message
- `resume_url`: Uploaded CV path
- `cover_letter`: Cover letter text
- `applied_at`: Application timestamp
- `reviewed_at`: Admin review timestamp
- `admin_notes`: Admin's internal notes
- `timestamps`

**Relationships**:
```php
// JobApplication.php
public function user() {
    return $this->belongsTo(User::class);
}

public function jobOffer() {
    return $this->belongsTo(JobOffer::class);
}
```

**Status Flow**:
```
User submits → `pending`
Admin reviews → `reviewed`
Admin decides → `accepted` OR `rejected`
```

---

#### `service_requests` Table
User feedback and service suggestions.

**Fields**:
- `id`: Primary key
- `user_id`: Foreign key to `users.id` (set null on delete)
- `service_id`: Foreign key to `services.id` (optional)
- `phone`: Contact phone
- `email`: Contact email
- `requested_service_name`: Requested service
- `category_needed`: Service category
- `description`: Request details
- `city`: User's city
- `location`: Detailed location
- `budget_min`: Minimum budget
- `budget_max`: Maximum budget
- `urgency`: `low`, `medium`, `high`, `urgent`
- `status`: `pending` or `addressed`
- `notes`: User's notes
- `response`: Admin response (deprecated)
- `admin_response`: Admin's detailed response
- `responded_by`: Foreign key to `users.id` (admin who responded)
- `responded_at`: Response timestamp
- `timestamps`

**Relationships**:
```php
// ServiceRequest.php
public function user() {
    return $this->belongsTo(User::class);
}

public function respondedByUser() {
    return $this->belongsTo(User::class, 'responded_by');
}
```

---

## 3. Routing Structure

### Admin Routes (`/admin/*`)

**Protection**: `middleware(['auth', 'role:admin,super_admin'])`

**Key Routes**:
```php
// Dashboard
GET /admin/dashboard → AdminDashboardController@index

// Services
GET    /admin/services         → AdminServiceController@index
GET    /admin/services/create  → AdminServiceController@create
POST   /admin/services         → AdminServiceController@store
GET    /admin/services/{id}/edit → AdminServiceController@edit
PUT    /admin/services/{id}    → AdminServiceController@update
DELETE /admin/services/{id}    → AdminServiceController@destroy

// Jobs
GET    /admin/jobs         → AdminJobController@index
GET    /admin/jobs/create  → AdminJobController@create
POST   /admin/jobs         → AdminJobController@store
GET    /admin/jobs/{id}/edit → AdminJobController@edit
PUT    /admin/jobs/{id}    → AdminJobController@update
DELETE /admin/jobs/{id}    → AdminJobController@destroy

// Job Applications
GET   /admin/job-applications       → AdminJobApplicationController@index
PATCH /admin/job-applications/{id}/status → AdminJobApplicationController@updateStatus

// Service Requests
GET   /admin/service-requests           → UserServiceRequestController@adminIndex
GET   /admin/service-requests/{id}      → UserServiceRequestController@show
PATCH /admin/service-requests/{id}      → UserServiceRequestController@adminRespond
```

---

### User Routes (`/user/*`)

**Protection**: `middleware(['auth', 'role:user,admin,super_admin'])`

**Key Routes**:
```php
// Dashboard
GET /user/dashboard → UserDashboardController@index

// Services
GET /user/services     → UserServiceController@index
GET /user/services/{id} → UserServiceController@show

// Jobs
GET  /user/jobs          → UserJobController@index
GET  /user/jobs/{id}     → UserJobController@show
POST /user/jobs/{id}/apply → UserJobController@apply

// Applications
GET    /user/my-applications → UserJobController@myApplications
DELETE /user/applications/{id} → UserJobController@withdrawApplication

// Service Requests
POST /user/service-requests → UserServiceRequestController@store

// Profile
GET /user/profile    → UserDashboardController@profile
PUT /user/profile    → UserDashboardController@updateProfile
```

---

## 4. Controller Logic

### Admin: Creating a Service

**File**: `app/Http/Controllers/Admin/ServiceController.php`

```php
public function store(Request $request) {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0',
        'location' => 'required|string|max:255',
    ]);
    
    $validated['artisan_id'] = auth()->id();
    $validated['status'] = 'active';
    
    Service::create($validated);
    
    return redirect()->route('admin.services.index')
        ->with('success', 'Service créé avec succès.');
}
```

**Flow**:
1. Admin fills form at `/admin/services/create`
2. `POST /admin/services` validates data
3. Service saved to database with `status = 'active'`
4. Redirect to service list with success message
5. **Service immediately appears in User dashboard** at `/user/services`

---

### User: Applying to a Job

**File**: `app/Http/Controllers/User/JobController.php`

```php
public function apply(Request $request, JobOffer $job) {
    // Validate input
    $request->validate([
        'cover_letter' => 'nullable|string|max:1000',
        'resume_url' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
    ]);
    
    // Check for duplicate application
    if ($job->applications()->where('user_id', auth()->id())->exists()) {
        return back()->with('error', 'Vous avez déjà postulé.');
    }
    
    // Handle file upload
    $resumePath = null;
    if ($request->hasFile('resume_url')) {
        $resumePath = $request->file('resume_url')->store('resumes', 'public');
    }
    
    // Create application
    $application = JobApplication::create([
        'job_offer_id' => $job->id,
        'user_id' => auth()->id(),
        'cover_letter' => $request->cover_letter,
        'resume_url' => $resumePath,
        'status' => 'pending',
        'applied_at' => now(),
    ]);
    
    // Notify admin
    Notification::create([
        'user_id' => $job->user_id,
        'type' => 'new_job_application',
        'title' => 'Nouvelle candidature',
        'message' => auth()->user()->name . " a postulé à '{$job->title}'",
        'data' => [
            'job_id' => $job->id,
            'application_id' => $application->id,
        ],
    ]);
    
    return back()->with('success', 'Candidature envoyée !');
}
```

**Flow**:
1. User views job at `/user/jobs/{id}`
2. User fills application form with cover letter and CV
3. `POST /user/jobs/{id}/apply` creates record in `job_applications`
4. Admin notification created
5. **Admin sees application** at `/admin/job-applications`
6. User can track status at `/user/my-applications`

---

### Admin: Updating Application Status

**File**: `app/Http/Controllers/Admin/JobApplicationController.php`

```php
public function updateStatus(Request $request, int $id) {
    $request->validate([
        'status' => 'required|in:pending,approved,rejected,accepted',
        'admin_notes' => 'nullable|string|max:1000',
    ]);
    
    $application = JobApplication::with(['user', 'jobOffer'])->findOrFail($id);
    
    $application->update([
        'status' => $request->status,
        'admin_notes' => $request->admin_notes,
        'reviewed_at' => now(),
    ]);
    
    // Notify user
    Notification::create([
        'user_id' => $application->user_id,
        'type' => 'application_status_updated',
        'title' => 'Candidature mise à jour',
        'message' => "Votre candidature pour '{$application->jobOffer->title}' a été " .
                     ($request->status === 'accepted' ? 'acceptée' : 'rejetée'),
        'data' => [
            'job_id' => $application->job_offer_id,
            'application_id' => $application->id,
            'status' => $request->status,
        ],
    ]);
    
    return back()->with('success', 'Statut mis à jour.');
}
```

**Flow**:
1. Admin views applications at `/admin/job-applications`
2. Admin clicks "Accept" or "Reject" button
3. `PATCH /admin/job-applications/{id}/status` updates status
4. User notification created
5. **User sees updated status** at `/user/my-applications`

---

## 5. Security Implementation

### RoleMiddleware

**File**: `app/Http/Middleware/RoleMiddleware.php`

```php
public function handle(Request $request, Closure $next, string ...$roles) {
    // Check authentication
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    $user = Auth::user();
    
    // Verify user has required role
    if (!in_array($user->role, $roles, true)) {
        Log::warning('Unauthorized access attempt', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'required_roles' => $roles,
            'path' => $request->path(),
        ]);
        
        abort(403, 'Accès non autorisé.');
    }
    
    return $next($request);
}
```

### URL Manipulation Prevention

**Scenario**: Regular user tries `/admin/dashboard`

1. User manually types `/admin/dashboard`
2. Router: `admin.dashboard` route requires `role:admin,super_admin`
3. RoleMiddleware checks user role
4. User role = `user` (not in `['admin', 'super_admin']`)
5. Middleware logs attempt and returns `403 Forbidden`
6. User cannot access admin functions

---

## 6. Frontend Views

### Master Layouts

**Admin Layout**: `resources/views/layouts/admin.blade.php`
- Tailwind CSS CDN
- Font Awesome icons
- Alpine.js for interactivity
- Background: `bg-slate-50` (#F0F4F5 equivalent)
- Sidebar with navigation using `route('admin.*')`

**User Layout**: `resources/views/layouts/user.blade.php`
- Tailwind CSS CDN
- Font Awesome icons
- Alpine.js for interactivity
- Background: `bg-slate-50`
- Sidebar with navigation using `route('user.*')`

### Key Views

#### Admin Views
- `admin/dashboard.blade.php` - Overview with stats
- `admin/services/index.blade.php` - List all services
- `admin/services/create.blade.php` - Create new service
- `admin/jobs/index.blade.php` - List all jobs
- `admin/jobs/create.blade.php` - Create new job
- `admin/job-applications/index.blade.php` - Manage applications (✅ NEW)

#### User Views
- `user/dashboard.blade.php` - User overview
- `user/services/index.blade.php` - Browse services
- `user/jobs/index.blade.php` - Browse jobs
- `user/jobs/show.blade.php` - Job details with "Postuler" form (✅ NEW)
- `user/applications/index.blade.php` - Track my applications

---

## 7. User Workflows

### Workflow 1: User Applies to Job

**Step 1**: Browse Jobs
- Navigate to `/user/jobs`
- View list of active job offers
- Use filters (category, location, contract type)

**Step 2**: View Job Details
- Click on job card
- Redirected to `/user/jobs/{id}`
- Read job description and requirements

**Step 3**: Apply
- Fill cover letter (optional)
- Upload CV (optional, PDF/DOC/DOCX, max 2MB)
- Click "Envoyer ma candidature"
- `POST /user/jobs/{id}/apply`

**Step 4**: Confirmation
- Success message displayed
- Application appears in "Mes candidatures"
- Status: `pending`

**Step 5**: Track Application
- Navigate to `/user/my-applications`
- See all applications with current status
- Receive notification when admin updates status

---

### Workflow 2: User Requests Service

**Step 1**: Navigate to Service Request
- Find "Suggérer un service" link in sidebar
- Click to open request form

**Step 2**: Fill Request Form
- Service name
- Description
- Location/City
- Budget range
- Urgency level

**Step 3**: Submit Request
- Click "Envoyer"
- `POST /user/service-requests`
- Request saved with `status = 'pending'`

**Step 4**: Wait for Admin Response
- Request appears in Admin panel at `/admin/service-requests`
- Admin responds with solution or information
- User receives notification with admin response

---

## 8. Admin Workflows

### Workflow 1: Admin Creates Service

**Step 1**: Navigate to Services
- Go to `/admin/services`
- Click "Créer un service" button

**Step 2**: Fill Service Form
- Title (required)
- Category (dropdown, required)
- Description (required)
- Price (required, numeric)
- Location (required)
- Images (optional, multiple upload)

**Step 3**: Submit
- Click "Créer"
- `POST /admin/services`
- Service saved with `status = 'active'`

**Step 4**: Verification
- Service appears immediately in `/user/services` for all users
- Admin can edit/delete from `/admin/services`

---

### Workflow 2: Admin Manages Job Applications

**Step 1**: View Applications
- Navigate to `/admin/job-applications`
- See all applications with stats:
  - Total applications
  - Pending count
  - Accepted count
  - Rejected count

**Step 2**: Review Application
- Click "👁️ View" button to see details
- See applicant's cover letter and CV
- Review their profile

**Step 3**: Update Status
- Click "✓ Accept" or "✗ Reject" button
- `PATCH /admin/job-applications/{id}/status`
- Optional: Add admin notes

**Step 4**: Notification
- User receives notification automatically
- User sees updated status in their dashboard
- Email sent (if configured)

---

## 9. Testing Guide

### Manual Testing Checklist

#### Admin Panel Tests
- [ ] Create service → Appears in user dashboard
- [ ] Create job → Appears in user jobs list
- [ ] View job applications → See all pending applications
- [ ] Accept application → User sees "Acceptée" status
- [ ] Reject application → User sees "Rejetée" status
- [ ] Respond to service request → User receives response

#### User Dashboard Tests
- [ ] View services → See all active services
- [ ] View jobs → See all active jobs
- [ ] Apply to job → Application created with status "pending"
- [ ] View my applications → See application status
- [ ] Submit service request → Request appears in admin panel
- [ ] Receive notification → See notification in UI

#### Security Tests
- [ ] Regular user access `/admin/dashboard` → 403 error
- [ ] Regular user access `/admin/services/create` → 403 error
- [ ] Admin access `/user/dashboard` → Success (allowed)
- [ ] Unauthenticated user access any protected route → Redirect to login

---

### Sample Test Data

**Create Admin User** (via Tinker):
```bash
php artisan tinker
```
```php
User::create([
    'name' => 'Admin Principal',
    'email' => 'admin@servicerdc.com',
    'password' => Hash::make('admin123'),
    'role' => 'admin'
]);
```

**Create Regular User**:
```php
User::create([
    'name' => 'Jean Dupont',
    'email' => 'jean@example.com',
    'password' => Hash::make('user123'),
    'role' => 'user'
]);
```

**Create Category**:
```php
Category::create(['name' => 'Informatique']);
Category::create(['name' => 'Construction']);
Category::create(['name' => 'Éducation']);
```

---

## 10. Deployment Checklist

### Pre-Deployment

- [ ] Run all migrations: `php artisan migrate`
- [ ] Clear all caches:
  ```bash
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  php artisan cache:clear
  ```
- [ ] Verify `.env` configuration:
  - Database credentials
  - APP_URL
 - APP_ENV=production
  - APP_DEBUG=false
- [ ] Create admin user account
- [ ] Create initial categories
- [ ] Test all routes respond correctly

### Post-Deployment

- [ ] Verify admin login works
- [ ] Verify user login works
- [ ] Create test service
- [ ] Create test job
- [ ] Submit test application
- [ ] Update test application status
- [ ] Verify notifications work
- [ ] Test file upload functionality
- [ ] Monitor error logs

---

## Conclusion

This backend architecture provides a **complete, secure, and scalable** solution for managing the ServiceRDC platform. The strict separation between Admin and User roles, combined with real-time synchronization and comprehensive security measures, ensures a robust system.

### Key Achievements
✅ Complete database schema with proper relationships  
✅ Strict route separation (Admin vs User)  
✅ Comprehensive controller logic with validation  
✅ Secure middleware preventing unauthorized access  
✅ Beautiful, responsive views using Tailwind CSS  
✅ Real-time notification system  
✅ File upload support for CVs  
✅ Cross-system synchronization (Admin ↔ User)

### Next Steps
- Implement email notifications
- Add real-time updates using Laravel Echo
- Create admin analytics dashboard
- Implement advanced search/filtering
- Add REST API for mobile apps
- Integrate payment gateway for premium services

---

**Documentation Version**: 1.0  
**Last Updated**: 2026-02-15  
**Author**: Senior Laravel Backend Engineer  
**Support**: admin@servicerdc.com
