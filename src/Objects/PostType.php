<?php

namespace Crust\Objects;

use Crust\Helpers\Utils;
use Doctrine\Common\Inflector\Inflector;

class PostType extends Base
{
    public $theme;

    protected $metas = [];
    protected $taxonomies = [];
    protected $labels;

    function __construct($name, $settings = [])
    {
        parent::__construct($name);
        $this->initSettings($settings);
    }

    public function taxonomies()
    {
        return $this->taxonomies;
    }

    public function addTaxonomy(Taxonomy $taxonomy)
    {
        $this->taxonomies[] = $taxonomy;
        $taxonomy->addPostType($this);
        return $this;
    }

    public function metas()
    {
        return $this->metas;
    }

    public function addMeta(Meta $meta)
    {
        $this->metas[] = $meta;
        return $this;
    }

    public function hasMeta()
    {
        return count($this->metas) > 0;
    }

    public function hasMetaInColumns()
    {
        foreach ($this->metas as $meta) {
            if ($meta->settings('has_column')) return true;
        }
        return false;
    }

    public function labels()
    {
        return $this->labels;
    }

    private function initSettings($settings)
    {
        $defaultSettings = [
            'menu_position' => 5,
            'supports' => ['title', 'editor', 'thumbnail'],
            'has_archive' => true,
            'public' => true,
            'rewrite' => ['slug' => $this->slug, 'with_front' => false],
            'capability_type' => 'post'
        ];

        $this->settings = Utils::join($defaultSettings, $settings);
        $this->generateLabels();
    }

    private function generateLabels()
    {
        $pluralName = Inflector::pluralize($this->name);

        $this->labels = [
            'name' => $pluralName,
            'singular_name' => $this->name,
            'add_new' => 'Add New',
            'add_new_item' => 'Add New ' . $this->name,
            'edit_item' => 'Edit ' . $this->name,
            'new_item' => 'New ' . $this->name,
            'view_item' => 'View ' . $this->name,
            'view_items' => 'View ' . $pluralName,
            'search_items' => 'Search ' . $pluralName,
            'not_found' => 'No ' . $pluralName . ' found',
            'not_found_in_trash' => 'No ' . $pluralName . ' found in Trash',
            'parent_item_colon' => 'Parent ' . $this->name . ':',
            'all_items' => 'All ' . $pluralName,
            'archives' => $this->name . ' Archives',
            'attributes' => $this->name . ' Attributes',
            'insert_into_item' => 'Insert into ' . $this->name,
            'uploaded_to_this_item' => 'Uploaded to this ' . $this->name,
            'menu_name' => $pluralName
        ];
    }

    /*













    */
}
