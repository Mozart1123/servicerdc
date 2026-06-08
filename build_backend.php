<?php

$files = [
    'app/Http/Controllers/Artisan/ServiceController.php' => '<?php

namespace App\Http\Controllers\Artisan;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Notification;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::where("user_id", $request->user()->id)->get();
        return response()->json($services);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => "required|string|max:255",
            "category" => "required|string",
            "description" => "required|string",
            "price" => "required|numeric",
            "city" => "required|string",
            "availability_status" => "required|string|in:available,unavailable",
            "images" => "nullable|string"
        ]);

        $service = Service::create(array_merge($validated, [
            "user_id" => $request->user()->id,
            "artisan_name" => $request->user()->name,
            "artisan_phone_email" => $request->user()->email,
        ]));

        return response()->json(["message" => "Service created successfully", "service" => $service], 201);
    }

    public function requests(Request $request)
    {
        $requests = ServiceRequest::whereHas("service", function ($q) use ($request) {
            $q->where("user_id", $request->user()->id);
        })->with("user", "service")->get();

        return response()->json($requests);
    }

    public function acceptRequest($id)
    {
        $request = ServiceRequest::findOrFail($id);
        $request->update(["status" => "accepted"]);

        Notification::create([
            "user_id" => $request->user_id,
            "title" => "Service Request Accepted",
            "message" => "Your request for service {$request->service->title} was accepted.",
            "type" => "service_request",
            "related_id" => $request->id
        ]);

        return response()->json(["message" => "Request accepted", "request" => $request]);
    }

    public function rejectRequest($id)
    {
        $request = ServiceRequest::findOrFail($id);
        $request->update(["status" => "rejected"]);

        Notification::create([
            "user_id" => $request->user_id,
            "title" => "Service Request Rejected",
            "message" => "Your request for service {$request->service->title} was rejected.",
            "type" => "service_request",
            "related_id" => $request->id
        ]);

        return response()->json(["message" => "Request rejected", "request" => $request]);
    }
}
',
    'app/Http/Controllers/Client/ServiceController.php' => '<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Notification;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where("availability_status", "available")->get();
        return response()->json($services);
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }

    public function storeRequest(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        if (ServiceRequest::where("user_id", $request->user()->id)->where("service_id", $id)->where("status", "pending")->exists()) {
            return response()->json(["message" => "You already have a pending request for this service."], 400);
        }

        $serviceRequest = ServiceRequest::create([
            "user_id" => $request->user()->id,
            "service_id" => $service->id,
            "status" => "pending",
        ]);

        Notification::create([
            "user_id" => $service->user_id,
            "title" => "New Service Request",
            "message" => "User {$request->user()->name} has requested your service {$service->title}.",
            "type" => "service_request",
            "related_id" => $serviceRequest->id
        ]);

        return response()->json(["message" => "Service request sent successfully", "request" => $serviceRequest], 201);
    }
}
',
    'app/Http/Controllers/Recruiter/JobOfferController.php' => '<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use App\Models\JobApplication;
