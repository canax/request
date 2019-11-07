<?php

namespace Anax\Request;

use PHPUnit\Framework\TestCase;

/**
 * Test getting and setting globals in a request.
 */
class RequestGlobalsTest extends TestCase
{
    /**
     * Set and get GET values.
     */
    public function testSetGetGet()
    {
        // Create and set empty by default
        $request = new Request();
        $request->unsetGlobals();

        // Its empty by default
        $res = $request->getGet("nothing");
        $this->assertEmpty($res);

        // Get whole array, empty by default
        $res = $request->getGet();
        $this->assertEmpty($res);
        $this->assertIsArray($res);

        // Set a plain value and retrieve it
        $key = "somekey";
        $value = "somevalue";
        $request->setGet($key, $value);
        $res = $request->getGet($key);
        $this->assertEquals($res, $value);

        // Check if value exists or not
        $res = $request->hasGet($key);
        $this->assertTrue($res);
        $res = $request->hasGet("nada");
        $this->assertFalse($res);

        // Set a value using an array and retrieve it
        $array = [
            "key" => "value",
            "key1" => "value1",
        ];
        $request->setGet($array);

        $res = $request->getGet("key");
        $this->assertEquals($res, "value");

        $res = $request->getGet("key1");
        $this->assertEquals($res, "value1");
    }



    /**
     * Set and get POST values.
     */
    public function testSetGetPost()
    {
        // Create and set empty by default
        $request = new Request();
        $request->unsetGlobals();

        // Its empty by default
        $res = $request->getPost("nothing");
        $this->assertEmpty($res);

        // Get whole array, empty by default
        $res = $request->getPost();
        $this->assertEmpty($res);
        $this->assertIsArray($res);

        // Set a plain value and retrieve it
        $key = "somekey";
        $value = "somevalue";
        $request->setPost($key, $value);
        $res = $request->getPost($key);
        $this->assertEquals($res, $value);

        // Set a value using an array and retrieve it
        $array = [
            "key" => "value",
            "key1" => "value1",
        ];
        $request->setPost($array);

        $res = $request->getPost("key");
        $this->assertEquals($res, "value");

        $res = $request->getPost("key1");
        $this->assertEquals($res, "value1");
    }



    /**
     * Set and get SERVER values.
     */
    public function testSetGetServer()
    {
        // Create and set empty by default
        $request = new Request();
        $request->unsetGlobals();

        // Its empty by default
        $res = $request->getServer("nothing");
        $this->assertEmpty($res);

        // Get whole array, empty by default
        $res = $request->getServer();
        $this->assertEmpty($res);
        $this->assertIsArray($res);

        // Set a plain value and retrieve it
        $key = "somekey";
        $value = "somevalue";
        $request->setServer($key, $value);
        $res = $request->getServer($key);
        $this->assertEquals($res, $value);

        // Set a value using an array and retrieve it
        $array = [
            "key" => "value",
            "key1" => "value1",
        ];
        $request->setServer($array);

        $res = $request->getServer("key");
        $this->assertEquals($res, "value");

        $res = $request->getServer("key1");
        $this->assertEquals($res, "value1");
    }



    /**
     * Set and get GET, POST, SERVER values.
     */
    public function testSetGetGlobals()
    {
        // Create and set empty by default
        $request = new Request();
        $request->unsetGlobals();

        // Set up globals
        $array = [
            "key1" => "keyval1",
            "key2" => "keyval2",
        ];

        $request->setGlobals([
            "server" => $array,
            "get" => $array,
            "post" => $array,
        ]);

        // Check them
        $res = $request->getServer();
        $this->assertIsArray($res);
        $this->assertEquals($res["key1"], "keyval1");
        $this->assertEquals($res["key2"], "keyval2");

        $res = $request->getPost();
        $this->assertIsArray($res);
        $this->assertEquals($res["key1"], "keyval1");
        $this->assertEquals($res["key2"], "keyval2");

        $res = $request->getGet();
        $this->assertIsArray($res);
        $this->assertEquals($res["key1"], "keyval1");
        $this->assertEquals($res["key2"], "keyval2");
    }
}
