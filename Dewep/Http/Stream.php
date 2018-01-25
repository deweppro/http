<?php

namespace Dewep\Http;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Stream
{

    const PIPE  = 4480;
    const OTHER = 33206;

    /** @var array */
    private static $modes = [
        'readable' => ['r', 'r+', 'w+', 'a+', 'x+', 'c+'],
        'writable' => ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'],
    ];
    /** @var resource */
    private $handle;
    /** @var bool */
    private $pipe = false;

    /**
     * @param null $handle
     * @throws \Exception
     */
    public function __construct($handle = null)
    {
        if (!is_resource($handle)) {
            throw new \Exception('Not supplied resource.');
        }

        $this->handle = $handle;
        $stat         = fstat($this->handle);

        if ($stat['mode'] == self::PIPE) {
            $this->pipe = true;
        } elseif ($stat['mode'] == self::OTHER) {
            $this->pipe = false;
        } else {
            throw new \Exception('Undefined resource mode.');
        }
    }

    /**
     * @param null $handle
     * @return Stream
     */
    public static function bootstrap($handle = null): Stream
    {
        if (!is_resource($handle)) {
            $handle = fopen('php://temp', 'r+');
            stream_copy_to_stream(fopen('php://input', 'r'), $handle);
            rewind($handle);
        }

        return new static($handle);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            $this->rewind();

            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @throws \Exception
     */
    public function rewind()
    {
        if (
            !$this->isSeekable() ||
            rewind($this->handle) === false
        ) {
            throw new \Exception('Could not rewind stream');
        }
    }

    /**
     * @return bool
     */
    public function isSeekable(): bool
    {
        $mode = $this->getMetadata('seekable');

        return !empty($mode);
    }

    /**
     * @param $key
     * @return null
     */
    public function getMetadata($key)
    {
        $meta = stream_get_meta_data($this->handle);

        return $meta[(string)$key] ?? null;
    }

    /**
     * @return array
     */
    public function getAllMetadata(): array
    {
        return stream_get_meta_data($this->handle);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getContents(): string
    {
        if (
            !$this->isReadable() ||
            ($contents = stream_get_contents($this->handle)) === false
        ) {
            throw new \Exception('Could not get contents of stream.');
        }

        return $contents;
    }

    /**
     * @return bool
     */
    public function isReadable(): bool
    {
        $mode     = $this->getMetadata('mode');
        $readeble = array_filter(
            self::$modes['readable'],
            function ($v) use ($mode) {
                return stripos((string)$mode, $v) !== false;
            },
            ARRAY_FILTER_USE_BOTH
        );

        return !empty($readeble);
    }

    public function close()
    {
        if ($this->pipe) {
            pclose($this->handle);
        } else {
            fclose($this->handle);
        }
    }

    /**
     * @return resource
     */
    public function detach()
    {
        $old = $this->handle;

        $this->handle = null;
        $this->pipe   = false;

        return $old;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        $stats = fstat($this->handle);

        return (int)($stats['size'] ?? false);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function tell(): int
    {
        if (($position = ftell($this->handle)) === false || $this->pipe) {
            throw new \Exception('Could not get the position of the pointer in stream');
        }

        return (int)$position;
    }

    /**
     * @return bool
     */
    public function eof(): bool
    {
        return feof($this->handle);
    }

    /**
     * @param int $offset
     * @param int $whence
     * @throws \Exception
     */
    public function seek(int $offset, $whence = SEEK_SET)
    {
        if (
            !$this->isSeekable() ||
            fseek($this->handle, $offset, (int)$whence) === -1
        ) {
            throw new \Exception('Could not seek in stream');
        }
    }

    /**
     * @param $string
     * @return int
     * @throws \Exception
     */
    public function write($string): int
    {
        if (
            !$this->isWritable() ||
            ($written = fwrite($this->handle, (string)$string)) === false
        ) {
            throw new \Exception('Could not write to stream');
        }

        return $written;
    }

    /**
     * @return bool
     */
    public function isWritable(): bool
    {
        $mode     = $this->getMetadata('mode');
        $writable = array_filter(
            self::$modes['writable'],
            function ($v) use ($mode) {
                return stripos((string)$mode, $v) !== false;
            },
            ARRAY_FILTER_USE_BOTH
        );

        return !empty($writable);
    }

    /**
     * @param int $length
     * @return string
     * @throws \Exception
     */
    public function read(int $length): string
    {
        if (
            !$this->isReadable() ||
            ($data = fread($this->handle, $length)) === false
        ) {
            throw new \Exception('Could not read from stream');
        }

        return $data;
    }

}
