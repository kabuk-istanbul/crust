<?php

namespace Crust\Objects;

use Crust\Crust;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class TaxonomyTest extends TestCase {

    public function createCrust()
    {
        $output = new ConsoleOutput();
        $input = new ArgvInput();
        return new Crust($input, $output);
    }

    public function testInstance()
    {
        $taxonomy = new Taxonomy('Custom Taxonomy', []);
        $this->assertInstanceOf(Taxonomy::class, $taxonomy);
        $this->assertEquals('Custom Taxonomy', $taxonomy->name);
        $this->assertEquals('custom_taxonomy', $taxonomy->id);
        $this->assertEquals('custom-taxonomy', $taxonomy->slug);
    }

    public function testPostTypeMethods()
    {
        $taxonomy = new Taxonomy('Custom Taxonomy', []);
        $postType = new PostType('Post Type', []);
        $result = $taxonomy->addPostType($postType);
        $this->assertEquals($taxonomy, $result);
        $this->assertEquals(1, count($taxonomy->postTypes()));
    }
}