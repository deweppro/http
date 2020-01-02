<?php

declare(strict_types=1);

namespace Dewep\Http;

final class CookieBag extends ArrayAccess
{
    /** @var array */
    protected $update = [];

    public function __construct(array $data)
    {
        parent::__construct(false);

        foreach ($data as $key => $value) {
            $this->getObject()->offsetSet($key, $value);
        }
    }

    public static function initialize(): self
    {
        return new static($_COOKIE);
    }

    /**
     * @param mixed $value
     */
    public function set(
        string $key,
        $value,
        ?int $expire = null,
        ?string $path = null,
        ?string $domain = null
    ): void {
        if (!is_scalar($value)) {
            return;
        }

        parent::set($key, $value);

        $this->update[$key] = [$key, $value, $expire, $path, $domain];
    }

    public function remove(string $key): void
    {
        parent::remove($key);

        $this->update[$key] = [$key, '', time() - 1];
    }

    public function reset(): void
    {
        foreach ($this->all() as $key => $value) {
            $this->update[$key] = [$key, '', time() - 1];
        }
        parent::reset();
    }

    public function send(): void
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
