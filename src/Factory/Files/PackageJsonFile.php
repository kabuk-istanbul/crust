<?php

namespace Cruster\Factory\Files;

use Cruster\Cruster;
use Cruster\Factory\Objects\Theme;

class PackageJsonFile implements File {

    public $theme;

    function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    public function create()
    {
        touch($this->fileName());

        $tpl = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR);

        $content = Cruster::renderTemplate($tpl, [
            'theme' => $this->theme
        ]);

        file_put_contents($this->fileName(), $content);
    }

    public function fileName()
    {
        return $this->theme->dir() . DIRECTORY_SEPARATOR . 'package.json';
    }
}