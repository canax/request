<?php

namespace Anax\Request;

use PHPUnit\Framework\TestCase;

/**
 * Fail testing of Request.
 */
class RequestFailTest extends TestCase
{
    /**
     * Properties
     */
    private $request;



    /**
     * Set up a request object
     *
     * @return void
     */
    public function setUp() : void
    {
        $this->request = new Request();
    }



    /**
     * Send incorrect JSON as part of body.
     */
    public function testGetBodyAsJson()
    {
        $this->expectException("\JsonException");

        $exp = "[1 2]";
        $this->request->setBody($exp);
        $this->request->getBodyAsJson();
    }
}
