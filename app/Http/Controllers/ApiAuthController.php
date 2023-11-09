<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Utils;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuthController extends Controller
{

    use ApiResponser;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {

        /* $token = auth('api')->attempt([
            'username' => 'admin',
            'password' => 'admin',
        ]);
        die($token); */
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $query = auth('api')->user();
        return $this->success($query, $message = "Profile details", 200);
    }


    public function users()
    {
        $u = auth('api')->user();
        if ($u == null) {
            return $this->error('Account not found');
        }

        return $this->success(User::where([
            'company_id' => $u->company_id
        ])->get(), $message = "Success", 200);
    }

    public function projects()
    {
        $u = auth('api')->user();
        if ($u == null) {
            return $this->error('Account not found');
        }
        return $this->success(Project::where([
            'company_id' => $u->company_id
        ])
            ->get(), $message = "Success", 200);
    }

    public function tasks()
    {
        $u = auth('api')->user();
        if ($u == null) {
            return $this->error('Account not found');
        }
        return $this->success(Task::where([
            'assigned_to' => $u->id,
        ])
            ->orWhere([
                'manager_id' => $u->id,
            ])
            ->get(), $message = "Success", 200);
    }

    public function tasks_update_status(Request $r)
    {
        $u = auth('api')->user();
        if ($u == null) {
            return $this->error('Account not found');
        }

        if ($r->task_id == null) {
            return $this->error('Task ID is required.');
        }


        $task = Task::find($r->task_id);
        if ($task == null) {
            return $this->error('Task not found. ' . $r->task_id);
        }
        if (strlen($r->delegate_submission_status) > 2) {
            $task->delegate_submission_status = $r->delegate_submission_status;
        }
        if (strlen($r->manager_submission_status) > 2) {
            $task->manager_submission_status = $r->manager_submission_status;
        }
        if (strlen($r->delegate_submission_remarks) > 2) {
            $task->delegate_submission_remarks = $r->delegate_submission_remarks;
        }
        if (strlen($r->manager_submission_remarks) > 2) {
            $task->manager_submission_remarks = $r->manager_submission_remarks;
        }

        try {
            $task->save();
        } catch (\Throwable $th) {
            return $this->error('Failed to update task.');
        }

        return $this->success(null, $message = "Success", 200);
    }





    public function login(Request $r)
    {
        if ($r->username == null) {
            return $this->error('Username is required.');
        }

        if ($r->password == null) {
            return $this->error('Password is required.');
        }

        $r->username = trim($r->username);

        $u = User::where('phone_number_1', $r->username)
            ->orWhere('username', $r->username)
            ->orWhere('id', $r->username)
            ->orWhere('email', $r->username)
            ->first();


        if ($u == null) {
            $phone_number = Utils::prepare_phone_number($r->username);
            if (Utils::phone_number_is_valid($phone_number)) {
                $phone_number = $r->phone_number;
                $u = User::where('phone_number_1', $phone_number)
                    ->orWhere('username', $phone_number)
                    ->orWhere('email', $phone_number)
                    ->first();
            }
        }

        if ($u == null) {
            return $this->error('User account not found.');
        }


        JWTAuth::factory()->setTTL(60 * 24 * 30 * 365);

        $token = auth('api')->attempt([
            'id' => $u->id,
            'password' => trim($r->password),
        ]);


        if ($token == null) {
            return $this->error('Wrong credentials.');
        }



        $u->token = $token;
        $u->remember_token = $token;

        return $this->success($u, 'Logged in successfully.');
    }


    public function register(Request $r)
    {
        if ($r->phone_number_1 == null) {
            return $this->error('Phone number is required.');
        }

        $phone_number = Utils::prepare_phone_number(trim($r->phone_number));


        if (!Utils::phone_number_is_valid($phone_number)) {
            return $this->error('Invalid phone number. ' . $phone_number);
        }

        if ($r->password == null) {
            return $this->error('Password is required.');
        }

        if ($r->name == null) {
            return $this->error('Name is required.');
        }





        $u = Administrator::where('phone_number_1', $phone_number)
            ->orWhere('username', $phone_number)->first();
        if ($u != null) {
            return $this->error('User with same phone number already exists.');
        }

        $user = new Administrator();

        $name = $r->name;

        $x = explode(' ', $name);

        if (
            isset($x[0]) &&
            isset($x[1])
        ) {
            $user->first_name = $x[0];
            $user->last_name = $x[1];
        } else {
            $user->first_name = $name;
        }

        $user->phone_number_1 = $phone_number;
        $user->username = $phone_number;
        $user->reg_number = $phone_number;
        $user->country = $phone_number;
        $user->occupation = $phone_number;
        $user->profile_photo_large = '';
        $user->location_lat = '';
        $user->location_long = '';
        $user->facebook = '';
        $user->twitter = '';
        $user->linkedin = '';
        $user->website = '';
        $user->other_link = '';
        $user->cv = '';
        $user->language = '';
        $user->about = '';
        $user->address = '';
        $user->name = $name;
        $user->password = password_hash(trim($r->password), PASSWORD_DEFAULT);
        if (!$user->save()) {
            return $this->error('Failed to create account. Please try again.');
        }

        $new_user = Administrator::find($user->id);
        if ($new_user == null) {
            return $this->error('Account created successfully but failed to log you in.');
        }
        Config::set('jwt.ttl', 60 * 24 * 30 * 365);

        $token = auth('api')->attempt([
            'username' => $phone_number,
            'password' => trim($r->password),
        ]);

        $new_user->token = $token;
        $new_user->remember_token = $token;
        return $this->success($new_user, 'Account created successfully.');
    }


    public function meetings_post(Request $r)
    {
        $u = auth('api')->user();
        if ($u == null) {
            return Utils::response([
                'status' => 0,
                'code' => 0,
                'message' => "User not found.",
            ]);
        }

        if ($r->gps_latitude == null) {
            return Utils::response([
                'status' => 0,
                'code' => 0,
                'message' => "GPS latitude is required.",
            ]);
        }


        if ($r->resolutions == null) {
            return Utils::response([
                'status' => 0,
                'code' => 0,
                'message' => "Resolutions are required.",
            ]);
        }




        $meeting = new Meeting();
        $meeting->name = $r->gps_latitude;
        $meeting->company_id = $u->company_id;
        $meeting->created_by = $u->id;
        $meeting->minutes_of_meeting = $r->details;
        $meeting->details = $r->details;
        $meeting->meeting_start_time = Carbon::parse($r->created_at);
        $start_date = Carbon::parse($r->start_date);
        $end_date = Carbon::parse($r->end_date);
        $meeting->meeting_start_time = $meeting->meeting_start_time->addHours($start_date->hour)->addMinutes($start_date->minute);
        $meeting->meeting_end_time = $meeting->meeting_start_time->addHours($end_date->hour)->addMinutes($end_date->minute);
        $meeting->other_data = $r->location_text;
        $meeting->location_gps_latitude = $r->location_gps_latitude;

        $images = [];
        foreach (Image::where([
            'parent_id' => $r->id,
        ])->get() as $key => $value) {
            $images[] = 'images/' . $value->src;
        }
        $meeting->attendance_list_pictures = $images;

        $message = "";
        try {
            $meeting->save();
            $_resolutions = [];
            if (strlen($r->resolutions) > 2) {
                try {
                    $_resolutions = json_decode($r->resolutions);
                } catch (\Throwable $th) {
                    return Utils::response([
                        'status' => 0,
                        'code' => 0,
                        'message' => "Failed to parse resolutions.",
                    ]);
                }
            }

            foreach ($_resolutions as $key => $val) {
                $task = new Task();
                $task->company_id = $u->id;
                $task->meeting_id = $meeting->id;
                $task->assigned_to = $val->attribute_5;
                $task->manager_id = $val->attribute_7;
                $task->created_by = $u->id;
                $task->name = $val->attribute_2;
                $task->task_description = $val->attribute_3;
                $task->due_to_date = Carbon::parse($val->attribute_4);
                $task->priority = 'Medium';
                $task->save();
            }
        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return Utils::response([
                'status' => 0,
                'code' => 0,
                'message' => $message,
            ]);
        }


        return Utils::response([
            'status' => 1,
            'code' => 1,
            'message' => 'Meeting created successfully.',
        ]);

        /* 
updated_at
company_id
created_by
name
details
minutes_of_meeting
location
location_gps_latitude
location_gps_longitude
meeting_start_time
meeting_end_time
attendance_list_pictures
members_pictures
attachments
members_present
other_data
        */
    }

    public function upload_media(Request $request)
    {
        $u = auth('api')->user();
        if ($u == null) {
            return Utils::response([
                'status' => 0,
                'code' => 0,
                'message' => "User not found.",
            ]);
        }

        $administrator_id = $u->id;
        if (
            !isset($request->parent_id) ||
            $request->parent_id == null ||
            ((int)($request->parent_id)) < 1
        ) {
            return Utils::response([
                'status' => 0,
                'code' => 0,
                'message' => "Local parent ID is missing.",
            ]);
        }

        if (
            !isset($request->parent_endpoint) ||
            $request->parent_endpoint == null ||
            (strlen(($request->parent_endpoint))) < 3
        ) {
            return Utils::response([
                'status' => 0,
                'code' => 0,
                'message' => "Local parent ID endpoint is missing.",
            ]);
        }



        if (
            empty($_FILES)
        ) {
            return Utils::response([
                'status' => 0,
                'code' => 0,
                'message' => "Files not found.",
            ]);
        }


        $images = Utils::upload_images_2($_FILES, false);
        $_images = [];

        if (empty($images)) {
            return Utils::response([
                'status' => 0,
                'code' => 0,
                'message' => 'Failed to upload files.',
                'data' => null
            ]);
        }


        $msg = "";
        foreach ($images as $src) {

            if ($request->parent_endpoint == 'edit') {
                $img = Image::find($request->local_parent_id);
                if ($img) {
                    return Utils::response([
                        'status' => 0,
                        'code' => 0,
                        'message' => "Original photo not found",
                    ]);
                }
                $img->src =  $src;
                $img->thumbnail =  null;
                $img->save();
                return Utils::response([
                    'status' => 1,
                    'code' => 1,
                    'data' => json_encode($img),
                    'message' => "File updated.",
                ]);
            }


            $img = new Image();
            $img->administrator_id =  $administrator_id;
            $img->src =  $src;
            $img->thumbnail =  null;
            $img->parent_endpoint =  $request->parent_endpoint;
            $img->parent_id =  (int)($request->parent_id);
            $img->size = 0;
            $img->note = '';
            if (
                isset($request->note)
            ) {
                $img->note =  $request->note;
                $msg .= "Note not set. ";
            }

            $online_parent_id = ((int)($request->online_parent_id));
            if (
                $online_parent_id > 0
            ) {
                $animal = Product::find($online_parent_id);
                if ($animal != null) {
                    $img->parent_endpoint =  'Animal';
                    $img->parent_id =  $animal->id;
                } else {
                    $msg .= "parent_id NOT not found => {$request->online_parent_id}.";
                }
            } else {
                $msg .= "Online_parent_id NOT set. => {$online_parent_id} ";
            }

            $img->save();
            $_images[] = $img;
        }
        //Utils::process_images_in_backround();
        return Utils::response([
            'status' => 1,
            'code' => 1,
            'data' => json_encode($_POST),
            'message' => "File uploaded successfully.",
        ]);
    }
}
