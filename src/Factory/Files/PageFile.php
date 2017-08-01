<?php

namespace Cruster\Factory\Files;

use Cruster\Cruster;
use Cruster\Factory\Objects\Page;
use Cruster\Factory\Objects\Theme;

class PageFile implements File
{

    protected $theme;
    protected $page;

    public function __construct(Theme $theme, Page $page)
    {
        $this->theme = $theme;
        $this->page = $page;
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
        switch ($this->page->slug) {
            case 'page':
                $fileName = $this->theme->dir() . DIRECTORY_SEPARATOR . 'page' . '.php';
                break;
            case 'front':
                $fileName = $this->theme->dir() . DIRECTORY_SEPARATOR . 'front-page' . '.php';
                break;
            default:
                $fileName = $this->theme->dir() . DIRECTORY_SEPARATOR . 'page-' . $this->page->slug . '.php';
                break;
        }
        return $fileName;
    }
}