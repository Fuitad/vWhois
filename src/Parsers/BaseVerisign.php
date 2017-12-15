<?php

namespace vWhois\Parsers;

class BaseVerisign extends Base
{
    public function parse()
    {
        parent::parse();

        $this->record->registered = !preg_match('/No match for /i', $this->record->content);

        if ($this->record->registered) {
            $this->record->domain = $this->valForKey('Domain Name');

            $this->record->createdOn = $this->valForKey('Creation Date');

            $this->record->updatedOn = $this->valForKey('Updated Date');

            $this->record->expiresOn = $this->valForKey('Registry Expiry Date');
        }
    }
}
