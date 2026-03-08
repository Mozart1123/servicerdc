# 🔧 GUIDE TECHNIQUE - ARCHITECTURE SERVICERDC

## 📚 Sommaire Technique

Ce document décrit l'architecture complète du système dashboard ServiceRDC pour les développeurs.

---

## 🏗️ Architecture Générale

```
┌─────────────────────────────────────────────────────────┐
│                 SERVICERDC PLATFORM                      │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌─────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │   Routes    │  │ Controllers  │  │   Models     │ │
│  │   (25+)     │  │   (4 types)  │  │  (9 total)   │ │
│  └─────────────┘  └──────────────┘  └──────────────┘ │
│         ↓               ↓                   ↓         │
│  /user/dashboard   DashboardCtr      User.php        │
│  /user/services    ServiceCtr        Service.php     │
│  /user/jobs        JobCtr            JobOffer.php    │
│  /user/missions    ServiceReqCtr     Mission.php     │
│                                      Notification.php │
│  ┌──────────────────────────────────────────────┐   │
│  │         Views (Blade Templates)              │   │
│  ├──────────────────────────────────────────────┤   │
│  │ • dashboard.blade.php (Main layout)          │   │
│  │ • partials/* (5 sections)                    │   │
│  │ • service-requests/* (NEW)                   │   │
│  └──────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────┐   │
│  │       Database (MySQL 5.7+)                  │   │
│  ├──────────────────────────────────────────────┤   │
│  │ • 9 tables principales                       │   │
│  │ • 8 migrations exécutées                     │   │
│  │ • Relationships & Scopes optimisés           │   │
│  └──────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

---

## 🗂️ Structure de Fichiers

```
servicerdc/
├── app/
│   ├── Models/
│   │   ├── User.php                    [Enhanced] 8 relations
│   │   ├── Service.php                 [Enhanced] 5 scopes
│   │   ├── JobOffer.php                [Enhanced] 5 scopes
│   │   ├── JobApplication.php          [Enhanced] 3 scopes
│   │   ├── Mission.php                 [NEW]      5 scopes
│   │   ├── Notification.php            [NEW]      3 scopes
│   │   ├── ServiceRequest.php          [ENHANCED] 8 scopes ← KEY
│   │   ├── Category.php
│   │   ├── Report.php
│   │   └── Setting.php
│   │
│   └── Http/Controllers/
│       ├── User/
│       │   ├── DashboardController.php [13 méthodes]
│       │   ├── ServiceController.php   [8 méthodes]
│       │   ├── JobController.php       [5 méthodes]
│       │   └── ServiceRequestController.php [3 méthodes] ← NEW
│       ├── Admin/
│       │   └── ... (gestion admin)
│       └── SuperAdmin/
│           └── ... (gestion super admin)
│
├── database/
│   ├── migrations/
│   │   ├── 0001_*                      [Base tables]
│   │   ├── 2026_01_10_*                [Initial setup]
│   │   ├── 2026_01_12_000001-000006   [Enhancements]
│   │   ├── 2026_01_13_000001           [Services fix]
│   │   └── 2026_01_13_000002           [ServiceRequest ← NEW]
│   └── seeders/
│       └── ... (test data)
│
├── resources/
│   └── views/
│       └── user/
│           ├── dashboard.blade.php      [Main layout]
│           ├── partials/
│           │   ├── overview.blade.php
│           │   ├── services.blade.php
│           │   ├── jobs.blade.php
│           │   ├── applications.blade.php
│           │   ├── missions.blade.php
│           │   └── service-requests.blade.php ← NEW
│           ├── services/
│           │   └── index.blade.php
│           ├── jobs/
│           │   └── index.blade.php
│           └── service-requests/       ← NEW DIRECTORY
│               ├── index.blade.php
│               └── show.blade.php
│
├── routes/
│   └── web.php                         [25+ routes]
│
└── public/
    └── ... (assets)
