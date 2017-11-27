<?php

namespace Crust\Objects;

class Option extends Base
{
    function __construct($name, $settings = [])
    {
        parent::__construct($name);
        $this->initSettings($settings);
    }

    function initSettings($settings)
    {
        $defaultSettings = [
            'title' => null,
            'type' => 'string', // number, select
            'source' => null,
            'description' => null
        ];

        $this->settings = array_replace_recursive($defaultSettings, $settings);
        $this->settings['title'] = $this->settings['title'] ? $this->settings['title'] : $this->name;
    }
}
