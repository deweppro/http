<?php

declare(strict_types=1);

namespace Dewep\Http;

final class HeaderTypeBag
{
    // with HTTP_
    public const ACCEPT_TYPE = 'Accept';

    public const ACCEPT_LANGUAGE = 'Accept-Language';

    public const ACCEPT_ENCODING = 'Accept-Encoding';

    public const REFERER = 'Referer';

    public const USER_AGENT = 'User-Agent';

    public const CONNECTION = 'Connection';

    public const HOST = 'Host';

    public const AJAX = 'X-Requested-With';

    // without HTTP_
    public const REQUEST_SCHEME = 'Request-Scheme';

    public const SERVER_PROTOCOL = 'Server-Protocol';

    public const DOCUMENT_ROOT = 'Document-Root';

    public const DOCUMENT_URI = 'Document-Uri';

    public const REQUEST_URI = 'Request-Uri';

    public const SCRIPT_NAME = 'Script-Name';

    public const CONTENT_LENGTH = 'Content-Length';

    public const CONTENT_TYPE = 'Content-Type';

    public const REQUEST_METHOD = 'Request-Method';

    public const QUERY_STRING = 'Query-String';

    public const REQUEST_TIME = 'Request-Time';

    public const SERVER_NAME = 'Server-Name';

    public const METHOD_CONNECT = 'CONNECT';

    public const METHOD_DELETE = 'DELETE';

    public const METHOD_GET = 'GET';

    public const METHOD_HEAD = 'HEAD';

    public const METHOD_OPTIONS = 'OPTIONS';

    public const METHOD_PATCH = 'PATCH';

    public const METHOD_POST = 'POST';

    public const METHOD_PUT = 'PUT';

    public const METHOD_TRACE = 'TRACE';

    public const VALID_METHODS = [
        self::METHOD_CONNECT,
        self::METHOD_DELETE,
        self::METHOD_GET,
        self::METHOD_HEAD,
        self::METHOD_OPTIONS,
        self::METHOD_PATCH,
        self::METHOD_POST,
        self::METHOD_PUT,
        self::METHOD_TRACE,
    ];

    public const MESSAGES_CODE = [
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
}
