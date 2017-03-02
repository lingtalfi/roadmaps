<?php
$prefs = [
    'actionColumnsPosition' => 'right',
    'prettyTableNames' => [
    ],
    'foreignKeyPrettierColumns' => [
        'roadmaps.project' => 'name',
        'roadmaps.task' => 'label',
    ],
    'prettyColumnNames' => [
        'start_date' => 'start date',
        'end_date' => 'end date',
        'parent_task_id' => 'parent task',
        'project_id' => 'project',
    ],
    'urlTransformerIf' => function ($c) {
            return (false !== strpos($c, 'url_'));
        },
];
