<?php

namespace Crust\Objects;

use Crust\Crust;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class ThemeTest extends TestCase {

    public function createCrust()
    {
        $output = new ConsoleOutput();
        $input = new ArgvInput();
        return new Crust($input, $output);
    }

    public function testInstance()
    {
        $crust = $this->createCrust();
        $theme = new Theme($crust, 'Test Theme', []);
        $this->assertInstanceOf(Theme::class, $theme);
        $this->assertEquals('Test Theme', $theme->name);
        $this->assertEquals('test_theme', $theme->id);
        $this->assertEquals('test-theme', $theme->slug);
    }

    public function testSettings()
    {
        $crust = $this->createCrust();
        $settings = [
            'languages' => ['fr', 'tr'],
            'foo' => 'bar'
        ];
        $theme = new Theme($crust, 'Test Theme', $settings);
        $this->assertEquals($settings['languages'], $theme->settings('languages'));
        $this->assertEquals($settings['foo'], $theme->settings('foo'));
        $this->assertEquals($settings['languages'], $theme->languages());
        $this->assertTrue($theme->isMultilingual());
    }

    public function testCreate()
    {
        $crust = $this->createCrust();
        $crust->wpDir = './wp';
        $theme = new Theme($crust, 'Test Theme', []);
        $postType = $crust->postType('Custom Type', []);
        $theme->addPostType($postType);
        $taxonomy = $crust->taxonomy('Custom Taxonomy', []);
        $theme->addTaxonomy($taxonomy);
        $result = $theme->create();
        $this->assertEquals($theme, $result);
        $this->assertTrue(file_exists('./wp/wp-content/themes/test-theme'));
        $this->assertTrue(file_exists('./wp/wp-content/themes/test-theme/inc'));
        $this->assertTrue(file_exists('./wp/wp-content/themes/test-theme/screenshot.png'));
        $this->assertTrue(file_exists($theme->dir() . '/functions.php'));
        $this->assertTrue(file_exists($theme->dir() . '/header.php'));
        $this->assertTrue(file_exists($theme->dir() . '/footer.php'));
        $this->assertTrue(file_exists($theme->dir() . '/single.php'));
        $this->assertTrue(file_exists($theme->dir() . '/archive.php'));
        $this->assertTrue(file_exists($theme->dir() . '/page.php'));
        $this->assertTrue(file_exists($theme->dir() . '/front-page.php'));
        $this->assertTrue(file_exists($theme->dir() . '/404.php'));
        $this->assertTrue(file_exists($theme->dir() . '/index.php'));
        $this->assertTrue(file_exists($theme->dir() . '/style.css'));
        $this->assertTrue(file_exists($theme->dir() . '/theme.js'));

        $this->assertTrue(file_exists($theme->dir() . '/inc/post-type-custom-type.php'));
        $this->assertTrue(file_exists($theme->dir() . '/single-custom-type.php'));
        $this->assertTrue(file_exists($theme->dir() . '/archive-custom-type.php'));

        $this->assertTrue(file_exists($theme->dir() . '/inc/taxonomy-custom-taxonomy.php'));
        $this->assertTrue(file_exists($theme->dir() . '/taxonomy-custom-taxonomy.php'));

        $this->assertTrue(file_exists($theme->dir() . '/languages/en_EN.po'));

        $crust->fs->remove($crust->wpDir);
    }

    public function testPostTypeMethods()
    {
        $crust = $this->createCrust();
        $theme = new Theme($crust, 'Test Theme', []);
        $postType = $crust->postType('Post Type', []);
        $result = $theme->addPostType($postType);
        $this->assertEquals($theme, $result);
        $this->assertTrue($theme->hasPostType('Post Type'));
        $this->assertEquals(1, count($theme->postTypes()));
    }

    public function testTaxonomyMethods()
    {
        $crust = $this->createCrust();
        $theme = new Theme($crust, 'Test Theme', []);
        $taxonomy = $crust->taxonomy('Custom Taxonomy', []);
        $result = $theme->addTaxonomy($taxonomy);
        $this->assertEquals($theme, $result);
        $this->assertTrue($theme->hasTaxonomy('Custom Taxonomy'));
        $this->assertEquals(1, count($theme->taxonomies()));
    }

    public function testOptionMethods()
    {
        $crust = $this->createCrust();
        $theme = new Theme($crust, 'Test Theme', []);
        $option = $crust->option('Custom Option', []);
        $result = $theme->addOption($option);
        $this->assertEquals($theme, $result);
        $this->assertEquals(1, count($theme->options()[0]));
        $optionGroup = new OptionGroup('Option Group');
        $theme->addOptionGroup($optionGroup);
        $this->assertEquals(2, count($theme->options()));
    }

    public function testTranslationTextMethods()
    {
        $crust = $this->createCrust();
        $theme = new Theme($crust, 'Test Theme', []);
        $count = count($theme->texts());
        $theme->addText('Test');
        $this->assertEquals('Test', $theme->texts()[$count]);

        $postType = $crust->postType('Post Type', []);
        $theme->addPostType($postType);
        $count = count($theme->texts()) - 1;
        $this->assertEquals('Uploaded to this Post Type', $theme->texts()[$count]);

        $taxonomy = $crust->taxonomy('Custom Taxonomy', []);
        $theme->addTaxonomy($taxonomy);
        $count = count($theme->texts()) - 1;
        $this->assertEquals('No Custom Taxonomies found.', $theme->texts()[$count]);
    }
}
