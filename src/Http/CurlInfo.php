<?php

namespace Controlabs\Http;

use InvalidArgumentException;

class CurlInfo
{
    protected $resource;

    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException("Handler passed as argument must be a resource.");
        }

        $this->resource = $resource;
    }

    public function collect($option = null)
    {
        $info = curl_getinfo($this->resource);

        return $option ? ($info[$option] ?? null) : $info;
    }
}
