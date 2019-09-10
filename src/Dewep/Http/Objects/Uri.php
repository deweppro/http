<?php

namespace Dewep\Http\Objects;

/**
 * Class Uri
 *
 * @package Dewep\Http
 */
class Uri
{

    /** @var array */
    protected static $schemePortDefault = [
        //-- общепринятые
        'ftp'              => 21,
        'http'             => 80,
        'rtmp'             => null,
        'rtsp'             => null,
        'https'            => 443,
        'gopher'           => null,
        'mailto'           => null,
        'news'             => null,
        'nntp'             => null,
        'irc'              => null,
        'smb'              => null,
        'prospero'         => null,
        'telnet'           => null,
        'wais'             => null,
        'xmpp'             => null,
        'file'             => null,
        'data'             => null,
        'tel'              => null,
        //-- экзотические
        'afs'              => null,
        'cid'              => null,
        'mid'              => null,
        'mailserver'       => null,
        'nfs'              => null,
        'tn3270'           => null,
        'z39.50'           => null,
        'skype'            => null,
        'smsto'            => null,
        'ed2k'             => null,
        'market'           => null,
        'steam'            => null,
        'bitcoin'          => null,
        'ob'               => null,
        'tg'               => null,
        //-- схемы в браузерах
        'view-source'      => null,
        'chrome'           => null,
        'chrome-extension' => null,
        'opera'            => null,
        'browser'          => null,
    ];
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
     * Uri constructor.
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
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->path = $path ?? '/';
        $this->query = $query;
        $this->fragment = $fragment;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return \Dewep\Http\Objects\Uri
     */
    public static function bootstrap(): Uri
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
     * @param string $scheme
     *
     * @return \Dewep\Http\Objects\Uri
     */
    public function setScheme(string $scheme): Uri
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * @param string $user
     * @param string $password
     *
     * @return \Dewep\Http\Objects\Uri
     */
    public function setUserInfo(string $user, string $password): Uri
    {
        $this->user = $user;
        $this->password = $password;

        return $this;
    }

    /**
     * @param string $host
     *
     * @return \Dewep\Http\Objects\Uri
     */
    public function setHost(string $host): Uri
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param int $port
     *
     * @return \Dewep\Http\Objects\Uri
     */
    public function setPort(int $port): Uri
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @param string $path
     *
     * @return \Dewep\Http\Objects\Uri
     */
    public function setPath(string $path): Uri
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param string $query
     *
     * @return \Dewep\Http\Objects\Uri
     */
    public function setQuery(string $query): Uri
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param string $fragment
     *
     * @return \Dewep\Http\Objects\Uri
     */
    public function setFragment(string $fragment): Uri
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
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

}
