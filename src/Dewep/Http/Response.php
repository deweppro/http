<?php declare(strict_types=1);

namespace Dewep\Http;

use Dewep\Http\Formatters\Helper;

/**
 * Class Response
 *
 * @package Dewep\Http
 */
class Response
{
    /** @var int */
    protected $status = 200;

    /** @var \Dewep\Http\HeaderBag */
    protected $header;

    /** @var \Dewep\Http\CookieBag */
    protected $cookie;

    /** @var mixed */
    protected $body;

    /**
     * Response constructor.
     *
     * @param \Dewep\Http\HeaderBag $header
     * @param \Dewep\Http\CookieBag $cookie
     */
    public function __construct(HeaderBag $header, CookieBag $cookie)
    {
        $this->setHeader($header);
        $this->setCookie($cookie);
    }

    /**
     * @return \Dewep\Http\Response
     */
    public static function initialize(): self
    {
        return new static(new HeaderBag([]), new CookieBag([]));
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return \Dewep\Http\Response
     */
    public function setStatusCode(int $status): Response
    {
        $this->status = $status;

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
     * @return Response
     */
    public function setHeader(\Dewep\Http\HeaderBag $header): Response
    {
        $this->header = $header;

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
     * @return Response
     */
    public function setCookie(\Dewep\Http\CookieBag $cookie): Response
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
     *
     * @return Response
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->getHeader()->getContentType();
    }

    /**
     * @param string $value
     *
     * @return \Dewep\Http\Response
     */
    public function setContentType(string $value): Response
    {
        $this->getHeader()->set(HeaderTypeBag::CONTENT_TYPE, $value);

        return $this;
    }

    /**
     * @param string $url
     * @param int    $code
     *
     * @return \Dewep\Http\Response
     */
    public function redirect(string $url, int $code = 307): Response
    {
        $this->header->set('Location', $url);
        $this->setStatusCode($code);

        return $this;
    }

    /**
     * @throws \Dewep\Exception\UndefinedFormatException
     */
    public function send()
    {
        http_response_code($this->getStatusCode());

        $this->getHeader()->send();
        $this->getCookie()->send();

        echo Helper::encode($this->getContentType(), $this->getBody());
    }
}
