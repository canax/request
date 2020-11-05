<?php

namespace Anax\Request;

use Anax\Configure\Configuration;
use Anax\DI\DIFactoryConfig;
use PHPUnit\Framework\TestCase;

/**
 * Try that a DI service can be created from the di config file.
 */
class DiServiceTest extends TestCase
{
    /**
     * Create the service from default config file.
     */
    public function testCreateDiService()
    {
        $di = new DIFactoryConfig();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");

        $request = $di->get("request");
        $this->assertInstanceOf(Request::class, $request);
    }
}
