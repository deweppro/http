<?php declare(strict_types=1);

namespace Dewep\Http\Formatters\Providers;

use Dewep\Http\Interfaces\FormattersInterface;
use Dewep\Http\Stream;

/**
 * Class DumpFormat
 *
 * @package Dewep\Http\Formatters\Providers
 */
class DumpFormat implements FormattersInterface
{
    /**
     * @param string $contentType
     *
     * @return bool
     */
    public static function detect(string $contentType): bool
    {
        return true;
    }

    /**
     * @return string
     * @throws \Dewep\Exception\StreamException
     */
    public static function data()
    {
        return Stream::initialize()->getContents();
    }

    /**
     * @param mixed $data
     *
     * @return string
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
     *
     * @return string
     */
    public static function encode($data): string
    {
        if ($data === null) {
            return '';
        }

        if (is_scalar($data)) {
            return (string)$data;
        }

        return var_export($data, true);
    }

}
