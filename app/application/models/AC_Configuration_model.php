<?php

class AC_Configuration_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_configuracion';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
        $configuracion_table = $this->db->dbprefix('ac_configuracion');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $configuracion_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $configuracion_table.id_cliente = $id_cliente";
        }
		
		$id_macrozona = get_array_value($options, "id_macrozona");
        if ($id_macrozona) {
            $where .= " AND $configuracion_table.id_macrozona = $id_macrozona";
        }
		
		$origen_budget = get_array_value($options, "origen_budget");
        if ($origen_budget) {
            $where .= " AND $configuracion_table.origen_budget = '$origen_budget'";
        }

		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $configuracion_table.client_area = '$client_area'";
        }
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $configuracion_table.* FROM $configuracion_table WHERE";
		$sql .= " $configuracion_table.deleted = 0";
		$sql .= " $where";
		$sql .= " ORDER BY $configuracion_table.created DESC";
		
        return $this->db->query($sql);
		
    }

}
