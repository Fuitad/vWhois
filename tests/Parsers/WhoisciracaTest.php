<?php

namespace Tests\Parsers;

use Tests\BaseTestCase;

class Whoisciraca extends BaseTestCase
{
    protected $whoisResult;

    public function setup()
    {
        parent::setup();

        $this->whoisResult = $this->vwhois->lookup('cira.ca');
    }

    /** @test */
    public function parser_works_for_cira_dot_ca()
    {
        // Domain is correctly parsed
        $this->assertEquals('cira.ca', $this->whoisResult->domain);

        // Domain ID is correctly parsed
        $this->assertEquals('', $this->whoisResult->domain_id);

        // is not available
        $this->assertEquals(false, $this->whoisResult->available);

        // registar name is set
        $this->assertEquals('Please contact CIRA at 1-877-860-1411 for more information', $this->whoisResult->registrar->name);

        // created on 1998-02-05 00:00:00
        $this->assertEquals('1998-02-05 00:00:00', $this->whoisResult->createdOn->toDateTimeString());

        // updated on 2017-07-26 00:00:00
        $this->assertEquals('2017-07-26 00:00:00', $this->whoisResult->updatedOn->toDateTimeString());

        // expires on 2050-02-05 00:00:00
        $this->assertEquals('2050-02-05 00:00:00', $this->whoisResult->expiresOn->toDateTimeString());
    }
}
