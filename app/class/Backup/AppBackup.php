<?php


namespace Backup;


use FileCleaner\SimpleFileCleaner;

class AppBackup
{

    private $dir;


    public function __construct()
    {
        $this->dir = APP_ROOT_DIR . "/backup";
    }

    public static function create()
    {
        return new static();
    }

    public function setDir($dir)
    {
        $this->dir = $dir;
        return $this;
    }


    public function createBackup($relativePath = null)
    {
        if (null === $relativePath) {
            $relativePath = 'auto/' . date("Ymd--His") . "--backup.sql";
        }
        $file = $this->dir . "/" . $relativePath;

        if (false === file_exists($file)) {
            $sPass = ('' !== DB_PASS) ? ' -p' . DB_PASS : '';


            $cmd = MYSQLDUMP_PATH . ' -u' . DB_NAME . $sPass . ' --default-character-set=utf8 --add-drop-database -B roadmaps > "' . $file . '"';
            $this->executeCmd($cmd);

//            SimpleFileCleaner::create()
////                ->setTestMode(true)// remove this line in prod
//                ->setDir($this->dir)
//                ->keep('last 7 days')
//                ->keep('1 per month')
//                ->clean();
        }
    }

//
//    public function restoreBackup($file)
//    {
//        $file = $this->dir . "/" . $file;
//        if (file_exists($file)) {
//            // assuming only zilu is using the system (i.e. no checking on double quotes...)
//            $cmd = PATH_TO_MYSQL . ' -uroot zilu < "' . $file . '"';
//            $this->executeCmd($cmd);
//            return true;
//
//        } else {
//            $this->error("File not found: $file");
//        }
//        return false;
//    }

    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    private function error($m)
    {
        throw new \Exception($m);
    }

    private function executeCmd($cmd)
    {
        $returnVar = 0;
        ob_start();
        passthru($cmd, $returnVar);
        $content = ob_get_clean();

        if (0 !== $returnVar) {
            throw new \Exception("Command failed: $cmd");
        }
    }
}