<?php

namespace vWhois;

use vWhois\Exceptions\NotImplementedException;
use vWhois\Exceptions\NoWhoisServerException;

class vWhois
{
    /**
     * type
     *
     * tld, ipv4 or ipv6
     *
     * @var string
     */
    protected $type = '';

    /**
     * adapter
     *
     * @var vWhois\Adapter\Base
     */
    protected $adapter;

    /**
     * definitionTypes
     *
     * @var array
     */
    protected $definitionTypes = ['tld', 'ipv4', 'ipv6'];

    /**
     * Connection timeout
     *
     * @var int
     */
    protected $timeout = 10;

    /**
     * query
     *
     * @var string
     */
    protected $query;

    /**
     * definitions
     *
     * @var array
     */
    protected $definitions = [];

    public function __construct()
    {
        $this->loadDefinitions();
    }

    protected function loadDefinitions()
    {
        foreach ($this->definitionTypes as $definitionType) {
            $definitionFile = dirname(__FILE__) . '/../data/' . $definitionType . '.json';

            if (file_exists($definitionFile)) {
                $this->definitions[$definitionType] = json_decode(file_get_contents($definitionFile), true);
            } else {
                $this->definitions[$definitionType] = [];
            }
        }
    }

    public function prepareForLookup($query)
    {
        return $this->setQuery($query)
            ->determineTypeOfQuery()
            ->determineDefinitionForQuery();
    }

    protected function determineTypeOfQuery()
    {
        if (!$this->query) {
            return false;
        }

        if ($this->isIpv4($this->query)) {
            $this->type = 'ipv4';
        } elseif ($this->isIpv6($this->query)) {
            $this->type = 'ipv6';
        } else {
            $this->type = 'tld';
        }

        return $this;
    }

    protected function isIpv4($string)
    {
        return filter_var($string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    protected function isIpv6($string)
    {
        return filter_var($string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    protected function isTld($string)
    {
        return preg_match('/^\.(xn--)?[a-z0-9]+$/', $string);
    }

    protected function determineDefinitionForQuery()
    {
        if (!$this->query || !$this->type) {
            return false;
        }

        $functionByType = 'determineDefinitionForQuery' . ucfirst($this->type);

        $this->$functionByType();

        return $this;
    }

    protected function determineDefinitionForQueryIpv4()
    {
        throw new NotImplementedException();
    }

    protected function determineDefinitionForQueryIpv6()
    {
        throw new NotImplementedException();
    }

    protected function determineDefinitionForQueryTld()
    {
        if ($this->isTld($this->query)) {
            $this->createNewAdapter('tld', 'whois.iana.org');

            return $this;
        }

        $query = $this->query;

        while ($query !== '' && strpos($query, '.') !== false) {
            $beforeFirstDot = substr($query, 0, strpos($query, '.'));
            $tld = substr($query, strlen($beforeFirstDot) + 1);

            $definition = $this->definitionForTypeAndTld($tld);

            if ($definition !== false) {
                $this->createNewAdapter('tld', array_get($definition, 'host'), $definition);

                return $this;
            }

            $query = $tld;
        }

        throw new NoWhoisServerException('Unable to find a WHOIS server for ' . $this->query);
    }

    protected function definitionForTypeAndTld($tld)
    {
        if (!$this->type || !array_key_exists($this->type, $this->definitions)) {
            return false;
        }

        if (array_key_exists($tld, $this->definitions[$this->type])) {
            return $this->definitions[$this->type][$tld];
        } else {
            return false;
        }
    }

    protected function createNewAdapter($type, $host, $options = [])
    {
        $adapter = 'vWhois\\Adapters\\' . ucfirst(camel_case(array_get($options, 'adapter', 'standard')));
        $this->adapter = new $adapter($type, $host, $options);

        return $this;
    }

    public function query($query = false, $allowRecursive = true, $returnAllResponses = false)
    {
        if (!$query) {
            return false;
        }

        if ($query && $this->query !== $query) {
            $this->prepareForLookup($query);
        }

        $this->adapter->request($query, $allowRecursive);

        $response = $this->adapter->getBuffer();

        return $returnAllResponses ? $response : end($response);
    }

    /**
     * @return vWhois\Adapter\Base
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param vWhois\Adapter\Base $adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }
}
