<?php

namespace App\ApiServices;

use App\Http\Utils\Constants;
use App\Mail\UserTerminationMail;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\BookingRequestFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\RatingsAndReviewFields;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use App\Services\LearnerBookingRequestService;
use App\Services\LearnerSvc;
use App\Services\TutorSvc;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\FieldNames\DocProofFields;


class TutorApiService
{
    const LEARNER_TUTOR_RELATION_NONE = 0;
    const LEARNER_TUTOR_RELATION_REQUESTED = 1;
    const LEARNER_TUTOR_RELATION_CONNECTED = 2;

    /**
     * Show the complete details of the selected tutor
     * @param int $tutorId - The id of the selected tutor
     * @param int $learnerId - The id of the learner who views the tutor
     * @return JsonResponse|mixed
     */
    public function getTutor(int $tutorId, int $learnerId)
    {
        $result = null;

        try
        {
            $extraFields = [
                ProfileFields::About,
                ProfileFields::Certifications,
                ProfileFields::Education,
                ProfileFields::Experience,
                UserFields::Contact,
                UserFields::Address,
                'email',
            ];

            $result = $this->queryGetTutors(['extraFields' => $extraFields])
            ->where('users.id', $tutorId)
            ->firstOrFail();
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json(['message' => "The selected tutor could not be found or may have been removed."], ResponseCode::NOT_FOUND);
        }
        catch (Exception $e)
        {
            return response()->json(['message' => "Sorry, we encountered a problem while trying to retrieve the tutor's details."], ResponseCode::INTERNAL_SERVER_ERROR);
        }

        $tutor = $this->mapQueryResultSingle($result);

        $tutor['contact']   = $result->{UserFields::Contact};
        $tutor['email']     = $result->email;
        $tutor['address']   = $result->{UserFields::Address};
        $tutor['relation']  = self::LEARNER_TUTOR_RELATION_NONE;

        // Check if the learner is already connected to the tutor
        if (Booking::where(BookingFields::LearnerId, $learnerId)->where(BookingFields::TutorId, $tutorId)->exists())
            $tutor['relation'] = self::LEARNER_TUTOR_RELATION_CONNECTED;

        // Check if the learner's add request is pending
        $hasPendingRequest = BookingRequest::where(BookingRequestFields::SenderId, $learnerId)->where(BookingRequestFields::ReceiverId, $tutorId)->exists();

        if ($hasPendingRequest)
            $tutor['relation'] = self::LEARNER_TUTOR_RELATION_REQUESTED;

        return response()->json(['message' => 'Success', 'data' => $tutor], ResponseCode::OK);
    }

    /**
     * Get the full list of tutors which includes the tutor's profile picture,
     * fullname, short bio, ratings and total students.
     * @return JsonResponse
     */
    public function getTutors($data = []): JsonResponse
    {
        $tutors     = [];
        $learnerId  = $data['learnerId'];
        $query      = $this->queryGetTutors($data);

        if (!isset($data['retrievalMode']))
            return response()->json(['message' => 'Invalid payload', 'data' => $tutors], ResponseCode::BAD_REQUEST);

        $retrievalMode = $data['retrievalMode'];
        match ($retrievalMode)
        {
            // Mainly used in "Find Tutors"
            'exclude' => $query->whereDoesntHave('bookingsAsTutor', function($subquery) use($learnerId) {
                $subquery->where(BookingFields::LearnerId, $learnerId);
            }),

            // Used in "My Tutors"
            'include' => $query->whereHas('bookingsAsTutor', function($subquery) use($learnerId) {
                $subquery->where(BookingFields::LearnerId, $learnerId);
            }),
        };

        $tutors = $this->mapQueryResult($query);

        return response()->json(['message' => 'Success', 'data' => $tutors], ResponseCode::OK);
    }

