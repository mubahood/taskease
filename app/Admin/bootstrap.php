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

$u = Admin::user();

if ($u != null) {

    Utils::system_boot();
    Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {



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
}
