<?php

namespace vWhois\Adapters;

class Standard extends Base
{
    public function request($query, $allowRecursive = true)
    {
        $response = $this->querySocket($query, $this->host);
        $this->bufferAppend($response, $this->host);
    }
}
