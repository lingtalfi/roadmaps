<?php


namespace LayoutDynamicHead;


use Installer\ModuleInstaller;
use Installer\Saas\ModuleSaasInterface;

class LayoutDynamicHeadInstaller extends ModuleInstaller implements ModuleSaasInterface
{
    public function getSubscriberServiceIds()
    {
        return [
            'Layout.registerAssets',
        ];
    }
}