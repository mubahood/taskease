<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

            /*             if (
                $model->manager_submission_status == null ||
                strlen($model->manager_submission_status) < 2
            ) {

            } */
            $model->manager_submission_status = 'Not Submitted';
            $model->delegate_submission_status = 'Not Submitted';
            /*             if (
                $model->delegate_submission_status == null ||
                strlen($model->delegate_submission_status) < 2
            ) {

            } */

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

            $model->company_id = auth()->user()->company_id;
            $model->created_by = auth()->user()->id;
            if ($model->assign_to_type == 'to_me') {
                $model->assigned_to = Auth::user()->id;
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
        if ($project_section != null) {
            $model->project_id = $project_section->project_id;
        }
        $assigned_to_user = Administrator::find($model->assigned_to);
        $manager_user = Administrator::find($assigned_to_user->managed_by);
        if ($manager_user != null) {
            $model->manager_id = $manager_user->id;
        } else {
            $model->manager_id = $assigned_to_user->id;
        }

        if (
            $model->manager_submission_status != 'Not Submitted' &&
            $model->delegate_submission_status != 'Not Submitted'
        ) {
            $model->is_submitted = 'Yes';
        }else{
            $model->is_submitted = 'No';
        } 
        return $model;
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
