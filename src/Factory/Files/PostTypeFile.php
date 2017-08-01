<?php

namespace Cruster\Factory\Files;

use Cruster\Cruster;
use Cruster\Factory\Objects\Theme;
use Cruster\Factory\Objects\PostType;

class PostTypeFile implements File {

    public $postType;
    public $theme;

    public function __construct(Theme $theme, PostType $postType)
    {
        $this->theme = $theme;
        $this->postType = $postType;
    }

    public function create()
    {
        touch($this->fileName());

        $tpl = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR);



        $content = Cruster::renderTemplate($tpl, [
            'theme' => $this->theme,
            'post_type' => $this->postType
        ]);

        file_put_contents($this->fileName(), $content);
    }

    public function fileName()
    {
        return $this->theme->dir() . '/inc/post-type-' . $this->postType->slug . '.php';
    }
}