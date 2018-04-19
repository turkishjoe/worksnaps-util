<?php
/**
 * TODO:
 * Created by PhpStorm.
 * User: prog12
 * Date: 19.04.18
 * Time: 12:40
 */

namespace AppBundle\Model;


use AppBundle\View\TelegramViewer;
use Shaygan\TelegramBotApiBundle\TelegramBotApi;
use SymfonyBundles\RedisBundle\Service\Client;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

class TelegramManager
{
    /**
     * @var TelegramBotApi
     */
    protected $telegramApi;

    /**
     * @var Client $cache
     */
    protected $cache;

    /**
     * @var integer
     */
    protected $chatId;

    /**
     * @var integer
     */
    protected $projectId;

    /**
     * @var float
     */
    protected $salary;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var WorkMonitoringService
     */
    protected $workMonitoringService;

    /**
     * @var TelegramViewer
     */
    protected $telegramViewer;

    /**
     * Telegram stores update for one day. My bot
     * is simple tool just only for me. Storing processed date
     * in redis it is ok for me.
     *
     * @param TelegramBotApi $telegramApi
     * @param Client $cache
     */
    public function __construct(WorkMonitoringService $workMonitoringService,
                                TelegramViewer $telegramViewer,
                                TelegramBotApi $telegramApi,
                                Client $cache,
                                $chatId, $prefix=null)
    {
        $this->telegramViewer = $telegramViewer;
        $this->telegramApi = $telegramApi;
        $this->cache = $cache;
        $this->chatId = $chatId;
        $this->prefix = $prefix;
        $this->workMonitoringService = $workMonitoringService;
    }

    /**
     * TODO:
     * @param mixed $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * TODO:
     * @param mixed $salary
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;
        return $this;
    }


    /**
     * TODO:
     * @param mixed $chatId
     */
    public function setChatId($chatId)
    {
        $this->chatId = $chatId;
        return $this;
    }

    public function processUpdates()
    {
        /**
         * Update[]
         */
        $updates = $this->telegramApi->getApiBot()->getUpdates();

        foreach($updates as $update)
        {
            $this->processUpdate($update);
        }
    }

    /**
     * TODO:
     * @param Update $update
     */
    protected function processUpdate(Update $update)
    {
        /**
         * @var Message $message
         */
        $message = $this->getMessageUpdate($update);

        if(is_null($message))
        {
            return;
        }

        $userId = $message->getFrom()->getId();

        if($userId == $this->chatId)
        {
            if($this->isMessageNew($message)) {
                $this->writeToRedis($this->getRedisKey(), $message->getDate());

                $args = explode(' ', $message->getText());

                //TODO: Add Validator
                if (count($args) == 3 && $args[0] = '/get')
                {
                    $this->sendToTelegram($args[1], $args[2]);
                }
            }
        }

        return $this;
    }

    /**
     * TODO:
     * @param Message $message
     * @return bool
     */
    protected function isMessageNew(Message $message)
    {
        $key = $this->getRedisKey();
        $result = false;
        $keys = $this->cache->keys($key);

        if(!empty($keys[0]) && count($keys) == 1)
        {
            $redisData = json_decode($this->cache->get($keys[0]), true);

            if(empty($redisData['time']) || $redisData['time'] < $message->getDate()
                && strncmp($message->getText(), '/get', 4) == 0
            )
            {
                $result = true;
            }
        }
        else if(empty($keys))
        {
            $result = true;
        }

        return $result;
    }

    protected function sendToTelegram($startDate, $endDate)
    {
        $workModel = $this->workMonitoringService->getSalaryInfo(
            $startDate, $endDate
        );

        $this->telegramViewer->sendWorkModel($this->chatId, $workModel);

        return $this;
    }

    /**
     * TODO:
     * @param Update $update
     */
    protected function getMessageUpdate(Update $update)
    {
        /**
         * @var Message $message
         */
        $message = $update->getMessage();

        if(is_null($message))
        {
            $message = $update->getEditedMessage();

            if(!is_null($message))
            {
                return null;
            }
        }

        return $message;
    }

    /**
     * TODO:Add your description
     *
     * @param $api
     * @param $userId
     *
     * @return string
     */
    protected function getRedisKey()
    {
        return $this->prefix . $this->telegramApi->getApiBot()->getMe()->getId() . '_' . $this->chatId;
    }

    /**
     * TODO:Add your description
     *
     * @param $key
     * @param $date
     *
     * @return $this
     */
    protected function writeToRedis($key, $date)
    {
        $data = [
            'time'=>$date,
        ];

        $this->cache->set($key, json_encode($data));
        return $this;
    }



}