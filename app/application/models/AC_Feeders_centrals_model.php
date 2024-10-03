<?php

class AC_Feeders_centrals_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_feeders_centrales';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
		
        $ac_feeders_centrales_table = $this->db->dbprefix('ac_feeders_centrales');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $ac_feeders_centrales_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $ac_feeders_centrales_table.id_cliente = $id_cliente";
        }
		
		$id_macrozona = get_array_value($options, "id_macrozona");
        if ($id_macrozona) {
            $where .= " AND $ac_feeders_centrales_table.id_macrozona = $id_macrozona";
        }
		
		$nombre_central = get_array_value($options, "nombre_central");
        if ($nombre_central) {
            $where .= " AND $ac_feeders_centrales_table.nombre_central = '$nombre_central'";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $ac_feeders_centrales_table.client_area = '$client_area'";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $ac_feeders_centrales_table.* FROM $ac_feeders_centrales_table WHERE";
		$sql .= " $ac_feeders_centrales_table.deleted=0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }	
	
}
