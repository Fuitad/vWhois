<?php

namespace vWhois\Parsers;

class Whoisgodaddycom extends BaseIcannCompliant
{
    protected function setExpiresOn()
    {
        $this->record->expiresOn = $this->valForKey('Registry Expiry Date');

        return $this;
    }
}
