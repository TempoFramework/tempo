<?php

// Solicita datos al usuario desde la consola
function askQuestion($question, $default = null) {
    echo $question;
    // Asegúrate de que el buffer se limpia
    fflush(STDIN);
    $input = trim(fgets(STDIN));
    echo "Entrada recibida (con espacios): '$input'\n";
    echo "Longitud de entrada: " . strlen($input) . "\n";
    if (empty($input) && $default !== null) {
        return $default;
    }
    if (empty($input)) {
        echo "Input cannot be empty.\n";
        exit(1);
    }
    return $input;
}

$projectName = askQuestion("\nEnter the project name: ");
$authorName = askQuestion("Enter the author name: ");
$version = askQuestion("Enter the initial version (default is 1): ", '1');
$dataBaseProvider = askQuestion("Enter the database provider (e.g., mysql, postgres): ");

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

try {
    Router::init();
} catch (Exception \$e) {
    echo json_encode([
        'error' => \$e->getMessage()
    ]);
}
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
        "name" => $projectName,
        "author" => $authorName,
        "version" => $version . '.0'
    ],
    "dataBase" => [
        "provider" => $dataBaseProvider
    ]
], JSON_PRETTY_PRINT);

// Crea Api.config.json en la raíz del proyecto
file_put_contents('Api.config.json', $configContent);

echo "\n\n---------------------------\n| Configuration completed |\n---------------------------\n\n";
