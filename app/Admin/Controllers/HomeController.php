<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Utils;
use Carbon\Carbon;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Auth;
use SplFileObject;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        $u = Auth::user();
        $content
            ->title('Company Name - Dashboard')
            ->description('Hello ' . $u->name . "!");
        $u = Admin::user();


        $content->row(function (Row $row) {
            $row->column(3, function (Column $column) {
                $column->append(view('widgets.box-5', [
                    'is_dark' => false,
                    'title' => 'Registered Farmers',
                    'sub_title' => 'Joined 30 days ago.',
                    'number' => number_format(User::count()),
                    'link' => 'javascript:;'
                ]));
            });
            // $row->column(3, function (Column $column) {
            //     $column->append(view('widgets.box-5', [
            //         'is_dark' => false,
            //         'title' => 'Registered Gardens',
            //         'sub_title' => 'All time.',
            //         'number' => number_format(Garden::count()),
            //         'link' => 'javascript:;'
            //     ]));
            // });
            $row->column(3, function (Column $column) {
                $column->append(view('widgets.box-5', [
                    'is_dark' => false,
                    'title' => 'Garden Activities',
                    'sub_title' => 'From System',
                    'number' => number_format(User::count()),
                    'link' => 'javascript:;'
                ]));
            });
            $row->column(3, function (Column $column) {
                $column->append(view('widgets.box-5', [
                    'is_dark' => false,
                    'title' => 'Production Guides',
                    'sub_title' => 'From system',
                    'number' => number_format(User::count()),
                    'link' => 'javascript:;'
                ]));
            });
            $row->column(3, function (Column $column) {
                $column->append(view('widgets.box-5', [
                    'is_dark' => false,
                    'title' => 'Weather',
                    'sub_title' => 'Weather API',
                    'number' => 20 . '&#176;C',
                    'link' => 'javascript:;'
                ]));
            });
        });


        return $content;
    }

    public function calendar(Content $content)
    {


        $u = Auth::user();
        $content
            ->title('Calendar');


        $content->row(function (Row $row) {

            $row->column(8, function (Column $column) {

                $column->append(Dashboard::dashboard_calender());
            });
        });
        return $content;


        return $content;
    }
}
