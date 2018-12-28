<?php
namespace TweePacker\Utils;
use PHPUnit\Framework\TestCase;

class PackTest extends TestCase
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
