<?php

namespace Crust\Objects;

use Crust\Helpers\Utils;

class Meta extends Base
{
    public $type;

    function __construct($name, $settings = [])
    {
        parent::__construct($name);
        $this->initSettings($settings);
    }

    function initSettings($settings)
    {
        $defaultSettings = [
            'type' => 'string', // number, key_value, select
            'single' => true,
            'position' => 'advanced',
            'has_column' => false,
            'source' => null
        ];

        $this->settings = array_replace_recursive($defaultSettings, $settings);
    }

    function isSingle()
    {
        return $this->settings['single'];
    }
}
