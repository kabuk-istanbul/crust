<?php

namespace Crust\Objects;

use Crust\Crust;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class MetaTest extends TestCase {

    public function createCrust()
    {
        $output = new ConsoleOutput();
        $input = new ArgvInput();
        return new Crust($input, $output);
    }

    public function testInstance()
    {
        $meta = new Meta('Custom Meta', []);
        $this->assertInstanceOf(Meta::class, $meta);
        $this->assertEquals('Custom Meta', $meta->name);
        $this->assertEquals('custom_meta', $meta->id);
        $this->assertEquals('custom-meta', $meta->slug);
    }
}