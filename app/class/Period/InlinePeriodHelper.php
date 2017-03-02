<?php


namespace Period;

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

        $dateStart = new \DateTime($this->period->getStartDate());
        $dateEnd = new \DateTime($this->period->getEndDate());

        $interval = $dateEnd->getTimestamp() - $dateStart->getTimestamp();
        if ($interval > 0) {
            $periodUnit = $this->period->getInterval();
            $nbPeriods = ceil($interval / $periodUnit);

            $time = $dateStart->getTimestamp();
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