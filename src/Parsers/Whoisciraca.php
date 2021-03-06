<?php

namespace vWhois\Parsers;

use vWhois\Record\Registrar;
use vWhois\Record\Nameserver;
use vWhois\Record\Contact;

class Whoisciraca extends Base
{
    protected $contacts = [
        'registrant' => 'Registrant:',
        'administrative' => 'Administrative contact:',
        'technical' => 'Technical contact:'
    ];

    protected $registredStatus = [
        'registered',
        'redemption',
        'auto-renew grace',
        'to be released',
        'pending delete',
        'unavailable'
    ];

    public function parse()
    {
        parent::parse();

        $this->record->domain = strtolower($this->valForKey('Domain name'));

        $this->record->registered = in_array($this->valForKey('Domain status'), $this->registredStatus);

        if ($this->record->registered) {
            $whoisRegistrarBlock = $this->findBlock('Registrar:', '\n\n', false);

            $this->record->registrar = new Registrar(
                 array_get($whoisRegistrarBlock, 'Number'),
                 array_get($whoisRegistrarBlock, 'Name')
            );

            foreach ($this->contacts as $type => $heading) {
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

                    /*
                     * TODO
                     *
                     * There's a bug in this section with Cira. example, bnc.ca returns
                     * fax: "Email:             dns@bnc.ca"
                     * and nothing for email
                     */
                    $contact->fax = array_get($contactBlock, 'Fax');
                    $contact->email = array_get($contactBlock, 'Email');
                    $this->record->addContact($contact, $type);
                }
            }

            $this->record->createdOn = $this->valForKey('Creation date');

            $this->record->updatedOn = $this->valForKey('Updated date');

            $this->record->expiresOn = $this->valForKey('Expiry date');

            $whoisNameserversBlock = $this->findBlock('Name servers:', '\n\n', true);

            if ($whoisNameserversBlock) {
                $whoisNameservers = explode("\n", trim($whoisNameserversBlock));
                foreach ($whoisNameservers as $whoisNs) {
                    $whoisNs = trim($whoisNs);

                    if (!preg_match('/^(?<nameserver>.*?)(\s+(?<ipv4>.*?))?(\s+(?<ipv6>.*?))?$/', $whoisNs, $nsBlocks)) {
                        continue;
                    }

                    $nameserver = new Nameserver(
                        array_get($nsBlocks, 'nameserver'),
                        array_get($nsBlocks, 'ipv4'),
                        array_get($nsBlocks, 'ipv6')
                    );

                    $this->record->addNameserver($nameserver);
                }
            }
        }
    }
}
