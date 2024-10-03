<?php

class AC_Information_closing_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_informacion_cierres';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
        $informacion_cierres_table = $this->db->dbprefix('ac_informacion_cierres');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $informacion_cierres_table.id = $id";
        }
		
		$id_informacion = get_array_value($options, "id_informacion");
        if ($id_informacion) {
            $where .= " AND $informacion_cierres_table.id_informacion = $id_informacion";
        }
		
		$fecha_cierre = get_array_value($options, "fecha_cierre");
        if ($fecha_cierre) {
            $where .= " AND $informacion_cierres_table.fecha_cierre = $fecha_cierre";
        }
		
		$motivo_cierre = get_array_value($options, "motivo_cierre");
        if ($motivo_cierre) {
            $where .= " AND $informacion_cierres_table.motivo_cierre = '$motivo_cierre'";
        }
		
		$comentarios = get_array_value($options, "comentarios");
        if ($comentarios) {
            $where .= " AND $informacion_cierres_table.comentarios = '$comentarios'";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $informacion_cierres_table.* FROM $informacion_cierres_table WHERE";
		$sql .= " $informacion_cierres_table.deleted = 0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }

}
