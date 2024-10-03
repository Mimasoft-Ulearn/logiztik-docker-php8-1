<?php

class AC_Execution_records_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_registros_ejecucion';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()){
		
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');
		$registros_ejecucion_table = $this->db->dbprefix('ac_registros_ejecucion');
		
		$where = "";
		
		$id = get_array_value($options, "id");
        if ($id) {
			$where .= " AND $registros_ejecucion_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
			$where .= " AND $informacion_table.id_cliente = $id_cliente";
			$where .= " AND $registros_ejecucion_table.id_cliente = $id_cliente";
			$where .= " AND $beneficiarios_table.id_cliente = $id_cliente";
        }
		
		$id_informacion = get_array_value($options, "id_informacion");
        if ($id_informacion) {
            $where .= " AND $informacion_table.id = $id_informacion";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
			$where .= " AND $informacion_table.client_area = '$client_area'";
			$where .= " AND $registros_ejecucion_table.client_area = '$client_area'";
			$where .= " AND $beneficiarios_table.client_area = '$client_area'";
        }
		
		$id_macrozona = get_array_value($options, "id_macrozona");
        if ($id_macrozona) {
            $where .= " AND $informacion_table.id_macrozona = $id_macrozona";
        }
		
		$id_feeder_central = get_array_value($options, "id_feeder_central");
        if ($id_feeder_central) {
            $where .= " AND $informacion_table.id_feeder_central = $id_feeder_central";
        }
		
		$ejecutor = get_array_value($options, "ejecutor");
		if($ejecutor){
			//$where .= " AND $informacion_table.ejecutor LIKE '%\"$ejecutor\"%'";
			$where .= " AND JSON_CONTAINS($informacion_table.ejecutor, '\"$ejecutor\"')";
		}	
		
		$id_comuna = get_array_value($options, "id_comuna");
		if($id_comuna){
			$where .= " AND $informacion_table.id_comuna = $id_comuna";
		}	
				
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $registros_ejecucion_table.id, $registros_ejecucion_table.fecha_ejecucion, $informacion_table.id_macrozona AS id_macrozona_informacion, $informacion_table.nombre_convenio,";
		$sql .= " $beneficiarios_table.nombre_beneficiario, $registros_ejecucion_table.tipo_ejecucion, $registros_ejecucion_table.descripcion_info_adicional, $registros_ejecucion_table.id_informacion,";
		$sql .= " $informacion_table.id_feeder_central, $registros_ejecucion_table.created, $registros_ejecucion_table.modified, $informacion_table.ejecutor, $informacion_table.id_comuna, $registros_ejecucion_table.id_cliente";
		$sql .= " FROM $registros_ejecucion_table, $informacion_table, $beneficiarios_table";
		$sql .= " WHERE $registros_ejecucion_table.id_informacion = $informacion_table.id";
		$sql .= " AND $informacion_table.id_beneficiario = $beneficiarios_table.id";
		$sql .= " AND $informacion_table.deleted = 0";
		$sql .= " AND $registros_ejecucion_table.deleted = 0";
		$sql .= " AND $beneficiarios_table.deleted = 0";
		$sql .= " $where";
		$sql .= " ORDER BY $registros_ejecucion_table.created DESC";

        return $this->db->query($sql);
		
	}
	
	function get_historical_executions($options = array()){
		
		$registros_ejecucion_table = $this->db->dbprefix('ac_registros_ejecucion');
		
		$where = "";
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $registros_ejecucion_table.id_cliente = $id_cliente";
        }
		
		$id_informacion = get_array_value($options, "id_informacion");
        if ($id_informacion) {
            $where .= " AND $registros_ejecucion_table.id_informacion = $id_informacion";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $registros_ejecucion_table.client_area = '$client_area'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $registros_ejecucion_table.*";
		$sql .= " FROM $registros_ejecucion_table";
		$sql .= " WHERE $registros_ejecucion_table.deleted = 0";
		$sql .= " $where";
		$sql .= " ORDER BY $registros_ejecucion_table.fecha_ejecucion DESC";

		return $this->db->query($sql);
		
	}

}
