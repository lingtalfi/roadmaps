<?php


namespace Crud\Auto;


class LeftMenuPreferences
{


    public static function getLeftMenuSectionBlocks()
    {
        return [
    'Website' => [
        'roadmaps.compte_mail',
        'roadmaps.historique_mail',
        'roadmaps.project',
        'roadmaps.task',
        'roadmaps.users',
        'roadmaps.users_has_task',
    ],
];
    }

    /**
     * Labels are used in the left menu only
     */
    public static function getTableLabels()
    {
        return [
    'roadmaps.compte_mail' => 'compte mail',
    'roadmaps.historique_mail' => 'historique mail',
    'roadmaps.users_has_task' => 'users has task',
];
    }

}