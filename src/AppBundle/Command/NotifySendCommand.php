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
            ->addArgument('telegramChatId', InputArgument::REQUIRED, 'Telegram chat Id')
            ->addArgument('projectId', InputArgument::REQUIRED, 'Worksnaps project Id')
            ->addArgument('salary', InputArgument::REQUIRED, 'Salary ')
            ->addArgument('startDate', InputArgument::REQUIRED, 'Start Date(YYYY-mm-dd)')
            ->addArgument('endDate', InputArgument::REQUIRED, 'End Date(YYYY-mm-dd)');
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
        $telegramViewer = $this->getContainer()->get('app.view.telegram_viewer');

        $workModel = $monitoringService->getSalaryInfo(
            $input->getArgument('startDate'),
            $input->getArgument('endDate')
        );

        $telegramViewer->sendWorkModel($input->getArgument('telegramChatId'), $workModel);
    }

}
