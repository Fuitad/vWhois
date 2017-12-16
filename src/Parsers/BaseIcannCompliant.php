<?php

namespace vWhois\Parsers;

use vWhois\Record\Registrar;
use vWhois\Record\Nameserver;
use vWhois\Record\Contact;

class BaseIcannCompliant extends Base
{
    protected $contacts = [
        'registrant' => 'Registrant',
        'administrative' => 'Admin',
        'technical' => 'Tech'
    ];

    public function parse()
    {
        parent::parse();

        $this->record->domain = strtolower($this->valForKey('Domain Name'));

        $this->record->registered = !preg_match('/No match for /i', $this->record->content);

        if ($this->record->registered) {
            $this->record->domain_id = strtolower($this->valForKey('Registry Domain ID'));

            $this->setRegistrar();

            foreach ($this->contacts as $type => $kvStart) {
                $contact = new Contact();
                $contact->id = $this->valForKey("Registry ${kvStart} ID");
                $contact->name = $this->valForKey("${kvStart} Name");
                $contact->organization = $this->valForKey("${kvStart} Organization");
                $contact->address = $this->valForKey("${kvStart} Street");

                $contact->city = $this->valForKey("${kvStart} City");
                $contact->state = $this->valForKey("${kvStart} State/Province");
                $contact->zip = $this->valForKey("${kvStart} Postal Code");
                $contact->countryCode = $this->valForKey("${kvStart} Country");
                $contact->phone = $this->valForKey("${kvStart} Phone");
                $contact->fax = $this->valForKey("${kvStart} Fax");
                $contact->email = $this->valForKey("${kvStart} Email");
                $this->record->addContact($contact, $type);
            }

            $this->setStatus()
                ->setCreatedOn()
                ->setUpdatedOn()
                ->setExpiresOn();

            foreach ($this->valForKey('Name Server', []) as $whoisNs) {
                $nameserver = new Nameserver($whoisNs);
                $this->record->addNameserver($nameserver);
            }
        }
    }

    protected function setRegistrar()
    {
        $this->record->registrar = new Registrar(
            $this->firstValIfArray($this->valForKey('Registrar IANA ID')),
            $this->firstValIfArray($this->valForKey('Registrar')),
            $this->firstValIfArray($this->valForKey('Registrar URL'))
        );

        return $this;
    }

    protected function setStatus()
    {
        $statusFound = [];

        foreach ($this->arrayValIfSingle($this->valForKey('Domain Status')) as $status) {
            if (strpos($status, ' ') !== false) {
                $statusName = substr($status, 0, strpos($status, ' '));
            }

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
        $this->record->expiresOn = $this->valForKey('Registrar Registration Expiration Date');

        return $this;
    }
}
