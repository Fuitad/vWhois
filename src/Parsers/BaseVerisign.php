<?php

namespace vWhois\Parsers;

class BaseVerisign extends BaseIcannCompliant
{
    public function parse()
    {
        parent::parse();

        $this->record->registered = !preg_match('/No match for /i', $this->record->content);

        if ($this->record->registered) {
            $this->record->domain = $this->valForKey('Domain Name');

            $this->setCreatedOn()
                ->setUpdatedOn()
                ->setExpiresOn();
        }
    }
}
