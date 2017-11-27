<?php

namespace Crust\Objects;

use Crust\Crust;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class OptionGroupTest extends TestCase {

    public function createCrust()
    {
        $output = new ConsoleOutput();
        $input = new ArgvInput();
        return new Crust($input, $output);
    }

    public function testInstance()
    {
        $optionGroup = new OptionGroup('Option Group', 'Option Group Title');
        $this->assertInstanceOf(OptionGroup::class, $optionGroup);
        $this->assertEquals('Option Group Title', $optionGroup->title);
        $option = new Option('Custom Option', []);
        $result = $optionGroup->addOption($option);
        $this->assertInstanceOf(OptionGroup::class, $result);
        $this->assertEquals(1, count($optionGroup->options()));
        $this->assertEquals($option, $optionGroup->options()[0]);
    }
}
