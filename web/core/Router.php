<?php
namespace App\Core;
use Exception;
use App\Core\Components\Controller as Controller;
class Router
{
    /*
     * This is the routes array. So far it only works for GET and POST but this can be changed.
     */
    public array $routes = [

        'GET' => [],
        'POST' => []

    ];
    /*
     * This function loads the routes from a file. In this framework, the routes are stored in app/routes.php.
     */
    public static function load($file): static
    {
        $router = new static;

        require $file;
        
        return $router;

    }
    
    /*
     * This function gets the GET route based on the URI and passes it off to the controller.
     */
    public function get($uri, $controller): void
    {
        $this->routes['GET'][$uri] = $controller;
    }
    /*
     * This function gets the POST route based on the URI and passes it off to the controller.
     */
    public function post($uri, $controller): void
    {
        $this->routes['POST'][$uri] = $controller;
    }
    /*
     * This function using array notation routing gets the GET routes. PHP does not support function overloading (also known as method overloading in OOP), so we cannot name this function get even though it has a different number of parameters than the get function used for routing without array notation.
     */
    public function getArray($routes): void
    {
        $this->routes['GET'] = $routes;
    }
    /*
     * This function using array notation routing gets the POST routes. PHP does not support function overloading (also known as method overloading in OOP), so we cannot name this function post even though it has a different number of parameters than the post function used for routing without array notation.
     */
    public function postArray($routes): void
    {
        $this->routes['POST'] = $routes;
    }  

    /*
     * This function directs the user to the route based on the request type.
     */
    /**
     * @throws Exception
     */
    public function direct($uri, $requestType, $request)
    {
        if (array_key_exists($uri, $this->routes[$requestType])) {
            $arrr = explode('@', $this->routes[$requestType][$uri]);
            return $this->callAction($arrr[0], $arrr[1], $request);
        }

        foreach ($this->routes[$requestType] as $key => $value) {
            $pattern = preg_replace('#\(/\)#', '/?', $key);
            $pattern = "@^" . preg_replace('/{([\w\-]+)}/', '(?<$1>[\w\-]+)', $pattern) . "$@D";
            preg_match($pattern, $uri, $matches);
            array_shift($matches);
            if ($matches) {
                $action = explode('@', $value);
                return $this->callAction($action[0], $action[1], $request, $matches);
            }
        }

        header("HTTP/1.1 404 Not Found");
        throw new Exception('No route defined for this URI.');
    }
    /*
     * This function calls the controller for an action.
     */
    /**
     * @throws Exception
     */
    protected function callAction($controller, $action,  Request $request, $vars = [])
    {
        $controller = "App\\Controllers\\{$controller}";

        $controller = new $controller($request);

        if (!method_exists($controller, $action))
        {
            throw new Exception("{$controller} does not respond to the {$action} action.");
        }

        return $controller->$action($vars);
    }
}