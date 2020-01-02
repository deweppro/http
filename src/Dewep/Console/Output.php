<?php

declare(strict_types=1);

namespace Dewep\Console;

use Dewep\Http\ArrayAccess;

/**
 * @method write(string $message)
 * @method success(string $message)
 * @method danger(string $message)
 * @method warning(string $message)
 * @method info(string $message)
 */
final class Output extends ArrayAccess
{
    /** @var array */
    protected static $colors = [
        'write'   => "\e[0m",
        'success' => "\e[032m",
        'danger'  => "\e[031m",
        'warning' => "\e[033m",
        'info'    => "\e[034m",
    ];

    public function __construct()
    {
        parent::__construct(false);
    }

    public function __call(string $name, array $arguments): void
    {
        if (!isset(self::$colors[$name])) {
            throw new \LogicException('Undefined method '.$name);
        }

        $this->colors($name, $arguments);
    }

    public function progress(
        int $current = 0,
        int $max = 100,
        string $message = ''
    ): void {
        $linelen = 30;
        $datalen = 60;

        $count = (int)abs($current * $linelen / $max);
        $count = $count > $linelen ? $linelen : $count;

        $message = mb_substr($message, 0, $datalen);
        $len     = mb_strlen($message);
        if ($len < $datalen) {
            $message .= str_repeat(' ', $datalen - $len);
        }

        echo "\r[";
        if ($count > 0) {
            echo str_repeat('=', $count);
        }
        if ($count === $linelen) {
            echo str_repeat('.', $linelen - $count);
        }
        echo ']';
        echo $current.'/'.$max;
        echo $message;
        if ($count === $linelen) {
            echo PHP_EOL;
        }
    }

    protected function colors(string $name, array $messages): void
    {
        echo self::$colors[$name];
        array_walk_recursive(
            $messages,
            function ($el) {
                if (is_scalar($el)) {
                    echo $el;
                    echo "\t";
                }
            }
        );
        echo self::$colors[$name];
        echo PHP_EOL;
    }
}
