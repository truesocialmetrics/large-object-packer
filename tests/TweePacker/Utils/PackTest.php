<?php
namespace TweePacker\Utils;
use PHPUnit_Framework_TestCase;

class PackTest extends PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $service = new Pack();
        $service->append('a');
        $service->append('b');
        $service->append('c');
        $this->assertEquals(array('a', 'b', 'c'), $service->toArray());
    }
}