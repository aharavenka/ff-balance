<?php
$db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
$db['dsn'] = 'mysql:host=' . $_ENV['DB_TEST_HOST'] . ';port=' . $_ENV['DB_TEST_PORT']
    . ';dbname=' . $_ENV['DB_TEST_DATABASE'];
$db['username'] = $_ENV['DB_TEST_USERNAME'];
$db['password'] = $_ENV['DB_TEST_PASSWORD'];

return $db;
