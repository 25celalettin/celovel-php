<?php
class App {
    private $config = [
        'prefix' => null,
        'middlewares' => []
    ];
    private $allowed_methods = ['get', 'post', 'put', 'patch', 'delete'];
    private $current_path;
    private $current_method;
    private static $routes = [];
    private $request_body = [];
    private $params_names = [];

    public function __construct($config = null) {
        $this->current_path = explode('?', $_SERVER['REQUEST_URI'])[0] ;
        $this->current_method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($config) {
            $this->config = $config;
        }

        if (strlen($this->current_path) > 1 && substr($this->current_path, -1) == '/') {
            $this->current_path = substr($this->current_path, 0, -1);
        }

        $this->request_body = $this->parse_body();
    }

    // parse json & form data
    private function parse_body() {
        $body = [];
        if (isset($_POST)) {
            $body = $_POST;
        } else {
            $entityBody = file_get_contents('php://input');
            if (!empty($entityBody)) {
                $body = json_decode($entityBody);
            }
        }

        return $body;
    }

    // control & filter path
    private function path_control($path) {
        if ($path == '') {
            return new CustomError(400, 'Please do not empty \'path\' field!');
        }

        // if has a prefix, add prefix
        $this->config['prefix'] = $this->config['prefix'] ?? '';
        $path = $this->config['prefix'] . $path;

        // add / to start of path
        if (substr($path, 0, 1) != '/') {
            $path = '/' . $path;
        }

        // remove / if last char is /
        if (strlen($path) > 1 && substr($path, -1) == '/') {
            $path = substr($path, 0, -1);
        }

        // edit dynamic routes
        if (strpos($path, ':') !== false) {
            $arr = array_filter(explode('/', $path));
            foreach($arr as $ix => $item) {
                if ($item[0] == ':') {
                    array_push($this->params_names, substr($item, 1));
                    $arr[$ix] = '([0-9a-zA-Z-_]+)';
                }
            }
            $path = '/' . implode('/', $arr);
        }
        return $path;
    }

    // get callback function name from controller string
    public function get_callback($string, $type = 'controllers/') {
        list($file_name, $func_name) = explode('@', $string);

        $file_path = ROOT_DIR . '/'. $type . str_replace('.', '/', $file_name) . '.php';
        if (file_exists($file_path)) {
            require_once $file_path;
            if (function_exists($func_name)) {
                return $func_name;
            } else {
                return new CustomError(400, 'undefined function name: ' . $func_name . ' in /' . $type . $file_name . '.php');
            }
        } else {
            return new CustomError(400, 'undefined file path: /' . $file_path);
        }
    }

    // route for all methods
    public function any($path, $callback) {
        $routeObj = [
            'route' => $this->path_control($path),
            'method' => '*',
            'callback' => $callback,
            'middlewares' => $this->config['middlewares']
        ];
        self::$routes[] = $routeObj;
    }

    // route for get method
    public function get($path, $middlewares, $callback = null) {
        if ($callback === null) {
            $callback = $middlewares;
            $middlewares = [];
        }
    
        $middlewares = array_merge($this->config['middlewares'], $middlewares);

        $routeObj = [
            'route' => $this->path_control($path),
            'method' => 'get',
            'callback' => $callback,
            'middlewares' => $middlewares,
            'params' => $this->params_names
        ];
        self::$routes[] = $routeObj;
        $this->params_names = [];
    }

    // route for post method
    public function post($path, $middlewares, $callback = null) {
        if ($callback === null) {
            $callback = $middlewares;
            $middlewares = [];
        }

        $middlewares = array_merge($this->config['middlewares'], $middlewares);

        $routeObj = [
            'route' => $this->path_control($path),
            'method' => 'post',
            'callback' => $callback,
            'middlewares' => $middlewares,
            'params' => $this->params_names
        ];
        self::$routes[] = $routeObj;
        $this->params_names = [];
    }

