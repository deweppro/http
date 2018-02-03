<?php

namespace Dewep\Http;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
trait HttpTrait
{

    /**
     * @param string $key
     * @return string
     */
    protected function normalizeKey(string $key): string
    {
        $key = strtr(strtolower($key), '_', '-');
        if (stripos($key, 'http-') === 0) {
            $key = substr($key, 5);
        }

        return $key;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function originalKey(string $key): string
    {
        if (stripos($key, 'HTTP_') === 0) {
            $key = substr($key, 5);
        }
        $key = str_replace(['_', '-'], ' ', $key);
        $key = ucwords(strtolower($key));

        return str_replace(' ', '-', $key);
    }

}
