<?php

namespace Cruster\Factory\Files;

use Cruster\Cruster;
use Cruster\Factory\Objects\Taxonomy;
use Cruster\Factory\Objects\Theme;

class TaxonomyFile implements File {

    public $taxonomy;
    public $theme;

    public function __construct(Theme $theme, Taxonomy $taxonomy)
    {
        $this->theme = $theme;
        $this->taxonomy = $taxonomy;
    }

    public function create()
    {
        touch($this->fileName());

        $tpl = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR);

        $content = Cruster::renderTemplate($tpl, [
            'theme' => $this->theme,
            'taxonomy' => $this->taxonomy,
            'post_types' => array_values($this->taxonomy->postTypes())
        ]);

        file_put_contents($this->fileName(), $content);
    }

    public function fileName()
    {
        return $this->theme->includedFilesDir() . DIRECTORY_SEPARATOR . 'taxonomy-' . $this->taxonomy->slug . '.php';
    }
}