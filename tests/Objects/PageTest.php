<?php

namespace Crust\Objects;

use Crust\Crust;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class PageTest extends TestCase {

    public function createCrust()
    {
        $output = new ConsoleOutput();
        $input = new ArgvInput();
        return new Crust($input, $output);
    }

    public function testInstance()
    {
        $page = new Page('Custom Page', []);
        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals('Custom Page', $page->name);
        $this->assertEquals('custom_page', $page->id);
        $this->assertEquals('custom-page', $page->slug);
    }
}