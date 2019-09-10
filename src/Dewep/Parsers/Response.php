<?php

namespace Dewep\Parsers;

/**
 * Class Response
 *
 * @package Dewep\Parsers
 */
class Response
{

    /**
     * Типы контента
     */
    const TYPE_JSON = 'json';
    const TYPE_XML  = 'xml';
    const TYPE_HTML = 'html';
    const TYPE_TEXT = 'text';

    /**
     * HTTP заголовки
     */
    const HTTP_JSON = 'application/json; charset=UTF-8';
    const HTTP_XML  = 'application/xml; charset=UTF-8';
    const HTTP_HTML = 'text/html; charset=UTF-8';
    const HTTP_TEXT = 'text/plain; charset=UTF-8';
    //--
    const HTTP_JPG = 'image/jpg';
    const HTTP_PNG = 'image/png';
    const HTTP_GIF = 'image/gif';
    //--
    const HTTP_PDF   = 'application/pdf';
    const HTTP_ZIP   = 'application/zip';
    const HTTP_OTHER = 'application/octet-stream';

    /**
     * @param array $body
     *
     * @return string
     */
    public static function json(array $body): string
    {
        return (string)json_encode($body);
    }

    /**
     * @param array $body
     *
     * @return string
     */
    public static function html(array $body): string
    {
        $xml = self::xml($body, '<html/>');
        $doc = new \DOMDocument('5', 'UTF-8');
        $doc->loadXML($xml);

        return $doc->saveHTML();
    }

    /**
     * @param array  $body
     * @param string $root
     *
     * @return string
     */
    public static function xml(array $body, string $root = '<root/>'): string
    {
        $xml = new \SimpleXMLElement($root);
        array_walk_recursive($body, [$xml, 'addChild']);

        return (string)$xml->asXML();
    }

    /**
     * @param mixed $body
     *
     * @return mixed
     */
    public static function other($body)
    {
        return $body;
    }

}
