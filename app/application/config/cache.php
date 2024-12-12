<?php

defined('BASEPATH') or exit('No direct script access allowed');
// Archivo: application/config/cache.php
// $config['adapter'] = 'memcached';
// $config['backup'] = 'file';

// // Configuración específica de Memcached
// $config['memcached'] = array(
// 'default' => array(
// 'hostname' => 'memcached_server', // Reemplaza con el hostname de tu servidor Memcached
// 'port' => '11211',
// 'weight' => '1',
// ),
// );

// Configuración de Redis
$config['adapter'] = 'redis';  // Usar Redis como caché
$config['backup']  = 'file';   // Respaldo en archivo en caso de fallo de Redis

// Configuración específica de Redis
$config['redis'] = array(
    'socket_type' => 'tcp',            // Conexión a través de TCP
    'host'        => 'redis_server',   // Servicio de Redis en Docker Compose
    'password'    => NULL,             // Contraseña de Redis, si la tienes configurada
    'port'        => 6379,             // Puerto de Redis
    'timeout'     => 0                 // Timeout de la conexión
);

// Tiempo de vida por defecto de los datos en caché (en segundos)
$config['cache_expiration'] = 3600;    // 1 hora