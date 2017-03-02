<?php


namespace Installer;


use Installer\Report\ReportInterface;

abstract class BaseModuleInstaller extends WithPackModuleInstaller
{

    public function install(ReportInterface $report)
    {
        /**
         * Deploy Files:
         * - assets/modules/$$/
         * - pages/modules/$$/
         * - layout-elements/nullos/modules/$$/
         * - lang/++/modules/$$/
         *
         *
         * Hook into:
         * - class/Router/RouterServices
         * - class/Layout/LayoutServices
         */
        $installer = new Installer();
        $this->onInstallBefore($installer);
        $this->prepareDeployFiles($installer);


        $installer->run($report);
    }

    public function uninstall(ReportInterface $report)
    {
        $installer = new Installer();
        $this->onUninstallBefore($installer);
        $this->prepareRemoveFiles($installer);
        $installer->run($report);
    }


}