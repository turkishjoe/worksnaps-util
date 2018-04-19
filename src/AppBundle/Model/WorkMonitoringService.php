<?php
/**
 * worksnaps_old
 *
 * @category ADD category
 * @package ADD package
 * @author TurhishJoe
 *
 */

namespace AppBundle\Model;
use DateInterval;
use DateTime;
use \Umbrella\WorksnapsBundle\Service\WorksnapsService ;

/**
 * Work Monitorig Service
 *
 * @author TurhishJoe
 *
 */
class WorkMonitoringService
{
    /**
     * Worksnaps api
     * @var WorksnapsService
     */
    protected $umbrellaClient;

    /**
     * Worksnaps project id
     * @var integer
     */
    protected $projectId;

    /**
     * Salary per hour
     *
     * @var float
     */
    protected $salary;


    /**
     * @param WorksnapsService $umbrellaClient
     * @param integer $projectId
     * @param float $salary
     */
    public function __construct(WorksnapsService $umbrellaClient)
    {
        $this->umbrellaClient = $umbrellaClient;
    }

    /**
     * 
     * @param int $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * 
     * @param float $salary
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;
        return $this;
    }

    /**
     * @var string $startDate
     * @var string $endDate
     */
    public function getSalaryInfo($startDate, $endDate)
    {
        $startDateObject = new DateTime($startDate);
        $endDateObject = new DateTime($endDate);

        $duration = $this->getDuration($this->getTimeFromService($startDateObject, $endDateObject));
        $needDays = $this->getWorkedDay($startDateObject, $endDateObject);

        return new WorkInfoModel($duration, $needDays, $this->salary, $startDateObject, $endDateObject);
    }

    /**
     * 
     * @param $timeEntries
     * @return int
     */
    private function getDuration($timeEntries)
    {
        $duration = 0;

        if(!empty($timeEntries['time_entry'])) {
            foreach ($timeEntries['time_entry'] as $timeEntry) {
                $duration += $timeEntry['duration_in_minutes'];
            }
        }

        return $duration;
    }

    /**
     * 
     * @param DateTime $startDateObject
     * @param DateTime $endDateObject
     * @return mixed
     */
    private function getTimeFromService(DateTime $startDateObject, DateTime $endDateObject)
    {
        $userId = $this->umbrellaClient->getMyUser()['id'];

        return  $this->umbrellaClient->getTimeEntries(
            $this->projectId,
            array($userId),
            $startDateObject->getTimestamp(),
            $endDateObject->getTimestamp()
        );
    }

    /**
     * Count workdays(without holidays)
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return int
     */
    private  function getWorkedDay(DateTime $startDate, DateTime $endDate)
    {
        $count = 0;
        for($i = clone $startDate ; $i <= $endDate ; $i->add(new DateInterval('P1D')))
        {
            $dayOfWeek = $i->format('N');

            if($dayOfWeek >= 1 && $dayOfWeek <= 5)
            {
                $count++;
            }
        }

        return $count;
    }
}