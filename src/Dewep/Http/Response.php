<?php

declare(strict_types=1);

namespace Dewep\Http;

use Dewep\Http\Formatters\Helper;

final class Response
{
    /** @var int */
    private $status = 200;

    /** @var \Dewep\Http\HeaderBag */
    private $header;

    /** @var \Dewep\Http\CookieBag */
    private $cookie;

    /** @var mixed */
    private $body;

    public function __construct(HeaderBag $header, CookieBag $cookie)
    {
        $this->setHeader($header);
        $this->setCookie($cookie);
    }

    public static function initialize(): self
    {
        return new static(new HeaderBag([]), new CookieBag([]));
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function setStatusCode(int $status): Response
    {
        $this->status = $status;

        return $this;
    }

    public function getHeader(): HeaderBag
    {
        return $this->header;
    }

    public function setHeader(HeaderBag $header): Response
    {
        $this->header = $header;

        return $this;
    }

    public function getCookie(): CookieBag
    {
        return $this->cookie;
    }

    public function setCookie(CookieBag $cookie): Response
    {
        $this->cookie = $cookie;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getContentType(): string
    {
        return $this->getHeader()->getContentType();
    }

    public function setContentType(string $value): Response
    {
        $this->getHeader()->set(HeaderTypeBag::CONTENT_TYPE, $value);

        return $this;
    }

    public function redirect(string $url, int $code = 307): Response
    {
        $this->header->set('Location', $url);
        $this->setStatusCode($code);

        return $this;
    }

    /**
     * @throws \Dewep\Exception\UndefinedFormatException
     */
    public function send(): void
    {
        http_response_code($this->getStatusCode());

        $this->getHeader()->send();
        $this->getCookie()->send();

        echo Helper::encode($this->getContentType(), $this->getBody());
    }
}
