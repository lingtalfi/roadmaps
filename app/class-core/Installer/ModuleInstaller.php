<?php


namespace Installer;


use Installer\Report\ReportInterface;

abstract class ModuleInstaller implements ModuleInstallerInterface
{
    //------------------------------------------------------------------------------/
    // MODULE INSTALLER
    //------------------------------------------------------------------------------/
    public function install(ReportInterface $report)
    {

    }

    public function uninstall(ReportInterface $report)
    {
    }
}