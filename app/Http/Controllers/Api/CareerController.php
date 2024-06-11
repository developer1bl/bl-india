<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CareerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Career::all();

        return response()->json([
            'data' => $jobs ?? [],
            'success' => true
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules with custom closure for experience_range
        $rules = [
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'job_responsibility' => 'required|string',
            'experience_range' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d+-\d+$/', $value)) {
                        return $fail('The experience range must be in the format "min-max" where min and max are numbers.');
                    }
                    [$min, $max] = explode('-', $value);
                    if ($min > $max) {
                        return $fail('The minimum value of the experience range must be less than or equal to the maximum value.');
                    }
                },
            ],
            'job_status' => 'required|boolean',
        ];

        // Define custom error messages (optional)
        $messages = [
            'job_title.required' => 'The job title is required.',
            'job_title.string' => 'The job title must be a string.',
            'job_title.max' => 'The job title must not exceed 255 characters.',
            'job_description.required' => 'The job description is required.',
            'job_description.string' => 'The job description must be a string.',
            'job_responsibility.required' => 'The job responsibility is required.',
            'job_responsibility.string' => 'The job responsibility must be a string.',
            'experience_range.required' => 'The experience range is required.',
            'experience_range.string' => 'The experience range must be a string.',
            'job_status.required' => 'The job status is required.',
            'job_status.boolean' => 'The job status must be a boolean.',
        ];


        // Perform validation
        $validator = Validator::make($request->all(), $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new career
        $career = Career::create($request->all());

        // Return the created career with a 201 status code
        return response()->json(['success' => true, 'message' => 'job created successfully '], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $job = Career::whereJob_status(true)->find($id);

        if (!$job) {

            return response()->json([
                'success' => false,
                'message' => 'job not found'
            ], 403);
        }

        return response()->json([
            'data' => $job,
            'success' => true,
            'message' => ''
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $job = Career::find($id);

        if (!$job) {

            return response()->json([
                'success' => false,
                'message' => 'job not found'
            ], 403);
        }

        // Define validation rules with custom closure for experience_range
        $rules = [
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'job_responsibility' => 'required|string',
            'experience_range' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d+-\d+$/', $value)) {
                        return $fail('The experience range must be in the format "min-max" where min and max are numbers.');
                    }
                    [$min, $max] = explode('-', $value);
                    if ($min > $max) {
                        return $fail('The minimum value of the experience range must be less than or equal to the maximum value.');
                    }
                },
            ],
            'job_status' => 'required|boolean',
        ];

        // Define custom error messages (optional)
        $messages = [
            'job_title.required' => 'The job title is required.',
            'job_title.string' => 'The job title must be a string.',
            'job_title.max' => 'The job title must not exceed 255 characters.',
            'job_description.required' => 'The job description is required.',
            'job_description.string' => 'The job description must be a string.',
            'job_responsibility.required' => 'The job responsibility is required.',
            'job_responsibility.string' => 'The job responsibility must be a string.',
            'experience_range.required' => 'The experience range is required.',
            'experience_range.string' => 'The experience range must be a string.',
            'job_status.required' => 'The job status is required.',
            'job_status.boolean' => 'The job status must be a boolean.',
        ];


        // Perform validation
        $validator = Validator::make($request->all(), $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new career
        $career = $job->update($request->all());

        // Return the created career with a 201 status code
        return response()->json(['success' => true, 'message' => 'job Updated successfully '], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $job = Career::find($id);

        if (!$job) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'job not found'
                                    ], 403);
        }

        $job->delete();

        return response()->json([
                                'success' => true,
                                'message' => 'job deleted successfully'
                                ], 200);
    }
}
