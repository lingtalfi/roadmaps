<?php
$prefs = [
    'actionColumnsPosition' => 'right',
    'prettyTableNames' => [
        'roadmaps.compte_mail' => 'compte mail',
        'roadmaps.historique_mail' => 'historique mail',
        'roadmaps.users_has_task' => 'users has task',
    ],
    'foreignKeyPrettierColumns' => [
        'roadmaps.users' => 'pseudo',
        'roadmaps.project' => 'name',
        'roadmaps.task' => 'label',
        'roadmaps.compte_mail' => 'pseudo',
    ],
    'prettyColumnNames' => [
        'date_envoi' => 'date envoi',
        'task_id' => 'task',
        'task_label' => 'task label',
        'task_start_date' => 'task start date',
        'project_id' => 'project',
        'project_name' => 'project name',
        'compte_mail_pseudo' => 'compte mail pseudo',
        'compte_mail_email' => 'compte mail email',
        'task_end_date' => 'task end date',
        'users_id' => 'users',
        'start_date' => 'start date',
        'end_date' => 'end date',
        'parent_task_id' => 'parent task',
        'compte_mail_id' => 'compte mail',
        'mail_sent' => 'mail sent',
    ],
    'urlTransformerIf' => function ($c) {
            return (false !== strpos($c, 'url_'));
        },
];
