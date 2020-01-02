<?php

declare(strict_types=1);

namespace Dewep\Http\Formatters\Providers;

use Dewep\Exception\UndefinedFormatException;
use Dewep\Http\Interfaces\FormattersInterface;
use Dewep\Http\Stream;

final class HtmlFormat implements FormattersInterface
{
    public static function detect(string $contentType): bool
    {
        return (bool)stripos($contentType, '/html');
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
    public static function decode($data): \DOMDocument
    {
        if ($data instanceof \DOMDocument) {
            return $data;
        }

        if (!is_string($data)) {
            throw new UndefinedFormatException('Invalid HTML format');
        }

        try {
            $doc = new \DOMDocument('5', 'UTF-8');
            $doc->loadHTML($data);
        } catch (\Throwable $e) {
            throw new UndefinedFormatException(
                'Invalid HTML format: '.$e->getMessage()
            );
        }

        return $doc;
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
            throw new UndefinedFormatException('Invalid HTML format');
        }

        try {
            $doc = new \DOMDocument('5', 'UTF-8');
            $doc->loadXML(XmlFormat::encode($data));

            return (string)$doc->saveHTML();
        } catch (UndefinedFormatException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new UndefinedFormatException(
                'Invalid HTML format: '.$e->getMessage()
            );
        }
    }
}
