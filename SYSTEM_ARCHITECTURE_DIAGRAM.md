# ServiceRDC System Architecture Diagram

```
╔═══════════════════════════════════════════════════════════════════════════════╗
║                         SERVICERDC BACKEND SYSTEM                             ║
║                     Admin Panel & User Dashboard Sync                         ║
╚═══════════════════════════════════════════════════════════════════════════════╝

┌───────────────────────────────────────────────────────────────────────────────┐
│                            USER INTERFACE LAYER                               │
├───────────────────────────────┬───────────────────────────────────────────────┤
│         ADMIN PANEL           │          USER DASHBOARD                       │
│        (/admin/*)             │           (/user/*)                           │
├───────────────────────────────┼───────────────────────────────────────────────┤
│  • Dashboard Overview         │  • Dashboard Overview                         │
│  • Create/Edit Services       │  • Browse Services                            │
│  • Create/Edit Jobs           │  • Browse Jobs                                │
│  • Manage Applications        │  • Apply to Jobs ("Postuler")                 │
│  • Respond to Requests        │  • Submit Service Requests                    │
│  • User Management            │  • Track My Applications                      │
│  • Category Management        │  • View Notifications                         │
└───────────────────────────────┴───────────────────────────────────────────────┘
                                        │
                                        ▼
┌───────────────────────────────────────────────────────────────────────────────┐
│                          SECURITY LAYER (Middleware)                          │
├───────────────────────────────────────────────────────────────────────────────┤
│  RoleMiddleware: app/Http/Middleware/RoleMiddleware.php                       │
│                                                                               │
│  ┌─────────────────────────┐         ┌─────────────────────────┐             │
│  │   Admin Protection      │         │   User Protection       │             │
│  │   role:admin,super_admin│         │   role:user,admin,      │             │
│  │                         │         │        super_admin      │             │
│  └─────────────────────────┘         └─────────────────────────┘             │
│                                                                               │
│  Features:                                                                    │
│  ✓ Authentication check                                                       │
│  ✓ Role verification                                                          │
│  ✓ Unauthorized access logging                                                │
│  ✓ 403 response for blocked users                                             │
└───────────────────────────────────────────────────────────────────────────────┘
                                        │
                                        ▼
┌───────────────────────────────────────────────────────────────────────────────┐
│                        CONTROLLER LAYER (Business Logic)                      │
├───────────────────────────────┬───────────────────────────────────────────────┤
│    ADMIN CONTROLLERS          │        USER CONTROLLERS                       │
├───────────────────────────────┼───────────────────────────────────────────────┤
│  AdminDashboardController     │  UserDashboardController                      │
│  AdminServiceController       │  UserServiceController                        │
│  AdminJobController           │  UserJobController                            │
│  AdminJobApplicationController│  UserServiceRequestController                 │
│  AdminUserController          │                                               │
│  AdminCategoryController      │                                               │
└───────────────────────────────┴───────────────────────────────────────────────┘
                                        │
                                        ▼
┌───────────────────────────────────────────────────────────────────────────────┐
│                        MODEL LAYER (Eloquent ORM)                             │
├───────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│   ┌─────────────┐    ┌──────────────┐    ┌─────────────────┐                │
│   │   Service   │    │  JobOffer    │    │ JobApplication  │                │
│   ├─────────────┤    ├──────────────┤    ├─────────────────┤                │
│   │ - artisan_id│    │ - user_id    │    │ - job_offer_id  │                │
│   │ - category_id│   │ - employer_id│    │ - user_id       │                │
│   │ - title     │    │ - title      │    │ - status        │                │
│   │ - price     │    │ - salary     │    │ - resume_url    │                │
│   │ - location  │    │ - location   │    │ - cover_letter  │                │
│   │ - is_verified│   │ - status     │    │ - applied_at    │                │
│   └─────────────┘    └──────────────┘    └─────────────────┘                │
│                                                                               │
│   ┌──────────────────┐    ┌──────────┐    ┌──────────────┐                  │
│   │ ServiceRequest   │    │   User   │    │ Notification │                  │
│   ├──────────────────┤    ├──────────┤    ├──────────────┤                  │
│   │ - user_id        │    │ - name   │    │ - user_id    │                  │
│   │ - service_name   │    │ - email  │    │ - type       │                  │
│   │ - description    │    │ - role   │    │ - message    │                  │
│   │ - status         │    │ - password│   │ - data       │                  │
│   │ - admin_response │    └──────────┘    │ - read_at    │                  │
│   │ - responded_by   │                    └──────────────┘                  │
│   └──────────────────┘                                                        │
│                                                                               │
│  Relationships:                                                               │
│  • User (1) ──→ (many) Services                                               │
│  • User (1) ──→ (many) JobOffers                                              │
│  • User (1) ──→ (many) JobApplications                                        │
│  • JobOffer (1) ──→ (many) JobApplications                                    │
│  • Service (1) ──→ (many) ServiceRequests                                     │
│  • Category (1) ──→ (many) Services                                           │
└───────────────────────────────────────────────────────────────────────────────┘
                                        │
                                        ▼
┌───────────────────────────────────────────────────────────────────────────────┐
│                          DATABASE LAYER (MySQL)                               │
├───────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  Tables:                                                                      │
│  ┌───────────────┬───────────────┬──────────────────┬─────────────────┐      │
│  │   users       │   services    │   job_offers     │ job_applications│      │
│  │   categories  │   notifications│  service_requests│   sessions      │      │
│  │   migrations  │   cache       │   jobs           │                 │      │
│  └───────────────┴───────────────┴──────────────────┴─────────────────┘      │
│                                                                               │
│  Foreign Keys:                                                                │
│  • services.artisan_id → users.id                                             │
│  • services.category_id → categories.id                                       │
│  • job_offers.user_id → users.id                                              │
│  • job_applications.user_id → users.id                                        │
│  • job_applications.job_offer_id → job_offers.id (cascade delete)             │
│  • service_requests.user_id → users.id (set null on delete)                   │
│  • notifications.user_id → users.id                                           │
└───────────────────────────────────────────────────────────────────────────────┘


╔═══════════════════════════════════════════════════════════════════════════════╗
║                            CROSS-SYSTEM FLOWS                                 ║
╚═══════════════════════════════════════════════════════════════════════════════╝

┌───────────────────────────────────────────────────────────────────────────────┐
│  FLOW 1: Admin Creates Service → User Sees It                                │
├───────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  ┌──────────┐      ┌─────────────────┐      ┌──────────┐      ┌──────────┐  │
│  │  Admin   │─────>│ POST /admin/    │─────>│ Service  │─────>│  User    │  │
│  │  Panel   │      │ services/create │      │ Database │      │Dashboard │  │
│  └──────────┘      └─────────────────┘      └──────────┘      └──────────┘  │
│                                                                               │
│  Steps:                                                                       │
│  1. Admin fills form (Title, Category, Price, Location)                      │
│  2. AdminServiceController@store validates and saves                         │
│  3. Service saved with status='active'                                        │
│  4. UserServiceController@index fetches active services                       │
│  5. Service appears immediately in user dashboard                             │
└───────────────────────────────────────────────────────────────────────────────┘

┌───────────────────────────────────────────────────────────────────────────────┐
│  FLOW 2: User Applies to Job → Admin Sees Application                        │
├───────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  ┌──────────┐      ┌─────────────────┐      ┌──────────┐      ┌──────────┐  │
│  │   User   │─────>│ POST /user/jobs/│─────>│JobApplic'│─────>│  Admin   │  │
│  │Dashboard │      │    {id}/apply   │      │ Database │      │  Panel   │  │
│  └──────────┘      └─────────────────┘      └──────────┘      └──────────┘  │
│                          │                                          │         │
│                          └──────────────────────────────────────────┘         │
│                              Notification Created for Admin                   │
│                                                                               │
│  Steps:                                                                       │
│  1. User fills cover letter and uploads CV                                   │
│  2. UserJobController@apply creates JobApplication                           │
│  3. Application saved with status='pending'                                   │
│  4. Notification created for job owner                                        │
│  5. Admin sees application in /admin/job-applications                         │
└───────────────────────────────────────────────────────────────────────────────┘

┌───────────────────────────────────────────────────────────────────────────────┐
│  FLOW 3: Admin Updates Status → User Notified                                │
├───────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  ┌──────────┐      ┌─────────────────┐      ┌──────────┐      ┌──────────┐  │
│  │  Admin   │─────>│ PATCH /admin/   │─────>│ Update   │─────>│   User   │  │
│  │  Panel   │      │ job-applications│      │ Database │      │Notified  │  │
│  └──────────┘      │ /{id}/status    │      └──────────┘      └──────────┘  │
│                    └─────────────────┘            │                          │
│                                                   │                          │
│                                         ┌─────────▼─────────┐                │
│                                         │ Notification      │                │
│                                         │ Created for User  │                │
│                                         └───────────────────┘                │
│                                                                               │
│  Steps:                                                                       │
│  1. Admin clicks "Accept" or "Reject" button                                 │
│  2. AdminJobApplicationController@updateStatus updates status                │
│  3. Application status changed to 'accepted' or 'rejected'                    │
│  4. Notification created for user                                             │
│  5. User sees updated status in /user/my-applications                         │
└───────────────────────────────────────────────────────────────────────────────┘


╔═══════════════════════════════════════════════════════════════════════════════╗
║                           SECURITY ARCHITECTURE                               ║
╚═══════════════════════════════════════════════════════════════════════════════╝

┌───────────────────────────────────────────────────────────────────────────────┐
│  URL Manipulation Prevention Example                                          │
├───────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  Scenario: Regular user tries to access /admin/dashboard                      │
│                                                                               │
│  1. User (role: 'user') types: http://localhost:8000/admin/dashboard         │
│                                                                               │
│  2. Laravel Router matches route: 'admin.dashboard'                           │
│     Route middleware: ['auth', 'role:admin,super_admin']                      │
│                                                                               │
│  3. RoleMiddleware::handle() executes:                                        │
│     ┌──────────────────────────────────────────────────┐                     │
│     │ if (!in_array($user->role, ['admin','super_admin'])) │                 │
│     │     Log::warning('Unauthorized access')          │                     │
│     │     abort(403, 'Accès non autorisé')             │                     │
│     └──────────────────────────────────────────────────┘                     │
│                                                                               │
│  4. Log entry created:                                                        │
│     [WARNING] Unauthorized access attempt                                     │
│     User ID: 5 | Role: user | Path: admin/dashboard                          │
│     Required Roles: ['admin', 'super_admin']                                  │
│     IP: 127.0.0.1                                                             │
│                                                                               │
│  5. User sees: 403 FORBIDDEN - Accès non autorisé                             │
│                                                                               │
│  Result: ✅ Security breach prevented                                         │
└───────────────────────────────────────────────────────────────────────────────┘


╔═══════════════════════════════════════════════════════════════════════════════╗
║                         FILE STRUCTURE OVERVIEW                               ║
╚═══════════════════════════════════════════════════════════════════════════════╝

c:\xampp\htdocs\rdc\
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ServiceController.php
│   │   │   │   ├── JobController.php
│   │   │   │   ├── JobApplicationController.php ✨ ENHANCED
│   │   │   │   ├── UserController.php
│   │   │   │   └── CategoryController.php
│   │   │   └── User/
│   │   │       ├── DashboardController.php
│   │   │       ├── ServiceController.php
│   │   │       ├── JobController.php
│   │   │       └── ServiceRequestController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php ✓ VERIFIED
│   │
│   └── Models/
│       ├── Service.php
│       ├── JobOffer.php
│       ├── JobApplication.php
│       ├── ServiceRequest.php
│       ├── User.php
│       ├── Category.php
│       └── Notification.php
│
├── database/
│   └── migrations/
│       ├── create_services_table.php
│       ├── create_job_offers_table.php
│       ├── create_job_applications_table.php
│       ├── create_service_requests_table.php
│       └── (25 total migrations)
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── admin.blade.php
│       │   └── user.blade.php
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── services/
│       │   ├── jobs/
│       │   └── job-applications/
│       │       └── index.blade.php ✨ NEW
│       └── user/
│           ├── dashboard.blade.php
│           ├── services/
│           ├── jobs/
│           │   ├── index.blade.php
│           │   └── show.blade.php ✨ NEW
│           └── applications/
│               └── index.blade.php
│
├── routes/
│   └── web.php ✓ VERIFIED
│
└── Documentation/
    ├── BACKEND_ARCHITECTURE_IMPLEMENTATION.md ✨ NEW
    ├── DELIVERABLES_SUMMARY.md ✨ NEW
    ├── COMPLETE_BACKEND_GUIDE.md ✨ NEW
    ├── QUICK_REFERENCE.md ✨ NEW
    ├── BACKEND_README.md ✨ NEW
    ├── IMPLEMENTATION_SUMMARY.md ✨ NEW
    └── SYSTEM_ARCHITECTURE_DIAGRAM.md ✨ THIS FILE


╔═══════════════════════════════════════════════════════════════════════════════╗
║                            TECHNOLOGY STACK                                   ║
╚═══════════════════════════════════════════════════════════════════════════════╝

Backend:
  • Laravel 12 (PHP Framework)
  • PHP 8.2+
  • MySQL 8.0+
  • Eloquent ORM

Frontend:
  • Tailwind CSS (Play CDN)
  • Alpine.js (Interactivity)
  • Font Awesome 6 (Icons)
  • Google Fonts (Inter, Plus Jakarta Sans)

Security:
  • Laravel Sanctum
  • RoleMiddleware
  • CSRF Protection
  • Password Hashing (Bcrypt)

Development:
  • Composer (Dependency Management)
  • Artisan CLI
  • PSR-12 Coding Standards


╔═══════════════════════════════════════════════════════════════════════════════╗
║                              STATUS SUMMARY                                   ║
╚═══════════════════════════════════════════════════════════════════════════════╝

✅ Database Schema: COMPLETE
✅ Routing Architecture: COMPLETE
✅ Controller Logic: COMPLETE
✅ Security Implementation: COMPLETE
✅ Frontend Views: COMPLETE
✅ Cross-System Sync: COMPLETE
✅ Documentation: COMPLETE

Status: 🎉 PRODUCTION READY 🎉

Version: 1.0.0
Date: February 15, 2026
Engineer: Senior Laravel Backend Developer
```
