<?php

class Contingencies_correction_record_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'contingencias_correccion';
        parent::__construct($this->table);
    }

    function get_details($options = array()){
        $contingencies_table = $this->db->dbprefix('contingencias_correccion');
        $contingencies_event_table = $this->db->dbprefix('contingencias_evento');

        $where = "";
        $id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $contingencies_event_table.id_proyecto = $id_proyecto";
        }
		
        $sql = "SELECT $contingencies_table.*";
        $sql .= " FROM $contingencies_table";
        $sql .= " LEFT JOIN $contingencies_event_table ON $contingencies_table.id_contingencia_evento = $contingencies_event_table.id";
        $sql .= " WHERE $contingencies_table.deleted = 0";
        $sql .= $where;

        return $this->db->query($sql);
    }
}