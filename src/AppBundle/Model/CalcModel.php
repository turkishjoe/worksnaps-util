<?php
/**
 * worksnaps_old
 *
 * @category TODO:ADD category
 * @package TODO:ADD package
 * @author TurhishJoe
 *
 */

namespace AppBundle\Model;

use DateTime;
/**
 * TODO:Add your description
 *
 * @category TODO:ADD category
 * @package TODO:ADD package
 * @author TurhishJoe
 *
 */
class CalcModel
{
    /**
     * @Route("/", name="homepage")
     */
    public function calc(\Umbrella\WorksnapsBundle\Service\WorksnapsService $umbrellaClient)
    {
        $projectId = 33220;
        $salary = 450;

        $dateTime = new \DateTime();
        $day = $dateTime->format('j');
        $year = $dateTime->format('Y');
        $month = $dateTime->format('m');

        if($day < 28 && $day > 13)
        {
            $date1 = $year . '-' . $month . '-' . 14;
            $date2 = $year . '-' . $month . '-' . 27;
        }
        else
        {
            if($day > 13)
            {
                $startMonth = $month;
                $endMonth = (new DateTime())->modify('+1 month')->format('m');
            }
            else
            {
                $startMonth = (new DateTime())->modify('-1 month')->format('m');
                $endMonth = $month;

            }

            $date1 = $year . '-' . $startMonth . '-' . 28;
            $date2 = $year . '-' . $endMonth . '-' . 13;
        }


        $userId = $umbrellaClient->getMyUser()['id'];
        $fromTimeStamp = (new DateTime($date1))->getTimestamp();
        $endTimeStamp = (new DateTime($date2))->getTimestamp();

        $timeEntries = $umbrellaClient->getTimeEntries( $projectId, array( $userId ), $fromTimeStamp, $endTimeStamp);
        $duration = 0;

        if(!empty($timeEntries['time_entry'])) {
            foreach ($timeEntries['time_entry'] as $timeEntry) {
                $duration += $timeEntry['duration_in_minutes'];
            }
        }

        $needDays = $this->getWorkedDay(new DateTime($date1));
        $price = $duration * $salary / 60;
        $priceNeeded = $needDays * 8 * $salary;
        // replace this example code with whatever you need
        return [
            'price'=>$price,
            'start_date'=>$date1,
            'end_date'=>$date2,
            'duration'=>$duration,
            'durationInHours'=>$duration/60,
            'workedDays'=>$needDays,
            'priceExcected'=>$priceNeeded,
            'priceDelta'=>$priceNeeded-$price,
            'workMinuteDelta'=>$needDays * 60 * 8 - $duration,
            'workHourDelta'=>$needDays * 8  - $duration/60,
        ];
    }

    protected function getWorkedDay(DateTime $dateTime)
    {
        $count = 0;
        $day = $dateTime->format('j');
        if($day > 13 && $day < 28 )
        {
            $start = 14;
            $end = 27;
            $circle = false;
        }
        else
        {
            $start = 28 ;
            $end = 13;
            $circle = true;
        }


        $maxDays = $dateTime->format('t');
        for($i = $start ; $i <= $end || $circle ; $i++)
        {
            if($i >= $maxDays)
            {
                $i = 1;
                $circle = false;
            }

            $dayOfWeek = $dateTime->format('N');

            if($dayOfWeek >= 1 && $dayOfWeek <= 5)
            {
                $count++;
            }

            $dateTime->modify('+1 day');
        }

        return $count;
    }
}