    // route for put method
    public function put($path, $middlewares, $callback = null) {
        if ($callback === null) {
            $callback = $middlewares;
            $middlewares = [];
        }

        $middlewares = array_merge($this->config['middlewares'], $middlewares);

        $routeObj = [
            'route' => $this->path_control($path),
            'method' => 'put',
            'callback' => $callback,
            'middlewares' => $middlewares,
            'params' => $this->params_names
        ];
        self::$routes[] = $routeObj;
        $this->params_names = [];
    }

    // route for patch method
    public function patch($path, $middlewares, $callback = null) {
        if ($callback === null) {
            $callback = $middlewares;
            $middlewares = [];
        }

        $middlewares = array_merge($this->config['middlewares'], $middlewares);

        $routeObj = [
            'route' => $this->path_control($path),
            'method' => 'patch',
            'callback' => $callback,
            'middlewares' => $middlewares,
            'params' => $this->params_names
        ];
        self::$routes[] = $routeObj;
        $this->params_names = [];
    }

    // route for delete method
    public function delete($path, $middlewares, $callback = null) {
        if ($callback === null) {
            $callback = $middlewares;
            $middlewares = [];
        }

        $middlewares = array_merge($this->config['middlewares'], $middlewares);

        $routeObj = [
            'route' => $this->path_control($path),
            'method' => 'delete',
            'callback' => $callback,
            'middlewares' => $middlewares,
            'params' => $this->params_names
        ];
        self::$routes[] = $routeObj;
        $this->params_names = [];
    }

    // route groups
    public function group($prefix, $middlewares, $callback = null) {
        $prefix = $this->path_control($prefix);

        if ($callback === null) {
            $callback = $middlewares;
            $middlewares = [];
        }

        $middlewares = array_merge($this->config['middlewares'], $middlewares);

        $newRouter = new App([
            'prefix' => $prefix,
            'middlewares' => $middlewares
        ]);

        $callback($newRouter);
    }

    // start the routing control process
    public function startRoutes() {
        foreach (self::$routes as $routeObj) {
            if ((preg_match('%^' . $routeObj['route'] . '$%', $this->current_path, $matches) === 1 || $routeObj['route'] == '/*') && ($this->current_method == $routeObj['method'] || $routeObj['method'] == '*')) {
                array_shift($matches);

                foreach($matches as $index => $param) {
                    $matches[$routeObj['params'][$index]] = $matches[$index];
                    unset($matches[$index]);
                }

                $request = new Request([
                    'params' => $matches,
                    'body' => $this->request_body,
                    'current_path' => $this->current_path
                ]);

                $response = new Response();

                if ($routeObj['middlewares']) {

                    foreach($routeObj['middlewares'] as $middlewareName) {
                        $middleware = $this->get_callback($middlewareName, 'middlewares/');
                        $status = $middleware($request, $response);

                        if ($status === true) {
                            continue;
                        } else if ($status === false) {
                            return new CustomError(400, 'You can\'t pass the middleware: ' . $middlewareName);
                        } else {
                            $request->check_redirect();
                            return new CustomError(400, 'You should return boolean from the middleware: ' . $middlewareName); 
                        }
                    }
                }

                if (is_string($routeObj['callback'])) {
                    $routeObj['callback'] = $this->get_callback($routeObj['callback']);
                    $routeObj['callback']($request, $response);
                } else if (is_array($routeObj['callback'])) {
                    foreach($routeObj['callback'] as $callback) {
                        $callback = $this->get_callback($callback);
                        $status = $callback($request, $response);
                        
                        if ($status !== true) {
                            break;
                        }
                    }
                } else if (is_callable($routeObj['callback'])) {
                    $routeObj['callback']($request, $response);
                }
                return;
            }
        }
        return new CustomError(400, 'cannot request ' . $this->current_method . ' to ' . $this->current_path);
    }

    function __destruct() {
        if (!$this->config['prefix']) {
            $this->startRoutes();
        }
    }
}