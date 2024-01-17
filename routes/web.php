<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\MainController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\Gen;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Utils;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('project-report', function () {

    $id = $_GET['id'];
    $project = Project::find($id);

    $pdf = App::make('dompdf.wrapper');

    $pdf->loadHTML(view('project-report', [
        'title' => 'project',
        'item' => $project,
    ])->render());
    return $pdf->stream('file_name.pdf');
});

//return view('mail-1');

Route::get('reset-mail', function () {

    //return view('mail-1');
    $u = User::find(1);
    try {
        $u->send_password_reset();
        die('Email sent');
    } catch (\Throwable $th) {
        die($th->getMessage());
    }
});

Route::get('reset-password', function () {
    $u = User::where([
        'stream_id' => $_GET['token']
    ])->first();
    if ($u == null) {
        die('Invalid token');
    }
    return view('auth.reset-password', ['u' => $u]);
});

Route::post('reset-password', function () {
    $u = User::where([
        'stream_id' => $_GET['token']
    ])->first();
    if ($u == null) {
        die('Invalid token');
    }
    $p1 = $_POST['password'];
    $p2 = $_POST['password_1'];
    if ($p1 != $p2) {
        return back()
            ->withErrors(['password' => 'Passwords do not match.'])
            ->withInput();
    }
    $u->password = bcrypt($p1);
    $u->save();

    if (Auth::attempt([
        'email' => $u->email,
        'password' => $p1,
    ], true)) {
        return redirect('dashboard');
        die();
    }
    return back()
        ->withErrors(['password' => 'Failed to login. Try again.'])
        ->withInput();
});

Route::get('auth/login', function () {
    $u = Admin::user();
    if ($u != null) {
        return redirect(url('/'));
    }

    return view('auth.login');
})->name('login');

Route::get('mobile', function () {
    return url('');
});
Route::get('test', function () {
    $m = Meeting::find(1);
});


Route::get('policy', function () {
    return view('policy');
});

Route::get('/gen-form', function () {
    die(Gen::find($_GET['id'])->make_forms());
})->name("gen-form");


Route::get('generate-class', [MainController::class, 'generate_class']);
Route::get('/gen', function () {
    $m = Gen::find($_GET['id']);
    if ($m == null) {
        return "Not found";
    }
    die($m->do_get());
})->name("register");
