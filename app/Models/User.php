<?php

namespace App\Models;

use Encore\Admin\Form\Field\BelongsToMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as RelationsBelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Encore\Admin\Auth\Database\Administrator;


class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;



    public function send_password_reset()
    {
        $u = $this;
        $u->stream_id = rand(100000, 999999);
        $u->save();
        $data['email'] = $u->email;
        $data['name'] = $u->name;
        $data['subject'] = "TASKEASE - Password Reset";
        $data['body'] = "Dear " . $u->name . ",<br>";
        $data['body'] .= "<br>Please click the link below to reset your password.<br>";
        $data['body'] .= "https://taskeas.com/reset-password?token=" . $u->stream_id . "<br>";
        $data['body'] .= "<br>Thank you.<br>";
        $data['body'] .= "Regards.<br>";
        $data['body'] .= "<br><small>This is an automated message, please do not reply.</small><br>";
        $data['view'] = 'mail-1';
        $data['data'] = $data['body'];
        try {
            Utils::mail_sender($data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function update_rating($id)
    {
        $user = User::find($id);
        $tasks = Task::where('assigned_to', $id)->get();
        $rate = 0;
        $count = 0;
        foreach ($tasks as $task) {
            if ($task->manager_submission_status != 'Not Submitted') {
                $rate += $task->rate;
                $count++;
            }
        }
        if ($count > 0) {
            $rate = $rate / $count;
        }
        $user->rate = $rate;
        $user->save();
    }


    protected $table = "admin_users";

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }
}
