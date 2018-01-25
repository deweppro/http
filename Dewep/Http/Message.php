<?php

namespace Dewep\Http;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
abstract class Message
{

    /** @var array */
    protected static $validProtocolVersions = ['1.0', '1.1', '2.0', '2',];
    /** @var string */
    protected $protocolVersion = '1.1';
    /** @var Headers */
    public $headers;
    /** @var Stream */
    public $body;

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @param string $version
     * @return Message
     * @throws \Exception
     */
    public function withProtocolVersion(string $version): Message
    {
        if (!in_array($version, self::$validProtocolVersions)) {
            throw new \Exception('Invalid HTTP version.');
        }

        $clone                  = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers->all();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader(string $name): bool
    {
        return $this->headers->has($name);
    }

    /**
     * @param string $name
     * @return array
     */
    public function getHeader(string $name): array
    {
        return $this->headers->get($name);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeaderLine(string $name): string
    {
        $value = $this->headers->get($name, []);

        return implode(', ', $value);
    }

    /**
     * @param string $name
     * @param array $value
     * @return Message
     */
    public function withHeader(string $name, array $value): Message
    {
        $clone = clone $this;
        $clone->headers->set($name, $value);

        return $clone;
    }

    /**
     * @param string $name
     * @param array $value
     * @return Message
     */
    public function withAddedHeader(string $name, array $value): Message
    {
        $clone = clone $this;
        $clone->headers->add($name, $value);

        return $clone;
    }

    /**
     * @param $name
     * @return Message
     */
    public function withoutHeader($name): Message
    {
        $clone = clone $this;
        $clone->headers->remove($name);

        return $clone;
    }

    /**
     * @return Stream
     */
    public function getBody(): Stream
    {
        return $this->body;
    }

    /**
     * @param Stream $body
     * @return Message
     */
    public function withBody(Stream $body): Message
    {
        $clone       = clone $this;
        $clone->body = $body;

        return $clone;
    }

}
