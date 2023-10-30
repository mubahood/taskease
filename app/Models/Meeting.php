<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    public function setAttendanceListPicturesAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['attendance_list_pictures'] = json_encode($pictures);
        }
    }
    public function getAttendanceListPicturesAttribute($pictures)
    {
        if ($pictures != null)
            return json_decode($pictures, true);
    }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