```

---

## 🔌 Flux de Données

### Flux 1: Soumettre une Demande de Service Personnalisée

```
User (Frontend)
    ↓ Remplit form + Click "Envoyer"
    ↓ Alpine.js: @submit.prevent="submitForm()"
    ↓
POST /user/service-requests (AJAX + JSON)
    ↓ Validation ServerSide
    ↓
ServiceRequestController@store()
    ├── Valide données
    ├── Crée ServiceRequest record
    ├── Notifie tous les admins
    │   └── Notification::create() pour chaque admin
    └── Retourne JSON success
    ↓
Frontend reçoit réponse
    ├── Si succès: Message vert + Refresh page
    └── Si erreur: Message rouge + Details erreur
```

### Flux 2: Affichage du Dashboard

```
User accède /user/dashboard
    ↓
Routes (web.php) → DashboardController@index()
    ↓
DashboardController résout:
    ├── $stats (5 métriques)
    ├── $recentJobs (6 derniers)
    ├── $allJobs (tous actifs)
    ├── $recentServices (6 derniers)
    ├── $categories (toutes)
    ├── $myApplications (mes candidatures)
    └── $notifications (5 dernières)
    ↓
return view('user.dashboard', compact(...))
    ↓
Blade template → HTML + Alpine.js
    ├── Affiche statistiques
    ├── Init tabs (Alpine.js x-data)
    └── Contenu caché jusqu'à click onglet
    ↓
HTML envoyé au navigateur
    ↓
Alpine.js active interactivité
    ├── @click="activeTab = '...'"
    ├── x-show conditionals
    └── Animations transitions
```

### Flux 3: Filtrer Services

```
User remplit filtres + Click "Appliquer"
    ↓
GET /user/services?category=2&location=Kinshasa&search=climatisation
    ↓
DashboardController@services(Request)
    ├── Récupère paramètres
    ├── Construit query:
    │   $query = Service::active()->verified()
    │   if ($category) $query->byCategory($category)
    │   if ($location) $query->byLocation($location)
    │   if ($search) $query->search($search)
    ├── Applique paginate(12)
    └── Retourne vue avec résultats
    ↓
Affiche 12 services avec pagination
```

---

## 💾 Schéma Base de Données

### Table: `service_requests` (ENRICHIE)

```sql
CREATE TABLE service_requests (
    id                      BIGINT UNSIGNED PRIMARY KEY,
    
    -- User relationship
    user_id                BIGINT UNSIGNED,
    FOREIGN KEY(user_id)   REFERENCES users(id) ON DELETE SET NULL,
    
    -- Contact info
    phone                  VARCHAR(20) NULLABLE,
    email                  VARCHAR(255) NULLABLE,
    
    -- Service requested
    requested_service_name VARCHAR(255) NOT NULL,
    category_needed        VARCHAR(100) NULLABLE,
    description            TEXT NULLABLE,
    city                   VARCHAR(100) NULLABLE,
    location               VARCHAR(255) NULLABLE,
    
    -- Budget
    budget_min            DECIMAL(10,2) NULLABLE,
    budget_max            DECIMAL(10,2) NULLABLE,
    
    -- Urgency level
    urgency               ENUM('low','medium','high','urgent') DEFAULT 'medium',
    
    -- Status workflow
    status                ENUM('pending','addressed') DEFAULT 'pending',
    
    -- Admin response
    response              TEXT NULLABLE,
    responded_by          BIGINT UNSIGNED NULLABLE,
    FOREIGN KEY(responded_by) REFERENCES users(id) ON DELETE SET NULL,
    responded_at          TIMESTAMP NULLABLE,
    
    -- Additional
    notes                 TEXT NULLABLE,
    created_at            TIMESTAMP,
    updated_at            TIMESTAMP,
    
    -- Indexes
    INDEX(user_id),
    INDEX(status),
    INDEX(urgency),
    INDEX(created_at)
);
```

### Table: `missions`

```sql
CREATE TABLE missions (
    id                BIGINT PRIMARY KEY,
    service_id        BIGINT,      -- Service concernée
    client_id         BIGINT,      -- Utilisateur client
    artisan_id        BIGINT,      -- Utilisateur artisan
    title             VARCHAR(255),
    description       TEXT,
    status            ENUM('pending','in_progress','completed','cancelled'),
    amount            DECIMAL(10,2),
    rating            INTEGER NULLABLE (1-5),
    feedback          TEXT,
    created_at        TIMESTAMP,
    updated_at        TIMESTAMP
);
```

### Table: `notifications`

```sql
CREATE TABLE notifications (
    id              BIGINT PRIMARY KEY,
    user_id         BIGINT,        -- Destinataire
    type            VARCHAR(100),  -- 'custom_service_request', 'job_application', etc
    title           VARCHAR(255),
    message         TEXT,
    data            JSON,          -- Données additionnelles
    is_read         BOOLEAN DEFAULT FALSE,
    read_at         TIMESTAMP NULLABLE,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP
);
```

---

## 📋 Models & Relationships

### User Model

```php
class User extends Authenticatable {
    // ✨ Services offerts par artisan
    public function services()              // ONE-TO-MANY
    
