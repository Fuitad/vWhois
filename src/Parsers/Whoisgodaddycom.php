<?php

namespace vWhois\Parsers;

class Whoisgodaddycom extends BaseIcannCompliant
{
    public function parse()
    {
        parent::parse();

        if ($this->record->registered) {
            $this->record->expiresOn = $this->valForKey('Registrar Registration Expiration Date');
        }
    }
}
