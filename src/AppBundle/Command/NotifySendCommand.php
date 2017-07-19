<?php

namespace AppBundle\Command;

use AppBundle\Model\CalcModel;
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
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');

        if ($input->getOption('option')) {
            // ...
        }

        $data = (new CalcModel())->calc($this->getContainer()->get('umbrella.worksnaps'));
        $api = $this->getContainer()->get('shaygan.telegram_bot_api');

        $str = '';

        foreach ($data as $key=>$value)
        {
            $str .= $key . '=' . $value . PHP_EOL;
        }

        $api->getApiBot()->sendMessage(67197900, $str);
        $output->writeln('Command result.');
    }

}
