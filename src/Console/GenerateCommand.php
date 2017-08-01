<?php

namespace Cruster\Console;

use Cruster\Cruster;
use Cruster\Factory\Factory;
use Cruster\Factory\Meta;
use Cruster\Factory\MetaType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command {
    protected function configure() {
        $this
            ->setName('generate')
            ->setAliases(['g'])
            ->setDescription('Builds Fii app.')
            ->addArgument('item', InputArgument::REQUIRED, 'Generate what?')
            ->addArgument('name', InputArgument::REQUIRED, 'What is its name?')
            ->addArgument('options', InputArgument::OPTIONAL, 'Options');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $item = $input->getArgument('item');
        $name = $input->getArgument('name');
        $options = $input->getArgument('options') || [];

        switch ($item) {
            case 'post-type':
                Cruster::postType($name, $options)->generate();
                break;
            case 'taxonomy':
                Cruster::taxonomy($name, $options)->generate();
                break;
        }
    }
}