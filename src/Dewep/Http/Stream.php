<?php

declare(strict_types=1);

namespace Dewep\Http;

use Dewep\Exception\StreamException;

final class Stream
{
    public const PIPE = 4480;

    public const OTHER = 33206;

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
     * @param mixed $handle
     *
     * @throws StreamException
     */
    public function __construct($handle)
    {
        if (!is_resource($handle)) {
            throw new StreamException('Not supplied resource.');
        }

        $this->handle = $handle;

        $fstat = fstat($this->handle);
        if (!is_array($fstat)) {
            throw new StreamException('Undefined resource mode.');
        }

        $mode = (int)($fstat['mode'] ?? 0);

        if (self::PIPE === $mode) {
            $this->pipe = true;
        } elseif (self::OTHER === $mode) {
            $this->pipe = false;
        } else {
            throw new StreamException('Undefined resource mode.');
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    public function __toString(): string
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
    public static function initialize(): self
    {
        $handle = fopen('php://temp', 'r+');
        $source = fopen('php://input', 'r');

        if (false !== $handle && false !== $source) {
            stream_copy_to_stream($source, $handle);
            rewind($handle);
            fclose($source);
        }

        return new self($handle);
    }

    /**
     * @throws \Dewep\Exception\StreamException
     */
    public function rewind(): void
    {
        if (
            false === $this->isSeekable() ||
            false === rewind($this->handle)
        ) {
            throw new StreamException('Could not rewind stream');
        }
    }

    public function isSeekable(): bool
    {
        $mode = $this->getMetadata('seekable');

        return !empty($mode);
    }

    /**
     * @return mixed
     */
    public function getMetadata(string $key)
    {
        $meta = stream_get_meta_data($this->handle);

        return $meta[$key] ?? null;
    }

    public function getAllMetadata(): array
    {
        return stream_get_meta_data($this->handle);
    }

    public function getContents(): string
    {
        if (
            false === $this->isReadable() ||
            false === ($contents = stream_get_contents($this->handle))
        ) {
            return '';
        }

        return (string)$contents;
    }

    public function isReadable(): bool
    {
        $mode     = $this->getMetadata('mode');
        $readeble = array_filter(
            self::$modes['readable'],
            function ($v) use ($mode) {
                return false !== stripos((string)$mode, $v);
            },
            ARRAY_FILTER_USE_BOTH
        );

        return !empty($readeble);
    }

    public function close(): void
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

    public function getSize(): int
    {
        $stats = (array)fstat($this->handle);

        return (int)($stats['size'] ?? 0);
    }

    /**
     * @throws \Dewep\Exception\StreamException
     */
    public function tell(): int
    {
        if (
            false === ($position = ftell($this->handle)) ||
            $this->pipe
        ) {
            throw new StreamException(
                'Could not get the position of the pointer in stream'
            );
        }

        return (int)$position;
    }

    public function eof(): bool
    {
        return feof($this->handle);
    }

    /**
     * @throws \Dewep\Exception\StreamException
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (
            !$this->isSeekable() ||
            -1 === fseek($this->handle, $offset, $whence)
        ) {
            throw new StreamException('Could not seek in stream');
        }
    }

    /**
     * @throws \Dewep\Exception\StreamException
     */
    public function write(string $string): int
    {
        if (
            !$this->isWritable() ||
            false === ($written = fwrite($this->handle, $string))
        ) {
            throw new StreamException('Could not write to stream');
        }

        return (int)$written;
    }

    public function isWritable(): bool
    {
        $mode     = $this->getMetadata('mode');
        $writable = array_filter(
            self::$modes['writable'],
            function ($v) use ($mode) {
                return false !== stripos((string)$mode, $v);
            },
            ARRAY_FILTER_USE_BOTH
        );

        return !empty($writable);
    }

    /**
     * @throws \Dewep\Exception\StreamException
     */
    public function read(int $length): string
    {
        if (
            !$this->isReadable() ||
            false === ($data = fread($this->handle, $length))
        ) {
            throw new StreamException('Could not read from stream');
        }

        return (string)$data;
    }
}
