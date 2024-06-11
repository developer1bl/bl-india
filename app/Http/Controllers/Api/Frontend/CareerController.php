<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Career;

class CareerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getCurrentOpenedJobs()
    {
        $jobs = Career::whereJob_status(1)->get();

        return response()->json([
                                'data' => $jobs?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function getSingleJobs($job){

        $job = Career::find($job);

        if ($job) {

            return response()->json([
                                'data' => $job?? [],
                                'success' => true
                                ], 200);
        } else {

            return response()->json([
                                'data' => [],
                               'success' => false
                                ], 404);
        }
    }

    /**
     * Display a Recruitment process.
     */
    public function getRecruitmentProcess(){

        $data = [
            'section_title' => 'Learn our Recruitment process',
            'process_steps' => [
                                'CV Submission',
                                'Phone screening',
                                'Technical Round'
                                ],
        ];

        return response()->json([
                                'data' => $data,
                                'success' => true
                                ], 200);
    }

    /**
     * Display the related service.
     */
    public function getRelatedJob($job){

        $job = Career::find($job);

        if ($job) {

            return response()->json([
                                'data' => $job->similarJobs(),
                                'success' => true
                                ], 200);
        } else {

            return response()->json([
                                'data' => [],
                                'success' => false,
                                'message' => 'No similar job found'
                                ], 404);
        }


    }
}

