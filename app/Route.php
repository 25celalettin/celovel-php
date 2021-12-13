<?php
class Router {
    private $config = [];
    private $allowed_methods = [
        'get',
        'post',
        'put',
        'patch',
        'delete'
    ];
    private $current_path;
    private $current_method;
    private static $routes = [];

    public function __construct($config = null) {
        $this->current_path = $_SERVER['REQUEST_URI'];
        $this->current_method = $_SERVER['REQUEST_METHOD'];

        if ($config) {
            $this->config = $config;
        }

        if (strlen($this->current_path) > 1 && substr($this->current_path, -1) == '/') {
            $this->current_path = substr($this->current_path, 0, -1);
        }
    }

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
                    $arr[$ix] = '([0-9a-zA-Z-_]+)';
                }
            }
            $path = '/' . implode('/', $arr);
        }

        return $path;
    }

    public function run_middleware($string) {
        list($file_name, $func_name) = explode('@', $string);
        $file_path = ROOT_DIR . '/middlewares/'. $file_name . '.php';
        if (file_exists($file_path)) {
            require_once $file_path;
            if (function_exists($func_name)) {
                if (!call_user_func($func_name)) {
                    return new CustomError(400, 'you cant pass middleware, middleware: ' . $func_name);
                }
            } else {
                return new CustomError(400, 'undefined function name: ' . $func_name . ' in ' . $file_name . '.php');
            }
        } else {
            return new CustomError(400, 'undefined file path: ' . $file_path);
        }
    }

    public function run_controller($string, $params = []) {
        list($file_name, $func_name) = explode('@', $string);
        $file_path = ROOT_DIR . '/controllers/'. $file_name . '.php';
        if (file_exists($file_path)) {
            require_once $file_path;
            if (function_exists($func_name)) {
                return call_user_func($func_name, ...$params);
            } else {
                return new CustomError(400, 'undefined function name: ' . $func_name . ' in ' . $file_name . '.php');
            }
        } else {
            return new CustomError(400, 'undefined file path: ' . $file_path);
        }
    }

    // for only get, post, put, patch, delete http methods
    public function route($method, $path, $middlewares, $callback = null) {
        $routeObj = [];

        if (!in_array($method, $this->allowed_methods)) {
            return new CustomError(400, 'unknown http method');
        }

        $routeObj['route'] = $this->path_control($path);
        $routeObj['method'] = $method;

        if (!is_array($middlewares)) {
            $routeObj['callback'] = $middlewares;
            $middlewares = [];
        } else {
            $routeObj['callback'] = $callback;
        }

        if (!empty($this->config['middlewares'])) {
            $middlewares = array_merge($this->config['middlewares'], $middlewares);
        }
        $routeObj['middlewares'] = $middlewares;
        
        self::$routes[] = $routeObj;
    }

    // route groups
    public function group($prefix, $middlewares, $callback = null) {
        $prefix = $this->path_control($prefix);

        if (!is_array($middlewares)) {
            $callback = $middlewares;
        }

        $newRouter = new Router([
            'prefix' => $prefix,
            'middlewares' => $middlewares
        ]);
        $callback($newRouter);
    }

    public function startRoutes() {
        foreach (self::$routes as $routeObj) {
            if (preg_match('%^' . $routeObj['route'] . '$%', $this->current_path, $matches) === 1) {
                array_shift($matches);

                if (!empty($routeObj['middlewares'])) {
                    foreach ($routeObj['middlewares'] as $middleware) {
                        $this->run_middleware($middleware);
                    }
                }

                if (is_callable($routeObj['callback'])) {
                    $result = call_user_func($routeObj['callback'], ...$matches);
                } else if (is_string($routeObj['callback'])) {
                    $result = $this->run_controller($routeObj['callback'], $matches);
                } else {
                    return new CustomError(400, 'Only Callable function or String path to controller@func_name');
                }

                if (isset($result['template_engine']) && $result['template_engine'] === true) {
                    $template_engine = new PtEngine([
                        'views' => ROOT_DIR . '/views',
                        'cache' => ROOT_DIR . '/cache',
                        'suffix' => 'celovel'
                    ]);
                    echo $template_engine->view($result['view'], $result['data']);
                } else if (is_string($result)) {
                    echo $result;
                } else if (is_array($result)) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($result);
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