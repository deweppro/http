<?php

namespace Dewep\Http;

use Dewep\Http\Objects\Headers;
use Dewep\Http\Objects\Route;
use Dewep\Http\Objects\Stream;
use Dewep\Http\Objects\UploadedFile;
use Dewep\Http\Objects\Uri;
use Dewep\Http\Traits\MessageTrait;
use Dewep\Parsers\Request as BodyParser;

/**
 * Class Request
 *
 * @package Dewep\Http
 */
class Request
{
    use MessageTrait;

    /** @var Uri */
    public $url;

    /** @var Route */
    public $route;

    /** @var array */
    public $uploadedFiles;

    /** @var array */
    protected $validMethods = [
        'CONNECT',
        'DELETE',
        'GET',
        'HEAD',
        'OPTIONS',
        'PATCH',
        'POST',
        'PUT',
        'TRACE',
    ];
    /** @var mixed */
    protected $bodyParsers;

    /** @var mixed */
    protected $bodyParsed;

    /**
     * @param Uri            $url
     * @param Route          $route
     * @param Headers        $headers
     * @param Stream         $body
     * @param UploadedFile[] $uploadedFiles
     */
    public function __construct(
        Uri $url,
        Route $route,
        Headers $headers,
        Stream $body,
        array &$uploadedFiles
    ) {
        $this->url = $url;
        $this->route = $route;
        $this->headers = $headers;
        $this->body = $body;
        $this->uploadedFiles = &$uploadedFiles;
        //--
        $this->setDefaultParsersBody();
    }

    /**
     *
     */
    private function setDefaultParsersBody()
    {
        $this->bodyParsers[BodyParser::JSON] = BodyParser::class.'::json';
        $this->bodyParsers[BodyParser::XML_APP] = BodyParser::class.'::xml';
        $this->bodyParsers[BodyParser::XML_TEXT] = BodyParser::class.'::xml';
        $this->bodyParsers[BodyParser::FORM_DATA] = BodyParser::class.'::url';
        $this->bodyParsers[BodyParser::FORM_WWW] = BodyParser::class.'::other';
        $this->bodyParsers['*'] = BodyParser::class.'::other';
    }

    /**
     * @param array $routes
     *
     * @return Request
     * @throws \Exception
     */
    public static function bootstrap(array $routes): Request
    {
        $url = Uri::bootstrap();
        $headers = Headers::bootstrap();
        $route = (new Route($routes, $headers))->bind();
        $body = Stream::bootstrap();
        $uploadedFiles = UploadedFile::bootstrap();

        return new static($url, $route, $headers, $body, $uploadedFiles);
    }

    /**
     * @param bool $resource
     *
     * @return Stream|mixed|null
     */
    public function getBody(bool $resource = false)
    {
        if ($resource) {
            return $this->body;
        }

        if ($this->bodyParsed === null) {
            $contentType = $this->headers->getContentType();

            if ($contentType == BodyParser::FORM_WWW) {
                $this->bodyParsed = $_POST;
            } else {
                $handler = $this->bodyParsers[$contentType] ?? $this->bodyParsers['*'];

                if (is_callable($handler)) {
                    $this->bodyParsed = call_user_func(
                        $handler,
                        (string)$this->body
                    );
                } elseif (is_callable($handler)) {
                    $this->bodyParsed = $handler((string)$this->body);
                }
            }
        }

        return $this->bodyParsed;
    }

    /**
     * @param string   $type
     * @param callable $function
     */
    public function setBodyParser(string $type, $function)
    {
        $this->bodyParsers[$type] = $function;
    }
}
