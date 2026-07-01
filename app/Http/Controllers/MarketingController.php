<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Project;
use Carbon\Carbon;

class MarketingController extends Controller
{
    public function index()
    {
        $documentsProcessed = Invoice::count(); 
        // or InvoiceItem::count()
        $customersCount = Customer::count();
        $servicesCount = Service::count();
$firstActivity = collect([
    Invoice::min('created_at'),
    Customer::min('created_at'),
])->filter()->sort()->first();

$yearsServing = 0;
$hasExtraMonths = false;

if ($firstActivity) {
    $months = Carbon::parse($firstActivity)->diffInMonths(now());
    $yearsServing = max(1, intdiv($months, 12));
    $hasExtraMonths = ($months % 12) > 0;
}

        $servicesCategories = Service::select('category')->distinct()->pluck('category');    

        return view('marketing', compact(
            'documentsProcessed',
            'customersCount',
            'servicesCount',
            'yearsServing',
            'hasExtraMonths',
            'servicesCategories'
        ));
    } 
    
    public function contact()
    {
        $servicesCategories = Service::select('category')->distinct()->pluck('category');    

        return view('contact', compact('servicesCategories'));
    }

    public function about()
    {
        $servicesCategories = Service::select('category')->distinct()->pluck('category');    
        $firstActivity = collect([
            Invoice::min('created_at'),
            Customer::min('created_at'),
        ])->filter()->sort()->first();

        $yearsServing = 0;
        $yearsLabel   = '0';

        if ($firstActivity) {
            $months = Carbon::parse($firstActivity)->diffInMonths(now());
            $years  = intdiv($months, 12);

            // Minimum display logic
            $yearsServing = max(1, $years);

            // Add "+" only if extra months exist
            $yearsLabel = ($months % 12 > 0)
                ? $yearsServing . '+'
                : (string) $yearsServing;
        }

        return view('about', compact('servicesCategories', 'yearsLabel'));
    }  
    
    public function byCategory($category)
    {
        $services = Service::where('category', $category)->get();
        $servicesCategories = Service::select('category')->distinct()->pluck('category');    

        return view('services.category', compact('services', 'category', 'servicesCategories'));
    }

    public function showService($id)
    {
        $service = Service::findOrFail($id);
        $servicesCategories = Service::select('category')->distinct()->pluck('category');    
        $relatedServices = Service::where('category', $service->category)
                ->where('id', '!=', $service->id)
                ->where('is_active', true)
                ->get();

        return view('services.service', compact('service', 'servicesCategories', 'relatedServices'));
    } 
    
    public function work()
    {
        $projects = Project::where('board_type', 'public')
            ->whereIn('status', ['milestone', 'completed'])
            ->latest()
            ->get();
            // dd($projects);
        $servicesCategories = Service::select('category')->distinct()->pluck('category');    

        return view('work.index', compact('projects', 'servicesCategories'));
    }

    public function byType(string $type)
    {
        $projects = Project::where('board_type', 'public')
            ->where('type', $type)
            ->whereIn('status', ['milestone', 'completed'])
            ->latest()
            ->get();
        $servicesCategories = Service::select('category')->distinct()->pluck('category');    

        return view('work.type', compact('projects', 'type', 'servicesCategories'));
    }

    public function show(Project $project)
    {
        abort_if(
            $project->board_type !== 'public' ||
            !in_array($project->status, ['milestone', 'completed']),
            404
        );
        $project->load('media');
        $servicesCategories = Service::select('category')->distinct()->pluck('category');    

        return view('work.show', compact('project', 'servicesCategories'));
    } 

    /**
     * Show all training courses (public)
     * Optional tier filter via ?tier=
     */
    public function trainingCourses(Request $request)
    {
        $activeTier = $request->query('tier');

        $query = Service::where('category', 'Training')
            ->where('type', 'course');

        if ($activeTier) {
            $query->where('tier', $activeTier);
        }

        $courses = $query
            ->orderBy('tier')
            ->paginate(6)
            ->withQueryString();

        $servicesCategories = Service::select('category')
            ->distinct()
            ->pluck('category');

        return view('training.index', compact(
            'courses',
            'servicesCategories',
            'activeTier'
        ));
    }

    /**
     * Show a single course by slug
     */
    public function showCourse(Service $course)
    {
        abort_if(
            $course->category !== 'Training' ||
            $course->type !== 'course' ||
            !$course->is_active,
            404
        );

        $servicesCategories = Service::select('category')
            ->distinct()
            ->pluck('category');
            
        $relatedCourses = Service::where('category', 'Training')
            ->where('type', 'course')
            ->where('id', '!=', $course->id)
            ->where('tier', $course->tier) // same tier = higher relevance
            ->limit(5)
            ->get();    

        return view('training.show', compact('course', 'servicesCategories', 'relatedCourses'));
    }

    /**
     * Saturday timetable / schedule view
     */
    public function schedule()
    {
        $courses = Service::where('category', 'Training')
            ->where('type', 'course')
            ->whereNotNull('schedule')
            ->orderBy('tier')
            ->orderBy('title')
            ->get();

        $servicesCategories = Service::select('category')->distinct()->pluck('category');    

        return view('training.schedule', [
            'courses' => $courses
        ], compact('servicesCategories'));
    }    

}
