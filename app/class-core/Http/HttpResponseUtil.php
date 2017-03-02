<?php


namespace Http;

use Bat\UriTool;

class HttpResponseUtil
{
    public static function redirect($url)
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        $url = UriTool::getWebsiteAbsoluteUrl() . $url;
        header("Location: " . $url);
        exit;
    }


    public static function downloadFile($file, $mime="application/octet-stream")
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: private');
        readfile($file);
        exit;
    }
}