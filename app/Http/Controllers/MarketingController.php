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

        $monthsServing = $firstActivity
            ? Carbon::parse($firstActivity)->diffInMonths(now())
            : 0;
        $servicesCategories = Service::select('category')->distinct()->pluck('category');    

        return view('marketing', compact(
            'documentsProcessed',
            'customersCount',
            'servicesCount',
            'monthsServing',
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

        return view('about', compact('servicesCategories'));
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
}
