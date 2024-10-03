<?php

class AC_Configuration_associated_payments_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_configuracion_pagos_asociados';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()){
		
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		$beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');
		$configuracion_pagos_asociados_table = $this->db->dbprefix('ac_configuracion_pagos_asociados');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		
		$where = "";
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $informacion_table.id_cliente = $id_cliente";
        }
		
		$id_informacion = get_array_value($options, "id_informacion");
        if ($id_informacion) {
            $where .= " AND $informacion_table.id = $id_informacion";
        }
		
		$id_macrozona = get_array_value($options, "id_macrozona");
        if ($id_macrozona) {
            $where .= " AND $informacion_table.id_macrozona = $id_macrozona";
        }
		
		$id_beneficiario = get_array_value($options, "id_beneficiario");
        if ($id_beneficiario) {
            $where .= " AND $informacion_table.id_beneficiario = $id_beneficiario";
        }
		
		$ejecutor = get_array_value($options, "ejecutor");
        if ($ejecutor) {
			//$where .= " AND $informacion_table.ejecutor LIKE '%\"$ejecutor\"%'";
			$where .= " AND JSON_CONTAINS($informacion_table.ejecutor, '\"$ejecutor\"')";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $informacion_table.client_area = '$client_area'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT * FROM ";
		$sql .= "(";
        $sql .= "SELECT $informacion_table.id as id_informacion, $informacion_table.id_comuna, $macrozonas_table.nombre as nombre_macrozona, $informacion_table.nombre_convenio,";
		$sql .= " $beneficiarios_table.nombre_beneficiario, $informacion_table.ejecutor, $configuracion_pagos_asociados_table.fecha_registro, $informacion_table.client_area,";
		$sql .= " $configuracion_pagos_asociados_table.id AS id_pago_asociado, $configuracion_pagos_asociados_table.id_configuracion, $configuracion_pagos_asociados_table.total_pago_asociado,";
		$sql .= " $macrozonas_table.id AS id_macrozona";
		$sql .= " FROM $informacion_table";
		$sql .= " LEFT JOIN $configuracion_table ON $configuracion_table.id_informacion_convenio = $informacion_table.id ";
		$sql .= " LEFT JOIN $configuracion_pagos_asociados_table ON $configuracion_pagos_asociados_table.id_informacion = $informacion_table.id";
		$sql .= " LEFT JOIN $beneficiarios_table ON $beneficiarios_table.id = $informacion_table.id_beneficiario";
		$sql .= " LEFT JOIN $macrozonas_table ON $macrozonas_table.id = $informacion_table.id_macrozona";
		$sql .= " WHERE $informacion_table.deleted = 0 AND";
		$sql .= " $configuracion_table.id IS NOT NULL ";
		$sql .= " $where";
		$sql .= " ORDER BY $configuracion_pagos_asociados_table.created DESC";
		$sql .= ") AS tabla_virtual";
		$sql .= " GROUP BY id_informacion";
		
        return $this->db->query($sql);
		
	}
	
	function get_total_pagos_by_macrozone($id_cliente, $macrozona, $client_area, $estado = NULL){
		
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		$macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		$configuracion_pagos_asociados_table = $this->db->dbprefix('ac_configuracion_pagos_asociados');
		
		$where = "";
		if ($estado) {
            $where .= " AND $configuracion_pagos_asociados_table.estado_pago = '$estado'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT COUNT($configuracion_pagos_asociados_table.id) AS total_pagos ";
		$sql .= "FROM $configuracion_table, $macrozonas_table, $configuracion_pagos_asociados_table ";
		$sql .= "WHERE ";
		$sql .= "$configuracion_table.deleted = 0 AND ";
		$sql .= "$configuracion_table.id_cliente = $id_cliente AND ";
		$sql .= "$configuracion_table.client_area = '$client_area' AND ";
		$sql .= "$configuracion_table.id_macrozona = ac_macrozonas.id AND ";
		$sql .= "$macrozonas_table.deleted = 0 AND ";
		$sql .= "$macrozonas_table.nombre = '$macrozona' AND ";
		$sql .= "$configuracion_pagos_asociados_table.deleted = 0 AND ";
		$sql .= "$configuracion_pagos_asociados_table.id_configuracion = ac_configuracion.id ";
		$sql .= "$where";
		
        return $this->db->query($sql)->row()->total_pagos;
		
	}

	function get_total_pagos_by_comuna($id_cliente, $macrozona, $client_area, $estado = NULL){
		
		$comunas_table = $this->db->dbprefix('ac_comunas');
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		$macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		$configuracion_pagos_asociados_table = $this->db->dbprefix('ac_configuracion_pagos_asociados');
		
		$where = "";
		if ($estado) {
            $where .= " AND $configuracion_pagos_asociados_table.estado_pago = '$estado'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $comunas_table.id, $comunas_table.nombre, COUNT($configuracion_pagos_asociados_table.id) AS total_comuna ";
		$sql .= "FROM $comunas_table, $informacion_table, $configuracion_table, $macrozonas_table, $configuracion_pagos_asociados_table ";
		$sql .= "WHERE ";
		$sql .= "$informacion_table.deleted = 0 AND ";
		$sql .= "$comunas_table.deleted = 0 AND ";
		$sql .= "$comunas_table.id = $informacion_table.id_comuna AND ";
		$sql .= "$configuracion_table.id_informacion_convenio = $informacion_table.id AND ";
		$sql .= "$configuracion_table.deleted = 0 AND ";
		$sql .= "$configuracion_table.id_cliente = $id_cliente AND ";
		$sql .= "$configuracion_table.client_area = '$client_area' AND ";
		$sql .= "$configuracion_table.id_macrozona = $macrozonas_table.id AND ";
		$sql .= "$macrozonas_table.deleted = 0 AND ";
		$sql .= "$macrozonas_table.nombre = '$macrozona' AND ";
		$sql .= "$configuracion_pagos_asociados_table.deleted = 0 AND ";
		$sql .= "$configuracion_pagos_asociados_table.id_configuracion = $configuracion_table.id ";
		$sql .= "$where ";
		$sql .= "GROUP BY $comunas_table.nombre ";
		$sql .= "ORDER BY $comunas_table.id ";
		
		return $this->db->query($sql);
		
	}
	
	function get_total_pagos_territory($client_area, $id_cliente, $id_central = NULL, $estado = NULL){
		
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		$macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		$configuracion_pagos_asociados_table = $this->db->dbprefix('ac_configuracion_pagos_asociados');
		$informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');
		
		$where = "";
		if ($id_central) {
            $where .= " AND $informacion_table.id_feeder_central = $id_central";
        }
		if ($estado) {
            $where .= " AND $configuracion_pagos_asociados_table.estado_pago = '$estado'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT COUNT($configuracion_pagos_asociados_table.id) AS total_pagos ";
		$sql .= "FROM $informacion_table ";
		$sql .= "INNER JOIN $configuracion_table ON $configuracion_table.id_informacion_convenio = $informacion_table.id ";
		$sql .= "INNER JOIN $macrozonas_table ON $configuracion_table.id_macrozona = $macrozonas_table.id ";
		$sql .= "INNER JOIN $configuracion_pagos_asociados_table ON $configuracion_table.id = $configuracion_pagos_asociados_table.id_configuracion ";
		$sql .= "LEFT JOIN $informacion_cierres_table ON $informacion_table.id = $informacion_cierres_table.id_informacion ";
		$sql .= "WHERE ";
		$sql .= "$informacion_table.deleted = 0 AND ";
		$sql .= "$configuracion_table.deleted = 0 AND ";
		$sql .= "$configuracion_table.id_cliente = $id_cliente AND ";
		$sql .= "$configuracion_table.client_area = 'territory' AND ";
		$sql .= "$configuracion_table.id_macrozona = $macrozonas_table.id AND ";
		$sql .= "$macrozonas_table.deleted = 0 AND ";
		$sql .= "$macrozonas_table.id != 4 AND ";
		$sql .= "$configuracion_pagos_asociados_table.deleted = 0 AND ";
		$sql .= "$informacion_cierres_table.id IS NULL";
		$sql .= "$where";
				
        return $this->db->query($sql)->row()->total_pagos;
		
	}
	
	function get_total_pagos_por_comuna_territory($client_area, $id_cliente, $id_comuna = NULL, $estado = NULL){
		
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		$comunas_table = $this->db->dbprefix('ac_comunas');
		$configuracion_pagos_asociados_table = $this->db->dbprefix('ac_configuracion_pagos_asociados');
		$informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');
		
		$where = "";
		if ($id_comuna) {
            $where .= " AND $comunas_table.id = $id_comuna";
        }
		if ($estado) {
            $where .= " AND $configuracion_pagos_asociados_table.estado_pago = '$estado'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT COUNT($configuracion_pagos_asociados_table.id) AS total_pagos ";
		$sql .= "FROM $informacion_table ";
		$sql .= "INNER JOIN $configuracion_table ON $configuracion_table.id_informacion_convenio = $informacion_table.id ";
		$sql .= "INNER JOIN $comunas_table ON $informacion_table.id_comuna = $comunas_table.id ";
		$sql .= "INNER JOIN $configuracion_pagos_asociados_table ON $configuracion_table.id = $configuracion_pagos_asociados_table.id_configuracion ";
		$sql .= "LEFT JOIN $informacion_cierres_table ON $informacion_table.id = $informacion_cierres_table.id_informacion ";
		$sql .= "WHERE ";
		$sql .= "$informacion_table.deleted = 0 AND ";
		$sql .= "$configuracion_table.deleted = 0 AND ";
		$sql .= "$configuracion_table.id_cliente = $id_cliente AND ";
		$sql .= "$configuracion_table.client_area = 'territory' AND ";
		$sql .= "$comunas_table.deleted = 0 AND ";
		$sql .= "$configuracion_pagos_asociados_table.deleted = 0 AND ";
		$sql .= "$informacion_cierres_table.id IS NULL";
		$sql .= "$where";
				
        return $this->db->query($sql)->row()->total_pagos;
		
	}
	
	function get_total_pagos_distribution($client_area, $id_cliente, $id_comuna = NULL, $estado = NULL){
		
		$informacion_table = $this->db->dbprefix('ac_informacion');
		$configuracion_table = $this->db->dbprefix('ac_configuracion');
		//$comunas_table = $this->db->dbprefix('ac_comunas');
		$configuracion_pagos_asociados_table = $this->db->dbprefix('ac_configuracion_pagos_asociados');
		$informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');
		
		$where = "";
		if ($id_comuna) {
            $where .= " AND $informacion_table.id_comuna = $id_comuna";
        }
		if ($estado) {
            $where .= " AND $configuracion_pagos_asociados_table.estado_pago = '$estado'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT COUNT($configuracion_pagos_asociados_table.id) AS total_pagos ";
		$sql .= "FROM $informacion_table ";
		$sql .= "INNER JOIN $configuracion_table ON $configuracion_table.id_informacion_convenio = $informacion_table.id ";
		//$sql .= "INNER JOIN $comunas_table ON $informacion_table.id_comuna = $comunas_table.id ";
		$sql .= "INNER JOIN $configuracion_pagos_asociados_table ON $configuracion_table.id = $configuracion_pagos_asociados_table.id_configuracion ";
		$sql .= "LEFT JOIN $informacion_cierres_table ON $informacion_table.id = $informacion_cierres_table.id_informacion ";
		$sql .= "WHERE ";
		$sql .= "$informacion_table.deleted = 0 AND ";
		$sql .= "$configuracion_table.deleted = 0 AND ";
		$sql .= "$configuracion_table.id_cliente = $id_cliente AND ";
		$sql .= "$configuracion_table.client_area = 'distribution' AND ";
		$sql .= "$configuracion_pagos_asociados_table.deleted = 0 AND ";
		$sql .= "$informacion_cierres_table.id IS NULL";
		$sql .= "$where";
						
        return $this->db->query($sql)->row()->total_pagos;
		
	}
	
}
