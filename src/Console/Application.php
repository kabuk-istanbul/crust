<?php

namespace Crust\Console;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Application extends ConsoleApplication
{

    public function start()
    {
        $this->init();
        $this->run();
    }

    public function init()
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(ConsoleEvents::ERROR, function (ConsoleErrorEvent $event)
        {
            $input = $event->getInput();
            $output = $event->getOutput();

            if ($event->getError() instanceof CommandNotFoundException) {
                $commandName = $this->getCommandName($input);
                $command = $this->find('run');

                $arguments = array(
                    'command' => 'run',
                    'method' => $commandName
                );

                $runInput = new ArrayInput($arguments);
                $command->run($runInput, $output);
            }
            else {
                throw $event->getError();
            }
        });

        $this->setDispatcher($dispatcher);
        $this->setCatchExceptions(false);
        $this->loadCommands();
    }

    private function loadCommands()
    {
        $this->add(new RunCommand());
        $this->add(new InstallCommand());
        $this->setDefaultCommand('run');
    }
}