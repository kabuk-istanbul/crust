<?php

namespace Crust\Objects;

use Crust\Helpers\Utils;
use Doctrine\Common\Inflector\Inflector;

class Taxonomy extends Base
{
    public $theme;

    protected $postTypes = array();
    protected $labels;

    function __construct($name, $settings)
    {
        parent::__construct($name);
        $this->initSettings($settings);
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

    public function labels()
    {
        return $this->labels;
    }

    private function initSettings($settings)
    {
        $defaultOptions = [
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => array('slug' => $this->slug)
        ];

        $this->settings = Utils::join($defaultOptions, $settings);
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
}
