<?php

declare(strict_types=1);

namespace Dewep\Http\Formatters\Providers;

use Dewep\Exception\UndefinedFormatException;
use Dewep\Http\Interfaces\FormattersInterface;

final class FormFormat implements FormattersInterface
{
    public static function detect(string $contentType): bool
    {
        return in_array(
            $contentType,
            [
                'application/x-www-form-urlencoded',
                'multipart/form-data',
            ]
        );
    }

    /**
     * @return array
     */
    public static function data()
    {
        return $_POST;
    }

    /**
     * @param mixed $data
     *
     * @throws \Dewep\Exception\UndefinedFormatException
     */
    public static function decode($data): array
    {
        if (!is_array($data)) {
            throw new UndefinedFormatException('Invalid FORM format');
        }

        return $data;
    }

    /**
     * @param mixed $data
     *
     * @throws \Dewep\Exception\UndefinedFormatException
     */
    public static function encode($data): string
    {
        if (null === $data) {
            return '';
        }

        if (is_scalar($data)) {
            return (string)$data;
        }

        if (!is_array($data)) {
            throw new UndefinedFormatException('Invalid FORM format');
        }

        try {
            return (string)http_build_query($data);
        } catch (\Throwable $e) {
            throw new UndefinedFormatException(
                'Invalid FORM format: '.$e->getMessage()
            );
        }
    }
}
