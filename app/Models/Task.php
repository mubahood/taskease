<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    //fillables
    protected $fillable = [
        'company_id',
        'project_id',
        'project_section_id',
        'assigned_to',
        'created_by',
        'manager_id',
        'name',
        'task_description',
        'due_to_date',
        'delegate_submission_status',
        'delegate_submission_remarks',
        'manager_submission_status',
        'manager_submission_remarks',
        'priority',
        'meeting_id',
    ];


    static public function boot()
    {
        parent::boot();

        static::created(function ($model) {
            User::update_rating($model->assigned_to);
            Project::update_progress($model->project_id);
        });
        static::updated(function ($model) {
            User::update_rating($model->assigned_to);
            Project::update_progress($model->project_id);
        });
        static::deleted(function ($model) {
            User::update_rating($model->assigned_to);
            Project::update_progress($model->project_id);
        });

        static::creating(function ($model) {

            if (
                $model->manager_submission_status == null ||
                strlen($model->manager_submission_status) < 2
            ) {
                $model->manager_submission_status = 'Not Submitted';
            }

            if (
                $model->delegate_submission_status == null ||
                strlen($model->delegate_submission_status) < 2
            ) {
                $model->delegate_submission_status = 'Not Submitted';
            }

            $model->rate = 0;
            if ($model->manager_submission_status == 'Not Submitted') {
                $model->rate = 0;
            } else if ($model->manager_submission_status == 'Done') {
                $model->rate = 6;
            } else if ($model->manager_submission_status == 'Done Late') {
                $model->rate = 3;
            } else if ($model->manager_submission_status == 'Not Attended To') {
                $model->rate = -6;
            }

            return Task::prepare_saving($model);
        });
        static::updating(function ($model) {
            $model->rate = 0;
            if (
                $model->manager_submission_status == null ||
                strlen($model->manager_submission_status) < 2
            ) {
                $model->manager_submission_status = 'Not Submitted';
            }
            if (
                $model->delegate_submission_status == null ||
                strlen($model->delegate_submission_status) < 2
            ) {
                $model->delegate_submission_status = 'Not Submitted';
            }
            if ($model->manager_submission_status == 'Not Submitted') {
                $model->rate = 0;
            } else if ($model->manager_submission_status == 'Done') {
                $model->rate = 6;
            } else if ($model->manager_submission_status == 'Done Late') {
                $model->rate = 3;
            } else if ($model->manager_submission_status == 'Not Attended To') {
                $model->rate = -6;
            }
            return Task::prepare_saving($model);
        });
    }

    public static function prepare_saving($model)
    {
        $project_section = ProjectSection::find($model->project_section_id);
        if ($project_section == null) {
            return;
        }
        $assigned_to_user = Administrator::find($model->assigned_to);
        if ($assigned_to_user == null) {
            return;
            throw new \Exception("Assigned to user not found");
        }
        if ($assigned_to_user->manager_id  == null) {
            $model->manager_id = $assigned_to_user->id;
        } else {
            $model->manager_id = $assigned_to_user->manager_id;
        }
        $model->project_id = $project_section->project_id;
    }


    public function assigned_to_user()
    {
        return $this->belongsTo(Administrator::class, 'assigned_to');
    }
    public function created_by_user()
    {
        return $this->belongsTo(Administrator::class, 'created_by');
    }
    public function manager_user()
    {
        return $this->belongsTo(Administrator::class, 'manager_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function project_section()
    {
        return $this->belongsTo(ProjectSection::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
