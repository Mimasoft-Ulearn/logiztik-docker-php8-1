<?php

class Values_compromises_reportables_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'valores_compromisos_reportables';
        parent::__construct($this->table);
    }

    function get_details($options = array(), $order = array()) {
        
		$compromises_values_table = $this->db->dbprefix('valores_compromisos_reportables');
        $compromises_table = $this->db->dbprefix('compromisos_reportables');
        $eval_cump_comp_reportables_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_reportables');
        $estados_cumplimiento_compromisos_table = $this->db->dbprefix('estados_cumplimiento_compromisos');
        $planificaciones_reportables_compromisos_table = $this->db->dbprefix('planificaciones_reportables_compromisos');
        
        $status_filter = get_array_value($options, "status_filter");

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $compromises_values_table.id = $id ";
        }
		
		$id_compromiso = get_array_value($options, "id_compromiso");
        if ($id_compromiso) {
            $where .= " AND $compromises_table.id = $id_compromiso ";
        }

        if(count($status_filter)){
            $status_filter = "'" . implode("', '", $status_filter) . "'";
            $where .= " AND $estados_cumplimiento_compromisos_table.categoria IN ($status_filter) ";
        }

        $phase_reportable_filter = get_array_value($options, "phase_reportable_filter");

        if($phase_reportable_filter){
            $where .= " AND $compromises_values_table.etapa = '$phase_reportable_filter'";
        }

        $tema_ambiental = get_array_value($options, "tema_ambiental");
        if ($tema_ambiental) {
            $where .= " AND $compromises_values_table.tema_ambiental = '$tema_ambiental' ";
        }

        $tipo_cumplimiento = get_array_value($options, "tipo_cumplimiento");
        if ($tipo_cumplimiento) {
            $where .= " AND $compromises_values_table.tipo_cumplimiento = '$tipo_cumplimiento' ";
        }

        $instrumento_gestion_ambiental = get_array_value($options, "instrumento_gestion_ambiental");
        if ($instrumento_gestion_ambiental) {
            $where .= " AND $compromises_values_table.instrumento_gestion_ambiental = '$instrumento_gestion_ambiental' ";
        }

        $afectacion_medio = get_array_value($options, "afectacion_medio_por_incumplimiento");
        if ($afectacion_medio) {
            $where .= " AND $compromises_values_table.afectacion_medio_por_incumplimiento = '$afectacion_medio' ";
        }

        $start_date = get_array_value($options, "start_date");
        $end_date = get_array_value($options, "end_date");
		if($start_date && $end_date){
			$where .= " AND DATE($planificaciones_reportables_compromisos_table.planificacion) >= '$start_date'";
			$where .= " AND DATE($planificaciones_reportables_compromisos_table.planificacion) <= '$end_date'";
		}
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		/*$sql = "SELECT $compromises_table.id_proyecto, $compromises_values_table.*
				FROM $compromises_values_table, $compromises_table 
				WHERE $compromises_table.id = $compromises_values_table.id_compromiso
				AND $compromises_values_table.deleted = 0 
				$where 
                ORDER BY $compromises_values_table.created DESC";*/

        $sql = "SELECT $compromises_table.id_proyecto, $compromises_values_table.*, MAX($eval_cump_comp_reportables_table.id) AS id_evaluacion, ";
        $sql .= " $eval_cump_comp_reportables_table.id_estados_cumplimiento_compromiso, $estados_cumplimiento_compromisos_table.categoria, ";
        $sql .= " $eval_cump_comp_reportables_table.id_valor_compromiso, $planificaciones_reportables_compromisos_table.planificacion";
        $sql .= " FROM $eval_cump_comp_reportables_table";

        $sql .= " LEFT JOIN $estados_cumplimiento_compromisos_table";
        $sql .= " ON $eval_cump_comp_reportables_table.id_estados_cumplimiento_compromiso = $estados_cumplimiento_compromisos_table.id";

        $sql .= " LEFT JOIN $compromises_values_table";
        $sql .= " ON $compromises_values_table.id = $eval_cump_comp_reportables_table.id_valor_compromiso";

        $sql .= " LEFT JOIN $compromises_table";
        $sql .= " ON $compromises_table.id = $compromises_values_table.id_compromiso";

        $sql .= " LEFT JOIN $planificaciones_reportables_compromisos_table";
        $sql .= " ON $compromises_values_table.id = $planificaciones_reportables_compromisos_table.id_compromiso";

        $sql .= " WHERE $eval_cump_comp_reportables_table.deleted = 0";
        $sql .= " AND $compromises_values_table.deleted = 0";
        $sql .= " $where";

        $sql .= " GROUP BY $eval_cump_comp_reportables_table.id_valor_compromiso";
        $sql .= " ORDER BY $compromises_values_table.created DESC";

        return $this->db->query($sql);
    }
	
	function delete_values_compromises($id){
		
		$valores_compromisos_reportables = $this->db->dbprefix('valores_compromisos_reportables');
		
        $sql = "UPDATE $valores_compromisos_reportables SET $valores_compromisos_reportables.deleted=1 WHERE $valores_compromisos_reportables.id=$id; ";
        $this->db->query($sql);
	}

}
