<?php

namespace Dewep\Http;

use Dewep\Exception\HttpException;
use Dewep\Http\Objects\Headers;
use Dewep\Http\Traits\BaseTrait;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

/**
 * Class Route
 *
 * @package Dewep\Http
 */
class Route
{
    use BaseTrait;

    /** @var array */
    public $routes = [];
    /** @var Headers */
    protected $headers;
    /** @var array */
    protected $result = [];

    /**
     * @param array   $routes
     * @param Headers $headers
     */
    public function __construct(array $routes, Headers $headers)
    {
        $this->routes = $routes;
        $this->headers = $headers;
    }

    /**
     *
     * @param string $path
     * @param string $methods
     * @param string $class
     */
    public function set(string $path, string $methods, string $class)
    {
        $this->routes[$path][$methods] = $class;
    }

    /**
     * @return Route
     * @throws \Exception
     */
    public function bind(): Route
    {
        $routes = $this->routes;

        $dispatcher = \FastRoute\simpleDispatcher(
            function (RouteCollector $r) use ($routes) {
                foreach ($routes as $uri => $route) {
                    foreach ($route as $method => $handler) {
                        $method = explode(',', $method);
                        $r->addRoute($method, $this->fixUri($uri), $handler);
                    }
                }
            }
        );

        $httpMethod = (string)$this->headers->server->get(
            HeaderType::REQUEST_METHOD,
            'GET'
        );

        $uri = (string)$this->headers->server->get(HeaderType::REQUEST_URI, '/');
        list($baseurl,) = explode('?', strtolower($uri), 2);
        $routeInfo = $dispatcher->dispatch(strtoupper($httpMethod), $this->fixUri($baseurl));

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new HttpException('Method not found', 404);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new HttpException('Method not allowed', 405);
                break;
        }

        $this->result = $routeInfo;

        if (!empty($this->result[2])) {
            foreach ($this->result[2] as $k => $v) {
                $this->setAttribute($k, $v);
            }
        }

        return $this;
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    private function fixUri(string $uri)
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
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->result[2][self::normalize($name)] ?? $default;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setAttribute(string $name, $value)
    {
        $this->result[2][self::normalize($name)] = $value;
    }

    /**
     * @param string $name
     */
    public function removeAttribute(string $name)
    {
        unset($this->result[2][self::normalize($name)]);
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
     * @throws \Exception
     */
    public function getHandler()
    {
        if (empty($this->result[1])) {
            throw new \Exception('Handler is not found', 500);
        }

        return $this->result[1];
    }

}
