<?php

$company = $department->company;
$logo_link = url('storage/' . $company->logo);
// $link = url('css/bootstrap-print.css');
use App\Models\Utils;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @include('css')
    <title>{{ $title }}</title>
</head>

<body>
    <table class="w-100 ">
        <tbody>
            <tr>
                <td style="width: 12%;" class="">
                    <img class="img-fluid" src="{{ $logo_link }}" alt="{{ $company->name }}">
                </td>
                <td class=" text-center">
                    <h1 class="h3 ">{{ $company->name }}</h1>
                    <p class="mt-1">Address {{ $company->address }}, {{ $company->p_o_box }}</p>
                    <p class="mt-0">Website: Email: {{ $company->website }}, Email: {{ $company->email }}</p>
                    <p class="mt-0">Tel: <b>{{ $company->phone_number }}</b> , <b>{{ $company->phone_number_2 }}</b>
                    </p>
                </td>
                <td style="width: 10%;"><br></td>
            </tr>
        </tbody>
    </table>
    <hr style="border-width: 4px; color: {{ $company->color }}; border-color: {{ $company->color }};"
        class="mt-3 mb-1">
    <hr style="border-width: 3px; color: black; border-color: black;" class="mb-3 mt-0">

    <p class="text-center fw-600 fs-24 mt-4"><u>{{ $department->name }} Weekly Workplan and Report</u></p>

    <p class="text-right mt-3 fw-600">As On {{ Utils::my_date(now()) }}</p>

    <p class="fs-20 fw-600 mt-2">1. Workplans</p>
    <p class="fs-18 mt-2">1.1 Bwambale Muhidin</p>
    <table>
        <thead>
            <tr>
                <th class="border-bottom border-top border-left border-right">Project</th>
                <th class="border-bottom border-top border-left border-right">Section</th>
                <th class="border-bottom border-top border-left border-right">Assigned To</th>
                <th class="border-bottom border-top border-left border-right">Task</th>
                <th class="border-bottom border-top border-left border-right">Description</th>
                <th class="border-bottom border-top border-left border-right">Due To</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($workplans as $workplan)
                <tr>
                    <td class="border-bottom border-left border-right">{{ $workplan->project->name }}</td>
                    <td class="border-bottom border-left border-right">{{ $workplan->projectSection->name }}</td>
                    <td class="border-bottom border-left border-right">{{ $workplan->assignedTo->name }}</td>
                    <td class="border-bottom border-left border-right">{{ $workplan->name }}</td>
                    <td class="border-bottom border-left border-right">{{ $workplan->task_description }}</td>
                    <td class="border-bottom border-left border-right">{{ Utils::my_date($workplan->due_to_date) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- 
project_id
project_section_id
->assigned_to
name
task_description
due_to_date
	

    --}}
</body>

</html>
