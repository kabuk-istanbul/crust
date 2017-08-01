<?php

namespace Cruster\Factory\Files;

use Cruster\Cruster;

class Template
{

    protected $template;
    protected $content;
    protected $fileName;

    public function __construct($template, $content, $fileName)
    {
        $this->theme = $theme;
    }

    public function create()
    {
        touch($this->fileName);

        $tpl = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR);

        $content = Cruster::renderTemplate($tpl, [
            'theme' => $this->theme
        ]);

        file_put_contents($this->fileName(), $content);
    }
}