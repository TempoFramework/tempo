<?php

namespace Tempo;

/**
 * Class Cors
 * 
 * A class to handle CORS (Cross-Origin Resource Sharing) settings.
 */
class Cors
{
    private $allowedOrigins = '*';
    private $allowedMethods = 'GET, POST, PUT, PATCH, DELETE';
    private $allowedHeaders = 'Origin, X-Requested-With, Content-Type, Accept, Authorization';

    /**
     * Set allowed origins.
     *
     * @param string $origins The allowed origins.
     * @return $this
     */
    public function setAllowedOrigins(string $origins)
    {
        $this->allowedOrigins = $origins;
        return $this;
    }

    /**
     * Set allowed methods.
     *
     * @param string $methods The allowed methods.
     * @return $this
     */
    public function setAllowedMethods(string $methods)
    {
        $this->allowedMethods = $methods;
        return $this;
    }

    /**
     * Set allowed headers.
     *
     * @param string $headers The allowed headers.
     * @return $this
     */
    public function setAllowedHeaders(string $headers)
    {
        $this->allowedHeaders = $headers;
        return $this;
    }

    /**
     * Initialize CORS settings.
     */
    public function init()
    {
        header("Access-Control-Allow-Origin: {$this->allowedOrigins}");
        header("Access-Control-Allow-Methods: {$this->allowedMethods}");
        header("Access-Control-Allow-Headers: {$this->allowedHeaders}");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("HTTP/1.1 200 OK");
            exit;
        }
    }
}