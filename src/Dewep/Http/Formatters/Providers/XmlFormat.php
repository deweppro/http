<?php

declare(strict_types=1);

namespace Dewep\Http\Formatters\Providers;

use Dewep\Exception\UndefinedFormatException;
use Dewep\Http\Interfaces\FormattersInterface;
use Dewep\Http\Stream;

final class XmlFormat implements FormattersInterface
{
    public static function detect(string $contentType): bool
    {
        return (bool)stripos($contentType, '/xml');
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
     *
     * @throws \Dewep\Exception\UndefinedFormatException
     */
    public static function decode($data): \SimpleXMLElement
    {
        if ($data instanceof \SimpleXMLElement) {
            return $data;
        }

        if (!is_string($data)) {
            throw new UndefinedFormatException('Invalid XML format');
        }

        try {
            $backup        = libxml_disable_entity_loader(true);
            $backup_errors = libxml_use_internal_errors(true);
            $data          = simplexml_load_string($data);
            libxml_disable_entity_loader($backup);
            libxml_clear_errors();
            libxml_use_internal_errors($backup_errors);

            if ($data instanceof \SimpleXMLElement) {
                return $data;
            }
        } catch (\Throwable $e) {
            throw new UndefinedFormatException(
                'Invalid XML format'.$e->getMessage()
            );
        }

        return new \SimpleXMLElement('<root/>');
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
            throw new UndefinedFormatException('Invalid XML format');
        }

        try {
            $xml = new \SimpleXMLElement('<root/>');

            array_walk_recursive(
                $data,
                function ($value, $key) use ($xml) {
                    $xml->addChild((string)$key, (string)$value);
                }
            );

            return (string)$xml->asXML();
        } catch (\Throwable $e) {
            throw new UndefinedFormatException(
                'Invalid XML format: '.$e->getMessage()
            );
        }
    }
}
