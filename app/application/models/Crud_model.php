<?php

// Extend from this model to execute basic DB operations
class Crud_model extends CI_Model {

    private $table;
    private $log_activity = false;
    private $log_type = "";
    private $log_for = "";
    private $log_for_key = "";
    private $log_for2 = "";
    private $log_for_key2 = "";
    protected $cache_time = 3600; // Cache duration in seconds

    public function __construct($table = null) {
        parent::__construct();
        if ($table) {
            $this->use_table($table);
        }
        $this->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);
    }

    protected function use_table($table) {
        $this->table = $table;
    }

    protected function init_activity_log(
        $log_type = "",
        $log_type_title_key = "",
        $log_for = "",
        $log_for_key = "",
        $log_for2 = "",
        $log_for_key2 = ""
    ) {
        if ($log_type) {
            $this->log_activity = true;
            $this->log_type = $log_type;
            $this->log_type_title_key = $log_type_title_key;
            $this->log_for = $log_for;
            $this->log_for_key = $log_for_key;
            $this->log_for2 = $log_for2;
            $this->log_for_key2 = $log_for_key2;
        }
    }

    // Generate cache key
    protected function generate_cache_key($function_name, $params = []) {
        ksort($params);
        $params_str = json_encode($params);
        $hashed_params = md5($params_str);
        return "{$this->table}_{$function_name}_{$hashed_params}";
    }

    // Get data with cache
    protected function get_cached_data($cache_key, $query_callback) {

        $cached_data = $this->cache->get($cache_key);
        if ($cached_data !== false) {
            return $cached_data;
        }
        $data = $query_callback();
        
        // Calcular el tamaño de los datos
        $data_size = is_array($data) ? json_encode($data) : json_encode([$data]);
        
        // verificar si se guardo el cache
        $this->cache->save($cache_key, $data, $this->cache_time);
        return $data;
    }

    protected function clear_cache($cache_key) {
        $this->cache->delete($cache_key);
    }

    public function get_one($id = 0) {
        $cache_key = $this->generate_cache_key('get_one', ['id' => $id, 'deleted' => 0]);
        $data = $this->get_cached_data($cache_key, function () use ($id) {
            return $this->get_one_where(['id' => $id, 'deleted' => 0]);
        });

        return $this->create_db_result_object([$data])->row();
    }

    public function get_one_where($where = []) {
        $cache_key = $this->generate_cache_key('get_one_where', $where);
        return $this->get_cached_data($cache_key, function () use ($where) {
            $result = $this->db->get_where($this->table, $where, 1);
            if ($result->num_rows()) {
                return $result->row();
            } else {
                $db_fields = $this->db->list_fields($this->table);
                $fields = new stdClass();
                foreach ($db_fields as $field) {
                    $fields->$field = null; // Use null instead of empty string
                }
                return $fields;
            }
        });
    }

    public function get_all($include_deleted = false) {
        $where = $include_deleted ? [] : ['deleted' => 0];
        $cache_key = $this->generate_cache_key('get_all', $where);

        $cached_data = $this->cache->get($cache_key);
        if ($cached_data !== false) {
            return $this->create_db_result_object($cached_data);
        }

        $query = $this->db->get_where($this->table, $where);
        $data = $query->result();
        $this->cache->save($cache_key, $data, $this->cache_time);
        return $this->create_db_result_object($data);
    }
    

    public function get_all_where($where = [], $limit = 1000000, $offset = 0) {
        if (isset($where['where_in']) && is_array($where['where_in'])) {
            foreach ($where['where_in'] as $key => $values) {
                $this->db->where_in($key, $values);
            }
            unset($where['where_in']);
        }

        $cache_key = $this->generate_cache_key('get_all_where', array_merge($where, ['limit' => $limit, 'offset' => $offset]));

        $cached_data = $this->cache->get($cache_key);
        if ($cached_data !== false) {
            return $this->create_db_result_object($cached_data);
        }

        $query = $this->db->get_where($this->table, $where, $limit, $offset);
        $data = $query->result();
        $this->cache->save($cache_key, $data, $this->cache_time);
        return $this->create_db_result_object($data);
    }

    // Create a proxy object that behaves like CI_DB_result
    protected function create_db_result_object($cached_data) {
        return new class($cached_data) {
            private $cached_data;

            public function __construct($cached_data) {
                $this->cached_data = $cached_data;
            }

            public function result() {
                return $this->cached_data;
            }

            public function result_array(){
                $array_result = [];
                foreach($this->cached_data as $object){
                    $array_result[] = (array) $object; // Convert each object to an array
                }
                return $array_result;
            }

            public function row($n = 0) {
                return isset($this->cached_data[$n]) ? $this->cached_data[$n] : null;
            }

            public function num_rows() {
                return is_array($this->cached_data) ? count($this->cached_data) : 0;
            }
        };
    }

    public function save(&$data = [], $id = 0) {
        if ($id) {
            // Update
            $where = ['id' => $id];
    
            // Invalidate relevant cache before update
            $this->invalidate_related_cache($where);
    
            // Log activity before update
            if ($this->log_activity) {
                $data_before_update = $this->get_one($id);
            }
    
            $success = $this->update_where($data, $where);
            if ($success) {
                if ($this->log_activity) {
                    // Log changes
                    $fields_changed = [];
                    foreach ($data as $field => $value) {
                        if (isset($data_before_update->$field) && $data_before_update->$field != $value) {
                            $fields_changed[$field] = ['from' => $data_before_update->$field, 'to' => $value];
                        }
                    }
    
                    if (!empty($fields_changed)) {
                        $log_data = [
                            "action" => "updated",
                            "log_type" => $this->log_type,
                            "log_type_title" => isset($data_before_update->{$this->log_type_title_key}) ? $data_before_update->{$this->log_type_title_key} : "",
                            "log_type_id" => $id,
                            "changes" => serialize($fields_changed),
                            "log_for" => $this->log_for,
                            "log_for_id" => isset($data_before_update->{$this->log_for_key}) ? $data_before_update->{$this->log_for_key} : 0,
                            "log_for2" => $this->log_for2,
                            "log_for_id2" => isset($data_before_update->{$this->log_for_key2}) ? $data_before_update->{$this->log_for_key2} : 0,
                        ];
                        $this->Activity_logs_model->save($log_data);
                        $activity_log_id = $this->db->insert_id();
                        $data["activity_log_id"] = $activity_log_id;
                    }
                }
    
                // Invalidate 'get_all' cache after update
                $this->clear_cache($this->generate_cache_key('get_all', ['deleted' => 0]));
                log_message('info', "Cache invalidado: 'get_all' después de actualización, clave: " . $this->generate_cache_key('get_all', ['deleted' => 0]));
    
                return $success;
            }
        } else {
            // Insert
            if ($this->db->insert($this->table, $data)) {
                $insert_id = $this->db->insert_id();
                if ($this->log_activity) {
                    // Log creation
                    $log_data = [
                        "action" => "created",
                        "log_type" => $this->log_type,
                        "log_type_title" => isset($data[$this->log_type_title_key]) ? $data[$this->log_type_title_key] : "",
                        "log_type_id" => $insert_id,
                        "log_for" => $this->log_for,
                        "log_for_id" => isset($data[$this->log_for_key]) ? $data[$this->log_for_key] : 0,
                        "log_for2" => $this->log_for2,
                        "log_for_id2" => isset($data[$this->log_for_key2]) ? $data[$this->log_for_key2] : 0,
                    ];
                    $this->Activity_logs_model->save($log_data);
                    $activity_log_id = $this->db->insert_id();
                    $data["activity_log_id"] = $activity_log_id;
                }
    
                // Invalidate 'get_all' cache after insert
                $this->clear_cache($this->generate_cache_key('get_all', ['deleted' => 0]));
                log_message('info', "Cache invalidado: 'get_all' después de inserción, clave: " . $this->generate_cache_key('get_all', ['deleted' => 0]));
    
                return $insert_id;
            }
            return false;
        }
    }

    public function bulk_load($data = []) {
        if ($this->db->insert_batch($this->table, $data)) {
            // Invalidate 'get_all' cache after bulk insert
            $this->clear_cache($this->generate_cache_key('get_all', ['deleted' => 0]));
            return true;
        } else {
            return false;
        }
    }

    public function update_where($data = [], $where = []) {
        if (!empty($where)) {
            if ($this->db->update($this->table, $data, $where)) {
                $id = isset($where['id']) ? $where['id'] : true;
                return $id;
            }
        }
        return false;
    }

    public function delete($id = 0, $undo = false) {
    $data = ['deleted' => $undo ? 0 : 1];

    // Invalidate relevant cache before deletion/undo
    $this->invalidate_related_cache(['id' => $id]);

    // Realizar la actualización (marcar como borrado o restaurado)
    $this->db->where("id", $id);
    $success = $this->db->update($this->table, $data);

    if ($success) {
        if ($this->log_activity) {
            if ($undo) {
                // Eliminar el log de eliminación anterior si se restaura
                $this->Activity_logs_model->delete_where([
                    "action" => "deleted",
                    "log_type" => $this->log_type,
                    "log_type_id" => $id
                ]);
            } else {
                // Log de eliminación
                $model_info = $this->get_one($id);
                $log_data = [
                    "action" => "deleted",
                    "log_type" => $this->log_type,
                    "log_type_title" => isset($model_info->{$this->log_type_title_key}) ? $model_info->{$this->log_type_title_key} : "",
                    "log_type_id" => $id,
                    "log_for" => $this->log_for,
                    "log_for_id" => isset($model_info->{$this->log_for_key}) ? $model_info->{$this->log_for_key} : 0,
                    "log_for2" => $this->log_for2,
                    "log_for_id2" => isset($model_info->{$this->log_for_key2}) ? $model_info->{$this->log_for_key2} : 0,
                ];
                $this->Activity_logs_model->save($log_data);
            }
        }

        // Invalidate relevant cache again after deletion/undo
        $this->invalidate_related_cache(['id' => $id]);

        log_message('info', "Cache invalidado tras la eliminación/restauración: 'get_one', clave: " . $this->generate_cache_key('get_one', ['id' => $id, 'deleted' => 0]));
    }

        return $success;
    }

    public function get_dropdown_list($option_fields = [], $key = "id", $where = []) {
        $where["deleted"] = 0;
        $list_data = $this->get_all_where($where)->result();
        $result = [];
        foreach ($list_data as $data) {
            $text = '';
            foreach ($option_fields as $option) {
                $text .= "{$data->$option} ";
            }
            $result[$data->$key] = trim($text);
        }
        return $result;
    }

    // Prepare a query string to get custom fields like a normal field
    protected function prepare_custom_field_query_string($related_to, $custom_fields, $related_to_table) {
        $join_string = "";
        $select_string = "";
        $custom_field_values_table = $this->db->dbprefix('custom_field_values');

        if ($related_to && !empty($custom_fields)) {
            foreach ($custom_fields as $cf) {
                $cf_id = $cf->id;
                $virtual_table = "cfvt_$cf_id"; // Custom field values virtual table

                $select_string .= ", {$virtual_table}.value AS cfv_{$cf_id}";
                $join_string .= " LEFT JOIN {$custom_field_values_table} AS {$virtual_table} 
                                  ON {$virtual_table}.related_to_type = '{$related_to}' 
                                  AND {$virtual_table}.related_to_id = {$related_to_table}.id 
                                  AND {$virtual_table}.deleted = 0 
                                  AND {$virtual_table}.custom_field_id = {$cf_id}";
            }
        }

        return ["select_string" => $select_string, "join_string" => $join_string];
    }


    protected function invalidate_related_cache($where) {
        // Invalidate specific record cache
        $cache_key_one = $this->generate_cache_key('get_one_where', $where);
        $this->clear_cache($cache_key_one);
        log_message('info', "Cache invalidado: 'get_one_where', clave: $cache_key_one");
    
        // Invalidate lists, considering variations (if any)
        $cache_key_all_deleted = $this->generate_cache_key('get_all', ['deleted' => 0]);
        $cache_key_all_inactive = $this->generate_cache_key('get_all', ['deleted' => 1]);
    
        $this->clear_cache($cache_key_all_deleted);
        log_message('info', "Cache invalidado: 'get_all', clave: $cache_key_all_deleted");
    
        $this->clear_cache($cache_key_all_inactive);
        log_message('info', "Cache invalidado: 'get_all', clave: $cache_key_all_inactive");
    }
}
