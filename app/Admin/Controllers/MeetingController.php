<?php

namespace App\Admin\Controllers;

use App\Models\Meeting;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class MeetingController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Meetings';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Meeting());

        $grid->column('id', __('Id'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('company_id', __('Company id'));
        $grid->column('created_by', __('Created by'));
        $grid->column('name', __('Name'));
        $grid->column('details', __('Details'));
        $grid->column('minutes_of_meeting', __('Minutes of meeting'));
        $grid->column('location', __('Location'));
        $grid->column('location_gps_latitude', __('Location gps latitude'));
        $grid->column('location_gps_longitude', __('Location gps longitude'));
        $grid->column('meeting_start_time', __('Meeting start time'));
        $grid->column('meeting_end_time', __('Meeting end time'));
        $grid->column('attendance_list_pictures', __('Attendance list pictures'));
        $grid->column('members_pictures', __('Members pictures'));
        $grid->column('attachments', __('Attachments'));
        $grid->column('members_present', __('Members present'));
        $grid->column('other_data', __('Other data'));

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
        $show = new Show(Meeting::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('company_id', __('Company id'));
        $show->field('created_by', __('Created by'));
        $show->field('name', __('Name'));
        $show->field('details', __('Details'));
        $show->field('minutes_of_meeting', __('Minutes of meeting'));
        $show->field('location', __('Location'));
        $show->field('location_gps_latitude', __('Location gps latitude'));
        $show->field('location_gps_longitude', __('Location gps longitude'));
        $show->field('meeting_start_time', __('Meeting start time'));
        $show->field('meeting_end_time', __('Meeting end time'));
        $show->field('attendance_list_pictures', __('Attendance list pictures'));
        $show->field('members_pictures', __('Members pictures'));
        $show->field('attachments', __('Attachments'));
        $show->field('members_present', __('Members present'));
        $show->field('other_data', __('Other data'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Meeting());

        $u = auth()->user();
        /* 
    "id" => 2
    "username" => "admin"
    "password" => "$2y$10$f2FPnbmzIyxnySGb1mcjnOJPExTBD81plzS2b68pEceGV6QqrHFPS"
    "name" => "Admin User"
    "avatar" => "images/5912dac0-a754-4c8e-80f0-c8f7d68a72fd.jpeg"
    "remember_token" => "xKMg592KTMF3UWDtCOFypjuXyGmJ4JFsREktYFOYF1AOsvxpKt0gYbHBYx8f"
    "created_at" => "2023-09-27 17:02:28"
    "updated_at" => "2023-10-14 18:19:44"
    "enterprise_id" => 1
    "first_name" => "Admin"
    "last_name" => "User"
    "date_of_birth" => "2023-09-27"
    "place_of_birth" => "Consequat Voluptati"
    "sex" => "Female"
    "home_address" => "Qui vitae unde eum q"
    "current_address" => "Aut voluptate anim v"
    "phone_number_1" => "+1 (437) 915-8734"
    "phone_number_2" => "+1 (513) 549-7409"
    "email" => "admin@gmail.com"
    "nationality" => "Quo architecto eveni"
    "religion" => "Fugit molestiae nul"
    "spouse_name" => "Cheyenne Velasquez"
    "spouse_phone" => "+1 (637) 864-5592"
    "father_name" => "Naida Vance"
    "father_phone" => "+1 (946) 869-4363"
    "mother_name" => "Clark Cobb"
    "mother_phone" => "+1 (886) 997-4466"
    "languages" => "Quis ex commodo saep"
    "emergency_person_name" => "Nolan Moses"
    "emergency_person_phone" => "+1 (455) 477-2441"
    "national_id_number" => "599"
    "passport_number" => "397"
    "tin" => "253"
    "nssf_number" => "127"
    "bank_name" => "Kamal Acevedo"
    "bank_account_number" => "1"
    "primary_school_name" => "Odessa Vincent"
    "primary_school_year_graduated" => "2006"
    "seconday_school_name" => "Calvin Gardner"
    "seconday_school_year_graduated" => "2014"
    "high_school_name" => "Lee Ratliff"
    "high_school_year_graduated" => "1991"
    "degree_university_name" => "Nigel Valencia"
    "degree_university_year_graduated" => "2018"
    "masters_university_name" => "Chaney Ortega"
    "masters_university_year_graduated" => "1989"
    "phd_university_name" => "Lacey Wolf"
    "phd_university_year_graduated" => "1989"
    "user_type" => "employee"
    "demo_id" => 0
    "user_id" => null
    "user_batch_importer_id" => 0
    "school_pay_account_id" => null
    "school_pay_payment_code" => null
    "given_name" => null
    "deleted_at" => null
    "marital_status" => null
    "verification" => 0
    "current_class_id" => 0
    "current_theology_class_id" => 0
    "status" => 2
    "parent_id" => null
    "main_role_id" => null
    "stream_id" => null
    "account_id" => null
    "has_personal_info" => "Yes"
    "has_educational_info" => "Yes"
    "has_account_info" => "Yes"
    "diploma_school_name" => "Colby Chaney"
    "diploma_year_graduated" => "1976"
    "certificate_school_name" => "Alisa Watts"
    "certificate_year_graduated" => "1987"
    "company_id" => 2
    "managed_by" => null
    "title" => null
    "dob" => null
    "intro" => null
*/

        $form->hidden('company_id', __('Company id'))->default($u->company_id);
        $form->hidden('created_by', __('Created by'))->default($u->id);
        $form->text('name', __('Meeting Title'))->rules('required');
        $form->textarea('details', __('Details'));
        $form->textarea('minutes_of_meeting', __('Minutes of meeting'));
        $form->textarea('location', __('Location'));
        $form->textarea('location_gps_latitude', __('Location gps latitude'));
        $form->textarea('location_gps_longitude', __('Location gps longitude'));
        $form->textarea('meeting_start_time', __('Meeting start time'));
        $form->textarea('meeting_end_time', __('Meeting end time'));
        $form->textarea('attendance_list_pictures', __('Attendance list pictures'));
        $form->textarea('members_pictures', __('Members pictures'));
        $form->textarea('attachments', __('Attachments'));
        $form->textarea('members_present', __('Members present'));
        $form->textarea('other_data', __('Other data'));

        return $form;
    }
}
