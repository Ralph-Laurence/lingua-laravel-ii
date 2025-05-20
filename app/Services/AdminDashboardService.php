<?php

namespace App\Services;

use App\Http\Utils\Constants;
use App\Http\Utils\HashSalts;
use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use App\Models\PendingRegistration;
use App\Models\User;
use Hashids\Hashids;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use Session;

class AdminDashboardService
{
    private $tutorHashIds;
    private $learnerHashIds;

    function __construct()
    {
        $this->tutorHashIds = new Hashids(HashSalts::Tutors, 10);
        $this->learnerHashIds = new Hashids(HashSalts::Learners, 10);
    }

    public function getTotals(): array
    {
        $strRoleTutor    = User::ROLE_TUTOR;
        $strRoleLearner  = User::ROLE_LEARNER;
        $strFieldRole    = UserFields::Role;
        $strFieldFname   = UserFields::Firstname;
        $strFieldLname   = UserFields::Lastname;
        $strFieldPhoto   = UserFields::Photo;
        $strFieldLrnId   = BookingFields::LearnerId;
        $strFieldTutId   = BookingFields::TutorId;
        $strFieldDisability = ProfileFields::Disability;
        $strFieldProfileId  = ProfileFields::UserId;
        $strFieldVerified   = UserFields::IsVerified;
        $isVerified = 1;
        $deaf = 1;
        $mute = 2;
        $both = 3;

        $viewData = [];

        $totals = DB::table('users as u')
            ->join('profiles as p', "p.$strFieldProfileId", 'u.id')
            ->select(
                DB::raw("SUM(u.$strFieldRole = '$strRoleTutor') AS total_tutors"),
                DB::raw("SUM(u.$strFieldRole = '$strRoleLearner')        AS total_learners"),

                // Count the disabled vs normal users per user role: Learners = N, Tutors = N
                DB::raw("SUM(u.$strFieldRole = '$strRoleTutor'           AND p.$strFieldDisability <> 0 AND u.$strFieldVerified = $isVerified) AS total_impaired_tutors"),
                DB::raw("SUM(u.$strFieldRole = '$strRoleLearner'         AND p.$strFieldDisability <> 0) AS total_impaired_learners"),
                DB::raw("SUM(u.$strFieldRole = '$strRoleTutor'           AND p.$strFieldDisability = 0 AND u.$strFieldVerified = $isVerified) AS total_non_impaired_tutors"),
                DB::raw("SUM(u.$strFieldRole = '$strRoleLearner'         AND p.$strFieldDisability = 0) AS total_non_impaired_learners")
            )
            ->first();

        $totalGroupedImpairments = DB::select("
                SELECT
                    SUM(CASE WHEN u.$strFieldRole = $strRoleLearner AND p.$strFieldDisability = $deaf THEN 1 ELSE 0 END) AS total_deaf_learners,
                    SUM(CASE WHEN u.$strFieldRole = $strRoleLearner AND p.$strFieldDisability = $mute THEN 1 ELSE 0 END) AS total_mute_learners,
                    SUM(CASE WHEN u.$strFieldRole = $strRoleLearner AND p.$strFieldDisability = $both THEN 1 ELSE 0 END) AS total_deaf_and_mute_learners,

                    SUM(CASE WHEN u.$strFieldRole = $strRoleTutor AND p.$strFieldDisability = $deaf THEN 1 ELSE 0 END) AS total_deaf_tutors,
                    SUM(CASE WHEN u.$strFieldRole = $strRoleTutor AND p.$strFieldDisability = $mute THEN 1 ELSE 0 END) AS total_mute_tutors,
                    SUM(CASE WHEN u.$strFieldRole = $strRoleTutor AND p.$strFieldDisability = $both THEN 1 ELSE 0 END) AS total_deaf_and_mute_tutors
                FROM users u
                JOIN profiles p ON p.$strFieldProfileId = u.id");

        $topTutors = DB::table('bookings')
            ->select([
                'tutors.id',
                $strFieldPhoto,
                "$strFieldFname as tutor_fname",
                DB::raw("CONCAT($strFieldFname,' ',$strFieldLname) as tutor_name") ,
                DB::raw("COUNT(bookings.$strFieldLrnId) AS total_students")
            ])
            ->join('users AS tutors', "bookings.$strFieldTutId", '=', 'tutors.id')
            ->groupBy('tutors.id', $strFieldFname, $strFieldLname, $strFieldPhoto)
            ->orderBy('total_students', 'desc')
            ->limit(5)
            ->get();

        $learnerWithMostTutors = DB::table('bookings')
            ->select([
                'learner.id',
                $strFieldPhoto,
                DB::raw("CONCAT($strFieldFname,' ',$strFieldLname) as learner_name") ,
                DB::raw("COUNT(bookings.$strFieldTutId) AS total_tutors")
            ])
            ->join('users AS learner', "bookings.$strFieldLrnId", '=', 'learner.id')
            ->groupBy('learner.id', $strFieldFname, $strFieldLname, $strFieldPhoto)
            ->orderBy('total_tutors', 'desc')
            ->first();

        $tutorWithMostLearners = DB::table('bookings')
            ->select([
                'tutor.id',
                $strFieldPhoto,
                DB::raw("CONCAT($strFieldFname,' ',$strFieldLname) as tutor_name") ,
                DB::raw("COUNT(bookings.$strFieldLrnId) AS total_learners")
            ])
            ->join('users AS tutor', "bookings.$strFieldTutId", '=', 'tutor.id')
            ->groupBy('tutor.id', $strFieldFname, $strFieldLname, $strFieldPhoto)
            ->orderBy('total_learners', 'desc')
            ->first();

        if ($tutorWithMostLearners)
        {
            $topTutorId = $this->tutorHashIds->encode($tutorWithMostLearners->id);
            $topTutor = [
                'tutorDetails'   => route('admin.tutors-show', $topTutorId),
                'tutorName'      => $tutorWithMostLearners->tutor_name,
                'tutorPhoto'     => User::getPhotoUrl($tutorWithMostLearners->photo),
                'totalLearners'  => $tutorWithMostLearners->total_learners,
            ];
            $viewData['topTutor'] = $topTutor;
        }

        if ($topTutors->isNotEmpty())
        {
            $topTutorsArr = [];

            foreach ($topTutors as $k => $obj)
            {
                $topTutorsArr[] = [
                    'tutorDetails'  => route('admin.tutors-show', $this->tutorHashIds->encode($obj->id)),
                    'tutorFname'    => $obj->tutor_fname,
                    'tutorName'     => $obj->tutor_name,
                    'tutorPhoto'    => User::getPhotoUrl($obj->photo),
                    'totalLearners' => $obj->total_students,
                ];
            }

            $viewData['topTutors'] = json_encode($topTutorsArr);
        }

        if ($learnerWithMostTutors)
        {
            $topLearnerId = $this->learnerHashIds->encode($learnerWithMostTutors->id);
            $topLearner = [
                'learnerDetails'   => route('admin.learners-show', $topLearnerId),
                'learnerName'      => $learnerWithMostTutors->learner_name,
                'learnerPhoto'     => User::getPhotoUrl($learnerWithMostTutors->photo),
                'totalTutors'      => $learnerWithMostTutors->total_tutors,
            ];
            $viewData['topLearner']    = $topLearner;
        }

        $totalPending = PendingRegistration::count();

        $viewData['totalTutors']            = $totals->total_tutors;
        $viewData['totalLearners']          = $totals->total_learners;
        $viewData['totalMembers']           = $totals->total_learners + $totals->total_tutors;
        $viewData['totalPending']           = $totalPending;

        $viewData['impairmentRatio'] = [
            'Impaired Tutors'       => $totals->total_impaired_tutors,
            'Impaired Learners'     => $totals->total_impaired_learners
        ];

        $viewData['nonImpairedRatio'] = [
            'Non-Impaired Learners'  => $totals->total_non_impaired_learners,
            'Non-Impaired Tutors'    => $totals->total_non_impaired_tutors,
        ];

        $viewData['totalGroupedImpairments'] = $totalGroupedImpairments;

        return $viewData;
    }

    public function viewDashboardPendingRegistration(Request $request)
    {
        try
        {
            // Create a new request with the data you want to pass
            $data = [
                'search-keyword'    => '',
                'select-status'     => '1',
                'select-entries'    => '10',
                'select-disability' => -1,
                'temporary_filter'  => true
            ];

            $request = Request::create(route('admin.tutors-filter'), 'POST', $data);

            // Set the session on the request
            $request->setLaravelSession(Session::getFacadeRoot());

            // Call the filterTutors method and pass the request
            $response = app()->call('App\Http\Controllers\AdminController@tutors_filter', ['request' => $request]);

            return $response;
        }
        catch (Exception $e)
        {
            return response()->view('errors.500', [], 500);
        }
    }
}