    /**
     * Base query builder to select tutors list
     */
    private function queryGetTutors(array $options = []): Builder
    {
        // Get all existing tutors
        $fields = [
            'users.id',
            UserFields::Firstname,
            UserFields::Lastname,
            UserFields::Photo           . ' as photo',
            ProfileFields::Bio          . ' as bio',
            ProfileFields::Disability   . ' as disability',

            // Average rating calculation
            DB::raw("(select FORMAT(avg(" . RatingsAndReviewFields::Rating . "), 1) from ratings_and_reviews where users.id = ratings_and_reviews." . RatingsAndReviewFields::TutorId . ") as averageRating"),
        ];

        if (!empty($options['extraFields']))
            $fields = array_merge($fields, $options['extraFields']);

        // Filter transformations
        // $hasDisabilityFilter = array_key_exists('disability', $options) && $options['disability'] != -1;

        // Build the query
        $builder = User::select($fields)
            ->join('profiles', 'users.id', '=', 'profiles.' . ProfileFields::UserId)
            ->where(UserFields::Role, User::ROLE_TUTOR)
            ->orderBy(UserFields::Firstname, 'ASC')

            // Count the total ratings and reviews made for that tutor
            ->withCount(['receivedRatings as totalReviews' => function($subquery) {
                // Count only rows where review (comment) is not null
                $subquery->whereNotNull(RatingsAndReviewFields::Review);
            }])

            // Count how many learners booked that tutor
            ->withCount(['bookingsAsTutor as totalLearners' => function($query) {
                $query->whereHas('learner', function($query) {
                    $query->where(UserFields::Role, User::ROLE_LEARNER);
                });
            }])

            ->when(!empty($options['search']), function($query) use($options)
            {
                // {empty() can be used to check for key existence or value existence}
                $searchWord = $options['search'];

                $query->where(function ($subquery) use ($searchWord)
                {
                    $subquery->where(UserFields::Firstname, 'LIKE', "%$searchWord%")
                             ->orWhere(UserFields::Lastname, 'LIKE', "%$searchWord%");
                });
            });

        // Return the query builder for later reuse
        return $builder;




            // ->when($hasDisabilityFilter, function($query) use($options)
            // {
            //     $query->where(ProfileFields::Disability, $options['disability']);
            // })
            // ->when(!empty($options['search']), function($query) use($options)
            // {
            //     // {empty() can be used to check for key existence or value existence}
            //     $searchWord = $options['search'];

            //     $query->where(function ($subquery) use ($searchWord)
            //     {
            //         $subquery->where(UserFields::Firstname, 'LIKE', "%$searchWord%")
            //                  ->orWhere(UserFields::Lastname, 'LIKE', "%$searchWord%");
            //     });
            // });
    }

    public function leaveTutor(int $tutorId, int $learnerId)
    {
        try
        {
            DB::beginTransaction();

            $deleted = Booking::where(BookingFields::LearnerId, $learnerId)
                    ->where(BookingFields::TutorId, $tutorId)
                    ->delete();

            if ($deleted)
            {
                DB::commit();
                return ResponseCode::OK;
            }
            else
            {
                DB::rollBack();
                return ResponseCode::INTERNAL_SERVER_ERROR;
            }
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            return ResponseCode::INTERNAL_SERVER_ERROR;
        }
    }
    //----------------------------------------------
    // BEAUTIFY THE RESULTS INTO USER READABLE FORM
    //----------------------------------------------

    /**
     * For a common list of tutors
     */
    private function mapQueryResult($query, $minEntries = 10)
    {
        return $query->paginate($minEntries)->through(function($result)
        {
            $returnData = $this->toMappingData($result);
            return $returnData;
        });
    }

    /**
     * For a single tutor record (eg view tutor details)
     */
    private function mapQueryResultSingle($result)
    {
        $returnData = $this->toMappingData($result);
        $educ = [];
        $work = [];
        $cert = [];

        if (!empty($result->{ProfileFields::Education}))
        {
            foreach (json_decode($result->{ProfileFields::Education}) as $e)
            {
                $educ[] = [
                    'docproofYear'      => implode('-', [$e->{DocProofFields::YearFrom}, $e->{DocProofFields::YearTo}]),
                    'docproofTitle'     => $e->{DocProofFields::EducInstitution},
                    'docproofCaption'   => $e->{DocProofFields::EducDegree}
                ];
            }
        }

        if (!empty($result->{ProfileFields::Experience}))
        {
            foreach (json_decode($result->{ProfileFields::Experience}) as $e)
            {
                $work[] = [
                    'docproofYear'      => implode('-', [$e->{DocProofFields::YearFrom}, $e->{DocProofFields::YearTo}]),
                    'docproofTitle'     => $e->{DocProofFields::WorkCompany},
                    'docproofCaption'   => $e->{DocProofFields::WorkRole}
                ];
            }
        }

        if (!empty($result->{ProfileFields::Certifications}))
        {
            foreach (json_decode($result->{ProfileFields::Certifications}) as $c)
            {
                $cert[] = [
                    'docproofYear'      => $c->{DocProofFields::YearFrom},
                    'docproofTitle'     => $c->{DocProofFields::CertTitle},
                    'docproofCaption'   => $c->{DocProofFields::CertDescr}
                ];
            }
        }

        $config     = HTMLPurifier_Config::createDefault();
        $purifier   = new HTMLPurifier($config);
        $about      = $purifier->purify($result->{ProfileFields::About});

        // $returnData['aboutme']          = $result->{ProfileFields::About};
        $returnData['aboutMe']          = $about;
        $returnData['certifications']   = $cert;
        $returnData['education']        = $educ;
        $returnData['workExperience']   = $work;

        return $returnData;
    }

    private function toMappingData($result)
    {
        return [
            'tutorId'           => TutorSvc::toHashedId($result->id),
            'name'              => $result->name,
            'photo'             => User::getPhotoUrl($result->{UserFields::Photo}),
            'bio'               => $result->profile->{ProfileFields::Bio} ?? 'N/A',
            'totalLearners'     => $result->totalLearners,
            'reviews'           => $result->totalReviews,
            'ratings'           => $result->averageRating ?? 0,
            'disability'        => $result->disability,

        ];
    }
}