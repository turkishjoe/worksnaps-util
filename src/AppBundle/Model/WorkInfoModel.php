<?php
namespace AppBundle\Model;

/**
 * Class WorkInfoModel
 */
class WorkInfoModel
{
    /**
     * @var integer
     */
    protected $needDays;

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var \DateTime
     */
    protected $endDate;

    /**
     * @var integer
     */
    protected $duration;

    /**
     * @var float
     */
    protected $salary;


    public function __construct($duration, $needDays, $salary, \DateTime $startDate, \DateTime $endDate)
    {
        $this->duration = $duration;
        $this->needDays = $needDays;
        $this->salary = $salary;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     *  
     * @return int
     */
    public function getNeedDays()
    {
        return $this->needDays;
    }

    /**
     *  
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     *  
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     *  
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     *  
     * @return float
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     *  
     * @return float
     */
    public function getPrice()
    {
        return $this->duration * $this->salary / 60;
    }

    /**
     *  
     * @return int
     */
    public function getPriceNeeded()
    {
        return $this->needDays * 8 * $this->salary;
    }
}