<?php declare(strict_types=1);

namespace Dewep\Http;

/**
 * Class CookieBag
 *
 * @package Dewep\Http
 */
class CookieBag extends ArrayAccess
{
    /** @var array */
    protected $update = [];

    /**
     * CookieBag constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct(false);

        $this->replace($data);
    }

    /**
     * @return \Dewep\Http\CookieBag
     */
    public static function initialize(): self
    {
        return new static($_COOKIE);
    }

    /**
     * @param string      $key
     * @param mixed       $value
     * @param int|null    $expire
     * @param string|null $path
     * @param string|null $domain
     */
    public function set(
        string $key,
        $value,
        ?int $expire = null,
        ?string $path = null,
        ?string $domain = null
    ) {
        if (!is_scalar($value)) {
            return;
        }
        parent::set($key, $value);

        $this->update[$key] = [$key, $value, $expire, $path, $domain];
    }

    /**
     * @param string $key
     */
    public function remove(string $key)
    {
        parent::remove($key);

        $this->update[$key] = [$key, '', time() - 1];
    }

    /**
     *
     */
    public function reset()
    {
        foreach ($this->all() as $key => $value) {
            $this->update[$key] = [$key, '', time() - 1];
        }
        parent::reset();
    }

    public function send()
    {
        foreach ($this->update as [$key, $value, $expire, $path, $domain]) {
            header(
                sprintf(
                    'Set-Cookie: %s=%s; %s %s %s Secure; HttpOnly; SameSite=Lax',
                    (string)$key,
                    (string)$value,
                    $expire ? sprintf(
                        'Expires=%s;',
                        gmdate('D, j M Y H:i:s T', time() + (int)$expire)
                    ) : '',
                    $path ? sprintf(
                        'Path=%s;',
                        $path
                    ) : '',
                    $domain ? sprintf(
                        'Domain=%s;',
                        $domain
                    ) : ''
                )
            );
        }
    }
}
