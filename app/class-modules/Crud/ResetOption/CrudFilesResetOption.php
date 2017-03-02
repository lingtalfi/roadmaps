<?php


namespace Crud\ResetOption;

use Bat\FileSystemTool;
use Boot\ResetOption\AbstractResetOption;
use Crud\CrudConfig;

class CrudFilesResetOption extends AbstractResetOption
{


    public function reset()
    {
        self::emptyCrudFilesDirectories();
    }


    private static function emptyCrudFilesDirectories()
    {
        /**
         * Note: this will actually create the directories if they don't exist
         */
        FileSystemTool::clearDir(CrudConfig::getCrudGenFormDir());
        FileSystemTool::clearDir(CrudConfig::getCrudGenListDir());
    }

}