<?php

namespace Anax\Route;

use Anax\Configure\Configuration;
use Anax\DI\DIFactoryConfig;
use PHPUnit\Framework\TestCase;

/**
 * A DI service can not be created when configuration has flaws.
 */
class DiServiceFailTest extends TestCase
{
    /**
     * Di callback throws exception.
     */
    public function testDiCallbackThrowException()
    {
        $this->expectException("\Error");

        $di = new DIFactoryConfig();
        $di->loadServices(ANAX_INSTALL_PATH . "/test/config/di");

        $di->get("request");
    }
}
