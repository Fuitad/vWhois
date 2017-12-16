<?php

namespace vWhois\Parsers;

class WhoisverisignGrscom extends BaseVerisign
{
    protected function setExpiresOn()
    {
        $this->record->expiresOn = $this->valForKey('Registry Expiry Date');

        return $this;
    }
}
