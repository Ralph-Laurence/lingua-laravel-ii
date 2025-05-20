<?php

namespace App\Http\Controllers\API;

use App\ApiServices\TutorApiService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\ApiServices\ResponseCode;
use App\Services\LearnerSvc;
use App\Services\TutorSvc;
use Illuminate\Support\Facades\Validator;

class LearnersApiController extends Controller
{
    public function __construct(private TutorApiService $tutorApiService)
    {}

    /**
     * Get the full list of tutors. During the retrieval, we
     *  will exclude all tutors already connected to the learner.
     *
     * @param Request $request
     * - The request should contain the learner's id (required)
     * and filter | sort options (optional)
     * @return JsonResponse
     */
    public function index(Request $request, $id):JsonResponse
    {
        $searchRaw = $request->query('search');
        $validator = Validator::make(
            ['learnerId' => $id, 'search' => $searchRaw],
            [
                'learnerId'          => 'required|string', // e.g. 23jZg5wRbn
                'search'             => 'nullable|string' // Add validation for the search parameter
            ],
            [
                'learnerId.required' => 'One of the necessary data for retrieving the record is missing.',
                'learnerId.string'   => 'One of the data supplied for retrieving the record is invalid.',
                'search.string'      => 'The search term is invalid.'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], ResponseCode::VALIDATION_ERROR);
        }

        $params  = $validator->validated();
        $payload = [
            'learnerId'     => LearnerSvc::toRawId($params['learnerId']),
            'retrievalMode' => 'include'
        ];

        if ($searchRaw) {
            $payload['search'] = $params['search'];
        }

        return $this->tutorApiService->getTutors($payload);
    }
}
