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
     * @var string $server Mapped to $_SERVER.
     * @var string $get    Mapped to $_GET.
     * @var string $post   Mapped to $_POST.
     */
    private $server;
    private $get;
    private $post;



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
        $this->siteUrl = "{$parts["scheme"]}://{$parts["host"]}"
            . (isset($parts["port"])
                ? ":{$parts["port"]}"
                : "");
        $this->baseUrl = $this->siteUrl . $this->path;

        return $this;
    }



    /**
     * Get site url.
     *
     * @return string
     */
    public function getSiteUrl()
    {
        return $this->siteUrl;
    }



    /**
     * Get base url.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }



    /**
     * Get script name.
     *
     * @return string
     */
    public function getScriptName()
    {
        return $this->scriptName;
    }



    /**
     * Get route parts.
     *
     * @return array with route in its parts
     */
    public function getRouteParts()
    {
        return $this->routeParts;
    }



    /**
     * Get the route.
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
     * @param string $key     to check if it exists in the $_SERVER variable
     * @param string $default value to return as default
     *
     * @return mixed
     */
    public function getServer($key, $default = null)
    {
        return isset($this->server[$key]) ? $this->server[$key] : $default;
    }



    /**
     * Set variable in the server array.
     *
     * @param mixed  $key   the key an the , or an key-value array
     * @param string $value the value of the key
     *
     * @return self
     */
    public function setServer($key, $value = null)
    {
        if (is_array($key)) {
            $this->server = array_merge($this->server, $key);
        } else {
            $this->server[$key] = $value;
        }
        return $this;
    }



    /**
     * Get a value from the _GET array and use default if it is not set.
     *
     * @param string $key     to check if it exists in the $_GET variable
     * @param string $default value to return as default
     *
     * @return mixed
     */
    public function getGet($key, $default = null)
    {
        return isset($this->get[$key]) ? $this->get[$key] : $default;
    }



    /**
     * Set variable in the get array.
     *
     * @param mixed  $key   the key an the , or an key-value array
     * @param string $value the value of the key
     *
     * @return self
     */
    public function setGet($key, $value = null)
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
     * @param string $key     to check if it exists in the $_POST variable
     * @param string $default value to return as default
     *
     * @return mixed
     */
    public function getPost($key = null, $default = null)
    {
        if ($key) {
            return isset($this->post[$key]) ? $this->post[$key] : $default;
        }

        return $this->post;
    }



    /**
     * Get the request body.
     *
     * @return mixed
     */
    public function getBody()
    {
        return file_get_contents("php://input");
    }
}
