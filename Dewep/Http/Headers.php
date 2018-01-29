<?php

namespace Dewep\Http;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Headers
{
    use HttpTrait;

    /** @var array */
    protected $headers = [];
    /** @var array */
    protected $serverParams = [];
    /** @var array */
    protected $cookies = [];

    /**
     * @param array $server
     * @param array $cookies
     */
    public function __construct(array $server, array $cookies)
    {
        $headers = array_filter(
            $server,
            function ($k) {
                return substr($k, 0, 5) == 'HTTP_';
            },
            ARRAY_FILTER_USE_KEY
        );

        $serverParams = array_diff_key($server, $headers);
        foreach ($serverParams as $key => $value) {
            $this->serverParams[$this->normalizeKey($key)] = $value;
        }

        foreach ($cookies as $key => $value) {
            $this->cookies[$this->normalizeKey($key)] = $value;
        }


        foreach ($headers as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param string $key
     * @param $value
     */
    public function set(string $key, array $value)
    {
        $this->headers[$this->normalizeKey($key)] = $value;
    }

    /**
     * @return Headers
     */
    public static function bootstrap(): Headers
    {
        return new static($_SERVER, $_COOKIE);
    }

    /**
     * @return array
     */
    public function all()
    {
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[$this->originalKey($key)] = $value;
        }

        return $headers;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $key = $this->normalizeKey($key);

        return isset($this->headers[$key]);
    }

    /**
     * @param string $key
     * @param array $value
     */
    public function add(string $key, array $value)
    {
        $key        = $this->normalizeKey($key);
        $valueExist = $this->get($key, []);

        $this->set($key, array_merge($valueExist, $value));
    }

    /**
     * @param string $key
     * @param array $default
     * @return array
     */
    public function get(string $key, array $default = []): array
    {
        $key = $this->normalizeKey($key);

        return $this->headers[$key] ?? $default;
    }

    /**
     * @param string $key
     */
    public function remove(string $key)
    {
        $key = $this->normalizeKey($key);
        unset($this->headers[$key]);
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
        $contentType      = $this->headers['content-type'][0] ?? '';
        $contentTypeArray = explode(';', strtolower($contentType), 2);
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
     * @return array
     */
    public function getServerParams(): array
    {
        $serverParams = [];
        foreach ($this->serverParams as $key => $value) {
            $serverParams[$this->originalKey($key)] = $value;
        }

        return $serverParams;
    }

    /**
     * @param string $key
     * @param string|null $default
     * @return mixed|string
     */
    public function getServerParam(string $key, string $default = null)
    {
        $key = $this->normalizeKey($key);

        return $this->serverParams[$key] ?? $default;
    }

    /**
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * @param string $key
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
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
        $key                 = $this->normalizeKey($key);
        $this->cookies[$key] = $value;
        setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * @param string $key
     * @param string|null $default
     * @return mixed|string
     */
    public function getCookie(string $key, string $default = null)
    {
        return $this->cookies[$key] ?? $default;
    }

    /**
     * @param string $key
     */
    public function removeCookies(string $key)
    {
        $key = $this->normalizeKey($key);
        unset($this->cookies[$key]);
        setcookie($key, '', time() - 1);
    }

}
