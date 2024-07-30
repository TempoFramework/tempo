<?php

namespace Tempo;
use Tempo\HttpResult;

class HttpRequest
{

    public static function fromBody($dtoInstance = null)
    {
        $data = null;

        // Obtener el contenido de la solicitud
        $rawData = file_get_contents("php://input");

        // Verificar si el contenido es JSON
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if ($contentType === "application/json") {
            // Decodificar el JSON
            $data = json_decode($rawData, true);
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                HttpResult::badRequest('Error al decodificar JSON.');
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Si es una solicitud POST y no es JSON, utilizar $_POST
            $data = $_POST;
        } elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            // Si es una solicitud PUT y no es JSON, se supone que es FormData
            parse_str($rawData, $data);
        }

        // Sanitizar los datos obtenidos
        $data = self::sanitize($data);

        // Si se proporcionó un objeto DTO, asignar propiedades si existen
        if ($dtoInstance !== null && is_object($dtoInstance)) {
            foreach ($data as $key => $value) {
                // Verificar si la propiedad existe en el DTO antes de asignar
                if (property_exists($dtoInstance, $key)) {
                    $dtoInstance->{$key} = $value;
                }
            }
            return $dtoInstance;
        }

        return $data;
    }

    public static function fromQuery ($dataRequest)
    {
        
        $data = $_GET[$dataRequest] ?? null;
        return self::sanitize($data);
        
    }

    public static function fromHeader($headerName)
    {
        $headerName = strtoupper(str_replace('-', '_', $headerName));
        $headerName = 'HTTP_' . $headerName;
        if (isset($_SERVER[$headerName])) {
            return self::sanitize($_SERVER[$headerName]);
        }
        return null;
    }

    public static function fromFile($fileRequest)
    {
        // Verificar que la solicitud sea POST y que exista un archivo con el nombre dado
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES[$fileRequest])) {
            // Obtener la información del archivo
            $fileInfo = $_FILES[$fileRequest];

            // Verificar errores al subir el archivo
            if ($fileInfo['error'] === UPLOAD_ERR_OK) {
                // El archivo se subió correctamente, devolver la información del archivo
                return $fileInfo;
            } else {
                // Manejar el error según el código de error
                switch ($fileInfo['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                        $errorMessage = 'El archivo excede el tamaño máximo permitido por el servidor.';
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $errorMessage = 'El archivo excede el tamaño máximo permitido por el formulario.';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $errorMessage = 'El archivo solo se ha subido parcialmente.';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $errorMessage = 'No se ha seleccionado ningún archivo para subir.';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                    case UPLOAD_ERR_CANT_WRITE:
                        $errorMessage = 'Error interno al intentar subir el archivo.';
                        break;
                    default:
                        $errorMessage = 'Error desconocido al subir el archivo.';
                        break;
                }
                // Devolver un error HTTP 400 Bad Request con el mensaje de error
                HttpResult::badRequest($errorMessage);
            }
        } else {
            // Si no es una solicitud POST o no se encontró el archivo solicitado, devolver null o manejar según tu lógica
            return null;
        }
    }

    public static function fromBearerToken()
    {
        $authorizationHeader = self::fromHeader('Authorization');

        if($authorizationHeader === null){
            return HttpResult::unauthorized('Se requiere autenticación');
        }

        if ($authorizationHeader !== null) {
            $authorizationHeader = explode(' ', $authorizationHeader);
            if (count($authorizationHeader) === 2 && $authorizationHeader[0] === 'Bearer') {
                return $authorizationHeader[1];
            }
        }
        return HttpResult::unauthorized('Token no válido');
    }

    private static function sanitize($data)
    {
        // Verificar si es un array
        if (is_array($data)) {
            // Aplicar sanitización a cada elemento del array recursivamente
            return array_map([self::class, 'sanitize'], $data);
        } else {
            // Si el valor es una cadena vacía, establecerlo como null
            if ($data === '') {
                return null;
            }
            // Si el valor no es una cadena, devolverlo tal cual
            if (!is_string($data)) {
                return $data;
            }
            // Proteger contra inyecciones XSS
            $sanitizedData = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            // Eliminar espacios en blanco del principio y el final de la cadena
            $sanitizedData = trim($sanitizedData);
            return $sanitizedData;
        }
    }

}