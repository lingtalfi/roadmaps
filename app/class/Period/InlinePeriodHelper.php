<?php


namespace Period;

use Util\GeneralUtil;

class InlinePeriodHelper
{
    private $period;

    public function __construct(Period $period)
    {
        $this->period = $period;
    }


    public function getTimeScalePlots()
    {
        $ret = [];


        $timeStart = GeneralUtil::gmMysqlToTime($this->period->getStartDate());
        $timeEnd = GeneralUtil::gmMysqlToTime($this->period->getEndDate());


        $interval = $timeEnd - $timeStart;
        if ($interval > 0) {
            $periodUnit = $this->period->getInterval();
            $nbPeriods = ceil($interval / $periodUnit);
            $time = $timeStart;

            for ($i = 0; $i < $nbPeriods; $i++) {
                $ret[] = $time;
                $time += $periodUnit;
            }

        } else {
            throw new \Exception("Negative time interval, date end must be higher than date start");
        }
        return $ret;
    }

}