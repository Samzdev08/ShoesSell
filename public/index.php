<?php
require __DIR__ . '/../vendor/autoload.php';
use Slim\Factory\AppFactory;

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