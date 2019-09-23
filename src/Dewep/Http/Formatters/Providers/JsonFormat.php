<?php declare(strict_types=1);

namespace Dewep\Http\Formatters\Providers;

use Dewep\Exception\UndefinedFormatException;
use Dewep\Http\Interfaces\FormattersInterface;
use Dewep\Http\Stream;

/**
 * Class JsonFormat
 *
 * @package Dewep\Http\Formatters\Providers
 */
class JsonFormat implements FormattersInterface
{
    /**
     * @param string $contentType
     *
     * @return bool
     */
    public static function detect(string $contentType): bool
    {
        return (bool)stripos($contentType, '/json');
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
     * @return array
     * @throws \Dewep\Exception\UndefinedFormatException
     */
    public static function decode($data): array
    {
        if (is_array($data)) {
            return $data;
        }

        if (!is_string($data)) {
            throw new UndefinedFormatException('Invalid JSON format');
        }

        try {
            $result = json_decode((string)$data, true);
        } catch (\Throwable $e) {
            throw new UndefinedFormatException(
                'Invalid JSON format: '.$e->getMessage()
            );
        }

        if (!is_array($result)) {
            throw new UndefinedFormatException('Invalid JSON format');
        }

        return $result;
    }

    /**
     * @param mixed $data
     *
     * @return string
     * @throws \Dewep\Exception\UndefinedFormatException
     */
    public static function encode($data): string
    {
        if ($data === null) {
            return '';
        }

        if (is_scalar($data)) {
            return (string)$data;
        }

        if (!is_array($data)) {
            throw new UndefinedFormatException('Invalid JSON format');
        }

        try {
            return (string)json_encode($data);
        } catch (\Throwable $e) {
            throw new UndefinedFormatException(
                'Invalid JSON format: '.$e->getMessage()
            );
        }
    }

}
