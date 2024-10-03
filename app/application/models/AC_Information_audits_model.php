<?php

class AC_Information_audits_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_auditorias_informacion';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
        $auditorias_informacion_table = $this->db->dbprefix('ac_auditorias_informacion');
		$informacion_table = $this->db->dbprefix('ac_informacion');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $auditorias_informacion_table.id = $id";
        }
		
		$auditado = get_array_value($options, "auditado");
        if ($auditado) {
            $where .= " AND $auditorias_informacion_table.auditado = $auditado";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $informacion_table.client_area = '$client_area'";
			$where .= " AND $auditorias_informacion_table.client_area = '$client_area'";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $informacion_table.id_cliente = $id_cliente";
        }
		
		$id_central = get_array_value($options, "id_central");
        if ($id_central) {
            $where .= " AND $informacion_table.id_feeder_central = $id_central";
        }
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $informacion_table.*, $auditorias_informacion_table.*, $informacion_table.id as id_informacion ";
		$sql .= " FROM $informacion_table, $auditorias_informacion_table WHERE";
		$sql .= " $informacion_table.deleted = 0 AND $auditorias_informacion_table.deleted = 0 AND";
		$sql .= " $informacion_table.id_auditoria = $auditorias_informacion_table.id";
		$sql .= " $where";
				
        return $this->db->query($sql);
		
    }
	
	// DASHBOARD CONVENIOS
	
	function get_total_convenios_aprobados($id_cliente, $client_area) {
		
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		$informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT COUNT(*) AS total";
		$sql .= " FROM (SELECT $informacion_table.id";
		$sql .= " FROM $informacion_table";
		$sql .= " LEFT JOIN $configuracion_table ON $informacion_table.id = $configuracion_table.id_informacion_convenio";
		$sql .= " LEFT JOIN $informacion_cierres_table ON $informacion_table.id = $informacion_cierres_table.id_informacion";
		$sql .= " WHERE $informacion_table.deleted = 0 AND $configuracion_table.deleted = 0";
		$sql .= " AND $informacion_table.id_cliente = $id_cliente AND $configuracion_table.id_cliente = $id_cliente";
		$sql .= " AND $informacion_table.client_area = '$client_area'";
		$sql .= " AND $configuracion_table.client_area = $informacion_table.client_area";
		$sql .= " AND $informacion_cierres_table.id IS NULL";
		$sql .= " GROUP BY $informacion_table.id) AS agrupado";
				
        return $this->db->query($sql)->row()->total;
		
    }
	
	function get_total_convenios_aprobados_by_macrozone($id_cliente, $macrozona, $client_area) {
		
        $macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT COUNT($informacion_table.id) AS total_zona FROM $informacion_table, $configuracion_table, $macrozonas_table";
		$sql .= " WHERE";
		$sql .= " $configuracion_table.id_informacion_convenio = $informacion_table.id AND";
		$sql .= " $informacion_table.id_cliente = $id_cliente AND";
		$sql .= " $configuracion_table.id_cliente = $informacion_table.id_cliente AND";
		$sql .= " $informacion_table.deleted = 0 AND";
		$sql .= " $configuracion_table.deleted = 0 AND";
		$sql .= " $configuracion_table.id_macrozona = $informacion_table.id_macrozona AND";
		$sql .= " $macrozonas_table.id = $informacion_table.id_macrozona AND";
		$sql .= " $macrozonas_table.nombre = '$macrozona' AND";
		$sql .= " $informacion_table.client_area = '$client_area' AND";
		$sql .= " $configuracion_table.client_area = $informacion_table.client_area";
		
        return $this->db->query($sql)->row()->total_zona;
		
    }
	
	function get_total_convenios_auditados_by_macrozone($id_cliente, $macrozona, $client_area) {
		
        $macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		$informacion_audits_table = $this->db->dbprefix('ac_auditorias_informacion');
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT COUNT($informacion_audits_table.id) AS total_zona FROM $macrozonas_table";
		$sql .= " LEFT JOIN $informacion_table ON $macrozonas_table.id = $informacion_table.id_macrozona";
		$sql .= " LEFT JOIN $configuracion_table ON $informacion_table.id = $configuracion_table.id_informacion_convenio";
		$sql .= " LEFT JOIN $informacion_audits_table ON $informacion_table.id_auditoria = $informacion_audits_table.id";
		$sql .= " WHERE";
		$sql .= " $macrozonas_table.deleted = 0 AND";
		$sql .= " $informacion_table.deleted = 0 AND";
		$sql .= " $configuracion_table.deleted = 0 AND";
		$sql .= " $informacion_audits_table.deleted = 0 AND";
		$sql .= " $informacion_table.id_cliente = $id_cliente AND";
		$sql .= " $informacion_audits_table.id_cliente = $id_cliente AND";
		$sql .= " $informacion_audits_table.auditado = 1 AND";
		$sql .= " $macrozonas_table.nombre = '$macrozona' AND";
		$sql .= " $informacion_table.client_area = '$client_area' AND";
		$sql .= " $informacion_audits_table.client_area = $informacion_table.client_area";
		
        return $this->db->query($sql)->row()->total_zona;
		
    }
	
	function get_total_convenios_auditados_territory($client_area, $id_cliente, $id_central = NULL){
		
        $macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		$informacion_audits_table = $this->db->dbprefix('ac_auditorias_informacion');
		$informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');
		
		$where = "";
		if ($id_central) {
            $where .= " AND $informacion_table.id_feeder_central = $id_central";
        }
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT COUNT($informacion_audits_table.id) AS total_zona FROM $macrozonas_table";
		$sql .= " LEFT JOIN $informacion_table ON $macrozonas_table.id = $informacion_table.id_macrozona";
		$sql .= " LEFT JOIN $configuracion_table ON $informacion_table.id = $configuracion_table.id_informacion_convenio";
		$sql .= " LEFT JOIN $informacion_audits_table ON $informacion_table.id_auditoria = $informacion_audits_table.id";
		$sql .= " LEFT JOIN $informacion_cierres_table ON $informacion_table.id = $informacion_cierres_table.id_informacion";
		$sql .= " WHERE";
		$sql .= " $macrozonas_table.deleted = 0 AND";
		$sql .= " $informacion_table.deleted = 0 AND";
		$sql .= " $configuracion_table.deleted = 0 AND";
		$sql .= " $informacion_audits_table.deleted = 0 AND";
		$sql .= " $informacion_table.id_cliente = $id_cliente AND";
		$sql .= " $informacion_audits_table.id_cliente = $id_cliente AND";
		$sql .= " $informacion_audits_table.auditado = 1 AND";
		$sql .= " $macrozonas_table.nombre != 4 AND";
		$sql .= " $informacion_table.client_area = '$client_area' AND";
		$sql .= " $informacion_audits_table.client_area = $informacion_table.client_area AND";
		$sql .= " $informacion_cierres_table.id IS NULL";
		$sql .= "$where";
				
        return $this->db->query($sql)->row()->total_zona;
		
    }
	
	// DASHBOARD DISTRIBUCION - CONVENIOS
	
	function get_total_comunas_num_informaciones($id_cliente, $macrozona, $client_area) {
		
        $macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		$comunas_table = $this->db->dbprefix('ac_comunas');
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		$informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		/*
        $sql = "SELECT $macrozonas_table.nombre, $comunas_table.id, $comunas_table.nombre AS comuna, COUNT(DISTINCT $informacion_table.id) AS num_informaciones ";
		$sql .= " FROM $informacion_table, $configuracion_table, $macrozonas_table, $comunas_table";
		$sql .= " WHERE";
		$sql .= " $macrozonas_table.id = $informacion_table.id_macrozona AND";
		$sql .= " $macrozonas_table.nombre = '$macrozona' AND";
		$sql .= " $comunas_table.id = $informacion_table.id_comuna AND";
		$sql .= " $configuracion_table.id_informacion_convenio = $informacion_table.id AND";
		$sql .= " $informacion_table.id_cliente = $id_cliente AND";
		$sql .= " $informacion_table.client_area = '$client_area' AND";
		$sql .= " $configuracion_table.id_cliente = $informacion_table.id_cliente AND";
		$sql .= " $configuracion_table.client_area = $informacion_table.client_area AND";
		$sql .= " $configuracion_table.id_macrozona = $informacion_table.id_macrozona";
		$sql .= " GROUP BY $macrozonas_table.id, $comunas_table.id, $informacion_table.id";
		*/

        $sql = "SELECT $macrozonas_table.nombre, $comunas_table.id, $comunas_table.nombre AS comuna, COUNT(DISTINCT $informacion_table.id) AS num_informaciones ";
		$sql .= " FROM $informacion_table";
		$sql .= " LEFT JOIN $informacion_cierres_table ON $informacion_table.id = $informacion_cierres_table.id_informacion";
		$sql .= " INNER JOIN $macrozonas_table ON $macrozonas_table.id = $informacion_table.id_macrozona";
		$sql .= " INNER JOIN $configuracion_table ON $informacion_table.id = $configuracion_table.id_informacion_convenio";
		$sql .= " INNER JOIN $comunas_table ON $comunas_table.id = $informacion_table.id_comuna";
		$sql .= " WHERE $configuracion_table.id_cliente = $informacion_table.id_cliente";
		$sql .= " AND $configuracion_table.client_area = $informacion_table.client_area";
		$sql .= " AND $configuracion_table.id_macrozona = $informacion_table.id_macrozona";
		$sql .= " AND $macrozonas_table.nombre = '$macrozona'";
		$sql .= " AND $informacion_table.id_cliente = $id_cliente";
		$sql .= " AND $informacion_table.client_area = '$client_area'";
		$sql .= " AND $informacion_cierres_table.id IS NULL";
		$sql .= " GROUP BY $comunas_table.id";
		
        return $this->db->query($sql);
		
    }
	
	function get_total_auditado_por_comuna($id_cliente, $macrozona, $client_area, $id_comuna) {
		
        $macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		$comunas_table = $this->db->dbprefix('ac_comunas');
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		$informacion_audits_table = $this->db->dbprefix('ac_auditorias_informacion');
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT COUNT(DISTINCT $configuracion_table.id_informacion_convenio) AS total_comuna ";
		$sql .= " FROM $comunas_table, $macrozonas_table, $informacion_table, $configuracion_table, $informacion_audits_table";
		$sql .= " WHERE";
		$sql .= " $informacion_table.id_cliente = $id_cliente AND";
		$sql .= " $informacion_table.client_area = '$client_area' AND";
		$sql .= " $informacion_table.deleted = 0 AND";
		$sql .= " $configuracion_table.id_informacion_convenio = $informacion_table.id AND";
		$sql .= " $configuracion_table.deleted = 0 AND";
		$sql .= " $informacion_audits_table.client_area = $informacion_table.client_area AND";
		$sql .= " $informacion_audits_table.id_cliente = $informacion_table.id_cliente AND";
		$sql .= " $informacion_audits_table.deleted = 0 AND";
		$sql .= " $informacion_audits_table.id = $informacion_table.id_auditoria AND";
		$sql .= " $informacion_audits_table.auditado = 1 AND";
		$sql .= " $comunas_table.id = $informacion_table.id_comuna AND";
		$sql .= " $comunas_table.deleted = 0 AND";
		$sql .= " $macrozonas_table.id = $informacion_table.id_macrozona AND";
		$sql .= " $macrozonas_table.nombre = '$macrozona' AND";
		$sql .= " $macrozonas_table.deleted = 0 AND";
		$sql .= " $comunas_table.id = $id_comuna";
				
        return $this->db->query($sql)->row()->total_comuna;
		
    }
	
	// POR CENTRALES
	
	function get_total_convenios_aprobados_by_centrals($id_cliente, $id_central, $client_area) {
		
        $macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		$informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT COUNT($informacion_table.id) AS total_zona FROM $informacion_table";
		$sql .= " LEFT JOIN $configuracion_table ON $informacion_table.id = $configuracion_table.id_informacion_convenio";
		$sql .= " LEFT JOIN $informacion_cierres_table ON $informacion_table.id = $informacion_cierres_table.id_informacion";
		$sql .= " INNER JOIN $macrozonas_table ON $informacion_table.id_macrozona = $macrozonas_table.id";
		$sql .= " WHERE";
		$sql .= " $informacion_table.id_cliente = $id_cliente AND";
		$sql .= " $configuracion_table.id_cliente = $informacion_table.id_cliente AND";
		$sql .= " $informacion_table.deleted = 0 AND";
		$sql .= " $configuracion_table.deleted = 0 AND";
		$sql .= " $configuracion_table.id_macrozona = $informacion_table.id_macrozona AND";
		$sql .= " $macrozonas_table.id = $informacion_table.id_macrozona AND";
		$sql .= " $macrozonas_table.id != 4 AND";
		$sql .= " $informacion_table.client_area = '$client_area' AND";
		$sql .= " $configuracion_table.client_area = $informacion_table.client_area AND";
		$sql .= " $informacion_cierres_table.id IS NULL AND";
		$sql .= " $informacion_table.id_feeder_central = $id_central";
				
        return $this->db->query($sql)->row()->total_zona;
		
    }
	
	function get_total_convenios_auditados_comuna_territory($client_area, $id_cliente, $id_comuna = NULL){
		
		$comunas_table = $this->db->dbprefix('ac_comunas');
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		$informacion_audits_table = $this->db->dbprefix('ac_auditorias_informacion');
		$informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');
		
		$where = "";
		if ($id_comuna) {
            $where .= " AND $comunas_table.id = $id_comuna";
        }
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT COUNT($informacion_audits_table.id) AS total_zona FROM $comunas_table";
		$sql .= " LEFT JOIN $informacion_table ON $comunas_table.id = $informacion_table.id_comuna";
		$sql .= " LEFT JOIN $configuracion_table ON $informacion_table.id = $configuracion_table.id_informacion_convenio";
		$sql .= " LEFT JOIN $informacion_audits_table ON $informacion_table.id_auditoria = $informacion_audits_table.id";
		$sql .= " LEFT JOIN $informacion_cierres_table ON $informacion_table.id = $informacion_cierres_table.id_informacion";
		$sql .= " WHERE";
		$sql .= " $comunas_table.deleted = 0 AND";
		$sql .= " $informacion_table.deleted = 0 AND";
		$sql .= " $configuracion_table.deleted = 0 AND";
		$sql .= " $informacion_audits_table.deleted = 0 AND";
		$sql .= " $informacion_table.id_cliente = $id_cliente AND";
		$sql .= " $informacion_audits_table.id_cliente = $id_cliente AND";
		$sql .= " $informacion_audits_table.auditado = 1 AND";
		$sql .= " $informacion_table.client_area = '$client_area' AND";
		$sql .= " $informacion_audits_table.client_area = $informacion_table.client_area AND";
		$sql .= " $informacion_cierres_table.id IS NULL";
		$sql .= "$where";
						
		return $this->db->query($sql)->row()->total_zona;
		
	}
	
	function get_total_convenios_aprobados_por_comuna($id_cliente, $client_area, $id_comuna = NULL) {
		
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		
		$where = "";
		if ($id_comuna) {
            $where .= " AND $informacion_table.id_comuna = $id_comuna";
        }
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT COUNT(*) AS total";
		$sql .= " FROM (SELECT $informacion_table.id";
		$sql .= " FROM $informacion_table";
		$sql .= " LEFT JOIN $informacion_cierres_table ON $informacion_table.id = $informacion_cierres_table.id_informacion";
		$sql .= " LEFT JOIN $configuracion_table ON $informacion_table.id = $configuracion_table.id_informacion_convenio";
		$sql .= " WHERE $informacion_table.deleted = 0";
		$sql .= " AND $configuracion_table.deleted = 0";
		$sql .= " AND $informacion_table.id_cliente = $id_cliente";
		$sql .= " AND $informacion_table.client_area = '$client_area'";
		$sql .= " AND $informacion_cierres_table.id IS NULL";
		$sql .= " $where";
		$sql .= " GROUP BY $informacion_table.id) AS agrupado";
				
        return $this->db->query($sql)->row()->total;
		
    }
	
	function get_acuerdos_auditados($options = array()){
		
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');
		$auditorias_informacion_table = $this->db->dbprefix('ac_auditorias_informacion');
		
		$where = "";
		$id_cliente = get_array_value($options, "id_cliente");
		if($id_cliente) {
            $where .= " AND $informacion_table.id_cliente = $id_cliente";
        }
		$id_comuna = get_array_value($options, "id_comuna");
		if($id_comuna) {
            $where .= " AND $informacion_table.id_comuna = $id_comuna";
        }
		$client_area = get_array_value($options, "client_area");
		if($client_area) {
            $where .= " AND $informacion_table.client_area = '$client_area'";
        }
		$auditado = get_array_value($options, "auditado");
		if($auditado) {
            $where .= " AND $auditorias_informacion_table.auditado = '$auditado'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $informacion_table.id AS id_informacion, $informacion_table.nombre_convenio,";
		$sql .= " $auditorias_informacion_table.auditado, $auditorias_informacion_table.observaciones";
		$sql .= " FROM $informacion_table";
		$sql .= " INNER JOIN $auditorias_informacion_table ON $informacion_table.id_auditoria = $auditorias_informacion_table.id";
		$sql .= " LEFT JOIN $informacion_cierres_table ON $informacion_table.id = $informacion_cierres_table.id_informacion";
		$sql .= " WHERE $informacion_table.deleted = 0";
		$sql .= " AND $auditorias_informacion_table.deleted = 0";
		$sql .= " AND $informacion_cierres_table.id IS NULL";
		$sql .= " $where";
				
		return $this->db->query($sql);
		
	}

}
