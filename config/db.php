<?php

$config = [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=db5006318407.hosting-data.io;dbname=dbs5274480',
    'username' => 'dbu2304583',
    'password' => 'mhw4TC&iBjBzpsZ',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];

if (str_contains($_SERVER['SERVER_NAME'], "localhost")) {
    $config['dsn'] = 'mysql:host=localhost;dbname=muhcutransporte';
    $config['username'] = 'muhcutransporte';
    $config['password'] = 'muhcutransporte';
}

return $config;
