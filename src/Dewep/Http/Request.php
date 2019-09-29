<?php declare(strict_types=1);

namespace Dewep\Http;

use Dewep\Http\Formatters\Helper;

/**
 * Class Request
 *
 * @package Dewep\Http
 */
class Request extends ArrayAccess
{
    /** @var \SimpleXMLElement|\DOMDocument|null */
    protected $raw;

    /** @var \Dewep\Http\ArrayAccess */
    protected $query;

    /** @var \Dewep\Http\ServerBag */
    protected $server;

    /** @var \Dewep\Http\FileBag[] */
    protected $files = [];

    /** @var \Dewep\Http\CookieBag */
    protected $cookie;

    /** @var \Dewep\Http\SessionBag */
    protected $session;

    /** @var \Dewep\Http\HeaderBag */
    protected $header;

    /** @var \Dewep\Http\RouteBag */
    protected $route;

    /**
     * @return \Dewep\Http\Request
     * @throws \Dewep\Exception\UndefinedFormatException
     */
    public static function initialize(): self
    {
        $self = new static();

        $self->setHeader(HeaderBag::initialize());
        $self->setServer(ServerBag::initialize());
        $self->setFiles(FileBag::initialize());
        $self->setCookie(CookieBag::initialize());

        $self->setQuery(new ArrayAccess(true));
        $self->getQuery()->replace(
            UrlBag::parse(
                (string)$self->getServer()->get(
                    HeaderTypeBag::REQUEST_URI,
                    '/'
                )
            )->getQueryMap()
        );

        $self->setRoute(
            new RouteBag(
                $self->getHeader(), $self->getServer()
            )
        );

        if (in_array(
            $self->getServer()->getRequestMethod(),
            [
                HeaderTypeBag::METHOD_POST,
                HeaderTypeBag::METHOD_PUT,
                HeaderTypeBag::METHOD_PATCH,
            ]
        )) {
            $self->setBody(
                Helper::fromGlobalData(
                    $self->getHeader()->getContentType()
                )
            );
        }

        return $self;
    }

    /**
     * @param mixed $data
     *
     * @return \Dewep\Http\Request
     */
    public function setBody($data): Request
    {
        $this->raw = null;

        if (is_array($data)) {
            $this->replace($data);
        } else {
            $this->reset();
            $this->raw = $data;
        }

        return $this;
    }

    /**
     * @return \DOMDocument|\SimpleXMLElement|array
     */
    public function getRawBody()
    {
        return $this->raw ?? $this->all();
    }

    /**
     * @return \Dewep\Http\ArrayAccess
     */
    public function getQuery(): \Dewep\Http\ArrayAccess
    {
        return $this->query;
    }

    /**
     * @param \Dewep\Http\ArrayAccess $query
     *
     * @return Request
     */
    public function setQuery(\Dewep\Http\ArrayAccess $query): Request
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return \Dewep\Http\ServerBag
     */
    public function getServer(): \Dewep\Http\ServerBag
    {
        return $this->server;
    }

    /**
     * @param \Dewep\Http\ServerBag $server
     *
     * @return Request
     */
    public function setServer(\Dewep\Http\ServerBag $server): Request
    {
        $this->server = $server;

        return $this;
    }

    /**
     * @return \Dewep\Http\FileBag[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param \Dewep\Http\FileBag[] $files
     *
     * @return Request
     */
    public function setFiles($files): Request
    {
        foreach ($files as $file) {
            if ($file instanceof FileBag) {
                $this->files[] = $file;
            }
        }

        return $this;
    }

    /**
     * @return \Dewep\Http\CookieBag
     */
    public function getCookie(): \Dewep\Http\CookieBag
    {
        return $this->cookie;
    }

    /**
     * @param \Dewep\Http\CookieBag $cookie
     *
     * @return Request
     */
    public function setCookie(\Dewep\Http\CookieBag $cookie): Request
    {
        $this->cookie = $cookie;

        return $this;
    }

    /**
     * @return \Dewep\Http\SessionBag|null
     */
    public function getSession(): ?\Dewep\Http\SessionBag
    {
        return $this->session;
    }

    /**
     * @param \Dewep\Http\SessionBag $session
     *
     * @return Request
     */
    public function setSession(\Dewep\Http\SessionBag $session): Request
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return \Dewep\Http\HeaderBag
     */
    public function getHeader(): \Dewep\Http\HeaderBag
    {
        return $this->header;
    }

    /**
     * @param \Dewep\Http\HeaderBag $header
     *
     * @return Request
     */
    public function setHeader(\Dewep\Http\HeaderBag $header): Request
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return \Dewep\Http\RouteBag
     */
    public function getRoute(): \Dewep\Http\RouteBag
    {
        return $this->route;
    }

    /**
     * @param \Dewep\Http\RouteBag $route
     *
     * @return Request
     */
    public function setRoute(\Dewep\Http\RouteBag $route): Request
    {
        $this->route = $route;

        return $this;
    }

}
