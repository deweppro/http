<?php

namespace Dewep\Http\Traits;

/**
 * Trait HttpTrait
 *
 * @package Dewep\Http
 */
trait Base
{

    /**
     * @param string $key
     *
     * @return string
     */
    protected static function normalize(string $key): string
    {
        $key = strtr(strtolower($key), '_', '-');
        if (stripos($key, 'http-') === 0) {
            $key = substr($key, 5);
        }

        return $key;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected static function original(string $key): string
    {
        if (stripos($key, 'HTTP_') === 0) {
            $key = substr($key, 5);
        }
        $key = str_replace(['_', '-'], ' ', $key);
        $key = ucwords(strtolower($key));

        return str_replace(' ', '-', $key);
    }
}
