<?php

namespace Crust\Tests;

use Crust\Crust;
use Crust\Helpers\Filesystem;
use Crust\Objects\Meta;
use Crust\Objects\Page;
use Crust\Objects\PostType;
use Crust\Objects\Taxonomy;
use Crust\Objects\Theme;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class CrustTest extends TestCase
{
    public function createCrust()
    {
        $output = new ConsoleOutput();
        $input = new ArgvInput();
        return new Crust($input, $output);
    }

    public function testInstance()
    {
        $output = new ConsoleOutput();
        $input = new ArgvInput();
        $crust = new Crust($input, $output);
        $this->assertInstanceOf(Crust::class, $crust);
        $this->assertInstanceOf(Filesystem::class, $crust->fs);
    }

    public function testCreate()
    {
        $output = new ConsoleOutput();
        $input = new ArgvInput();
        $crust = new Crust($input, $output);
        $crust->wpDir = './wp';
        $crust->install();
        $this->assertTrue(file_exists($crust->wpDir));
        $this->assertTrue(file_exists(Crust::SETTINGS_DIR));
        $this->assertTrue(file_exists(Crust::TEMP_DIR));
        $this->assertTrue(file_exists('./crust-file.php'));
        $crust->fs->remove($crust->wpDir);
        $crust->fs->remove(Crust::SETTINGS_DIR);
        $crust->fs->remove('./crust-file.php');
    }

    public function testRenderFile()
    {
        $output = new ConsoleOutput();
        $input = new ArgvInput();
        $crust = new Crust($input, $output);

        $tplFile = __DIR__ . '/../src/Templates/test.php.twig';
        $crust->fs->file($tplFile);
        file_put_contents($tplFile, '<?php {{testVar}}');
        $crust->renderFile('./test.php', ['testVar' => '$testVar;']);
        $this->assertTrue(file_exists('./test.php'));
        $this->assertEquals('<?php $testVar;', file_get_contents('./test.php'));
        $crust->fs->remove('./test.php');
        $crust->fs->remove($tplFile);
    }

    public function tesInstallPackage()
    {
        $output = new ConsoleOutput();
        $input = new ArgvInput();
        $crust = new Crust($input, $output);
        $crust->wpDir = './wp';

        $crust->install();
        $crust->installPackage('WordPress');

        $this->assertTrue(file_exists(Crust::TEMP_DIR . '/latest.zip'));
        $this->assertTrue(file_exists($crust->wpDir));
        $this->assertTrue(file_exists($crust->wpDir . '/index.php'));

        $crust->fs->remove(Crust::TEMP_DIR . '/latest.zip');
        $crust->fs->remove(Crust::TEMP_DIR . '/wordpress');
        $crust->fs->remove($crust->wpDir);
    }

    public function testTheme()
    {
        $crust = $this->createCrust();
        $theme = $crust->theme('Test Theme', []);
        $this->assertInstanceOf(Theme::class, $theme);
        $this->assertEquals('test_theme', $theme->id);
    }

    public function testPage()
    {
        $crust = $this->createCrust();
        $page = $crust->page('Custom Page', []);
        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals('custom_page', $page->id);
    }

    public function testPostType()
    {
        $crust = $this->createCrust();
        $postType = $crust->postType('Post Type', []);
        $this->assertInstanceOf(PostType::class, $postType);
        $this->assertEquals('post_type', $postType->id);
    }

    public function testTaxonomy()
    {
        $crust = $this->createCrust();
        $taxonomy = $crust->taxonomy('Custom Taxonomy', []);
        $this->assertInstanceOf(Taxonomy::class, $taxonomy);
        $this->assertEquals('custom_taxonomy', $taxonomy->id);
    }

    public function testMeta()
    {
        $crust = $this->createCrust();
        $meta = $crust->meta('Custom Meta', []);
        $this->assertInstanceOf(Meta::class, $meta);
        $this->assertEquals('custom_meta', $meta->id);
    }

    public function testProgressBar()
    {
        $crust = $this->createCrust();
        $this->assertInstanceOf(ProgressBar::class, $crust->progressBar());
    }
}
