<?php


use Privilege\Privilege;

class Helper
{

    public static function isLocal()
    {
        if (
            '/Volumes/' === substr(__DIR__, 0, 9) ||
            '/Users/' === substr(__DIR__, 0, 7)
        ) {
            return true;
        }
        return false;
    }

    public static function defaultLogMsg()
    {
        return __("Oops, an error occurred, please check the logs");
    }


    public static function layoutElementIf($fileName, $privilege, $default = null)
    {
        if (true === Privilege::has($privilege)) {
            return $fileName;
        }
        if (null === $default) {
            $default = "page-denied.php";
        }
        return $default;
    }


    /**
     * goal of this function:
     * you are in js code, and you need to write a translation string (see DataTable.php -> multiple action), like so:
     *
     *
     * if (confirm) {
     * if (true === window.confirm("<?php echo Helper::jsQuote(__("Are you sure you want to delete all the selected rows?")); ?>")) {
     * tableForm.submit();
     * }
     * }
     * else {
     * tableForm.submit();
     * }
     *
     */
    public static function jsQuote($m)
    {
        return str_replace('"', '\"', $m);
    }

}