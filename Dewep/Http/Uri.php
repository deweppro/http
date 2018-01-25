<?php

namespace Dewep\Http;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Uri
{

    protected static $schemePortDefault = [
        //-- общепринятые
        'ftp' => 21,
        'http' => 80,
        'rtmp' => null,
        'rtsp' => null,
        'https' => 443,
        'gopher' => null,
        'mailto' => null,
        'news' => null,
        'nntp' => null,
        'irc' => null,
        'smb' => null,
        'prospero' => null,
        'telnet' => null,
        'wais' => null,
        'xmpp' => null,
        'file' => null,
        'data' => null,
        'tel' => null,
        //-- экзотические
        'afs' => null,
        'cid' => null,
        'mid' => null,
        'mailserver' => null,
        'nfs' => null,
        'tn3270' => null,
        'z39.50' => null,
        'skype' => null,
        'smsto' => null,
        'ed2k' => null,
        'market' => null,
        'steam' => null,
        'bitcoin' => null,
        'ob' => null,
        'tg' => null,
        //-- схемы в браузерах
        'view-source' => null,
        'chrome' => null,
        'chrome-extension' => null,
        'opera' => null,
        'browser' => null,
    ];
    /** @var string */
    protected $scheme = '';
    /** @var string */
    protected $user = '';
    /** @var string */
    protected $password = '';
    /** @var string */
    protected $host = '';
    /** @var int */
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
     * @param string $scheme
     * @param string $host
     * @param int|null $port
     * @param string|null $path
     * @param string $query
     * @param string $fragment
     * @param string $user
     * @param string $password
     */
    public function __construct(
        string $scheme,
        string $host,
        int $port = null,
        string $path = null,
        string $query = '',
        string $fragment = '',
        string $user = '',
        string $password = ''
    ) {
        $this->scheme   = $scheme;
        $this->host     = $host;
        $this->port     = $port;
        $this->path     = $path ?? '/';
        $this->query    = $query;
        $this->fragment = $fragment;
        $this->user     = $user;
        $this->password = $password;
    }

    /**
     * @return Uri
     */
    public static function bootstrap(): Uri
    {
        $scheme   = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http' : 'https';
        $user     = $_SERVER['PHP_AUTH_USER'] ?? '';
        $pass     = $_SERVER['PHP_AUTH_PW'] ?? '';
        $host     = $_SERVER['HTTP_HOST'] ?? null;
        $port     = $_SERVER['SERVER_PORT'] ?? null;
        $path     = $_SERVER['REQUEST_URI'] ?? '/';
        $query    = $_SERVER['QUERY_STRING'] ?? '';
        $fragment = '';

        return new static($scheme, $host, $port, $path, $query, $fragment, $user, $pass);
    }

    /**
     * @param string $scheme
     * @return Uri
     */
    public function withScheme(string $scheme): Uri
    {
        $clone         = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    /**
     * @param string $user
     * @param string|null $password
     * @return Uri
     */
    public function withUserInfo(string $user, string $password = null): Uri
    {
        $clone           = clone $this;
        $clone->user     = $user;
        $clone->password = $password ?? '';

        return $clone;
    }

    /**
     * @param $host
     * @return Uri
     */
    public function withHost(string $host): Uri
    {
        $clone       = clone $this;
        $clone->host = $host;

        return $clone;
    }

    /**
     * @param int|null $port
     * @return Uri
     */
    public function withPort(int $port = null): Uri
    {
        $clone       = clone $this;
        $clone->port = $port;

        return $clone;
    }

    /**
     * @param string $path
     * @return Uri
     */
    public function withPath(string $path): Uri
    {
        $clone       = clone $this;
        $clone->path = $path;

        return $clone;
    }

    /**
     * @param string $query
     * @return Uri
     */
    public function withQuery(string $query): Uri
    {
        $clone        = clone $this;
        $clone->query = $query;

        return $clone;
    }

    /**
     * @param string $fragment
     * @return Uri
     */
    public function withFragment(string $fragment): Uri
    {
        $clone           = clone $this;
        $clone->fragment = trim($fragment, '#');

        return $clone;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $scheme    = $this->getScheme();
        $authority = $this->getAuthority();
        $path      = $this->getPath();
        $query     = $this->getQuery();
        $fragment  = $this->getFragment();

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
        $host     = $this->getHost();
        $port     = $this->getPort();

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
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

}