use App\Models\Notification;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{
    public function index(Request $request)
    {
        $jobs = JobOffer::where("user_id", $request->user()->id)->get();
        return response()->json($jobs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "job_title" => "required|string|max:255",
            "description" => "required|string",
            "requirements" => "required|string",
            "salary_range" => "nullable|string",
            "job_type" => "required|in:full-time,part-time,internship,freelance,contract",
            "location" => "required|string",
            "application_deadline" => "required|date",
            "status" => "required|in:active,closed",
        ]);

        $job = JobOffer::create(array_merge($validated, [
            "user_id" => $request->user()->id,
            "company_name" => $request->user()->name,
        ]));

        return response()->json(["message" => "Job offer published successfully", "job" => $job], 201);
    }

    public function applications(Request $request)
    {
        $applications = JobApplication::whereHas("jobOffer", function($q) use ($request) {
            $q->where("user_id", $request->user()->id);
        })->with("user.cv", "jobOffer")->get();

        return response()->json($applications);
    }

    public function showApplication($id)
    {
        $application = JobApplication::with("user.cv", "jobOffer")->findOrFail($id);
        return response()->json($application);
    }

    public function approveApplication($id)
    {
        $app = JobApplication::findOrFail($id);
        $app->update(["status" => "approved"]);

        Notification::create([
            "user_id" => $app->user_id,
            "title" => "Application Approved",
            "message" => "Your application for {$app->jobOffer->job_title} has been approved.",
            "type" => "job_application",
            "related_id" => $app->id
        ]);

        return response()->json(["message" => "Application approved"]);
    }

    public function rejectApplication($id)
    {
        $app = JobApplication::findOrFail($id);
        $app->update(["status" => "rejected"]);

        Notification::create([
            "user_id" => $app->user_id,
            "title" => "Application Rejected",
            "message" => "Your application for {$app->jobOffer->job_title} was rejected.",
            "type" => "job_application",
            "related_id" => $app->id
        ]);

        return response()->json(["message" => "Application rejected"]);
    }
}
',
    'app/Http/Controllers/Client/JobOfferController.php' => '<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use App\Models\JobApplication;
use App\Models\Notification;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{
    public function index()
    {
        $jobs = JobOffer::where("status", "active")->get();
        return response()->json($jobs);
    }

    public function show($id)
    {
        $job = JobOffer::findOrFail($id);
        return response()->json($job);
    }

    public function apply(Request $request, $id)
    {
        if (!$request->user()->cv) {
            return response()->json(["message" => "You must create a CV before applying."], 403);
        }

        $job = JobOffer::findOrFail($id);

        if (JobApplication::where("user_id", $request->user()->id)->where("job_offer_id", $id)->exists()) {
            return response()->json(["message" => "You have already applied to this job."], 400);
        }

        $app = JobApplication::create([
            "user_id" => $request->user()->id,
            "job_offer_id" => $id,
            "status" => "pending"
        ]);

        Notification::create([
            "user_id" => $job->user_id,
            "title" => "New Job Application",
            "message" => "{$request->user()->name} has applied to your job {$job->job_title}.",
            "type" => "job_application",
            "related_id" => $app->id
        ]);

        return response()->json(["message" => "Application submitted successfully", "application" => $app], 201);
    }
}
',
    'app/Http/Controllers/Client/CvController.php' => '<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use Illuminate\Http\Request;

class CvController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user()->cv);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "full_name" => "required|string|max:255",
            "email" => "required|email",
            "phone_number" => "required|string",
            "address" => "required|string",
            "education" => "required|string",
            "skills" => "required|string",
            "experience" => "required|string",
            "languages" => "required|string",
            "cv_file_path" => "nullable|string",
            "portfolio_link" => "nullable|url"
        ]);

        $cv = Cv::updateOrCreate(
            ["user_id" => $request->user()->id],
            $validated
        );

        return response()->json(["message" => "CV saved successfully", "cv" => $cv]);
    }
}
',
    'app/Http/Controllers/NotificationController.php' => '<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Notification::where("user_id", $request->user()->id)->orderBy("created_at", "desc")->get());
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(["is_read" => true]);
        return response()->json(["message" => "Notification marked as read"]);
    }
}
',
    'app/Models/Cv.php' => '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "full_name", "email", "phone_number", "address",
        "education", "skills", "experience", "languages", "cv_file_path", "portfolio_link"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
',
    'app/Models/Service.php' => '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
',
    'app/Models/ServiceRequest.php' => '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
',
    'app/Models/JobOffer.php' => '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }
}
',
    'app/Models/JobApplication.php' => '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobOffer()
    {
        return $this->belongsTo(JobOffer::class);
    }
}
',
    'app/Models/Notification.php' => '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
'
];

foreach ($files as $path => $content) {
    if (!is_dir(dirname($path))) {
        mkdir(dirname($path), 0777, true);
    }
    file_put_contents($path, $content);
}
echo "Build complete.";
