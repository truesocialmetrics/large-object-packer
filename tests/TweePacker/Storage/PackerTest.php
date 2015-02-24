<?php
namespace TweePacker\Storage;
use PHPUnit_Framework_TestCase;
use Zend\Cache\Storage\Adapter\Memory as AdapterMemory;

class PackerTest extends PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $storage = $this->getMock('Zend\Cache\Storage\StorageInterface');
        $service = new Packer($storage);
        $this->assertEquals($storage, $service->getStorage());
        $this->assertEquals(Packer::MAX_ITEM_SIZE, $service->getMaxItemSize());
    }

    public function testInitOverride()
    {
        $storage = $this->getMock('Zend\Cache\Storage\StorageInterface');
        $service = new Packer($storage, 1000);
        $this->assertEquals($storage, $service->getStorage());
        $this->assertEquals(1000, $service->getMaxItemSize());
    }

    public function testSmall()
    {
        $service = new Packer(new AdapterMemory());
        $service->setItem('xxx', 'abcdef');
        $this->assertEquals('abcdef', $service->getItem('xxx'));
        $this->assertCount(1, $service->getStorage()->getIterator());
    }

    public function testLarge()
    {
        $data = str_repeat('a', 10 * 3);
        $service = new Packer(new AdapterMemory(), 10);
        $service->setItem('xxx', $data);
        $this->assertEquals($data, $service->getItem('xxx'));
        $this->assertCount(5, $service->getStorage()->getIterator());
    }

    public function testEmpty()
    {
        $service = new Packer(new AdapterMemory());
        $this->assertEquals(false, $service->getItem('xxx'));
        $this->assertCount(0, $service->getStorage()->getIterator());
    }
}