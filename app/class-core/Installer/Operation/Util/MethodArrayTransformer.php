<?php


namespace Installer\Operation\Util;


use Bat\ClassTool;
use Installer\Report\ReportInterface;

class MethodArrayTransformer extends ArrayTransformer
{
    protected function replaceByMethod(ReportInterface $report, $class, $method, array $options = [])
    {

        $signature = 'public static function ' . $method . '()';
        if (array_key_exists('signature', $options)) {
            $signature = $options['signature'];
        }
        $signaturePattern = 'public static function ' . $method . '\s*\(\)';
        if (array_key_exists('signaturePattern', $options)) {
            $signaturePattern = $options['signaturePattern'];
        }

        $_method = new \ReflectionMethod($class, $method);
        $file = $_method->getFileName();
        $fileContent = file_get_contents($file);
        $content = ClassTool::getMethodContent($class, $method);
        $lines = OperationUtil::getClassLines($content);
        $this->transform($lines);
        $s = str_repeat(' ', 8);
        $replace = $signature . PHP_EOL;
        $replace .= '   {' . PHP_EOL;
        $replace .= $s . implode(';' . PHP_EOL . $s, $lines) . ';' . PHP_EOL;
        $replace .= '   }';
        $fileContent = preg_replace('!' . $signaturePattern . '\s*{(.*)}!Ums', $replace, $fileContent);
        file_put_contents($file, $fileContent);
    }


    public function setLocationTransformerAfter($item, $targetSubstr)
    {
        $this->setLocationTransformer(function (array &$arr) use ($item, $targetSubstr) {
            $_item = $item;
            if (false === in_array($_item, $arr, true)) {
                $insertIndex = 0;
                foreach ($arr as $k => $item) {
                    if (false !== strpos($item, $targetSubstr)) {
                        $insertIndex = $k + 1;
                        break;
                    }
                }
                array_splice($arr, $insertIndex, 0, $_item);
            }
        });
        return $this;
    }

    public function setLocationTransformerAppend($item)
    {
        $this->setLocationTransformer(function (array &$arr) use ($item) {
            if (false === in_array($item, $arr, true)) {
                $arr[] = $item;
            }
        });
        return $this;
    }

    public function setLocationTransformerByPosition($item, $pos)
    {
        $this->setLocationTransformer(function (array &$arr) use ($item, $pos) {
            if (false === in_array($item, $arr, true)) {
                array_splice($arr, $pos, 0, $item);
            }
        });
        return $this;
    }

    public function setLocationTransformerRemoveBySubstr($item)
    {
        $this->setLocationTransformer(function (array &$arr) use ($item) {
            foreach ($arr as $k => $v) {
                if (false !== strpos($v, $item)) {
                    unset($arr[$k]);
                }
            }
        });
        return $this;
    }
}