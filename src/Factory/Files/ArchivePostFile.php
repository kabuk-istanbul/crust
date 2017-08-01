<?php

namespace Cruster\Factory\Files;

use Cruster\Cruster;
use Cruster\Factory\Objects\PostType;
use Cruster\Factory\Objects\Theme;

class ArchivePostFile implements File {

    protected $theme;
    protected $postType;

    public function __construct(Theme $theme, PostType $postType = null)
    {
        $this->theme = $theme;
        $this->postType = $postType;
    }

    public function create()
    {
        touch($this->fileName());

        $tpl = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR);

        $content = Cruster::renderTemplate($tpl, [
            'post_type' => $this->postType,
            'metas' => array_values($this->postType->metas())
        ]);

        file_put_contents($this->fileName(), $content);
    }

    public function fileName()
    {
        return $this->postType->id == 'post' ?
            $this->theme->dir() . DIRECTORY_SEPARATOR . 'archive.php' :
            $this->theme->dir() . DIRECTORY_SEPARATOR . 'archive-' . $this->postType->slug . '.php';
    }
}