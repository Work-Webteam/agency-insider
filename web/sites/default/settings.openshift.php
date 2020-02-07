<?php
$databases['default']['default'] = array (
   'database' => getenv('INSIDER_MYSQL_DATABASE'),
   'username' => getenv('INSIDER_MYSQL_USER'),
   'password' => getenv('INSIDER_MYSQL_PASSWORD'),
   'host' => getenv('INSIDER_MYSQL_HOST'),
   'port' => getenv('INSIDER_MYSQL_PORT'),
   'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
   'driver' => 'mysql',
   'prefix' => '',
   'collation' => 'utf8mb4_general_ci',
);
$settings['hash_salt'] = json_encode($databases);