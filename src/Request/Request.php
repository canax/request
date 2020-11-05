<?php

namespace Anax\Request;

/**
 * Storing information from the request and calculating related essentials.
 *
 */
class Request
{
    /**
     * @var string $requestUri Request URI from $_SERVER.
     * @var string $scriptName Scriptname from $_SERVER, actual scriptname part.
     * @var string $path       Scriptname from $_SERVER, path-part.
     */
    private $requestUri;
    private $scriptName;
    private $path;



    /**
     * @var string $route      The route.
     * @var array  $routeParts The route as an array.
     */
    private $route;
    private $routeParts;



    /**
     * @var string $currentUrl Current url.
     * @var string $siteUrl    Url to this site, http://dbwebb.se.
     * @var string $baseUrl    Url to root dir,
     *                         siteUrl . /some/installation/directory/.
     */
    private $currentUrl;
    private $siteUrl;
    private $baseUrl;



    /**
     * @var array $server Mapped to $_SERVER.
     * @var array $get    Mapped to $_GET.
     * @var array $post   Mapped to $_POST.
     * @var array $body   Mapped to request body, defaults to php://input.
     */
    private $server;
    private $get;
    private $post;
    private $body;



    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->setGlobals();
    }



    /**
     * Read info from the globals.
     *
     * @param array $globals use to initiate globals with values.
     *
     * @return void
     */
    public function setGlobals($globals = [])
    {
        $this->server = [];
        $this->get = [];
        $this->post = [];

        $this->server = isset($globals["server"])
            ? array_merge($_SERVER, $globals["server"])
            : $_SERVER;

        $this->get = isset($globals["get"])
            ? array_merge($_GET, $globals["get"])
            : $_GET;

        $this->post = isset($globals["post"])
            ? array_merge($_POST, $globals["post"])
            : $_POST;
    }



    /**
     * Read info from the globals.
     *
     * @param array $globals use to initiate globals with values.
     *
     * @return void
     */
    public function unsetGlobals()
    {
        $this->server = [];
        $this->get = [];
        $this->post = [];
    }



    /**
     * Init the request class by reading information from the request.
     *
     * @return $this
     */
    public function init()
    {
        $this->requestUri = rawurldecode($this->getServer("REQUEST_URI"));
        $scriptName = rawurldecode($this->getServer("SCRIPT_NAME"));
        $this->path = rtrim(dirname($scriptName), "/");
        $this->scriptName = basename($scriptName);

        // The route and its parts
        $this->extractRoute();

        // Prepare to create siteUrl and baseUrl by using currentUrl
        $this->currentUrl = $this->getCurrentUrl();
        $parts = parse_url($this->currentUrl);
        if ($parts === false) {
            $this->siteUrl = null;
            $this->baseUrl = null;
            return $this;
        }

        // Build the url from its parts
        $this->siteUrl = "{$parts["scheme"]}://{$parts["host"]}"
            . (isset($parts["port"])
                ? ":{$parts["port"]}"
                : "");
        $this->baseUrl = $this->siteUrl . $this->path;

        return $this;
    }



    /**
     * Get site url including scheme, host and port.
     *
     * @return string
     */
    public function getSiteUrl()
    {
        return $this->siteUrl;
    }



    /**
     * Get base url including site url and path to current index.php.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }



    /**
     * Get script name, index.php or other.
     *
     * @return string
     */
    public function getScriptName()
    {
        return $this->scriptName;
    }



    /**
     * Get route path parts in an array.
     *
     * @return array with route in its parts
     */
    public function getRouteParts()
    {
        return $this->routeParts;
    }



    /**
     * Get route path as a string.
     *
     * @return string as the current extracted route
     */
    public function getRoute()
    {
        return $this->route;
    }



    /**
     * Get the request method.
     *
     * @return string as the request method
     */
    public function getMethod()
    {
        return $this->getServer("REQUEST_METHOD");
    }



    /**
     * Extract the part containing the route.
     *
     * @todo Should be private, or useful in test?
     *
     * @return string as the current extracted route
     */
    public function extractRoute()
    {
        $requestUri = $this->requestUri;
        $scriptPath = $this->path;
        $scriptFile = $this->scriptName;

        // Compare REQUEST_URI and SCRIPT_NAME as long they match,
        // leave the rest as current request.
        $i = 0;
        $len = min(strlen($requestUri), strlen($scriptPath));
        while ($i < $len
               && $requestUri[$i] == $scriptPath[$i]
        ) {
            $i++;
        }
        $route = trim(substr($requestUri, $i), "/");

        // Does the request start with script-name - remove it.
        $len1 = strlen($route);
        $len2 = strlen($scriptFile);

        if ($len2 <= $len1
            && substr_compare($scriptFile, $route, 0, $len2, true) === 0
        ) {
            $route = substr($route, $len2 + 1);
        }

        // Remove the ?-part from the query when analysing controller/metod/arg1/arg2
        $queryPos = strpos($route, "?");
        if ($queryPos !== false) {
            $route = substr($route, 0, $queryPos);
        }

        $route = ($route === false) ? "" : $route;

        $this->route = $route;
        $this->routeParts = explode("/", trim($route, "/"));

        return $this->route;
    }



    /**
     * Get the current url.
     *
     * @param boolean $queryString attach query string, default is true.
     *
     * @return string as current url.
     */
    public function getCurrentUrl($queryString = true)
    {
        $port  = $this->getServer("SERVER_PORT");
        $https = $this->getServer("HTTPS") == "on" ? true : false;

        $scheme = $https
            ? "https"
            : $this->getServer("REQUEST_SCHEME", "http");

        $server = $this->getServer("SERVER_NAME")
            ?: $this->getServer("HTTP_HOST");

        $port  = ($port === "80")
            ? ""
            : (($port == 443 && $https)
                ? ""
                : ":" . $port);

        $uri = rawurldecode($this->getServer("REQUEST_URI"));
        $uri = $queryString
            ? rtrim($uri, "/")
            : rtrim(strtok($uri, "?"), "/");

        $url  = htmlspecialchars($scheme) . "://";
        $url .= htmlspecialchars($server)
            . $port . htmlspecialchars(rawurldecode($uri));

        return $url;
    }



    /**
     * Get a value from the _SERVER array and use default if it is not set.
     *
     * @param string $key     to check if it exists in the $_SERVER variable,
     *                        or empty to get whole array.
     * @param mixed  $default value to return as default
     *
     * @return mixed
     */
    public function getServer($key = null, $default = null)
    {
        if ($key) {
            return $this->server[$key] ?? $default;
        }

        return $this->server;
    }



    /**
     * Set variable in the server array.
     *
     * @param mixed  $key   the key an the , or an key-value array
     * @param string $value the value of the key
     *
     * @return self
     */
    public function setServer($key, $value = null) : object
    {
        if (is_array($key)) {
            $this->server = array_merge($this->server, $key);
        } else {
            $this->server[$key] = $value;
        }
        return $this;
    }



    /**
     * Check if the value from the _GET array exists.
     *
     * @param string $key     to check if it exists in the $_GET variable
     *
     * @return boolean
     */
    public function hasGet($key)
    {
        return array_key_exists($key, $this->get);
    }



    /**
     * Get a value from the _GET array and use default if it is not set.
     *
     * @param string $key     to check if it exists in the $_GET variable,
     *                        or empty to get whole array.
     * @param mixed  $default value to return as default
     *
     * @return mixed
     */
    public function getGet($key = null, $default = null)
    {
        if ($key) {
            return $this->get[$key] ?? $default;
        }

        return $this->get;
    }



    /**
     * Set variable in the get array.
     *
     * @param mixed  $key   the key an the value, or an key-value array
     * @param string $value the value of the key
     *
     * @return self
     */
    public function setGet($key, $value = null) : object
    {
        if (is_array($key)) {
            $this->get = array_merge($this->get, $key);
        } else {
            $this->get[$key] = $value;
        }
        return $this;
    }



    /**
     * Get a value from the _POST array and use default if it is not set.
     *
     * @param string $key     to check if it exists in the $_POST variable,
     *                        or empty to get whole array.
     * @param mixed  $default value to return as default
     *
     * @return mixed
     */
    public function getPost($key = null, $default = null)
    {
        if ($key) {
            return $this->post[$key] ?? $default;
        }

        return $this->post;
    }



    /**
     * Set variable in the post array.
     *
     * @param mixed  $key   the key an the value, or an key-value array
     * @param string $value the value of the key
     *
     * @return self
     */
    public function setPost($key, $value = null) : object
    {
        if (is_array($key)) {
            $this->post = array_merge($this->post, $key);
        } else {
            $this->post[$key] = $value;
        }
        return $this;
    }



    /**
     * Set the request body (useful for unit testing).
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;
    }



    /**
     * Get the request body.
     *
     * @return mixed
     */
    public function getBody()
    {
        return isset($this->body)
            ? $this->body
            : file_get_contents("php://input");
    }



    /**
     * Get the request body from the HTTP request and treat it as
     * JSON data, return null if request body is empty.
     *
     * @throws \JsonException when request body is invalid JSON.
     *
     * @return mixed as the JSON converted content or null if body is empty.
     */
    public function getBodyAsJson()
    {
        $body = $this->getBody();
        if ($body == "") {
            return null;
        }

        $entry = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        return $entry;
    }
}
