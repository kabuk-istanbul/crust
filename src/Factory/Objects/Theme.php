<?php

namespace Cruster\Factory\Objects;

use Cruster\Cruster;
use Cruster\Factory\Files\ArchivePostFile;
use Cruster\Factory\Files\FooterFile;
use Cruster\Factory\Files\FunctionsFile;
use Cruster\Factory\Files\GulpFile;
use Cruster\Factory\Files\HeaderFile;
use Cruster\Factory\Files\JSFile;
use Cruster\Factory\Files\LanguageFile;
use Cruster\Factory\Files\PackageJsonFile;
use Cruster\Factory\Files\PageFile;
use Cruster\Factory\Files\PostTypeFile;
use Cruster\Factory\Files\SinglePostFile;
use Cruster\Factory\Files\StyleFile;
use Cruster\Factory\Files\TaxonomyFile;
use Cruster\Factory\Files\WebpackConfigFile;
use Cruster\Helpers\Klasor;
use Stringy\StaticStringy as Stringy;

class Theme
{
    public $id;
    public $name;
    public $slug;

    protected $settings = [];
    protected $postTypes = [];
    protected $taxonomies = [];
    protected $languages = [];

    function __construct($name, $settings)
    {
        $this->id = Stringy::slugify($name, '_');
        $this->name = Stringy::toTitleCase($name);
        $this->slug = Stringy::slugify($name);

        $this->initSettings($settings);
    }

    public function dir()
    {
        return Cruster::WP_DIR . '/wp-content/themes/' . $this->slug;
    }

    public function settings($name = null)
    {
        if ($name == null) {
            return $this->settings;
        }
        return $this->settings[$name];
    }

    public function languages()
    {
        return $this->settings['languages'];
    }

    public function isMultilingual()
    {
        return count($this->settings['languages']) > 1;
    }

    public function postTypes()
    {
        return $this->postTypes;
    }

    public function addPostType(PostType $postType)
    {
        if (empty($this->postTypes[$postType->id])) {
            $this->postTypes[$postType->id] = $postType;

            $taxonomies = $postType->taxonomies();
            foreach ($taxonomies as $id => $taxonomy) {
                $this->addTaxonomy($taxonomy);
            }
        }
        return $this;
    }

    public function taxonomies()
    {
        return $this->taxonomies;
    }

    public function addTaxonomy(Taxonomy $taxonomy)
    {
        if (empty($this->taxonomies[$taxonomy->id])) {
            $this->taxonomies[$taxonomy->id] = $taxonomy;

            $postTypes = $taxonomy->postTypes();
            foreach ($postTypes as $id => $postType) {
                $this->addPostType($postType);
            }
        }
        return $this;
    }

    public function collectTranslationTexts()
    {
        $translationTexts = [
            'Main Menu',
            'Social Links Menu',
            'Footer Menu'
        ];

        foreach ($this->postTypes as $id => $postType) {
            foreach ($postType->labels() as $label) {
                $translationTexts[] = $label;
            }
        }

        foreach ($this->taxonomies as $id => $taxonomy) {
            foreach ($taxonomy->labels() as $label) {
                $translationTexts[] = $label;
            }
        }

        return $translationTexts;
    }

    public function generate()
    {
        $this->createThemeFiles();
        $this->createPostTypeFiles();
        //$this->createTaxonomyFiles();
        //$this->createLanguageFiles();
    }

    private function initSettings($settings = [])
    {
        $defaultSettings = [
            'languages' => ['en_EN'],
            'front_end_tools' => [
                'task_runner' => 'gulp',
                'css_preprocessor' => 'stylus'
            ]
        ];

        $this->settings = array_merge_recursive($defaultSettings, $settings);

        if (!$this->settings['front_end_tools']['css_preprocessor_file_extension']) {
            switch ($this->settings['front_end_tools']['css_preprocessor']) {
                case 'stylus':
                    $this->settings['front_end_tools']['css_preprocessor_file_extension'] = 'styl';
                    break;
                default:
                    $this->settings['front_end_tools']['css_preprocessor_file_extension'] = $this->settings['front_end_tools']['css_preprocessor'];
                    break;
            }
        }
    }

    private function createThemeFiles()
    {
        $this->createDir();
        $this->createFunctionsFile();
        $this->createBaseFiles();
        $this->createFrontEndFiles();
    }

    private function createDir()
    {
        if (!file_exists($this->dir() . '/inc')) {
            mkdir($this->dir() . '/inc', 0777, true);
        }

        copy(__DIR__ . '/../../screenshot.png', $this->dir() . '/screenshot.png');
    }

    private function createFunctionsFile()
    {
        $functionsFile = new FunctionsFile($this);
        $functionsFile->create();
    }

    private function createBaseFiles() {
        $headerFile = new HeaderFile($this);
        $headerFile->create();

        $footerFile = new FooterFile($this);
        $footerFile->create();

        $singleFile = new SinglePostFile($this, new PostType('Post', []));
        $singleFile->create();

        $pageFile = new PageFile($this, new Page('Page', []));
        $pageFile->create();

        $frontPageFile = new PageFile($this, new Page('Front', []));
        $frontPageFile->create();

        touch($this->dir() . DIRECTORY_SEPARATOR . 'index.php');
        touch($this->dir() . DIRECTORY_SEPARATOR . '404.php');
    }

    private function createFrontEndFiles()
    {
        Klasor::mkdir($this->dir() . '/src');
        Klasor::mkdir($this->dir() . '/src/' . $this->settings['front_end_tools']['css_preprocessor_file_extension']);
        Klasor::mkdir($this->dir() . '/src/js');
        Klasor::mkdir($this->dir() . '/src/img');
        Klasor::mkdir($this->dir() . '/src/svg');

        $styleFile = new StyleFile($this);
        $styleFile->create();

        $jsFile = new JSFile($this);
        $jsFile->create();

        $gulpFile = new GulpFile($this);
        $gulpFile->create();

        $packageJsonFile = new PackageJsonFile($this);
        $packageJsonFile->create();

        $webpackConfigFile = new WebpackConfigFile($this);
        $webpackConfigFile->create();
    }

    private function createPostTypeFiles()
    {
        foreach ($this->postTypes as $id => $postType) {
            $postTypeFile = new PostTypeFile($this, $postType);
            $postTypeFile->create();

            $singlePostFile = new SinglePostFile($this, $postType);
            $singlePostFile->create();

            $archivePostFile = new ArchivePostFile($this, $postType);
            $archivePostFile->create();
        }
    }

    private function createTaxonomyFiles()
    {
        foreach ($this->taxonomies as $taxonomy) {
            $taxonomyFile = new TaxonomyFile($this, $taxonomy);
            $taxonomyFile->create();
        }
    }

    private function createLanguageFiles()
    {
        $translationTexts = $this->collectTranslationTexts();

        foreach ($this->languages() as $language) {
            $languageFile = new LanguageFile($this->slug, $language, $translationTexts);
            $languageFile->create();
        }
    }
}