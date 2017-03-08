<?php
$prefs = [
    'actionColumnsPosition' => 'right',
    'prettyTableNames' => [
    ],
    'foreignKeyPrettierColumns' => [
        'roadmaps.users' => 'pseudo',
        'roadmaps.project' => 'name',
        'roadmaps.task' => 'label',
    ],
    'prettyColumnNames' => [
        'users_id' => 'users',
        'start_date' => 'start date',
        'end_date' => 'end date',
        'parent_task_id' => 'parent task',
        'project_id' => 'project',
    ],
    'urlTransformerIf' => function ($c) {
            return (false !== strpos($c, 'url_'));
        },
];
