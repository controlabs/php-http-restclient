<?php

namespace Controlabs\Http;

class Response
{
    protected $httpCode;

    protected $content;

    protected $contentType;

    protected $mediaTypeParser;

    protected $formats = [
        'json' => [
            'application/json'
        ],
        'xml' => [
            'application/xml',
            'text/xml'
        ],
        'html' => [
            'text/html'
        ],
        'text' => [
            'text/plain'
        ]
    ];

    public function __construct($httpCode = 200, $content = null)
    {
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->mediaTypeParser = new MediaTypeParser();

        $this->mediaTypeParser->loadDefaults();
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    public function isJson()
    {
        return in_array($this->contentTypeWithoutEncoding(), $this->formats['json']);
    }

    public function isXml()
    {
        return in_array($this->contentTypeWithoutEncoding(), $this->formats['xml']);
    }

    public function isHtml()
    {
        return in_array($this->contentTypeWithoutEncoding(), $this->formats['html']);
    }

    public function content()
    {
        return $this->content;
    }

    public function body()
    {
        return $this->mediaTypeParser->parse(
            $this->contentTypeWithoutEncoding(),
            $this->content
        );
    }

    protected function contentTypeWithoutEncoding()
    {
        if (!$this->contentType) {
            return null;
        }

        return trim(explode(';', $this->contentType)[0]);
    }
}
