<?php

namespace App\Services;

use App\Mail\UserTerminationMail;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\FieldNames\DocProofFields;

class AdminActionsService
{
    public function terminateUser(Request $request, int $userType)
    {
        $validator = Validator::make($request->only('userid'), [
            'userid' => 'required|string'
        ]);

        if ($validator->fails() || empty($userType))
        {
            session()->flash('alert_error', "The requested action can't be processed due to an error.");
            return redirect()->back();
        }

        try
        {
            DB::beginTransaction();

            $hashedId  = $request->userid;
            $userId = $userType == User::ROLE_LEARNER
                    ? LearnerSvc::toRawId($hashedId)
                    : TutorSvc::toRawId($hashedId);

            $user = User::with('profile')->findOrFail($userId);

            if ($user->{UserFields::Role} != $userType)
            {
                throw new ModelNotFoundException();
            }

            $user->delete();

            $deleteFiles = [];

            if ($user->{UserFields::Role} == User::ROLE_TUTOR)
            {
                $education = $user->profile->{ProfileFields::Education};

                if ($education)
                {
                    foreach ($education as $educationItem)
                    {
                        $deleteFiles[] = $educationItem[DocProofFields::FullPath];
                    }
                }

                $certification = $user->profile->{ProfileFields::Certifications};

                if ($certification)
                {
                    foreach ($certification as $certificationItem)
                    {
                        $deleteFiles[] = $certificationItem[DocProofFields::FullPath];
                    }
                }

                $workExp = $user->profile->{ProfileFields::Experience};

                if ($workExp)
                {
                    foreach ($workExp as $workExpItem)
                    {
                        $deleteFiles[] = $workExpItem[DocProofFields::FullPath];
                    }
                }
            }

            // Add the profile picture to the list of files to delete
            $profilePicture = $user->{UserFields::Photo};

            if ($profilePicture) {
                $deleteFiles[] = "public/uploads/profiles/$profilePicture";
            }

            // Delete all files in the $deleteFiles array using Storage::delete
            foreach ($deleteFiles as $file) {
                Storage::delete($file);
            }

            // Notify the user via email
            if (config('app.feature_send_mails'))
            {
                // Disable AVAST Mail Shield "Outbound SMTP" before sending emails
                $emailData = [
                    'firstname' => $user->{UserFields::Firstname},
                    'logo'      => public_path('assets/img/logo-brand-sm.png')
                ];

                Mail::to($user->email)->send(new UserTerminationMail($emailData));
            }

            DB::commit();

            // Delete the user's files

            session()->flash('alert_success', "A user account has been successfully terminated.");

            $returnTo = match($userType)
            {
                User::ROLE_LEARNER  => 'admin.learners-index',
                User::ROLE_TUTOR    => 'admin.tutors-index'
            };

            return redirect()->route($returnTo);
        }
        catch(ModelNotFoundException $e)
        {
            session()->flash('alert_error', "Sorry, we're unable to find the user. They may already have been removed.");
            return redirect()->back();
        }
        catch (Exception $ex)
        {
            error_log("STOPPED HERE...");
            error_log($ex->getMessage());
            DB::rollBack();
            session()->flash('alert_error', "Sorry, we're unable to perform the requested action due to a technical error. Please try again later.");
            return redirect()->back();
        }
    }
}
