<?php declare(strict_types=1);

namespace Dewep\Http;

/**
 * Class UrlBag
 *
 * @package Dewep\Http
 */
class UrlBag
{
    /** @var string */
    protected $scheme = '';

    /** @var string */
    protected $user = '';

    /** @var string */
    protected $password = '';

    /** @var string */
    protected $host = '';

    /** @var int|null */
    protected $port;

    /** @var string */
    protected $basePath = '';

    /** @var string */
    protected $path = '';

    /** @var string */
    protected $query = '';

    /** @var string */
    protected $fragment = '';

    /**
     * UrlBag constructor.
     *
     * @param string   $scheme
     * @param string   $host
     * @param int|null $port
     * @param string   $path
     * @param string   $query
     * @param string   $fragment
     * @param string   $user
     * @param string   $password
     */
    public function __construct(
        string $scheme,
        string $host,
        ?int $port,
        string $path,
        string $query,
        string $fragment,
        string $user,
        string $password
    ) {
        $this->setScheme($scheme);
        $this->setHost($host);
        $this->setPort($port);
        $this->setPath($path);
        $this->setQuery($query);
        $this->setFragment($fragment);
        $this->setUserInfo($user, $password);
    }

    /**
     * @return \Dewep\Http\UrlBag
     */
    public static function initialize(): self
    {
        $scheme = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http' : 'https';
        $user = $_SERVER['PHP_AUTH_USER'] ?? '';
        $pass = $_SERVER['PHP_AUTH_PW'] ?? '';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $port = $_SERVER['SERVER_PORT'] ?? null;
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $query = $_SERVER['QUERY_STRING'] ?? '';
        $fragment = '';

        return new static($scheme, $host, $port, $path, $query, $fragment, $user, $pass);
    }

    /**
     * @param string $url
     *
     * @return \Dewep\Http\UrlBag
     */
    public static function parse(string $url): self
    {
        $data = parse_url($url);
        $scheme = $data['scheme'] ?? '';
        $user = $data['user'] ?? '';
        $pass = $data['pass'] ?? '';
        $host = $data['host'] ?? '';
        $port = $data['port'] ?? null;
        $path = $data['path'] ?? '/';
        $query = $data['query'] ?? '';
        $fragment = $data['fragment'] ?? '';

        return new static($scheme, $host, $port, $path, $query, $fragment, $user, $pass);
    }

    /**
     * @param string $scheme
     *
     * @return \Dewep\Http\UrlBag
     */
    public function setScheme(string $scheme): self
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * @param string $user
     * @param string $password
     *
     * @return \Dewep\Http\UrlBag
     */
    public function setUserInfo(string $user, string $password): self
    {
        $this->user = $user;
        $this->password = $password;

        return $this;
    }

    /**
     * @param string $host
     *
     * @return \Dewep\Http\UrlBag
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param int|null $port
     *
     * @return \Dewep\Http\UrlBag
     */
    public function setPort(?int $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @param string $path
     *
     * @return \Dewep\Http\UrlBag
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param string $query
     *
     * @return \Dewep\Http\UrlBag
     */
    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param string $fragment
     *
     * @return \Dewep\Http\UrlBag
     */
    public function setFragment(string $fragment): self
    {
        $this->fragment = trim($fragment, '#');

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        return ($scheme ? $scheme.':' : '')
            .($authority ? '//'.$authority : '')
            .'/'.trim($path, '/')
            .($query ? '?'.$query : '')
            .($fragment ? '#'.$fragment : '');
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getAuthority(): string
    {
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $port = $this->getPort();

        return ($userInfo ? $userInfo.'@' : '').$host.($port !== null ? ':'.$port : '');
    }

    /**
     * @return string
     */
    public function getUserInfo(): string
    {
        return $this->user.($this->password ? ':'.$this->password : '');
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getQueryMap(): array
    {
        $array = [];

        parse_str($this->query, $array);

        return $array;
    }

    /**
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }
}
