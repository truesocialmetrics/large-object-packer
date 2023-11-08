<?php
namespace TweePacker\Storage\Adapter;

use Laminas\Cache\Storage\StorageInterface;
use Laminas\Cache\Storage\TaggableInterface;
use Laminas\Cache\Storage\FlushableInterface;
use Laminas\Cache\Exception\UnsupportedMethodCallException;
use Laminas\Cache\Storage\Capabilities;
use stdClass;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Client as SystemMongoDBClient;

class MongoDb implements StorageInterface, TaggableInterface
{
	private const FIELD_KEY = '_id';
	private const FIELD_VALUE = 'value';
	private const FIELD_TAGS = 'tags';
	private const FIELD_DATESTAMP = 'created_at';

	private $options = [];

	private $collection = null;

	public function __construct(array $options)
	{
		$this->options = $options;
	}

	private function getCollection()
	{
		if ($this->collection) {
			return $this->collection;
		}
		$client = new SystemMongoDBClient($this->options);
		$db = $client->selectDatabase($this->options['database']);
		$this->collection = $client->selectDatabase($this->options['collection']);

		return $this->collection;
	}

    /**
     * Set options.
     *
     * @param array|Traversable|Adapter\AdapterOptions $options
     * @return StorageInterface Fluent interface
     */
    public function setOptions($options)
    {
    	$this->options = $options;
    }

    /**
     * Get options
     *
     * @return Adapter\AdapterOptions
     */
    public function getOptions()
    {
    	return $this->options;
    }

    /* reading */

    /**
     * Get an item.
     *
     * @param  string  $key
     * @param  bool $success
     * @param  mixed   $casToken
     * @return mixed Data on success, null on failure
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function getItem($key, & $success = null, & $casToken = null)
    {
    	$item = $this->getCollection()->findOne([self::FIELD_KEY, $key]);
    	if (!$item) {
    		return null;
    	}

    	return $item[self::FIELD_VALUE];
    }

    /**
     * Get multiple items.
     *
     * @param  array $keys
     * @return array Associative array of keys and values
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function getItems(array $keys)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Test if an item exists.
     *
     * @param  string $key
     * @return bool
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function hasItem($key)
    {
    	$item = $this->getCollection()->findOne([self::FIELD_KEY, $key]);
    	if (!$item) {
    		return false;
    	}

    	return true;
    }

    /**
     * Test multiple items.
     *
     * @param  array $keys
     * @return array Array of found keys
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function hasItems(array $keys)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Get metadata of an item.
     *
     * @param  string $key
     * @return array|bool Metadata on success, false on failure
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function getMetadata($key)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Get multiple metadata
     *
     * @param  array $keys
     * @return array Associative array of keys and metadata
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function getMetadatas(array $keys)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /* writing */

    /**
     * Store an item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function setItem($key, $value)
    {
    	$this->getCollection()->updateOne([self::FIELD_KEY => $key],
    		['$set' => [
    			self::FIELD_VALUE => $value,
    			self::FIELD_DATESTAMP => new UTCDateTime(),
    		]],
    		['upsert' => true, 'w' => 1]);
    }

    /**
     * Store multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function setItems(array $keyValuePairs)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Add an item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function addItem($key, $value)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Add multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function addItems(array $keyValuePairs)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Replace an existing item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function replaceItem($key, $value)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Replace multiple existing items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function replaceItems(array $keyValuePairs)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Set an item only if token matches
     *
     * It uses the token received from getItem() to check if the item has
     * changed before overwriting it.
     *
     * @param  mixed  $token
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     * @see    getItem()
     * @see    setItem()
     */
    public function checkAndSetItem($token, $key, $value)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Reset lifetime of an item
     *
     * @param  string $key
     * @return bool
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function touchItem($key)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Reset lifetime of multiple items.
     *
     * @param  array $keys
     * @return array Array of not updated keys
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function touchItems(array $keys)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Remove an item.
     *
     * @param  string $key
     * @return bool
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function removeItem($key)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Remove multiple items.
     *
     * @param  array $keys
     * @return array Array of not removed keys
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function removeItems(array $keys)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Increment an item.
     *
     * @param  string $key
     * @param  int    $value
     * @return int|bool The new value on success, false on failure
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function incrementItem($key, $value)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Increment multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Associative array of keys and new values
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function incrementItems(array $keyValuePairs)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Decrement an item.
     *
     * @param  string $key
     * @param  int    $value
     * @return int|bool The new value on success, false on failure
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function decrementItem($key, $value)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /**
     * Decrement multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Associative array of keys and new values
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function decrementItems(array $keyValuePairs)
    {
    	throw new UnsupportedMethodCallException(__METHOD__ . ' is not suported');
    }

    /* status */

    /**
     * Capabilities of this storage
     *
     * @return Capabilities
     */
    public function getCapabilities()
    {
		return $this->capabilities  = new Capabilities(
            $this,
            new stdClass(),
            [
                'supportedDatatypes' => [
                    'NULL'     => true,
                    'boolean'  => true,
                    'integer'  => true,
                    'double'   => true,
                    'string'   => true,
                    'array'    => true,
                    'object'   => false,
                    'resource' => false,
                ],
                'supportedMetadata'  => [
                    '_id',
                ],
                'minTtl'             => 1,
                'staticTtl'          => true,
                'maxKeyLength'       => 255,
                'namespaceIsPrefix'  => true,
            ]
        );
    }

    /**
     * Set tags to an item by given key.
     * An empty array will remove all tags.
     *
     * @param string   $key
     * @param string[] $tags
     * @return bool
     */
    public function setTags($key, array $tags)
    {
    	$this->getCollection()->updateOne([self::FIELD_KEY => $key],
    		['$set' => [
    			self::FIELD_TAGS => $value,
    		]],
    		['upsert' => true, 'w' => 1]);
    }

    /**
     * Get tags of an item by given key
     *
     * @param string $key
     * @return string[]|FALSE
     */
    public function getTags($key)
    {
    	$item = $this->getCollection()->findOne([self::FIELD_KEY, $key]);
    	if (!$item) {
    		return false;
    	}

    	return $item[self::FIELD_TAGS];
    }

    /**
     * Remove items matching given tags.
     *
     * If $disjunction only one of the given tags must match
     * else all given tags must match.
     *
     * @param string[] $tags
     * @param  bool  $disjunction
     * @return bool
     */
    public function clearByTags(array $tags, $disjunction = false)
    {
    	// tags: { $in: ['red', 'blank'] }
    	$this->getCollection()->deleteMany(['tags' => ['$in' => $tags]]);
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        $this->getCollection()->deleteMany([]);
    }
}