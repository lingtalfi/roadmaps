<?php


namespace LayoutDynamicHead;


use Layout\AssetsList;

/**
 * Quick and dirty class which helps a developer call assets dynamically.
 *
 * The main synopsis being a developer making quick css tests...
 */
class LayoutDynamicHeadModule
{


    private static $list = [];

    public static function registerAssets(AssetsList $assetsList)
    {
        foreach (self::$list as $url => $type) {
            if ('css' === $type) {
                $assetsList->css($url);
            } else {
                $assetsList->js($url);
            }
        }
    }

    public static function registerCss($url)
    {
        self::$list[$url] = 'css';
    }

    public static function registerJs($url)
    {
        self::$list[$url] = 'js';
    }


    public static function registerCssIf($url, $uri)
    {
        if ($uri === \Spirit::get('uri')) {
            self::registerCss($url);
        }
    }
}