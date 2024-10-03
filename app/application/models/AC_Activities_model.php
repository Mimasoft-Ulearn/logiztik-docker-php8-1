<?php

class AC_Activities_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_actividades';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
		
        $actividades_table = $this->db->dbprefix('ac_actividades');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $actividades_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $actividades_table.id_cliente = $id_cliente";
        }
		
		$tipo_actividad = get_array_value($options, "tipo_actividad");
        if ($tipo_actividad) {
            $where .= " AND $actividades_table.id_feeder_tipo_actividad = $tipo_actividad";
        }
		
		$sociedad = get_array_value($options, "sociedad");
        if ($sociedad) {
            $where .= " AND $actividades_table.id_feeder_sociedad = $sociedad";
        }
		

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $actividades_table.* FROM $actividades_table WHERE";
		$sql .= " $actividades_table.deleted=0";
		$sql .= " $where";
		$sql .= " ORDER BY $actividades_table.created DESC";
		
        return $this->db->query($sql);
		
    }
	
	// Territory
	function get_dashboard_activities_by_macrozone($options = array()){
			
		$actividades_table = $this->db->dbprefix('ac_actividades');
		$feeders_tipos_acuerdo_table = $this->db->dbprefix('ac_feeders_tipos_acuerdo');
		$macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		
		$where = "";
        
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $actividades_table.id_cliente = $id_cliente";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $actividades_table.client_area = '$client_area'";
        }
		
		$id_macrozona = get_array_value($options, "id_macrozona");
        if ($id_macrozona) {
            $where .= " AND $actividades_table.id_macrozona = $id_macrozona";
        }
		
		$id_feeder_tipo_acuerdo = get_array_value($options, "id_feeder_tipo_acuerdo");
        if ($id_feeder_tipo_acuerdo) {
            $where .= " AND $actividades_table.id_feeder_tipo_acuerdo = $id_feeder_tipo_acuerdo";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
        
		$sql = "SELECT $actividades_table.id_feeder_tipo_acuerdo, $feeders_tipos_acuerdo_table.tipo_acuerdo,";
		$sql .= " $actividades_table.id_macrozona, $macrozonas_table.nombre as nombre_macrozona,";
		$sql .= " COUNT(*) AS cant_tipo_acuerdo_por_macrozona";
		$sql .= " FROM $actividades_table, $feeders_tipos_acuerdo_table, $macrozonas_table";
		$sql .= " WHERE $actividades_table.id_feeder_tipo_acuerdo = $feeders_tipos_acuerdo_table.id";
		$sql .= " AND $actividades_table.id_macrozona = $macrozonas_table.id";
		$sql .= " AND $feeders_tipos_acuerdo_table.tipo_administracion = 'activity'";
		$sql .= " AND $actividades_table.deleted = 0";
		$sql .= " $where";
		$sql .= " GROUP BY $actividades_table.id_feeder_tipo_acuerdo, $actividades_table.id_macrozona";
		
		return $this->db->query($sql);
		
	}
	
	// Territory
	function get_dashboard_tipos_acuerdo_actividades($options = array()){
		
		$actividades_table = $this->db->dbprefix('ac_actividades');
		$feeders_tipos_acuerdo_table = $this->db->dbprefix('ac_feeders_tipos_acuerdo');
		$macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		
		$where = "";
        
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $actividades_table.id_cliente = $id_cliente";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $actividades_table.client_area = '$client_area'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $actividades_table.id AS id_actividad, $actividades_table.id_feeder_tipo_acuerdo, $feeders_tipos_acuerdo_table.tipo_acuerdo,";
		$sql .= " $actividades_table.id_macrozona, $macrozonas_table.nombre as nombre_macrozona";
		$sql .= " FROM $actividades_table, $feeders_tipos_acuerdo_table, $macrozonas_table";
		$sql .= " WHERE $actividades_table.id_feeder_tipo_acuerdo = $feeders_tipos_acuerdo_table.id";
		$sql .= " AND $actividades_table.id_macrozona = $macrozonas_table.id";
		$sql .= " AND $feeders_tipos_acuerdo_table.tipo_administracion = 'activity'";
		$sql .= " AND $actividades_table.deleted = 0";
		$sql .= " $where";
		$sql .= " GROUP BY $actividades_table.id_feeder_tipo_acuerdo";
		
		return $this->db->query($sql);
	
	}
	
	// Distribution
	function get_participantes_actividad_por_comuna($options = array()){
		
		$actividades_table = $this->db->dbprefix('ac_actividades');
		$comunas_table = $this->db->dbprefix('ac_comunas');
		
		$where = "";
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $actividades_table.id_cliente = $id_cliente";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $actividades_table.client_area = '$client_area'";
        }

		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $actividades_table.id_comuna, $comunas_table.nombre AS nombre_comuna, $comunas_table.color, $actividades_table.organizacion_participante";
		$sql .= " FROM $actividades_table, $comunas_table";
		$sql .= " WHERE $actividades_table.id_comuna = $comunas_table.id";
		$sql .= " AND $actividades_table.deleted = 0";
		$sql .= " $where";
		
		return $this->db->query($sql);
		
	}
	
	// Distribution
	function get_cantidad_participantes_por_comuna($options = array()){
		
		$actividades_table = $this->db->dbprefix('ac_actividades');
		$comunas_table = $this->db->dbprefix('ac_comunas');
		
		$where = "";
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $actividades_table.id_cliente = $id_cliente";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $actividades_table.client_area = '$client_area'";
        }

		$this->db->query('SET SQL_BIG_SELECTS=1');

		$sql = "SELECT $actividades_table.id_comuna, $comunas_table.nombre AS nombre_comuna, $comunas_table.color, $actividades_table.n_participantes, $actividades_table.n_estudiantes_6to, $actividades_table.id_feeder_tipo_acuerdo";
		$sql .= " FROM $actividades_table, $comunas_table";
		$sql .= " WHERE $actividades_table.id_comuna = $comunas_table.id";
		$sql .= " AND $actividades_table.deleted = 0";
		$sql .= " $where";
		
		return $this->db->query($sql);
		
	}
	
	// Distribution
	function get_beneficiarios_por_tipo_actividad($options = array()){
		
		$actividades_table = $this->db->dbprefix('ac_actividades');
		$mantenedoras_tipos_acuerdo_table = $this->db->dbprefix('ac_feeders_tipos_acuerdo');
		
		$where = "";
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $actividades_table.id_cliente = $id_cliente";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $actividades_table.client_area = '$client_area'";
        }

		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $actividades_table.id_feeder_tipo_acuerdo, $mantenedoras_tipos_acuerdo_table.tipo_acuerdo, $mantenedoras_tipos_acuerdo_table.play_energy,";
		$sql .= " $actividades_table.organizacion_participante, $actividades_table.n_participantes, $actividades_table.n_estudiantes_6to";
		$sql .= " FROM $actividades_table, $mantenedoras_tipos_acuerdo_table";
		$sql .= " WHERE $actividades_table.id_feeder_tipo_acuerdo = $mantenedoras_tipos_acuerdo_table.id";
		$sql .= " AND $actividades_table.deleted = 0";
		$sql .= " $where";
		
		return $this->db->query($sql);
		
	}
	
	// Distribution
	function get_comunas_dashboard_actividades_por_comuna($options = array()){
		
		$actividades_table = $this->db->dbprefix('ac_actividades');
		$feeders_tipos_acuerdo_table = $this->db->dbprefix('ac_feeders_tipos_acuerdo');
		$comunas_table = $this->db->dbprefix('ac_comunas');
		
		$where = "";
        
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $actividades_table.id_cliente = $id_cliente";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $actividades_table.client_area = '$client_area'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $actividades_table.id_comuna, $comunas_table.nombre AS nombre_comuna";
		$sql .= " FROM $actividades_table, $comunas_table, $feeders_tipos_acuerdo_table";
		$sql .= " WHERE $actividades_table.id_comuna = $comunas_table.id";
		$sql .= " AND $actividades_table.id_feeder_tipo_acuerdo = $feeders_tipos_acuerdo_table.id";
		$sql .= " AND $actividades_table.deleted = 0";
		$sql .= " $where";
		$sql .= " GROUP BY $actividades_table.id_comuna";
		
		return $this->db->query($sql);
		
	}
	
	// Distribution
	function get_dashboard_actividades_por_comuna($options = array()){
		
		$actividades_table = $this->db->dbprefix('ac_actividades');
		$feeders_tipos_acuerdo_table = $this->db->dbprefix('ac_feeders_tipos_acuerdo');
		$comunas_table = $this->db->dbprefix('ac_comunas');
		
		$where = "";
        
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $actividades_table.id_cliente = $id_cliente";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $actividades_table.client_area = '$client_area'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');

		$sql = "SELECT $actividades_table.id AS id_actividad, $actividades_table.id_feeder_tipo_acuerdo, $feeders_tipos_acuerdo_table.tipo_acuerdo,";
		$sql .= " $actividades_table.id_comuna, $comunas_table.nombre as nombre_comuna";
		$sql .= " FROM $actividades_table, $feeders_tipos_acuerdo_table, $comunas_table";
		$sql .= " WHERE $actividades_table.id_feeder_tipo_acuerdo = $feeders_tipos_acuerdo_table.id";
		$sql .= " AND $actividades_table.id_comuna = $comunas_table.id";
		$sql .= " AND $feeders_tipos_acuerdo_table.tipo_administracion = 'activity'";
		$sql .= " AND $actividades_table.deleted = 0";
		$sql .= " $where";
		$sql .= " GROUP BY $actividades_table.id_feeder_tipo_acuerdo";
		
		return $this->db->query($sql);
		
	}
	
	function get_dashboard_cant_actividades_por_comuna($options = array()){
		
		$actividades_table = $this->db->dbprefix('ac_actividades');
		$feeders_tipos_acuerdo_table = $this->db->dbprefix('ac_feeders_tipos_acuerdo');
		$comunas_table = $this->db->dbprefix('ac_comunas');
		
		$where = "";
        
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $actividades_table.id_cliente = $id_cliente";
        }
		
		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $actividades_table.client_area = '$client_area'";
        }
		
		$id_comuna = get_array_value($options, "id_comuna");
        if ($id_comuna) {
            $where .= " AND $actividades_table.id_comuna = $id_comuna";
        }
		
		$id_feeder_tipo_acuerdo = get_array_value($options, "id_feeder_tipo_acuerdo");
        if ($id_feeder_tipo_acuerdo) {
            $where .= " AND $actividades_table.id_feeder_tipo_acuerdo = $id_feeder_tipo_acuerdo";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $actividades_table.id_feeder_tipo_acuerdo, $feeders_tipos_acuerdo_table.tipo_acuerdo,";
		$sql .= " $actividades_table.id_comuna, $comunas_table.nombre as nombre_comuna,";
		$sql .= " COUNT(*) AS cant_actividad_por_comuna";
		$sql .= " FROM $actividades_table, $feeders_tipos_acuerdo_table, $comunas_table";
		$sql .= " WHERE $actividades_table.id_feeder_tipo_acuerdo = $feeders_tipos_acuerdo_table.id";
		$sql .= " AND $actividades_table.id_comuna = $comunas_table.id";
		$sql .= " AND $feeders_tipos_acuerdo_table.tipo_administracion = 'activity'";
		$sql .= " AND $actividades_table.deleted = 0";
		$sql .= " $where";
		$sql .= " GROUP BY $actividades_table.id_feeder_tipo_acuerdo, $actividades_table.id_comuna";
		
		return $this->db->query($sql);
		
	}
	
	
	/* COMUNIDADES - DASHBOARD ACTIVIDADES - DATA PARA GRÁFICOS */
	function activities_executed($options = array()){
        
        $actividades_table = $this->db->dbprefix('ac_actividades');

		$where = "";
        
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $actividades_table.id_cliente = $id_cliente";
        }

		$id_tipo_actividad = get_array_value($options, "id_tipo_actividad");

		$id_feeder_tipo_actividad = get_array_value($options, "id_feeder_tipo_actividad");
        if ($id_feeder_tipo_actividad) {
            $where .= " AND $actividades_table.id_feeder_tipo_actividad = $id_feeder_tipo_actividad";
        }

		$year = get_array_value($options, "year");
        if ($year) {
            $where .= " AND YEAR($actividades_table.fecha) = $year";
        }

        $sql = "SELECT COUNT(*) as cant";
		$sql .= " FROM $actividades_table";
		$sql .= " WHERE $actividades_table.deleted = 0";
		$sql .= " $where";

        $cant = $this->db->query($sql)->result()[0]->cant;

		// OBTENER LOS VALORES OBJETIVOS PARA LA SERIE % OBJETIVO		
		$options = array(
			'id_cliente' => $id_cliente,
			'id_tipo_actividad' => $id_tipo_actividad,
			"id_actividad" => $id_feeder_tipo_actividad,
			'grafico' => 'activities_executed',
			'deleted' => 0
		);

		$objetivos_data = $this->AC_Feeders_activity_objectives_model->get_all_where($options)->result();
		$objetivo = json_decode($objetivos_data[0]->objetivos);
		$cant_objetivo = $objetivo->$year ? (int) $objetivo->$year : 0;
        $porcentaje = $cant_objetivo != 0 ? ($cant * 100)/$cant_objetivo : 0; // Evitar division por cero

        return array(
            'cant' => $cant, 
            'cant_objetivo' => $cant_objetivo,
            'porcentaje' => $porcentaje
        );

    }

	function benefited_collaborators($options = array()){

		$actividades_table = $this->db->dbprefix('ac_actividades');

		$where = "";
        
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $actividades_table.id_cliente = $id_cliente";
        }

		$id_tipo_actividad = get_array_value($options, "id_tipo_actividad");

		$id_feeder_tipo_actividad = get_array_value($options, "id_feeder_tipo_actividad");
        if ($id_feeder_tipo_actividad) {
            $where .= " AND $actividades_table.id_feeder_tipo_actividad = $id_feeder_tipo_actividad";
        }

		$year = get_array_value($options, "year");
        if ($year) {
            $where .= " AND YEAR($actividades_table.fecha) = $year";
        }

        $sql = "SELECT $actividades_table.asistentes";
		$sql .= " FROM $actividades_table";
		$sql .= " WHERE $actividades_table.deleted = 0";
		$sql .= " $where";

        $actividades = $this->db->query($sql)->result();
		$array_asistentes = array();
		foreach($actividades as $actividad){
			$array_asistentes = array_merge($array_asistentes, json_decode($actividad->asistentes));
		}

		// $array_asistentes_unique = array_unique($array_asistentes);
		// $cant = count($array_asistentes_unique);
		$cant = count($array_asistentes);

		// OBTENER LOS VALORES OBJETIVOS PARA LA SERIE % OBJETIVO		
		$options = array(
			'id_cliente' => $id_cliente,
			'id_tipo_actividad' => $id_tipo_actividad,
			"id_actividad" => $id_feeder_tipo_actividad,
			'grafico' => 'benefited_collaborators',
			'deleted' => 0
		);

		$objetivos_data = $this->AC_Feeders_activity_objectives_model->get_all_where($options)->result();
		$objetivo = json_decode($objetivos_data[0]->objetivos);
		$cant_objetivo = $objetivo->$year ? (int) $objetivo->$year : 0;
        $porcentaje = $cant_objetivo != 0 ? ($cant * 100)/$cant_objetivo : 0; // Evitar division por cero

        return array(
            'cant' => $cant, 
            'cant_objetivo' => $cant_objetivo,
            'porcentaje' => $porcentaje
        );

	}

	function executed_amount($options = array()){

		$actividades_table = $this->db->dbprefix('ac_actividades');

		$where = "";
        
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $actividades_table.id_cliente = $id_cliente";
        }

		$id_tipo_actividad = get_array_value($options, "id_tipo_actividad");

		$id_feeder_tipo_actividad = get_array_value($options, "id_feeder_tipo_actividad");
        if ($id_feeder_tipo_actividad) {
            $where .= " AND $actividades_table.id_feeder_tipo_actividad = $id_feeder_tipo_actividad";
        }

		$year = get_array_value($options, "year");
        if ($year) {
            $where .= " AND YEAR($actividades_table.fecha) = $year";
        }

        $sql = "SELECT SUM($actividades_table.inversion) as cant";
		$sql .= " FROM $actividades_table";
		$sql .= " WHERE $actividades_table.deleted = 0";
		$sql .= " $where";

		$cant = $this->db->query($sql)->result()[0]->cant;

		// OBTENER LOS VALORES OBJETIVOS PARA LA SERIE % OBJETIVO		
		$options = array(
			'id_cliente' => $id_cliente,
			'id_tipo_actividad' => $id_tipo_actividad,
			"id_actividad" => $id_feeder_tipo_actividad,
			'grafico' => 'executed_amount',
			'deleted' => 0
		);

		$objetivos_data = $this->AC_Feeders_activity_objectives_model->get_all_where($options)->result();
		$objetivo = json_decode($objetivos_data[0]->objetivos);
		$cant_objetivo = $objetivo->$year ? (int) $objetivo->$year : 0;
        $porcentaje = $cant_objetivo != 0 ? ($cant * 100)/$cant_objetivo : 0; // Evitar division por cero

        return array(
            'cant' => $cant, 
            'cant_objetivo' => $cant_objetivo,
            'porcentaje' => $porcentaje
        );

	}

	function activities_by_society($options = array()){
		
		$actividades_table = $this->db->dbprefix('ac_actividades');

		$where = "";
        
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $actividades_table.id_cliente = $id_cliente";
        }

		$id_feeder_tipo_actividad = get_array_value($options, "id_feeder_tipo_actividad");
        if ($id_feeder_tipo_actividad) {
            $where .= " AND $actividades_table.id_feeder_tipo_actividad = $id_feeder_tipo_actividad";
        }

		$year = get_array_value($options, "year");
        if ($year) {
            $where .= " AND YEAR($actividades_table.fecha) = $year";
        }

		$id_feeder_sociedad = get_array_value($options, "id_feeder_sociedad");
        if ($id_feeder_sociedad) {
            $where .= " AND $actividades_table.id_feeder_sociedad = $id_feeder_sociedad";
        }

		$sql = "SELECT COUNT(*) as cant";
		$sql .= " FROM $actividades_table";
		$sql .= " WHERE $actividades_table.deleted = 0";
		$sql .= " $where";

		$cant = $this->db->query($sql)->result()[0]->cant;

		// echo $sql."<br>";
		// echo $cant."<br><br>";

        return $cant;

	}
	
}
