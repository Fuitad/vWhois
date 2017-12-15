<?php

namespace vWhois\Parsers;

class Whoisnetworksolutionscom extends BaseIcannCompliant
{
    protected function setExpiresOn()
    {
        $this->record->expiresOn = $this->valForKey('Registry Expiry Date');

        return $this;
    }
}
