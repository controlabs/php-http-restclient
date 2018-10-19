<?php

namespace Controlabs\Http;

/**
 * @method Response get()
 * @method Response put(array $params = [])
 * @method Response post(array $params = [])
 * @method Response delete(array $params = [])
 */
class RestClient
{
    protected const VALID_METHODS = ['get', 'post', 'put', 'delete'];

    private $handler;

    protected $url;

    protected $baseUrl = '';

    protected $method = 'GET';

    protected $params = [];

    protected $bindings = [];

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

    public function url($url, $params = [], $bindings = [])
    {
        $this->url = $url;

        if ($params) {
            $this->params = array_merge($this->params, $params);
        }

        if ($bindings) {
            $this->bindings = array_merge($this->bindings, $bindings);
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

    public function bind($name, $value)
    {
        $this->bindings[$name] = $value;

        return $this;
    }

    public function bindings(array $bindings)
    {
        $this->bindings = array_merge($this->bindings, $bindings);

        return $this;
    }

    public function data($data)
    {
        $this->content = $data;

        return $this;
    }

    protected function buildUri($params = [])
    {
        $baseParts = parse_url($this->baseUrl);

        $baseScheme = $baseParts['scheme'] ?? '';
        $baseHost = $baseParts['host'] ?? '';
        $basePort = $baseParts['port'] ?? null;
        $basePath = $baseParts['path'] ?? '';

        $baseUrl = ($baseScheme !== '' ? $baseScheme . '://' : '')
            . $baseHost
            . ($basePort ? ':' . $basePort : '')
            . $basePath;

        $parts = parse_url($this->url);

        $path = isset($parts['path']) ? $parts['path'] : '';
        $query = isset($parts['query']) ? $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? $parts['fragment'] : '';

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

        $bindings = $this->bindings;

        return str_replace(array_keys($bindings), array_values($bindings), $baseUrl . $url);
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

    public function __call($method, $arguments)
    {
        if (!in_array($method, self::VALID_METHODS)) {
            throw new \RuntimeException('Invalid method : ' . $method);
        }

        return $this->send(strtoupper($method), ...$arguments);
    }
}
