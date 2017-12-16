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

            $this->setCreatedOn()
                ->setUpdatedOn()
                ->setExpiresOn();
        }
    }

    protected function setCreatedOn()
    {
        $this->record->createdOn = $this->firstValIfArray($this->valForKey('Creation Date'));

        return $this;
    }

    protected function setUpdatedOn()
    {
        $this->record->updatedOn = $this->firstValIfArray($this->valForKey('Updated Date'));

        return $this;
    }

    protected function setExpiresOn()
    {
        $this->record->expiresOn = $this->valForKey('Registry Expiry Date');

        return $this;
    }
}
