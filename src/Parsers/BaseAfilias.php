<?php

namespace vWhois\Parsers;

class BaseAfilias extends Base
{
    public function parse()
    {
        parent::parse();

        $this->record->domain = strtolower($this->valForKey('Domain Name'));

        $this->record->registered = !preg_match('/^NOT FOUND/i', $this->record->content);

        if ($this->record->registered) {
            $this->record->domain_id = strtolower($this->valForKey('Registry Domain ID'));

            $this->setStatus()
            ->setCreatedOn()
            ->setUpdatedOn()
            ->setExpiresOn();
        }
    }

    protected function setStatus()
    {
        $statusFound = [];

        foreach ($this->arrayValIfSingle($this->valForKey('Domain Status')) as $status) {
            $statusName = substr($status, 0, strpos($status, ' '));

            if (!in_arrayi($statusName, $statusFound)) {
                $statusFound[] = $statusName;
            }
        }

        $this->record->status = $statusFound;

        return $this;
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
