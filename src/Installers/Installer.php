<?php

namespace Crust\Installers;

use Crust\Crust;

class Installer {

    protected $crust;

    public function __construct(Crust $crust)
    {
        $this->crust = $crust;
    }
}