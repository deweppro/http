<?php

declare(strict_types=1);

namespace Dewep\Http;

use Dewep\Http\Interfaces\ArrayAccessInterface;

final class SessionBag implements ArrayAccessInterface
{
    /** @var array */
    private $object = [];

    public function __construct(array &$data)
    {
        $this->object = $data;
    }

    public static function initialize(
        ?\SessionHandlerInterface $handler,
        int $lifetime = 3600,
        string $domain = '*',
        string $path = '/',
        bool $secure = false,
        bool $httponly = true
    ): self {
        if (null !== $handler) {
            session_set_save_handler($handler, true);
        }

        session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
        session_start();

        return new static($_SESSION);
    }

    /**
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        $this->object[$key] = $value;
    }

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->object[$key] ?? $default;
    }

    public function keys(): array
    {
        return array_keys($this->object);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->object);
    }

    public function all(): array
    {
        return $this->object;
    }

    public function replace(array $data): void
    {
        $this->object = $data;
    }

    public function remove(string $key): void
    {
        unset($this->object[$key]);
    }

    public function reset(): void
    {
        $this->object = [];
    }
}
