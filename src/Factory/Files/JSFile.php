<?php

namespace Cruster\Factory\Files;

use Cruster\Cruster;
use Cruster\Factory\Objects\Theme;

class JSFile implements File {

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
        $cssPreprocessor = $this->theme->settings()['front_end_tools']['css_preprocessor'];
        $extension = $cssPreprocessor == 'stylus' ? 'styl' : $cssPreprocessor;

        return $this->theme->dir() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $this->theme->slug . '.js';
    }
}