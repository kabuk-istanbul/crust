<?php

namespace Crust\Objects;

class OptionGroup extends Base
{
    public $title;
    protected $options = [];

    function __construct($name, $title = null)
    {
        parent::__construct($name);
        $this->title = $title ? $title : $this->name;
    }

    public function addOption(Option $option)
    {
        $this->options[] = $option;
        return $this;
    }

    public function options()
    {
        return $this->options;
    }
}
