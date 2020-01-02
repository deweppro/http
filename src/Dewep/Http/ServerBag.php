<?php

declare(strict_types=1);

namespace Dewep\Http;

final class ServerBag extends ArrayAccess
{
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
                return 0 !== stripos($k, 'HTTP_');
            },
            ARRAY_FILTER_USE_KEY
        );

        return new static($headers);
    }

    public function getRequestMethod(): string
    {
        return (string)$this->get(HeaderTypeBag::REQUEST_METHOD, '');
    }

    public function isOptions(): bool
    {
        return HeaderTypeBag::METHOD_OPTIONS === $this->getRequestMethod();
    }

    public function isGet(): bool
    {
        return HeaderTypeBag::METHOD_GET === $this->getRequestMethod();
    }

    public function isPost(): bool
    {
        return HeaderTypeBag::METHOD_POST === $this->getRequestMethod();
    }

    public function isPut(): bool
    {
        return HeaderTypeBag::METHOD_PUT === $this->getRequestMethod();
    }

    public function isDelete(): bool
    {
        return HeaderTypeBag::METHOD_DELETE === $this->getRequestMethod();
    }

    public function isPatch(): bool
    {
        return HeaderTypeBag::METHOD_PATCH === $this->getRequestMethod();
    }
}
