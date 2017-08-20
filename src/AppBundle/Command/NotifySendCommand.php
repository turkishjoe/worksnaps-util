<?php

namespace AppBundle\Command;

use AppBundle\Model\CalcModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

class NotifySendCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:notify')
            ->setDescription('...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var \SymfonyBundles\RedisBundle\Service\Client $redis
         */
        $redis = $this->getContainer()->get('sb_redis.client.default');
        /**
         * @var \Shaygan\TelegramBotApiBundle\TelegramBotApi  $api
         */
        $api = $this->getContainer()->get('shaygan.telegram_bot_api');

        /**
         * @var Update[] $updates
         */
        $updates = $api->getApiBot()->getUpdates();

        foreach($updates as $update)
        {
            /**
             * @var Message $message
             */
            $message = $update->getMessage();
            $userId = $message->getFrom()->getId();

            if($userId != $this->getContainer()->getParameter('my_user_id'))
            {
                $output->writeln('Sorry it is private bot. Please delete it');
            }
            else
            {
                $key = $this->getRedisKey($api, $userId);
                $isWrite = false;
                $keys = $redis->keys($key);
                if(!empty($keys[0]) && count($keys) == 1)
                {
                    $redisData = json_decode($redis->get($keys[0]), true);

                    if(empty($redisData['time']) || $redisData['time'] < $message->getDate() && $message->getText(
                        ) == '/get'
                    )
                    {
                        $isWrite = true;
                    }
                }
                else if(empty($keys))
                {
                    $isWrite = true;
                }
                else
                {
                    //TODO:LOG ERROR
                }

                if($isWrite)
                {
                    $this->writeToRedis($key, $message->getDate());
                    $api->getApiBot()->sendMessage($userId, $this->prepareData());
                }
            }
        }

        $output->writeln('Ok');
    }

    protected function send()
    {

    }

    protected function prepareData()
    {
        $data = (new CalcModel())->calc($this->getContainer()->get('umbrella.worksnaps'));

        $str = '';

        foreach ($data as $key=>$value)
        {
            $str .= $key . '=' . $value . PHP_EOL;
        }


        return $str;
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

        $this->getContainer()->get('sb_redis.client.default')->set($key, json_encode($data));
        return $this;
    }

    /**
     * TODO:Add your description
     *
     * @param $api
     * @param $userId
     *
     * @return string
     */
    protected function getRedisKey($api, $userId)
    {
        return $this->getContainer()->getParameter('redis_prefix'). $api->getApiBot()->getMe()->getId() . '_' . $userId;
    }
}
