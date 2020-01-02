<?php

declare(strict_types=1);

namespace Dewep\Http;

final class HeaderBag extends ArrayAccess
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
                return 0 === stripos($k, 'HTTP_');
            },
            ARRAY_FILTER_USE_KEY
        );

        return new static($headers);
    }

    public function getContentType(): string
    {
        $type = (string)$this->get(HeaderTypeBag::CONTENT_TYPE, '');

        return (string)explode(';', $type, 2)[0] ?? '';
    }

    public function getHost(): string
    {
        return (string)$this->get(HeaderTypeBag::HOST, '');
    }

    public function getReferer(): string
    {
        return (string)$this->get(HeaderTypeBag::REFERER, '');
    }

    public function getUserAgent(): string
    {
        return (string)$this->get(HeaderTypeBag::USER_AGENT, '');
    }

    public function getAcceptType(): string
    {
        $type = (string)$this->get(HeaderTypeBag::ACCEPT_TYPE, '');

        return (string)explode(';', $type, 2)[0] ?? '';
    }

    public function isAjax(): bool
    {
        return 'XMLHttpRequest' === (string)$this->get(HeaderTypeBag::AJAX, '');
    }

    public function send(): void
    {
        foreach ($this->all() as $name => $values) {
            header(sprintf('%s: %s', (string)$name, (string)$values), true);
        }
    }
}
