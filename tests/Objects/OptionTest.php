<?php

namespace Crust\Objects;

use Crust\Crust;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class OptionTest extends TestCase {

    public function createCrust()
    {
        $output = new ConsoleOutput();
        $input = new ArgvInput();
        return new Crust($input, $output);
    }

    public function testInstance()
    {
        $option = new Option('Custom Option', []);
        $this->assertInstanceOf(Option::class, $option);
        $this->assertEquals('Custom Option', $option->name);
        $this->assertEquals('custom_option', $option->id);
        $this->assertEquals('custom-option', $option->slug);
        $this->assertEquals('Custom Option', $option->settings('title'));
    }
}
