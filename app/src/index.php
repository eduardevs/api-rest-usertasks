<?php
declare(strict_types=1);

// use Dotenv\Dotenv;
use App\ErrorHandler;
use App\Database\Database;
use App\Gateway\TaskGateway;
use App\Controllers\TaskController;
// var_dump(dirname(__FILE__));
// var_dump(dirname(__DIR__)."/vendor/autoload.php");
// WHY NOT DIR (var/www/) ? HERE, FILE var/www/html/

require dirname(__DIR__)."/vendor/autoload.php";

set_error_handler("App\ErrorHandler::handleError");
set_exception_handler("App\ErrorHandler::handleException");

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$parts = explode("/", $path);
// var_dump($parts);
// die();
$resource = $parts[2];

$id = $parts[3] ?? null;

// echo $resource, ",", $id;

// echo $_SERVER["REQUEST_METHOD"];

if($resource !== "tasks") {

    http_response_code(404);
    exit;
}

header("Content-type: application/json; charset=UTF-8");

$database = new Database($_ENV['MYSQL_HOST'], $_ENV['MYSQL_DATABASE'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);

$database->getConnection();

$task_gateway = new TaskGateway($database);

$controller = new TaskController($task_gateway);

$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);




