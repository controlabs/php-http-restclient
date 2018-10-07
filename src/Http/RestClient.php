<?php

namespace Controlabs\Http;

use Controlabs\Http\Handler;

class RestClient
{
    private $handler;

    protected $url;

    protected $baseUrl = '';

    protected $method = 'GET';

    protected $params = [];

    protected $headers = [];

    protected $content;

    protected $contentType = 'application/json';

    protected $reuse = false;

    public function __construct($baseUrl = '', $headers = [], $reuse = false)
    {
        $this->baseUrl = $baseUrl;
        $this->headers = $headers;
        $this->reuse = $reuse;

        $this->handler = $this->createHandler();
    }

    protected function createHandler()
    {
        return new CurlHandler();
    }

    public static function create($baseUrl = '', $headers = [], $reuse = false)
    {
        return new static($baseUrl, $headers, $reuse);
    }

    public function url($url, $params = [])
    {
        $this->url = $url;

        if ($params) {
            $this->params = array_merge($this->params, $params);
        }

        return $this;
    }

    public function baseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function header($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function headers(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    public function param($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    public function params(array $params)
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function data($data)
    {
        $this->content = $data;

        return $this;
    }

    protected function buildUri($params = [])
    {
        $baseParts  = parse_url($this->baseUrl);

        $baseScheme = isset($baseParts['scheme']) ? $baseParts['scheme']: '';
        $baseHost   = isset($baseParts['host']) ? $baseParts['host']    : '';
        $basePort   = isset($baseParts['port']) ? $baseParts['port']    : null;

        $baseUrl = ($baseScheme !== '' ? $baseScheme . '://' : '')
            . $baseHost
            . ($basePort ? ':' . $basePort : '');

        $parts    = parse_url($this->url);

        $path     = isset($parts['path']) ? $parts['path']        : '';
        $query    = isset($parts['query']) ? $parts['query']      : '';
        $fragment = isset($parts['fragment']) ? $parts['fragment']: '';

        $params = $params ?: $this->params;

        $params = http_build_query($params);

        if ($query) {
            $params = $params ? '&' . $params : '';
        } else {
            $params = $params ? '?' . $params : '';
        }

        $url = $path
            . ($query ? '?' . $query : '')
            . $params
            . $fragment;

        $url = '/' . ltrim($url, '/');

        return $baseUrl . $url;
    }

    protected function send($method, $postFields = [])
    {
        $url = $this->buildUri();

        $this->handler->setUrl($url);
        $this->handler->setMethod($method);

        if ($postFields) {
            $this->handler->setPostFields($postFields);
        }

        $response = $this->handler->execute($this->reuse);

        return $response;
    }

    public function get($params = [])
    {
        $response = $this->send('GET');

        return $response;
    }

    public function post($values = [])
    {
        $response = $this->send('POST', $values);

        return $response;
    }

    public function put($values = [])
    {
        $response = $this->send('PUT', $values);

        return $response;
    }

    public function delete($values = [])
    {
        $response = $this->send('DELETE', $values);

        return $response;
    }
}
