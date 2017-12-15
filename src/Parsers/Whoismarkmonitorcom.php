<?php

namespace vWhois\Parsers;

class Whoismarkmonitorcom extends BaseIcannCompliant
{
    protected function setExpiresOn()
    {
        $this->record->expiresOn = $this->valForKey('Registry Expiry Date');

        return $this;
    }
}
