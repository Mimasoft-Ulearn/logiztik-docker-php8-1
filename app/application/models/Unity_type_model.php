<?php

class Unity_type_model extends Crud_bd_fc_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
		//$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
        $this->table = 'tipo_unidad';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
		
       	$tipo_unidad_table = $this->bd_mimasoft_fc->dbprefix('tipo_unidad');
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $tipo_unidad_table.* FROM $tipo_unidad_table";
		
        return $this->db->query($sql);
    }
	
	function get_unity_types_of_project($id_proyecto){
		
		/*
	
		SELECT dev_mimasoft_fc.tipo_unidad.id, dev_mimasoft_fc.tipo_unidad.nombre 
		FROM dev_mimasoft_fc.unidad, dev_mimasoft_fc.tipo_unidad, dev_mimasoft_sistema.proyecto_rel_huellas, dev_mimasoft_fc.huellas 
		WHERE dev_mimasoft_fc.tipo_unidad.id = dev_mimasoft_fc.unidad.id_tipo_unidad 
		AND dev_mimasoft_fc.huellas.id_tipo_unidad = dev_mimasoft_fc.tipo_unidad.id 
		AND dev_mimasoft_sistema.proyecto_rel_huellas.id_huella = dev_mimasoft_fc.huellas.id 
		AND dev_mimasoft_sistema.proyecto_rel_huellas.id_proyecto = 1 
		GROUP BY (dev_mimasoft_fc.huellas.id_tipo_unidad)

	   */
				
		//$bd_fc = 'dev_mimasoft_fc';
		$bd_fc = getFCBD();
		$tipo_unidad_table = $this->load->database(getFCBD(), TRUE)->dbprefix('tipo_unidad');
		$unidad_table = $this->load->database(getFCBD(), TRUE)->dbprefix('unidad');
		$huellas_table = $this->load->database(getFCBD(), TRUE)->dbprefix('huellas');
		$proyecto_rel_huellas_table = $this->db->dbprefix('proyecto_rel_huellas');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $tipo_unidad_table.id, $tipo_unidad_table.nombre";
		$sql .= " FROM $bd_fc.$unidad_table, $bd_fc.$tipo_unidad_table, $proyecto_rel_huellas_table, $bd_fc.$huellas_table WHERE";
		$sql .= " $tipo_unidad_table.id = $unidad_table.id_tipo_unidad";
		$sql .= " AND $huellas_table.id_tipo_unidad = $tipo_unidad_table.id";
		$sql .= " AND $proyecto_rel_huellas_table.id_huella = $huellas_table.id";
		$sql .= " AND $proyecto_rel_huellas_table.id_proyecto = $id_proyecto";
		$sql .= " GROUP BY ($huellas_table.id_tipo_unidad)";
		
		//var_dump($this->db->query($sql));
		return $this->db->query($sql);
		
		
	}

}
