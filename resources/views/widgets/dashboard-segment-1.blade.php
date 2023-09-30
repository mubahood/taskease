<?php
if (!isset($events_count)) {
    $events_count = 0;
}
if (!isset($project_count)) {
    $project_count = 0;
}
if (!isset($tasks_count)) {
    $tasks_count = 0;
}
?>
<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-md-4">
                @include('widgets.box-6', [
                    'is_dark' => false,
                    'title' => 'Ongoing Projects',
                    'icon' => 'box',
                    'number' => $project_count,
                    'link' => 'javascript:;',
                ])
            </div>
            <div class="col-md-4">
                @include('widgets.box-6', [
                    'is_dark' => false,
                    'title' => 'This Week\'s Tasks',
                    'icon' => 'list-task',
                    'number' => $tasks_count,
                    'link' => 'javascript:;',
                ])
            </div>
            <div class="col-md-4">
                @include('widgets.box-6', [
                    'is_dark' => false,
                    'title' => 'This Week\'s Events',
                    'icon' => 'calendar-event-fill',
                    'number' => $events_count,
                    'link' => 'javascript:;',
                ])
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        @include('dashboard.upcoming-events', [
            'items' => $events,
        ])
    </div>
    <div class="col-md-6">
        @include('dashboard.tasks', [
            'items' => $events,
        ])
    </div>
</div>
