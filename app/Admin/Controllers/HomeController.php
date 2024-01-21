<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Utils;
use Carbon\Carbon;
use Dflydev\DotAccessData\Util;
use Encore\Admin\Auth\Database\Administrator;
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
        $u = Admin::user();
        Admin::style('.content-header{display:none;} .content-wrapper{background-color:#F8F9FA;}');

        $content->row(function (Row $row) {
            $row->column(12, function (Column $column) {
                $u = Admin::user();
                $man = Utils::manifest($u);
                $column->append(view('dashboard.main', [
                    'man' => $man,
                    'company' => $u->company,
                    'greet' =>  Utils::greet() . " " . '<b>' . $u->last_name . '!</b>',
                ]));
            });
        });
        return $content;

        $man = Utils::manifest($u);
        $admin = Auth::user();

        //$faker = Faker::create();

        //example list of dental appointment titles
        /* 
        $dental_appointment_titles = [
            "Teeth Cleaning",
            "Cavity Filling",
            "Root Canal",
            "Tooth Extraction",
            "Braces Adjustment",
            "Teeth Whitening",
            "Dental Implant Consultation",
            "Gum Disease Treatment",
            "Oral Surgery Consultation",
            "Orthodontic Consultation"
        ];
        $admins = Administrator::all();
        foreach (Event::all() as $key => $event) {

            $event->created_at = $faker->dateTimeBetween('-1 months', 'now');
            $event->updated_at = $faker->dateTimeBetween('-1 months', 'now');
            $event->administrator_id = $admins[rand(0, (count($admins)-1))]->id;
            $event->reminder_state = $faker->randomElement(['On', 'Off']);
            $event->priority = $faker->randomElement(['Low', 'Medium', 'High']);
            $event->event_date = $faker->dateTimeBetween('-1 months', '+1 months');
            $event->reminder_date = $faker->dateTimeBetween('-1 months', '+1 months');
            $event->description = $faker->text(200);
            $event->company_id = $admin->company_id;
            $event->remind_beofre_days = $faker->randomElement([1, 2, 3, 4, 5, 6, 7]);
            $event->name = $faker->randomElement($dental_appointment_titles);
            $event->event_conducted = $faker->randomElement([
                'Pending' => 'Pending',
                'Conducted' => 'Conducted',
                'Cancelled' => 'Cancelled',
            ]); 
            $event->save();
        }
 */
        $u = Admin::user();

        $content
            ->title('<b>' . Utils::greet() . " " . $u->last_name . '!</b>');

        $content->row(function (Row $row) {
            $row->column(6, function (Column $column) {
                $u = Admin::user();
                $man = Utils::manifest($u);
                $column->append(view('widgets.dashboard-segment-1', [
                    'tasks_done' => $man->tasks_done,
                    'tasks_missed' => $man->tasks_missed,
                    'tasks_not_submitted' => $man->tasks_pending,
                    'events' => Event::where(
                        [
                            'company_id' => $u->company_id
                        ]
                    )->limit(5)->get(),
                    'tasks' => $man->tasks_pending_items->take(6),
                ]));
            });
            $row->column(6, function (Column $column) {
                $column->append(Dashboard::dashboard_calender());
            });
        });

        return $content;
    }

    public function calendar(Content $content)
    {
        $u = Auth::user();
        $content
            ->title('Company Calendar');
        $content->row(function (Row $row) {
            $row->column(8, function (Column $column) {
                $column->append(Dashboard::dashboard_calender());
            });
            $row->column(4, function (Column $column) {
                $u = Admin::user();
                $column->append(view('dashboard.upcoming-events', [
                    'items' => Meeting::where([])
                        ->where([/* 'event_date', '>=', Carbon::now()->format('Y-m-d') */])
                        ->orderBy('id', 'desc')->limit(8)->get()
                ]));
            });
        });
        return $content;
    }
}
