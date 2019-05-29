<?php

namespace Dewep\Http\Objects;

/**
 * Class Base
 *
 * @package Dewep\Objects
 */
interface BaseInterface
{
    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value);

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null);

    /**
     * @return array
     */
    public function keys(): array;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param array $data
     */
    public function replace(array $data);

    /**
     * @param string $key
     */
    public function remove(string $key);
}
