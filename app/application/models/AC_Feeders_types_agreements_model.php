<?php

class AC_Feeders_types_agreements_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_feeders_tipos_acuerdo';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
        $ac_feeders_types_agreements_table = $this->db->dbprefix('ac_feeders_tipos_acuerdo');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $ac_feeders_types_agreements_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $ac_feeders_types_agreements_table.id_cliente = $id_cliente";
        }
		
		$tipo_acuerdo = get_array_value($options, "tipo_acuerdo");
        if ($tipo_acuerdo) {
            $where .= " AND $ac_feeders_types_agreements_table.tipo_acuerdo = '$tipo_acuerdo'";
        }
		
		$tipo_administracion = get_array_value($options, "tipo_administracion");
        if ($tipo_administracion) {
            $where .= " AND $ac_feeders_types_agreements_table.tipo_administracion = '$tipo_administracion'";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $ac_feeders_types_agreements_table.client_area = '$client_area'";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $ac_feeders_types_agreements_table.* FROM $ac_feeders_types_agreements_table WHERE";
		$sql .= " $ac_feeders_types_agreements_table.deleted=0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }	

}
