<?php

namespace vWhois\Parsers;

class WhoisverisignGrscom extends BaseVerisign
{
    public function parse()
    {
        parent::parse();

        if ($this->record->registered) {
            $this->record->expiresOn = $this->valForKey('Registry Expiry Date');
        }
    }
}
