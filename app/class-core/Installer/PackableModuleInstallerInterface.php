<?php


namespace Installer;


interface PackableModuleInstallerInterface extends ModuleInstallerInterface
{
    /**
     *
     * The pack method navigate through the deployed files
     * and copy them back to the module's "InstallAssets" directory.
     *
     * This feature comes handy when you are developing your own module,
     * it allows you to work on the module live, and then make it exportable
     * "with one click" (rather than manually copy each deployed file back to your
     * InstallAssets dir).
     *
     *
     * By convention, the directory which contains the files to deploy is one of the following:
     *
     * - InstallAssets/app-nullos
     * - InstallAssets/project/app-nullos
     *
     * (the project version is used by modules that deploy whole websites outside of the nullos admin root dir,
     * for instance the FrontOne module does that).
     *
     * Note: some modules, like the Linguist module, rely on this convention to provide functionality.
     *
     *
     */
    public function pack();
}