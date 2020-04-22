<?php

namespace Anax\Request;

use PHPUnit\Framework\TestCase;

/**
 * Check how Request bahevs when pure cli.
 */
class RequestCliTest extends TestCase
{
    /**
     * Check that init works.
     */
    public function testInit()
    {
        $request = new Request();
        $obj = $request->init();
        $this->assertEquals($request, $obj);
    }
}
