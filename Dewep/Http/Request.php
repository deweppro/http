<?php

namespace Dewep\Http;

use Dewep\Parsers\Request as BodyParser;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Request extends Message
{

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

    /** @var Uri */
    public $url;
    /** @var Route */
    public $route;
    /** @var UploadedFile */
    public $uploadedFiles;
    /** @var mixed */
    protected $bodyParsers;
    /** @var bool */
    protected $bodyParsed = false;

    /**
     * @param array $routes
     * @return Request
     * @throws \Exception
     */
    public static function bootstrap(array $routes): Request
    {
        $url           = Uri::bootstrap();
        $headers       = Headers::bootstrap();
        $route         = (new Route($routes, $headers))->bind();
        $body          = Stream::bootstrap();
        $uploadedFiles = UploadedFile::bootstrap();

        return new static($url, $route, $headers, $body, $uploadedFiles);
    }

    /**
     * @param Uri $url
     * @param Route $route
     * @param Headers $headers
     * @param Stream $body
     * @param UploadedFile[] $uploadedFiles
     */
    public function __construct(
        Uri $url,
        Route $route,
        Headers $headers,
        Stream $body,
        array $uploadedFiles
    ) {
        $this->url           = $url;
        $this->route         = $route;
        $this->headers       = $headers;
        $this->body          = $body;
        $this->uploadedFiles = &$uploadedFiles;
        //--
        $this->setDefaultParsersBody();
    }

    /**
     * @return array
     */
    public function getServerParams(): array
    {
        return $this->headers->getServerParams();
    }

    /**
     * @param string $key
     * @param string|null $default
     * @return string
     */
    public function getServerParam(string $key, string $default = null): string
    {
        return $this->headers->getServerParam($key, $default);
    }

    /**
     * @return array
     */
    public function getCookieParams(): array
    {
        return $this->headers->getCookies();
    }

    /**
     * @param array $cookies
     * @return Request
     */
    public function withCookieParams(array $cookies): Request
    {
        $clone = clone $this;
        foreach ($cookies as $key => $value) {
            $clone->headers->setCookies($key, $value);
        }

        return $clone;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return BodyParser::url($this->url->getQuery());
    }

    /**
     * @param array $query
     * @return Request
     */
    public function withQueryParams(array $query): Request
    {
        $clone = clone $this;
        $clone->url->withQuery(http_build_query($query));

        return $clone;
    }

    /**
     * @return array
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * @param array $uploadedFiles
     * @return Request
     */
    public function withUploadedFiles(array $uploadedFiles): Request
    {
        $clone                = clone $this;
        $clone->uploadedFiles = $uploadedFiles;

        return $clone;
    }

    /**
     * @return bool|mixed
     */
    public function getParsedBody()
    {
        if ($this->bodyParsed === false) {
            $contentType = $this->headers->getContentType();
            $handler     = $this->bodyParsers[$contentType] ?? $this->bodyParsers['*'];

            if ($contentType == BodyParser::FORM_WWW) {
                $this->bodyParsed = $_POST;
            } else {
                if (is_string($handler)) {
                    $this->bodyParsed = call_user_func(
                        $handler,
                        (string)$this->body
                    );
                } else {
                    $this->bodyParsed = $handler((string)$this->body);
                }
            }
        }

        return $this->bodyParsed;
    }

    /**
     * @param string $type
     * @param $function
     */
    public function setParserBody(string $type, $function)
    {
        $this->bodyParsers[$type] = $function;
    }

    private function setDefaultParsersBody()
    {
        $this->bodyParsers[BodyParser::JSON]      = '\Dewep\Parsers\Request::json';
        $this->bodyParsers[BodyParser::XML_APP]   = '\Dewep\Parsers\Request::xml';
        $this->bodyParsers[BodyParser::XML_TEXT]  = '\Dewep\Parsers\Request::xml';
        $this->bodyParsers[BodyParser::FORM_DATA] = '\Dewep\Parsers\Request::url';
        $this->bodyParsers[BodyParser::FORM_WWW]  = '\Dewep\Parsers\Request::other';
        $this->bodyParsers['*']                   = '\Dewep\Parsers\Request::other';
    }

    /**
     * @param $data
     * @return Request
     * @throws \Exception
     */
    public function withParsedBody($data): Request
    {
        if (
            !is_array($data) &&
            !is_object($data) &&
            !is_null($data)
        ) {
            throw new \Exception('Passed to an unsupported argument');
        }
        $clone             = clone $this;
        $clone->bodyParsed = $data;

        return $clone;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->route->getAttributes();
    }

    /**
     * @param string $name
     * @param null $default
     * @return array
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->route->getAttribute($name, $default);
    }

    /**
     * @param $name
     * @param $value
     * @return Request
     */
    public function withAttribute(string $name, $value): Request
    {
        $clone = clone $this;
        $clone->route->setAttribute($name, $value);

        return $clone;
    }

    /**
     * @param string $name
     * @return Request
     */
    public function withoutAttribute(string $name): Request
    {
        $clone = clone $this;
        $clone->route->removeAttribute($name);

        return $clone;
    }

    /**
     * @return string
     */
    public function getRequestTarget(): string
    {
        $path  = $this->url->getPath();
        $path  = '/'.trim($path, '/');
        $query = $this->url->getQuery();
        if (!empty($query)) {
            $path .= '?'.$query;
        }

        return $path;
    }

    /**
     * @param string $requestTarget
     * @return Request
     */
    public function withRequestTarget(string $requestTarget): Request
    {
        $requestTarget = strtr($requestTarget, ' ', '');
        @list($path, $query) = explode('?', $requestTarget, 2);

        $clone = clone $this;

        $clone->url->withPath($path);
        if (!empty($query)) {
            $clone->url->withQuery($query);
        }

        return $clone;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->headers->get(HeaderType::REQUEST_METHOD)[0] ?? '';
    }

    /**
     * @param string $method
     * @return Request
     * @throws \Exception
     */
    public function withMethod(string $method): Request
    {
        $method = strtoupper($method);
        if (!in_array($method, $this->validMethods)) {
            throw new \Exception('Sent is not the standard method.');
        }
        $clone = clone $this;
        $clone->headers->set(HeaderType::REQUEST_METHOD, [$method]);

        return $clone;
    }

    /**
     * @return Uri
     */
    public function getUri(): Uri
    {
        return $this->url;
    }

    /**
     * @param Uri $uri
     * @param bool $preserveHost
     * @return Request
     */
    public function withUri(Uri $uri, bool $preserveHost = false): Request
    {
        $clone      = clone $this;
        $clone->uri = $uri;

        if (!$preserveHost) {
            if (!empty($uri->getHost())) {
                $clone->headers->set(HeaderType::HOST, [$uri->getHost()]);
            }
        } else {
            if (
                !empty($uri->getHost()) &&
                (!$this->hasHeader(HeaderType::HOST) || empty($this->getHeaderLine(HeaderType::HOST)))
            ) {
                $clone->headers->set(HeaderType::HOST, [$uri->getHost()]);
            }
        }

        return $clone;
    }

}
