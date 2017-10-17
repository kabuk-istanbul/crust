<?php

namespace Crust\Factory\Objects;

use Stringy\StaticStringy as Stringy;

class Meta {

    public $id;
    public $name;
    public $slug;
    public $type;

    private $settings;

    function __construct($name, $settings = [])
    {
        $this->id = Stringy::slugify($name, '_');
        $this->name = Stringy::toTitleCase($name);
        $this->slug = Stringy::slugify($name);

        $this->initSettings($settings);
    }

    function initSettings($settings)
    {
        $defaultSettings = [
            'type' => 'string', // number, key_value
            'single' => true,
            'position' => 'advanced',
            'has_column' => false,
            'source' => null
        ];

        $this->settings = array_replace($defaultSettings, $settings);
    }

    function position()
    {
        return $this->settings['position'];
    }

    function hasColumn()
    {
        return $this->settings['has_column'];
    }

    function isSingle()
    {
        return (int) $this->settings['single'];
    }

    function source()
    {
        return Stringy::slugify($this->settings['source']);
    }

    function isString()
    {
        return $this->settings['type'] == 'string';
    }

    function isKeyValue()
    {
        return $this->settings['type'] == 'key_value';
    }
}
