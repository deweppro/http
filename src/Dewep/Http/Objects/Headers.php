<?php

namespace Dewep\Http\Objects;

use Dewep\Http\HeaderType;

/**
 * Class Headers
 *
 * @package Dewep\Http
 */
class Headers extends Base
{
    /** @var \Dewep\Http\Objects\Base */
    public $server;

    /** @var \Dewep\Http\Objects\Base */
    public $cookie;

    /**
     * Headers constructor.
     *
     * @param array $headers
     * @param array $cookies
     * @param array $server
     */
    public function __construct(array $headers, array $cookies, array $server)
    {
        parent::__construct();

        $this->server = new Base();
        $this->cookie = new Base();

        $this->server->replace(array_diff_key($server, $headers));
        $this->cookie->replace($cookies);

        $this->replace($headers);
    }

    /**
     * @return \Dewep\Http\Objects\Headers
     */
    public static function bootstrap(): Headers
    {
        $headers = array_filter(
            $_SERVER,
            function ($k) {
                return substr($k, 0, 5) == 'HTTP_';
            },
            ARRAY_FILTER_USE_KEY
        );

        return new static($headers, $_COOKIE, $_SERVER);
    }

    /**
     * @return string
     */
    public function getHttpMethod(): string
    {
        return $this->get(HeaderType::REQUEST_METHOD, '');
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType()[0] ?? '';
    }

    /**
     * @return array
     */
    private function contentType(): array
    {
        $ct = $this->get(HeaderType::CONTENT_TYPE, '');
        $contentTypeArray = explode(';', $ct, 2);
        $contentTypeArray = array_map('trim', $contentTypeArray);

        return $contentTypeArray;
    }

    /**
     * @return array
     */
    public function getContentTypeParams(): array
    {
        $params = $this->contentType()[1] ?? '';

        return explode('=', $params, 2);
    }

    /**
     * @param string $key
     * @param string $value
     * @param int    $expire
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     */
    public function setCookies(
        string $key,
        string $value,
        int $expire = 3600,
        string $path = '/',
        string $domain = '*',
        bool $secure = false,
        bool $httponly = false
    ) {
        $this->cookie->set($key, $value);
        setcookie(self::normalize($key), $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     *
     */
    public function clearCookies()
    {
        foreach ($this->cookie->keys() as $key) {
            $this->cookie->remove($key);
            setcookie($key, '', time() - 1);
        }
    }

    /**
     * @param string $key
     */
    public function removeCookie(string $key)
    {
        $this->cookie->remove($key);
        setcookie($key, '', time() - 1);
    }

}
