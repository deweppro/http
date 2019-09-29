<?php declare(strict_types=1);

namespace Dewep\Http;

/**
 * Class HeaderBag
 *
 * @package Dewep\Http
 */
class HeaderBag extends ArrayAccess
{
    /**
     * HeaderBag constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct(true);

        $this->replace($data);
    }

    /**
     * @return \Dewep\Http\HeaderBag
     */
    public static function initialize(): self
    {
        $headers = array_filter(
            $_SERVER,
            function ($k) {
                return stripos($k, 'HTTP_') === 0;
            },
            ARRAY_FILTER_USE_KEY
        );

        return new static($headers);
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        $type = (string)$this->get(HeaderTypeBag::CONTENT_TYPE, '');

        return (string)explode(';', $type, 2)[0] ?? '';
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return (string)$this->get(HeaderTypeBag::HOST, '');
    }

    /**
     * @return string
     */
    public function getReferer(): string
    {
        return (string)$this->get(HeaderTypeBag::REFERER, '');
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return (string)$this->get(HeaderTypeBag::USER_AGENT, '');
    }

    /**
     * @return string
     */
    public function getAcceptType(): string
    {
        $type = (string)$this->get(HeaderTypeBag::ACCEPT_TYPE, '');

        return (string)explode(';', $type, 2)[0] ?? '';
    }

    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return (string)$this->get(HeaderTypeBag::AJAX, '') === 'XMLHttpRequest';
    }

    public function send()
    {
        foreach ($this->all() as $name => $values) {
            header(sprintf('%s: %s', (string)$name, (string)$values), true);
        }
    }
}
