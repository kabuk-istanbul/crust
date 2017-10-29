<?php

namespace Crust\Objects;

use Crust\Crust;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class PostTypeTest extends TestCase {

    public function createCrust()
    {
        $output = new ConsoleOutput();
        $input = new ArgvInput();
        return new Crust($input, $output);
    }

    public function testInstance()
    {
        $postType = new PostType('Post Type', []);
        $this->assertInstanceOf(PostType::class, $postType);
        $this->assertEquals('Post Type', $postType->name);
        $this->assertEquals('post_type', $postType->id);
        $this->assertEquals('post-type', $postType->slug);
    }

    public function testTaxonomyMethods()
    {
        $postType = new PostType('Post Type', []);
        $taxonomy = new Taxonomy('Custom Taxonomy', []);
        $result = $postType->addTaxonomy($taxonomy);
        $this->assertEquals($postType, $result);
        $this->assertEquals(1, count($postType->taxonomies()));
    }

    public function testMetaMethods()
    {
        $postType = new PostType('Post Type', []);
        $meta = new Meta('Custom Meta', ['has_column' => true]);
        $result = $postType->addMeta($meta);
        $this->assertEquals($postType, $result);
        $this->assertEquals(1, count($postType->metas()));
        $this->assertTrue($postType->hasMeta());
        $this->assertTrue($postType->hasMetaInColumns());
    }
}