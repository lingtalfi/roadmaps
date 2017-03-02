<?php


namespace Boot;


use Installer\Saas\ModuleSaasInterface;
use Installer\BaseModuleInstaller;
use Installer\Universe\ModuleUniverseInterface;


class BootInstaller extends BaseModuleInstaller implements ModuleSaasInterface, ModuleUniverseInterface
{


    //------------------------------------------------------------------------------/
    // SAAS
    //------------------------------------------------------------------------------/
    public function getSubscriberServiceIds()
    {
        return [
            'ToolsLeftMenuSection.displayToolsLeftMenuLinks:0',
            'Router.decorateUri2PagesMap',
        ];
    }

    public function getPlanetDependencies()
    {
        return [
            'git::/lingtalfi/QuickPdo',
            'git::/lingtalfi/FileCreator',
        ];
    }


}