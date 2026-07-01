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

    public function requestEnrollment(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
        ]);

        // 1. Find or create customer by phone
        $customer = Customer::firstOrCreate(
            ['phone' => $request->phone],
            ['name'  => $request->name]
        );

        // 2. Prevent duplicate requests
        $exists = Enrollment::where('customer_id', $customer->id)
            ->where('service_id', $request->service_id)
            ->whereIn('status', ['pending','active'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Enrollment already exists or pending approval'
            ], 409);
        }

        // 3. Create PENDING enrollment
        $enrollment = Enrollment::create([
            'customer_id' => $customer->id,
            'service_id'  => $request->service_id,
            'status'      => 'pending',
        ]);

        return response()->json([
            'message' => 'Enrollment request submitted. We will contact you shortly.'
        ]);
    }
    
    public function approve($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $enrollment->update([
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        return response()->json(['message' => 'Enrollment approved']);
    }

    public function reject($id)
    {
        Enrollment::findOrFail($id)->delete();

        return response()->json(['message' => 'Enrollment rejected']);
    }    
}
