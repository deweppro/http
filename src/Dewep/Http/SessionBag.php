<?php declare(strict_types=1);

namespace Dewep\Http;

use Dewep\Http\Interfaces\ArrayAccessInterface;

/**
 * Class SessionBag
 *
 * @package Dewep\Http
 */
class SessionBag implements ArrayAccessInterface
{
    /** @var array */
    protected $object = [];

    /**
     * SessionBag constructor.
     *
     * @param array $data
     */
    public function __construct(array &$data)
    {
        $this->object = $data;
    }

    /**
     * @param \SessionHandlerInterface|null $handler
     * @param int                           $lifetime
     * @param string                        $domain
     * @param string                        $path
     * @param bool                          $secure
     * @param bool                          $httponly
     *
     * @return \Dewep\Http\SessionBag
     */
    public static function initialize(
        ?\SessionHandlerInterface $handler,
        int $lifetime = 3600,
        string $domain = '*',
        string $path = '/',
        bool $secure = false,
        bool $httponly = true
    ): self {

        if ($handler !== null) {
            session_set_save_handler($handler, true);
        }

        session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
        session_start();

        return new static($_SESSION);
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value)
    {
        $this->object[$key] = $value;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->object[$key] ?? $default;
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->object);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->object);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->object;
    }

    /**
     * @param array $data
     */
    public function replace(array $data)
    {
        $this->object = $data;
    }

    /**
     * @param string $key
     */
    public function remove(string $key)
    {
        unset($this->object[$key]);
    }

    public function reset()
    {
        $this->object = [];
    }

}
