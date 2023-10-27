<?php
namespace TweePacker;
use PHPUnit\Framework\TestCase;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Cache\Storage\TaggableInterface;
use Laminas\Cache\Storage\Adapter\Memory as AdapterMemory;

class PackerTest extends TestCase
{
    public function testInit()
    {
        $service = new Packer(new AdapterMemory());
        $this->assertInstanceOf(Packer::class, $service);
    }

    public function testSmall()
    {
        $storage = new AdapterMemory();
        $service = new Packer($storage);
        $service->setItem('xxx', 'abcdef');
        $this->assertEquals('abcdef', $service->getItem('xxx'));
        $this->assertCount(1, $storage->getIterator());
    }

    public function testLarge()
    {
        $data = str_repeat('a', 10 * 3);
        $storage = new AdapterMemory();
        $service = new Packer($storage, 10);
        $service->setItem('xxx', $data);
        $this->assertEquals($data, $service->getItem('xxx'));
        $this->assertCount(5, $storage->getIterator());
    }

    public function testEmpty()
    {
        $storage = new AdapterMemory();
        $service = new Packer($storage);
        $this->assertEquals(false, $service->getItem('xxx'));
        $this->assertCount(0, $storage->getIterator());
    }

    public function testSmallTags()
    {
        $storage = new AdapterMemory();
        $service = new Packer($storage);
        $service->setItem('xxx', 'abcdef', ['yyy']);
        $service->setItem('xx0', 'abcdef', ['aaa', 'bb']);
        $service->setItem('xx1', 'abcde1', ['aaa']);
        $service->setItem('xx2', 'abcde2', ['bb']);

        $this->assertCount(4, $storage->getIterator());
        $service->clearByTags(['bb']);
        $this->assertCount(2, $storage->getIterator());
        $service->clearByTags(['aaa']);
        $this->assertCount(1, $storage->getIterator());
        $this->assertEquals('abcdef', $service->getItem('xxx'));
    }


    public function testLargeTags()
    {
        $data = str_repeat('a', 10 * 3);
        $storage = new AdapterMemory();
        $service = new Packer($storage, 10);
        $service->setItem('xxx', 'abcdef' . $data, ['yyy']);
        $service->setItem('xx0', 'abcde0' . $data, ['aaa', 'bb']);
        $service->setItem('xx1', 'abcde1' . $data, ['aaa']);
        $service->setItem('xx2', 'abcde2' . $data, ['bb']);

        $this->assertCount(24, $storage->getIterator());
        $service->clearByTags(['bb']);
        $this->assertCount(12, $storage->getIterator());
        $service->clearByTags(['aaa']);
        $this->assertCount(6, $storage->getIterator());
        $this->assertEquals('abcdef' . $data, $service->getItem('xxx'));
    }
}
