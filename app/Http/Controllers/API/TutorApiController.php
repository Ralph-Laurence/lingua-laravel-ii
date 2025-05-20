<?php

namespace App\Http\Controllers\API;

use App\ApiServices\ResponseCode;
use App\ApiServices\TutorApiService;
use App\Http\Controllers\Controller;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use App\Services\LearnerBookingRequestService;
use App\Services\LearnerSvc;
use App\Services\TutorSvc;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TutorApiController extends Controller
{
    public function __construct
    (
        private TutorApiService $tutorApiService,
        private LearnerBookingRequestService $lrnBookingReqSvc
    )
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
    public function index(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'learnerId' => 'required|string',
                'search' => 'nullable|string' // Add validation for the search parameter
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

        $payload = [
            'learnerId'     => LearnerSvc::toRawId($request->query('learnerId')),
            'retrievalMode' => 'exclude'
        ];

        if ($request->filled('search')) {
            $payload['search'] = $request->query('search');
        }

        return $this->tutorApiService->getTutors($payload);
    }

    /**
     * Show the full details of the selected tutor
     * @param string $id - The hashed id of the tutor
     * @return JsonResponse|mixed
     */
    public function show(Request $request, string $id)
    {
        if (empty($id))
        {
            return response()->json([
                'message' => 'One of the required pieces of data for fetching the record is invalid.'
            ], ResponseCode::VALIDATION_ERROR);
        }

        $tutorId   = TutorSvc::toRawId($id);
        $learnerId = LearnerSvc::toRawId($request->query('viewerId'));

        return $this->tutorApiService->getTutor($tutorId, $learnerId);
    }

    /**
     * The hire tutor is like facebook's friend request feature.
     * The learner can't just add the tutor directly as it needs
     * the tutor's consent --thus, add request
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|mixed
     */
    public function hireTutor(Request $request)
    {
        $inputs = $this->validateTutorAction($request);

        if ($inputs instanceof JsonResponse)
            return $inputs;

        $tutorId    = $inputs['tutorId'];
        $learnerId  = $inputs['learnerId'];
        $tutor      = $inputs['tutor'];

        $bookRequest = $this->lrnBookingReqSvc->hireTutor($learnerId, $tutorId);

        if ($bookRequest == ResponseCode::OK)
        {
            $tutorName = $tutor->{UserFields::Firstname};
            $message   = "Your request to add $tutorName has been sent, and they'll be notified soon.";

            return response()->json(['message' => $message], ResponseCode::OK);
        }

        // Fallback response
        // if ($bookRequest == ResponseCode::INTERNAL_SERVER_ERROR)
        return response()->json(['message' => "Sorry, something went wrong while trying to connect you with your tutor. Please try again later."], ResponseCode::INTERNAL_SERVER_ERROR);
    }

    public function cancelHireTutor(Request $request)
    {
        $inputs = $this->validateTutorAction($request);

        if ($inputs instanceof JsonResponse)
            return $inputs;

        $tutorId    = $inputs['tutorId'];
        $learnerId  = $inputs['learnerId'];
        $tutor      = $inputs['tutor'];

        $result = $this->lrnBookingReqSvc->cancelHireTutor($learnerId, $tutorId);

        if ($result == ResponseCode::OK)
        {
            $tutorName = $tutor->{UserFields::Firstname};
            $message   = "Your request to add $tutorName has been successfully cancelled.";

            return response()->json(['message' => $message], ResponseCode::OK);
        }

        // Fallback response
        return response()->json(['message' => "Sorry, something went wrong while trying to perform the requested action. Please try again later."], ResponseCode::INTERNAL_SERVER_ERROR);
    }

    public function leaveTutor(Request $request)
    {
        $inputs = $this->validateTutorAction($request);

        if ($inputs instanceof JsonResponse)
            return $inputs;

        $tutorId    = $inputs['tutorId'];
        $learnerId  = $inputs['learnerId'];
        $tutor      = $inputs['tutor'];

        $result = $this->tutorApiService->leaveTutor($tutorId, $learnerId);

        if ($result == ResponseCode::OK)
        {
            $tutorName = $tutor->{UserFields::Firstname};
            $message   = "Your connection to $tutorName has been successfully terminated.";

            return response()->json(['message' => $message], ResponseCode::OK);
        }

        // Fallback response
        return response()->json(['message' => "Sorry, something went wrong while trying to perform the requested action. Please try again later."], ResponseCode::INTERNAL_SERVER_ERROR);
    }

    private function validateTutorAction(Request $request)
    {
        $validatorData = $request->only(['learnerId', 'tutorId']);

        $validator = Validator::make(
            $validatorData,
            [
                'learnerId' => 'required|string',
                'tutorId'   => 'required|string'
            ],
            [
                'learnerId.required' => 'One of the necessary data for retrieving the record is missing.',
                'learnerId.string'   => 'One of the data supplied for retrieving the record is invalid.',
                'tutorId.required'   => 'One of the necessary data for retrieving the record is missing.',
                'tutorId.string'     => 'One of the data supplied for retrieving the record is invalid.',
            ]
        );

        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], ResponseCode::VALIDATION_ERROR);
        }

        $tutorId     = TutorSvc::toRawId($request->tutorId);
        $learnerId   = LearnerSvc::toRawId($request->learnerId);
        $tutor       = null;

        try
        {
            $tutor = User::where('users.id', $tutorId)->firstOrFail();
        }
        catch (ModelNotFoundException $ex)
        {
            return response()->json(['message' => "Sorry, we're unable to locate the tutor's details."], ResponseCode::NOT_FOUND);
        }

        return [
            'tutorId'   => $tutorId,
            'learnerId' => $learnerId,
            'tutor'     => $tutor
        ];
    }
}
