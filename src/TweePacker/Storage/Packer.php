<?php
namespace TweePacker\Storage;
use Zend\Cache\Storage\StorageInterface;
use TweePacker\Utils\Pack as UtilsPack;

class Packer
{
    const MAX_ITEM_SIZE = 300000;

    protected $storage = null;

    protected $maxItemSize = self::MAX_ITEM_SIZE;

    public function __construct(StorageInterface $storage, $maxItemSize = self::MAX_ITEM_SIZE)
    {
        $this->setStorage($storage);
        $this->setMaxItemSize($maxItemSize);
    }

    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function getStorage()
    {
        return $this->storage;
    }

    public function setMaxItemSize($maxItemSize)
    {
        $this->maxItemSize = $maxItemSize;
    }

    public function getMaxItemSize()
    {
        return $this->maxItemSize;
    }

    public function setItem($key, $value)
    {
        $value = serialize($value);
        if (strlen($value) < $this->getMaxItemSize()) {
            return $this->getStorage()->setItem($key, $value);
        }
        $pack = new UtilsPack();
        for ($i = 0; $i < strlen($value) / $this->getMaxItemSize(); $i++) {
            $_key   = $key . '::' . $i;
            $_value = substr($value, $i * $this->getMaxItemSize(), $this->getMaxItemSize());
            $pack->append($_key);
            $this->getStorage()->setItem($_key, $_value);
        }
        return $this->getStorage()->setItem($key, $pack);
    }

    public function getItem($key)
    {
        $raw = $this->getStorage()->getItem($key);
        if ($raw instanceof UtilsPack) {
            $value = '';
            foreach ($raw as $_key) {
                $_value = $this->getStorage()->getItem($_key);
                $value .= $_value;
            }
        } else {
            $value = $raw;
        }
        return unserialize($value);
    }
}