<?php

namespace Cruster\Factory\Files;

use Cruster\Cruster;
use Cruster\Factory\Objects\Theme;

class StyleFile implements File {

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
        file_put_contents($this->preprocessorFileName(), $content);
    }

    public function fileName()
    {
        return $this->theme->dir() . '/style.css';
    }

    public function preprocessorFileName()
    {
        $cssPreprocessor = $this->theme->settings()['front_end_tools']['css_preprocessor'];
        $extension = $this->theme->settings()['front_end_tools']['css_preprocessor_file_extension'];

        return $this->theme->dir() . '/src/' . $extension . '/style.' . $extension;
    }
}