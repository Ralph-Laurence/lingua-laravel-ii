<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TutorController;
use App\Http\Utils\ChatifyUtils;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::controller(HomeController::class)->group(function()
{
    Route::get('/', 'index');
    Route::get('/signlingua', function() {
        return redirect('/');
    });
});
//
// Override this so that after login we redirect to the approrpriate route
//
Route::get('/dashboard', function ()
{
    // return view('dashboard');

    if (Auth::check())
    {
        $role = Auth::user()->{UserFields::Role};

        if ($role == User::ROLE_ADMIN)
        {
            return redirect()->route('admin.dashboard');
        }
        else
        {
            return redirect()->to('/');
        }
    }

    return redirect()->to('/');

})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

$RoleMw = 'role-mw:'; // Role middleware

Route::controller(MyProfileController::class)
    ->prefix('/signlingua/my-profile')
    ->middleware(['auth', 'ensureNotPending'])
    ->group(function()
{
    Route::get('/edit',                     'index')->name('myprofile.edit');
    Route::put('/update-photo',             'updatePhoto')->name('myprofile.update-photo');
    Route::post('/remove-photo',            'removePhoto')->name('myprofile.remove-photo');
    Route::post('/update-password',         'updatePassword')->name('profile.update-password');
    Route::post('/update-identity',         'updateIdentity')->name('profile.update-identity');
    Route::post('/update-account',          'updateAccount')->name('profile.update-account');
    Route::post('/update-bio',              'updateBio')->name('profile.update-bio');
    Route::post('/update-about',            'updateAbout')->name('profile.update-about');
    Route::post('/update-disability',       'updateDisability')->name('myprofile.update-disability');

    Route::post('/revert-email',            'revertEmailUpdate')->name('myprofile.revert-email');
    Route::get('/confirm-email/{id}',       'showEmailConfirmation')->name('myprofile.email-confirmation');
    Route::post('/confirm-email',           'confirmEmailUpdate')->name('myprofile.confirm-email');

    Route::get('/fetch-education',          'fetchEducation')->name('myprofile.fetch-education');
    Route::post('/add-education',           'addEducation')->name('myprofile.add-education');
    Route::post('/update-education',        'updateEducation')->name('myprofile.update-education');
    Route::post('/remove-education',        'removeEducation')->name('myprofile.remove-education');

    Route::get('/fetch-workexp',            'fetchWorkExp')->name('myprofile.fetch-workexp');
    Route::post('/add-workexp',             'addWorkExp')->name('myprofile.add-work-exp');
    Route::post('/update-workexp',          'updateWorkExp')->name('myprofile.update-work-exp');
    Route::post('/remove-workexp',          'removeWorkExp')->name('myprofile.remove-work-exp');

    Route::get('/fetch-certification',      'fetchCertification')->name('myprofile.fetch-certification');
    Route::post('/add-certification',       'addCertification')->name('myprofile.add-certification');
    Route::post('/update-certification',    'updateCertification')->name('myprofile.update-certification');
    Route::post('/remove-certification',    'removeCertification')->name('myprofile.remove-certification');

    Route::post('/remove-skills',           'removeSkills')->name('myprofile.remove-skills');
    Route::post('/update-skills',           'updateSkills')->name('myprofile.update-skills');
    Route::post('/add-skills',              'addSkills')->name('myprofile.add-skills');
});

Route::middleware(['auth', $RoleMw . User::ROLE_ADMIN])->group(function ()
{
    Route::controller(AdminController::class)->group(function()
    {
        Route::get('/admin/dashboard',                          'index')->name('admin.dashboard');
        Route::get('/admin/tutors',                             'tutors_index')->name('admin.tutors-index');
        Route::get('/admin/tutors/filter/clear',                'tutors_clear_filter')->name('admin.tutors-clear-filter');
        Route::get('/admin/tutors/review/{id}',                 'tutors_review_registration')->name('admin.tutors-review-registration');
        Route::get('/admin/tutors/registration/approve/{id}',   'tutors_approve_registration')->name('admin.tutors-approve-registration');
        Route::get('/admin/tutors/registration/decline/{id}',   'tutors_decline_registration')->name('admin.tutors-decline-registration');
        Route::get('/admin/tutors/details/{id}',                'tutors_show')->name('admin.tutors-show');
        Route::get('/admin/tutors/pending',                     'dashboard_view_pending_registration')->name('admin.dashboard.view-pending');
        Route::post('/admin/tutors/filter',                     'tutors_filter')->name('admin.tutors-filter');

        Route::get('/admin/learners/list',                      'learners_index')->name('admin.learners-index');
        Route::get('/admin/learners/filter/clear',              'learners_clear_filter')->name('admin.learners-clear-filter');
        Route::get('/admin/learners/details/{id}',              'learners_show')->name('admin.learners-show');
        Route::post('/admin/learners/filter',                   'learners_filter')->name('admin.learners-filter');

        Route::post('/admin/terminate/tutor',                   'terminate_tutor')->name('admin.terminate-tutor');
        Route::post('/admin/terminate/learner',                   'terminate_learner')->name('admin.terminate-learner');
    });
});

