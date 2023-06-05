<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'db_test' => [
            'class' => yii\db\Connection::class,
            'dsn' => 'mysql:host=' . $_ENV['DB_TEST_HOST'] . ';port=' . $_ENV['DB_TEST_PORT']
                . ';dbname=' . $_ENV['DB_TEST_DATABASE'],
            'username' => $_ENV['DB_TEST_USERNAME'],
            'password' => $_ENV['DB_TEST_PASSWORD'],
            'charset' => 'utf8',
            'enableSchemaCache' => YII_ENV_PROD,
        ],
        'rabbitmq' => [
            'class' => 'app\components\RabbitMQService',
            'host' => $_ENV['RABBITMQ_HOST'],
            'port' => $_ENV['RABBITMQ_PORT'],
            'username' => $_ENV['RABBITMQ_USERNAME'],
            'password' => $_ENV['RABBITMQ_PASSWORD'],
        ],
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
    // configuration adjustments for 'dev' environment
    // requires version `2.1.21` of yii2-debug module
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
