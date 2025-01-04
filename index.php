<?php

declare(strict_types=1);

require "Database.php";

use DAL\RecipeDAL;
use controller\RecipeController;

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});


set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);

$id = $parts[2] ?? null;

$database = new Database("23.239.110.246", "recipefarm", "recipefarmuser", "recipeorgy69");


switch ($parts[1]) {
    case "recipe":
        $gateway = new RecipeDAL($database);
        $controller = new RecipeController($gateway);
        break;

    default:
        exit;
}

$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);












