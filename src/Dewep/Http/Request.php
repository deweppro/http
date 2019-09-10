<?php

namespace Dewep\Http;

use Dewep\Http\Objects\Base;
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

    /** @var \Dewep\Http\Objects\Uri */
    public $url;

    /** @var \Dewep\Http\Objects\Base */
    public $query;

    /** @var \Dewep\Http\Objects\Route */
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
    protected $parsers;

    /** @var mixed */
    protected $bodyParsed;

    /**
     * Request constructor.
     *
     * @param \Dewep\Http\Objects\Uri            $url
     * @param \Dewep\Http\Objects\Route          $route
     * @param \Dewep\Http\Objects\Headers        $headers
     * @param \Dewep\Http\Objects\Stream         $body
     * @param \Dewep\Http\Objects\UploadedFile[] $uploadedFiles
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

        $this->setDefaultParsersBody();

        $query = [];
        parse_str($this->url->getQuery(), $query);

        $this->query = new Base();
        $this->query->replace($query);
    }

    /**
     *
     */
    private function setDefaultParsersBody()
    {
        $this->parsers[BodyParser::JSON] = BodyParser::class.'::json';
        $this->parsers[BodyParser::XML_APP] = BodyParser::class.'::xml';
        $this->parsers[BodyParser::XML_TEXT] = BodyParser::class.'::xml';
        $this->parsers[BodyParser::FORM_DATA] = BodyParser::class.'::url';
        $this->parsers[BodyParser::FORM_WWW] = BodyParser::class.'::other';
        $this->parsers['*'] = BodyParser::class.'::other';
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
     * @return \Dewep\Http\Objects\Stream|null
     */
    public function getRaw(): ?Stream
    {
        return $this->body;
    }

    /**
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        if (!is_array($this->all())) {
            return $default;
        }

        return $this->bodyParsed[$key] ?? $default;
    }

    /**
     * @return mixed|null
     */
    public function all()
    {
        if ($this->bodyParsed === null) {
            $contentType = $this->headers->getContentType();

            if ($contentType == BodyParser::FORM_WWW) {
                $this->bodyParsed = $_POST;
            } else {
                $handler = $this->parsers[$contentType] ?? $this->parsers['*'];

                if (is_callable($handler)) {
                    $this->bodyParsed = call_user_func(
                        $handler,
                        (string)$this->body
                    );
                }
            }
        }

        return $this->bodyParsed;
    }


    /**
     * @param string   $type
     * @param callable $function
     */
    public function setBodyParser(string $type, callable $function)
    {
        $this->parsers[$type] = $function;
    }
}
