<?php

namespace vWhois\Adapters;

use vWhois\Exceptions\NotImplementedException;
use vWhois\Exceptions\SocketException;
use vWhois\Exceptions\NoParserForServerException;

class Base
{
    /**
     * Default WHOIS request port.
     *
     * @var integer
     */
    protected $defaultWhoisPort = 43;

    /**
     * The type of WHOIS server.
     *
     * @var string
     */
    protected $type;

    /**
     * Optional adapter properties.
     *
     * @var array
     */
    protected $options;

    /**
     * Temporary internal response buffer.
     *
     * @var array
     */
    protected $buffer = [];

    public function __construct($type, $host, $options = [])
    {
        $this->type = $type;
        $this->host = $host;
        $this->options = $options;
    }

    protected function querySocket($query, $host, $port = false)
    {
        if (!$port) {
            $port = $this->defaultWhoisPort;
        }

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            throw new SocketException('socket_create() failed: reason: ' . socket_strerror(socket_last_error()));
        }

        $result = socket_connect($socket, $host, $port);
        if ($result === false) {
            throw new SocketException('socket_connect() failed: reason: ' . socket_strerror(socket_last_error()));
        }

        $in = "${query}\r\n";

        $buffer = '';

        socket_write($socket, $in, strlen($in));

        while ($out = socket_read($socket, 2048)) {
            $buffer .= $out;
        }

        socket_close($socket);

        $buffer = str_replace("\r", '', $buffer);
        //$buffer = str_replace("\n", '[N]', $buffer);

        return $buffer;
    }

    protected function bufferAppend($response, $host)
    {
        $parserClass = 'vWhois\\Parsers\\' . ucfirst(camel_case(str_replace('.', '', $host)));

        if (!class_exists($parserClass)) {
            throw new NoParserForServerException("No parser for ${host} found. The class name should be ${parserClass}");
        }

        $parser = new $parserClass($response, $host);

        $parser->parse();

        $this->buffer[] = $parser->record;
    }

    public function request($query, $allowRecursive = true)
    {
        throw new NotImplementedException();
    }

    public function getBuffer()
    {
        return $this->buffer;
    }
}
