<?php

namespace Boot;


use Crud\CrudModule;

class BootBridge
{



    /**
     * Owned by:
     * - class-modules/Boot
     */
    public static function registerBootResetOptions(array &$options)
    {
        BootModule::registerBootResetOptions($options);
        CrudModule::registerBootResetOptions($options);
    }


}