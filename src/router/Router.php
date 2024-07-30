<?php

namespace Tempo;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use Tempo\Attributes\HttpRoute;
use Tempo\Attributes\GetMethod;
use Tempo\Attributes\PostMethod;
use Tempo\Attributes\PutMethod;
use Tempo\Attributes\DeleteMethod;
use Tempo\Attributes\PatchMethod;
use Tempo\HttpResult;


class Router
{
    private static $routes = [];

    public static function init($Controllerdirectory = __DIR__ . '/../../controllers')
    {
        self::registerRoutesFromDirectory($Controllerdirectory);
        self::handleRequest();
    }

    private static function registerRoutesFromDirectory($directory)
    {
        foreach (glob($directory . '/*.php') as $file) {
            require_once $file;
            $className = self::getClassNameFromFile($file);

            if (class_exists($className)) {
                self::registerRoutesFromClass($className);
            }
        }
    }

    private static function getClassNameFromFile($file)
    {
        $className = basename($file, '.php');
        return "App\\Controllers\\$className";
    }

    private static function registerRoutesFromClass($className)
    {
        $reflectionClass = new ReflectionClass($className);
        $attributes = $reflectionClass->getAttributes(HttpRoute::class);

        if (count($attributes) > 0) {
            $routeBase = $attributes[0]->newInstance()->route;

            foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $methodAttributes = $method->getAttributes();

                foreach ($methodAttributes as $attribute) {
                    $instance = $attribute->newInstance();
                    $route = $routeBase;

                    if ($instance instanceof GetMethod) {
                        $route .= $instance->subroute ?? '';
                        self::setRoute($route, $className, $method->getName(), 'GET');
                    } elseif ($instance instanceof PostMethod) {
                        $route .= $instance->subroute ?? '';
                        self::setRoute($route, $className, $method->getName(), 'POST');
                    } elseif ($instance instanceof PutMethod) {
                        $route .= $instance->subroute ?? '';
                        self::setRoute($route, $className, $method->getName(), 'PUT');
                    } elseif ($instance instanceof DeleteMethod) {
                        $route .= $instance->subroute ?? '';
                        self::setRoute($route, $className, $method->getName(), 'DELETE');
                    } elseif ($instance instanceof PatchMethod) {
                        $route .= $instance->subroute ?? '';
                        self::setRoute($route, $className, $method->getName(), 'PATCH');
                    }
                }
            }
        }
    }

    private static function setRoute($route, $controllerClassName, $methodName, $httpMethod)
    {
        self::$routes[$route][$httpMethod] = ['controller' => $controllerClassName, 'method' => $methodName];
    }

    private static function handleRequest()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes as $route => $methods) {
            if ($requestUri === $route && isset($methods[$requestMethod])) {
                $controllerClassName = $methods[$requestMethod]['controller'];
                $methodName = $methods[$requestMethod]['method'];

                if (class_exists($controllerClassName)) {
                    try {
                        $controller = new $controllerClassName();
                        if (method_exists($controller, $methodName)) {
                            $controller->$methodName();
                        } else {
                            HttpResult::methodNotAllowed('Método no implementado.');
                        }
                    } catch (Exception $e) {
                        HttpResult::badRequest('Error interno del servidor: ' . $e->getMessage() . ' ' . $e->getTraceAsString() . '. Por favor, contactar con soporte.');
                    }
                } else {
                    HttpResult::notFound('Controlador no encontrado');
                }
                return;
            }
        }

        HttpResult::notFound('Ruta no encontrada');
    }
}
