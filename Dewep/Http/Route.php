<?php

namespace Dewep\Http;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

/**
 * Fast-Route
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Route
{

    use HttpTrait;

    /** @var array */
    protected $routes;
    /** @var Headers */
    protected $headers;
    /** @var array */
    protected $result;

    /**
     * @param array $routes
     * @param Headers $headers
     */
    public function __construct(array $routes, Headers $headers)
    {
        $this->routes  = $routes ?? [];
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
                        $r->addRoute($method, $uri, $handler);
                    }
                }
            }
        );

        $httpMethod = $this->headers->getServerParam(
            HeaderType::REQUEST_METHOD,
            'GET'
        );

        $uri       = $this->headers->getServerParam(HeaderType::REQUEST_URI, '/');
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new \Exception('Method not found', 404);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new \Exception('Method not allowed', 405);
                break;
        }

        $this->result = $routeInfo;

        if (!empty($this->result[2])) {
            $this->result[2] = array_map(
                [$this, 'normalizeKey'],
                $this->result[2]
            );
        }

        return $this;
    }

    /**
     * @param string $name
     * @param null $default
     * @return array
     */
    public function getAttribute(string $name, $default = null): array
    {
        $name = $this->normalizeKey($name);

        return $this->result[2][$name] ?? $default;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function setAttribute(string $name, $value)
    {
        $this->result[2][$this->normalizeKey($name)] = $value;
    }

    /**
     * @param string $name
     */
    public function removeAttribute(string $name)
    {
        unset($this->result[2][$this->normalizeKey($name)]);
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
