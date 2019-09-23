<?php declare(strict_types=1);

namespace Dewep\Http;

use Dewep\Http\Interfaces\ArrayAccessInterface;

/**
 * Class ArrayAccess
 *
 * @package Dewep\Http
 */
class ArrayAccess implements ArrayAccessInterface
{
    /** @var \ArrayObject */
    protected $object;

    /** @var bool */
    protected $canonize = true;

    /**
     * ArrayAccess constructor.
     *
     * @param bool $canonize
     */
    public function __construct(bool $canonize = true)
    {
        $this->setCanonize($canonize);
        $this->setObject(new \ArrayObject([], \ArrayObject::STD_PROP_LIST));
    }

    /**
     * @return \ArrayObject
     */
    private function getObject(): \ArrayObject
    {
        return $this->object;
    }

    /**
     * @param \ArrayObject $object
     */
    private function setObject(\ArrayObject $object): void
    {
        $this->object = $object;
    }

    /**
     * @return bool
     */
    private function isCanonize(): bool
    {
        return $this->canonize;
    }

    /**
     * @param bool $canonize
     */
    private function setCanonize(bool $canonize): void
    {
        $this->canonize = $canonize;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value)
    {
        if ($this->isCanonize()) {
            $key = self::canonize($key);
        }
        $this->getObject()->offsetSet($key, $value);
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if ($this->isCanonize()) {
            $key = self::canonize($key);
        }

        if ($this->getObject()->offsetExists($key)) {
            return $this->getObject()->offsetGet($key);
        }

        return $default;
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        $keys = [];
        $iterator = $this->getObject()->getIterator();
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
        if ($this->isCanonize()) {
            $key = self::canonize($key);
        }

        return $this->getObject()->offsetExists($key);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->getObject()->getArrayCopy();
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
     * @param string $key
     */
    public function remove(string $key)
    {
        if ($this->isCanonize()) {
            $key = self::canonize($key);
        }
        $this->getObject()->offsetUnset($key);
    }

    public function reset()
    {
        $iterator = $this->getObject()->getIterator();
        while ($iterator->valid()) {
            $this->getObject()->offsetUnset($iterator->key());
            $iterator->rewind();
        }
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public static function canonize(string $key): string
    {
        if (stripos($key, 'HTTP_') === 0) {
            $key = substr($key, 5);
        }
        $key = str_replace(['_', '-'], ' ', $key);
        $key = ucwords(strtolower($key));

        return str_replace(' ', '-', $key);
    }

}
