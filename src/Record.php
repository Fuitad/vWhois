<?php

namespace vWhois;

use Carbon\Carbon;
use vWhois\Record\Nameserver;
use vWhois\Record\Contact;

class Record
{
    protected $properties = [
        'domain' => '',
        'domain_id' => '',
        'available' => '',
        'reseller' => '',
        'registered' => '',
        'createdOn' => '',
        'updatedOn' => '',
        'expiresOn' => '',
        'registrar' => '',
        'status' => [],
        'nameservers' => [],
        'contacts' => [
            'registrant' => null,
            'administrative' => null,
            'technical' => null
        ],
    ];

    protected $casts = [
        'available' => 'bool',
        'registered' => 'bool',
        'createdOn' => 'datetime',
        'updatedOn' => 'datetime',
        'expiresOn' => 'datetime'
    ];

    /**
     * @var bool
     */
    public $incomplete;

    /**
     * @var bool
     */
    public $throttled;

    /**
     * @var bool
     */
    public $unavailable;

    /**
     * @var string
     */
    public $content;

    public function __get($key)
    {
        return array_get($this->properties, $key, null);
    }

    public function __set($key, $value)
    {
        if (!array_key_exists($key, $this->properties)) {
            return false;
        }

        if (!$value) {
            $this->properties[$key] = $value;
        } else {
            switch (array_get($this->casts, $key, null)) {
                case 'bool':
                    $this->properties[$key] = (bool)$value;
                    break;
                case 'datetime':
                    $this->properties[$key] = Carbon::parse($value);
                    break;
                default:
                    $this->properties[$key] = $value;
            }
        }

        if ($key === 'registered') {
            $this->properties['available'] = !(bool)$value;
        } elseif ($key === 'available') {
            $this->properties['registered'] = !(bool)$value;
        }
    }

    public function addNameserver(Nameserver $nameserver)
    {
        $this->properties['nameservers'][] = $nameserver;
    }

    public function addContact(Contact $contact, $type)
    {
        if (!array_key_exists($type, $this->properties['contacts'])) {
            return false;
        }

        $contact->type = $type;

        $this->properties['contacts'][$type] = $contact;
    }

    public function toArray()
    {
        return array_merge(
            [
                'response' => [
                    'incomplete' => $this->incomplete,
                    'throttled' => $this->throttled,
                    'unavailable' => $this->unavailable,
                    'content' => $this->content
                ]
            ],
            $this->properties
        );
    }
}
