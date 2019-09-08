<?php

namespace Dewep\Http\Objects;

use Dewep\Http\Traits\BaseTrait;

/**
 * Class Base
 *
 * @package Dewep\Objects
 */
class Base implements \JsonSerializable, BaseInterface
{
    use BaseTrait;

    /** @var \ArrayObject */
    protected $object;

    /**
     * Base constructor.
     */
    public function __construct()
    {
        $this->object = new \ArrayObject(new \stdClass());
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value)
    {
        $this->object[self::normalize($key)] = $value;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        $key = self::normalize($key);
        if (!$this->object->offsetExists($key)) {
            return $default;
        }

        return $this->object->offsetGet($key);
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        $keys = [];
        $iterator = $this->object->getIterator();
        while ($iterator->valid()) {
            $keys[] = $iterator->key();
            $iterator->next();
        }

        return $keys;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->object->offsetExists(self::normalize($key));
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->object->getArrayCopy();
    }

    /**
     * @return array
     */
    public function allOrig(): array
    {
        $orig = [];
        foreach ($this->all() as $key => $value) {
            $orig[self::original($key)] = $value;
        }

        return $orig;
    }

    /**
     * @param array $data
     */
    public function replace(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set((string)$key, $value);
        }
    }

    /**
     *
     */
    public function clear()
    {
        $iterator = $this->object->getIterator();
        while ($iterator->valid()) {
            $this->object->offsetUnset($iterator->key());
            $iterator->rewind();
        }
    }

    /**
     * @param string $key
     */
    public function remove(string $key)
    {
        $this->object->offsetUnset(self::normalize($key));
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->allOrig();
    }
}
