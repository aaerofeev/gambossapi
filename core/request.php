<?php

class Request {

    protected $routes = array();

    protected $currentRoute;
    protected $currentFormat = 'html';
    protected $currentRouteParams = array();

    protected $requestUri;
    protected $baseUrl = '/';
    protected $defaultRouteName;

    protected $errorCallback;

    public function __construct($requestUri) {

        $this->requestUri = $requestUri;
    }

    public function setErrorCallback($callback) {

        $this->errorCallback = $callback;
    }

    public function addRoute($name, $pattern, $urlPattern = NULL, $callback = NULL) {

        $this->routes[] = array(
            'name' => $name,
            'pattern' => $pattern,
            'urlPattern' => $urlPattern,
            'callback' => $callback,
        );
    }

    public function getRoute() {

        if ($this->currentRoute) {
            return $this->currentRoute;
        }

        return $this->defaultRouteName;
    }

    public function setDefaultRouteName($name) {

        $this->defaultRouteName = $name;
    }

    public function setBaseUrl($value) {

        $this->baseUrl = $value;
    }

    public function url($route, $_ = null) {

        $args = func_get_args();

        if (is_array($_)) {

            $params = $args[1];
            $query = isset($args[2]) ? $args[2] : array();

        } else {

            $params = array_splice($args, 1);
            $query = array();
        }

        $query = array_filter($query);

        foreach ($this->routes as $_route) {

            if ($_route['name'] == $route) {

                $pattern = $_route['urlPattern'];

                if (!$pattern) {
                    $pattern = $_route['pattern'];
                }

                array_unshift($params, $pattern);
                $url = call_user_func_array('sprintf', $params);

                if ($query) {

                    $url .= '?' . http_build_query($query);
                }

                return $this->baseUrl($url);
            }
        }

        return null;
    }

    public function baseUrl($path) {

        if (strpos($path, 'http') === 0) {
            return $path;
        }

        return 'http://' . $_SERVER['HTTP_HOST'] . $this->baseUrl . ltrim($path, '/');
    }

    /**
     * Получить не именные аргументы
     *
     * @return array
     */
    public function getArguments() {

        $argv = array();

        foreach ($this->currentRouteParams as $k => $v) {
            if (is_numeric($k)) {
                $argv[] = $v;
            }
        }

        return $argv;
    }

    public function run() {

        $urlParts = parse_url($this->requestUri);
        $routeUri = preg_replace('|' . $this->baseUrl . '|ui', '', $urlParts['path'], 1);
        $routeSuccess = false;

        if (preg_match('/(?<routeUri>.+?)(?<format>\.\\w+)?$/ui', $routeUri, $routeParams)) {

            $routeUri = $routeParams['routeUri'];

            if (isset($routeParams['format'])) {
                $this->currentFormat = ltrim($routeParams['format'], '.');
            }
        }

        foreach ($this->routes as $route) {

            if ($route['pattern'] == $routeUri) {

                $this->currentRoute = $route['name'];
                $this->currentRouteParams = array();

                if ($route['callback']) {

                    try {

                        call_user_func_array(array($route['callback'][0], 'run'), array($this, $route['callback'][1]));

                    } catch (Exception $e) {

                        call_user_func_array($this->errorCallback, array($e));
                    }
                }

                $routeSuccess = true;
                break;
            }

            if ($route['pattern'] && preg_match('|^' . $route['pattern'] . '$|ui', $routeUri, $this->currentRouteParams)) {

                $this->currentRoute = $route['name'];
                $arguments = $this->getArguments();

                if ($route['callback']) {

                    try {

                        call_user_func_array(array($route['callback'][0], 'run'), array($this, $route['callback'][1], array_splice($arguments, 1)));

                    } catch (Exception $e) {

                        call_user_func_array($this->errorCallback, array($e));
                    }
                }

                $routeSuccess = true;
                break;
            }
        }

        if (!$routeSuccess && $this->errorCallback) {

            $e = new Exception('Route not found', 404);
            call_user_func_array($this->errorCallback, array($e));
        }

        return $routeSuccess;
    }

    public function getFormat()
    {
        return $this->currentFormat;
    }

    public function getParams()
    {
        return $this->currentRouteParams;
    }

    public function getParam($name) {

        if (isset($this->currentRouteParams[$name])) {

            return $this->currentRouteParams[$name];
        }

        if (isset($_GET[$name])) {

            return $_GET[$name];
        }

        return false;
    }

    public function setParam($name, $value) {

        $this->currentRouteParams[$name] = $value;
    }

    public function sendCode($code, $text = 'OK') {
        header('HTTP/1.1 ' . $code . ' ' . $text);
    }

    public function send($header) {
        header($header);
    }

    public function redirect($path) {

        $this->send("Location: {$path}");
        exit(0);
    }

    public function serverUri() {

        return $_SERVER['REQUEST_URI'];
    }

    public function serverUrl() {
        return $this->baseUrl($this->serverUri());
    }
}