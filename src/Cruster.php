<?php

namespace Cruster;

use Cruster\Factory\Objects\Meta;
use Cruster\Factory\Objects\Page;
use Cruster\Factory\Objects\PostType;
use Cruster\Factory\Objects\Taxonomy;
use Cruster\Factory\Objects\Theme;
use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class Cruster {

    const POST_TYPE_DIR = 'inc';
    const INCLUDE_DIR = 'inc';

    const THEME_NAME = 'Cruster';
    const WP_DIR = '.';
    const WP_THEMES_DIR = 'wp-content' . DIRECTORY_SEPARATOR . 'themes';
    const THEME_INCLUDE_DIR = 'inc';

    const SETTINGS_DIR = './.cruster';
    const TEMP_DIR = './.cruster/tmp';

    static private $app;

    static public $dir = '';

    static private $themeName = 'Cruster';
    static $theme;

    static $renderer = null;

    static $fs;
    static $output;
    static $input;

    static $extensions = [
        'stylus' => 'styl',
        'sass' => 'scss'
    ];

    static $postTypes = [];

    static public function init()
    {
        self::$fs = new Filesystem();
    }

    static public function setIO(InputInterface $input, OutputInterface $output)
    {
        self::setInput($input);
        self::setOutput($output);
    }

    static public function setOutput(OutputInterface $output)
    {
        self::$output = $output;
        self::setOutputFormatterStyles();
    }

    static public function getOutput() : OutputInterface
    {
        return self::$output;
    }

    static public function setInput(InputInterface $input)
    {
        self::$input = $input;
    }

    static public function getInput() : InputInterface
    {
        return self::$input;
    }

    static private function setOutputFormatterStyles()
    {
        $style = new OutputFormatterStyle('blue', null, ['bold']);
        self::$output->getFormatter()->setStyle('title', $style);

        $style = new OutputFormatterStyle('green');
        self::$output->getFormatter()->setStyle('success', $style);

        $style = new OutputFormatterStyle('red');
        self::$output->getFormatter()->setStyle('error', $style);
    }

    static public function fs() : Filesystem
    {
        return self::$fs;
    }

    static public function getFs() : Filesystem
    {
        return self::$fs;
    }

    static public function out() : OutputInterface
    {
        return self::$output;
    }

    static public function theme($name = null, $settings = []) : Theme
    {
        $name = $name ? $name : self::THEME_NAME;
        self::$theme = new Theme($name, $settings);
        return self::$theme;
    }

    static function taxonomy($name, $options = []) : Taxonomy
    {
        $taxonomy = new Taxonomy($name, $options);
        return $taxonomy;
    }

    static function postType($name, $settings = []) : PostType
    {
        $postType = new PostType($name, $settings);
        return $postType;
    }

    static function meta($name, $type = Meta::STRING_TYPE, $settings = []) : Meta
    {
        $meta = new Meta($name, $type, $settings);
        return $meta;
    }

    static function page($name, $settings = []) : Page
    {
        $page = new Page($name, $settings);
        return $page;
    }

    static function renderTemplate($tpl, $vars) {
        if (!self::$renderer) {
            self::$renderer = new Mustache_Engine([
                'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/Factory/Templates/Partials')
            ]);
        }
        return self::$renderer->render($tpl, $vars);
    }

    static public function progressBar() : ProgressBar
    {
        $progressBar = new ProgressBar(self::getOutput(), 100);
        $progressBar->setFormat('[%bar%] %percent%% %message%');
        $progressBar->setBarCharacter('■');
        $progressBar->setEmptyBarCharacter('⁃');
        $progressBar->setProgressCharacter('■');
        $progressBar->setBarWidth(50);
        return $progressBar;
    }
}