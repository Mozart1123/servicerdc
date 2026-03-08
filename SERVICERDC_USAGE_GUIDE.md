# 🎯 GUIDE D'UTILISATION - DASHBOARD SERVICERDC

## 📝 TABLE DES MATIÈRES
1. [Installation & Configuration](#-installation--configuration)
2. [Structures de Données](#-structures-de-données)
3. [Utilisation des Modèles](#-utilisation-des-modèles)
4. [Exemples de Code](#-exemples-de-code)
5. [Création des Vues](#-création-des-vues)

---

## 🔧 Installation & Configuration

### 1. Exécuter les migrations
```bash
cd c:\xampp\htdocs\rdc\rdc
php artisan migrate
```

### 2. Vérifier les routes
```bash
php artisan route:list | findstr user
```

### 3. Lancer le serveur
```bash
php artisan serve
```

---

## 📊 Structures de Données

### Table `services`
```sql
CREATE TABLE services (
    id BIGINT PRIMARY KEY,
    artisan_id BIGINT NOT NULL,           -- FK users
    category_id BIGINT,                    -- FK categories
    title VARCHAR(255),
    description TEXT,
    price DECIMAL(10,2),
    location VARCHAR(255),
    images JSON,                           -- ["path1.jpg", "path2.jpg"]
    is_verified BOOLEAN DEFAULT false,
    status ENUM('active', 'inactive'),
    rating DECIMAL(3,2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Table `missions`
```sql
CREATE TABLE missions (
    id BIGINT PRIMARY KEY,
    service_id BIGINT,                     -- FK services
    client_id BIGINT NOT NULL,             -- FK users (client)
    artisan_id BIGINT NOT NULL,            -- FK users (artisan)
    title VARCHAR(255),
    description TEXT,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled'),
    start_date DATE,
    end_date DATE,
    amount DECIMAL(10,2),
    rating INTEGER,                        -- 1-5
    feedback TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Table `notifications`
```sql
CREATE TABLE notifications (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL,               -- FK users
    type VARCHAR(255),                     -- 'new_job_application', 'mission_assigned'
    title VARCHAR(255),
    message TEXT,
    data JSON,                             -- {"job_id": 1, "applicant_id": 5}
    is_read BOOLEAN DEFAULT false,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 🖥️ Utilisation des Modèles

### Récupérer les Services d'un Artisan
```php
<?php
use App\Models\Service;

// Tous les services de l'artisan
$services = auth()->user()->services()->get();

// Services actifs et vérifiés
$activeServices = auth()->user()->services()
    ->active()
    ->verified()
    ->get();

// Avec catégorie
$servicesWithCategory = auth()->user()->services()
    ->with('category')
    ->get();
```

### Rechercher des Services
```php
<?php
use App\Models\Service;

// Par titre/description
$results = Service::active()
    ->verified()
    ->search('plomberie')
    ->get();

// Par catégorie
$categoryServices = Service::active()
    ->byCategory(1)
    ->get();

// Par localisation
$localServices = Service::active()
    ->byLocation('Kinshasa')
    ->get();

// Combiné
$filtered = Service::active()
    ->verified()
    ->byCategory(2)
    ->byLocation('Kinshasa')
    ->search('électrique')
    ->with('artisan')
    ->paginate(12);
```

### Gérer les Emplois
```php
<?php
use App\Models\JobOffer;

// Offres actives et non expirées
$activeJobs = JobOffer::active()
    ->notExpired()
    ->get();

// Filtrer par type de contrat
$cdis = JobOffer::active()
    ->byContractType('CDI')
    ->get();

// Rechercher
$searchResults = JobOffer::active()
    ->search('développeur')
    ->byLocation('Kinshasa')
    ->get();

// Avec candidatures
$jobWithApplications = JobOffer::with('applications')
    ->findOrFail(1);

echo $jobWithApplications->applications->count(); // Nombre de candidatures
```

### Gérer les Candidatures
```php
<?php
use App\Models\JobApplication;

// Mes candidatures en attente
$pending = auth()->user()->jobApplications()
    ->pending()
    ->get();

// Acceptées
$accepted = auth()->user()->jobApplications()
    ->accepted()
    ->with('jobOffer')
    ->get();

// Avec détails
foreach ($accepted as $app) {
    echo $app->user->name;           // Nom du candidat
    echo $app->jobOffer->title;      // Titre du poste
    echo $app->status_label;          // "Accepté"
}
```

### Gérer les Missions
```php
<?php
use App\Models\Mission;

// Mes missions en tant que client
$myOrders = auth()->user()->missionsAsClient()
    ->inProgress()
    ->with('artisan', 'service')
    ->get();

// Mes missions en tant qu'artisan
$myWork = auth()->user()->missionsAsArtisan()
    ->inProgress()
    ->get();

// Compléter une mission
$mission = Mission::findOrFail(1);
$mission->update([
    'status' => 'completed',
    'rating' => 5,
    'feedback' => 'Excellent travail !',
]);
```

### Gérer les Notifications
```php
<?php
use App\Models\Notification;

// Notifications non lues
$unread = auth()->user()->notifications()
    ->unread()
    ->latest()
    ->get();

// Créer une notification
Notification::create([
    'user_id' => 1,
    'type' => 'new_job_application',
    'title' => 'Nouvelle candidature',
    'message' => 'Jean a postulé à votre offre',
    'data' => [
        'job_id' => 5,
        'applicant_id' => 10,
        'application_id' => 25,
    ],
]);

// Marquer comme lue
$notification = Notification::find(1);
$notification->markAsRead();

// Obtenir non lues
$count = auth()->user()->notifications()->unread()->count();
```

---

## 💻 Exemples de Code

### 1. Afficher un Service et ses Détails
```php
<?php
// Dans le contrôleur
public function show($id)
{
    $service = Service::with('category', 'artisan')
        ->findOrFail($id);
    
    $relatedServices = Service::active()
        ->verified()
        ->where('category_id', $service->category_id)
        ->where('id', '!=', $id)
        ->take(4)
        ->get();
    
    return view('user.services.show', [
        'service' => $service,
        'relatedServices' => $relatedServices,
    ]);
}
```

### 2. Créer un Service
```php
<?php
public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'description' => 'required|string|min:50',
        'price' => 'required|numeric|min:0',
        'location' => 'required|string',
        'images' => 'array|max:5',
        'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $images = [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('services', 'public');
            $images[] = $path;
        }
    }

    auth()->user()->services()->create([
        'category_id' => $request->category_id,
        'title' => $request->title,
        'description' => $request->description,
        'price' => $request->price,
        'location' => $request->location,
        'images' => $images,
        'status' => 'active',
        'is_verified' => false,
    ]);

    return redirect()->route('user.services.my')
        ->with('success', 'Service créé avec succès');
}
```

### 3. Postuler à un Emploi
```php
<?php
public function apply(Request $request, JobOffer $job)
{
    $request->validate([
        'cover_letter' => 'nullable|string|max:1000',
        'resume_url' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
    ]);

    // Vérifier si déjà candidat
    $exists = JobApplication::where('job_offer_id', $job->id)
        ->where('user_id', auth()->id())
        ->exists();

    if ($exists) {
        return back()->with('error', 'Déjà candidat');
    }

    // Upload CV si fourni
    $resumePath = null;
    if ($request->hasFile('resume_url')) {
        $resumePath = $request->file('resume_url')
            ->store('resumes', 'public');
    }

    // Créer candidature
    $application = JobApplication::create([
        'job_offer_id' => $job->id,
        'user_id' => auth()->id(),
        'cover_letter' => $request->cover_letter,
        'resume_url' => $resumePath,
        'status' => 'pending',
        'applied_at' => now(),
    ]);

    // Notifier l'admin
    Notification::create([
        'user_id' => $job->employer_id ?? 1,
        'type' => 'new_job_application',
        'title' => 'Nouvelle candidature',
        'message' => auth()->user()->name . ' a postulé',
        'data' => ['job_id' => $job->id, 'application_id' => $application->id],
    ]);

    return back()->with('success', 'Candidature envoyée');
}
```

### 4. Dashboard avec Statistiques
```php
<?php
public function dashboard()
{
    $user = auth()->user();
    
    $stats = [
        'applied_jobs' => $user->jobApplications()->count(),
        'active_missions' => $user->missionsAsArtisan()
            ->inProgress()->count() + 
            $user->missionsAsClient()->inProgress()->count(),
        'completed_missions' => $user->missionsAsArtisan()
            ->completed()->count(),
        'unread_notifications' => $user->notifications()
            ->unread()->count(),
    ];

    $recentApplications = $user->jobApplications()
        ->with('jobOffer')
        ->latest()
        ->take(5)
        ->get();

    $activeMissions = $user->missionsAsArtisan()
        ->inProgress()
        ->with('client', 'service')
        ->get();

    return view('user.dashboard', compact(
        'stats',
        'recentApplications',
        'activeMissions'
    ));
}
```

---

## 📄 Création des Vues

### Service Index (`services/index.blade.php`)
```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Services Disponibles</h1>
    </div>

    <!-- Filtres -->
    <form class="mb-8 bg-white p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium">Rechercher</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium">Catégorie</label>
                <select name="category" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Toutes</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" 
                            @selected(request('category') == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Localisation</label>
                <input type="text" name="location" value="{{ request('location') }}" 
                    class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>
        <button type="submit" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg">
            Rechercher
        </button>
    </form>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($services as $service)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                @if($service->images && count($service->images) > 0)
                    <img src="{{ asset('storage/' . $service->images[0]) }}" 
                        alt="{{ $service->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">Pas d'image</span>
                    </div>
                @endif

                <div class="p-4">
                    <h3 class="font-bold text-lg mb-2">{{ $service->title }}</h3>
                    <p class="text-gray-600 text-sm mb-2">{{ Str::limit($service->description, 80) }}</p>
                    
                    <div class="flex justify-between items-center mb-4">
                        <span class="font-bold text-lg">{{ number_format($service->price, 0) }} FC</span>
                        @if($service->rating > 0)
                            <span class="text-yellow-500">
                                ⭐ {{ number_format($service->rating, 1) }}
                            </span>
                        @endif
                    </div>

                    <p class="text-gray-500 text-xs mb-4">📍 {{ $service->location }}</p>

                    <a href="{{ route('user.services.show', $service) }}" 
                        class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Voir Détails
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500">Aucun service trouvé</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $services->links() }}
    </div>
</div>
@endsection
```

### Job Application List (`applications/index.blade.php`)
```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Mes Candidatures</h1>

    <!-- Statistiques -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['pending'] }}</div>
            <div class="text-gray-600">En attente</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-green-600">{{ $stats['accepted'] }}</div>
            <div class="text-gray-600">Acceptées</div>
        </div>
        <div class="bg-red-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</div>
            <div class="text-gray-600">Rejetées</div>
        </div>
    </div>

    <!-- Liste -->
    <div class="bg-white rounded-lg shadow">
        @forelse($applications as $app)
            <div class="border-b p-4 hover:bg-gray-50 transition">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-lg">{{ $app->jobOffer->title }}</h3>
                        <p class="text-gray-600">{{ $app->jobOffer->company_name }}</p>
                        <p class="text-sm text-gray-500 mt-2">
                            Candidature du {{ $app->applied_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($app->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($app->status === 'accepted') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $app->status_label }}
                        </span>
                        @if($app->status === 'pending')
                            <form action="{{ route('user.applications.withdraw', $app) }}" method="POST" 
                                class="mt-2" onsubmit="return confirm('Retirer la candidature ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                    Retirer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">
                <p>Vous n'avez pas encore de candidatures</p>
                <a href="{{ route('user.jobs.index') }}" class="text-blue-600 hover:underline mt-2">
                    Consulter les offres d'emploi
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $applications->links() }}
    </div>
</div>
@endsection
```

---

## 🎯 Points Clés à Retenir

✅ **Toutes les relations** sont déjà configurées  
✅ **Tous les scopes** sont prêts à l'usage  
✅ **Les migrations** doivent être exécutées  
✅ **Les contrôleurs** sont fonctionnels  
✅ **Les routes** sont définies  

⚠️ **À faire** : Créer les vues Blade  
⚠️ **À faire** : Ajouter Policies pour l'authorization  
⚠️ **À faire** : Tester avec des données réelles

