<?php

namespace Lang;

class LangModule
{


    public static function getLang($default = null)
    {
        $lang = (null !== $default) ? $default : "en";
        $allowedLangs = LangConfig::$allowedLangs;
        if (array_key_exists('lang', $_GET) && in_array($_GET['lang'], $allowedLangs)) {
            $lang = $_GET['lang'];
            $_SESSION['lang'] = $lang;
        } elseif (array_key_exists('lang', $_SESSION)) {
            $lang = $_SESSION['lang'];
        }
        return $lang;
    }


    public static function displayTopBar()
    {
        ?>
        <div class="lang-tool">
            <span>Language</span>
            <ul class="lang-list">
                <li><a href="<?php echo url(null, ['lang' => 'en']); ?>">en</a></li>
                <li class="last"><a href="<?php echo url(null, ['lang' => 'fr']); ?>">fr</a></li>
            </ul>
        </div>
        <?php
    }
}