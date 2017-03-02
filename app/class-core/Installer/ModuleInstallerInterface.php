<?php


namespace Installer;


use Installer\Report\ReportInterface;

/**
 * If you implement this interface, then read this comment:
 *
 * - to deploy/remove assets, the files should be located in an "InstallAssets" directory.
 *
 *
 *
 *
 */
interface ModuleInstallerInterface
{
    public function install(ReportInterface $report);

    public function uninstall(ReportInterface $report);

}