<?php


namespace Router;

use Boot\BootModule;
use Crud\CrudModule;
use FrontOne\FrontOneModule;
use IconsViewer\IconsViewerModule;
use Linguist\LinguistModule;
use LogWatcher\LogWatcherModule;
use ModuleInstaller\ModuleInstallerModule;
use NullosInfo\NullosInfoModule;
use QuickDoc\QuickDocModule;
use SqlTools\SqlToolsModule;

class RouterServices
{

    public static function decorateUri2PagesMap(array &$uri2pagesMap)
    {
        \Crud\CrudModule::decorateUri2PagesMap($uri2pagesMap);
    }

}