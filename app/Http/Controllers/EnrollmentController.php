<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Service;
use App\Models\Customer;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
        ]);

        $enrollment = Enrollment::create([
            'customer_id' => $request->customer_id,
            'service_id' => $request->service_id,
            'enrolled_at' => now(),
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Student enrolled successfully',
            'data' => $enrollment->load(['customer', 'service'])
        ]);
    }

    public function index()
    {
        $enrollments = Enrollment::with(['customer', 'service'])
            ->latest()
            ->get();

        $customers = Customer::select('id', 'name', 'phone')
            ->orderBy('name')
            ->get();

        $courses = Service::where('category', 'Training')
            ->where('is_active', 1)
            ->select('id', 'name', 'price', 'unit', 'category')
            ->orderBy('name')
            ->get();

        return response()->json([
            'enrollments' => $enrollments,
            'customers' => $customers,
            'courses' => $courses
        ]);
    }
}
