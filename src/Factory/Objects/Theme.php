<?php

namespace Crust\Factory\Objects;

use Crust\Crust;
use Crust\Helpers\ArraySet;
use Crust\Helpers\Klasor;
use Stringy\StaticStringy as Stringy;

class Theme
{
    public $id;
    public $name;
    public $slug;
    public $scope;

    protected $settings = [];
    protected $postTypes = [];
    protected $taxonomies = [];
    protected $languages = [];

    function __construct(Crust $scope, $name, $settings)
    {
        $this->scope = $scope;
        $this->id = Stringy::slugify($name, '_');
        $this->name = Stringy::toTitleCase($name);
        $this->slug = Stringy::slugify($name);

        $this->initSettings($settings);
    }

    public function dir()
    {
        return Crust::WP_DIR . '/wp-content/themes/' . $this->slug;
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
        if (!$this->hasPostType($postType->id)) {
            $this->postTypes[] = $postType;
            $postType->theme = $this;

            $taxonomies = $postType->taxonomies();
            foreach ($taxonomies as $taxonomy) {
                $this->addTaxonomy($taxonomy);
            }
        }
        return $this;
    }

    public function hasPostType($key)
    {
        foreach ($this->postTypes as $postType) {
            if ($postType->id == $key || $postType->name == $key || $postType->slug == $key) {
                return true;
            }
        }
        return false;
    }

    public function taxonomies()
    {
        return $this->taxonomies;
    }

    public function hasTaxonomy($key)
    {
        foreach ($this->taxonomies as $taxonomy) {
            if ($taxonomy->id == $key || $taxonomy->name == $key || $taxonomy->slug == $key) {
                return true;
            }
        }
        return false;
    }

    public function addTaxonomy(Taxonomy $taxonomy)
    {
        if (!$this->hasTaxonomy($taxonomy->id)) {
            $this->taxonomies[] = $taxonomy;
            $taxonomy->theme = $this;

            $postTypes = $taxonomy->postTypes();
            foreach ($postTypes as $postType) {
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
        return $this->create();
    }

    public function create()
    {
        $this->createThemeFiles();
        $this->createPostTypeFiles();
        $this->createTaxonomyFiles();
        //$this->createLanguageFiles();
        return $this;
    }

    public function install($item)
    {
        $this->scope->output->writeln('<title>Installing ' . Stringy::upperCaseFirst($item) . '</title>');
        $installerClass = '\\Crust\\Factory\\Installers\\' . Stringy::upperCaseFirst($item) . 'Installer';
        $installer = new $installerClass($this);
        $installer->install();
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

        $this->settings = ArraySet::join($defaultSettings, $settings);

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
        $this->createBaseFiles();
        $this->createFrontEndFiles();
    }

    private function createDir()
    {
        $this->scope->output->writeln('<title>Creating Theme Directories</title>');
        if (!file_exists($this->dir() . '/inc')) {
            mkdir($this->dir() . '/inc', 0777, true);
        }
        copy(__DIR__ . '/../../screenshot.png', $this->dir() . '/screenshot.png');
    }

    private function createFile($file, $templateName = null)
    {
        if (!file_exists($file)) {
            $fileName = basename($file);

            $this->scope->output->write("         $fileName");
            if ($templateName == null) {
                $templateName = $fileName . '.twig';
            }
            $render = $this->scope->renderer->render($templateName, array('theme' => $this));
            if (file_put_contents($file, $render)) {
                $this->scope->output->writeln(' <success>✓</success>');
            }
            else {
                $this->scope->output->writeln(' <error>✗</error>');
            }
        }
    }

    private function createBaseFiles()
    {
        $this->scope->output->writeln('<title>Creating Base Files</title>');
        $this->createFile($this->dir() . '/functions.php');
        $this->createFile($this->dir() . '/header.php');
        $this->createFile($this->dir() . '/footer.php');
        $this->createFile($this->dir() . '/single.php');
        $this->createFile($this->dir() . '/archive.php');
        $this->createFile($this->dir() . '/page.php');
        $this->createFile($this->dir() . '/front-page.php');
        $this->createFile($this->dir() . '/404.php');
        $this->createFile($this->dir() . '/index.php');
    }

    private function createFrontEndFiles()
    {
        $this->scope->output->writeln('<title>Creating Front-end Files</title>');
        Klasor::mkdir($this->dir() . '/src');
        Klasor::mkdir($this->dir() . '/src/' . $this->settings['front_end_tools']['css_preprocessor_file_extension']);
        Klasor::mkdir($this->dir() . '/src/js');
        Klasor::mkdir($this->dir() . '/src/img');
        Klasor::mkdir($this->dir() . '/src/svg');
        Klasor::mkdir($this->dir() . '/src/font');

        $this->createFile($this->dir() . '/style.css');
        $this->createFile($this->dir() . '/src/' . $this->settings['front_end_tools']['css_preprocessor_file_extension'] . '/style.' . $this->settings['front_end_tools']['css_preprocessor_file_extension'], 'style.css.twig');
        $this->createFile($this->dir() . '/src/js/theme.js');
        $this->createFile($this->dir() . '/gulpfile.js');
        $this->createFile($this->dir() . '/package.json');
        $this->createFile($this->dir() . '/webpack.config.js');
    }

    private function createPostTypeFiles()
    {
        foreach ($this->postTypes as $id => $postType) {
            $postType->createFiles();
        }
    }

    private function createTaxonomyFiles()
    {
        foreach ($this->taxonomies as $taxonomy) {
            $taxonomy->createFiles();
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
