<?php

namespace Cruster\Factory\Objects;

use Doctrine\Common\Inflector\Inflector;
use Stringy\StaticStringy as Stringy;

class Page
{

    public $id;
    public $name;
    public $slug;
    public $theme;

    protected $settings = [];

    function __construct($name, $settings)
    {
        $this->id = Stringy::slugify($name, '_');
        $this->name = Stringy::toTitleCase($name);
        $this->slug = Stringy::slugify($this->name);

        $this->initSettings($settings);
    }

    public function settings() {
        return $this->settings;
    }

    private function initSettings($settings)
    {
        $defaultSettings = [];

        $this->settings = array_merge($defaultSettings, $settings);
    }
}