<?php

class AC_Information_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_informacion';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
        $informacion_table = $this->db->dbprefix('ac_informacion');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $informacion_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $informacion_table.id_cliente = $id_cliente";
        }
		
		$id_macrozona = get_array_value($options, "id_macrozona");
        if ($id_macrozona) {
            $where .= " AND $informacion_table.id_macrozona = $id_macrozona";
        }
		
		$id_beneficiario = get_array_value($options, "id_beneficiario");
        if ($id_beneficiario) {
            $where .= " AND $informacion_table.id_beneficiario = $id_beneficiario";
        }
		
		$id_comuna = get_array_value($options, "id_comuna");
        if ($id_comuna) {
            $where .= " AND $informacion_table.id_comuna = $id_comuna";
        }
		
		$categoria = get_array_value($options, "categoria");
        if ($categoria) {
            $where .= " AND $informacion_table.categoria = '$categoria'";
        }
		
		$ejecutor = get_array_value($options, "ejecutor");
        if($ejecutor) {
			//$where .= " AND $informacion_table.ejecutor LIKE '%\"$ejecutor\"%'";
			$where .= " AND JSON_CONTAINS($informacion_table.ejecutor, '\"$ejecutor\"')";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $informacion_table.client_area = '$client_area'";
        }
		
		$id_feeder_central = get_array_value($options, "id_feeder_central");
        if ($id_feeder_central) {
            $where .= " AND $informacion_table.id_feeder_central = $id_feeder_central";
        }
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $informacion_table.* FROM $informacion_table WHERE";
		$sql .= " $informacion_table.deleted = 0";
		$sql .= " $where";
		$sql .= " ORDER BY $informacion_table.created DESC";
		
        return $this->db->query($sql);
		
    }
	
	function get_summary($options = array()){
		
		$ac_informacion_table = $this->db->dbprefix('ac_informacion');
		$ac_feeders_centrales_table = $this->db->dbprefix('ac_feeders_centrales');
		$ac_beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');
		$ac_configuracion_table = $this->db->dbprefix('ac_configuracion');
		$ac_informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');
		
		$where = "";
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $ac_informacion_table.id_cliente = $id_cliente";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $ac_informacion_table.client_area = '$client_area'";
        }
		
		$id_informacion = get_array_value($options, "id_informacion");
        if ($id_informacion) {
            $where .= " AND $ac_informacion_table.id = $id_informacion";
        }
		
		$sql = "SELECT $ac_informacion_table.id, $ac_informacion_table.nombre_convenio, $ac_feeders_centrales_table.nombre_central, $ac_beneficiarios_table.nombre_beneficiario,";
		$sql .= " $ac_configuracion_table.inicio, $ac_configuracion_table.termino, $ac_informacion_cierres_table.id AS id_informacion_cierre,";
		$sql .= " $ac_configuracion_table.id AS id_configuracion, $ac_informacion_table.id_macrozona, $ac_informacion_table.id_cliente";
		$sql .= " FROM $ac_informacion_table";
		$sql .= " LEFT JOIN $ac_configuracion_table ON $ac_informacion_table.id = $ac_configuracion_table.id_informacion_convenio";
		$sql .= " INNER JOIN $ac_beneficiarios_table ON $ac_informacion_table.id_beneficiario = $ac_beneficiarios_table.id";
		$sql .= " INNER JOIN $ac_feeders_centrales_table ON $ac_informacion_table.id_feeder_central = $ac_feeders_centrales_table.id";
		$sql .= " LEFT JOIN $ac_informacion_cierres_table ON $ac_informacion_table.id = $ac_informacion_cierres_table.id_informacion";
		$sql .= " WHERE $ac_informacion_table.deleted = 0";
		$sql .= " $where";
		$sql .= " ORDER BY $ac_informacion_table.created DESC";
								
		return $this->db->query($sql);
		
	}
	
	// DASHBOARD CONVENIOS
	
	function get_total_convenios_configurados($id_cliente, $client_area){
		
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		$informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT COUNT(*) AS total";
		$sql .= " FROM (SELECT $informacion_table.id";
		$sql .= " FROM $informacion_table";
		$sql .= " INNER JOIN $configuracion_table ON $informacion_table.id = $configuracion_table.id_informacion_convenio";
		$sql .= " LEFT JOIN $informacion_cierres_table ON $informacion_table.id = $informacion_cierres_table.id_informacion";
		$sql .= " WHERE $informacion_table.id_cliente = $id_cliente";
		$sql .= " AND $informacion_table.client_area = '$client_area'";
		$sql .= " AND $informacion_table.deleted = 0";
		$sql .= " AND $configuracion_table.id_cliente = $id_cliente";
		$sql .= " AND $configuracion_table.client_area = '$client_area'";
		$sql .= " AND $configuracion_table.deleted = 0";
		$sql .= " AND $informacion_cierres_table.id IS NULL";
		$sql .= " GROUP BY $informacion_table.id) AS agrupado";
				
		return $this->db->query($sql)->row()->total;
		
	}
	
	function get_total_convenios_configurados_por_comuna($id_cliente, $client_area, $id_comuna){
		
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		$informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');
		$comunas_table = $this->db->dbprefix('ac_comunas');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT COUNT($informacion_table.id) AS total_comuna FROM $informacion_table";
		$sql .= " INNER JOIN $configuracion_table ON $informacion_table.id = $configuracion_table.id_informacion_convenio";
		$sql .= " INNER JOIN $comunas_table ON $informacion_table.id_comuna = $comunas_table.id";
		$sql .= " LEFT JOIN $informacion_cierres_table ON $informacion_table.id = $informacion_cierres_table.id_informacion";
		$sql .= " WHERE $informacion_table.id_cliente = $id_cliente";
		$sql .= " AND $informacion_table.client_area = '$client_area'";
		$sql .= " AND $informacion_table.deleted = 0";
		$sql .= " AND $configuracion_table.id_cliente = $id_cliente";
		$sql .= " AND $configuracion_table.client_area = '$client_area'";
		$sql .= " AND $configuracion_table.deleted = 0";
		$sql .= " AND $comunas_table.id = $id_comuna";
		$sql .= " AND $informacion_cierres_table.id IS NULL";
				
		return $this->db->query($sql)->row()->total_comuna;
		
	}

}
