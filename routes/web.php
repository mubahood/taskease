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
    die(Gen::find($_GET['id'])->do_get());
})->name("register");
