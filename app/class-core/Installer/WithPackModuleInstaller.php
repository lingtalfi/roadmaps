<?php


namespace Installer;


use Bat\FileSystemTool;
use Installer\Operation\DeployFile\DeployFileOperation;
use Installer\Operation\DeployFile\RemoveFileOperation;
use Installer\Report\Report;

abstract class WithPackModuleInstaller extends ModuleInstaller implements PackableModuleInstallerInterface
{

    public function pack()
    {
        $report = new Report();


        $sources = $this->getSources();
        $targetDir = $this->getTargetDir();
        $sourceDir = $this->getSourceDir();
        FileSystemTool::mkdir($sourceDir, 0777, true);

        foreach ($sources as $source) {
            $extSrc = $targetDir . '/' . $source;
            $intDst = $sourceDir . "/" . $source;
            if (is_dir($extSrc)) {
                $errors = [];

                /**
                 * Not sure if destroying the internal dirs is the best solution,
                 * but it ensures having synced directories, at the cost of being
                 * a little bit more destructive.
                 * Anyway, I just had the need for that, so let's give it a try...
                 */
                if (file_exists($intDst)) {
                    FileSystemTool::remove($intDst);
                }


                FileSystemTool::copyDir($extSrc, $intDst, true, $errors);
                if (count($errors) > 0) {
                    $report->addMessage($errors);
                }
            } else {
                $dirName = dirname($intDst);
                if (!file_exists($dirName)) {
                    mkdir($dirName, 0777, true);
                }
                if (file_exists($extSrc)) {
                    copy($extSrc, $intDst);
                }
            }
        }

        return $report;

    }


    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    protected function getModuleClass()
    {
        $calledClass = get_called_class();
        $p = explode('\\', $calledClass, 2);
        $moduleName = $p[0];
        return $moduleName;
    }


    protected function prepareDeployFiles(InstallerInterface $installer)
    {
        $installer->addOperation(DeployFileOperation::create()
            ->sourceDir($this->getSourceDir())
            ->destDir($this->getTargetDir())
        );
    }

    protected function prepareRemoveFiles(InstallerInterface $installer)
    {
        $sources = $this->getSources();
        $installer->addOperation(RemoveFileOperation::create()
            ->sources($sources)
            ->destDir($this->getTargetDir()));

    }

    protected function onInstallBefore(InstallerInterface $installer)
    {

    }

    protected function onUninstallBefore(InstallerInterface $installer)
    {

    }

    //------------------------------------------------------------------------------/
    // getSources, getTargetDir and getSourceDir
    // work together to implement the packing system
    //------------------------------------------------------------------------------/

    /**
     * Note: getSources is called only by the uninstall and pack methods,
     * which means the files are already deployed.
     * I leverage this statement to dynamically find languages and assets
     * (rather than having a boring getLanguages and hasAsset method).
     *
     */
    protected function getSources()
    {
        $moduleClass = $this->getModuleClass();
        $moduleClassLower = lcfirst($moduleClass);

        $sources = [
            'layout-elements/nullos/modules/' . $moduleClassLower,
            'pages/modules/' . $moduleClassLower,
        ];


        if (is_dir(APP_ROOT_DIR . "/assets/modules/$moduleClassLower")) {
            $sources[] = 'assets/modules/' . $moduleClassLower;
        }


        $langDir = APP_ROOT_DIR . "/lang";
        if (is_dir($langDir)) {
            $langs = scandir($langDir);
            foreach ($langs as $lang) {
                if ('.' !== $lang && '..' !== $lang) {
                    $dir = $langDir . '/' . $lang . '/modules/' . $moduleClassLower;
                    if (is_dir($dir)) {
                        $sources[] = 'lang/' . $lang . '/modules/' . $moduleClassLower;
                    }
                }
            }
        }
        return $sources;
    }

    protected function getTargetDir()
    {
        return APP_ROOT_DIR;
    }

    protected function getSourceDir()
    {
        $inst = new \ReflectionClass($this);
        return dirname($inst->getFileName()) . "/InstallAssets/app-nullos";
    }
}