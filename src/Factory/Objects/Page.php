<?php

namespace Crust\Factory\Objects;

use Crust\Helpers\ArraySet;
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

        $this->settings = ArraySet::join($defaultSettings, $settings);
    }
}
