<?php

namespace Dewep\Http;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class UploadedFile
{
    /** @var string */
    protected $file;
    /** @var Stream */
    protected $stream;
    /** @var string */
    protected $name;
    /** @var string */
    protected $type;
    /** @var int */
    protected $size;
    /** @var int */
    protected $error = UPLOAD_ERR_OK;
    /** @var bool */
    protected $moved = false;

    /**
     * @param $file
     * @param $name
     * @param $type
     * @param $size
     * @param $error
     */
    public function __construct($file, $name, $type, $size, $error)
    {
        $this->file  = $file;
        $this->name  = $name;
        $this->type  = $type;
        $this->size  = $size;
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
                $file['tmp_name'] ?? null,
                $file['name'] ?? null,
                $file['type'] ?? null,
                $file['size'] ?? null,
                $file['error'] ?? null
            );
        }

        return $files;
    }

    /**
     * @return Stream
     * @throws \Exception
     */
    public function getStream(): Stream
    {
        if ($this->moved) {
            throw new \Exception(
                "Uploaded file {$this->name} has already been moved"
            );
        }
        if (is_null($this->stream)) {
            $this->stream = new Stream(fopen($this->file, 'r'));
        }

        return $this->stream;
    }

    /**
     * @param string $targetPath
     * @return bool
     * @throws \Exception
     */
    public function moveTo(string $targetPath): bool
    {
        if ($this->moved) {
            throw new \Exception('Uploaded file already moved');
        }
        if (!move_uploaded_file($this->file, $targetPath)) {
            throw new \Exception("Error moving uploaded file {$this->name} to {$targetPath}");
        }

        return $this->moved = true;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return (int)$this->size;
    }

    /**
     * @return int
     */
    public function getError(): int
    {
        return (int)$this->error;
    }

    /**
     * @return string
     */
    public function getClientFilename(): string
    {
        return (string)$this->name;
    }

    /**
     * @return string
     */
    public function getClientMediaType(): string
    {
        return (string)$this->type;
    }

}