    // 📋 Candidatures envoyées
    public function jobApplications()       // ONE-TO-MANY
    
    // 💼 Offres d'emploi créées (admin)
    public function jobOffers()             // ONE-TO-MANY
    
    // 🛠️ Missions comme client
    public function missionsAsClient()      // ONE-TO-MANY (client_id)
    
    // 🛠️ Missions comme artisan
    public function missionsAsArtisan()     // ONE-TO-MANY (artisan_id)
    
    // 🔔 Notifications
    public function notifications()         // ONE-TO-MANY
    
    // 📝 Demandes de services personnalisés ← NEW
    public function serviceRequests()       // ONE-TO-MANY
}
```

### ServiceRequest Model (ENRICHI)

```php
class ServiceRequest extends Model {
    // Relationships
    public function user()                  // Demandeur
    public function service()               // Service lié (nullable)
    public function respondedByUser()       // Admin qui a répondu ← NEW
    
    // Scopes (Filtrage)
    public function scopePending()          // WHERE status = 'pending'
    public function scopeAddressed()        // WHERE status = 'addressed'
    public function scopeByStatus($status)  // WHERE status = ?
    public function scopeByUrgency()        // WHERE urgency = ?
    public function scopeByCity($city)      // WHERE city LIKE ?
    public function scopeByCategory()       // WHERE category_needed LIKE ?
    public function scopeSearch($term)      // WHERE name/desc LIKE ?
    public function scopeUnresponded()      // WHERE responded_at IS NULL
    
    // Accessors (Affichage)
    public function getStatusLabelAttribute()  // 'pending' → 'En attente'
    public function getUrgencyLabelAttribute() // 'high' → 'Élevée'
    public function getBudgetRangeAttribute()  // Formatage budget
}
```

---

## 🎯 Controllers - Méthodes Clés

### DashboardController (13 méthodes)

```php
class DashboardController extends Controller {
    // Main
    public function index(): View                     // Stats + recent items
    public function profile(): View                   // User profile view
    public function updateProfile(Request): Redirect  // Update user info
    
    // Services
    public function services(Request): View           // List with filters
    public function serviceDetail(int): View          // Single service
    
    // Jobs
    public function jobs(Request): View               // Job listings
    public function jobDetail(int): View              // Job detail
    public function applyToJob(Request, int): Redirect // Apply + notify
    public function myApplications(): View            // Track applications
    
    // Missions
    public function missions(Request): View           // List user missions
    public function missionDetail(int): View          // Mission detail
    public function updateMissionStatus(Request): Redirect // Change status
    
