<?php

namespace vWhois\Adapters;

class Verisign extends Base
{
    protected function request($query)
    {
        $response = $this->querySocket("=${query}", $this->host);
        $this->bufferAppend($response, $this->host);

        if (array_get($this->options, 'referral', true) !== false && $referral = $this->extractReferral($response)) {
            $response = $this->querySocket("${query}", $referral);
            $this->bufferAppend($response, $referral);
        }
    }

    protected function extractReferral($response)
    {
        if (!preg_match('/Registrar WHOIS Server:(.*)/i', $response, $match)) {
            return false;
        }

        return trim($match[1]);
    }
}
