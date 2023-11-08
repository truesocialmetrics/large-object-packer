<?php
namespace TweePacker\Storage\Adapter;

use PHPUnit\Framework\TestCase;
use TweePacker\Storage\Adapter\MongoDb;
use MongoDB\Client as SystemMongoDBClient;
use MongoDB\Collection as SystemMongoDbCollection;
use ReflectionProperty;

class MongoDbTest extends TestCase
{
    // ... Test methods ...
    public function testGetItemReturnsValueOnSuccess()
    {
        $mockCollection = $this->createMock(SystemMongoDbCollection::class);

        $property = new ReflectionProperty(MongoDb::class, 'collection');
        $property->setAccessible(true);

        $mongoDb = new MongoDb([]);

        $property->setValue($mongoDb, $mockCollection);


        $key = 'testKey';
        $expectedValue = 'testValue';

        // Mocking findOne to return a sample array
        $mockCollection->method('findOne')->willReturn(['value' => $expectedValue]);

        $value = $mongoDb->getItem($key);
        $this->assertEquals($expectedValue, $value);
    }


    public function testSetItemUpdatesValue()
    {
        $mockCollection = $this->createMock(SystemMongoDbCollection::class);

        $property = new ReflectionProperty(MongoDb::class, 'collection');
        $property->setAccessible(true);

        $mongoDb = new MongoDb([]);

        $property->setValue($mongoDb, $mockCollection);

        $key = 'testKey';
        $value = 'newValue';

        // Expecting 'updateOne' to be called with specific parameters
        $mockCollection->expects($this->once())->method('updateOne')->with(
            $this->equalTo(['_id' => $key]),
            $this->anything(), // You might want to be more specific here
            $this->equalTo(['upsert' => true, 'w' => 1])
        );

        $mongoDb->setItem($key, $value);
    }

    public function testClearByTags()
    {
        $mockCollection = $this->createMock(SystemMongoDbCollection::class);

        $property = new ReflectionProperty(MongoDb::class, 'collection');
        $property->setAccessible(true);

        $mongoDb = new MongoDb([]);

        $property->setValue($mongoDb, $mockCollection);

        $tags = ['tag1', 'tag2'];

        $mockCollection->expects($this->once())
                             ->method('deleteMany')
                             ->with($this->equalTo(['tags' => ['$in' => $tags]]));

        // Invoke clearByTags
        $mongoDb->clearByTags($tags);
    }

    public function testFlush()
    {
        $mockCollection = $this->createMock(SystemMongoDbCollection::class);

        $property = new ReflectionProperty(MongoDb::class, 'collection');
        $property->setAccessible(true);

        $mongoDb = new MongoDb([]);

        $property->setValue($mongoDb, $mockCollection);

        $tags = ['tag1', 'tag2'];

        $mockCollection->expects($this->once())
                             ->method('deleteMany')
                             ->with($this->equalTo([]));

        // Invoke clearByTags
        $mongoDb->flush();
    }
}
