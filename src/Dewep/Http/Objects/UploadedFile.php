<?php

namespace Dewep\Http\Objects;

/**
 * Class UploadedFile
 *
 * @package Dewep\Http
 */
class UploadedFile
{
    /** @var string */
    protected $file = '';

    /** @var \Dewep\Http\Objects\Stream|false */
    protected $stream = false;

    /** @var string */
    protected $name = '';

    /** @var string */
    protected $type = '';

    /** @var int */
    protected $size = 0;

    /** @var int */
    protected $error = UPLOAD_ERR_OK;

    /** @var bool */
    protected $moved = false;

    /**
     * UploadedFile constructor.
     *
     * @param string $file
     * @param string $name
     * @param string $type
     * @param int    $size
     * @param int    $error
     */
    public function __construct(string $file, string $name, string $type, int $size, int $error)
    {
        $this->file = $file;
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->error = $error;
    }

    /**
     * @return array
     */
    public static function bootstrap(): array
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
     * @return \Dewep\Http\Objects\Stream
     * @throws \Exception
     */
    public function getStream(): Stream
    {
        if ($this->moved === true) {
            throw new \Exception(
                "Uploaded file {$this->name} has already been moved"
            );
        }
        if ($this->stream === false) {
            $this->stream = new Stream(fopen($this->file, 'r'));
        }

        return $this->stream;
    }

    /**
     * @param string $targetPath
     *
     * @return bool
     * @throws \Exception
     */
    public function moveTo(string $targetPath): bool
    {
        if ($this->moved === true) {
            throw new \Exception('Uploaded file already moved');
        }
        if (false === move_uploaded_file($this->file, $targetPath)) {
            throw new \Exception("Error moving uploaded file {$this->name} to {$targetPath}");
        }

        return $this->moved = true;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getClientFilename(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getClientMediaType(): string
    {
        return $this->type;
    }

}
