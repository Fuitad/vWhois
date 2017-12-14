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

        $this->record->registered = !preg_match('/No match for /i', $this->record->raw);

        if ($this->record->registered) {
            $this->record->domain_id = strtolower($this->valForKey('Registry Domain ID'));

            $this->record->registrar = new Registrar(
                $this->valForKey('Registrar IANA ID'),
                $this->valForKey('Registrar'),
                $this->valForKey('Registrar'),
                $this->valForKey('Registrar URL')
            );

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

            $this->record->status = $this->valForKey('Domain Status');

            $this->record->createdOn = $this->valForKey('Creation Date');

            $this->record->updatedOn = $this->valForKey('Updated Date');

            $this->record->expiresOn = $this->valForKey('Registrar Registration Expiration Date');

            foreach ($this->valForKey('Name Server') as $whoisNs) {
                $nameserver = new Nameserver($whoisNs);
                $this->record->addNameserver($nameserver);
            }
        }
    }
}
