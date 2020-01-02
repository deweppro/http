<?php

declare(strict_types=1);

namespace Dewep\Http;

use Dewep\Http\Formatters\Helper;

final class Request extends ArrayAccess
{
    /** @var \DOMDocument|\SimpleXMLElement|null */
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
     * @throws \Dewep\Exception\UndefinedFormatException
     */
    public static function initialize(): self
    {
        $self = new self();

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
                $self->getHeader(),
                $self->getServer()
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
     * @return array|\DOMDocument|\SimpleXMLElement
     */
    public function getRawBody()
    {
        return $this->raw ?? $this->all();
    }

    /**
     * @return \Dewep\Http\ArrayAccess
     */
    public function getQuery(): ArrayAccess
    {
        return $this->query;
    }

    /**
     * @param \Dewep\Http\ArrayAccess $query
     */
    public function setQuery(ArrayAccess $query): Request
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return \Dewep\Http\ServerBag
     */
    public function getServer(): ServerBag
    {
        return $this->server;
    }

    /**
     * @param \Dewep\Http\ServerBag $server
     */
    public function setServer(ServerBag $server): Request
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
    public function getCookie(): CookieBag
    {
        return $this->cookie;
    }

    /**
     * @param \Dewep\Http\CookieBag $cookie
     */
    public function setCookie(CookieBag $cookie): Request
    {
        $this->cookie = $cookie;

        return $this;
    }

    /**
     * @return \Dewep\Http\SessionBag|null
     */
    public function getSession(): ?SessionBag
    {
        return $this->session;
    }

    /**
     * @param \Dewep\Http\SessionBag $session
     */
    public function setSession(SessionBag $session): Request
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return \Dewep\Http\HeaderBag
     */
    public function getHeader(): HeaderBag
    {
        return $this->header;
    }

    /**
     * @param \Dewep\Http\HeaderBag $header
     */
    public function setHeader(HeaderBag $header): Request
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return \Dewep\Http\RouteBag
     */
    public function getRoute(): RouteBag
    {
        return $this->route;
    }

    /**
     * @param \Dewep\Http\RouteBag $route
     */
    public function setRoute(RouteBag $route): Request
    {
        $this->route = $route;

        return $this;
    }
}
