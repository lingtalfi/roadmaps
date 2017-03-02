<?php

namespace ToolsLeftMenuSection;


class ToolsLeftMenuSectionServices
{


    /**
     * Owned by:
     * - class-modules/LeftMenuSection/Tools/ToolsLeftMenuSectionModule
     */
    public static function displayToolsLeftMenuLinks()
    {
        \Boot\BootModule::displayToolsLeftMenuLinks();
//        \IconsViewer\IconsViewerModule::displayToolsLeftMenuLinks();
        \Crud\CrudModule::displayToolsLeftMenuLinks();

//        \NullosInfo\NullosInfoModule::displayToolsLeftMenuLinks();
//        \Linguist\LinguistModule::displayToolsLeftMenuLinks();
//        \QuickDoc\QuickDocModule::displayToolsLeftMenuLinks();
//        \SqlTools\SqlToolsModule::displayToolsLeftMenuLinks();
//        \LogWatcher\LogWatcherModule::displayToolsLeftMenuLinks();
//        \Counter\CounterModule::displayToolsLeftMenuLinks();
    }


}