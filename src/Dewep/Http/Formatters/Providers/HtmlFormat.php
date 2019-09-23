<?php declare(strict_types=1);

namespace Dewep\Http\Formatters\Providers;

use Dewep\Exception\UndefinedFormatException;
use Dewep\Http\Interfaces\FormattersInterface;
use Dewep\Http\Stream;

/**
 * Class HtmlFormat
 *
 * @package Dewep\Http\Formatters\Providers
 */
class HtmlFormat implements FormattersInterface
{
    /**
     * @param string $contentType
     *
     * @return bool
     */
    public static function detect(string $contentType): bool
    {
        return (bool)stripos($contentType, '/html');
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
     * @return \DOMDocument
     * @throws \Dewep\Exception\UndefinedFormatException
     */
    public static function decode($data): \DOMDocument
    {
        if ($data instanceof \DOMDocument) {
            return $data;
        }

        if (is_string($data)) {
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
