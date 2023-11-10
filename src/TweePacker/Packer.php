<?php
namespace TweePacker;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Cache\Storage\TaggableInterface;
use TweePacker\Utils\Pack as UtilsPack;

class Packer
{
    const MAX_ITEM_SIZE = 300000;

    protected $storage = null;

    protected $maxItemSize = self::MAX_ITEM_SIZE;

    public function __construct(StorageInterface&TaggableInterface $storage, $maxItemSize = self::MAX_ITEM_SIZE)
    {
        $this->storage = $storage;
        $this->maxItemSize = $maxItemSize;
    }

    public function setItem($key, $value, array $tags = []) : void
    {
        $value = serialize($value);
        if (strlen($value) < $this->maxItemSize) {
            $this->storage->setItem($key, $value);
            $this->storage->setTags($key, $tags);
            return;
        }
        $pack = new UtilsPack();
        for ($i = 0; $i < strlen($value) / $this->maxItemSize; $i++) {
            $_key   = $key . '::' . $i;
            $_value = substr($value, $i * $this->maxItemSize, $this->maxItemSize);
            $pack->append($_key);
            $this->storage->setItem($_key, $_value);
            $this->storage->setTags($_key, $tags);
        }
        $this->storage->setItem($key, $pack);
        $this->storage->setTags($key, $tags);
    }

    public function getItem($key)
    {
        $raw = $this->storage->getItem($key);
        if ($raw instanceof UtilsPack) {
            $value = '';
            foreach ($raw as $_key) {
                $_value = $this->storage->getItem($_key);
                $value .= $_value;
            }
        } else {
            $value = $raw;
        }
        return unserialize($value);
    }

    public function clearByTags(array $tags)
    {
        $this->storage->clearByTags($tags, true);
    }
}