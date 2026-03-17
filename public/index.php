<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);
$app->add(\App\Middleware\SessionMiddleware::class);


require __DIR__ . '/../config/routes.php';

$app->run();