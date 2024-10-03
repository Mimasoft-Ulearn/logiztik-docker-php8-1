<?php

class Project_rel_footprints_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'proyecto_rel_huellas';
        parent::__construct($this->table);
    }
	
    /* Elimina la(s) huella relacionada a un proyecto */
    function delete_footprints_related_to_project($project_id){
        
        $project_rel_footprints_table = $this->db->dbprefix('proyecto_rel_huellas');
        $sql = "DELETE FROM $project_rel_footprints_table WHERE";
        $sql .= " $project_rel_footprints_table.id_proyecto = $project_id";
        
        if($this->db->query($sql)){
            return true;
        } else {
            return false;
        }
    }
	
	//Función update a 1 o eliminado
	function delete_footprints_rel_project($project_id) {
        
        $project_rel_footprints_table = $this->db->dbprefix('proyecto_rel_huellas');
        $delete_footprints_rel = "UPDATE $project_rel_footprints_table SET $project_rel_footprints_table.deleted=1 WHERE $project_rel_footprints_table.id_proyecto=$project_id; ";
        $this->db->query($delete_footprints_rel);
    }
	
	
	//Función para obtener las huellas de un proyecto.
	function get_footprints_of_project($project_id, $options = array()) {
        
        $project_rel_footprints_table = $this->db->dbprefix('proyecto_rel_huellas');
		
		$footprints_db = getFCBD();
		$footprints_table = $this->load->database(getFCBD(), TRUE)->dbprefix('huellas');
		
        $this->db->query('SET SQL_BIG_SELECTS=1'); 

		$where = "";
		$footprint_ids = get_array_value($options, "footprint_ids");
        if (count($footprint_ids)) {
            $where .= " AND $footprints_table.id IN (".implode(',', $footprint_ids).") ";
        }

		$sql = "SELECT $footprints_table.*";
		$sql .= " FROM $project_rel_footprints_table, $footprints_db.$footprints_table";
		$sql .= " WHERE";
		$sql .= " $footprints_table.id = $project_rel_footprints_table.id_huella";
		$sql .= " AND $project_rel_footprints_table.id_proyecto = $project_id";
		$sql .= " AND $project_rel_footprints_table.deleted = 0";
		$sql .= " AND $footprints_table.deleted = 0";
		$sql .= $where;
		$sql .= " ORDER BY $project_rel_footprints_table.id";
		
        return $this->db->query($sql);
    }

	
	function get_footprints_of_project_json($project_id, $options = array()){
	
		$fields_for_table = $this->get_footprints_of_project($project_id, $options)->result();

        $json_string = "";
        foreach ($fields_for_table as $column) {
			$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
				"id_cliente" => $this->login_user->client_id, 
				"id_proyecto" => $this->session->project_context, 
				"id_tipo_unidad" => $column->id_tipo_unidad, 
				"deleted" => 0
			))->id_unidad;

			$nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;
			
            $json_string .= ',' . '{"title":"' . $column->nombre . '<br /> ('.$nombre_unidad_huella.' '.$column->indicador.')", "class": "text-right dt-head-center"}';
        }

        return $json_string;
		
	}

}
