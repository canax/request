<?php

namespace Anax\Request;

use Anax\DI\DIFactoryConfig;
use PHPUnit\Framework\TestCase;

/**
 * Try out the constructs used in the README-file.
 */
class RequestReadmeTest extends TestCase
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
     * Check that the module exception can be thrown.
     */
    public function testRequestException()
    {
        $this->expectException("Anax\Request\Exception");
        throw new Exception("Module exception");
    }



    /**
     * Create the service.
     *
     * @SuppressWarnings(PHPMD.MissingImport)
     */
    public function testCreateDiService()
    {
        $config = [
            // Services to add to the container.
            "services" => [
                "request" => [
                    "shared" => true,
                    "callback" => function () {
                        $obj = new \Anax\Request\Request();
                        $obj->init();
                        return $obj;
                    }
                ],
            ],
        ];

        $di = new DIFactoryConfig();
        $di->loadServices($config);

        $request = $di->get("request");
        $this->assertInstanceOf(Request::class, $request);
    }



    /**
     * Check general methods exists.
     */
    public function testCheckMethodsExists()
    {
        $request = new Request();
        $this->assertTrue(method_exists($request, "getRoute"));
        $this->assertTrue(method_exists($request, "getMethod"));
        $this->assertTrue(method_exists($request, "getRoute"));
        $this->assertTrue(method_exists($request, "setGlobals"));
        $this->assertTrue(method_exists($request, "init"));

        $this->assertTrue(method_exists($request, "getSiteUrl"));
        $this->assertTrue(method_exists($request, "getBaseUrl"));
        $this->assertTrue(method_exists($request, "getCurrentUrl"));
        $this->assertTrue(method_exists($request, "getScriptName"));
        $this->assertTrue(method_exists($request, "getMethod"));
        $this->assertTrue(method_exists($request, "getRoute"));
        $this->assertTrue(method_exists($request, "getRouteParts"));
    }



    /**
     * Get and set $_SERVER.
     */
    public function testGetSetServer()
    {
        $request = new Request();

        // Get all key value pairs
        $res = $request->getServer();
        $this->assertEquals($_SERVER, $res);

        $key = "key";
        $value = "value";
        $default = "default";
        $request->setServer($key, $value);

        // Get value from key
        $res = $request->getServer($key);
        $this->assertEquals($value, $res);

        // Get value from key, with default
        $res = $request->getServer($key, $default);
        $this->assertEquals($value, $res);

        // Get value from no existing key, with default
        $res = $request->getServer("NO KEY", $default);
        $this->assertEquals($default, $res);
    }


    /**
     * Get and set $_GET.
     */
    public function testGetSetGet()
    {
        $request = new Request();

        $key = "key";
        $value = "value";
        $default = "default";
        $request->setGet($key, $value);

        // Get value from key
        $res = $request->getGet($key);
        $this->assertEquals($value, $res);

        // Get all key value pairs
        $res = $request->getGet();
        $this->assertEquals([$key => $value], $res);

        // Get value from key, with default
        $res = $request->getGet($key, $default);
        $this->assertEquals($value, $res);

        // Get value from no existing key, with default
        $res = $request->getGet("NO KEY", $default);
        $this->assertEquals($default, $res);
    }



    /**
     * Get and set $_POST.
     */
    public function testGetSetPost()
    {
        $request = new Request();

        $key = "key";
        $value = "value";
        $default = "default";
        $request->setPost($key, $value);

        // Get value from key
        $res = $request->getPost($key);
        $this->assertEquals($value, $res);

        // Get all key value pairs
        $res = $request->getPost();
        $this->assertEquals([$key => $value], $res);

        // Get value from key, with default
        $res = $request->getPost($key, $default);
        $this->assertEquals($value, $res);

        // Get value from no existing key, with default
        $res = $request->getPost("NO KEY", $default);
        $this->assertEquals($default, $res);
    }


    /**
     * Get and set body.
     */
    public function testGetSetBody()
    {
        $request = new Request();

        $body = "[1]";
        $request->setBody($body);

        // Get value from body
        $res = $request->getBody();
        $this->assertEquals($body, $res);

        // Get json value from body
        $res = $request->getBodyAsJson();
        $exp = json_decode($body);
        $this->assertEquals($exp, $res);
    }
}
