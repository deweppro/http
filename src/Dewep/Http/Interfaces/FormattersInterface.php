<?php declare(strict_types=1);

namespace Dewep\Http\Interfaces;

/**
 * Interface FormattersInterface
 *
 * @package Dewep\Http\Interfaces
 */
interface FormattersInterface
{
    public static function data();

    public static function detect(string $contentType): bool;

    public static function decode($data);

    public static function encode($data);
}
