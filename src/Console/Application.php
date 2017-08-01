<?php

namespace Cruster\Console;

use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{

    public function init()
    {
        $this->loadCommands();
    }

    private function loadCommands()
    {
        $this->add(new InitCommand());
        $this->add(new GenerateCommand());
        $this->add(new RunCommand());
        $this->add(new InstallWordpressCommand());
        $this->setDefaultCommand('run');
    }
}