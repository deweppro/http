<?php

namespace Dewep\Http;

use Dewep\Parsers\Response as Resp;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Response extends Message
{

    protected static $messages = [
        //1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        //2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        //3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        //4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        //5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];
    /** @var int */
    protected $status = 200;
    /** @var string */
    protected $reasonPhrase = '';

    /**
     * @param int $status
     * @param Headers $headers
     * @param Stream $body
     */
    public function __construct(
        int $status = 200,
        Headers $headers,
        Stream $body
    ) {
        $this->status  = $status;
        $this->headers = $headers;
        $this->body    = $body;
    }

    /**
     * @return Response
     */
    public static function bootstrap(): Response
    {
        $headers = new Headers([], $_COOKIE);
        $body    = new Stream(fopen('php://temp', 'r+'));

        return new static(200, $headers, $body);
    }

    /**
     * @param int $code
     * @param string $reasonPhrase
     * @return Response
     * @throws \Exception
     */
    public function withStatus(int $code, string $reasonPhrase = ''): Response
    {
        if (!isset(static::$messages[$code])) {
            throw new \Exception('Transferred to non-standard status code');
        }

        $clone         = clone $this;
        $clone->status = $code;

        if (empty($reasonPhrase)) {
            $reasonPhrase = static::$messages[$code];
        }
        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }

    /**
     * @param $body
     * @param null $format
     * @return Message
     * @throws \Exception
     */
    public function setBody($body, $format = null)
    {
        if (is_string($format)) {
            $head     = $format;
            $handler  = null;
            $filename = null;
        } else {
            $head     = $format['head'] ?? Resp::HTTP_OTHER;
            $handler  = $format['handler'] ?? null;
            $filename = $format['filename'] ?? null;
        }

        $isFile = false;

        switch (true) {
            // json
            case $head == Resp::TYPE_JSON || $head == Resp::HTTP_JSON:
                $head    = Resp::HTTP_JSON;
                $handler = '\Dewep\Parsers\Response::json';
                $body    = is_array($body) ? $body : [$body];
                break;
            // xml
            case $head == Resp::TYPE_XML || $head == Resp::HTTP_XML:
                $head    = Resp::HTTP_XML;
                $handler = '\Dewep\Parsers\Response::xml';
                $body    = is_array($body) ? $body : [$body];
                break;
            // html
            case $head == Resp::TYPE_HTML || $head == Resp::HTTP_HTML:
                $head    = Resp::HTTP_HTML;
                $handler = '\Dewep\Parsers\Response::html';
                $body    = is_array($body) ? $body : ['body' => $body];
                break;
            // text
            case $head == Resp::TYPE_TEXT || $head == Resp::HTTP_TEXT:
                $head    = Resp::HTTP_TEXT;
                $handler = is_array($body) ? '\Dewep\Parsers\Response::json' : null;
                break;
            // image
            case in_array($head, [Resp::HTTP_GIF, Resp::HTTP_JPG, Resp::HTTP_PNG]):

                break;
            //--
            default:
                $isFile = true;
                $body   = (string)$body;
        }

        if (!empty($handler)) {
            $body = (string)call_user_func($handler, $body);
        }

        $stream = new Stream(fopen('php://temp', 'r+'));
        $stream->write($body);

        if ($isFile) {
            $filename = $filename ?? hash('md5', random_bytes(10)).'.bin';
            $head     = [
                HeaderType::CONTENT_TYPE    => [$head],
                'Pragma'                    => ['public'],
                'Expires'                   => ['0'],
                'Cache-Control'             => ['must-revalidate, post-check=0, pre-check=0', 'private'],
                'Content-Transfer-Encoding' => ['binary'],
                'Content-Length'            => [$stream->getSize()],
                'Content-Disposition'       => ['attachment', sprintf('filename="%s"', $filename)],
            ];
        } else {
            $head = [HeaderType::CONTENT_TYPE => [$head]];
        }

        $clone = $this->withBody($stream);

        return $clone->withHeaderFromArray($head);
    }

    /**
     * @param string $url
     * @return Response
     */
    public function redirect(string $url, int $code = 307)
    {
        $clone = clone $this;
        $clone->headers->set('Location', [$url]);
        $clone->setStatusCode($code);

        return $clone;
    }

    /**
     * @param int $code
     * @return Response
     */
    public function setStatusCode(int $code): Response
    {
        $this->status = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $http = sprintf(
            'HTTP/%s %s %s',
            $this->getProtocolVersion(),
            $this->getStatusCode(),
            $this->getReasonPhrase()
        );
        header($http, true);

        foreach ($this->getHeaders() as $name => $values) {
            $line = sprintf('%s: %s', $name, $this->getHeaderLine($name));
            header($line, true);
        }

        return (string)$this->getBody();
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * @return mixed|string
     */
    public function getReasonPhrase()
    {
        if (!is_null($this->reasonPhrase)) {
            return $this->reasonPhrase;
        }
        if (isset(static::$messages[$this->status])) {
            return static::$messages[$this->status];
        }

        return '';
    }

}
