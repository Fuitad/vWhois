<?php

use vWhois\vWhois;

class vWhoisTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function tld_properly_routed()
    {
        // given that I have a WhoisClient object
        $vWhois = new vWhois();

        // and that I prepare a lookup to .com
        $vWhois->prepareForLookup('.com');

        // the adapter returned should be Standard
        $this->assertEquals('vWhois\Adapters\Standard', get_class($vWhois->getAdapter()));
    }

    /** @test */
    public function google_dot_com_tld_properly_routed()
    {
        // given that I have a vWhois object
        $vWhois = new vWhois();

        // and that I prepare a lookup to google.com
        $vWhois->prepareForLookup('google.com');

        // the adapter returned should be Verisign
        $this->assertEquals('vWhois\Adapters\Verisign', get_class($vWhois->getAdapter()));
    }

    /** @test */
    public function google_dot_co_dot_uk_tld_properly_routed()
    {
        // given that I have a vWhois object
        $vWhois = new vWhois();

        // and that I prepare a lookup to google.co.uk
        $vWhois->prepareForLookup('google.co.uk');

        // the adapter returned should be Standard
        $this->assertEquals('vWhois\Adapters\Standard', get_class($vWhois->getAdapter()));
    }
}
