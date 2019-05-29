<?php


namespace Dewep\Console;


use Dewep\Http\Objects\Base;

/**
 * Class Input
 * @package Dewep\Console
 */
class Input extends Base
{

    /**
     * Input constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function build()
    {
        $result = getopt(
            '',
            array_map(
                function ($el) {
                    return $el.'::';
                },
                $this->keys()
            )
        );
        $this->replace($result);
    }

    /**
     * @param string $name
     * @param string $type
     * @param null $default
     */
    public function setOptions(string $name, $default = null)
    {
        $name = strtolower($name);
        $this->set($name, $default);
    }
}
