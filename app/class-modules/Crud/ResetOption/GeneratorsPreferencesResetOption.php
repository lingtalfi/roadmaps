<?php


namespace Crud\ResetOption;

use Boot\ResetOption\AbstractResetOption;
use Crud\CrudConfig;

class GeneratorsPreferencesResetOption extends AbstractResetOption
{


    public function reset()
    {
        $f = CrudConfig::getCrudFilesPreferencesAutoFile();
        if (file_exists($f)) {
            unlink($f);
        }
    }
}