<?php

declare(strict_types=1);

namespace Dewep\Console;

use Dewep\Http\ArrayAccess;

final class Input extends ArrayAccess
{
    public function __construct()
    {
        parent::__construct(false);
    }

    public function initialize(): void
    {
        foreach ($_SERVER['argv'] as $key => $arg) {
            $matches = null;
            if (preg_match('@^\-\-(.+)=(.+)@', $arg, $matches)) {
                if ($this->has($matches[1])) {
                    $this->set($matches[1], $matches[2]);
                }
            } elseif (preg_match('@^\\-\\-(.+)@', $arg, $matches)) {
                if ($this->has($matches[1])) {
                    $this->set($matches[1], true);
                }
            }
        }
    }

    /**
     * @param null $default
     */
    public function setOptions(string $name, $default = null): void
    {
        $name = strtolower($name);
        $this->set($name, $default);
    }
}
