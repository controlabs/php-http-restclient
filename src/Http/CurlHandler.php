<?php

namespace Controlabs\Http;

use Controlabs\Http\CurlInfo;
use Controlabs\Http\CurlResponse;

class CurlHandler
{
    private $resource;

    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException(
                'The cURL extensions is not loaded, make sure you have installed the cURL extension: https://php.net/manual/curl.setup.php'
            );
        }

        $this->init();
        $this->enableReturn();
        $this->setFollow();
    }

    protected function init()
    {
        $this->resource = curl_init();

        $this->info = new CurlInfo($this->resource);
        $this->error = new CurlError($this->resource);
    }

    public function enableReturn()
    {
        $this->setOption(CURLOPT_RETURNTRANSFER, true);
    }

    public function getResource()
    {
        return $this->resource;
    }

    protected function setOption($option, $value)
    {
        curl_setopt($this->resource, $option, $value);
    }

    public function setUrl($url)
    {
        $this->setOption(CURLOPT_URL, $url);
    }

    public function setMethod($method)
    {
        $this->setOption(CURLOPT_CUSTOMREQUEST, $method);
    }

    public function setAsGet()
    {
        $this->setMethod('GET');
    }

    public function setAsPost()
    {
        $this->setOption(CURLOPT_POST, true);
    }

    public function setPostFields($data)
    {
        $this->setOption(CURLOPT_POSTFIELDS, $data);
    }

    public function setAsPut()
    {
        $this->setMethod('PUT');
    }

    public function setAsDelete()
    {
        $this->setMethod('DELETE');
    }

    public function setNoHeaders()
    {
        $this->setOption(CURLOPT_HTTPHEADER, false);
    }

    public function setFollow($enadled = true, $limit = 5)
    {
        $this->setOption(CURLOPT_FOLLOWLOCATION, $enadled);
        $this->setOption(CURLOPT_MAXREDIRS, $limit);
    }

    public function setPort($port)
    {
        $this->setOption(CURLOPT_PORT, intval($port));
    }

    public function setBasicAuthentication($username, $password = '')
    {
        $this->setOption(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOption(CURLOPT_USERPWD, $username . ':' . $password);
    }

    public function setUserAgent($userAgent)
    {
        $this->setOption(CURLOPT_USERAGENT, $userAgent);
    }

    public function execute($close = true)
    {
        $content = curl_exec($this->resource);

        $response = $this->respond(
            $content, $this->info->collect()
        );

        if ($close) {
            $this->close();
        }

        return $response;
    }

    protected function respond($content, $info)
    {
        $contentType = $info['content_type'];
        $httpCode = $info['http_code'];

        $response = new CurlResponse($httpCode, $content);
        $response->setContentType($contentType);

        return $response;
    }

    public function close()
    {
        curl_close($this->resource);
    }

    public function error()
    {
       return $this->error;
    }
}
