<?php


namespace Crud;


use Installer\BaseModuleInstaller;
use Installer\Saas\ModuleSaasInterface;
use Installer\Universe\ModuleUniverseInterface;


class CrudInstaller extends BaseModuleInstaller implements ModuleSaasInterface, ModuleUniverseInterface
{


    protected function getSources()
    {
        return [
            'assets/modules/crud',
            /**
             * Note: left the lang,
             * because other modules use the Crud module (ArrayDataTable),
             * which needs the translations...
             */
//                'lang/en/modules/crud',
//                'lang/fr/modules/crud',
            'layout-elements/nullos/modules/crud',
            'pages/modules/crud',
            /**
             * Note: I left the app-nullos/crud directory alone, because they can contain user information
             */
        ];
    }


    //------------------------------------------------------------------------------/
    // SAAS
    //------------------------------------------------------------------------------/
    public function getSubscriberServiceIds()
    {
        return [
            'ToolsLeftMenuSection.displayToolsLeftMenuLinks:2',
            'Router.decorateUri2PagesMap',
            'Layout.displayLeftMenuBlocks',
        ];
    }

    public function getPlanetDependencies()
    {
        return [
            'git::/lingtalfi/AdminTable',
            'git::/lingtalfi/ArrayExport',
            'git::/lingtalfi/ArrayToString',
            'git::/lingtalfi/AssetsList',
            'git::/lingtalfi/Bat',
            'git::/lingtalfi/QuickForm',
            'git::/lingtalfi/QuickPdo',
        ];
    }


}