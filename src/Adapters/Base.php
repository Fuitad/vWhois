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
            throw new SocketException('socket_create() failed. Reason: ' . socket_strerror(socket_last_error()));
        }

        $result = socket_connect($socket, $host, $port);
        if ($result === false) {
            throw new SocketException('socket_connect() failed. Reason: ' . socket_strerror(socket_last_error()));
        }

        $in = "${query}\r\n";

        $buffer = '';

        try {
            socket_write($socket, $in, strlen($in));

            while ($out = socket_read($socket, 2048)) {
                $buffer .= $out;
            }

            socket_close($socket);
        } catch (\Exception $e) {
            throw new SocketException('Unable to perform complete communication with server. Reason: ' . socket_strerror(socket_last_error()));
        }

        $buffer = str_replace("\r\n", "\n", $buffer);

        return trim($buffer);
    }

    protected function bufferAppend($response, $host)
    {
        $this->buffer[$host] = $response;
    }

    protected function request($query)
    {
        throw new NotImplementedException();
    }

    public function lookup($query)
    {
        $this->request($query);

        $queriedHosts = array_keys($this->buffer);

        $foundParserClass = false;

        for (end($queriedHosts); key($queriedHosts) !== null; prev($queriedHosts)) {
            $hostToParse = current($queriedHosts);

            $parserClass = 'vWhois\\Parsers\\' . ucfirst(camel_case(str_replace('.', '', $hostToParse)));

            if (class_exists($parserClass)) {
                $foundParserClass = true;
                break;
            }
        }

        if (!$foundParserClass) {
            $hostToParse = end($queriedHosts);

            if (!isset($parserClass)) {
                $parserClass = 'UNKNOWN';
            }
            
            throw new NoParserForServerException("No parser for ${hostToParse} found. The class name should be ${parserClass}");
        }

        $parser = new $parserClass(implode("\n", $this->buffer), $hostToParse);

        $parser->parse();

        return $parser->record;
    }

    public function getBuffer()
    {
        return $this->buffer;
    }
}
