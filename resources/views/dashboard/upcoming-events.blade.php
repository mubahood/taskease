<?php
use App\Models\Utils;
?>{{-- 
  "id" => 12
    "created_at" => "2023-10-27 23:40:25"
    "updated_at" => "2023-10-30 22:26:08"
    "company_id" => 1
    "created_by" => 1
    "name" => "Some data"
    "details" => "Some details"
    "minutes_of_meeting" => null
    "location" => "Some Venue"
    "location_gps_latitude" => null
    "location_gps_longitude" => null
    "meeting_start_time" => "2023-10-28 14:55:25"
    "meeting_end_time" => "2023-10-28 14:55:25"
    "attendance_list_pictures" => "["files\/System Development Agreement.pdf"]"
    "members_pictures" => null
    "attachments" => null
    "members_present" => null
    "other_data" => "Some venue.."
--}}
<div class="card mb-4 mb-md-5 border-0 ">
    <div class="card-header p-0 bg-primary rounded-top "
        style="border-top-left-radius: 1rem !important; border-top-right-radius: 1rem !important;">
        <h3 class="px-4 pb-2  text-white py-4 fs-20 fw-700"><b>This Week's Meetings</b></h3>
    </div>
    <div class="card-body p-0 ">
        <div class="list-group list-group-flush p-0">
            @foreach ($items as $item)
                <hr class="p-0 m-0">
                <a href="{{ admin_url('events/' . $item->id) }}" target="_blank" title="View Event Details"
                    class="list-group-item list-group-item-action flex-column align-items-start py-2">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><b></b></h5>
                        <small><b class="text-primay">{{ Utils::my_date_time_1($item->created_at) }}</b></small>
                    </div>
                    <p class="mb-1">
                        {{ $item->name }}
                    </p>
                    <small class="text-muted">{{ $item->location }}</small>
                </a>
            @endforeach
        </div>
    </div>
</div>
