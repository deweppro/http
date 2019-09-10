<?php

namespace Dewep\Http\Traits;

use Dewep\Exception\HttpException;

/**
 * Class Message
 *
 * @package Dewep\Http
 */
trait MessageTrait
{
    /** @var array */
    protected static $validProtocolVersions = ['1.0', '1.1', '2.0', '2',];

    /** @var \Dewep\Http\Objects\Headers */
    public $headers;

    /** @var \Dewep\Http\Objects\Stream */
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
     * @return $this
     * @throws \Dewep\Exception\HttpException
     */
    public function setProtocolVersion(string $version)
    {
        if (!in_array($version, self::$validProtocolVersions)) {
            throw new HttpException('Invalid HTTP version.');
        }

        $this->protocolVersion = $version;

        return $this;
    }
}
