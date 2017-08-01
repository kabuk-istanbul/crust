<?php

namespace Cruster\Console;

use Cruster\Cruster;
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
            ->addArgument('method', InputArgument::OPTIONAL, 'Command name in crust-file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Cruster::$output = $output;
        Cruster::$input = $input;

        if (!file_exists('crust-file.php')) {
            Cruster::error('crust-file.php doesn\'t exist!');
            return;
        }

        require_once './crust-file.php';

        if ($input->getArgument('method')) {
            $command = 'crust_' . $input->getArgument('method');
        }
        else {
            $command = 'crust';
        }

        $command();
    }
}