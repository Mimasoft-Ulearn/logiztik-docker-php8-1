<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class AC_Activities_dashboard extends MY_Controller {
	
	private $id_client_context_module;
	private $id_client_context_submodule;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$client_area = $this->session->client_area;
		if($client_area == "territory"){
			$this->id_client_context_module = 7;
			$this->id_client_context_submodule = 10;
		} 
		// if($client_area == "distribution"){
		// 	$this->id_client_context_module = 7;
		// 	$this->id_client_context_submodule = 13;
		// }
		
		$id_cliente = $this->login_user->client_id;
		//$this->block_url_client_context($id_cliente, $this->id_client_context_module);
		
		$acuerdos_territorio_disponibilidad_modulo = $this->Client_module_availability_model->get_one_where(array(
			"id_cliente" => $this->login_user->client_id,
			"id_modulo" => 5,
			"deleted" => 0
		));
		if($client_area == "territory" && !$acuerdos_territorio_disponibilidad_modulo->disponible){
			// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Acuerdos Territorio esté deshabilitada.
			$this->block_url_client_context($id_cliente, 5);
		}
		// $acuerdos_distribucion_disponibilidad_modulo = $this->Client_module_availability_model->get_one_where(array(
		// 	"id_cliente" => $this->login_user->client_id,
		// 	"id_modulo" => 16,
		// 	"deleted" => 0
		// ));
		// if($client_area == "distribution" && !$acuerdos_distribucion_disponibilidad_modulo->disponible){
		// 	// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Acuerdos Distribución esté deshabilitada.
		// 	$this->block_url_client_context($id_cliente, 16);
		// }

		// LOG
		// $this->id_home_modules_info = 2; // Acuerdos
		// $this->id_module_territory = 32; // Dashboard de Actividades Territorio
		// $this->id_module_distribution = 33; // Dashboard de Actividades Distribucion
		
    }

    function index() {
		
		$view_data = array();
		$id_usuario = $this->session->user_id;
		$id_cliente = $this->login_user->client_id;
		$cliente = $this->Clients_model->get_one($id_cliente);
		$client_area = $this->session->client_area;
		
		$view_data["puede_ver"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
		
		$view_data["AC_Activities_dashboard"] = $this;
		$view_data["AC_Communes_model"] = $this->AC_Communes_model;
		
		$view_data["client_agreements_info"] = $this->AC_Client_agreements_info_model->get_details(array(
			"client_area" => $client_area
		))->row();

		$view_data["client_area"] = $client_area;
		$view_data["id_cliente"] = $id_cliente;

		$years = array(2020, 2021, 2022, 2023, 2024);
		$view_data['years'] = json_encode($years);
		
		if($client_area == "territory"){

			$array_activities_by_type = array(); // AGRUPA ACTIVIDADES POR TIPO, PARA GENERAR TABS Y SECCIONES DEL DASHBOARD.
			$tipos_actividad = $this->AC_Types_of_activities_model->get_all()->result();
			$view_data["tipos_actividad"] = $tipos_actividad;

			$chart_data_activities_executed = array();
			$chart_data_benefited_collaborators = array();
			$chart_data_executed_amount = array();
			$chart_data_activities_by_society = array();

			foreach($tipos_actividad as $tipo_actividad){

				$activities = $this->AC_Feeders_type_of_activities_model->get_details(array(
					"id_cliente" => $id_cliente, 
					"id_tipo_actividad" => $tipo_actividad->id
				))->result();

				$array_activities_by_type[$tipo_actividad->id] = array(
					"name_type_of_activity" => lang($tipo_actividad->name),
					"activities" => $activities
				);

				// POR CADA ACTIVIDAD (MANTENEDORA), BUSCA LOS REGISTROS DE ACTIVIDAD ASOCIADOS Y GENERA LOS DATOS DE CADA GRÁFICO
				foreach($activities AS $activity){

					$chart_data_activities_executed[$tipo_actividad->id][$activity->id] = json_encode($this->gen_chart_data_activities_executed(array(
						"id_cliente" => $id_cliente,
						"id_tipo_actividad" => $tipo_actividad->id,
						"id_feeder_activity" => $activity->id,
						"years" => $years
					)));

					$chart_data_benefited_collaborators[$tipo_actividad->id][$activity->id] = json_encode($this->gen_chart_data_benefited_collaborators(array(
						"id_cliente" => $id_cliente,
						"id_tipo_actividad" => $tipo_actividad->id,
						"id_feeder_activity" => $activity->id,
						"years" => $years
					)));

					$chart_data_executed_amount[$tipo_actividad->id][$activity->id] = json_encode($this->gen_chart_data_executed_amount(array(
						"id_cliente" => $id_cliente,
						"id_tipo_actividad" => $tipo_actividad->id,
						"id_feeder_activity" => $activity->id,
						"years" => $years
					)));

					$chart_data_activities_by_society[$tipo_actividad->id][$activity->id] = json_encode($this->gen_chart_data_activities_by_society(array(
						"id_cliente" => $id_cliente,
						"id_tipo_actividad" => $tipo_actividad->id,
						"id_feeder_activity" => $activity->id,
						"years" => $years
					)));

				}

			}

			$view_data["array_activities_by_type"] = $array_activities_by_type;
			$view_data["chart_data_activities_executed"] = $chart_data_activities_executed;
			$view_data["chart_data_benefited_collaborators"] = $chart_data_benefited_collaborators;
			$view_data["chart_data_executed_amount"] = $chart_data_executed_amount;
			$view_data["chart_data_activities_by_society"] = $chart_data_activities_by_society;

		}

		// echo "<br>";
		// echo "<pre>";
		// var_dump($chart_data_activities_by_society);
		// exit();
		// AGREGAR LOG
		// if($client_area == "territory"){
		// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_territory, NULL, "accessed");
		// }
		// if($client_area == "distribution"){
		// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_distribution, NULL, "accessed");
		// }
		
		// var_dump(":D");
        $this->template->rander("ac_activities_dashboard/index", $view_data);
    
	}
	
	/**
	 * Función que genera el arreglo con datos para los gráfico "Actividades ejecutadas" de cada sección (cada Actividad
	 * registrada en la Mantenedora de Actividades) que corresponda a su Tipo de Actividad
	 */
	function gen_chart_data_activities_executed($options = array()){

		$id_cliente = $options["id_cliente"];
		$id_tipo_actividad = $options["id_tipo_actividad"];
		$id_feeder_activity = $options["id_feeder_activity"];
		$years = $options["years"];

		$chart_data = array(
			array(
				'name' => 'Actual',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => 'Objetivo',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => '% objetivo',
				'type' => 'spline',
				'yAxis' => 1,
				'data' => array(),
				'tooltip' => array(
					'valueSuffix' => '%'
				)
			)
		);

		foreach($years as $year) {
			
			$result = $this->AC_Activities_model->activities_executed(array(
				"id_cliente" => $id_cliente,
				"id_tipo_actividad" => $id_tipo_actividad,
				"id_feeder_tipo_actividad" => $id_feeder_activity,
				"year" => $year
			));

			$chart_data[0]['data'][] = (int) $result['cant'];
			$chart_data[1]['data'][] = (int) $result['cant_objetivo'];
			$chart_data[2]['data'][] = (float) $result['porcentaje'];
			
		}

		return $chart_data;
	}

	function gen_chart_data_benefited_collaborators($options = array()){

		$id_cliente = $options["id_cliente"];
		$id_tipo_actividad = $options["id_tipo_actividad"];
		$id_feeder_activity = $options["id_feeder_activity"];
		$years = $options["years"];

		$chart_data = array(
			array(
				'name' => 'Actual',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => 'Objetivo',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => '% objetivo',
				'type' => 'spline',
				'yAxis' => 1,
				'data' => array(),
				'tooltip' => array(
					'valueSuffix' => '%'
				)
			)
		);

		foreach ($years as $year) {

			$result = $this->AC_Activities_model->benefited_collaborators(array(
				"id_cliente" => $id_cliente,
				"id_tipo_actividad" => $id_tipo_actividad,
				"id_feeder_tipo_actividad" => $id_feeder_activity,
				"year" => $year
			));

			$chart_data[0]['data'][] = (int) $result['cant'];
			$chart_data[1]['data'][] = (int) $result['cant_objetivo'];
			$chart_data[2]['data'][] = (float) $result['porcentaje'];
		}

		return $chart_data;
	}

	function gen_chart_data_executed_amount($options = array()){

		$id_cliente = $options["id_cliente"];
		$id_tipo_actividad = $options["id_tipo_actividad"];
		$id_feeder_activity = $options["id_feeder_activity"];
		$years = $options["years"];

		$chart_data = array(
			array(
				'name' => 'Actual',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => 'Objetivo',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => '% objetivo',
				'type' => 'spline',
				'yAxis' => 1,
				'data' => array(),
				'tooltip' => array(
					'valueSuffix' => '%'
				)
			)
		);

		foreach ($years as $year) {
			
			$result = $this->AC_Activities_model->executed_amount(array(
				"id_cliente" => $id_cliente,
				"id_tipo_actividad" => $id_tipo_actividad,
				"id_feeder_tipo_actividad" => $id_feeder_activity,
				"year" => $year
			));

			$chart_data[0]['data'][] = (int) $result['cant'];
			$chart_data[1]['data'][] = (int) $result['cant_objetivo'];
			$chart_data[2]['data'][] = (float) $result['porcentaje'];

		}

		return $chart_data;
	}

	function gen_chart_data_activities_by_society($options = array()){

		$id_cliente = $options["id_cliente"];
		$id_tipo_actividad = $options["id_tipo_actividad"];
		$id_feeder_activity = $options["id_feeder_activity"];
		$years = $options["years"];

		// LLAMAR A LAS SOCIEDADES DEL CLIENTE (MANTENEDORA SOCIEDADES). SERÁN LAS SERIRES DEL GRÁFICO
		$societies = $this->AC_Feeders_societies_model->get_all_where(array("id_cliente" => $id_cliente, "deleted" => 0))->result();
		$chart_data = array();
		foreach ($societies as $society) {

			$data = array(
				'name' => $society->nombre_sociedad,
				'data' => array()
			);
			foreach ($years as $year) {

				$data['data'][] = (int)$this->AC_Activities_model->activities_by_society(array(
					"id_cliente" => $id_cliente,
					"id_tipo_actividad" => $id_tipo_actividad,
					"id_feeder_tipo_actividad" => $id_feeder_activity,
					"year" => $year,
					"id_feeder_sociedad" => $society->id
				));
			}
			$chart_data[] = $data;
		}

		return $chart_data;

	}
	
	function get_pdf(){
		
		$id_cliente = $this->login_user->client_id;
		$id_usuario = $this->session->user_id;
		$client_area = $this->session->client_area;
		
		$info_cliente = $this->Clients_model->get_one($id_cliente);
		
		$view_data["info_cliente"] = $info_cliente;
		$view_data["client_area"] = $client_area;
		
		if($client_area == "territory"){
			$imagenes_graficos = $this->input->post("imagenes_graficos");
			$view_data["grafico_beneficiarios_por_macrozona"] = $imagenes_graficos["image_beneficiarios_por_macrozona"];
			$view_data["grafico_beneficiarios_asistentes"] = $imagenes_graficos["image_beneficiarios_asistentes"];
			$view_data["grafico_beneficiarios_actividad"] = $imagenes_graficos["image_beneficiarios_actividad"];
			$view_data["graficos_actividades_macrozona"] = $imagenes_graficos["graficos_actividades_macrozona"];
			$view_data["grafico_tipo_beneficiario"] = $imagenes_graficos["image_tipo_beneficiario"];
		}
		
		if($client_area == "distribution"){
			$imagenes_graficos = $this->input->post("imagenes_graficos");
			$view_data["grafico_beneficiarios_por_comuna"] = $imagenes_graficos["image_beneficiarios_por_comuna"];
			$view_data["grafico_participantes_por_comuna"] = $imagenes_graficos["image_participantes_por_comuna"];
			$view_data["grafico_beneficiarios_tipo_actividad"] = $imagenes_graficos["image_beneficiarios_tipo_actividad"];
			$view_data["graficos_actividades_comuna"] = $imagenes_graficos["graficos_actividades_comuna"];
			$view_data["grafico_tipo_beneficiario"] = $imagenes_graficos["image_tipo_beneficiario"];
		}
				
		$view_data["puede_ver"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
		
		if($client_area == "territory"){
			
			// Sección Beneficiarios por Macrozona
			// Sub-Sección Beneficiarios
			$actividades = $this->AC_Activities_model->get_all_where(array(
				"id_cliente" => $id_cliente,
				"client_area" => $client_area,
				"deleted" => 0
			))->result();
			
			$array_actividades = array();
			foreach($actividades as $actividad){
				$beneficiarios = json_decode($actividad->organizacion_participante);
				
				$organizacion_participante = array();
				foreach($beneficiarios as $beneficiario_id) {
					$beneficiario = $this->AC_Beneficiaries_model->get_one($beneficiario_id);
					if ($beneficiario->tipo_stakeholder == "legal_person") {
						array_push($organizacion_participante, $beneficiario);
					}
				}
				
				$array_actividades[] = array(
					"id_macrozona" => $actividad->id_macrozona,
					"organizacion_participante" => count($organizacion_participante)
				);
			}
	
			$array_beneficiarios_organizaciones = array();
			$array_beneficiarios_organizaciones["Zona Norte"] = 0;
			$array_beneficiarios_organizaciones["Zona Centro"] = 0;
			$array_beneficiarios_organizaciones["Zona Sur"] = 0;
			
			$total_beneficiarios = 0;
			foreach($array_actividades as $beneficiario){
				$cantidad_participantes = $beneficiario["organizacion_participante"];
				$macrozona = $this->AC_Macrozones_model->get_one($beneficiario["id_macrozona"]);
				$key = $macrozona->nombre;
				$array_beneficiarios_organizaciones[$key] += $cantidad_participantes;
				$total_beneficiarios += $cantidad_participantes;
			}
			
			$view_data["array_beneficiarios_organizaciones"] = $array_beneficiarios_organizaciones;
			$view_data["total_beneficiarios"] = $total_beneficiarios;
			// Fin Sub-Sección Beneficiarios
			
			// Sub-Sección Asistentes
			$array_actividades = array();
			foreach($actividades as $actividad){	
				$tipo_acuerdo = $this->AC_Feeders_types_agreements_model->get_one($actividad->id_feeder_tipo_acuerdo);
				if($tipo_acuerdo->play_energy){
					$array_actividades[] = array(
						"id_macrozona" => $actividad->id_macrozona,
						"n_participantes" => ($actividad->n_estudiantes_6to) ? $actividad->n_estudiantes_6to : 0
					);
				}else{
					$array_actividades[] = array(
						"id_macrozona" => $actividad->id_macrozona,
						"n_participantes" => ($actividad->n_participantes) ? $actividad->n_participantes : 0
					);
				}
				
			}
			
			$array_beneficiarios_asistentes = array();
			$array_beneficiarios_asistentes["Zona Norte"] = 0;
			$array_beneficiarios_asistentes["Zona Centro"] = 0;
			$array_beneficiarios_asistentes["Zona Sur"] = 0;
			
			$total_participantes = 0;
			foreach($array_actividades as $beneficiario){
				$cantidad_asistentes = $beneficiario["n_participantes"];
				$macrozona = $this->AC_Macrozones_model->get_one($beneficiario["id_macrozona"]);
				$key = $macrozona->nombre;
				$array_beneficiarios_asistentes[$key] += $cantidad_asistentes;
				$total_participantes += $cantidad_asistentes;
			}
			
			$view_data["array_beneficiarios_asistentes"] = $array_beneficiarios_asistentes;
			$view_data["total_participantes"] = $total_participantes;
			// Fin Sub-Sección Asistentes
			// Fin Sección Beneficiarios por Macrozona
			
			
			// Sección Beneficiarios por Actividad
			$array_actividades = array();
			foreach($actividades as $actividad){
	
				$tipo_acuerdo = $this->AC_Feeders_types_agreements_model->get_one($actividad->id_feeder_tipo_acuerdo);
				if($tipo_acuerdo->play_energy){
					
					$array_actividades[] = array(
						"id_feeder_tipo_acuerdo" => $actividad->id_feeder_tipo_acuerdo,
						"n_participantes" => ($actividad->n_estudiantes_6to) ? $actividad->n_estudiantes_6to : 0
					);
					
				}else{
					
					$array_actividades[] = array(
						"id_feeder_tipo_acuerdo" => $actividad->id_feeder_tipo_acuerdo,
						"n_participantes" => ($actividad->n_participantes) ? $actividad->n_participantes : 0
					);
					
				}
				
			}
			
			$array_beneficiarios_tipo_actividad = array();
			$total_asistentes = 0;
			foreach($array_actividades as $actividad){
				$cantidad_asistentes = $actividad["n_participantes"];
				$tipo_acuerdo = $this->AC_Feeders_types_agreements_model->get_one($actividad["id_feeder_tipo_acuerdo"]);
				$key = $tipo_acuerdo->tipo_acuerdo;
				$array_beneficiarios_tipo_actividad[$key] += $cantidad_asistentes;
				$total_asistentes += $cantidad_asistentes;
			}
			
			$view_data["array_beneficiarios_tipo_actividad"] = $array_beneficiarios_tipo_actividad;
			$view_data["total_asistentes"] = $total_asistentes;
			// Fin Sección Beneficiarios por Actividad
			
			//Sección Actividades por Macrozona
			$array_macrozonas[1] = "Zona Norte";
			$array_macrozonas[2] = "Zona Centro";
			$array_macrozonas[3] = "Zona Sur";
			$view_data["array_macrozonas"] = $array_macrozonas;
	
			$opciones_tipo_acuerdo_actividades = array(
				"id_cliente" => $id_cliente,
				"client_area" => "territory"
			);
			$tipos_acuerdo_actividades = $this->AC_Activities_model->get_dashboard_tipos_acuerdo_actividades($opciones_tipo_acuerdo_actividades)->result_array();
			
			$view_data["tipos_acuerdo_actividades"] = $tipos_acuerdo_actividades;

		}
		
		
		if($client_area == "distribution"){
			
			// Sección Beneficiarios por Comuna
			// Sub-Sección Beneficiarios
			$opciones_beneficiarios_comuna = array(
				"id_cliente" => $id_cliente,
				"client_area" => $client_area
			); 
			$beneficiarios_comuna = $this->AC_Activities_model->get_participantes_actividad_por_comuna($opciones_beneficiarios_comuna)->result();
			
			$array_beneficiarios_comuna = array();
			$total_beneficiarios_comuna = 0;
			foreach($beneficiarios_comuna as $beneficiario_comuna){
				//$organizacion_participante = json_decode($beneficiario_comuna->organizacion_participante);
				
				$beneficiarios = json_decode($beneficiario_comuna->organizacion_participante);
				
				$organizacion_participante = array();
				foreach($beneficiarios as $beneficiario_id) {
					$beneficiario = $this->AC_Beneficiaries_model->get_one($beneficiario_id);
					if ($beneficiario->tipo_stakeholder == "natural_person") {
						array_push($organizacion_participante, $beneficiario);
					}
				}
				
				$array_beneficiarios_comuna[] = array(
					"comuna" => $beneficiario_comuna->nombre_comuna,
					"color" => $beneficiario_comuna->color,
					"organizacion_participante" => count($organizacion_participante)
				);
				
				$total_beneficiarios_comuna += count($organizacion_participante);
			}

			$array_final_beneficiarios_comuna = array();
			foreach($array_beneficiarios_comuna as $beneficiario_comuna){

				if(isset($array_final_beneficiarios_comuna[$beneficiario_comuna["comuna"]]["op"])){
					$array_final_beneficiarios_comuna[$beneficiario_comuna["comuna"]]["op"] += $beneficiario_comuna["organizacion_participante"];
				} else {
					$array_final_beneficiarios_comuna[$beneficiario_comuna["comuna"]]["op"] = $beneficiario_comuna["organizacion_participante"];
				}
				
				$array_final_beneficiarios_comuna[$beneficiario_comuna["comuna"]]["color"] = $beneficiario_comuna["color"];
				
			}
			
			$view_data["array_beneficiarios_comuna"] = $array_final_beneficiarios_comuna;
			$view_data["total_beneficiarios_comuna"] = $total_beneficiarios_comuna;
			// Fin Sub-Sección Beneficiarios
			
			
			// Sub-Sección Asistentes
			$opciones_participantes_comuna = array(
				"id_cliente" => $id_cliente,
				"client_area" => $client_area
			); 
			$participantes_comuna = $this->AC_Activities_model->get_cantidad_participantes_por_comuna($opciones_participantes_comuna)->result();
			
			$array_participantes_comuna = array();
			$total_participantes_comuna = 0;
			foreach($participantes_comuna as $participante_comuna){
				
				$tipo_acuerdo = $this->AC_Feeders_types_agreements_model->get_one($participante_comuna->id_feeder_tipo_acuerdo);
				if($tipo_acuerdo->play_energy){
					
					if($participante_comuna->n_estudiantes_6to){
						$array_participantes_comuna[] = array(
							"comuna" => $participante_comuna->nombre_comuna,
							"color" => $participante_comuna->color,
							"organizacion_participante" => $participante_comuna->n_estudiantes_6to
						);
					}
					$total_participantes_comuna += $participante_comuna->n_estudiantes_6to;
					
				}else{
					
					if($participante_comuna->n_participantes){
						$array_participantes_comuna[] = array(
							"comuna" => $participante_comuna->nombre_comuna,
							"color" => $participante_comuna->color,
							"organizacion_participante" => $participante_comuna->n_participantes
						);
					}
					$total_participantes_comuna += $participante_comuna->n_participantes;
					
				}
				
			}
			
			$array_final_participantes_comuna = array();
			foreach($array_participantes_comuna as $participante_comuna){

				if(isset($array_final_participantes_comuna[$participante_comuna["comuna"]]["op"])){
					$array_final_participantes_comuna[$participante_comuna["comuna"]]["op"] += $participante_comuna["organizacion_participante"];
				} else {
					$array_final_participantes_comuna[$participante_comuna["comuna"]]["op"] = $participante_comuna["organizacion_participante"];
				}
				
				$array_final_participantes_comuna[$participante_comuna["comuna"]]["color"] = $participante_comuna["color"];
				
			}
			
			
			$view_data["array_participantes_comuna"] = $array_final_participantes_comuna;
			$view_data["total_participantes_comuna"] = $total_participantes_comuna;
			// Fin Sub-Sección Asistentes
			// Fin Sección Beneficiarios por Comuna
			
			
			// Sección Beneficiarios por Actividad
			$opciones_beneficiarios_actividad = array(
				"id_cliente" => $id_cliente,
				"client_area" => $client_area
			); 
			$beneficiarios_actividad = $this->AC_Activities_model->get_beneficiarios_por_tipo_actividad($opciones_beneficiarios_actividad)->result();
			
			$array_beneficiarios_actividad = array();
			$total_beneficiarios_actividad = 0;
			foreach($beneficiarios_actividad as $beneficiario_actividad){
				
				if($beneficiario_actividad->play_energy){
					
					$array_beneficiarios_actividad[] = array(
						"tipo_acuerdo" => $beneficiario_actividad->tipo_acuerdo,
						"n_participantes" => ($beneficiario_actividad->n_estudiantes_6to) ? $beneficiario_actividad->n_estudiantes_6to : 0
					);
					$total_beneficiarios_actividad += $beneficiario_actividad->n_estudiantes_6to;
					
				}else{
					
					$array_beneficiarios_actividad[] = array(
						"tipo_acuerdo" => $beneficiario_actividad->tipo_acuerdo,
						"n_participantes" => ($beneficiario_actividad->n_participantes) ? $beneficiario_actividad->n_participantes : 0
					);
					$total_beneficiarios_actividad += $beneficiario_actividad->n_participantes;
					
				}
				
			}
			
			$array_final_beneficiarios_actividad = array();
			foreach($array_beneficiarios_actividad as $beneficiario_actividad){

				if(isset($array_final_beneficiarios_actividad[$beneficiario_actividad["tipo_acuerdo"]])){
					$array_final_beneficiarios_actividad[$beneficiario_actividad["tipo_acuerdo"]] += $beneficiario_actividad["n_participantes"];
				} else {
					$array_final_beneficiarios_actividad[$beneficiario_actividad["tipo_acuerdo"]] = $beneficiario_actividad["n_participantes"];
				}
				
			}
			
			$view_data["array_beneficiarios_actividad"] = $array_final_beneficiarios_actividad;
			$view_data["total_beneficiarios_actividad"] = $total_beneficiarios_actividad;
			// Fin Sección Beneficiarios por Actividad
			
			
			// Sección Actividades por Comuna
			$opciones_comunas = array(
				"id_cliente" => $id_cliente,
				"client_area" => $client_area
			);
			$array_comunas = array();
			$comunas = $this->AC_Activities_model->get_comunas_dashboard_actividades_por_comuna($opciones_comunas)->result();
			foreach($comunas as $comuna){
				$array_comunas[$comuna->id_comuna] = $comuna->nombre_comuna;
			}
			$view_data["comunas"] = $array_comunas;
			
			$opciones_actividades_comuna = array(
				"id_cliente" => $id_cliente,
				"client_area" => "distribution"
			);
			$actividades_comuna = $this->AC_Activities_model->get_dashboard_actividades_por_comuna($opciones_actividades_comuna)->result_array();
						
			$view_data["actividades_comuna"] = $actividades_comuna;
			// Fin Sección Actividades por Comuna
			
		}
		
		
		$view_data["AC_Activities_dashboard"] = $this;
		//Fin Sección Actividades por Macrozona
		
		// TABLA Y GRAFICO BENEFICIARIOS NUEVOS Y ANTIGUOS (TERRITORIO Y DISTRIBUCION)
		$beneficiarios_total = $this->AC_Beneficiaries_model->get_all_where(array(
			"id_cliente" => $id_cliente,
			"client_area" => $client_area,
			"deleted" => 0
		))->num_rows();
		
		$beneficiarios_nuevos = $this->AC_Beneficiaries_model->get_all_where(array(
			"id_cliente" => $id_cliente,
			"client_area" => $client_area,
			"tipo_beneficiario" => "new",
			"deleted" => 0
		))->num_rows();
		
		$beneficiarios_antiguos = $this->AC_Beneficiaries_model->get_all_where(array(
			"id_cliente" => $id_cliente,
			"client_area" => $client_area,
			"tipo_beneficiario" => "old",
			"deleted" => 0
		))->num_rows();
		
		$view_data["beneficiarios_total"] = $beneficiarios_total;
		$view_data["beneficiarios_nuevos"] = $beneficiarios_nuevos;
		$view_data["beneficiarios_antiguos"] = $beneficiarios_antiguos;
		
		$view_data["porc_beneficiarios_nuevos"] = ($beneficiarios_total == 0)?0:round(($beneficiarios_nuevos * 100) / $beneficiarios_total, 2);
		$view_data["porc_beneficiarios_antiguos"] = ($beneficiarios_total == 0)?0:round(($beneficiarios_antiguos * 100) / $beneficiarios_total, 2);
		// FIN TABLA Y GRAFICO BENEFICIARIOS NUEVOS Y ANTIGUOS (TERRITORIO Y DISTRIBUCION)
		
		
		
		// create new PDF document
        $this->load->library('Pdf');
		
		// set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Autor');
        $this->pdf->SetTitle($info_cliente->sigla."_".lang("activities")."_".date('Y-m-d'));
        $this->pdf->SetSubject($info_cliente->sigla."_".lang("activities")."_".date('Y-m-d'));
        $this->pdf->SetKeywords('TCPDF, PDF');
		
		//$this->pdf->SetPrintHeader(false);
		//$this->pdf->SetPrintFooter(false);
		// set default header data
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', '', array(0, 64, 255), array(0, 64, 128));
        $this->pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));
		// set header and footer fonts
        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$this->pdf->SetAutoPageBreak(TRUE,PDF_MARGIN_BOTTOM);	
		//relación utilizada para ajustar la conversión de los píxeles
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		// ---------------------------------------------------------
		// set default font subsetting mode
        $this->pdf->setFontSubsetting(true);
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		//$this->SetFont('freemono', '', 14, '', true);        
		$fontawesome = TCPDF_FONTS::addTTFfont('assets/js/font-awesome/fonts/fontawesome-webfont.ttf', 'TrueTypeUnicode', '', 96); 
		
		$this->pdf->AddPage();

		$image_file = get_file_uri('assets/images/mimasoft-logo-fondo.png');
		$this->pdf->Image($image_file, 160, 10, 35, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		$view_data["fontawesome"] = $fontawesome;
		$view_data["pdf"] = $this->pdf;
		$html = $this->load->view('ac_activities_dashboard/pdf_view', $view_data, true);
		
		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf_file_name = $info_cliente->sigla."_".lang("activities")."_".date('Y-m-d').".pdf";
		
		$tmp = get_setting("temp_file_path");
		$this->pdf->Output(getcwd() . '/' . $tmp.$pdf_file_name, "F");

		// LOG ACTION downloaded_pdf
		// if($client_area == "territory"){
		// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_territory, NULL, "downloaded_pdf");
		// }
		// if($client_area == "distribution"){
		// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_distribution, NULL, "downloaded_pdf");
		// }

		echo $pdf_file_name;
		
	}
	
}