<?php

namespace Crust\Objects;

use Stringy\StaticStringy as Stringy;

class Base {

    public $id;
    public $name;
    public $slug;
    protected $settings = [];

    public function __construct($name)
    {
        $this->id = Stringy::slugify($name, '_');
        $this->name = Stringy::toTitleCase($name);
        $this->slug = Stringy::slugify($this->name);
    }

    public function settings($name = null)
    {
        return $name == null ? $this->settings : $this->settings[$name];
    }

}