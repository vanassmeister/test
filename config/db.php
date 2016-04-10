<?php

$dbFile = realpath(__DIR__."/../runtime")."/data.db";

return [
    'class' => 'yii\db\Connection',
    'dsn' => "sqlite:$dbFile",
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];