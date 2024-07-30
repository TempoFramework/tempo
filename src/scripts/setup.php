<?php

// Define el contenido de index.php
$indexContent = <<<PHP
<?php
use SimplePhpApi\Cors;

require_once(__DIR__ . '/vendor/autoload.php');

use Dotenv\Dotenv;
use SimplePhpApi\Router;

\$dotenv = Dotenv::createImmutable(__DIR__);
\$dotenv->load();

\$cors = new Cors();
\$cors->init();

\$Router::init();

PHP;

// Crea index.php en la raíz del proyecto
file_put_contents('index.php', $indexContent);

// Crea directorios controllers y models
mkdir('controllers', 0777, true);
mkdir('models', 0777, true);

// Define el contenido de Connection.php
$connectionContent = <<<PHP
<?php

namespace App;

use SimplePhpApi\DbConfigConnection;

class Connection extends DbConfigConnection
{
}
PHP;

// Crea Connection.php en la raíz del proyecto
file_put_contents('Connection.php', $connectionContent);

// Define el contenido de Api.config.json
$configContent = json_encode([
    "project" => [
        "name" => "API",
        "version" => "1.0.0"
    ],
    "secrets" => [
        "path" => "./secrets.env"
    ],
    "controllers" => [
        "path" => "./controllers/"
    ]
], JSON_PRETTY_PRINT);

// Crea Api.config.json en la raíz del proyecto
file_put_contents('Api.config.json', $configContent);

echo "\n\n---------------------------\n| Configuration completed |\n---------------------------\n\n";
