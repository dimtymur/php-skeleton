<?php

namespace App\Core;

class Route {
    private $namespace = "App\Controllers\\";
    private $controller;
    private $action;
    private $params;

    static private $resources = [];

    public function __construct() {
        $this->useRoute();
    }

    private function useRoute() : void {
        $url = empty($_GET["url"]) ? "" : trim($_GET["url"], "/");

        foreach (self::$resources as $route => $action)
            if ($this->validateRoute($url, $route)) {
                $params = $this->getRouteParams($url, $route);
                if ($this->execRoute($action, $params)) return;
            }
        throw new \App\Exceptions\RouteException("Route not found.");
    }

    private function validateRoute(string $url, string $route) : bool {
        $pattern = "/^" . str_replace(["{}", "/"], ["\w+", "\/"], $route) . "$/";
        return preg_match($pattern, $url);
    }

    private function getRouteParams(string $url, string $route) : array {
        $params = [];
        $url_pieces = explode("/", $url);
        $route_pieces = explode("/", $route);
        for ($p = 0; $p < count($route_pieces); $p++)
            if ($route_pieces[$p] == "{}") $params[] = $url_pieces[$p];
        return $params;
    }

    private function execRoute($action, array $params) : bool {
        if (gettype($action) == "string") {
            $action_pieces = explode("@", $action);

            $this->controller  = $action_pieces[0];
            $this->action      = $action_pieces[1];
            $this->params      = $params;

            return $this->execController();
        } else $action(...$params);
        return true;
    }

    private function execController() : bool {
        $controller = $this->namespace . $this->controller;
        if (class_exists($controller)) {
            $this->controller = new $controller;
            if (method_exists($this->controller, $this->action)) {
                call_user_func_array([$this->controller, $this->action], $this->params);
                return true;
            }
        }
    }

    static public function setRoute(string $route, $action) : void {
        self::$resources[trim($route, "/")] = $action;
    }
}
