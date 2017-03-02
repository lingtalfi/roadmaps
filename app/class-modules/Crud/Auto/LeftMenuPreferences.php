<?php


namespace Crud\Auto;


class LeftMenuPreferences
{


    public static function getLeftMenuSectionBlocks()
    {
        return [
    'Website' => [
        'roadmaps.project',
        'roadmaps.task',
    ],
];
    }

    /**
     * Labels are used in the left menu only
     */
    public static function getTableLabels()
    {
        return [
];
    }

}