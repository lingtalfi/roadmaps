<?php

namespace Boot;


class BootConfig
{

    public static function getBootPage()
    {
        return "modules/boot/boot.php";
    }

    public static function getBootUri()
    {
        return "/boot";
    }


}