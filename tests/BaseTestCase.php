<?php

namespace Tests;

use vWhois\vWhois;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    protected $vwhois;

    public function __constuct()
    {
        parent::__construct();

        $this->setUp();
    }

    protected function setUp()
    {
        $this->vwhois = new vWhois();
    }
}
