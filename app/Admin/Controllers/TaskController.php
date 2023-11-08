<?php

namespace App\Admin\Controllers;

use App\Models\Task;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TaskController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Task';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Task());
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->equal('assigned_to', __('Assigned To'))->select(\App\Models\User::where('company_id', auth()->user()->company_id)->pluck('name', 'id'));
            $filter->equal('manager_id', __('Supervisor'))->select(\App\Models\User::where('company_id', auth()->user()->company_id)->pluck('name', 'id'));
            $filter->equal('project_id', __('Project'))->select(\App\Models\Project::where('company_id', auth()->user()->company_id)->pluck('name', 'id'));
            $filter->equal('project_section_id', __('Project Section'))->select(\App\Models\ProjectSection::where('company_id', auth()->user()->company_id)->pluck('name', 'id'));
            $filter->equal('priority', __('Priority'))->select([
                'Low' => 'Low',
                'Medium' => 'Medium',
                'High' => 'High',
            ]);
            $filter->equal('delegate_submission_status', __('Delegate Submission Status'))->select([
                'Not Submitted' => 'Not Submitted',
                'Done' => 'Done',
                'Done Late' => 'Done Late',
                'Not Attended To' => 'Not Attended To',
            ]);
            $filter->equal('manager_submission_status', __('Supervisor Submission Status'))->select([
                'Not Submitted' => 'Not Submitted',
                'Done' => 'Done',
                'Done Late' => 'Done Late',
                'Not Attended To' => 'Not Attended To',
            ]);
            $filter->between('due_to_date', __('Due Date'))->date();
        });
        $grid->disableBatchActions();
        $grid->model()->where('company_id', auth()->user()->company_id)
            ->orderBy('id', 'Desc');
        $grid->column('id', __('Id'))->sortable();
        $grid->column('created_at', __('Created'))
            ->display(function ($created_at) {
                return date('d-m-Y', strtotime($created_at));
            })
            ->hide()
            ->sortable();


        $grid->column('name', __('Task'))->sortable();

        $grid->column('assigned_to', __('Assigned To'))
            ->display(function ($assigned_to) {
                $user = $this->assigned_to_user;
                if ($user == null) {
                    return "User not found";
                }
                return $user->name;
            })
            ->sortable();

        $grid->column('due_to_date', __('Due Date'))
            ->display(function ($due_to_date) {
                return Utils::my_date($due_to_date);
            })->sortable();


        $grid->column('delegate_submission_status', __('Delegate Submission'))
            ->label([
                'Not Submitted' => 'default',
                'Done' => 'success',
                'Missed' => 'danger',
            ])->sortable();
        $grid->column('delegate_submission_remarks', __('Delegate Remarks'))
            ->hide();
        $grid->column('manager_submission_status', __('Manager Submission'))
            ->label([
                'Not Submitted' => 'default',
                'Done' => 'success',
                'Missed' => 'danger',
            ])->sortable();
        $grid->column('manager_submission_remarks', __('Manager Remarks'))
            ->sortable();
        $grid->column('task_description', __('Task Details'))
            ->hide();
        $grid->column('project_id', __('Project'))
            ->display(function ($project_id) {
                $project = $this->project;
                if ($project == null) {
                    return "Project not found";
                }
                return $project->name;
            })
            ->sortable();

        $grid->column('priority', __('Priority'))
            ->sortable();

        $grid->column('created_by', __('Created By'))
            ->display(function ($created_by) {
                $user = $this->created_by_user;
                if ($user == null) {
                    return "User not found";
                }
                return $user->name;
            })
            ->hide()
            ->sortable();
        $grid->column('project_section_id', __('Project'))
            ->display(function ($project_section_id) {
                $project_section = $this->project_section;
                if ($project_section == null) {
                    return "Deliverable not found";
                }
                return $project_section->name_text;
            })
            ->hide()
            ->sortable();


        $grid->column('rate', __('Rating'))->sortable();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Task::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('created_at', __('Created'));
        $show->field('assigned_to', __('Assigned To'));
        $show->field('created_by', __('Created by'));
        $show->field('manager_id', __('Manager id'));
        $show->field('name', __('Name'));
        $show->field('task_description', __('Task description'));
        $show->field('due_to_date', __('Due to date'));
        $show->field('delegate_submission_status', __('Delegate submission status'));
        $show->field('delegate_submission_remarks', __('Delegate submission remarks'));
        $show->field('manager_submission_status', __('Manager submission status'));
        $show->field('manager_submission_remarks', __('Manager submission remarks'));
        $show->field('priority', __('Priority'));
        $show->field('meeting_id', __('Meeting id'));
        $show->field('rate', __('Rate'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Task());

        $form->number('company_id', __('Company id'));
        $form->number('project_id', __('Project id'));
        $form->number('project_section_id', __('Project section id'));
        $form->number('assigned_to', __('Assigned to'));
        $form->number('created_by', __('Created by'));
        $form->number('manager_id', __('Manager id'));
        $form->textarea('name', __('Name'));
        $form->textarea('task_description', __('Task description'));
        $form->datetime('due_to_date', __('Due to date'))->default(date('Y-m-d H:i:s'));
        $form->text('delegate_submission_status', __('Delegate submission status'));
        $form->textarea('delegate_submission_remarks', __('Delegate submission remarks'));
        $form->text('manager_submission_status', __('Manager submission status'));
        $form->textarea('manager_submission_remarks', __('Manager submission remarks'));
        $form->text('priority', __('Priority'))->default('Medium');
        $form->number('meeting_id', __('Meeting id'));
        $form->number('rate', __('Rate'));

        return $form;
    }
}
