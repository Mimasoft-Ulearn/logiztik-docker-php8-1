<?php

class Module_availability_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'module_availability_settings';
        parent::__construct($this->table);
    }
	
	/* Crea la configuración por defecto del proyecto. Se llama a este método si un proyecto no tiene configuración */
	function save_default_settings($id_cliente, $id_proyecto){

		$clients_modules = $this->Clients_modules_model->get_details()->result();
		
		foreach($clients_modules as $mod){
			$default_module_availability = array(
				"id_cliente" => $id_cliente,
				"id_proyecto" => $id_proyecto,
				"id_modulo_cliente" => $mod->id,
				"available" => 1,
				"thresholds" => 1,
			);
			$this->save($default_module_availability);	
		}

	}
	
	function get_project_setting($id_cliente, $id_proyecto){
		
		$clients_modules_table = $this->db->dbprefix('clients_modules');
		$module_availability_settings_table = $this->db->dbprefix('module_availability_settings');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $module_availability_settings_table.*, $clients_modules_table.name, $module_availability_settings_table.id_modulo_cliente AS id
				FROM $module_availability_settings_table, $clients_modules_table WHERE";
		$sql .= " $module_availability_settings_table.id_cliente = $id_cliente";
		$sql .= " AND $module_availability_settings_table.id_proyecto = $id_proyecto";
		$sql .= " AND $module_availability_settings_table.id_modulo_cliente = $clients_modules_table.id";
		$sql .= " AND $module_availability_settings_table.deleted = 0";
		$sql .= " AND $clients_modules_table.deleted = 0";
		$sql .= " ORDER BY $clients_modules_table.sort ASC";
		//echo $sql;
		return $this->db->query($sql);
		
	}
	
	function delete_module_availability($id){
		
        $module_availability_settings = $this->db->dbprefix('module_availability_settings');
		
        $sql = "DELETE FROM $module_availability_settings WHERE";
        $sql .= " $module_availability_settings.id = $id";
		
		$this->db->query($sql);

	}
	
	
	
	
}