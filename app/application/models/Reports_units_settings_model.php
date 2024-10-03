<?php

class Reports_units_settings_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'reports_units_settings';
        parent::__construct($this->table);
    }
	
	function delete_reports_units_settings($project_id, $client_id){
		
		$reports_units_settings_table = $this->db->dbprefix('reports_units_settings');
		$sql = "DELETE FROM $reports_units_settings_table WHERE";
		$sql .= " $reports_units_settings_table.id_proyecto = $project_id";
		$sql .= " AND $reports_units_settings_table.id_cliente = $client_id";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	}
	
	function save_default_settings($id_cliente, $id_proyecto){
		$tipos_de_unidad = $this->Unity_type_model->get_all()->result();
		
		foreach($tipos_de_unidad as $tipo_unidad){
			
			$unidad = $this->Unity_model->get_one_where(array("id_tipo_unidad" => $tipo_unidad->id));
			
			$default_report_units = array(
				"id_cliente" => $id_cliente,
				"id_proyecto" => $id_proyecto,
				"id_tipo_unidad" => $tipo_unidad->id,
				"id_unidad" => $unidad->id,
			);

			$this->save($default_report_units);
			
		}
	
	}
	
	function delete_reports_units_settings_by_project($id){
		
		$reports_units_settings = $this->db->dbprefix('reports_units_settings');

		$sql = "UPDATE $reports_units_settings SET $reports_units_settings.deleted=1 WHERE $reports_units_settings.id=$id; ";
		$this->db->query($sql);
	}
	
	

}