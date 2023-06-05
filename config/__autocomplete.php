<?php

use yii\console\Application;
use yii\web\User;

/**
 * This class only exists here for IDE (PHPStorm/Netbeans/...) autocompletion.
 * This file is never included anywhere.
 * Adjust this file to match classes configured in your application config, to enable IDE autocompletion for custom components.
 * Example: A property phpdoc can be added in `__Application` class as `@property \vendor\package\Rollbar|__Rollbar $rollbar` and adding a class in this file
 * ```php
 * // @property of \vendor\package\Rollbar goes here
 * class __Rollbar {
 * }
 * ```
 */
class Yii
{
    public static Application|__Application|\yii\web\Application $app;
}

/**
 * @property yii\rbac\DbManager $authManager
 * @property User|__WebUser $user
 *
 */
class __Application
{
    /**
     * @var mixed|object|null
     */
    public mixed $rabbitmq;
    /**
     * @var mixed|object|null
     */
    public mixed $db_test;
}

/**
 * @property app\models\User $identity
 */
class __WebUser
{
}
