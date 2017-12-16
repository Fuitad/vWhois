<?php

namespace vWhois\Record;

class Registrar
{
    public $id;
    public $name;
    public $url;

    public function __construct($id = '', $name = '', $url = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
    }

    public function __toString()
    {
        return $this->name;
    }
}
