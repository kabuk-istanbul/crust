<?php

namespace Cruster\Console;

use Cruster\Cruster;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Init cruster.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Cruster::init();
        Cruster::setIO($input, $output);
        $this->createFilesAndFolders();
    }

    private function createFilesAndFolders()
    {
        if (!Cruster::getFs()->exists('./crust-file.php')) {
            Cruster::getFs()->touch('./crust-file.php');
        }

        if (!Cruster::getFs()->exists('./.cruster')) {
            Cruster::getFs()->mkdir('./.cruster');
        }

        if (!Cruster::getFs()->exists('./.cruster/tmp')) {
            Cruster::getFs()->mkdir('./.cruster/tmp');
        }
    }
}