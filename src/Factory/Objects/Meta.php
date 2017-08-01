<?php

namespace Cruster\Factory\Objects;

use Stringy\StaticStringy as Stringy;

class Meta {

    const SINGLE = 1;
    const MULTIPLE = 2;

    const STRING_TYPE = 1;
    const BOOLEAN_TYPE = 2;
    const DATE_TYPE = 3;
    const ARRAY_TYPE = 4;
    const POST_TYPE = 5;

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
            'type' => Meta::STRING_TYPE,
            'single' => true,
            'position' => 'advanced',
            'has_column' => false,
            'source' => null
        ];

        $this->settings = array_merge($defaultSettings, $settings);
    }

    function position()
    {
        return $this->settings['position'];
    }

    function hasColumn()
    {
        return $this->settings['has_column'];
    }

    function source()
    {
        return Stringy::slugify($this->settings['source']);
    }

    function isString()
    {
        return $this->settings['type'] == Meta::STRING_TYPE;
    }
}