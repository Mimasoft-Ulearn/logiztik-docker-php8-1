<?php

class AC_Feeders_societies_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_feeders_sociedades';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
		
        $ac_feeders_sociedades_table = $this->db->dbprefix('ac_feeders_sociedades');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $ac_feeders_sociedades_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $ac_feeders_sociedades_table.id_cliente = $id_cliente";
        }
		
		$nombre_sociedad = get_array_value($options, "nombre_sociedad");
        if ($nombre_sociedad) {
            $where .= " AND $ac_feeders_sociedades_table.nombre_sociedad = '$nombre_sociedad'";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $ac_feeders_sociedades_table.client_area = '$client_area'";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $ac_feeders_sociedades_table.* FROM $ac_feeders_sociedades_table WHERE";
		$sql .= " $ac_feeders_sociedades_table.deleted=0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }	
	
}
