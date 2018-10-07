<?php

namespace Controlabs\Http;

class CurlResponse
{
    use MediaTypeParserTrait;

    protected $httpCode;

    protected $content;

    protected $contentType;

    protected $formats = [
        'json' => [
            'application/json'
        ],
        'xml' => [
            'application/xml',
            'text/xml'
        ]
    ];

    public function __construct($httpCode = 200, $content = null)
    {
        $this->httpCode = $httpCode;
        $this->content = $content;

        $this->registerDefaultMediaTypeParser();
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    public function isJson()
    {
        return in_array($this->contentType, $this->formats['json']);
    }

    public function isXml()
    {
        return in_array($this->contentType, $this->formats['xml']);
    }

    public function content()
    {
        return $this->content;
    }

    public function body()
    {
        if (isset($this->mediaTypeParsers[$this->contentType]) === true) {
            $body = $this->mediaTypeParsers[$this->contentType]($this->content);

            return $body;
        }

        return null;
    }
}
