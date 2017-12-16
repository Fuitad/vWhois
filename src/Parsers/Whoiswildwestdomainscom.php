<?php

namespace vWhois\Parsers;

class Whoiswildwestdomainscom extends BaseIcannCompliant
{
    protected function setUpdatedOn()
    {
        $this->record->updatedOn = $this->valForKey('Updated Date');

        return $this;
    }

    protected function setExpiresOn()
    {
        $this->record->expiresOn = $this->valForKey('Registry Expiry Date');

        return $this;
    }
}
