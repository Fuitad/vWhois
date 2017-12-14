<?php

namespace vWhois\Parsers;

use vWhois\Record\Registrar;
use vWhois\Record\Nameserver;
use vWhois\Record\Contact;

class Whoisnicuk extends Base
{
    protected $contacts = [
        'registrant' => 'Registrant:',
        'administrative' => 'Administrative contact:',
        'technical' => 'Technical contact:'
    ];

    protected $registredStatus = [
        'registered until expiry date.',
        'registration request being processed.',
        'renewal request being processed.',
        'no longer required',
        'no registration status listed.',
        'renewal required.'
    ];

    public function parse()
    {
        parent::parse();

        if (!$this->valForKey('Registration status')) {
            $this->record->registered = false;
        } else {
            $this->record->registered = in_array(strtolower($this->valForKey('Registration status')), $this->registredStatus);
        }

        if ($this->record->registered) {
            $this->record->domain = strtolower($this->valForKey('Domain name'));

            $this->record->registrar = new Registrar(
                '',
                $this->valForKey('Registrar'),
                $this->valForKey('Registrar'),
                $this->valForKey('URL')
            );

            $contact = new Contact();
            $contact->name = $this->valForKey('Registrant');
            $this->record->addContact($contact, 'registrant');

            /*foreach ($this->contacts as $type => $heading) {
                $contactBlock = $this->findBlock($heading);

                if ($contactBlock) {
                    $contact = new Contact();
                    $contact->name = array_get($contactBlock, 'Name');
                    $contact->organization = array_get($contactBlock, 'Organization');
                    $contact->address = array_get($contactBlock, 'Street');

                    $contact->city = array_get($contactBlock, 'City');
                    $contact->state = array_get($contactBlock, 'State/Province');
                    $contact->zip = array_get($contactBlock, 'Postal Code');
                    $contact->countryCode = array_get($contactBlock, 'Country');
                    $contact->phone = array_get($contactBlock, 'Phone');
                    $contact->fax = array_get($contactBlock, 'Fax');
                    $contact->email = array_get($contactBlock, 'Email');
                    $this->record->addContact($contact, $type);
                }
            }*/

            $this->record->createdOn = $this->valForKey('Registered on');

            $this->record->updatedOn = $this->valForKey('Last updated');

            $this->record->expiresOn = $this->valForKey('Expiry date');

            $whoisNameserversBlock = $this->findBlock('Name servers:', '\n\n', true);

            if ($whoisNameserversBlock) {
                $whoisNameservers = explode("\n", trim($whoisNameserversBlock));
                foreach ($whoisNameservers as $whoisNs) {
                    $nameserver = new Nameserver(trim($whoisNs));
                    $this->record->addNameserver($nameserver);
                }
            }
        }
    }
}
