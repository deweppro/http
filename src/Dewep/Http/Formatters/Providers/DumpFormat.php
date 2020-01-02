<?php

declare(strict_types=1);

namespace Dewep\Http\Formatters\Providers;

use Dewep\Http\Interfaces\FormattersInterface;
use Dewep\Http\Stream;

final class DumpFormat implements FormattersInterface
{
    public static function detect(string $contentType): bool
    {
        return true;
    }

    /**
     * @throws \Dewep\Exception\StreamException
     *
     * @return string
     */
    public static function data()
    {
        return Stream::initialize()->getContents();
    }

    /**
     * @param mixed $data
     */
    public static function decode($data): string
    {
        if (!is_string($data)) {
            return var_export($data, true);
        }

        return $data;
    }

    /**
     * @param mixed $data
     */
    public static function encode($data): string
    {
        if (null === $data) {
            return '';
        }

        if (is_scalar($data)) {
            return (string)$data;
        }

        return var_export($data, true);
    }
}
