<?php

class Routes {

  public static function  get($path, $handler) {
        self::addRoute('GET', $path, $handler);
  }

  public static function post($path, $handler) {
        self::addRoute('POST', $path, $handler);
  }

  public static function addRoute($method, $path, $handler) {
        $path = trim($path, '/');
        $key = self::getKey($method, $path);
        self::$routes[$key] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
  }

  public static function match($method, $path) {
        $path = trim($path, '/');
        $key = self::getKey($method, $path);
        return isset(self::$routes[$key]) ? self::$routes[$key] : null;
  }


  private static function getKey($method, $path) {
        return strtoupper("{$method} - {$path}");
  }

  static public $routes = [];
}
