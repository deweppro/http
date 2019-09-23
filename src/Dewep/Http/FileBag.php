<?php declare(strict_types=1);

namespace Dewep\Http;

class FileBag
{
    /** @var string */
    protected $file = '';

    /** @var \Dewep\Http\Stream|null */
    protected $stream;

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
     * FileBag constructor.
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
     * @return \Dewep\Http\Stream|null
     * @throws \Dewep\Exception\StreamException
     */
    public function get(): ?Stream
    {
        if ($this->stream === null) {
            $this->open();
        }

        return $this->stream;
    }

    /**
     * @throws \Dewep\Exception\StreamException
     */
    protected function open()
    {
        $this->stream = new Stream(fopen($this->file, 'r'));
    }

    /**
     * @param string $targetPath
     *
     * @return bool
     * @throws \Dewep\Exception\StreamException
     */
    public function moveTo(string $targetPath): bool
    {
        if ($this->moved === true) {
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
