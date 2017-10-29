<?php

namespace Crust\Console;

use Crust\Crust;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Installs Crust. Downloads and installs WordPress.')
            ->addOption(
                'wordpress',
                'w',
                InputOption::VALUE_NONE,
                'Include WordPress installation.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $includeWp = $input->getOption('wordpress');
        $crust = new Crust($input, $output);
        $crust->install();
        if ($includeWp) $crust->installPackage('WordPress');
    }
}