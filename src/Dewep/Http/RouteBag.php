<?php declare(strict_types=1);

namespace Dewep\Http;

use Dewep\Exception\HttpException;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

/**
 * Class RouteBag
 *
 * @package Dewep\Http
 */
class RouteBag
{
    /** @var array */
    protected $routes = [];

    /** @var \Dewep\Http\HeaderBag */
    protected $headers;

    /** @var \Dewep\Http\ServerBag */
    protected $server;

    /** @var array */
    protected $result = [];

    /**
     * RouteBag constructor.
     *
     * @param \Dewep\Http\HeaderBag $headers
     * @param \Dewep\Http\ServerBag $server
     */
    public function __construct(HeaderBag $headers, ServerBag $server)
    {
        $this->headers = $headers;
        $this->server = $server;
    }

    /**
     * @param string $path
     * @param string $methods
     * @param string $class
     *
     * @return \Dewep\Http\RouteBag
     */
    public function set(string $path, string $methods, string $class): self
    {
        $this->routes[$path][$methods] = $class;

        return $this;
    }

    /**
     * @param array $routes
     *
     * @return \Dewep\Http\RouteBag
     */
    public function replace(array $routes): self
    {
        $this->routes = $routes;

        return $this;
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    protected static function fixUri(string $uri): string
    {
        return sprintf(
            '/%s',
            str_replace(
                ['***'],
                ['{_:.*}'],
                trim($uri, '?/')
            )
        );
    }

    /**
     * @return \Dewep\Http\RouteBag
     * @throws \Dewep\Exception\HttpException
     */
    public function bind(): self
    {
        $routes = $this->routes;

        $dispatcher = \FastRoute\simpleDispatcher(
            function (RouteCollector $r) use ($routes) {
                foreach ($routes as $uri => $route) {
                    foreach ($route as $method => $handler) {
                        $method = explode(',', $method);
                        $r->addRoute($method, self::fixUri($uri), $handler);
                    }
                }
            }
        );

        $httpMethod = strtoupper((string)$this->server->getRequestMethod());

        $url = UrlBag::parse(
            (string)$this->server->get(
                HeaderTypeBag::REQUEST_URI,
                '/'
            )
        );

        $info = $dispatcher->dispatch($httpMethod, self::fixUri($url->getPath()));

        switch ($info[0]) {
            case Dispatcher::NOT_FOUND:
                throw new HttpException('Method not found', 404);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new HttpException('Method not allowed', 405);
                break;
        }

        $this->result = $info;

        if (!empty($this->result[2])) {
            foreach ($this->result[2] as $k => $v) {
                $this->setAttribute($k, $v);
            }
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->result[2][ArrayAccess::canonize($name)] ?? $default;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return \Dewep\Http\RouteBag
     */
    public function setAttribute(string $name, $value): self
    {
        $this->result[2][ArrayAccess::canonize($name)] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return \Dewep\Http\RouteBag
     */
    public function removeAttribute(string $name): self
    {
        unset($this->result[2][ArrayAccess::canonize($name)]);

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->result[2] ?? [];
    }

    /**
     * @return mixed
     * @throws \Dewep\Exception\HttpException
     */
    public function getHandler()
    {
        if (empty($this->result[1])) {
            throw new HttpException('Handler is not found', 500);
        }

        return $this->result[1];
    }

}
