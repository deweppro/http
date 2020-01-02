<?php

declare(strict_types=1);

namespace Dewep\Http;

final class UrlBag
{
    /** @var string */
    private $scheme = '';

    /** @var string */
    private $user = '';

    /** @var string */
    private $password = '';

    /** @var string */
    private $host = '';

    /** @var int */
    private $port = 0;

    /** @var string */
    private $path = '';

    /** @var string */
    private $query = '';

    /** @var string */
    private $fragment = '';

    public function __construct(
        string $scheme,
        string $host,
        int $port,
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

    public static function initialize(): self
    {
        $scheme = (empty($_SERVER['HTTPS']) || 'off' == $_SERVER['HTTPS']) ? 'http' : 'https';
        $user = (string)($_SERVER['PHP_AUTH_USER'] ?? '');
        $pass = (string)($_SERVER['PHP_AUTH_PW'] ?? '');
        $host = (string)($_SERVER['HTTP_HOST'] ?? '');
        $port = (int)($_SERVER['SERVER_PORT'] ?? 0);
        $path = (string)($_SERVER['REQUEST_URI'] ?? '/');
        $query = (string)($_SERVER['QUERY_STRING'] ?? '');
        $fragment = '';

        return new self(
            $scheme,
            $host,
            $port,
            $path,
            $query,
            $fragment,
            $user,
            $pass
        );
    }

    public static function parse(string $url): self
    {
        $data = (array)parse_url($url);
        $scheme = (string)($data['scheme'] ?? '');
        $user = (string)($data['user'] ?? '');
        $pass = (string)($data['pass'] ?? '');
        $host = (string)($data['host'] ?? '');
        $port = (int)($data['port'] ?? null);
        $path = (string)($data['path'] ?? '/');
        $query = (string)($data['query'] ?? '');
        $fragment = (string)($data['fragment'] ?? '');

        return new self(
            $scheme,
            $host,
            $port,
            $path,
            $query,
            $fragment,
            $user,
            $pass
        );
    }

    public function setScheme(string $scheme): self
    {
        $this->scheme = $scheme;

        return $this;
    }

    public function setUserInfo(string $user, string $password): self
    {
        $this->user = $user;
        $this->password = $password;

        return $this;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function setFragment(string $fragment): self
    {
        $this->fragment = trim($fragment, '#');

        return $this;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $port = $this->getPort();

        return ($userInfo ? $userInfo.'@' : '').$host.(0 < $port ? ':'.$port : '');
    }

    public function getUserInfo(): string
    {
        return $this->user.($this->password ? ':'.$this->password : '');
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getQueryMap(): array
    {
        $array = [];

        parse_str($this->query, $array);

        return $array;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }
}
