<?php


namespace MailHelper;

use Project\Project;
use Umail\Umail;
use UserHasTask\UserHasTask;
use Util\GeneralUtil;

class MailHelper
{

    public static function sendNotificationMail($userId, $projectId, $taskId, $subject = null, $plain = null)
    {

        $items = UserHasTask::getCompteMailInfoByTaskId($userId, $taskId);
        $res = 0;
        foreach ($items as $item) {

            $to = $item['email'];
            $nom = $item['pseudo'];
            $url = MailHelper::getBestUrlForProject($projectId);

            if (null === $subject) {
                $subject = MailHelper::getDefaultSubject();
            }

            if (null === $plain) {
                $plain = MailHelper::getDefaultPlainText();
            }

            $tags = [
                '{url}' => $url,
                '{nom}' => $nom,
            ];
            $plain = str_replace(array_keys($tags), array_values($tags), $plain);



            $n = Umail::create()
                ->to($to)
                ->from(MAIL_FROM)
                ->subject($subject)
//        ->htmlBody('Hi, this is <b>just</b> an <span style="color: red">test message</span>')
                ->plainBody($plain)
                ->send();

            if ($n > 0) {
                UserHasTask::incrementMailSent($userId, $taskId, $item['compte_mail_id']);
            }


            $res += $n;
        }
        return $res;
    }

    public static function getDefaultSubject()
    {
        return "Leaderfit-planning: Nouvelle tâche à traiter";
    }


    /**
     * tags:
     * - url
     * - nom
     */
    public static function getDefaultPlainText()
    {
        return file_get_contents(APP_ROOT_DIR . "/assets/mail/you-ve-got-new-task.txt");
    }


    public static function getBestUrlForProject($id)
    {
        $safeLeftPaddingInDays = 1;
        $safeRightPaddingInDays = 1;


        //--------------------------------------------
        //
        //--------------------------------------------
        list($startDatetime, $endDatetime) = Project::getPeriod($id);

        $timeStart = GeneralUtil::gmMysqlToTime($startDatetime);
        $timeEnd = GeneralUtil::gmMysqlToTime($endDatetime);


        if ($timeEnd < $timeStart) {
            throw new \Exception("time end cannot be lower than time start");
        }

        $timeStart -= $safeLeftPaddingInDays * 86400;
        $timeEnd += $safeRightPaddingInDays * 86400;


        $nbSec = $timeEnd - $timeStart;
        $nbDays = ceil($nbSec / 86400);

        $dateStart = date("Y-m-d", $timeStart);


        return APP_PUBLIC_URL . "/calendrier?project_id=$id&start=$dateStart&interval=86400&segments=$nbDays";

    }


}