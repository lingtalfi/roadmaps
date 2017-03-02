<?php


namespace ApplicationLog;


use Installer\ModuleInstaller;
use Installer\Saas\ModuleSaasInterface;
use Installer\Universe\ModuleUniverseInterface;


class ApplicationLogInstaller extends ModuleInstaller implements ModuleSaasInterface, ModuleUniverseInterface
{


    //------------------------------------------------------------------------------/
    // SAAS
    //------------------------------------------------------------------------------/
    public function getSubscriberServiceIds()
    {
        return [
            'LogWatcher.decorateLogToWatch',
        ];
    }

    public function getPlanetDependencies()
    {
        return [
            'git::/lingtalfi/Bat',
        ];
    }


}