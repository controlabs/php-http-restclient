<?php

namespace Controlabs\Http;

class CurlError
{
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException("Handler passed as argument must be a resource.");
        }

        $this->resource = $resource;
    }

    public function code()
    {
        return curl_errno($this->resource);

    }

    public function message()
    {
        return curl_error($this->resource);
    }
}
