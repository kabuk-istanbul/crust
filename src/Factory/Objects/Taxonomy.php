<?php

namespace Crust\Factory\Objects;

use Doctrine\Common\Inflector\Inflector;
use Stringy\StaticStringy as Stringy;

class Taxonomy
{
    public $id;
    public $name;
    public $slug;
    public $theme;

    protected $postTypes = array();
    protected $settings;
    protected $labels;

    function __construct($name, $settings)
    {
        $this->id = Stringy::slugify($name, '_');
        $this->name = Stringy::toTitleCase($name);
        $this->slug = Stringy::slugify($name);

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
        $this->postTypes[] = $postType;
        return $this;
    }

    private function initSettings($settings)
    {
        $defaultOptions = [
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => array('slug' => $this->slug),
        ];

        $this->settings = array_replace_recursive($defaultOptions, $settings);
        var_dump($this->settings);
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

    public function createFiles() // TODO Taxonomy archive
    {
        $render = $this->theme->scope->renderer->render('register-taxonomy.php.twig', array('taxonomy' => $this));
        file_put_contents($this->theme->dir() . '/inc/taxonomy-' . $this->slug . '.php', $render);
    }
}