Route::controller(LearnerController::class)->prefix('/signlingua/learner')->group(function() use ($RoleMw)
{
    // The default user role is Learner. Pending or unregistered tutors are
    // given a role of "learner", meaning they have access to learner routes.
    // We have to make sure they can't access any learner and tutor routes
    // unless their profile has been verified, thus "ensureNotPending" .
    Route::middleware(['auth', 'ensureNotPending', $RoleMw . User::ROLE_LEARNER])->group(function()
    {
        Route::get('/find-tutors',                  'find_tutors')->name('learner.find-tutors');
        Route::get('/find-tutors/filter',           'filterTutors')->name('learner.find-filtered-tutors');
        Route::get('/find-tutors/clear',            'clearFilterTutors')->name('learner.find-tutors-clear');
        Route::get('/my-tutors',                    'myTutors')->name('mytutors');
        Route::get('/become-tutor',                 'becomeTutor_index')->name('become-tutor');
        Route::get('/become-tutor/forms',           'becomeTutor_create')->name('become-tutor.forms');

        Route::post('/hire-tutor',                  'hireTutor')->name('learner.hire-tutor');
        Route::post('/cancel-hire-tutor',           'cancelHireTutor')->name('learner.cancel-hire-tutor');
        Route::post('/become-tutor/forms/submit',   'becomeTutor_store')->name('become-tutor.forms.submit');
        Route::post('/rate-tutor',                  'storeTutorReview')->name('tutor.store-review');
        Route::post('/delete-review',               'deleteTutorReview')->name('tutor.delete-review');
    });

    Route::middleware('guest')->group(function()
    {
        Route::get('/register',  'registerLearner_create')->name('learner.register');
        Route::post('/register', 'registerLearner_store')->name('learner.register-submit');
    });

    // Move this to API routes
    Route::middleware(['auth'])->group(function()
    {
        Route::get('/fetch/show', 'fetchLearnerDetails')->name('learner.fetch-details');
    });

    Route::get('/become-tutor/success', 'becomeTutor_success')->name('become-tutor.success');
});

Route::controller(TutorController::class)->group(function() use ($RoleMw)
{
    Route::prefix('/signlingua/learner')->middleware(['auth', 'ensureNotPending', $RoleMw . User::ROLE_LEARNER])
    ->group(function()
    {
        Route::get('/tutor-details/{id}',   'show')->name('tutor.show');
        Route::post('/leave-tutor',         'endContract')->name('tutor.end');
    });

    Route::prefix('/signlingua/tutor')->middleware(['auth', 'ensureNotPending', $RoleMw . User::ROLE_TUTOR])
    ->group(function()
    {
        Route::get('/find-learners',        'find_learners')->name('tutor.find-learners');
        Route::get('/find-learners/clear',  'find_learners_clear_filter')->name('tutor.find-learners.clear');

        Route::get('/my-learners',          'my_learners')->name('tutor.my-learners');
        Route::get('/my-learners/clear',    'my_learners_clear_filter')->name('tutor.my-learners.clear');

        Route::get('/requests',             'hire_requests')->name('tutor.hire-requests');
        Route::post('/requests/accept',     'hire_request_accept')->name('tutor.accept-hire-request');
        Route::post('/requests/decline',    'hire_request_decline')->name('tutor.decline-hire-request');
    });

    Route::middleware(['guest', 'ensureNotPending'])->group(function()
    {
        // Redirects authenticated learners to /sign-lingua/become-tutor
        // if they try to access tutor registration routes.
        Route::get('/signlingua/tutor/register',  'registerTutor_create')
            ->middleware('learnerToTutor')
            ->name('tutor.register');

        Route::post('/signlingua/tutor/register', 'registerTutor_store')
            ->middleware('learnerToTutor')
            ->name('tutor.register-submit');
    });
});

Route::middleware(['auth', $RoleMw . User::ROLE_TUTOR])->group(function ()
{
    Route::get('/tutor', function (){
        return view('tutor.dashboard');
    });
});

Route::get('/chat-hash/{id}', function (Request $request, $id) {
    // $id = 'Z2ornMK8kL';
    echo ChatifyUtils::toHashedChatId($id);
});

// Route::get('/generate-password', function () {
//     $password = 'Joy_021428';
//     $hashedPassword = Hash::make($password);
//     echo $hashedPassword;
// });

// use App\Mail\TestMail;
// use Illuminate\Support\Facades\Mail;

// Route::get('/send-test-email', function ()
// {
//     // Disable AVAST Mail Shield "Outbound SMTP"
//     Mail::to('bluescreen512@gmail.com')->send(new TestMail());
//     return 'Test email sent!';
// });

// Route::get('/mail', function() {
//     return view('mails.registration-declined');
// });
/*
---------------------------------------------------------------------------
Verb	    URI	                    Typical Method Name	    Route Name
---------------------------------------------------------------------------
GET	        /photos	                index()	                photos.index
GET	        /photos/create	        create()	            photos.create
POST	    /photos	                store()	                photos.store
GET	        /photos/{photo}	        show()	                photos.show
GET	        /photos/{photo}/edit	edit()	                photos.edit
PUT /PATCH	/photos/{photo}	        update()	            photos.update
DELETE	    /photos/{photo}	        destroy()	            photos.destroy
*/

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('view:cache'); // Optional: Precompile views
    return 'Caches cleared!';
});

// Route::get('/symlink', function () {
//     Artisan::call('storage:link');
//     return 'Storage linked!';
// });

Route::get('/symlink', function() {
    if(file_exists(public_path('storage'))) {
        return 'The "public/storage" directory already exists.';
    }
    
    app('files')->link(
        storage_path('app/public'), 
        public_path('storage')
    );
    
    return 'The [public/storage] directory has been linked.';
});

Route::get('/phpinfo', function () {
    return phpinfo();
});
