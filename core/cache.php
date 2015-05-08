<?php

/**
 * Class Cache
 */
class Cache {

    /**
     * @var Cache
     */
    protected static $instance;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $saveKey;

    /**
     * @var bool
     */
    protected $enable = true;

    /**
     * @var CacheItem[]
     */
    protected $cache = array();

    private function __construct($directory) {

        $this->directory = rtrim($directory, DIRECTORY_SEPARATOR);
        register_shutdown_function(array($this, 'write'));
    }

    public static function setup($directory) {

        static::$instance = new Cache($directory);
        return static::$instance;
    }

    /**
     * @return Cache
     */
    public static function getInstance() {

        return static::$instance;
    }

    public function get($key) {

        if (!$this->enable) {
            return false;
        }

        $this->saveKey = $key;
        $item = $this->getItem($key);

        if ($item) {
            return $item->getValue();
        }

        return false;
    }

    protected function getItem($key) {

        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $filename = $this->getFilename($key);
        $mtime = @filemtime($filename);

        if ($mtime != FALSE) {

            $node = @file_get_contents($filename);

            if ($node) {

                $item = CacheItem::decode($node);
                $this->cache[$key] = $item;

                if ($item->checkTime($mtime)) {

                    return $item;
                }
            }
        }

        return false;
    }

    public function set($data, $key = NULL, $lifetime = NULL) {

        if (!$this->enable) {
            return false;
        }

        if (!$key) {
            $key = $this->saveKey;
        }

        if (preg_match('|[a-z_A-z_-]^|ui', $key)) {
            throw new Exception('Format key [a-z_A-z_-]');
        }

        $item = $this->getItem($key);

        if (!$item) {

            $item = new CacheItem($key, $lifetime);
        }

        $this->cache[$key] = $item;
        return $item->setValue($data, $lifetime);
    }

    protected function getFilename($key) {

        return $this->directory . '/' . $key . '.ch';
    }

    public function test($key) {

        if (!$this->enable) {
            return false;
        }

        return file_exists($this->getFilename($key));
    }

    public function write() {

        foreach ($this->cache as $item) {

            if ($item->isModified()) {

                $node = $item->encode();
                file_put_contents($this->getFilename($item->getKey()), $node);
            }
        }
    }

    public function setEnable($enable) {
        $this->enable = $enable;
    }
}

class CacheItem {

    /**
     * @var string
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var bool
     */
    private $modified;

    /**
     * @var int
     */
    private $lifetime;

    /**
     * @param string $key
     * @param int $lifetime
     */
    public function __construct($key, $lifetime = 60) {

        $this->key = $key;
        $this->lifetime = $lifetime;
    }

    public function setValue($value, $lifetime = NULL) {

        if ($this->value != $value) {

            $this->value = $value;
            $this->modified = true;
            $this->lifetime = $lifetime;

            return TRUE;
        }

        return FALSE;
    }

    public function isModified() {
        return $this->modified;
    }

    public function getKey() {
        return $this->key;
    }

    public function getValue() {
        return $this->value;
    }

    public function checkTime($currentTime) {

        if (!$this->lifetime) {
            return true;
        }

        return (time() - $currentTime) <= $this->lifetime;
    }

    /**
     * @return string
     */
    public function encode() {
        $this->modified = false;
        return serialize($this);
    }

    /**
     * @param string $node
     * @return CacheItem
     */
    public static function decode($node) {
        return unserialize($node);
    }
}

function cacheEnable($enable) {
    $cache = Cache::getInstance();
    $cache->setEnable($enable);
}

function cacheGet($key) {
    $cache = Cache::getInstance();
    return $cache->get($key);
}

function cacheSet($data, $key = NULL, $lifetime = NULL) {
    $cache = Cache::getInstance();
    return $cache->set($data, $key, $lifetime);
}

function cacheTest($key) {
    $cache = Cache::getInstance();
    return $cache->test($key);
}