    // Notifications
    public function notifications(): View             // Show notifications
    public function markNotificationAsRead(int): Redirect // Mark read
}
```

### ServiceRequestController (3 méthodes) ← NEW

```php
class ServiceRequestController extends Controller {
    // Soumettre demande (AJAX)
    public function store(Request $request): JsonResponse {
        // Valider
        // Créer ServiceRequest
        // Notifier admins
        // Retourner JSON
    }
    
    // Lister ses demandes
    public function index(): View {
        // Récupérer demandes utilisateur
        // Calculer stats
        // Retourner vue
    }
    
    // Voir détails demande
    public function show(ServiceRequest $serviceRequest): View {
        // Vérifier autorisation
        // Retourner détail avec réponse admin
    }
}
```

---

## 🛣️ Routes Essentielles

```php
// Routes utilisateur (25+)
Route::middleware(['auth', 'role:user,admin,super_admin'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', 'DashboardController@index')
            ->name('dashboard');
        
        // Service Requests ← NEW
        Route::get('/service-requests', 'ServiceRequestController@index')
            ->name('service-requests.index');
        Route::get('/service-requests/{serviceRequest}', 'ServiceRequestController@show')
            ->name('service-requests.show');
        Route::post('/service-requests', 'ServiceRequestController@store')
            ->name('service-requests.store');
        
        // Services, Jobs, Missions, Notifications...
    });
```

---

## 📊 Scopes & Query Building

### Exemple: Filtrer Services

```php
// Frontend: /user/services?category=5&location=Kinshasa&search=clim

// Backend
$query = Service::active()                    // Scope: status = 'active'
    ->verified();                             // Scope: is_verified = true

if ($request->category) {
    $query->byCategory($request->category);   // Scope: category_id = ?
}

if ($request->location) {
    $query->byLocation($request->location);   // Scope: location LIKE ?
}

if ($request->search) {
    $query->search($request->search);         // Scope: title/description LIKE ?
}

$services = $query->with('category', 'artisan')  // Eager load
                 ->paginate(12);                 // 12 per page

// Résultat: Requête SQL optimisée avec eager loading
```

### Exemple: Demandes non répondues

```php
// Obtenir demandes en attente pour les admins
$pendingRequests = ServiceRequest::pending()      // status = 'pending'
    ->unresponded()                               // responded_at IS NULL
    ->byUrgency('high')                           // urgency = 'high'
    ->with('user')                                // Eager load user
    ->latest()
    ->paginate(10);

// Utiliser
@foreach($pendingRequests as $request)
    <div>
        <h3>{{ $request->requested_service_name }}</h3>
        <p>Urgence: {{ $request->urgency_label }}</p>
        <p>Budget: {{ $request->budget_range }}</p>
    </div>
@endforeach
```

---

## 🎨 Frontend - Alpine.js Integration

### Dashboard Tabs

```javascript
// resources/views/user/dashboard.blade.php
<div x-data="dashboardTabs()">
    <!-- Boutons onglets -->
    <button @click="activeTab = 'overview'"
        :class="activeTab === 'overview' ? 'active' : ''">
        📊 Aperçu
    </button>
    
    <!-- Contenu onglets -->
    <div x-show="activeTab === 'overview'">
        @include('user.partials.overview')
    </div>
    
    <div x-show="activeTab === 'demandes'">
        @include('user.partials.service-requests')
    </div>
</div>

<script>
function dashboardTabs() {
    return {
        activeTab: 'overview',
        init() {
            // Load from URL if present
            const tab = new URLSearchParams(location.search).get('tab');
            if (tab) this.activeTab = tab;
        }
    };
}
</script>
```

### Service Request Form

```javascript
// resources/views/user/partials/service-requests.blade.php
<div x-data="customServiceRequestForm()">
    <form @submit.prevent="submitForm()">
        <input x-model="form.requested_service_name">
        <input x-model="form.phone">
        <textarea x-model="form.description"></textarea>
        
        <button :disabled="loading">
            <span x-show="!loading">✈️ Envoyer</span>
            <span x-show="loading">Envoi...</span>
        </button>
    </form>
