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
            $this->scope->output->write('Creating crust-file.php');
            touch('./crust-file.php');
            $renderer = new \Mustache_Engine();
            $tpl = file_get_contents(__DIR__ . '/../Factory/Templates/crust-file.php.mustache');
            $content = $renderer->render($tpl, array());
            file_put_contents('./crust-file.php', $content);
            $this->scope->output->writeln(' <success>✓</success>');
        }

        $this->scope->output->write('Creating crust directories.');
        if (!file_exists('./.crust')) {
            mkdir('./.crust');
        }
        if (file_exists('./.crust/tmp')) {
            mkdir('./.crust/tmp');
        }
        $this->scope->output->writeln(' <success>✓</success>');

        $this->scope->output->write('Copying crust executable to project directory.');
        copy(__DIR__ . '/../../crust', './crust');
        shell_exec('chmod +x ./crust');
        $this->scope->output->writeln(' <success>✓</success>');
    }
}