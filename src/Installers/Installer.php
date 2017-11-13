<?php

namespace Crust\Installers;

use Crust\Crust;

class Installer {

    protected $crust;
    protected $settings;

    public function __construct(Crust $crust)
    {
        $this->crust = $crust;
    }
}