</div>

<script>
function customServiceRequestForm() {
    return {
        loading: false,
        error: '',
        form: { /* ... */ },
        async submitForm() {
            this.loading = true;
            const response = await fetch(route('user.service-requests.store'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.form)
            });
            
            if (response.ok) {
                this.success = true;
                setTimeout(() => location.reload(), 2000);
            } else {
                this.error = 'Erreur lors de la soumission';
            }
            this.loading = false;
        }
    };
}
</script>
```

---

## 🧪 Testing

### Test Routes
```bash
# Routes du dashboard
GET /user/dashboard
GET /user/services?category=2
GET /user/jobs
GET /user/missions
GET /user/service-requests
GET /user/notifications
```

### Test Models
```php
// Vérifier scopes
$pending = ServiceRequest::pending()->count();
$urgent = ServiceRequest::byUrgency('urgent')->count();
$kinshasa = ServiceRequest::byCity('Kinshasa')->count();

// Vérifier relations
$user = User::with('serviceRequests')->find(1);
$requests = $user->serviceRequests;
$label = $requests[0]->urgency_label;
```

### Test Controller
```php
// Dans Laravel Tinker
$user = User::find(1);
Auth::login($user);

// Appeler controller
$controller = new DashboardController();
$view = $controller->index();

// Vérifier variables
dd($view->gatherData());
```

---

## 🚀 Déploiement Checklist

- [ ] Migrations exécutées: `php artisan migrate`
- [ ] Cache clear: `php artisan cache:clear`
- [ ] Routes cache: `php artisan route:cache`
- [ ] Config cache: `php artisan config:cache`
- [ ] Database backup: Avant migration
- [ ] Test routes: Vérifier toutes les routes
- [ ] Test notifications: Vérifier création notifications
- [ ] Test formulaire: Soumettre demande test
- [ ] Vérifier permissions: RBAC en place

---

## 📈 Performance Considerations

### Eager Loading
```php
// ✅ BON - Évite N+1 queries
$jobs = JobOffer::with('user', 'applications')->get();

// ❌ MAUVAIS - N+1 queries
$jobs = JobOffer::all();
foreach ($jobs as $job) {
    $user = $job->user; // Query par job
}
```

### Pagination
```php
// ✅ BON - Limite résultats
$services = Service::paginate(12);

// ❌ MAUVAIS - Charge tout
$services = Service::get();
```

### Indexing
```php
// Indexes sur colonnes fréquemment filtrées
service_requests:
  - INDEX(user_id)
  - INDEX(status)
  - INDEX(created_at)
  - INDEX(urgency)
```

---

## 🔍 Debugging

### Utiliser Log
```php
Log::info('Service request submitted', [
    'user_id' => Auth::id(),
    'service' => $request->requested_service_name,
    'urgency' => $request->urgency,
]);
```

### Utiliser Tinker
```bash
php artisan tinker

> ServiceRequest::latest()->first()
> User::find(1)->serviceRequests
> Notification::where('type', 'custom_service_request')->get()
```

### Utiliser Laravel Debugbar
```
composer require barryvdh/laravel-debugbar --dev
php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider"
```

---

## 📚 References

- [Laravel Docs](https://laravel.com/docs)
- [Eloquent Relations](https://laravel.com/docs/eloquent-relationships)
- [Alpine.js Docs](https://alpinejs.dev)
- [Tailwind CSS](https://tailwindcss.com)

---

**Version**: 1.0  
**Framework**: Laravel 12.46.0  
**PHP**: 8.2.12  
**Database**: MySQL 5.7+  
**Frontend**: Blade + Alpine.js + Tailwind CSS  

**Status**: ✅ Production Ready
