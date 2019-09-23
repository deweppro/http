<?php declare(strict_types=1);

namespace Dewep\Console;

use Dewep\Http\ArrayAccess;

/**
 * Class Input
 *
 * @package Dewep\Console
 */
class Input extends ArrayAccess
{
    /**
     * Input constructor.
     */
    public function __construct()
    {
        parent::__construct(false);
    }

    public function initialize()
    {
        foreach ($_SERVER["argv"] as $key => $arg) {
            $matches = null;
            if (preg_match('@^\-\-(.+)=(.+)@', $arg, $matches)) {
                if ($this->has($matches[1])) {
                    $this->set($matches[1], $matches[2]);
                }
            } elseif (preg_match("@^\-\-(.+)@", $arg, $matches)) {
                if ($this->has($matches[1])) {
                    $this->set($matches[1], true);
                }
            }
        }
    }

    /**
     * @param string $name
     * @param mixed  $default
     */
    public function setOptions(string $name, $default = null)
    {
        $name = strtolower($name);
        $this->set($name, $default);
    }
}
