<?php

namespace Crust;

use Crust\Helpers\Filesystem;
use Crust\Objects\Meta;
use Crust\Objects\Page;
use Crust\Objects\PostType;
use Crust\Objects\Taxonomy;
use Crust\Objects\Theme;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Crust {

    const THEME_NAME = 'Crust';
    const SETTINGS_DIR = './.crust';
    const TEMP_DIR = './.crust/tmp';

    public $fs;
    public $output;
    public $input;
    public $renderer;
    public $theme;
    public $wpDir = '.';
    protected $settings;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->fs = new Filesystem();
        $this->setup();
    }

    public function settings($name = null)
    {
        return $name == null ? $this->settings : $this->settings[$name];
    }

    function setup()
    {
        $this->setTemplateEngine();
        $this->setOutputFormatterStyles();
    }

    private function setTemplateEngine()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/Templates');
        $this->renderer = new \Twig_Environment($loader);

        $var = new \Twig_Filter('var', function ($value) {
            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }
            elseif (is_string($value)) {
                return "'$value'";
            }
            elseif (is_array($value)) {
                return "['". implode("', '", $value) . "']";
            }
            return $value;
        });
        $this->renderer->addFilter($var);

        $test = new \Twig_Test('array', function ($value) {
            return is_array($value);
        });
        $this->renderer->addTest($test);
        $test = new \Twig_Test('post_type', function ($value) {
            return $value instanceof PostType;
        });
        $this->renderer->addTest($test);
    }

    private function setOutputFormatterStyles()
    {
        $style = new OutputFormatterStyle('blue', null, ['bold']);
        $this->output->getFormatter()->setStyle('title', $style);

        $style = new OutputFormatterStyle('green');
        $this->output->getFormatter()->setStyle('success', $style);

        $style = new OutputFormatterStyle('red');
        $this->output->getFormatter()->setStyle('error', $style);
    }

    public function install()
    {
        $this->output->writeln('<title>Installing...</title>');
        $this->fs->dir(self::SETTINGS_DIR);
        $this->fs->dir(self::TEMP_DIR);
        $this->fs->dir($this->wpDir);
        $this->fs->copyTo(__DIR__ . '/../bin/crust', '.');
        $this->renderFile('./crust-file.php');
    }

    public function installPackage($package)
    {
        $class = '\\Crust\\Installers\\' . $package . 'Installer';
        if (class_exists($class, true)) {
            $installer = new $class($this);
            $installer->install();
        }
        else {
            $this->output->writeln("<error>Installer for package $package was not found.</error>");
        }
    }

    public function renderFile($file, $data = [], $tpl = null)
    {
        if (!file_exists($file)) {
            $fileName = basename($file);
            $this->output->write("<title>→</title> $fileName");
            $tpl = $tpl ?? $fileName . '.twig';
            $render = $this->renderer->render($tpl, $data);
            if (file_put_contents($file, $render)) {
                $this->output->writeln(' <success>✓</success>');
            }
            else {
                $this->output->writeln(' <error>✗</error>');
            }
        }
    }

    public function progressBar()
    {
        $progressBar = new ProgressBar($this->output, 100);
        $progressBar->setFormat('[%bar%] %percent%% %message%');
        $progressBar->setBarCharacter('■');
        $progressBar->setEmptyBarCharacter('⁃');
        $progressBar->setProgressCharacter('■');
        $progressBar->setBarWidth(50);
        return $progressBar;
    }

    public function theme($name, $settings = [])
    {
        $this->theme = new Theme($this, $name, $settings);
        return $this->theme;
    }

    public function page($name, $settings = [])
    {
        return new Page($name, $settings);
    }

    public function postType($name, $settings = [])
    {
        return new PostType($name, $settings);
    }

    public function taxonomy($name, $settings = [])
    {
        return new Taxonomy($name, $settings);
    }

    public function meta($name, $settings = [])
    {
        return new Meta($name, $settings);
    }
}
