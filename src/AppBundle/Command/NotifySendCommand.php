<?php

namespace AppBundle\Command;

use AppBundle\Model\WorkMonitoringService;
use AppBundle\View\TelegramViewer\TelegramViewer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NotifySendCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:notify')
            ->setDescription('Telegram command cron')
            ->addArgument('projectId', InputArgument::REQUIRED, 'Worksnaps project Id')
            ->addArgument('salary', InputArgument::REQUIRED, 'Salary ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var WorkMonitoringService $monitoringService
         */
        $monitoringService = $this->getContainer()->get('app.model.work_monitoring_service');

        $monitoringService->setProjectId($input->getArgument('projectId'))
                          ->setSalary($input->getArgument('salary'));

        /**
         * @var TelegramViewer $telegramViewer
         */
        $telegramManager = $this->getContainer()->get('app.model.telegram_manager');

        $telegramManager->processUpdates();
    }

}
