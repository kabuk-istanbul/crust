<?php

namespace Crust\Console;

use Crust\Crust;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    private $scope;

    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Init Crust.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->scope = new Crust($input, $output);
        $this->createFilesAndFolders();
    }

    private function createFilesAndFolders()
    {
        if (!file_exists('./crust-file.php')) {
            touch('./crust-file.php');
        }

        if (!file_exists('./.Crust')) {
            mkdir('./.Crust');
        }

        if (file_exists('./.Crust/tmp')) {
            mkdir('./.Crust/tmp');
        }
    }
}