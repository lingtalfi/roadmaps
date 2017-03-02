<?php

namespace Boot;


use Boot\ResetOption\InitResetOption;
use Privilege\Privilege;

class BootModule
{

    public static function applicationIsInitialized()
    {
        return (false === defined('I_AM_JUST_THE_FALLBACK_INIT'));
    }


    public static function decorateUri2PagesMap(array &$uri2pagesMap)
    {
        $uri2pagesMap[BootConfig::getBootUri()] = BootConfig::getBootPage();
    }


    public static function displayToolsLeftMenuLinks()
    {

        $ll = "modules/boot/boot";
        if (Privilege::has('boot.access.init')):
            ?>
            <li>
                <a href="<?php echo self::getUrl('init'); ?>"><?php echo __("Init writer", $ll); ?></a>
            </li>
            <?php
        endif;
        if (Privilege::has('boot.access.reset')):
            ?>
            <li>
                <a href="<?php echo self::getUrl('reset'); ?>"><?php echo __("Reset", $ll); ?></a>
            </li>
            <?php
        endif;
    }

    public static function registerBootResetOptions(array &$options)
    {
        $options[] = new InitResetOption('boot_init', __('remove the init file', 'modules/boot/boot'));
    }


    public static function getUrl($action)
    {
        return BootConfig::getBootUri() . '?action=' . $action;
    }

}