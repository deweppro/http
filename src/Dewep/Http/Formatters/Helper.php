<?php

declare(strict_types=1);

namespace Dewep\Http\Formatters;

use Dewep\Exception\UndefinedFormatException;
use Dewep\Http\Formatters\Providers\DumpFormat;
use Dewep\Http\Formatters\Providers\FormFormat;
use Dewep\Http\Formatters\Providers\HtmlFormat;
use Dewep\Http\Formatters\Providers\JsonFormat;
use Dewep\Http\Formatters\Providers\XmlFormat;

final class Helper
{
    /** @var array */
    private static $formats = [
        FormFormat::class,
        JsonFormat::class,
        XmlFormat::class,
        HtmlFormat::class,
        DumpFormat::class,
    ];

    /**
     * @throws \Dewep\Exception\UndefinedFormatException
     *
     * @return array|\DOMDocument|\SimpleXMLElement
     */
    public static function fromGlobalData(string $contentType)
    {
        foreach (self::$formats as $format) {
            if (call_user_func([$format, 'detect'], $contentType)) {
                return call_user_func(
                    [$format, 'encode'],
                    call_user_func([$format, 'data'])
                );
            }
        }

        throw new UndefinedFormatException('Format not recognized');
    }

    /**
     * @param mixed $data
     *
     * @throws \Dewep\Exception\UndefinedFormatException
     */
    public static function encode(string $contentType, $data): string
    {
        foreach (self::$formats as $format) {
            if (call_user_func([$format, 'detect'], $contentType)) {
                return call_user_func([$format, 'encode'], $data);
            }
        }

        throw new UndefinedFormatException('Format not recognized');
    }

    /**
     * @param mixed $data
     *
     * @throws \Dewep\Exception\UndefinedFormatException
     *
     * @return array|\DOMDocument|\SimpleXMLElement
     */
    public static function decode(string $contentType, $data)
    {
        if (is_array($data)) {
            return $data;
        }

        foreach (self::$formats as $format) {
            if (call_user_func([$format, 'detect'], $contentType)) {
                return call_user_func([$format, 'decode'], $data);
            }
        }

        throw new UndefinedFormatException('Format not recognized');
    }
}
