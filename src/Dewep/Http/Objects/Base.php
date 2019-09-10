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

    /** @var bool */
    protected $normalize = true;

    /**
     * Base constructor.
     *
     * @param bool $normalize
     */
    public function __construct(bool $normalize = true)
    {
        $this->normalize = $normalize;
        $this->object = new \ArrayObject(new \stdClass());
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value)
    {
        if ($this->normalize) {
            $key = self::normalize($key);
        }
        $this->object[$key] = $value;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        if ($this->normalize) {
            $key = self::normalize($key);
        }

        return $this->object->offsetGet($key) ?? $default;
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
        if ($this->normalize) {
            $key = self::normalize($key);
        }

        return $this->object->offsetExists($key);
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
            if ($this->normalize) {
                $key = self::original($key);
            }

            $orig[$key] = $value;
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
        if ($this->normalize) {
            $key = self::normalize($key);
        }
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
