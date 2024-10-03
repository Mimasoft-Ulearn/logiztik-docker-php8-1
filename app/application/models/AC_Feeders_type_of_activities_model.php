<?php

class AC_Feeders_type_of_activities_model extends Crud_model{

    private $table;

    function __construct() {
        $this->table = 'ac_feeders_tipo_actividades';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
        $ac_feeders_type_of_activities_table = $this->db->dbprefix('ac_feeders_tipo_actividades');
        $ac_type_of_activities_table = $this->db->dbprefix('ac_tipo_actividades');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $ac_feeders_type_of_activities_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $ac_feeders_type_of_activities_table.id_cliente = $id_cliente";
        }
		
		$id_tipo_actividad = get_array_value($options, "id_tipo_actividad");
        if ($id_tipo_actividad) {
            $where .= " AND $ac_feeders_type_of_activities_table.id_tipo_actividad = '$id_tipo_actividad'";
        }
		
		$actividad = get_array_value($options, "actividad");
        if ($actividad) {
            $where .= " AND $ac_feeders_type_of_activities_table.actividad = '$actividad'";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $ac_feeders_type_of_activities_table.*, $ac_type_of_activities_table.name as nombre_tipo_actividad";
        $sql .= " FROM $ac_feeders_type_of_activities_table ";
        $sql .= " INNER JOIN $ac_type_of_activities_table ON $ac_feeders_type_of_activities_table.id_tipo_actividad = $ac_type_of_activities_table.id";
        $sql .= " WHERE 1";
		$sql .= " AND $ac_feeders_type_of_activities_table.deleted=0";
		$sql .= " AND $ac_type_of_activities_table.deleted=0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }

}