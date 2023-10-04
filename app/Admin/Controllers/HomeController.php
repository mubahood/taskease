<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Utils;
use Carbon\Carbon;
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
        
        $u = Auth::user();
        
        /*   
place_of_birth	 
home_address	
current_address	
phone_number_1	
phone_number_2	
email	
	
religion	
spouse_name	
spouse_phone	
father_name	
father_phone	
mother_name	
mother_phone	
languages	
emergency_person_name	
emergency_person_phone	
national_id_number	
passport_number	
tin	
nssf_number	
bank_name	
bank_account_number	
primary_school_name	
primary_school_year_graduated	
seconday_school_name	
seconday_school_year_graduated	
high_school_name	
high_school_year_graduated	
degree_university_name	
degree_university_year_graduated	
masters_university_name	
masters_university_year_graduated	
phd_university_name	
phd_university_year_graduated	
user_type	
demo_id	
user_id	
user_batch_importer_id	
school_pay_account_id	
school_pay_payment_code	
given_name	
deleted_at	
marital_status	
verification	
current_class_id	
current_theology_class_id	
status	
parent_id	
main_role_id	
stream_id	
account_id	
has_personal_info	
has_educational_info	
has_account_info	
diploma_school_name	
diploma_year_graduated	
certificate_school_name	
certificate_year_graduated	
company_id	
managed_by	
 */
        $faker = Faker::create();
        for($i = 1; $i< 10; $i++){
            //create a loop of 20 dummy users using Administator model and use above fields 
            $admin = new Administrator();
            $admin->username = $faker->userName;
            $admin->password = bcrypt('password');
            $admin->name = $faker->name;
            $admin->avatar = $faker->imageUrl($width = 640, $height = 480);
            $admin->remember_token = $faker->text($maxNbChars = 200);
            $admin->created_at = $faker->dateTime($max = 'now', $timezone = null);
            $admin->updated_at = $faker->dateTime($max = 'now', $timezone = null);
            $admin->enterprise_id = $u->company_id;
            $admin->company_id = $u->company_id;
            $admin->first_name = $faker->firstName($gender = null);
            $admin->last_name = $faker->lastName;
            $admin->date_of_birth = $faker->dateTime($max = 'now', $timezone = null);
            $admin->sex = ['Male','Female'][rand(0,1)]; 
            $admin->nationality = $faker->country;  
            $admin->phone_number_1 = $faker->phoneNumber;     
            //$admin->save();

        }
        
        dd($u->company_id);
        $content
            ->title('<b>' . Utils::greet() . " " . $u->last_name . '!</b>');
        $u = Admin::user();



        $content->row(function (Row $row) {
            $row->column(6, function (Column $column) {
                $u = Admin::user();
                $column->append(view('widgets.dashboard-segment-1', [
                    'events' => Event::where([
                        'company_id' => $u->company_id,
                    ])->where('event_date', '>=', Carbon::now()->format('Y-m-d'))->orderBy('id', 'desc')->limit(8)->get()
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
                    'items' => Event::where([
                        'company_id' => $u->company_id,
                    ])
                        ->where('event_date', '>=', Carbon::now()->format('Y-m-d'))
                        ->orderBy('id', 'desc')->limit(8)->get()
                ]));
            });
        });
        return $content;


        return $content;
    }
}
