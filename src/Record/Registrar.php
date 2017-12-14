<?php

namespace vWhois\Record;

class Registrar
{
    public $id;
    public $name;
    public $organization;
    public $url;

    public function __construct($id = '', $name = '', $organization = '', $url = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->organization = $organization;
        $this->url = $url;
    }

    public function __toString()
    {
        return $this->name;
    }
}
