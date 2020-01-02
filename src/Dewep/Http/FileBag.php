<?php

declare(strict_types=1);

namespace Dewep\Http;

final class FileBag
{
    /** @var string */
    private $file = '';

    /** @var \Dewep\Http\Stream|null */
    private $stream;

    /** @var string */
    private $name = '';

    /** @var string */
    private $type = '';

    /** @var int */
    private $size = 0;

    /** @var int */
    private $error = UPLOAD_ERR_OK;

    /** @var bool */
    private $moved = false;

    public function __construct(string $file, string $name, string $type, int $size, int $error)
    {
        $this->file = $file;
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->error = $error;
    }

    /**
     * @return \Dewep\Http\FileBag[]
     */
    public static function initialize(): array
    {
        $files = [];

        foreach ($_FILES as $id => $file) {
            $files[$id] = new static(
                $file['tmp_name'],
                $file['name'],
                $file['type'],
                $file['size'],
                $file['error']
            );
        }

        return $files;
    }

    /**
     * @throws \Dewep\Exception\StreamException
     */
    public function get(): ?Stream
    {
        if (null === $this->stream) {
            $this->open();
        }

        return $this->stream;
    }

    /**
     * @throws \Dewep\Exception\StreamException
     */
    public function moveTo(string $targetPath): bool
    {
        if (true === $this->moved) {
            $result = copy($this->file, $targetPath);
        } else {
            $result = move_uploaded_file($this->file, $targetPath);
        }

        if ($result) {
            $this->file = $targetPath;
            $this->open();
        }

        return $result;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getClientFilename(): string
    {
        return $this->name;
    }

    public function getClientMediaType(): string
    {
        return $this->type;
    }

    /**
     * @throws \Dewep\Exception\StreamException
     */
    private function open(): void
    {
        $this->stream = new Stream(fopen($this->file, 'r'));
    }
}
