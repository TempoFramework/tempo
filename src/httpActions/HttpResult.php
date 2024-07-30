<?php

namespace Tempo;

class HttpResult 
{
    public static function ok($message = 'Ok'): void
    {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($message);
        exit();
    }

    public static function badRequest($message = 'Bad Request'): void
    {
        http_response_code(400);
        echo json_encode($message);
        exit();

    }

    public static function methodNotAllowed($message = 'Method Not Allowed'): void
    {
        http_response_code(405);
        echo json_encode($message);
        exit();

    }

    public static function notFound($message = 'Not found'): void
    {
        http_response_code(404);
        echo json_encode($message);
        exit();

    }

    public static function unauthorized($message = 'Unauthorized'): void
    {
        http_response_code(401);
        echo json_encode($message);
        exit();

    }

    public static function internalServerError($message = 'Internal Server Error'): void
    {
        http_response_code(500);
        echo json_encode($message);
        exit();

    }

    public static function forbidden($message = 'Forbidden'): void
    {
        http_response_code(403);
        echo json_encode($message);
        exit();

    }

    public static function conflict($message = 'Conflict'): void
    {
        http_response_code(409);
        echo json_encode($message);
        exit();

    }

    public static function created($message = 'Created'): void
    {
        http_response_code(201);
        echo json_encode($message);
        exit();

    }

    public static function noContent($message = 'No Content'): void
    {
        http_response_code(204);
        echo json_encode($message);
        exit();

    }

    public static function notImplemented($message = 'Not Implemented'): void
    {
        http_response_code(501);
        echo json_encode($message);
        exit();

    }

    public static function serviceUnavailable($message = 'Service Unavailable'): void
    {
        http_response_code(503);
        echo json_encode($message);
        exit();

    }

    public static function gone($message = 'Gone'): void
    {
        http_response_code(410);
        echo json_encode($message);
        exit();

    }

    public static function tooManyRequests($message = 'Too Many Requests'): void
    {
        http_response_code(429);
        echo json_encode($message);
        exit();

    }

    public static function unauthorizedToken($message = 'Unauthorized Token'): void
    {
        http_response_code(401);
        echo json_encode($message);
        exit();

    }
}