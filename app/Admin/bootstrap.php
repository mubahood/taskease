<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use App\Models\Utils;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\Auth;
use App\Admin\Extensions\Nav\Shortcut;
use App\Admin\Extensions\Nav\Dropdown;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

foreach (Task::all() as $key => $value) {
    $value->hours = rand(1, 10);
    $value->save();
}

$u = Admin::user();

if ($u != null) {

    Utils::system_boot();
    Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {


        $u = Admin::user();
        $links = [];
        $links = [
            'New Task' => admin_url('tasks/create'),
            'New Event' => admin_url('events/create'),
        ];
        if ($u->can('admin')) {
            $links['New Employee'] = admin_url('employees/create');
        }
        $navbar->left(Shortcut::make($links, 'fa-plus')->title('CREATE NEW'));
    });
    Admin::css('/assets/js/calender/main.css');
    Admin::js('/assets/js/calender/main.js');
    Admin::css('/css/jquery-confirm.min.css');
    Admin::js('/assets/js/jquery-confirm.min.js');
    Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {
    });
    Encore\Admin\Form::forget(['map', 'editor']);
    Admin::css(url('/assets/css/bootstrap.css'));
    Admin::css('/css/styles.css');
    Admin::css('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css');

    //remove reset button from form
    Encore\Admin\Show::init(function (Encore\Admin\Show $show) {
        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
            $tools->disableEdit();
        });
    });

    Encore\Admin\Form::init(function (Encore\Admin\Form $form) {
        $form->tools(function ($tools) {
            $tools->disableDelete();
            $tools->disableView();
        });
        $form->disableReset();
        $form->disableViewCheck();
    });
}
