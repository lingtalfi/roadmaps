<?php


namespace ToolsLeftMenuSection;




use Installer\ModuleInstaller;
use Installer\Saas\ModuleSaasInterface;

class ToolsLeftMenuSectionInstaller extends ModuleInstaller implements ModuleSaasInterface
{
    //------------------------------------------------------------------------------/
    // SAAS
    //------------------------------------------------------------------------------/
    public function getSubscriberServiceIds()
    {
        return [
            'Layout.displayLeftMenuBlocks',
        ];
    }

}