<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Received request to create employee', $request->all());

        try {
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'company_id' => 'required|exists:companies,id',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
            ]);

            Log::info('Validated data', $validatedData);

            $employee = Employee::create($validatedData);

            Log::info('Created employee', $employee->toArray());

            return response()->json($employee, 201);
        } catch (ValidationException $e) {
            Log::error('Validation error creating employee: ' . json_encode($e->errors()), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating employee: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json(['error' => 'Failed to create employee'], 500);
        }
    }
}
