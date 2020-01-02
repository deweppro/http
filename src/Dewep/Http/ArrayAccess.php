<?php

declare(strict_types=1);

namespace Dewep\Http;

use Dewep\Http\Interfaces\ArrayAccessInterface;

class ArrayAccess implements ArrayAccessInterface
{
    /** @var \ArrayObject */
    protected $object;

    /** @var bool */
    protected $canonize = true;

    public function __construct(bool $canonize = true)
    {
        $this->setCanonize($canonize);
        $this->setObject(new \ArrayObject([], \ArrayObject::STD_PROP_LIST));
    }

    /**
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        if ($this->isCanonize()) {
            $key = self::canonize($key);
        }
        $this->getObject()->offsetSet($key, $value);
    }

    /**
     * @param mixed $default
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

    public function keys(): array
    {
        $keys     = [];
        $iterator = $this->getObject()->getIterator();
        while ($iterator->valid()) {
            $keys[] = $iterator->key();
            $iterator->next();
        }

        return $keys;
    }

    public function has(string $key): bool
    {
        if ($this->isCanonize()) {
            $key = self::canonize($key);
        }

        return $this->getObject()->offsetExists($key);
    }

    public function all(): array
    {
        return $this->getObject()->getArrayCopy();
    }

    public function replace(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set((string)$key, $value);
        }
    }

    public function remove(string $key): void
    {
        if ($this->isCanonize()) {
            $key = self::canonize($key);
        }
        $this->getObject()->offsetUnset($key);
    }

    public function reset(): void
    {
        $iterator = $this->getObject()->getIterator();
        while ($iterator->valid()) {
            $this->getObject()->offsetUnset($iterator->key());
            $iterator->rewind();
        }
    }

    public static function canonize(string $key): string
    {
        if (0 === stripos($key, 'HTTP_')) {
            $key = substr($key, 5);
        }
        $key = str_replace(['_', '-'], ' ', $key);
        $key = ucwords(strtolower($key));

        return str_replace(' ', '-', $key);
    }

    protected function getObject(): \ArrayObject
    {
        return $this->object;
    }

    protected function setObject(\ArrayObject $object): void
    {
        $this->object = $object;
    }

    protected function isCanonize(): bool
    {
        return $this->canonize;
    }

    protected function setCanonize(bool $canonize): void
    {
        $this->canonize = $canonize;
    }
}
