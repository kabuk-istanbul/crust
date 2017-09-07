<?php

namespace Crust\Factory\Installers;

use Crust\Crust;
use Crust\Factory\Objects\Theme;
use Crust\Helpers\Klasor;

class FluInstaller implements Installer
{
    public $theme;

    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    public function install()
    {
        if (!file_exists(Crust::TEMP_DIR . '/flu')) {
            shell_exec('git clone https://github.com/kabuk-istanbul/flu.git ' . Crust::TEMP_DIR . '/flu');
        }
        if (!file_exists($this->theme->dir() . '/src/styl/flu')) {
            Klasor::copy(Crust::TEMP_DIR . '/flu/flu', $this->theme->dir() . '/src/styl/flu');
        }
    }
}