<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index() {
        $this->load->view('test/index');
    }

    // Comprobar configuraciones de MySQL
    public function verificar_max_allowed_packet() {
        $query_server = $this->db->query("SHOW VARIABLES LIKE 'max_allowed_packet';");
        $resultado_server = $query_server->row();

        $query_client = $this->db->query("SELECT @@session.max_allowed_packet AS client_max_allowed_packet;");
        $resultado_client = $query_client->row();

        $query_tmpdir = $this->db->query("SHOW VARIABLES LIKE 'tmpdir';");
        $resultado_tmpdir = $query_tmpdir->row();

        // Mostrar resultados
        echo "Servidor: " . $resultado_server->Value . "<br>";
        echo "Cliente: " . $resultado_client->client_max_allowed_packet . "<br>";
        echo "Tmpdir: " . $resultado_tmpdir->Value . "<br>";
    }

    // Mostrar estadísticas del sistema de caché utilizando Redis
    public function mostrar_estadisticas_cache() {
        // Usar Redis en lugar de Memcached
        $redis = new Redis();
        try {
            $redis->connect('redis_server', 6379); // Cambia 'redis_server' si es necesario
        } catch (RedisException $e) {
            echo "No se pudo conectar al servidor Redis. Error: " . $e->getMessage();
            return;
        }

        // Obtener estadísticas de Redis
        $stats = $redis->info();

        if ($stats) {
            // Mostrar estadísticas clave
            $uptime = isset($stats['uptime_in_seconds']) ? $stats['uptime_in_seconds'] : 0;
            $used_memory = isset($stats['used_memory']) ? $stats['used_memory'] : 0;
            $total_memory = isset($stats['maxmemory']) ? $stats['maxmemory'] : 0; // Si maxmemory no está configurado, Redis usará toda la memoria disponible

            // Hits y misses
            $hits = isset($stats['keyspace_hits']) ? $stats['keyspace_hits'] : 0;
            $misses = isset($stats['keyspace_misses']) ? $stats['keyspace_misses'] : 0;

            // Memoria usada como porcentaje
            $memory_percentage = $total_memory > 0 ? ($used_memory / $total_memory) * 100 : 0;

            // Mostrar las estadísticas de Redis
            echo "<h2>Estadísticas del sistema de caché (Redis)</h2>";
            echo "<p><strong>Consultas rápidas (hits):</strong> $hits</p>";
            echo "<p><strong>Consultas lentas (misses):</strong> $misses</p>";
            echo "<p><strong>Tiempo en funcionamiento:</strong> " . gmdate("H:i
:s
", $uptime) . " horas</p>";
            echo "<p><strong>Memoria utilizada:</strong> " . round($used_memory / (1024 * 1024), 2) . " MB de " . ($total_memory > 0 ? round($total_memory / (1024 * 1024), 2) . " MB" : "ilimitada") . "</p>";
      echo "<p><strong>Porcentaje de memoria utilizada:</strong> " . round($memory_percentage, 2) . "%</p>";

      // Representación visual de la memoria utilizada
      echo "<div style='width:300px; background-color:#ccc;'>
              <div style='width:" . round($memory_percentage, 2) . "%; background-color:green; height:20px;'></div>
            </div>";

    } else {
            echo "No se pudieron obtener las estadísticas del servidor Redis.";
        }
    }

    // Prueba básica de caché usando Redis
    public function prueba_cache() {
        // Cambiar el driver de cache a Redis
        $this->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);

        $key = 'test_key';
        $data = 'Hola Caché en Redis';

        // Guardar en caché
        if ($this->cache->save($key, $data, 300)) { // 300 segundos = 5 minutos
            echo "Datos guardados en caché (Redis).<br>";
        } else {
            echo "Fallo al guardar en caché (Redis).<br>";
        }

        // Obtener de caché
        $cached_data = $this->cache->get($key);
        if ($cached_data) {
            echo "Datos en caché: " . $cached_data;
        } else {
            echo "No se encontraron datos en caché.";
        }
    }

}