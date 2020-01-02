<?php

declare(strict_types=1);

namespace Dewep\Http\Interfaces;

interface ArrayAccessInterface
{
    /**
     * @param mixed $value
     */
    public function set(string $key, $value): void;

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    public function keys(): array;

    public function has(string $key): bool;

    public function all(): array;

    public function replace(array $data): void;

    public function remove(string $key): void;

    public function reset(): void;
}
