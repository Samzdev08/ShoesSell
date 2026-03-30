<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use App\Models\Email;
use App\Controllers\AuthController;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([

    Email::class => \DI\create(Email::class),

    AuthController::class => \DI\create(AuthController::class)
        ->constructor(\DI\get(Email::class)),

]);
$container = $containerBuilder->build();


AppFactory::setContainer($container);
$app = AppFactory::create();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setErrorHandler(
    \Slim\Exception\HttpNotFoundException::class,
    function (\Psr\Http\Message\ServerRequestInterface $request, Throwable $exception) use ($app) {
        $response = $app->getResponseFactory()->createResponse();
        return $response->withHeader('Location', '/')->withStatus(302);
    }
);

$app->add(\App\Middleware\SessionMiddleware::class);

require __DIR__ . '/../config/routes.php';

$app->run();