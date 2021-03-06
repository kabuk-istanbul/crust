<?php

namespace Crust\Objects;

class Meta extends Base
{
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
            'source' => null,
            'description' => null
        ];

        $this->settings = array_replace_recursive($defaultSettings, $settings);
    }

    function isSingle()
    {
        return $this->settings['single'];
    }
}
