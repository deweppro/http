<?php

declare(strict_types=1);

namespace Dewep\Http\Interfaces;

interface FormattersInterface
{
    /**
     * @return mixed
     */
    public static function data();

    public static function detect(string $contentType): bool;

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public static function decode($data);

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public static function encode($data);
}
