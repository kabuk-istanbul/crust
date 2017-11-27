<?php

namespace Crust\Objects;

use Crust\Crust;
use Stringy\StaticStringy as Stringy;

class Theme extends Base
{
    public $crust;

    protected $postTypes = [];
    protected $taxonomies = [];
    protected $languages = [];
    protected $texts = [];
    protected $options = [];

    function __construct(Crust $crust, $name, $settings)
    {
        parent::__construct($name);
        $this->crust = $crust;
        $this->options[] = new OptionGroup('default');
        $this->initSettings($settings);
    }

    public function dir()
    {
        return $this->crust->wpDir . '/wp-content/themes/' . $this->slug;
    }

    private function initSettings($settings = [])
    {
        $this->texts[] = 'Main Menu';
        $this->texts[] = 'Social Links Menu';
        $this->texts[] = 'Footer Menu';

        $defaultSettings = [
            'languages' => ['en']
        ];
        $this->settings = array_replace_recursive($defaultSettings, $settings);
    }

    public function languages()
    {
        return $this->settings['languages'];
    }

    public function isMultilingual()
    {
        return count($this->settings['languages']) > 1;
    }

    public function addPostType(PostType $postType)
    {
        if (!$this->hasPostType($postType->id)) {
            $this->postTypes[] = $postType;
            $postType->theme = $this;

            foreach ($postType->labels() as $label) {
                $this->addText($label);
            }

            $taxonomies = $postType->taxonomies();
            foreach ($taxonomies as $taxonomy) {
                $this->addTaxonomy($taxonomy);
            }
        }
        return $this;
    }

    public function hasPostType($key)
    {
        $id = Stringy::slugify($key, '_');
        foreach ($this->postTypes as $postType) {
            if ($postType->id == $id) {
                return true;
            }
        }
        return false;
    }

    public function postTypes()
    {
        return $this->postTypes;
    }

    public function addTaxonomy(Taxonomy $taxonomy)
    {
        if (!$this->hasTaxonomy($taxonomy->id)) {
            $this->taxonomies[] = $taxonomy;
            $taxonomy->theme = $this;

            foreach ($taxonomy->labels() as $label) {
                $this->addText($label);
            }

            $postTypes = $taxonomy->postTypes();
            foreach ($postTypes as $postType) {
                $this->addPostType($postType);
            }
        }
        return $this;
    }

    public function hasTaxonomy($key)
    {
        $id = Stringy::slugify($key, '_');
        foreach ($this->taxonomies as $taxonomy) {
            if ($taxonomy->id == $id) {
                return true;
            }
        }
        return false;
    }

    public function taxonomies()
    {
        return $this->taxonomies;
    }

    public function create()
    {
        $this->createThemeFiles();
        $this->createPostTypeFiles();
        $this->createTaxonomyFiles();
        $this->createLanguageFiles();
        return $this;
    }

    public function installPackage($package)
    {
        $this->crust->installPackage($package);
        return $this;
    }

    private function createThemeFiles()
    {
        $this->crust->output->writeln('<title>Creating Theme Files</title>');
        $this->createDir();
        $this->createBaseFiles();
    }

    private function createDir()
    {
        $this->crust->fs->dir($this->dir());
        $this->crust->fs->dir($this->dir() . '/inc');
        $this->crust->fs->copyTo(__DIR__ . '/../screenshot.png', $this->dir());
    }

    private function createBaseFiles()
    {
        $this->crust->output->writeln('<title>Creating Base Files</title>');
        $this->crust->renderFile($this->dir() . '/functions.php', ['theme' => $this]);
        $this->crust->renderFile($this->dir() . '/header.php', ['theme' => $this]);
        $this->crust->renderFile($this->dir() . '/footer.php', ['theme' => $this]);
        $this->crust->renderFile($this->dir() . '/single.php', ['theme' => $this]);
        $this->crust->renderFile($this->dir() . '/archive.php', ['theme' => $this]);
        $this->crust->renderFile($this->dir() . '/page.php', ['theme' => $this]);
        $this->crust->renderFile($this->dir() . '/front-page.php', ['theme' => $this]);
        $this->crust->renderFile($this->dir() . '/404.php', ['theme' => $this]);
        $this->crust->renderFile($this->dir() . '/index.php', ['theme' => $this]);
        $this->crust->renderFile($this->dir() . '/style.css', ['theme' => $this]);
        $this->crust->renderFile($this->dir() . '/theme.js', ['theme' => $this]);
        $this->crust->renderFile($this->dir() . '/inc/options.php', ['theme' => $this]);
    }

    private function createPostTypeFiles()
    {
        foreach ($this->postTypes as $id => $postType) {
            $this->crust->renderFile($this->dir() . '/single-' . $postType->slug . '.php', ['postType' => $postType], 'single-post-type.php.twig');
            $this->crust->renderFile($this->dir() . '/archive-' . $postType->slug . '.php', ['postType' => $postType], 'archive-post-type.php.twig');
            $this->crust->renderFile($this->dir() . '/inc/post-type-' . $postType->slug . '.php', ['postType' => $postType], 'register-post-type.php.twig');
        }
    }

    private function createTaxonomyFiles()
    {
        foreach ($this->taxonomies as $taxonomy) {
            $this->crust->renderFile($this->dir() . '/inc/taxonomy-' . $taxonomy->slug . '.php', ['taxonomy' => $taxonomy], 'register-taxonomy.php.twig');
            $this->crust->renderFile($this->dir() . '/taxonomy-' . $taxonomy->slug . '.php', ['taxonomy' => $taxonomy], 'taxonomy-custom.php.twig');
        }
    }

    private function createLanguageFiles()
    {
        $this->crust->fs->dir($this->dir() . '/languages');
        foreach ($this->languages() as $language) {
            $file = $language . '_' . strtoupper($language) . '.po';
            $this->crust->renderFile($this->dir() . '/languages/' . $file, ['theme' => $this, 'language' => $language], 'lang.po.twig');
        }
    }

    public function addText($text)
    {
        if (!in_array($text, $this->texts)) {
            $this->texts[] = $text;
        }
        return $this;
    }

    public function addTexts(array $texts)
    {
        foreach ($texts as $text) {
            $this->addText($text);
        }
        return $this;
    }

    public function texts()
    {
        return $this->texts;
    }

    public function addOption(Option $option)
    {
        $this->options[0]->addOption($option);
        $this->addText($option->settings('title'));
        return $this;
    }

    public function addOptionGroup(OptionGroup $optionGroup)
    {
        $this->options[] = $optionGroup;
        return $this;
    }

    public function options()
    {
        return $this->options;
    }
}
