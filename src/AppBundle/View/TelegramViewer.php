<?php

namespace AppBundle\View\TelegramViewer;
use AppBundle\Model\WorkInfoModel;
use Shaygan\TelegramBotApiBundle\TelegramBotApi;

/**
 * Telegram viewer class
 */
class TelegramViewer
{
    /**
     * @var TelegramBotApi
     */
    protected $telegramApi;

    public function __construct(TelegramBotApi $telegramApi)
    {
        $this->telegramApi = $telegramApi;
    }

    /**
     * public method for sender
     *
     * @param integer $chatId telegram chat id
     * @param WorkInfoModel $workModel work model with info
     */
    public function sendWorkModel($chatId, WorkInfoModel $workModel)
    {
        $this->telegramApi->getApiBot()->sendMessage(
            $chatId,
            $this->makeString($this->prepareData($workModel))
        );

        return $this;
    }

    /**
     * Make string for telegram
     * @param array $data
     * @return string
     */
    private function makeString($data)
    {
        $str = '';
        foreach ($data as $key=>$value)
        {
            $str .= $key . '=' . $value . PHP_EOL;
        }

        return $str;
    }

    /**
     * Create Info for bot
     * @param WorkInfoModel $workModel
     * @return array
     */
    private function prepareData(WorkInfoModel $workModel)
    {
        return [
            'price'=>$workModel->getPrice(),
            'start_date'=>$workModel->getStartDate(),
            'end_date'=>$workModel->getEndDate(),
            'duration'=>$workModel->getDuration(),
            'durationInHours'=>$workModel->getDuration()/60,
            'workedDays'=>$workModel->getNeedDays(),
            'priceExcected'=>$workModel->getPriceNeeded(),
            'priceDelta'=>$workModel->getPriceNeeded() - $workModel->getPrice(),
            'workMinuteDelta'=>$workModel->getNeedDays() * 60 * 8 - $workModel->getDuration(),
            'workHourDelta'=>$workModel->getNeedDays() * 8  - $workModel->getDuration()/60
        ];
    }
}