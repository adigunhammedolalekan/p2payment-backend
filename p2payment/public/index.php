<?php
/**
 * Created by PhpStorm.
 * User: Lekan Adigun
 * Date: 3/29/2018
 * Time: 2:50 AM
 */

    require __DIR__ . '/../vendor/autoload.php';
    $settings = require __DIR__ . '/../src/settings.php';

    $app = new Slim\App($settings);

    require __DIR__ . '/../src/jwt/JWT.php';
    require __DIR__ . "/../src/middlewares.php";
    require __DIR__ . '/../src/routes.php';
    require __DIR__ . '/../utils.php';
    require __DIR__ . '/../src/dependencies.php';
    require __DIR__ . '/../src/db/db.php';

    $app->add(new JwtAuth());
    try {
        $app->run();
    }catch (Exception $exception) {
        echo $exception->getMessage();
    }
?>