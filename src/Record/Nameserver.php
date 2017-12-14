<?php

namespace vWhois\Record;

class Nameserver
{
    public $server;
    public $ipv4;
    public $ipv6;

    public function __construct($server, $ipv4 = false, $ipv6 = '')
    {
        if (!$server) {
            return false;
        }

        $this->server = strtolower($server);

        $this->ipv4 = $ipv4 ?: gethostbyname($this->server);

        $this->ipv6 = $ipv6;
    }

    public function __toString()
    {
        return $this->server;
    }
}
