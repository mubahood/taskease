<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\MainController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\Gen;
use App\Models\Meeting;
use App\Models\Task;
use App\Models\User;
use App\Models\Utils;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Route::get('test', function () {
    $m = Meeting::find(1);
    $done = [];
    foreach ($m->tasks as $key => $task) {
        if (in_array($task->manager_id, $done)) {
            continue;
        }
        $done[] = $task->manager_id;
    }
    foreach ($done as $key => $d) {
        $u = User::find($d);
        if ($u == null) {
            continue;
        }
        $count_tasks = Task::where([
            'manager_id' => $d,
            'meeting_id' => $m->id,
        ])->count();

        //mail message 
        $message = "Dear " . $u->name . ",\n";
        $message .= "You have " . $count_tasks . " tasks to attend to in the meeting " . $m->name . ".\n";
        $message .= "Please login to the App to attend to them.\n";
        $message .= "Thank you.\n";
        $message .= "Regards,\n";
        $message .= "System Administrator.\n";
        $message .= "This is an automated message, please do not reply.\n";


        $u->email = 'mubahood360@gmail.com';
        $data['email'] = $u->email;
        $data['name'] = $u->name;
        $data['subject'] = "Supervision tasks for meeting " . $m->name . "";
        $data['body'] = $message;
        $data['view'] = 'mail';
        $data['data'] = $message;
        try {
            Utils::mail_sender($data);
            $m->is_sent = 'Sent';
            $m->save();
        } catch (\Throwable $th) {
            try {
                $m->is_sent = 'Failed';
                $m->sent_failed_reason = $th->getMessage();
                $m->save();
            } catch (\Throwable $th) {
            }
            return;
        }
        die("done");
    }
    dd($done);
});
/* 
   "id" => 7
    "created_at" => "2023-10-30 22:09:08"
    "updated_at" => "2023-11-06 13:08:14"
    "company_id" => 2
    "project_id" => 1
    "project_section_id" => 1
    "assigned_to" => 30
    "created_by" => 2
 
    "name" => "Some info.."
    "task_description" => "Some details"
    "due_to_date" => "2023-12-05 05:30:30"
    "delegate_submission_status" => "Done"
    "delegate_submission_remarks" => "Some"
    "manager_submission_status" => "Not Attended To"
    "manager_submission_remarks" => "Some"
    "priority" => "Low"
    "meeting_id" => 1
    "rate" => -6
  ]
*/
Route::get('departmental-workplan', function () {

    $id = 0;
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    }

    $u = User::find($id);
    if ($u == null) {
        die("User not found");
    }
    //set file name to name of department and date (dompdf) 

    $tasks_tot = 0;
    $tasks_not_submited = 0;
    $tasks_submited = 0;
    $tasks_done = 0;
    $tasks_done_late = 0;
    $tasks_missed = 0;
    $tasks_tot = count($u->tasks);
    foreach ($u->tasks as $key => $task) {
        if ($task->manager_submission_status == 'Not Submitted') {
            $tasks_not_submited++;
        } else {
            $tasks_submited++;
            if ($task->manager_submission_status == 'Done') {
                $tasks_done++;
            } else if ($task->manager_submission_status == 'Done Late') {
                $tasks_done_late++;
            } else if ($task->manager_submission_status == 'Not Attended To') {
                $tasks_missed++;
            }
        }
    }

    if ($tasks_submited > 0) {
        $tasks_done .= " (" . round(($tasks_done / $tasks_submited) * 100, 0) . "%)";
        $tasks_done_late .= " (" . round(($tasks_done_late / $tasks_submited) * 100, 0) . "%)";
        $tasks_missed .= " (" . round(($tasks_missed / $tasks_submited) * 100, 0) . "%)";
    }

    if ($tasks_tot > 0) {
        $tasks_not_submited .= " (" . round(($tasks_not_submited / $tasks_tot) * 100, 0) . "%)";
        $tasks_submited .= " (" . round(($tasks_submited / $tasks_tot) * 100, 0) . "%)";
    }



    /* 
        'Done' => 'Done',
        'Done Late' => '',
        'Not Attended To' => 'Not Attended To',

        "id" => 1
        "created_at" => "2023-10-25 23:23:14"
        "updated_at" => "2023-10-25 23:23:14"
        "company_id" => 1
        "project_id" => null
        "project_section_id" => null
        "assigned_to" => 1
        "created_by" => 1
        "manager_id" => 1
        "name" => "This is a simple tes"
        "task_description" => "Spome das'"
        "due_to_date" => "2023-10-09 00:00:00"
        "delegate_submission_status" => "Not Submitted"
        "delegate_submission_remarks" => null
        "manager_submission_status" => "Not Submitted"
        "manager_submission_remarks" => null
        "priority" => "Medium"
        "meeting_id" => 6


    
    */

    $title = $u->name . " " . date("Y-m-d H:i:s");
    $file_name = $title . ".pdf";
    $pdf = App::make('dompdf.wrapper', ['enable_remote' => true, 'enable_html5_parser' => true, 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);



    $pdf->setPaper('A4', 'portrait');
    $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'open-sans']);
    $pdf->setOptions(['isPhpEnabled' => true, 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

    $pdf->loadHTML(view('departmental-workplan', [
        'user' => $u,
        'title' => $title,
        'tasks_tot' => $tasks_tot,
        'tasks_missed' => $tasks_missed,
        'tasks_done_late' => $tasks_done_late,
        'tasks_done' => $tasks_done,
        'tasks_not_submited' => $tasks_not_submited,
        'tasks_submited' => $tasks_submited,
    ])->render());
    return $pdf->stream($file_name);
});



Route::get('policy', function () {
    return view('policy');
});

Route::get('/gen-form', function () {
    die(Gen::find($_GET['id'])->make_forms());
})->name("gen-form");


Route::get('generate-class', [MainController::class, 'generate_class']);
Route::get('/gen', function () {
    die(Gen::find($_GET['id'])->do_get());
})->name("register");
 

/* 
x
Route::get('generate-variables', [MainController::class, 'generate_variables']); 
Route::get('/', [MainController::class, 'index'])->name('home');
Route::get('/about-us', [MainController::class, 'about_us']);
Route::get('/our-team', [MainController::class, 'our_team']);
Route::get('/news-category/{id}', [MainController::class, 'news_category']);
Route::get('/news-category', [MainController::class, 'news_category']);
Route::get('/news', [MainController::class, 'news_category']);
Route::get('/news/{id}', [MainController::class, 'news']);
Route::get('/members', [MainController::class, 'members']);
Route::get('/dinner', [MainController::class, 'dinner']);
Route::get('/ucc', function(){ return view('chair-person-message'); });
Route::get('/vision-mission', function(){ return view('vision-mission'); }); 
Route::get('/constitution', function(){ return view('constitution'); }); 
Route::get('/register', [AccountController::class, 'register'])->name('register');

Route::get('/login', [AccountController::class, 'login'])->name('login')
    ->middleware(RedirectIfAuthenticated::class);

Route::post('/register', [AccountController::class, 'register_post'])
    ->middleware(RedirectIfAuthenticated::class);

Route::post('/login', [AccountController::class, 'login_post'])
    ->middleware(RedirectIfAuthenticated::class);


Route::get('/dashboard', [AccountController::class, 'dashboard'])
    ->middleware(Authenticate::class);


Route::get('/account-details', [AccountController::class, 'account_details'])
    ->middleware(Authenticate::class);

Route::post('/account-details', [AccountController::class, 'account_details_post'])
    ->middleware(Authenticate::class);

Route::get('/logout', [AccountController::class, 'logout']);
 */