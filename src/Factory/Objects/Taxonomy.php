<?php

namespace Cruster\Factory\Objects;

use Cruster\Cruster;
use Doctrine\Common\Inflector\Inflector;
use Nette\PhpGenerator\Helpers;
use Stringy\StaticStringy as Stringy;

class Taxonomy
{
    public $id;
    public $name;
    public $slug;

    public $fileName;
    public $theme;

    protected $postTypes = [];
    protected $settings;
    protected $labels;

    function __construct($name, $settings)
    {
        $this->id = Stringy::slugify($name, '_');
        $this->name = Stringy::toTitleCase($name);
        $this->slug = Stringy::slugify($name);

        $this->fileName = 'taxonomy-' . $this->slug . '.php';

        $this->initSettings($settings);
    }

    public function settings() {
        return $this->settings;
    }

    public function labels() {
        return $this->labels;
    }

    public function postTypes()
    {
        return $this->postTypes;
    }

    public function addPostType(PostType $postType)
    {
        $this->postTypes[$postType->id] = $postType;
        return $this;
    }

    public function generate()
    {
        $this->createFiles();

        $content = '<?php' . PHP_EOL . PHP_EOL .
            $this->generateRegisterTaxonomyFunction();

        file_put_contents(Cruster::INCLUDE_DIR . DIRECTORY_SEPARATOR . $this->fileName, $content);
    }

    private function initSettings($settings)
    {
        $defaultOptions = [
            'hierarchical' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => array('slug' => $this->slug),
        ];

        $this->settings = array_merge($defaultOptions, $settings);
        $this->generateLabels();
    }

    private function generateLabels()
    {
        $pluralName = Inflector::pluralize($this->name);

        $this->labels = [
            'name' => $pluralName,
            'singular_name' => $this->name,
            'menu_name' => $pluralName,
            'all_items' => 'All ' . $pluralName,
            'edit_item' => 'Edit ' . $this->name,
            'view_item' => 'View ' . $this->name,
            'update_item' => 'Update ' . $this->name,
            'add_new_item' => 'Add New ' . $this->name,
            'new_item_name' => 'New ' . $this->name . ' Name',
            'parent_item' => 'Parent ' . $this->name,
            'parent_item_colon' => 'Parent ' . $this->name . ':',
            'search_items' => 'Search ' . $pluralName,
            'popular_items' => 'Popular ' . $pluralName,
            'separate_items_with_commas' => 'Separate ' . $pluralName . ' with commas',
            'add_or_remove_items' => 'Add or remove ' . $pluralName,
            'choose_from_most_used' => 'Choose from the most used ' . $pluralName,
            'not_found' => 'No ' . $pluralName . ' found.'
        ];
    }

    private function createFiles()
    {
        touch(Cruster::INCLUDE_DIR . DIRECTORY_SEPARATOR . $this->fileName);
        touch('taxonomy-' . $this->slug . '.php');
    }

    private function generateRegisterTaxonomyFunction()
    {
        $str = '$options = ' . Helpers::dump($this->options, true) . ';' . PHP_EOL . PHP_EOL .
            'register_taxonomy(\'' . $this->slug . '\', $options);';
        if (count($this->postTypes) > 0) {
            $str .= PHP_EOL . PHP_EOL;

            foreach ($this->postTypes as $id => $postType) {
                $str .= 'register_taxonomy_for_object_type(\'' . $this->slug . '\', \'' . $postType->slug . '\');' . PHP_EOL;
            }
        }

        return $str;
    }
}