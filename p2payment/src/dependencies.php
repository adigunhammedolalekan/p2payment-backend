<?php
/**
 * Created by PhpStorm.
 * User: Lekan Adigun
 * Date: 3/29/2018
 * Time: 3:57 AM
 */

    $container = $app->getContainer();
    $container["db"] = function ($c) {

        $config = $c["settings"]["database"];

        $dsn = "mysql:host=" . $config["host"] . ";dbname=" . $config["database"];

        $db = new DatabaseManager($dsn, $config);
        return $db;
    };


    $container['logger'] = function ($c) {
        $settings = $c['settings']['logger'];
        $logger = new Monolog\Logger($settings['name']);
        $logger->pushProcessor(new Monolog\Processor\UidProcessor());
        $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

        return $logger;
    };

?>