<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OnlineVisit;

class OnlineVisitController extends Controller
{
    public function totalVisits()
    {
        return response()->json([
            'total' => OnlineVisit::count()
        ]);
    }

    public function todayVisits()
    {
        return response()->json([
            'today' => OnlineVisit::whereDate('visited_at', now())->count()
        ]);
    } 
    
    public function uniqueVisitors()
    {
        return response()->json([
            'unique_visitors' => OnlineVisit::distinct('visitor_id')->count('visitor_id')
        ]);
    } 
    
    public function topPages()
    {
        return response()->json(
            OnlineVisit::select('url')
                ->selectRaw('count(*) as total')
                ->groupBy('url')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
        );
    }    
}
