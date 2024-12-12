<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config = array(
    'socket_type' => 'tcp',         // 'tcp' o 'unix'
    'host' => 'redis_server',          // Dirección del servidor Redis
    'password' => NULL,             // Contraseña si Redis está protegido
    'port' => 6379,                 // Puerto de Redis (por defecto 6379)
    'timeout' => 0                  // Tiempo de espera de la conexión
);