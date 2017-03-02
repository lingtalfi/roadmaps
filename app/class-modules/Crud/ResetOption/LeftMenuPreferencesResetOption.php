<?php


namespace Crud\ResetOption;

use Boot\ResetOption\AbstractResetOption;
use Crud\Util\LeftMenuPreferencesGenerator;


class LeftMenuPreferencesResetOption extends AbstractResetOption
{


    public function reset()
    {
        LeftMenuPreferencesGenerator::create()
            ->withoutLeftMenuSections()
            ->withoutTableLabels()
            ->generate();
    }
}