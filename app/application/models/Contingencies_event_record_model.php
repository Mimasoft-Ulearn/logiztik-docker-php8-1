<?php

class Contingencies_event_record_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'contingencias_evento';
        parent::__construct($this->table);
    }

    function get_details($options = array()){
        $contingencies_table = $this->db->dbprefix('contingencias_evento');

        $where = "";
        $id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $contingencies_table.id_proyecto = $id_proyecto";
        }

        $start_date = get_array_value($options, "start_date");
        $end_date = get_array_value($options, "end_date");
        if($start_date && $end_date){
			$where .= " AND DATE($contingencies_table.fecha_identificacion) >= '$start_date'";
			$where .= " AND DATE($contingencies_table.fecha_identificacion) <= '$end_date'";
		}
		
        $sql = "SELECT $contingencies_table.*";
        $sql .= " FROM $contingencies_table";
        $sql .= " WHERE $contingencies_table.deleted = 0". $where;

        return $this->db->query($sql);
    }

    function get_summary($options = array()){
        $contingencies_event_table = $this->db->dbprefix('contingencias_evento');
        $contingencies_correction_table = $this->db->dbprefix('contingencias_correccion');
        $contingencies_verification_table = $this->db->dbprefix('contingencias_verificacion');

        $where = "";
        $id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $contingencies_event_table.id_proyecto = $id_proyecto";
        }

        $sql = "SELECT $contingencies_event_table.*, $contingencies_correction_table.responsable_correccion, $contingencies_correction_table.fecha_correccion, $contingencies_verification_table.responsable_verificacion, $contingencies_verification_table.fecha_verificacion";
        $sql .= " FROM $contingencies_event_table";
        $sql .= " LEFT JOIN $contingencies_correction_table ON $contingencies_event_table.id = $contingencies_correction_table.id_contingencia_evento";
        $sql .= " LEFT JOIN $contingencies_verification_table ON $contingencies_event_table.id = $contingencies_verification_table.id_contingencia_evento";
        $sql .= " WHERE $contingencies_event_table.deleted = 0";
        // $sql .= " AND $contingencies_correction_table.deleted = 0";
        // $sql .= " AND $contingencies_verification_table.deleted = 0";
        $sql .= $where;

        return $this->db->query($sql);
    }

    function count_event_types($options = array()){
        $contingencies_event_table = $this->db->dbprefix('contingencias_evento');

        $where = "";
        $id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $contingencies_event_table.id_proyecto = $id_proyecto";
        }

        $start_date = get_array_value($options, "start_date");
        $end_date = get_array_value($options, "end_date");
        if($start_date && $end_date){
			$where .= " AND DATE($contingencies_event_table.fecha_identificacion) >= '$start_date'";
			$where .= " AND DATE($contingencies_event_table.fecha_identificacion) <= '$end_date'";
		}

        $sql = "SELECT $contingencies_event_table.tipo_evento, count(*) AS cant";
        $sql .= " FROM $contingencies_event_table";
        $sql .= " WHERE $contingencies_event_table.deleted = 0";
        $sql .= $where;
        $sql .= " GROUP BY $contingencies_event_table.tipo_evento";

        return $this->db->query($sql);
    }

    function count_categories_and_series($options = array()){
        $contingencies_event_table = $this->db->dbprefix('contingencias_evento');
        
        $where = "";

        $id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $contingencies_event_table.id_proyecto = $id_proyecto";
        }

        $sql = "SELECT $contingencies_event_table.gerencia,";
        $sql .= " SUM(CASE WHEN $contingencies_event_table.tipo_evento = 'near_incident' THEN 1 ELSE 0 END)AS near_incident,"; 
        $sql .= " SUM(CASE WHEN $contingencies_event_table.tipo_evento = 'minor_incident' THEN 1 ELSE 0 END)AS minor_incident,";
        $sql .= " SUM(CASE WHEN $contingencies_event_table.tipo_evento = 'significant_incident' THEN 1 ELSE 0 END)AS significant_incident,"; 
        $sql .= " SUM(CASE WHEN $contingencies_event_table.tipo_evento = 'environmental_damage' THEN 1 ELSE 0 END)AS environmental_damage,";
        $sql .= " SUM(CASE WHEN $contingencies_event_table.tipo_evento = 'environmental_emergency' THEN 1 ELSE 0 END)AS environmental_emergency"; 
        $sql .= " FROM $contingencies_event_table";
        $sql .= " WHERE $contingencies_event_table.deleted = 0";
        $sql .= $where;
        $sql .= " GROUP BY gerencia";

        return $this->db->query($sql);
    }
}