<?php

namespace vWhois\Parsers;

class Whoistucowscom extends BaseIcannCompliant
{
    public function parse()
    {
        parent::parse();

        if ($this->record->registered) {
            $this->record->domain_id = strtolower($this->valForKey('Domain ID'));
            $this->record->reseller = strtolower($this->valForKey('Reseller'));
        }
    }

    protected function setExpiresOn()
    {
        $this->record->expiresOn = $this->valForKey('Registrar Registration Expiration Date');

        return $this;
    }
}
