<?php declare(strict_types=1);

namespace Dewep\Http;

use Dewep\Exception\StreamException;

/**
 * Class Stream
 *
 * @package Dewep
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
     * Stream constructor.
     *
     * @param resource|false $handle
     *
     * @throws \Dewep\Exception\StreamException
     */
    public function __construct($handle)
    {
        if (!is_resource($handle)) {
            throw new StreamException('Not supplied resource.');
        }

        $this->handle = $handle;
        $stat = fstat($this->handle);

        if ($stat['mode'] == self::PIPE) {
            $this->pipe = true;
        } elseif ($stat['mode'] == self::OTHER) {
            $this->pipe = false;
        } else {
            throw new StreamException('Undefined resource mode.');
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * @return \Dewep\Http\Stream
     * @throws \Dewep\Exception\StreamException
     */
    public static function initialize(): self
    {
        $handle = fopen('php://temp', 'r+');
        $source = fopen('php://input', 'r');

        if ($handle !== false && $source !== false) {
            stream_copy_to_stream($source, $handle);
            rewind($handle);
            fclose($source);
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
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * @throws \Dewep\Exception\StreamException
     */
    public function rewind()
    {
        if (
            false === $this->isSeekable() ||
            false === rewind($this->handle)
        ) {
            throw new StreamException('Could not rewind stream');
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
     * @param string $key
     *
     * @return mixed
     */
    public function getMetadata(string $key)
    {
        $meta = stream_get_meta_data($this->handle);

        return $meta[$key] ?? null;
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
     */
    public function getContents(): string
    {
        if (
            $this->isReadable() === false ||
            ($contents = stream_get_contents($this->handle)) === false
        ) {
            return '';
        }

        return (string)$contents;
    }

    /**
     * @return bool
     */
    public function isReadable(): bool
    {
        $mode = $this->getMetadata('mode');
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
        if (!is_resource($this->handle)) {
            return;
        }

        if ($this->pipe) {
            pclose($this->handle);
        } else {
            fclose($this->handle);
        }

        unset($this->handle);
    }

    /**
     * @return resource
     */
    public function detach()
    {
        $old = $this->handle;

        unset($this->handle);
        $this->pipe = false;

        return $old;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        $stats = fstat($this->handle);

        return $stats['size'] ?? 0;
    }

    /**
     * @return int
     * @throws \Dewep\Exception\StreamException
     */
    public function tell(): int
    {
        if (
            ($position = ftell($this->handle)) === false ||
            $this->pipe
        ) {
            throw new StreamException(
                'Could not get the position of the pointer in stream'
            );
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
     *
     * @throws \Dewep\Exception\StreamException
     */
    public function seek(int $offset, int $whence = SEEK_SET)
    {
        if (
            !$this->isSeekable() ||
            fseek($this->handle, $offset, $whence) === -1
        ) {
            throw new StreamException('Could not seek in stream');
        }
    }

    /**
     * @param string $string
     *
     * @return int
     * @throws \Dewep\Exception\StreamException
     */
    public function write(string $string): int
    {
        if (
            !$this->isWritable() ||
            ($written = fwrite($this->handle, $string)) === false
        ) {
            throw new StreamException('Could not write to stream');
        }

        return (int)$written;
    }

    /**
     * @return bool
     */
    public function isWritable(): bool
    {
        $mode = $this->getMetadata('mode');
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
     *
     * @return string
     * @throws \Dewep\Exception\StreamException
     */
    public function read(int $length): string
    {
        if (
            !$this->isReadable() ||
            ($data = fread($this->handle, $length)) === false
        ) {
            throw new StreamException('Could not read from stream');
        }

        return (string)$data;
    }
}
