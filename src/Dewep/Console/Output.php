<?php

namespace Dewep\Console;

/**
 * Class Output
 *
 * @package Dewep\Console
 */
class Output
{
    const COLOR_BLANK  = "\e[0m";
    const COLOR_RED    = "\e[031m";
    const COLOR_GREEN  = "\e[032m";
    const COLOR_YELLOW = "\e[033m";
    const COLOR_BLUE   = "\e[034m";
    const BAR          = "\r[";
    const BAR_END      = "]";
    const BAR_CLEN     = 30;
    const BAR_LEN      = 60;

    /**
     * @param string $message
     */
    public function write(string $message = '')
    {
        echo self::COLOR_BLANK.$message.self::COLOR_BLANK.PHP_EOL;
    }

    /**
     * @param string $message
     */
    public function success(string $message = '')
    {
        echo self::COLOR_GREEN.$message.self::COLOR_BLANK.PHP_EOL;
    }

    /**
     * @param string $message
     */
    public function danger(string $message = '')
    {
        echo self::COLOR_RED.$message.self::COLOR_BLANK.PHP_EOL;
    }

    /**
     * @param string $message
     */
    public function warning(string $message = '')
    {
        echo self::COLOR_YELLOW.$message.self::COLOR_BLANK.PHP_EOL;
    }

    /**
     * @param string $message
     */
    public function info(string $message = '')
    {
        echo self::COLOR_BLUE.$message.self::COLOR_BLANK.PHP_EOL;
    }

    /**
     * @param int    $current
     * @param int    $max
     * @param string $message
     */
    public function progress(int $current = 0, int $max = 100, string $message = '')
    {
        $count = (int)abs($current * self::BAR_CLEN / $max);
        $count = $count > self::BAR_CLEN ? self::BAR_CLEN : $count;

        $message = mb_substr($message, 0, self::BAR_LEN);
        $len = mb_strlen($message);
        if ($len < self::BAR_LEN) {
            $message .= str_repeat(' ', self::BAR_LEN - $len);
        }

        echo self::BAR.
            ($count === 0 ? '' : str_repeat("=", $count)).
            ($count === self::BAR_CLEN ? '' : str_repeat(".", self::BAR_CLEN - $count)).
            self::BAR_END.
            ' '.$current.'/'.$max.' '.
            $message.
            ($count === self::BAR_CLEN ? PHP_EOL : '');
    }
}
