<?php


namespace Installer\Operation\Init\InitAutoloadOperation;


use Installer\Exception\AbortInstallException;
use Installer\Operation\OperationInterface;
use Installer\Operation\Util\ArrayTransformer;
use Installer\Report\ReportInterface;

class InitAutoloadOperation extends ArrayTransformer implements OperationInterface
{

    public function execute(ReportInterface $report)
    {
        $file = APP_ROOT_DIR . "/init.php";
        $content = file_get_contents($file);
        $locations = self::getLocations($content);
        $this->transform($locations);
        $content = self::setLocations($content, $locations);
        file_put_contents($file, $content);
    }

    private static function getLocations($content)
    {
        $ret = [];
        if (preg_match('!ButineurAutoloader::getInst\(\)(.*);\s*ButineurAutoloader!Us', $content, $match)) {
            $p = explode('->addLocation', $match[1]);
            $p = array_map(function ($v) {
                return substr(trim($v), 1, -1);
            }, $p);
            $ret = array_filter($p);
        }
        return $ret;
    }

    private static function setLocations($content, array $locations)
    {
        $replace = 'ButineurAutoloader::getInst()' . PHP_EOL;
        $replace .= '    ->addLocation(' . implode(')' . PHP_EOL . '    ->addLocation(', $locations) . ');' . PHP_EOL;
        $replace .= 'ButineurAutoloader';
        return preg_replace('!ButineurAutoloader::getInst\(\)(.*);\s*ButineurAutoloader!Us', $replace, $content);
    }

    public static function create()
    {
        return new self();
    }

}