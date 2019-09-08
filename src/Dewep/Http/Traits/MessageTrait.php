<?php

namespace Dewep\Http\Traits;

use Dewep\Exception\HttpException;
use Dewep\Http\Objects\Headers;
use Dewep\Http\Objects\Stream;

/**
 * Class Message
 *
 * @package Dewep\Http
 */
trait MessageTrait
{
    /** @var array */
    protected static $validProtocolVersions = ['1.0', '1.1', '2.0', '2',];
    /** @var Headers */
    public $headers;
    /** @var Stream */
    public $body;
    /** @var string */
    protected $protocolVersion = '1.1';

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @param string $version
     *
     * @return \Dewep\Http\Traits\MessageTrait
     * @throws \Dewep\Exception\HttpException
     */
    public function setProtocolVersion(string $version): self
    {
        if (!in_array($version, self::$validProtocolVersions)) {
            throw new HttpException('Invalid HTTP version.');
        }

        $this->protocolVersion = $version;

        return $this;
    }
}
