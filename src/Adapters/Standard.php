<?php

namespace vWhois\Adapters;

class Standard extends Base
{
    protected function request($query)
    {
        $response = $this->querySocket($query, $this->host);
        $this->bufferAppend($response, $this->host);
    }
}
