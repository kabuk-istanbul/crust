<?php

namespace Crust\Console;

use Crust\Crust;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Executes crust-file.php')
            ->addArgument('method', InputArgument::OPTIONAL, 'Command name in crust-file', 'default');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $crust = new Crust($input, $output);

        if (!file_exists('crust-file.php')) {
            //Crust::error('crust-file.php doesn\'t exist!');
            return;
        }

        require_once './crust-file.php';

        $command = 'crust_' . $input->getArgument('method');
        $command($crust);
    }
}