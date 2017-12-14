<?php

namespace vWhois\Parsers;

class Whoismarkmonitorcom extends BaseIcannCompliant
{
    public function parse()
    {
        parent::parse();

        if ($this->record->registered) {
            $this->record->expiresOn = $this->valForKey('Registry Expiry Date');
        }
    }
}
