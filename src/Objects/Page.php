<?php

namespace Crust\Objects;

use Crust\Helpers\Utils;

class Page extends Base
{
    public $theme;

    function __construct($name, $settings)
    {
        parent::__construct($name);
        $this->initSettings($settings);
    }

    private function initSettings($settings)
    {
        $defaultSettings = [];
        $this->settings = Utils::join($defaultSettings, $settings);
    }
}