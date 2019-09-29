<?php declare(strict_types=1);

namespace Dewep\Http;

/**
 * Class ServerBag
 *
 * @package Dewep\Http
 */
class ServerBag extends ArrayAccess
{
    /**
     * ServerBag constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct(true);

        $this->replace($data);
    }

    public static function initialize(): self
    {
        $headers = array_filter(
            $_SERVER,
            function ($k) {
                return stripos($k, 'HTTP_') !== 0;
            },
            ARRAY_FILTER_USE_KEY
        );

        return new static($headers);
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return (string)$this->get(HeaderTypeBag::REQUEST_METHOD, '');
    }


    /**
     * @return bool
     */
    public function isOptions(): bool
    {
        return $this->getRequestMethod() === HeaderTypeBag::METHOD_OPTIONS;
    }

    /**
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->getRequestMethod() === HeaderTypeBag::METHOD_GET;
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->getRequestMethod() === HeaderTypeBag::METHOD_POST;
    }

    /**
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->getRequestMethod() === HeaderTypeBag::METHOD_PUT;
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->getRequestMethod() === HeaderTypeBag::METHOD_DELETE;
    }

    /**
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->getRequestMethod() === HeaderTypeBag::METHOD_PATCH;
    }

}
