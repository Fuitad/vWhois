<?php

namespace Tests\Parsers;

use Tests\BaseTestCase;

class Whoisenomcom extends BaseTestCase
{
    protected $whoisResult;

    public function setup()
    {
        parent::setup();

        $this->whoisResult = $this->vwhois->lookup('enom.com');
    }

    /** @test */
    public function parser_works_for_enom_dot_com()
    {
        // Domain is correctly parsed
        $this->assertEquals('enom.com', $this->whoisResult->domain);

        // Domain ID is correctly parsed
        $this->assertEquals('3066175_domain_com-vrsn', $this->whoisResult->domain_id);

        // is not available
        $this->assertEquals(false, $this->whoisResult->available);
    }
}
