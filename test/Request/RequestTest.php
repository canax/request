<?php

namespace Anax\Request;

use PHPUnit\Framework\TestCase;

/**
 * Storing information from the request and calculating related essentials.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RequestTest extends TestCase
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
        $this->request->setGlobals(
            [
                'server' => [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "80",
                    'REQUEST_URI' => "/anax-mvc/webroot/app.php",
                    'SCRIPT_NAME' => "/anax-mvc/webroot/app.php",
                ]
            ]
        );
    }



    /**
     * Provider for routes
     *
     * @return array
     */
    public function providerRoute()
    {
        return [
            [""],
            ["controller"],
            ["controller/action"],
            ["controller/action/arg1"],
            ["controller/action/arg1/arg2"],
            ["controller/action/arg1/arg2/arg3"],
        ];
    }



    /**
     * Test
     *
     * @param string $route the route part
     *
     * @return void
     *
     * @dataProvider providerRoute
     */
    public function testGetRoute($route)
    {
        $uri = $this->request->getServer('REQUEST_URI');
        //$this->assertEmpty($uri, "REQUEST_URI is empty.");

        $this->request->setServer('REQUEST_URI', $uri . '/' . $route);
        $this->request->init();

        $this->assertEquals($route, $this->request->extractRoute(), "Failed extractRoute: " . $route);
        $this->assertEquals($route, $this->request->getRoute(), "Failed getRoute: " . $route);
    }



    /**
     * Provider for $_SERVER
     *
     * @return array
     */
    public function providerGetCurrentUrl()
    {
        return [
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "80",
                    'REQUEST_URI' => "/",
                    'url'         => "http://dbwebb.se",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "80",
                    'REQUEST_URI' => "/img",
                    'url'         => "http://dbwebb.se/img",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "80",
                    'REQUEST_URI' => "/img/",
                    'url'         => "http://dbwebb.se/img",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "80",
                    'REQUEST_URI' => "/anax-mvc/webroot/app.php",
                    'url'         => "http://dbwebb.se/anax-mvc/webroot/app.php",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "8080",
                    'REQUEST_URI' => "/anax-mvc/webroot/app.php",
                    'url'         => "http://dbwebb.se:8080/anax-mvc/webroot/app.php",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "8080",
                    'REQUEST_URI' => "/anax-mvc/webroot/%31.php",
                    'url'         => "http://dbwebb.se:8080/anax-mvc/webroot/1.php",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "https",
                    'HTTPS'       => "on", //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "443",
                    'REQUEST_URI' => "/anax-mvc/webroot/app.php",
                    'url'         => "https://dbwebb.se/anax-mvc/webroot/app.php",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "https",
                    'HTTPS'       => "on", //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "8080",
                    'REQUEST_URI' => "/anax-mvc/webroot/app.php",
                    'url'         => "https://dbwebb.se:8080/anax-mvc/webroot/app.php",
                ]
            ],
        ];
    }



    /**
     * Test
     *
     * @param string $server the $_SERVER part
     *
     * @return void
     *
     * @dataProvider providerGetCurrentUrl
     *
     */
    public function testGetCurrentUrl($server)
    {
        $this->request->setServer('REQUEST_SCHEME', $server['REQUEST_SCHEME']);
        $this->request->setServer('HTTPS', $server['HTTPS']);
        $this->request->setServer('SERVER_NAME', $server['SERVER_NAME']);
        $this->request->setServer('SERVER_PORT', $server['SERVER_PORT']);
        $this->request->setServer('REQUEST_URI', $server['REQUEST_URI']);

        $url = $server['url'];

        $res = $this->request->getCurrentUrl();

        $this->assertEquals($url, $res, "Failed url: " . $url);
    }


    /**
     * Provider for $_SERVER
     *
     * @return array
     */
    public function providerGetCurrentUrlNoServerName()
    {
        return [
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'HTTP_HOST'   => "webdev.dbwebb.se",
                    'SERVER_PORT' => "80",
                    'REQUEST_URI' => "/",
                    'url'         => "http://dbwebb.se",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "",
                    'HTTP_HOST'   => "webdev.dbwebb.se",
                    'SERVER_PORT' => "80",
                    'REQUEST_URI' => "/img",
                    'url'         => "http://webdev.dbwebb.se/img",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
//                    'SERVER_NAME' => "",
                    'HTTP_HOST'   => "dbwebb.se",
                    'SERVER_PORT' => "80",
                    'REQUEST_URI' => "/img/",
                    'url'         => "http://dbwebb.se/img",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "",
                    'HTTP_HOST'   => "dbwebb.se",
                    'SERVER_PORT' => "80",
                    'REQUEST_URI' => "/anax-mvc/webroot/app.php",
                    'url'         => "http://dbwebb.se/anax-mvc/webroot/app.php",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "",
                    'HTTP_HOST'   => "dbwebb.se",
                    'SERVER_PORT' => "8080",
                    'REQUEST_URI' => "/anax-mvc/webroot/app.php",
                    'url'         => "http://dbwebb.se:8080/anax-mvc/webroot/app.php",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "https",
                    'HTTPS'       => "on", //"on",
                    'SERVER_NAME' => "",
                    'HTTP_HOST'   => "dbwebb.se",
                    'SERVER_PORT' => "443",
                    'REQUEST_URI' => "/anax-mvc/webroot/app.php",
                    'url'         => "https://dbwebb.se/anax-mvc/webroot/app.php",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "https",
                    'HTTPS'       => "on", //"on",
                    'SERVER_NAME' => "",
                    'HTTP_HOST'   => "dbwebb.se",
                    'SERVER_PORT' => "8080",
                    'REQUEST_URI' => "/anax-mvc/webroot/app.php",
                    'url'         => "https://dbwebb.se:8080/anax-mvc/webroot/app.php",
                ]
            ],
        ];
    }



    /**
     * Test
     *
     * @param string $server the $_SERVER part
     *
     * @return void
     *
     * @dataProvider providerGetCurrentUrlNoServerName
     *
     */
    public function testGetCurrentUrlNoServerName($server)
    {
        $fakeGlobal = ['server' => $server];

        $this->request->setGlobals($fakeGlobal);

        $url = $fakeGlobal['server']['url'];

        $res = $this->request->getCurrentUrl();

        $this->assertEquals($url, $res, "Failed url: " . $url);
    }


    /**
     * Provider for $_SERVER
     *
     * @return array
     */
    public function providerInit()
    {
        return [
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "80",
                    'REQUEST_URI' => "/anax-mvc/webroot/app.php",
                    'SCRIPT_NAME' => "/anax-mvc/webroot/app.php",
                    'siteUrl'     => "http://dbwebb.se",
                    'baseUrl'     => "http://dbwebb.se/anax-mvc/webroot",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "8080",
                    'REQUEST_URI' => "/anax-mvc/webroot/app.php",
                    'SCRIPT_NAME' => "/anax-mvc/webroot/app.php",
                    'siteUrl'     => "http://dbwebb.se:8080",
                    'baseUrl'     => "http://dbwebb.se:8080/anax-mvc/webroot",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "https",
                    'HTTPS'       => "on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "8080",
                    'REQUEST_URI' => "/anax-mvc/webroot/app.php",
                    'SCRIPT_NAME' => "/anax-mvc/webroot/app.php",
                    'siteUrl'     => "https://dbwebb.se:8080",
                    'baseUrl'     => "https://dbwebb.se:8080/anax-mvc/webroot",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "https",
                    'HTTPS'       => "on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "443",
                    'REQUEST_URI' => "/anax-mvc/webroot/app.php",
                    'SCRIPT_NAME' => "/anax-mvc/webroot/app.php",
                    'siteUrl'     => "https://dbwebb.se",
                    'baseUrl'     => "https://dbwebb.se/anax-mvc/webroot",
                ]
            ]
        ];
    }



    /**
     * Test
     *
     * @param string $server the route part
     *
     * @dataProvider providerInit
     *
     */
    public function testInit($server)
    {
        $this->request->setServer('REQUEST_SCHEME', $server['REQUEST_SCHEME']);
        $this->request->setServer('HTTPS', $server['HTTPS']);
        $this->request->setServer('SERVER_NAME', $server['SERVER_NAME']);
        $this->request->setServer('SERVER_PORT', $server['SERVER_PORT']);
        $this->request->setServer('REQUEST_URI', $server['REQUEST_URI']);
        $this->request->setServer('SCRIPT_NAME', $server['SCRIPT_NAME']);

        $siteUrl = $server['siteUrl'];
        $baseUrl = $server['baseUrl'];

        $res = $this->request->init();
        $this->assertInstanceOf(get_class($this->request), $res, "Init did not return this.");

        $this->assertEquals($siteUrl, $this->request->getSiteUrl(), "Failed siteurl: " . $siteUrl);
        $this->assertEquals($baseUrl, $this->request->getBaseUrl(), "Failed baseurl: " . $baseUrl);
    }



    /**
     * Check that the HTTP method can be retrieved.
     */
    public function testRequestMethod()
    {
        // Initial value
        $exp = null;
        $res = $this->request->getScriptName();
        $this->assertEquals($exp, $res);

        // Set and get the method
        $exp = "GET";
        $this->request->setServer("REQUEST_METHOD", $exp);
        $res = $this->request->getMethod();
        $this->assertEquals($exp, $res);
    }



    /**
     * Provider for $_SERVER to get siteurl and baseurl.
     */
    public function providerSiteBaseUrl()
    {
        return [
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "80",
                    'REQUEST_URI' => "/install_dir/htdocs/index.php",
                    'SCRIPT_NAME' => "/install_dir/htdocs/index.php",
                    'siteUrl'     => "http://dbwebb.se",
                    'baseUrl'     => "http://dbwebb.se/install_dir/htdocs",
                ]
            ],
            [
                [
                    'REQUEST_SCHEME' => "http",
                    'HTTPS'       => null, //"on",
                    'SERVER_NAME' => "dbwebb.se",
                    'SERVER_PORT' => "80",
                    'REQUEST_URI' => "/index.php",
                    'SCRIPT_NAME' => "/index.php",
                    'siteUrl'     => "http://dbwebb.se",
                    'baseUrl'     => "http://dbwebb.se",
                ]
            ],
        ];
    }



    /**
     * Check that the baseurl is created.
     * @dataProvider providerInit
     */
    public function testGetBaseUrls($server)
    {
        $this->request->setServer('REQUEST_SCHEME', $server['REQUEST_SCHEME']);
        $this->request->setServer('HTTPS', $server['HTTPS']);
        $this->request->setServer('SERVER_NAME', $server['SERVER_NAME']);
        $this->request->setServer('SERVER_PORT', $server['SERVER_PORT']);
        $this->request->setServer('REQUEST_URI', $server['REQUEST_URI']);
        $this->request->setServer('SCRIPT_NAME', $server['SCRIPT_NAME']);

        $baseUrl = $server['baseUrl'];

        $res = $this->request->init();

        $exp = $server['siteUrl'];
        $res = $this->request->getSiteUrl();
        $this->assertEquals($exp, $res);

        $exp = $server['baseUrl'];
        $res = $this->request->getBaseUrl();
        $this->assertEquals($exp, $res);
    }



    /**
     * Check that the siteurl and baseurl is created.
     */
    public function testEmptyGetSiteBaseUrl()
    {
        $request = new Request();

        // Initial value
        $exp = null;
        $res = $request->getBaseUrl();
        $this->assertEquals($exp, $res);

        $exp = null;
        $res = $request->getSiteUrl();
        $this->assertEquals($exp, $res);

        // Value after empty init
        $request->init();
        $exp = null;
        $res = $request->getBaseUrl();
        $this->assertEquals($exp, $res);

        $exp = null;
        $res = $request->getSiteUrl();
        $this->assertEquals($exp, $res);
    }



    /**
     * Check that the script name can be retrieved.
     */
    public function testGetScriptName()
    {
        // Initial value
        $exp = null;
        $res = $this->request->getScriptName();
        $this->assertEquals($exp, $res);

        // Set and get the name
        $res = "index.php";
        $this->request->setServer("SCRIPT_NAME", $exp);
        $res = $this->request->getScriptName();
        $this->assertEquals($exp, $res);
    }



    /**
     * Check that the route parts can be retrieved.
     */
    public function testGetRouteParts()
    {
        // Initial value
        $exp = null;
        $res = $this->request->getRouteParts();
        $this->assertEquals($exp, $res);

        // Test the empty route
        $this->request->setServer("REQUEST_URI", "");
        $this->request->init();
        $exp = [""];
        $res = $this->request->getRouteParts();
        $this->assertEquals($exp, $res);

        // Test another route
        $this->request->setServer("REQUEST_URI", "some/route");
        $this->request->init();
        $exp = ["some", "route"];
        $res = $this->request->getRouteParts();
        $this->assertEquals($exp, $res);

        // Test route with querystring
        $this->request->setServer("REQUEST_URI", "some/route?arg=1&arg2");
        $this->request->init();
        $exp = ["some", "route"];
        $res = $this->request->getRouteParts();
        $this->assertEquals($exp, $res);
    }



    /**
     * Check that the body can be set/get.
     */
    public function testSetAndGetBody()
    {
        // Initial value
        $exp = null;
        $res = $this->request->getBody();
        $this->assertEquals($exp, $res);

        // Set and get body
        $exp = "body";
        $this->request->setBody($exp);
        $res = $this->request->getBody();
        $this->assertEquals($exp, $res);
    }



    /**
     * Get the body as JSON.
     */
    public function testGetBodyAsJson()
    {
        // Initial value
        $exp = null;
        $res = $this->request->getBodyAsJson();
        $this->assertEquals($exp, $res);

        // Set and get body
        $exp = "[1]";
        $this->request->setBody($exp);
        $res = $this->request->getBodyAsJson();
        $this->assertEquals(json_decode($exp), $res);
    }
}
