<?php


namespace Lang;

use Installer\Installer;
use Installer\ModuleInstallerInterface;
use Installer\Operation\LayoutServices\LayoutBridgeDisplayTopBarOperation;
use Installer\Report\Report;
use Installer\Report\ReportInterface;
use Installer\Saas\ModuleSaasInterface;


class LangInstaller implements ModuleInstallerInterface, ModuleSaasInterface
{
    public function install(ReportInterface $report)
    {
        /**
         * Hook into:
         * - class/Layout/LayoutServices
         */
        $installer = new Installer();
        $installer->run($report);
    }


    public function uninstall(ReportInterface $report)
    {
        $installer = new Installer();
        $installer->run($report);
    }

    //------------------------------------------------------------------------------/
    // SAAS
    //------------------------------------------------------------------------------/
    public function getSubscriberServiceIds()
    {
        return [
            'Layout.displayTopBar',
        ];
    }

}