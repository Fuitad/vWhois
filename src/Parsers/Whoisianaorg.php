<?php

namespace vWhois\Parsers;

use vWhois\Record\Nameserver;

class Whoisianaorg extends Base
{
    protected $contacts = [
        'administrative' => 'contact:\s+administrative',
        'technical' => 'contact:\s+technical'
    ];

    public function parse()
    {
        parent::parse();

        $this->record->domain = strtolower($this->valForKey('domain'));

        $this->record->registered = (strtolower($this->valForKey('status')) === 'active');

        if ($this->record->registered) {
            //foreach ($this->contacts AS $type => $heading) {
            //$contact = $this->findBlock($heading);

            //if ($contact) {

            //}
            $this->record->createdOn = $this->valForKey('created');

            $this->record->updatedOn = $this->valForKey('changed');

            foreach ($this->valForKey('nserver') as $whoisNs) {
                list($server, $ipv4, $ipv6) = explode(' ', $whoisNs);
                $nameserver = new Nameserver($server, $ipv4, $ipv6);
                $this->record->addNameserver($nameserver);
            }
        }
    }
}
