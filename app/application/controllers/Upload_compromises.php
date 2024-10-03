<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Upload_compromises extends MY_Controller {
	
	private $id_admin_module;
	private $id_admin_submodule;

    function __construct() {
        parent::__construct();
		
		$this->id_admin_module = 8; // Compromisos
		$this->id_admin_submodule = 29; // Carga de Compromisos
		
		$this->load->helper('email');
        $this->init_permission_checker("client");
    }

    function index() {
		$this->access_only_allowed_members();
		$access_info = $this->get_access_info("invoice");
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
        $this->template->rander("upload_compromises/index", $view_data);
    }

	function carga_individual($id_compromiso_proyecto, $tipo_matriz) {
		
		//Obtener las columnas (campos y evaluados) de la matriz de cumplimiento del proyecto
		$json_string_campos = "";
		if($tipo_matriz == "rca"){
			$columnas_campos = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
		}else{
			$columnas_campos = $this->Compromises_reportables_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
		}
		
		foreach($columnas_campos as $columna){
			if($columna["id_tipo_campo"] == 1){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
			}else if($columna["id_tipo_campo"] == 2){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-center"}';
			}else if($columna["id_tipo_campo"] == 3){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-right dt-head-center"}';
			}else if($columna["id_tipo_campo"] >= 4 && $columna["id_tipo_campo"] <= 9){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
			}else if($columna["id_tipo_campo"] == 10){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-center"}';
			}else if(($columna["id_tipo_campo"] == 11) || ($columna["id_tipo_campo"] == 12)){
				continue;
			}else if($columna["id_tipo_campo"] == 13 || $columna["id_tipo_campo"] == 14){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
			}else if($columna["id_tipo_campo"] == 15){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-right dt-head-center"}';
			}else if($columna["id_tipo_campo"] == 16){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
			}else{
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '"}';
			}
			
		}
		
		$view_data["columnas_campos"] = $json_string_campos;
		$view_data["id_compromiso_proyecto"] = $id_compromiso_proyecto;
		$view_data["tipo_matriz"] = $tipo_matriz;

		$id_proyecto = $this->Compromises_reportables_model->get_one($id_compromiso_proyecto)->id_proyecto;
		$view_data["id_proyecto"] = $id_proyecto;
		
        $this->load->view('upload_compromises/carga_individual/index', $view_data);
    }
	
	function modal_form_carga_individual($id_compromiso_proyecto, $tipo_matriz){
			
		$id_elemento = $this->input->post('id');
		if($tipo_matriz == "rca"){
			$campos_compromiso = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
		}else{
			$campos_compromiso = $this->Compromises_reportables_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
		}
		
		$view_data["campos_compromiso"] = $campos_compromiso;
		$view_data["id_compromiso_proyecto"] = $id_compromiso_proyecto;
		$view_data["Upload_compromises_controller"] = $this;
		$view_data["tipo_matriz"] = $tipo_matriz;

		$id_proyecto = $this->Compromises_reportables_model->get_one($id_compromiso_proyecto)->id_proyecto;

		$view_data["id_proyecto"] = $id_proyecto;
		
		if($tipo_matriz == "rca"){
			$fases_disponibles = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
			$fases_dropdown = array();
			foreach($fases_disponibles as $fase){
				$fases_dropdown[$fase["id"]] = lang($fase["nombre_lang"]);
			}
			$view_data["fases_disponibles"] = $fases_dropdown;
		}

		if($tipo_matriz == "reportable"){

			// INSTRUMENTO DE GESTÓN AMBIENTAL
			$array_environmental_management_instrument = array(
				"" => "-",
				"mpama" => lang("mpama"),
				"pama" => lang("pama"),
				"pama_and_mpama" => lang("pama_and_mpama"),
				"dia" => lang("dia"),
				"mdia" => lang("mdia"),
				"dia_and_mdia" => lang("dia_and_mdia"),
				"n/a" => lang("n/a")
			);
			$view_data["array_environmental_management_instrument"] = $array_environmental_management_instrument;

			// ETAPA
			$array_phase = array(
				"" => "-",
				"construction" => lang("construction"),
				"operation" => lang("operation"),
				"closing" => lang("closing")
			);
			$view_data["array_phase"] = $array_phase;

			// TIPO DE CUMPLIMIENTO
			$array_compliance_types = array(
				"" => "-",
				"environmental_adaptation_actions" => lang("environmental_adaptation_actions"),
				"mitigation_measures_in_existing_components" => lang("mitigation_measures_in_existing_components"),
				"prevention_control_and_mitigation_measures_in_projected_components" => lang("prevention_control_and_mitigation_measures_in_projected_components"),
				"measures_for_the_prevention_control_and_mitigation_of_environmental_impacts" => lang("measures_for_the_prevention_control_and_mitigation_of_environmental_impacts"),
				"closure_or_abandonment_plan" => lang("closure_or_abandonment_plan"),
				"contingency_plan" => lang("contingency_plan"),
				"solid_waste_management_plan" => lang("solid_waste_management_plan"),
				"main_environmental_obligations" => lang("main_environmental_obligations"),
				"environmental_education_program" => lang("environmental_education_program"),
				"environmental_monitoring_program" => lang("environmental_monitoring_program"),
				"citizen_participation_program" => lang("citizen_participation_program"),
				"environmental_signage_program" => lang("environmental_signage_program"),
				"preventive_or_corrective_program" => lang("preventive_or_corrective_program"),
				"closing_activities_projected_components" => lang("closing_activities_projected_components"),
			);
			$view_data["array_compliance_types"] = $array_compliance_types;

			// TEMA AMBIENTAL / MEDIO
			$array_environmental_topic = array(
				"" => "-",
				"water" => lang("water"),
				"air" => lang("air"),
				"air/noise" => lang("air/noise"),
				"control_of_agricultural_inputs" => lang("control_of_agricultural_inputs"),
				"crops" => lang("crops"),
				"environmental_education" => lang("environmental_education"),
				"industrial_and_domestic_effluents" => lang("industrial_and_domestic_effluents"),
				"social_environment" => lang("social_environment"),
				"flora_and_fauna" => lang("flora_and_fauna"),
				"non_hazardous_waste" => lang("non_hazardous_waste"),
				"hazardous_waste" => lang("hazardous_waste"),
				"hazardous_and_non_hazardous_waste" => lang("hazardous_and_non_hazardous_waste"),
				"security_and_health_at_work" => lang("security_and_health_at_work"),
				"ground" => lang("ground"),
				"other" => lang("other"),
			);
			$view_data["array_environmental_topic"] = $array_environmental_topic;

			// AFECTACIÓN AL MEDIO POR INCUMPLIMIENTO / COMPONENTE
			$array_impact_on_the_environment_due_to_non_compliance = array(
				"" => "-",
				"water" => lang("water"),
				"air_and_noise" => lang("air_and_noise"),
				"community/social_environment" => lang("community/social_environment"),
				"flora_and_fauna" => lang("flora_and_fauna"),
				"solid_waste" => lang("solid_waste"),
				"hazardous_waste" => lang("hazardous_waste"),
				"non_hazardous_waste" => lang("non_hazardous_waste"),
				"health" => lang("health"),
				"ground" => lang("ground"),
				"physical_environment" => lang("physical_environment"),
				"physical_and_socioeconomic_environment" => lang("physical_and_socioeconomic_environment")
			);
			$view_data["array_impact_on_the_environment_due_to_non_compliance"] = $array_impact_on_the_environment_due_to_non_compliance;

			// ÁREA RESPONSABLE
			$array_responsible_area = array(
				"personal_administration" => lang("personal_administration"),
				"warehouse" => lang("warehouse"),
				"quality" => lang("quality"),
				"field" => lang("field"),
				"training" => lang("training"),
				"communications" => lang("communications"),
				"crops" => lang("crops"),
				"management" => lang("management"),
				"icp" => lang("icp"),
				"maintenance" => lang("maintenance"),
				"machinery" => lang("machinery"),
				"operations" => lang("operations"),
				"packing" => lang("packing"),
				"landscaping" => lang("landscaping"),
				"pre_mix" => lang("pre_mix"),
				"projects" => lang("projects"),
				"recruitment" => lang("recruitment"),
				"social_responsability" => lang("social_responsability"),
				"irrigation" => lang("irrigation"),
				"occupational_health" => lang("occupational_health"),
				"industrial_security" => lang("industrial_security"),
				"general_services" => lang("general_services"),
				"sig" => lang("sig"),
				"transportation" => lang("transportation"),
				"environment" => lang("environment")
			);
			$view_data["array_responsible_area"] = $array_responsible_area;

		}
		
		if($id_elemento){ //edit
			if($tipo_matriz == "rca"){
				$model_info = $this->Values_compromises_rca_model->get_one($id_elemento);
			}else{
				$model_info = $this->Values_compromises_reportables_model->get_one($id_elemento);

				$responsible_area_decoded = json_decode($model_info->area_responsable);
				$view_data['responsible_area'] = $responsible_area_decoded;
				
				$planificaciones = $this->Plans_reportables_compromises_model->get_all_where(
					array(
						"id_compromiso" => $id_elemento,
						"deleted" => 0,
					)
				)->result();
				$view_data["planificaciones"] = $planificaciones;
				
				$array_planificaciones = array();
				foreach($planificaciones as $planificacion){
					$array_planificaciones[] = array("descripcion" => $planificacion->descripcion, "planificacion" => $planificacion->planificacion);
				}
				
				$view_data['array_planificaciones'] = $array_planificaciones;
			}
			$view_data['model_info'] = $model_info;

			$fases_decoded = json_decode($model_info->fases);
			$view_data['fases_compromiso'] = $fases_decoded;
			
		} 

		$this->load->view('upload_compromises/carga_individual/modal_form', $view_data);
		
	}
	
	function save_carga_individual($id_compromiso_proyecto, $tipo_matriz){
		
		$id_elemento = $this->input->post('id'); //para la edición, este es el id de un elemento (valores_compromisos)

		$id_proyecto = $this->Compromises_reportables_model->get_one($id_compromiso_proyecto)->id_proyecto;
		
		if($tipo_matriz == "rca"){

			$numero_compromiso = $this->input->post('numero_compromiso');
			$nombre_compromiso = $this->input->post('nombre_compromiso');
			$fases = $this->input->post('phases');
			$json_fases = json_encode($fases);
			$reportabilidad = ($this->input->post('reportability')) ? 1 : 0;
			$compliance_action_control = $this->input->post('compliance_action_control');
			$execution_frequency = $this->input->post('execution_frequency');

			$columnas = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
			$matriz_info = $this->Compromises_rca_model->get_one($id_compromiso_proyecto);
			$project_info = $this->Projects_model->get_one($matriz_info->id_proyecto);


		}else{ // reportable
				
			$n_activity = $this->input->post('n_activity');
			$environmental_management_instrument = $this->input->post('environmental_management_instrument');
			$phase = $this->input->post('phase');
			$compliance_type = $this->input->post('compliance_type');
			$environmental_topic = $this->input->post('environmental_topic');
			$impact_on_the_environment_due_to_non_compliance = $this->input->post('impact_on_the_environment_due_to_non_compliance');
			$action_type = $this->input->post('action_type');
			$responsible_area = $this->input->post('responsible_area');
			$json_responsible_area = json_encode($responsible_area);
			$commitment_description = $this->input->post('commitment_description');

			$columnas = $this->Compromises_reportables_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
			$matriz_info = $this->Compromises_reportables_model->get_one($id_compromiso_proyecto);
			$project_info = $this->Projects_model->get_one($matriz_info->id_proyecto);

		}
		
		$array_datos = array();
		foreach($columnas as $columna){

			// VERIFICO SI EL CAMPO EN LOOP VIENE DESHABILITADO
			$deshabilitado = $columna->habilitado;
			$default_value = $columna->default_value;
			
			if($columna->id_tipo_campo == 5){

				if($deshabilitado){
					$array_datos[$columna->id_campo] = json_decode($default_value, true);
				}else{
					$json_name = $columna->html_name;
					$array_name = json_decode($json_name, true);
					$start_name = $array_name["start_name"];
					$end_name = $array_name["end_name"];
					
					$array_datos[$columna->id_campo] = array(
						"start_date" => $this->input->post($start_name),
						"end_date" => $this->input->post($end_name)
					);
				}
				
			} else if($columna->id_tipo_campo == 11){
				//CAMPO TIPO TEXTO FIJO NO SE GUARDA
			} else {

				if($deshabilitado){
					$array_datos[$columna->id_campo] = $default_value;
				}else{
					$array_datos[$columna->id_campo] =  $this->input->post($columna->html_name);
				}
			}

		}
		
		$json_datos = json_encode($array_datos);

		$data = array(
			"id_compromiso" => $id_compromiso_proyecto,
            "datos_campos" => $json_datos,
		);
		
		if($tipo_matriz == "rca"){

			$data["numero_compromiso"] = $numero_compromiso;
			$data["nombre_compromiso"] = $nombre_compromiso;
			$data["fases"] = $json_fases;
			$data["reportabilidad"] = $reportabilidad;
			$data["accion_cumplimiento_control"] = $compliance_action_control;
			$data["frecuencia_ejecucion"] = $execution_frequency;

		} else {

			$data["numero_actividad"] = $n_activity;
			$data["instrumento_gestion_ambiental"] = $environmental_management_instrument;
			$data["etapa"] = $phase;
			$data["tipo_cumplimiento"] = $compliance_type; 
			$data["tema_ambiental"] = $environmental_topic;
			$data["afectacion_medio_por_incumplimiento"] = $impact_on_the_environment_due_to_non_compliance;
			$data["tipo_accion"] = $action_type;
			$data["area_responsable"] = $json_responsible_area;
			$data["descripcion_compromiso"] = $commitment_description;
			
		}
		
		if($id_elemento){
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
		}else{
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
		}
		
		if($tipo_matriz == "rca"){
			$save_id = $this->Values_compromises_rca_model->save($data, $id_elemento);
		}else{
			
			// VALIDAR QUE NO VENGAN 2 O MAS VECES LAS MISMAS FECHAS DE PLANIFICACIÓN
			$array_fecha_termino = (array)$this->input->post('term_date');
			$valores_repetidos = (count(array_unique($array_fecha_termino)) != count($array_fecha_termino));
			
			if($valores_repetidos){
				echo json_encode(array("success" => false, 'message' => lang('repeated_planifications_message')));
				exit();
			}
			
			$save_id = $this->Values_compromises_reportables_model->save($data, $id_elemento);
		}
		
		if ($save_id) {
			
			if(!$id_elemento){ // Insert
				
				if($tipo_matriz == "rca"){
					// SI SE INGRESA EL COMPROMISO RCA, AUTOMATICAMENTE SE DEBEN INGRESAR TANTAS EVALUACIONES 
					// COMO EVALUADOS HAYAN RELACIONADOS A ESTE COMPROMISO Y CON ESTADO POR DEFECTO NO APLICA
					// PRIMERO VOY A BUSCAR EL ID DEL ESTADO NO APLICA DEL CLIENTE
					
					$estado_no_aplica = $this->Compromises_compliance_status_model->get_one_where(
						array(
							"id_cliente" => $project_info->client_id, 
							"tipo_evaluacion" => "rca", 
							"categoria" => "No Aplica", 
							"deleted" => 0
						)
					);
					$id_estado = $estado_no_aplica->id;
					
					$evaluados_matriz = $this->Evaluated_rca_compromises_model->get_all_where(array("id_compromiso" => $id_compromiso_proyecto, "deleted" => 0))->result();
					foreach($evaluados_matriz as $evaluado){
						
						$data_compliance_evaluation = array();
						$data_compliance_evaluation["id_valor_compromiso"] = $save_id;
						$data_compliance_evaluation["id_evaluado"] = $evaluado->id;
						$data_compliance_evaluation["id_estados_cumplimiento_compromiso"] = $id_estado;
						$data_compliance_evaluation["observaciones"] = NULL;
						$data_compliance_evaluation["responsable"] = $this->login_user->id;
						$data_compliance_evaluation["fecha_evaluacion"] = get_current_utc_time();
						//$data_compliance_evaluation["fecha_evaluacion"] = "2018-10-24";
						$data_compliance_evaluation["created_by"] = $this->login_user->id;
						$data_compliance_evaluation["created"] = get_current_utc_time();
						$evaluation_save_id = $this->Compromises_compliance_evaluation_rca_model->save($data_compliance_evaluation);
						
					}
				}else{// INGRESO DE COMPROMISO REPORTABLE
					
					$array_descripcion = (array)$this->input->post('description');
					$array_fecha_termino = (array)$this->input->post('term_date');
					array_shift($array_descripcion);
					array_shift($array_fecha_termino);
					
					$array_planificaciones = array();
					foreach($array_descripcion as $index => $descripcion){
						$fecha_termino = $array_fecha_termino[$index];
						
						$data_planificacion = array();
						$data_planificacion["id_compromiso"] = $save_id;	//id_valor_compromiso
						$data_planificacion["descripcion"] = $descripcion;
						$data_planificacion["planificacion"] = $fecha_termino;
						$data_planificacion["created_by"] = $this->login_user->id;
						$data_planificacion["created"] = get_current_utc_time();
						
						$plan_save_id = $this->Plans_reportables_compromises_model->save($data_planificacion);
						
						if($plan_save_id){
							$data_planificacion["id"] = $plan_save_id;
							$array_planificaciones[] = $data_planificacion;
						}
					}
					
					// CONTINUO CON LAS COMBINACIONES COMPROMISO-PLANIFICACION
					// (CON ESTADO SEGUN CRITERIO DE FECHA)
					
					foreach($array_planificaciones as $fila_planificacion){
						
						$fecha = $fila_planificacion["planificacion"];
						
						if($fecha < date("Y-m-d")){
							// no cumple
							$estado_no_cumple = $this->Compromises_compliance_status_model->get_one_where(
								array(
									"id_cliente" => $project_info->client_id, 
									"tipo_evaluacion" => "reportable", 
									"categoria" => "No Cumple", 
									"deleted" => 0
								)
							);
							$id_estado = $estado_no_cumple->id;
						}else{
							// pendiente
							$estado_pendiente = $this->Compromises_compliance_status_model->get_one_where(
								array(
									"id_cliente" => $project_info->client_id, 
									"tipo_evaluacion" => "reportable", 
									"categoria" => "Pendiente", 
									"deleted" => 0
								)
							);
							$id_estado = $estado_pendiente->id;
						}
						
						$data_compliance_evaluation = array();
						$data_compliance_evaluation["id_valor_compromiso"] = $save_id;
						$data_compliance_evaluation["id_planificacion"] = $fila_planificacion["id"];
						$data_compliance_evaluation["id_estados_cumplimiento_compromiso"] = $id_estado;
						$data_compliance_evaluation["observaciones"] = NULL;
						$data_compliance_evaluation["responsable"] = $this->login_user->id;
						$data_compliance_evaluation["fecha_evaluacion"] = get_current_utc_time();
						$data_compliance_evaluation["created_by"] = $this->login_user->id;

						$data_compliance_evaluation["created"] = get_current_utc_time();
						
						$evaluation_save_id = $this->Compromises_compliance_evaluation_reportables_model->save($data_compliance_evaluation);
						
						// Crear configuración por cada planificación de evaluación, con valores vacíos (ok).
						$data_alert_config = array(
							"id_client" => $project_info->client_id,
							"id_project" => $project_info->id,
							"id_client_module" => 6, // Compromisos
							"id_client_submodule" => 22, // Evaluación de Compromisos Reportables
							"alert_config" => json_encode(array(
								"id_planificacion" => (string)$fila_planificacion["id"],
								"risk_value" => "",
								"threshold_value" => ""
							)),
							"created_by" => $this->login_user->id,
							"created" => get_current_utc_time()
						);
						
						$alert_save_id = $this->AYN_Alert_projects_model->save($data_alert_config);
						
						// Guardar histórico alertas por cada planificación de evaluación
						$data_historical_alert = array(
							"id_client" => $project_info->client_id,
							"id_project" => $project_info->id,
							"id_user" => $this->login_user->id,
							"id_client_module" => 6, // Compromisos
							"id_client_submodule" => 22, // Evaluación de Compromisos Reportables
							"alert_config" => json_encode(array(
								"id_planificacion" => (string)$fila_planificacion["id"],
								"id_valor_compromiso" => (string)$save_id,
								"tipo_evaluacion" => "reportable",
							), TRUE),
							"id_alert_projects" => $alert_save_id,
							"id_element" => $save_id,
							"alert_date" => get_current_utc_time()
						);
						
						$historical_alert_save_id = $this->AYN_Alert_historical_model->save($data_historical_alert);
												
					}
				}
				
			}else{ // Update
				
				if($tipo_matriz == "rca"){
					
				}else{

					// PLANIFICACIONES AÑADIDAS AL EDITAR EL COMPROMISO
					$array_descripcion = (array)$this->input->post('description');
					$array_fecha_termino = (array)$this->input->post('term_date');
					array_shift($array_descripcion);
					array_shift($array_fecha_termino);
					
					$array_planificaciones = array();
					foreach($array_descripcion as $index => $descripcion){
						$fecha_termino = $array_fecha_termino[$index];
						
						$data_planificacion = array();
						$data_planificacion["id_compromiso"] = $save_id;
						$data_planificacion["descripcion"] = $descripcion;
						$data_planificacion["planificacion"] = $fecha_termino;
						$data_planificacion["created_by"] = $this->login_user->id;
						$data_planificacion["created"] = get_current_utc_time();
						
						$plan_save_id = $this->Plans_reportables_compromises_model->save($data_planificacion);
						
						if($plan_save_id){
							$data_planificacion["id"] = $plan_save_id;
							$array_planificaciones[] = $data_planificacion;
						}
					}
					
					// CONTINUO CON LAS COMBINACIONES COMPROMISO-PLANIFICACION
					// (CON ESTADO SEGUN CRITERIO DE FECHA)
					
					foreach($array_planificaciones as $fila_planificacion){
						
						$fecha = $fila_planificacion["planificacion"];
						
						if($fecha < date("Y-m-d")){
							// no cumple
							$estado_no_cumple = $this->Compromises_compliance_status_model->get_one_where(
								array(
									"id_cliente" => $project_info->client_id, 
									"tipo_evaluacion" => "reportable", 
									"categoria" => "No Cumple", 
									"deleted" => 0
								)
							);
							$id_estado = $estado_no_cumple->id;
						}else{
							// pendiente
							$estado_pendiente = $this->Compromises_compliance_status_model->get_one_where(
								array(
									"id_cliente" => $project_info->client_id, 
									"tipo_evaluacion" => "reportable", 
									"categoria" => "Pendiente", 
									"deleted" => 0
								)
							);
							$id_estado = $estado_pendiente->id;
						}
						
						$data_compliance_evaluation = array();
						$data_compliance_evaluation["id_valor_compromiso"] = $save_id;
						$data_compliance_evaluation["id_planificacion"] = $fila_planificacion["id"];
						$data_compliance_evaluation["id_estados_cumplimiento_compromiso"] = $id_estado;
						$data_compliance_evaluation["observaciones"] = NULL;
						$data_compliance_evaluation["responsable"] = $this->login_user->id;
						$data_compliance_evaluation["fecha_evaluacion"] = get_current_utc_time();
						$data_compliance_evaluation["created_by"] = $this->login_user->id;
						$data_compliance_evaluation["created"] = get_current_utc_time();
						
						$evaluation_save_id = $this->Compromises_compliance_evaluation_reportables_model->save($data_compliance_evaluation);
						
						// Crear configuración por cada planificación de evaluación, con valores vacíos (ok).
						$data_alert_config = array(
							"id_client" => $project_info->client_id,
							"id_project" => $project_info->id,
							"id_client_module" => 6, // Compromisos
							"id_client_submodule" => 22, // Evaluación de Compromisos Reportables
							"alert_config" => json_encode(array(
								"id_planificacion" => (string)$fila_planificacion["id"],
								"risk_value" => "",
								"threshold_value" => ""
							)),
							"created_by" => $this->login_user->id,
							"created" => get_current_utc_time()
						);
						
						$alert_save_id = $this->AYN_Alert_projects_model->save($data_alert_config);
						
						// Guardar histórico alertas por cada planificación de evaluación
						$data_historical_alert = array(
							"id_client" => $project_info->client_id,
							"id_project" => $project_info->id,
							"id_user" => $this->login_user->id,
							"id_client_module" => 6, // Compromisos
							"id_client_submodule" => 22, // Evaluación de Compromisos Reportables
							"alert_config" => json_encode(array(
								"id_planificacion" => (string)$fila_planificacion["id"],
								"id_valor_compromiso" => (string)$save_id,
								"tipo_evaluacion" => "reportable",
							), TRUE),
							"id_alert_projects" => $alert_save_id,
							"id_element" => $save_id,
							"alert_date" => get_current_utc_time()
						);
						
						$historical_alert_save_id = $this->AYN_Alert_historical_model->save($data_historical_alert);

					}
					// FIN PLANIFICACIONES AÑADIDAS AL EDITAR EL COMPROMISO

					// SI NO EXISTE CONFIGURACIÓN DE ALERTAS PARA LAS PLANIFICACIONES EXISTENTES DEL COMPROMISO, ANTES DE SU EDICIÓN,
					// CREA LA CONFIGURACIÓN DE ALERTAS Y GUARDA EL HISTÓRICO DE ALERTAS PARA CADA PLANIFICACIÓN
					$ids_planificaciones = $this->input->post("ids_planificaciones");
					foreach($ids_planificaciones as $id_planificacion){

						$config_options = array(
							"id_client" => $project_info->client_id,
							"id_project" => $project_info->id,
							"id_client_module" => 6, // Compromisos
							"id_client_submodule" => 22, // Evaluación de Compromisos Reportables
							"alert_config" => array("id_planificacion" => $id_planificacion)
						);
						
						$alert_projects_config_planification_rep = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();

						if(!$alert_projects_config_planification_rep->id){

							$data_alert_config = array(
								"id_client" => $project_info->client_id,
								"id_project" => $project_info->id,
								"id_client_module" => 6, // Compromisos
								"id_client_submodule" => 22, // Evaluación de Compromisos Reportables
								"alert_config" => json_encode(array(
									"id_planificacion" => (string)$id_planificacion,
									"risk_value" => "",
									"threshold_value" => ""
								)),
								"created_by" => $this->login_user->id,
								"created" => get_current_utc_time()
							);
							
							$alert_save_id = $this->AYN_Alert_projects_model->save($data_alert_config);
	
							$data_historical_alert = array(
								"id_client" => $project_info->client_id,
								"id_project" => $project_info->id,
								"id_user" => $this->login_user->id,
								"id_client_module" => 6, // Compromisos
								"id_client_submodule" => 22, // Evaluación de Compromisos Reportables
								"alert_config" => json_encode(array(
									"id_planificacion" => (string)$id_planificacion,
									"id_valor_compromiso" => (string)$save_id,
									"tipo_evaluacion" => "reportable",
								), TRUE),
								"id_alert_projects" => $alert_save_id,
								"id_element" => $save_id,
								"alert_date" => get_current_utc_time()
							);
							
							$historical_alert_save_id = $this->AYN_Alert_historical_model->save($data_historical_alert);

						}

					}

				}
				
			}
			
			if($tipo_matriz == "rca"){
				$columnas = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
				$elemento_compromiso = $this->Values_compromises_rca_model->get_one($save_id);
				$matriz = $this->Compromises_rca_model->get_one($elemento_compromiso->id_compromiso);
			}else{
				$columnas = $this->Compromises_reportables_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
				$elemento_compromiso = $this->Values_compromises_reportables_model->get_one($save_id);
				$matriz = $this->Compromises_reportables_model->get_one($elemento_compromiso->id_compromiso);
			}
			
			$proyecto = $this->Projects_model->get_one($matriz->id_proyecto);
			$id_cliente = $proyecto->client_id;
			// Guardar histórico notificaciones
			$options = array(
				"id_client" => $id_cliente,
				"id_project" => $proyecto->id,
				"id_user" => $this->login_user->id,
				"module_level" => "admin",
				"id_admin_module" => $this->id_admin_module,
				"id_admin_submodule" => $this->id_admin_submodule,
				"event" => ($tipo_matriz == "rca") ? "comp_rca_add" : "comp_rep_add",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options);

            echo json_encode(array("success" => true, "data" => $this->_row_data_carga_individual($save_id, $columnas, $id_compromiso_proyecto, $tipo_matriz), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
	}

	function list_data_carga_individual($id_compromiso_proyecto = 0, $tipo_matriz){
		
		$options = array(
			"id_compromiso" => $id_compromiso_proyecto
		);
		
		if($tipo_matriz == "rca"){
			$list_data = $this->Values_compromises_rca_model->get_details($options)->result();
			$columnas = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
		}else{
			$list_data = $this->Values_compromises_reportables_model->get_details($options)->result();
			$columnas = $this->Compromises_reportables_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
		}
		
		$result = array();
		foreach($list_data as $data) {
			$result[] = $this->_make_row_carga_individual($data, $columnas, $id_compromiso_proyecto, $tipo_matriz);
		}
		
		echo json_encode(array("data" => $result));	
	}
	
	function _row_data_carga_individual($id, $columnas, $id_compromiso_proyecto, $tipo_matriz){
		
		$options = array(
            "id" => $id
        );
		
		if($tipo_matriz == "rca"){
			$data = $this->Values_compromises_rca_model->get_details($options)->row();
		}else{
			$data = $this->Values_compromises_reportables_model->get_details($options)->row();
		}

        return $this->_make_row_carga_individual($data, $columnas, $id_compromiso_proyecto, $tipo_matriz);
		
	}
	
	function _make_row_carga_individual($data, $columnas, $id_compromiso_proyecto, $tipo_matriz){
		
		if($tipo_matriz == "rca"){
			$id_proyecto = $this->Compromises_rca_model->get_one($id_compromiso_proyecto)->id_proyecto;
		}else{
			$id_proyecto = $this->Compromises_reportables_model->get_one($id_compromiso_proyecto)->id_proyecto;
		}
		$row_data = array();
		$row_data[] = $data->id;
		
		if($tipo_matriz == "rca"){

			$row_data[] = $data->numero_compromiso;
			$row_data[] = $data->nombre_compromiso;

			$fases_decoded = json_decode($data->fases);
			$html_fases = "";
			foreach($fases_decoded as $id_fase){
				$nombre_lang = $this->Phases_model->get_one($id_fase)->nombre_lang;
				$nombre_fase = lang($nombre_lang);
				$html_fases .= "&bull; " . $nombre_fase . "<br>";
			}
			$row_data[] = $html_fases;
			$row_data[] = ($data->reportabilidad == 1) ? '<i class="fa fa-check" aria-hidden="true"></i>' : '<i class="fa fa-times" aria-hidden="true"></i>';

		} else {

			$row_data[] = $data->numero_actividad;
			$row_data[] = lang($data->instrumento_gestion_ambiental);
			$row_data[] = $data->etapa ? lang($data->etapa) : '-';
			$row_data[] = lang($data->tipo_cumplimiento);
			$row_data[] = lang($data->tema_ambiental);
			$row_data[] = lang($data->afectacion_medio_por_incumplimiento);
			$row_data[] = $data->tipo_accion;
			$area_resonsable_decoded = json_decode($data->area_responsable);
			$html_area_responsable = '';
			foreach($area_resonsable_decoded as $area){
				$nombre_area = lang($area);
				$html_area_responsable .= '&bull;&nbsp;' . $nombre_area . '<br>';
			}
			$row_data[] = $html_area_responsable;
			// $row_data[] = lang($data->area_responsable);

			// TOOLTIP Descripcion Compromiso
			$tooltip_descripcion_compromiso = '<span class="help" data-container="body" data-toggle="tooltip" title="'.  htmlspecialchars($data->descripcion_compromiso, ENT_QUOTES) .'"><i class="fas fa-info-circle fa-lg"></i></span>';
			$valor_descripcion_compromiso = ($data->descripcion_compromiso) ? $tooltip_descripcion_compromiso : "-";
			$row_data[] = $valor_descripcion_compromiso;
		
		}
		
		if($data->datos_campos){
			$arreglo_fila = json_decode($data->datos_campos, true);
			$cont = 0;
			
			foreach($columnas as $columna) {
				$cont++;
				
				// Si existe el campo dentro de los valores del registro
				if(isset($arreglo_fila[$columna->id_campo])){
					
					if($columna->id_tipo_campo == 2){ // Si es text area
						
						$tooltip_textarea = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$arreglo_fila[$columna->id_campo].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? $tooltip_textarea : "-";
					
					}elseif($columna->id_tipo_campo == 4){//si es fecha.
						$valor_campo = get_date_format($arreglo_fila[$columna->id_campo],$id_proyecto);
					}elseif($columna->id_tipo_campo == 5){// si es periodo
						$start_date = $arreglo_fila[$columna->id_campo]['start_date'];
						$end_date = $arreglo_fila[$columna->id_campo]['end_date'];
						$valor_campo = $start_date.' - '.$end_date;
					}elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
						continue;
					}elseif($columna->id_tipo_campo == 14){
						$valor_campo = convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id_campo]);
					}
					else{
						$valor_campo = $arreglo_fila[$columna->id_campo];
					}
					
				}else{
					if(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
						continue;
					}
					$valor_campo = '-';
				}
								
				$row_data[] = $valor_campo;
				
			}
			
		}
		
		if($tipo_matriz == "rca"){
			$row_data[] = $data->accion_cumplimiento_control;
			$row_data[] = $data->frecuencia_ejecucion;
		}else{
			
			$planificaciones = $this->Plans_reportables_compromises_model->get_all_where(
				array(
					"id_compromiso" => $data->id,
					"deleted" => 0,
				)
			)->result();
			
			$html_planes = "";
			foreach($planificaciones as $planificacion){
				$html_planes .= "&bull; ".get_date_format($planificacion->planificacion, $id_proyecto)."<br>";
			}
			$row_data[] = $html_planes;
			
		}
		
		$view = modal_anchor(get_uri("upload_compromises/preview/" .$id_compromiso_proyecto."/".$tipo_matriz), "<i class='fa fa-eye'></i>", array("class" => "view", "title" => lang('view_compromise'), "data-post-id" => $data->id));
		$edit = modal_anchor(get_uri("upload_compromises/modal_form_carga_individual/".$id_compromiso_proyecto."/".$tipo_matriz), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_compromise'), "data-post-id" => $data->id));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compromise'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("upload_compromises/delete/".$tipo_matriz), "data-action" => "delete-confirmation"));
		
		$row_data[] = $view . $edit . $delete;
		
		return $row_data;
		
	}
	
	function carga_masiva($id_compromiso_proyecto, $tipo_matriz) {
		
		$id_compromiso = $id_compromiso_proyecto;
		if($tipo_matriz == "rca"){
			$compromiso = $this->Compromises_rca_model->get_one($id_compromiso);
		}else{
			$compromiso = $this->Compromises_reportables_model->get_one($id_compromiso);
		}
		
		$id_proyecto = $compromiso->id_proyecto;
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$id_cliente = $proyecto->client_id;
		
		$excel_template = $this->get_excel_template_of_compromise($id_compromiso_proyecto, $id_cliente, $id_proyecto, $tipo_matriz);
		
		$view_data["id_cliente"] = $id_cliente;
		$view_data["id_proyecto"] = $id_proyecto;
		$view_data["id_compromiso"] = $id_compromiso;
		$view_data["excel_template"] = $excel_template;
		$view_data["tipo_matriz"] = $tipo_matriz;
		
        $this->load->view('upload_compromises/carga_masiva/index', $view_data);
    }
	
	function save_carga_masiva($tipo_matriz){
		
		$id_cliente = $this->input->post('id_cliente');
		$id_proyecto = $this->input->post('id_proyecto');
		$id_compromiso_proyecto = $this->input->post('id_compromiso');
		$file = $this->input->post('archivo_importado');
		
		if($tipo_matriz == "rca"){
			$Compromises_model = $this->Compromises_rca_model;
		}else{
			$Compromises_model = $this->Compromises_reportables_model;
		}

		$archivo_subido = move_temp_file($file, "files/carga_masiva_compromisos/client_".$id_cliente."/project_".$id_proyecto."/", "", "", $file);
		
		if($archivo_subido){
			
			$this->load->library('excel');
			
			$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$archivo_subido);
			$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$archivo_subido);
			$worksheet = $excelObj->getSheet(0);
			$lastRow = $worksheet->getHighestRow();
			
			// COMPROBACION DE DATOS CORRECTOS
			$num_errores = 0;
			$msg_obligatorio = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_obligatory_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_formato = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_format_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_columna = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_column_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_date_range = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_date_range_field').'"><i class="fa fa-question-circle"></i></span>';
			
			$campos_compromiso = $Compromises_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
			
			$html = '<table class="table table-responsive table-striped">';
			$html .= '<thead><tr>';
			$html .= '<th></th>';
			
			//	COMPROBAR CABECERAS
			if($tipo_matriz == "rca"){

				if(lang('compromise_number') == $worksheet->getCell('A1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('A1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('A1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}

				if(lang('name') == $worksheet->getCell('B1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('B1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('B1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				if(lang('phases') == $worksheet->getCell('C1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('C1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('C1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				if(lang('reportability') == $worksheet->getCell('D1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('D1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('D1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
			
				$cont = 4;
				
			}else{

				if(lang('n_activity') == $worksheet->getCell('A1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('A1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('A1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}

				if(lang('environmental_management_instrument') == $worksheet->getCell('B1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('B1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('B1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}

				if(lang('phase_reportable') == $worksheet->getCell('C1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('C1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('C1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}

				if(lang('compliance_type') == $worksheet->getCell('D1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('D1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('D1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}

				if(lang('environmental_topic') == $worksheet->getCell('E1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('E1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('E1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}

				if(lang('impact_on_the_environment_due_to_non_compliance') == $worksheet->getCell('F1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('F1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('F1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				if(lang('action_type') == $worksheet->getCell('G1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('G1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('G1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				if(lang('responsible_area') == $worksheet->getCell('H1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('H1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('H1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				if(lang('environmental_commitment') == $worksheet->getCell('I1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('I1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('I1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}

				$cont = 9;
				
			}
			
			foreach($campos_compromiso as $campo){
				
				if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				$letra_columna = $this->getNameFromNumber($cont);
				$valor_columna = $worksheet->getCell($letra_columna.'1')->getValue();
				//var_dump($campo->nombre_campo);
				//var_dump($valor_columna);
				//echo "se compara valor excel:".$valor_columna." con valor base de datos:".$campo->nombre."<br>";
				if($campo->nombre_campo == $valor_columna){
					$html .= '<th>'.$valor_columna.'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$valor_columna.' '.$msg_columna.'</th>';
					$num_errores++;
				}
				$cont++;
			}
			
			if($tipo_matriz == "rca"){
			
				if(lang('compliance_action_control') == $worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue()){
						$html .= '<th>'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				$cont++;
				
				if(lang('execution_frequency') == $worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue()){
						$html .= '<th>'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
			}else{
				
				if(lang('planning_description') == $worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue()){
						$html .= '<th>'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				$cont++;
				
				if(lang('planning_date') == $worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue()){
						$html .= '<th>'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
			}
			
			$html .= '</tr></thead>';
			$html .= '<tbody>';
			
			// DEFINICIÓN DE LOS VALORES QUE PUEDEN TENER LAS CELDAS TIPO LISTA
			if($tipo_matriz == "rca"){
			
				// CREAR ARREGLO DE LAS FASES DEL SISTEMA 1 SOLA VEZ
				$fases_disponibles = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
				$array_fases_disponibles = array();
				foreach($fases_disponibles as $fase){
					$array_fases_disponibles[] = lang($fase["nombre_lang"]);
				}
			} else { // REPORTABLE

				// INSTRUMENTO DE GESTIÓN AMBIENTAL
				$array_environmental_management_instrument = array(
					lang("mpama"),
					lang("pama"),
					lang("pama_and_mpama"),
					lang("dia"),
					lang("mdia"),
					lang("dia_and_mdia"),
					lang("n/a")
				);

				// ETAPA
				$array_phase= array(
					lang("construction"),
					lang("operation"),
					lang("closing"),
				);

				//TIPO DE CUMPLIMIENTO
				$array_compliance_types = array(
					lang("environmental_adaptation_actions"),
					lang("mitigation_measures_in_existing_components"),
					lang("prevention_control_and_mitigation_measures_in_projected_components"),
					lang("measures_for_the_prevention_control_and_mitigation_of_environmental_impacts"),
					lang("closure_or_abandonment_plan"),
					lang("contingency_plan"),
					lang("solid_waste_management_plan"),
					lang("main_environmental_obligations"),
					lang("environmental_education_program"),
					lang("environmental_monitoring_program"),
					lang("citizen_participation_program"),
					lang("environmental_signage_program"),
					lang("preventive_or_corrective_program"),
					lang("closing_activities_projected_components")
				);

				// TEMA AMBIENTAL
				$array_environmental_topic = array(
					lang("water"),
					lang("air"),
					lang("air/noise"),
					lang("control_of_agricultural_inputs"),
					lang("crops"),
					lang("environmental_education"),
					lang("industrial_and_domestic_effluents"),
					lang("social_environment"),
					lang("flora_and_fauna"),
					lang("non_hazardous_waste"),
					lang("hazardous_waste"),
					lang("hazardous_and_non_hazardous_waste"),
					lang("security_and_health_at_work"),
					lang("ground"),
					lang("other"),
				);

				// AFECTACIÓN AL MEDIO POR INCLUMPLIMIENTO
				$array_impact_on_the_environment_due_to_non_compliance = array(
					lang("water"),
					lang("air_and_noise"),
					lang("community/social_environment"),
					lang("flora_and_fauna"),
					lang("solid_waste"),
					lang("hazardous_waste"),
					lang("non_hazardous_waste"),
					lang("health"),
					lang("ground"),
					lang("physical_environment"),	
					lang("physical_and_socioeconomic_environment")
				);

				// ÁREA RESPONSABLE
				$array_responsible_area = array(
					lang("personal_administration"),
					lang("warehouse"),
					lang("quality"),
					lang("field"),
					lang("training"),
					lang("communications"),
					lang("crops"),
					lang("management"),
					lang("icp"),
					lang("maintenance"),
					lang("machinery"),
					lang("operations"),
					lang("packing"),
					lang("landscaping"),
					lang("pre_mix"),
					lang("projects"),
					lang("recruitment"),
					lang("social_responsability"),
					lang("irrigation"),
					lang("occupational_health"),
					lang("industrial_security"),
					lang("general_services"),
					lang("sig"),
					lang("transportation"),
					lang("environment")
				);
			
			}
			
			// DATOS DEL CUERPO
			for($row = 2; $row <= $lastRow; $row++){
				$html .= '<tr>';
				$html .= '<td>'.$row.'</td>';

				if($tipo_matriz == "rca"){

					//NUMERO COMPROMISO
					$numero_compromiso = $worksheet->getCell('A'.$row)->getValue();
					if(strlen(trim($numero_compromiso)) > 0){

						if(is_numeric($numero_compromiso)){
							$html .= '<td>'.$numero_compromiso.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$numero_compromiso.' '.$msg_formato.'</td>';
							$num_errores++;
						}

					}else{
						$html .= '<td class="error app-alert alert-danger">'.$numero_compromiso.' '.$msg_obligatorio.'</td>';
						$num_errores++;
					}
					
					// CELDA NOMBRE
					$nombre_compromiso = $worksheet->getCell('B'.$row)->getValue();
					if(strlen(trim($nombre_compromiso)) > 0){
						$html .= '<td>'.$nombre_compromiso.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$nombre_compromiso.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
					// CELDA FASES
					$fases = $worksheet->getCell('C'.$row)->getValue();
					$array_fases = explode(',', $fases);
					$array_fases_final = array();
					$error_fases = FALSE;
					
					foreach($array_fases as $nombre_fase){
						$nombre_fase_limpia = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $nombre_fase)));
						if(!in_array($nombre_fase_limpia, $array_fases_disponibles)){
							$error_fases = TRUE;
						}
						$array_fases_final[] = $nombre_fase_limpia;
					}
					
					$html_fases = "";
					foreach($array_fases_final as $nombre_fase){
						$html_fases .= "&bull; " . $nombre_fase . "<br>";
					}
					
					if(!$error_fases){
						$html .= '<td>'.$html_fases.'</td>';
					} else {
						$html .= '<td class="error app-alert alert-danger">'.$html_fases.' '.$msg_formato.'</td>';
						$num_errores++;
					}
						
					// CELDA REPORTABILIDAD
					$reportabilidad = $worksheet->getCell('D'.$row)->getValue();
					$reportabilidad_mayus = strtoupper($reportabilidad);
					
					if($reportabilidad_mayus == "SI"){
						$html .= '<td><i class="fa fa-check" aria-hidden="true"></i></td>';
					} else if($reportabilidad_mayus == "NO"){
						$html .= '<td><i class="fa fa-times" aria-hidden="true"></i></td>';
					} else {
						$html .= '<td class="error app-alert alert-danger">'.$reportabilidad.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
				}else{ 
						
					// CELDA N° ACTIVIDAD
					$n_activity = $worksheet->getCell('A'.$row)->getValue();
					if(strlen(trim($n_activity)) > 0){
						$html .= '<td>'.$n_activity.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$n_activity.' '.$msg_formato.'</td>';
						$num_errores++;
					}

					// INSTRUMENTO DE GESTIÓN AMBIENTAL
					$environmental_management_instrument = $worksheet->getCell('B'.$row)->getValue();
					if(in_array($environmental_management_instrument, $array_environmental_management_instrument)){
						$html .= '<td>'.$environmental_management_instrument.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$environmental_management_instrument.' '.$msg_obligatorio.'</td>';
						$num_errores++;
					}

					// ETAPA
					$phase = $worksheet->getCell('C'.$row)->getValue();
					if(in_array($phase, $array_phase)){
						$html .= '<td>'.$phase.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$phase.' '.$msg_obligatorio.'</td>';
						$num_errores++;
					}

					// CELDA TIPO CUMPLIMIENTO
					$compliance_type = $worksheet->getCell('D'.$row)->getValue();
					if(in_array($compliance_type, $array_compliance_types)){
						$html .= '<td>'.$compliance_type.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$compliance_type.' '.$msg_formato.'</td>';
						$num_errores++;
					}

					// CELDA TEMA AMBIENTAL
					$environmental_topic = $worksheet->getCell('E'.$row)->getValue();
					if(in_array($environmental_topic, $array_environmental_topic)){
						$html .= '<td>'.$environmental_topic.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$environmental_topic.' '.$msg_formato.'</td>';
						$num_errores++;
					}

					// CELDA AFECTACIÓN AL MEDIO POR INCLUMPLIMIENTO
					$impact_on_environment = $worksheet->getCell('F'.$row)->getValue();
					if(in_array($impact_on_environment, $array_impact_on_the_environment_due_to_non_compliance)){
						$html .= '<td>'.$impact_on_environment.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$impact_on_environment.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
					// CELDA TIPO ACCIÓN
					$action_type = $worksheet->getCell('G'.$row)->getValue();
					if(strlen(trim($action_type)) > 0){
						$html .= '<td>'.$action_type.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$action_type.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
					// CELDA ÁREA RESPONSABLE
					$celda = $worksheet->getCell('H'.$row)->getValue();
					$responsible_areas = explode(';',$celda);
					$datos_responsable_correctos = true;
					$html_responsables = '';
					foreach($responsible_areas as $responsible){
						if(in_array(trim($responsible), $array_responsible_area)){
							$html_responsables .= '&bull; '.$responsible.'<br>';
						}else{
							$datos_responsable_correctos = false;
						}
					}
					if($datos_responsable_correctos){
						$html .= '<td>'.$html_responsables.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$celda.' '.$msg_formato.'</td>';
						$num_errores++;
					}

					// CELDA DESCRIPCIÓN DEL COMPROMISO/AMBIENTAL
					$commitment_description = $worksheet->getCell('I'.$row)->getValue();
					if(strlen(trim($commitment_description)) > 0){
						$html .= '<td>'.$commitment_description.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$commitment_description .' '.$msg_formato.'</td>';
						$num_errores++;
					}

				}
				
				// OTRAS CELDAS
				if($tipo_matriz == "rca"){
					$cont = 4;
				}else{
					$cont = 9;
				}
				
				foreach($campos_compromiso as $campo){
					if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
						continue;
					}
					$letra_columna = $this->getNameFromNumber($cont);
					$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
					
					if($campo->id_tipo_campo == 1){		//Input text
						if($campo->obligatorio){
							if(strlen(trim($valor_columna)) > 0){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							$html .= '<td>'.$valor_columna.'</td>';
						}
						
					}
					if($campo->id_tipo_campo == 2){		//Texto Largo
						if($campo->obligatorio){

							if(strlen(trim($valor_columna)) > 0){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							$html .= '<td>'.$valor_columna.'</td>';
						}
					}
					if($campo->id_tipo_campo == 3){		//Número
						
						if($campo->obligatorio){
							if(strlen(trim($valor_columna)) > 0){
								
								if(is_numeric($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
										
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							if($valor_columna == "" || is_numeric($valor_columna)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}
						
						
					}
					if($campo->id_tipo_campo == 4){		//Fecha
						
						if($campo->obligatorio){
							if(strlen(trim($valor_columna)) > 0){
								
								if($this->validateDate($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
								
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							if($valor_columna == "" || $this->validateDate($valor_columna)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}
						
						
					}
					if($campo->id_tipo_campo == 5){		//Periodo
						
						if($campo->obligatorio){
							if(strlen($valor_columna) == 21){// YYYY-MM-DD/YYYY-MM-DD
							
								$array_periodo = explode("/", $valor_columna);
								$fecha_desde = $array_periodo[0];
								$fecha_hasta = $array_periodo[1];
								if($this->validateDate($fecha_desde) && $this->validateDate($fecha_hasta)){
									if((strtotime($fecha_hasta)) >= (strtotime($fecha_desde))){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_date_range.'</td>';
										$num_errores++;
									}
									
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
								
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}else{
							
							if(strlen($valor_columna) == 21){// YYYY-MM-DD/YYYY-MM-DD
								$array_periodo = explode("/", $valor_columna);
								$fecha_desde = $array_periodo[0];
								$fecha_hasta = $array_periodo[1];
								if($this->validateDate($fecha_desde) && $this->validateDate($fecha_hasta)){
									if((strtotime($fecha_hasta)) >= (strtotime($fecha_desde))){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_date_range.'</td>';
										$num_errores++;
									}
									
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
								
							}elseif(strlen($valor_columna) == 0){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
							
						}
						
					}
					if($campo->id_tipo_campo == 6){		//Selección
						$ops = json_decode($campo->opciones);
						$opciones = array();
						foreach($ops as $op){
							if($campo->obligatorio){
								if($op->value == ""){continue;}
							}else{
								if($op->value == ""){
									$opciones[] = "";
									continue;

								}
							}
							$opciones[] = $op->text;
						}
						
						if(in_array($valor_columna, $opciones)){
							$html .= '<td>'.$valor_columna.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
							$num_errores++;
						}
						
						
					}
					if($campo->id_tipo_campo == 7){		//select_multiple
						$ops = json_decode($campo->opciones);
						$opciones = array();
						foreach($ops as $op){
							if($campo->obligatorio){
								if($op->value == ""){continue;}
							}else{
								if($op->value == ""){
									$opciones[] = "";
									continue;

								}
							}
							$opciones[] = $op->text;
						}
						
						if(in_array($valor_columna, $opciones)){
							$html .= '<td>'.$valor_columna.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
							$num_errores++;
						}
					}
					if($campo->id_tipo_campo == 8){		//rut
						// POR AHORA NO ESTAMOS VALIDANDO CAMPO RUT
						if($campo->obligatorio){
							if(strlen(trim($valor_columna)) > 0){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							$html .= '<td>'.$valor_columna.'</td>';
						}
						
					}
					if($campo->id_tipo_campo == 9){		//Radio Buttons
						// CAMPO RADIO, SIEMPRE SERA OBLIGATORIO
						
						$ops = json_decode($campo->opciones);
						$opciones = array();
						foreach($ops as $op){
							$opciones[] = $op->value;
						}
						
						if(in_array($valor_columna, $opciones)){
							$html .= '<td>'.$valor_columna.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
							$num_errores++;
						}
					
					}
					if($campo->id_tipo_campo == 13){	//Correo
						
						if($campo->obligatorio){
							if(strlen(trim($valor_columna)) > 0){
								if(valid_email($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							if($valor_columna == "" || valid_email($valor_columna)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}
						
					}
					if($campo->id_tipo_campo == 14){	//Hora
						// OJO CON ESTE, DEPENDE DEL FORMATO DE HORA
						
						if($campo->obligatorio){
							//if(strlen($valor_columna) == 8){// 12:00 PM
							if(strlen($valor_columna) == 5){// 12:00
								//if(preg_match('/\d{2}:\d{2} (AM|PM)/', $valor_columna)){
								if(preg_match('/\d{2}:\d{2}/', $valor_columna)){
									$hora = explode(":", $valor_columna);
									if( ($hora[0] >= "00" && $hora[0] <= "23") && ($hora[1] >= "00" && $hora[1] <= "59") ){
										$html .= '<td>'.$valor_columna.'</td>';
									} else {
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}elseif(strlen(trim($valor_columna)) == 0){
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}else{
							//if($valor_columna == "" || preg_match('/\d{2}:\d{2} (AM|PM)/', $valor_columna)){
							if($valor_columna == "" || preg_match('/\d{2}:\d{2}/', $valor_columna)){
								$hora = explode(":", $valor_columna);
								if( ($hora[0] >= "00" && $hora[0] <= "23") && ($hora[1] >= "00" && $hora[1] <= "59") ){
									$html .= '<td>'.$valor_columna.'</td>';
								} else {
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}

					}
					if($campo->id_tipo_campo == 15){	//Unidad
						
						if($campo->obligatorio){
							if(strlen(trim($valor_columna)) > 0){
								
								if(is_numeric($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
										
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							if($valor_columna == "" || is_numeric($valor_columna)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}
						
					}
					
					$cont++;
				}
				
				if($tipo_matriz == "rca"){
					
					// CELDA ACCION
					$accion = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
					if(strlen(trim($accion)) > 0){
						$html .= '<td>'.$accion.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$accion.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
					$cont++;
					
					// CELDA FRECUENCIA EJECUCION
					$frecuencia = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
					if(strlen(trim($frecuencia)) > 0){
						$html .= '<td>'.$accion.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$frecuencia.' '.$msg_formato.'</td>';
						$num_errores++;
					}
				}else{
					
					// CELDA DESCRIPCIONES
					$descripciones = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
					$array_descripciones = explode(';', $descripciones);
					
					$html_descripciones = "";
					foreach($array_descripciones as $desc){
						$html_descripciones .= "&bull; " . $desc . "<br>";
					}
					
					if(count($array_descripciones) > 0){
						$html .= '<td>'.$html_descripciones.'</td>';
					} else {
						$html .= '<td class="error app-alert alert-danger">'.$html_descripciones.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
					$cont++;
					
					// CELDA PLANIFICACIONES
					$planificaciones = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
					$array_planificaciones = explode(';', $planificaciones);
					$error_planificaciones = FALSE;
					
					$html_planificaciones = "";
					foreach($array_planificaciones as $plan){
						
						$html_planificaciones .= "&bull; " . $plan . "<br>";
						
						if(strlen($plan) == 10){// YYYY-MM-DD
						
							if($this->validateDate($plan)){
								
							}else{
								$error_planificaciones = TRUE;
							}
						}else{
							$error_planificaciones = TRUE;
						}
						
					}
					
					if(count($array_planificaciones) != count($array_descripciones)){
						$error_planificaciones = TRUE;
					}
					
					if(!$error_planificaciones){
						$html .= '<td>'.$html_planificaciones.'</td>';
					} else {
						$html .= '<td class="error app-alert alert-danger">'.$html_planificaciones.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
				}
				

				$html .= '</tr>';

			}
			
			$html .= '</tbody>';
			$html .= '</table>';			

			if($num_errores > 0){
				echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => $html));
			}else{
				$this->bulk_load($id_cliente, $id_proyecto, $id_compromiso_proyecto, $archivo_subido, $tipo_matriz);
				//echo json_encode(array("success" => true, 'message' => lang('record_saved'), 'table' => $html));
			}
			
			exit();

			
		}
		
	}
	
	function bulk_load($id_cliente, $id_proyecto, $id_compromiso_proyecto, $archivo_subido, $tipo_matriz){
		
		if($tipo_matriz == "rca"){
			$Compromises_model = $this->Compromises_rca_model;
			$Values_compromises_model = $this->Values_compromises_rca_model;
			$Evaluated_compromises_model = $this->Evaluated_rca_compromises_model;
			$Compromises_compliance_evaluation_model = $this->Compromises_compliance_evaluation_rca_model;
		}else{
			$Compromises_model = $this->Compromises_reportables_model;
			$Values_compromises_model = $this->Values_compromises_reportables_model;
			$Evaluated_compromises_model = $this->Plans_reportables_compromises_model;
			$Compromises_compliance_evaluation_model = $this->Compromises_compliance_evaluation_reportables_model;
		}
		
		$compromiso = $Compromises_model->get_one($id_compromiso_proyecto);

		$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$archivo_subido);
		$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$archivo_subido);
		$worksheet = $excelObj->getSheet(0);
		$lastRow = $worksheet->getHighestRow();
		$campos_compromiso = $Compromises_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
		$array_insert = array();
		$array_planes = array();
		
		if($tipo_matriz == "rca"){
			// CREAR ARREGLO DE LAS FASES DEL SISTEMA 1 SOLA VEZ, CON LOS LANG
			$fases_disponibles = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
			$array_fases_disponibles = array();
			foreach($fases_disponibles as $fase){
				$array_fases_disponibles[lang($fase["nombre_lang"])] = $fase["id"];
			}
		}
		
		// POR CADA FILA
		for($row = 2; $row <= $lastRow; $row++){
			
			$array_row = array();
			
			
			if($tipo_matriz == "rca"){

				$numero_compromiso = (int)$worksheet->getCell('A'.$row)->getValue();
				$nombre_compromiso = $worksheet->getCell('B'.$row)->getValue();

				//
				$fases = $worksheet->getCell('C'.$row)->getValue();
				$array_fases = explode(',', $fases);
				$array_fases_final = array();
				
				foreach($array_fases as $nombre_fase){
					$nombre_fase_limpia = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $nombre_fase)));
					$id_fase = $array_fases_disponibles[$nombre_fase_limpia];
					$array_fases_final[] = $id_fase;
				}
				$array_fases_json = json_encode($array_fases_final);
				//
			
				$reportabilidad = $worksheet->getCell('D'.$row)->getValue();
				$reportabilidad_mayus = strtoupper($reportabilidad);
				
				if($reportabilidad_mayus == "SI"){
					$reportabilidad = 1;
				} else {
					$reportabilidad = 0;
				}
			}else{

				// CELDA N° ACTIVIDAD
				$n_activity = $worksheet->getCell('A'.$row)->getValue();

				// CELDA INSTRUMENTO DE GESTIÓN AMBIENTAL
				$array_environmental_management_instrument = array(
					lang("mpama") => "mpama",
					lang("pama") => "pama",
					lang("pama_and_mpama") => "pama_and_mpama",
					lang("dia") => "dia",
					lang("mdia") => "mdia",
					lang("dia_and_mdia") => "dia_and_mdia",
					lang("n/a") => "n/a"
				);

				$valor_celda_b = $worksheet->getCell('B'.$row)->getValue();
				$environmental_management_instrument = $array_environmental_management_instrument["$valor_celda_b"];

				// CELDA INSTRUMENTO DE ETAPA
				$array_phase = array(
					lang("construction") => "construction",
					lang("operation") => "operation",
					lang("closing") => "closing"
				);

				$valor_celda_c = $worksheet->getCell('C'.$row)->getValue();
				$phase = $array_phase["$valor_celda_c"];


				// CELDA TIPO DE CUMPLIMIENTO
				$array_compliance_types = array(
					lang("environmental_adaptation_actions") => "environmental_adaptation_actions",
					lang("mitigation_measures_in_existing_components") => "mitigation_measures_in_existing_components",
					lang("prevention_control_and_mitigation_measures_in_projected_components") => "prevention_control_and_mitigation_measures_in_projected_components",
					lang("measures_for_the_prevention_control_and_mitigation_of_environmental_impacts") => "measures_for_the_prevention_control_and_mitigation_of_environmental_impacts",
					lang("closure_or_abandonment_plan") => "closure_or_abandonment_plan",
					lang("contingency_plan") => "contingency_plan",
					lang("solid_waste_management_plan") => "solid_waste_management_plan",
					lang("main_environmental_obligations") => "main_environmental_obligations",
					lang("environmental_education_program") => "environmental_education_program",
					lang("environmental_monitoring_program") => "environmental_monitoring_program",
					lang("citizen_participation_program") => "citizen_participation_program",
					lang("environmental_signage_program") => "environmental_signage_program",
					lang("preventive_or_corrective_program") => "preventive_or_corrective_program",
					lang("closing_activities_projected_components") => "closing_activities_projected_components",
				);

				$valor_celda_d = $worksheet->getCell('D'.$row)->getValue();
				$compliance_type = $array_compliance_types["$valor_celda_d"];

				/* if($environmental_topic == "Agua"){
					$environmental_topic = "water";
				}elseif($environmental_topic == "Aire"){
					$environmental_topic = "air";
				}elseif($environmental_topic == "Plaguicidas"){
					$environmental_topic = "pesticides";
				}elseif($environmental_topic == "Residuos no peligrosos"){
					$environmental_topic = "non_hazardous_waste";
				}elseif($environmental_topic == "Residuos y sustancias peligrosas"){
					$environmental_topic = "waste_and_hazardous_substances";
				}elseif($environmental_topic == "Residuos líquidos"){
					$environmental_topic = "liquid_waste";
				}
				*/
				// CELDA TEMA AMBIENTAL
				$array_environmental_topic = array(
					lang("water") => "water",
					lang("air") => "air",
					lang("air/noise") => "air/noise",
					lang("control_of_agricultural_inputs") => "control_of_agricultural_inputs",
					lang("crops") => "crops",
					lang("environmental_education") => "environmental_education",
					lang("industrial_and_domestic_effluents") => "industrial_and_domestic_effluents",
					lang("social_environment") => "social_environment",
					lang("flora_and_fauna") => "flora_and_fauna",
					lang("non_hazardous_waste") => "non_hazardous_waste",
					lang("hazardous_waste") => "hazardous_waste",
					lang("hazardous_and_non_hazardous_waste") => "hazardous_and_non_hazardous_waste",
					lang("security_and_health_at_work") => "security_and_health_at_work",
					lang("ground") => "ground",
					lang("other") => "other",
				);

				$valor_celda_e = $worksheet->getCell('E'.$row)->getValue();
				$environmental_topic = $array_environmental_topic["$valor_celda_e"];

				// CELDA AFECTACIÓN AL MEDIO POR INCLUMPLIMIENTO
				$array_impact_on_the_environment_due_to_non_compliance = array(
					lang("water") => "water",
					lang("air_and_noise") => "air_and_noise",
					lang("community/social_environment") => "community/social_environment",
					lang("flora_and_fauna") => "flora_and_fauna",
					lang("solid_waste") => "solid_waste",
					lang("hazardous_waste") => "hazardous_waste",
					lang("non_hazardous_waste") => "non_hazardous_waste",
					lang("health") => "health",
					lang("ground") => "ground",
					lang("physical_environment") => "physical_environment",
					lang("physical_and_socioeconomic_environment") => "physical_and_socioeconomic_environment"
				);

				$valor_celda_f = $worksheet->getCell('F'.$row)->getValue();
				$impact_on_the_environment_due_to_non_compliance = $array_impact_on_the_environment_due_to_non_compliance["$valor_celda_f"];
				
				// CELDA TIPO DE ACCIÓN
				$action_type = $worksheet->getCell('G'.$row)->getValue();

				// OPCIONES PARA CELDA ÁREA RESPONSABLE
				$array_responsible_area = array(
					lang("personal_administration") => "personal_administration",
					lang("warehouse") => "warehouse",
					lang("quality") => "quality",
					lang("field") => "field",
					lang("training") => "training",
					lang("communications") => "communications",
					lang("crops") => "crops",
					lang("management") => "management",
					lang("icp") => "icp",
					lang("maintenance") => "maintenance",
					lang("machinery") => "machinery",
					lang("operations") => "operations",
					lang("packing") => "packing",
					lang("landscaping") => "landscaping",
					lang("pre_mix") => "pre_mix",
					lang("projects") => "projects",
					lang("recruitment") => "recruitment",
					lang("social_responsability") => "social_responsability",
					lang("irrigation") => "irrigation",
					lang("occupational_health") => "occupational_health",
					lang("industrial_security") => "industrial_security",
					lang("general_services") => "general_services",
					lang("sig") => "sig",
					lang("transportation") => "transportation",
					lang("environment") => "environment"
				);

				$array_valores_celda_h = explode(';',$worksheet->getCell('H'.$row)->getValue());
				
				$responsible_area_trimed = array();
				foreach($array_valores_celda_h as $valor){
					$responsible_area_trimed[] = $array_responsible_area[trim($valor)];
				}
				// Elimino posibles entradas duplicadas, array_values se usa para quitar los indices numericos que agrega array_unique.
				$responsible_area = array_values(array_unique($responsible_area_trimed));

				// CELDA DESCRIPCIÓN DEL PROCESO
				$commitment_description = $worksheet->getCell('I'.$row)->getValue();

			}
			
			if($tipo_matriz == "rca"){
				$cont = 4;
			}else{
				$cont = 9;
			}
			$array_campos_json = array();
			foreach($campos_compromiso as $campo){ 
				
				if($campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				if($campo->id_tipo_campo == 10){// ARCHIVO (DEBE IR SI O SI EL ID DEL CAMPO, POR LO QUE LO AGREGAREMOS VACIO)
					$array_campos_json["$campo->id_campo"] = NULL;
					continue;
				}
				
				$letra_columna = $this->getNameFromNumber($cont);
				$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
				//echo var_dump($letra_columna.$row.' - '.$campo->id_tipo_campo.': '.$valor_columna);
				
				if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 2 || $campo->id_tipo_campo == 3 || $campo->id_tipo_campo == 4){
					//$array_campos_json["$campo->id_campo"] = $valor_columna;
					// CAMPO DESHABILITADO = 1
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 5){
					if($campo->obligatorio){
						$array_periodo = explode("/", $valor_columna);
						$fecha_desde = $array_periodo[0];
						$fecha_hasta = $array_periodo[1];
						$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
					}else{
						/*if(trim($valor_columna) == ""){
							$json_periodo = array("start_date" => "", "end_date" => "");
						}else{
							$array_periodo = explode("/", $valor_columna);
							$fecha_desde = $array_periodo[0];
							$fecha_hasta = $array_periodo[1];
							$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
						}*/
						if($campo->habilitado == 1){
							if(trim($valor_columna) == ""){
								$json_periodo = array("start_date" => "", "end_date" => "");
							}else{
								$periodo = json_decode($campo->default_value);
								$json_periodo = array("start_date" => $periodo->start_date, "end_date" => $periodo->end_date);
							}
							
						}else{
							if(trim($valor_columna) == ""){
								$json_periodo = array("start_date" => "", "end_date" => "");
							}else{
								$array_periodo = explode("/", $valor_columna);
								$fecha_desde = $array_periodo[0];
								$fecha_hasta = $array_periodo[1];
								$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
							}
						}
						
					}
					
					$array_campos_json["$campo->id_campo"] = $json_periodo;
				}
				if($campo->id_tipo_campo == 6){
					/*$ops = json_decode($campo->opciones);
					$opciones = array();
					foreach($ops as $op){
						if($campo->obligatorio){
							if($op->value == ""){continue;}
						}else{
							if($op->value == ""){
								$opciones[""] = "";
								continue;

							}
						}
						$opciones[$op->text] = $op->value;
					}
					
					$array_campos_json["$campo->id_campo"] = $opciones[$valor_columna];*/
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
					
				}
				if($campo->id_tipo_campo == 8){// RUT
					//$array_campos_json["$campo->id_campo"] = $valor_columna;
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 9){// RADIO
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 13){// CORREO
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 14){// HORA
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 15){// UNIDAD
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				
				$cont++;
			}
			
			if($tipo_matriz == "rca"){
				$accion = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
				$cont++;
				$frecuencia = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
				$cont++;
			}else{
				$descripciones = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
				$cont++;
				$planificaciones = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
				$cont++;
			}
			
			
			
			$array_row["id_compromiso"] = $id_compromiso_proyecto;
			
			if($tipo_matriz == "rca"){

				$array_row["numero_compromiso"] = $numero_compromiso;
				$array_row["nombre_compromiso"] = $nombre_compromiso;
				$array_row["fases"] = $array_fases_json;
				$array_row["reportabilidad"] = $reportabilidad;
				$array_row["accion_cumplimiento_control"] = $accion;
				$array_row["frecuencia_ejecucion"] = $frecuencia;

			} else {
				
				$array_row["numero_actividad"] = $n_activity;
				$array_row["instrumento_gestion_ambiental"] = $environmental_management_instrument;
				$array_row["etapa"] = $phase;
				$array_row["tipo_cumplimiento"] = $compliance_type;
				$array_row["tema_ambiental"] = $environmental_topic;
				$array_row["afectacion_medio_por_incumplimiento"] = $impact_on_the_environment_due_to_non_compliance;
				$array_row["tipo_accion"] = $action_type;
				$array_row["area_responsable"] = json_encode($responsible_area);
				$array_row["descripcion_compromiso"] = $commitment_description;
				$array_planes[] = array("descripcion" => $descripciones, "fecha" => $planificaciones);
				
			}
			$json_datos_campos = json_encode($array_campos_json);
			$array_row["datos_campos"] = $json_datos_campos;
						
			$array_row["created_by"] = $this->login_user->id;
			$array_row["modified_by"] = NULL;
			$array_row["created"] = get_current_utc_time();
			$array_row["modified"] = NULL;
			$array_row["deleted"] = 0;
			
			$array_insert[] = $array_row;
		}// FIN FOR ROW
	
		$bulk_load = $Values_compromises_model->bulk_load($array_insert);
		if($bulk_load){
			
			if($tipo_matriz == "rca"){
				// SI SE INGRESAN LOS COMPROMISOS, AUTOMATICAMENTE SE DEBEN INGRESAR TANTAS EVALUACIONES 
				// COMO EVALUADOS HAYAN RELACIONADOS A ESTOS COMPROMISOS Y CON ESTADO POR DEFECTO NO APLICA
				// PRIMERO VOY A BUSCAR EL ID DEL ESTADO NO APLICA DEL CLIENTE
				
				$first_id = $this->db->insert_id();
				$last_id = $first_id + (count($array_insert) - 1);
				
				$array_ides = array();
				for($i = $first_id; $i <= $last_id; $i++) {
					$array_ides[] = $i;
				}
				
				foreach($array_insert as $index => $compromiso){
					$id_compromiso = $array_ides[$index];
					
					$estado_no_aplica = $this->Compromises_compliance_status_model->get_one_where(
						array(
							"id_cliente" => $id_cliente, 
							"categoria" => "No Aplica", 
							"deleted" => 0
						)
					);
					$id_estado = $estado_no_aplica->id;
					
					$evaluados_matriz = $Evaluated_compromises_model->get_all_where(array("id_compromiso" => $compromiso["id_compromiso"], "deleted" => 0))->result();
					foreach($evaluados_matriz as $evaluado){
						
						$data_compliance_evaluation = array();
						$data_compliance_evaluation["id_valor_compromiso"] = $id_compromiso;
						$data_compliance_evaluation["id_evaluado"] = $evaluado->id;
						$data_compliance_evaluation["id_estados_cumplimiento_compromiso"] = $id_estado;
						$data_compliance_evaluation["observaciones"] = NULL;
						$data_compliance_evaluation["responsable"] = $this->login_user->id;
						$data_compliance_evaluation["fecha_evaluacion"] = get_current_utc_time();
						//$data_compliance_evaluation["fecha_evaluacion"] = "2018-10-24";
						$data_compliance_evaluation["created_by"] = $this->login_user->id;
						$data_compliance_evaluation["created"] = get_current_utc_time();
						$evaluation_save_id = $Compromises_compliance_evaluation_model->save($data_compliance_evaluation);
						
					}
				}
			}else{
				// SI SE INGRESAN LOS COMPROMISOS, AUTOMATICAMENTE SE DEBEN INGRESAR LAS PLANIFICACIONES
				// Y TANTAS EVALUACIONES COMO PLANIFICACIONES HAYAN RELACIONADOS A ESTOS COMPROMISOS 
				// Y CON ESTADO POR DEFECTO "NO CUMPLE" O "PENDIENTE" DEPENDIENDO DE LA FECHA DE LA PLANIFICACION
				
				$first_id = $this->db->insert_id();
				$last_id = $first_id + (count($array_insert) - 1);
				
				$array_ides = array();
				for($i = $first_id; $i <= $last_id; $i++) {
					$array_ides[] = $i;
				}
				
				foreach($array_insert as $index => $compromiso){
					$id_compromiso = $array_ides[$index];
					
					$descripciones = $array_planes[$index]["descripcion"];
					$planificaciones = $array_planes[$index]["fecha"];
					
					$array_descripciones = explode(';', $descripciones);
					$array_plans = explode(';', $planificaciones);
					
					$array_planificaciones = array();
					foreach($array_descripciones as $index => $descripcion){
						$fecha_termino = $array_plans[$index];
						
						$data_planificacion = array();
						$data_planificacion["id_compromiso"] = $id_compromiso;
						$data_planificacion["descripcion"] = $descripcion;
						$data_planificacion["planificacion"] = $fecha_termino;
						$data_planificacion["created_by"] = $this->login_user->id;
						$data_planificacion["created"] = get_current_utc_time();
						
						$plan_save_id = $this->Plans_reportables_compromises_model->save($data_planificacion);
						
						if($plan_save_id){
							$data_planificacion["id"] = $plan_save_id;
							$array_planificaciones[] = $data_planificacion;
						}
					}
					
					// CONTINUO CON LAS COMBINACIONES COMPROMISO-PLANIFICACION
					// (CON ESTADO SEGUN CRITERIO DE FECHA)
					
					foreach($array_planificaciones as $fila_planificacion){
						
						$fecha = $fila_planificacion["planificacion"];
						
						if($fecha < date("Y-m-d")){
							// no cumple
							$estado_no_cumple = $this->Compromises_compliance_status_model->get_one_where(
								array(
									"id_cliente" => $id_cliente, 
									"tipo_evaluacion" => "reportable", 
									"categoria" => "No Cumple", 
									"deleted" => 0
								)
							);
							$id_estado = $estado_no_cumple->id;
						}else{
							// pendiente
							$estado_pendiente = $this->Compromises_compliance_status_model->get_one_where(
								array(
									"id_cliente" => $id_cliente, 
									"tipo_evaluacion" => "reportable", 
									"categoria" => "Pendiente", 
									"deleted" => 0
								)
							);
							$id_estado = $estado_pendiente->id;
						}
						
						$data_compliance_evaluation = array();
						$data_compliance_evaluation["id_valor_compromiso"] = $id_compromiso;
						$data_compliance_evaluation["id_planificacion"] = $fila_planificacion["id"];
						$data_compliance_evaluation["id_estados_cumplimiento_compromiso"] = $id_estado;
						$data_compliance_evaluation["observaciones"] = NULL;
						$data_compliance_evaluation["responsable"] = $this->login_user->id;
						$data_compliance_evaluation["fecha_evaluacion"] = get_current_utc_time();
						$data_compliance_evaluation["created_by"] = $this->login_user->id;
						$data_compliance_evaluation["created"] = get_current_utc_time();
						
						$evaluation_save_id = $this->Compromises_compliance_evaluation_reportables_model->save($data_compliance_evaluation);


						// Crear configuración por cada planificación de evaluación, con valores vacíos (ok).
						$data_alert_config = array(
							"id_client" => $id_cliente,
							"id_project" => $id_proyecto,
							"id_client_module" => 6, // Compromisos
							"id_client_submodule" => 22, // Evaluación de Compromisos Reportables
							"alert_config" => json_encode(array(
								"id_planificacion" => (string)$fila_planificacion["id"],
								"risk_value" => "",
								"threshold_value" => ""
							)),
							"created_by" => $this->login_user->id,
							"created" => get_current_utc_time()
						);
						
						$alert_save_id = $this->AYN_Alert_projects_model->save($data_alert_config);
						
						// Guardar histórico alertas por cada planificación de evaluación
						$data_historical_alert = array(
							"id_client" => $id_cliente,
							"id_project" => $id_proyecto,
							"id_user" => $this->login_user->id,
							"id_client_module" => 6, // Compromisos
							"id_client_submodule" => 22, // Evaluación de Compromisos Reportables
							"alert_config" => json_encode(array(
								"id_planificacion" => (string)$fila_planificacion["id"],
								"id_valor_compromiso" => (string)$id_compromiso,
								"tipo_evaluacion" => "reportable",
							), TRUE),
							"id_alert_projects" => $alert_save_id,
							"id_element" => $id_compromiso,
							"alert_date" => get_current_utc_time()
						);
						
						$historical_alert_save_id = $this->AYN_Alert_historical_model->save($data_historical_alert);
									
						
					}
					
					// Guardar histórico notificaciones
					$options = array(
						"id_client" => $id_cliente,
						"id_project" => $id_proyecto,
						"id_user" => $this->login_user->id,
						"module_level" => "admin",
						"id_admin_module" => $this->id_admin_module,
						"id_admin_submodule" => $this->id_admin_submodule,
						"event" => ($tipo_matriz == "rca") ? "comp_rca_add" : "comp_rep_add",
						"id_element" => $id_compromiso
					);
					ayn_save_historical_notification($options);
				}
			}
			
			echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
		}else{
			echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
		}
		
	}
	
	
	function preview($id_record = 0, $tipo_matriz){
		
		$id_compromiso_proyecto = $id_record;
		$id_elemento = $this->input->post('id');
		
		if($tipo_matriz == "rca"){
			$campos_compromiso = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
			$id_proyecto = $this->Compromises_rca_model->get_one($id_compromiso_proyecto)->id_proyecto;
		}else{
			$campos_compromiso = $this->Compromises_reportables_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
			$id_proyecto = $this->Compromises_reportables_model->get_one($id_compromiso_proyecto)->id_proyecto;
		}
		
		$view_data['campos_compromiso'] = $campos_compromiso;
		$view_data['id_compromiso'] = $id_record;
		$view_data['id_proyecto'] = $id_proyecto;
		
		if($id_elemento){
			if($tipo_matriz == "rca"){
				$model_info = $this->Values_compromises_rca_model->get_one($id_elemento);
				
				$fases_decoded = json_decode($model_info->fases);
				$html_fases = "";
				foreach($fases_decoded as $id_fase){
					$nombre_lang = $this->Phases_model->get_one($id_fase)->nombre_lang;
					$nombre_fase = lang($nombre_lang);
					$html_fases .= "&bull; " . $nombre_fase . "<br>";
				}
				$view_data['html_fases'] = $html_fases;
				
			}else{
				$model_info = $this->Values_compromises_reportables_model->get_one($id_elemento);

				$responsible_area_decoded = json_decode($model_info->area_responsable);
				$html_responsible_area = '';
				foreach($responsible_area_decoded as $area){
					$nombre_area = lang($area);
					$html_responsible_area .= "&bull; " . $nombre_area . "<br>";
				}
				$view_data['html_responsible_area'] = $html_responsible_area;
				
				$planificaciones = $this->Plans_reportables_compromises_model->get_all_where(
					array(
						"id_compromiso" => $id_elemento,
						"deleted" => 0,
					)
				)->result();
				
				$html_planes = "";
				foreach($planificaciones as $planificacion){
					$html_planes .= "&bull; ".get_date_format($planificacion->planificacion, $id_proyecto)."<br>";
				}
				$view_data['html_planes'] = $html_planes;
				
			}
			
			$view_data['model_info'] = $model_info;
			
		}
		
		$view_data["Upload_compromises_controller"] = $this;
		$view_data["tipo_matriz"] = $tipo_matriz;

        $this->load->view('upload_compromises/carga_individual/view', $view_data);
		
	}
		
	function delete($tipo_matriz) {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
		// SI EL COMPROMISO TIENE EVALUACION NO SE PUEDE ELIMINAR
		// POSTERIORMENTE SE CAMBIO, SI SE PUEDEN ELIMINAR Y DE PASO SE ELIMINAN SUS EVALUACIONES
		if($tipo_matriz == "rca"){
			$evaluaciones_compromiso = $this->Compromises_compliance_evaluation_rca_model->get_all_where(array(
				"id_valor_compromiso" => $id,
				"deleted" => 0
			))->result();
			foreach($evaluaciones_compromiso as $evaluacion){
				$this->Compromises_compliance_evaluation_rca_model->delete($evaluacion->id);
			}
			
		}else{
			$evaluaciones_compromiso = $this->Compromises_compliance_evaluation_reportables_model->get_all_where(array(
				"id_valor_compromiso" => $id,
				"deleted" => 0
			))->result();
			foreach($evaluaciones_compromiso as $evaluacion){
				$this->Compromises_compliance_evaluation_reportables_model->delete($evaluacion->id);
			}
		}
		
		/*if($evaluaciones_compromiso){
			echo json_encode(array("success" => false, 'message' => lang('cant_delete_compromise')));
			exit();
		}*/
		
		if($tipo_matriz == "rca"){
			$Values_compromises_model = $this->Values_compromises_rca_model;
		}else{
			$Values_compromises_model = $this->Values_compromises_reportables_model;
		}

        if ($this->input->post('undo')) {
            if ($Values_compromises_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($Values_compromises_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	/* devolver dropdown con los proyectos de un cliente */	
	function get_projects_of_client(){
	
		$id_cliente = $this->input->post('id_client');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$proyectos_de_cliente = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente, "deleted" => 0));
		
		$html = '';
		$html .= '<div class="col-md-4 p0">';
		$html .= '<label for="project" class="col-md-2">'.lang('project').'</label>';
		$html .= '<div class="col-md-10">';
		$html .= form_dropdown("project", array("" => "-") + $proyectos_de_cliente, "", "id='project' class='select2 validate-hidden col-md-12' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
				
	}
	
	/* devolver dropdown con los tipos de matrices */	
	function get_matrix_types(){

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$html = '';
		$html .= '<div class="col-md-4 p0">';
		$html .= '<label for="matrix_type" class="col-md-2">'.lang('matrix_type').'</label>';
		$html .= '<div class="col-md-10">';
		$html .=  form_dropdown("matrix_type", array("" => "-", "rca" => lang("rca"), "reportable" => lang("reportable")), "", "id='matrix_type' class='select2 validate-hidden col-md-12' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
				
	}
	
	function get_upload_compromises_of_project(){
		
		$id_cliente = $this->input->post("id_cliente");
		$id_proyecto = $this->input->post("id_proyecto");
		$tipo_matriz = $this->input->post("matrix_type");
		
		$view_data["nombre_proyecto"] = $this->Projects_model->get_one($id_proyecto)->title;
		if($tipo_matriz == "rca"){
			$view_data["id_compromiso_proyecto"] = $this->Compromises_rca_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0))->id;
		}else{
			$view_data["id_compromiso_proyecto"] = $this->Compromises_reportables_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0))->id;
		}
		
		$view_data["tipo_matriz"] = $tipo_matriz;
		
		$this->load->view("upload_compromises/upload_compromises_of_project", $view_data);
		
	}
	
	function get_field($id_campo, $id_elemento, $preview, $tipo_matriz){
		
        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$datos_campo = $this->Fields_model->get_one($id_campo);
		$id_tipo_campo = $datos_campo->id_tipo_campo;
		$etiqueta = $datos_campo->nombre;
		$name = $datos_campo->html_name;
		$default_value = $datos_campo->default_value;
		
		$opciones = $datos_campo->opciones;
		if($opciones){
			$array_opciones = json_decode($opciones, true);
			$options = array();
			foreach($array_opciones as $opcion){
				$options[$opcion['value']] = $opcion['text'];
			}
		}
		
		$obligatorio = $datos_campo->obligatorio;
		$habilitado = $datos_campo->habilitado;

		if($id_elemento){
			if($tipo_matriz == "rca"){
				$row_elemento = $this->Values_compromises_rca_model->get_details(array("id" => $id_elemento))->result();
			}else{
				$row_elemento = $this->Values_compromises_reportables_model->get_details(array("id" => $id_elemento))->result();
			}
			
			$decoded_default = json_decode($row_elemento[0]->datos_campos, true);
			$default_value = $decoded_default[$id_campo];
			
			if($id_tipo_campo == 5){
				$default_value1 = $default_value["start_date"]?$default_value["start_date"]:"";
				$default_value2 = $default_value["end_date"]?$default_value["end_date"]:"";
			}
			if($id_tipo_campo == 11){
				$default_value = $datos_campo->default_value;
			}
			if($id_tipo_campo == 7){
				$default_value_multiple = (array)$default_value;
			}
			
			if($id_tipo_campo == 16){
					
				$datos_mantenedora = json_decode($datos_campo->default_value, true);
				$id_mantenedora = $datos_mantenedora['mantenedora'];
				$id_field_label = $datos_mantenedora['field_label'];
				$id_field_value = $datos_mantenedora['field_value'];
				
				$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
				
				$array_opciones = array();
				foreach($datos as $index => $row){
					$fila = json_decode($row->datos, true);
					$label = $fila[$id_field_label];
					$value = $fila[$id_field_value];
					$array_opciones[$value] = $label;
				}
			
			}
	
			
		}else{
			if($id_tipo_campo == 5){
				if($default_value){
					$default_value1 = json_decode($default_value)->start_date?json_decode($default_value)->start_date:"";
					$default_value2 = json_decode($default_value)->end_date?json_decode($default_value)->end_date:"";
				}else{
					$default_value1 = "";
					$default_value2 = "";
				}
			}else if($id_tipo_campo == 7){
				$default_value_multiple = array();
				//var_dump(json_decode($default_value, true));exit();
				foreach(json_decode($default_value, true) as $value){
					$default_value_multiple[] = $value;
				}
				
			}else{
				
			}
			
			if($id_tipo_campo == 16){
				
				$datos_mantenedora = json_decode($default_value, true);
				$id_mantenedora = $datos_mantenedora['mantenedora'];
				$id_field_label = $datos_mantenedora['field_label'];
				$id_field_value = $datos_mantenedora['field_value'];
				
				$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
				
				$array_opciones = array();
				foreach($datos as $index => $row){
					$fila = json_decode($row->datos, true);
					$label = $fila[$id_field_label];
					$value = $fila[$id_field_value];
					$array_opciones[$value] = $label;
				}

			}
			
		}
		
		//Input text
		if($id_tipo_campo == 1){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"autocomplete"=> "off",
				"maxlength" => "255"
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
			
		}
		
		//Texto Largo
		if($id_tipo_campo == 2){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"style" => "height:150px;",
				"autocomplete"=> "off",
				"maxlength" => "2000"
			);
			
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_textarea($datos_campo);
		}
		
		//Número
		if($id_tipo_campo == 3){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"autocomplete" => "off",
				"data-rule-number" => true,
				"data-msg-number" => lang("enter_a_integer")
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
		}
		
		//Fecha
		if($id_tipo_campo == 4){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control datepicker",
				"placeholder" => "YYYY-MM-DD",
				"autocomplete" => "off",
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
		}
		
		//Periodo
		if($id_tipo_campo == 5){
			
			$name = json_decode($name, true);
			$name1 = $name['start_name'];
			$name2 = $name['end_name'];
			
			$datos_campo1 = array(
				"id" => $name1,
				"name" => $name1,
				"value" => $default_value1,
				"class" => "form-control datepicker",
				"placeholder" => "YYYY-MM-DD",
				"autocomplete" => "off",
			);
			
			$datos_campo2 = array(
				"id" => $name2,
				"name" => $name2,
				"value" => $default_value2,
				"class" => "form-control datepicker",
				"placeholder" => "YYYY-MM-DD",
				"data-rule-greaterThanOrEqual" => "#".$name1,
				"data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
				"autocomplete" => "off",
			);
			if($obligatorio){
				$datos_campo1['data-rule-required'] = true;
				$datos_campo1['data-msg-required'] = lang("field_required");
				$datos_campo2['data-rule-required'] = true;
				$datos_campo2['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo1['disabled'] = true;
				$datos_campo2['disabled'] = true;
			}
			
			
			$html = '<div class="col-md-6">';
			$html .= form_input($datos_campo1);
			$html .= '</div>';
			$html .= '<div class="col-md-6">';
			$html .= form_input($datos_campo2);
			$html .= '</div>';
		}
		
		//Selección
		if($id_tipo_campo == 6){
			
			$extra = "";
			if($obligatorio){
				$extra .= " data-rule-required='true', data-msg-required='".lang('field_required')."'";
			}
			if($habilitado){
				$extra .= " disabled";
			}
			
			$html = form_dropdown($name, $options, $default_value, "id='$name' class='select2 validate-hidden' $extra");
		}
		
		//Selección Múltiple
		if($id_tipo_campo == 7){
			
			$extra = "";
			if($obligatorio){
				$extra .= " data-rule-required='true', data-msg-required='".lang('field_required')."'";
			}
			if($habilitado){
				$extra .= " disabled";
			}
			
			$html = form_multiselect($name."[]", $options, $default_value_multiple, "id='$name' class='select2 validate-hidden' $extra multiple");

		}
		
		//Rut
		if($id_tipo_campo == 8){
			
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"autocomplete" => "off",
				"data-rule-minlength" => 6,
				"data-msg-minlength" => lang("enter_minimum_6_characters"),
				"data-rule-maxlength" => 13,
				"data-msg-maxlength" => lang("enter_maximum_13_characters"),
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
			
		}
		
		//Radio Buttons
		if($id_tipo_campo == 9){
			
			$html = '';
			$cont = 0;
			foreach($options as $value => $label){
				$cont++;
				
				$html .= '<div class="col-md-6">';
				$html .= $label;
				$html .= '</div>';
				
				$html .= '<div class="col-md-6">';
				$datos_campo = array(
					"id" => $name.'_'.$cont,
					"name" => $name,
					"value" => $value,
					"class" => "toggle_specific",
					//$disabled => "",
				);
				if($value == $default_value){
					$datos_campo["checked"] = true;
				}
				if($obligatorio){
					$datos_campo['data-rule-required'] = true;
					$datos_campo['data-msg-required'] = lang("field_required");
				}
				if($habilitado){
					$datos_campo['disabled'] = true;
				}
				$html .= form_radio($datos_campo);
				$html .= '</div>';
				
			}
			
			
		}
		
		//Archivo
		if($id_tipo_campo == 10){
			
			if($default_value){
				
				if($preview){
					$html = '<div class="col-md-8">';
					$html .= $default_value;
					$html .= '</div>';
					
					$html .= '<div class="col-md-4">';
					$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("environmental_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
					$html .= '</td>';
					$html .= '</tr>';
					$html .= '</thead>';
					$html .= '</table>';
					$html .= '</div>';
					
				} else {
					
					$html = '<div class="col-md-8">';
					$html .= $default_value;
					$html .= '</div>';
					
					$html .= '<div class="col-md-4">';
					$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("environmental_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => $obligatorio, "data-id" => $id_elemento, "data-campo" => $id_campo, "data-action-url" => get_uri("environmental_records/delete_file"), "data-action" => "delete-confirmation"));
					$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
					$html .= '</td>';
					$html .= '</tr>';
					$html .= '</thead>';
					$html .= '</table>';
					$html .= '</div>';
				}
				
				
			}else{
				
				$html = $this->load->view("includes/form_file_uploader", array(
					"upload_url" =>get_uri("fields/upload_file"),
					"validation_url" =>get_uri("fields/validate_file"),
					"html_name" => $name,
					"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
					"id_campo" => $id_campo,
					//"preimagen" => $default_value
				),
				true);
			}
			
		}
		
		//Texto Fijo
		if($id_tipo_campo == 11){
			$html = $default_value;
		}
		
		//Divisor: Se muestra en la vista
		if($id_tipo_campo == 12){
			$html = "<hr>";
		}
		
		//Correo
		if($id_tipo_campo == 13){
			
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"autocomplete"=> "off",
				"maxlength" => "255",
				"data-rule-email" => true,
				"data-msg-email" => lang("enter_valid_email"),
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
			
		}
		
		//Hora
		if($id_tipo_campo == 14){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control timepicker",
				//"placeholder" => "YYYY-MM-DD",
				"placeholder" => $etiqueta,
				"autocomplete" => "off",
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
		}
		
		///Unidad
		if($id_tipo_campo == 15){
			
			//$simbolo = $array_opciones[0]["symbol"];
			$id_simbolo = $array_opciones[0]["id_unidad"];
			$simbolo = $this->Unity_model->get_one($id_simbolo);
			
			$html = '';
			$html .= '<div class="col-md-10 p0">';
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"data-rule-number" => true,
				"data-msg-number" => lang("enter_a_integer"),
				"autocomplete" => "off",
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			$html .= form_input($datos_campo);
			$html .= '</div>';
			$html .= '<div class="col-md-2">';
			$html .= $simbolo->nombre;
			$html .= '</div>';
		
		}
		
		//Selección desde Mantenedora
		if($id_tipo_campo == 16){
			
			/* $datos_mantenedora = json_decode($default_value, true);
			$id_mantenedora = $datos_mantenedora['mantenedora'];
			$id_field_label = $datos_mantenedora['field_label'];
			$id_field_value = $datos_mantenedora['field_value'];
			
			$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
			
			$array_opciones = array();
			foreach($datos as $index => $row){
				$fila = json_decode($row->datos, true);
				$label = $fila[$id_field_label];
				$value = $fila[$id_field_value];
				$array_opciones[$value] = $label;
			} */
			
			//var_dump($array_opciones);
			
			$extra = "";
			if($obligatorio){
				$extra .= " data-rule-required='true', data-msg-required='".lang('field_required')."'";
			}
			if($habilitado){
				$extra .= " disabled";
			}
			
			$html = form_dropdown($name, array("" => "-") + $array_opciones, $default_value, "id='$name' class='select2 validate-hidden' $extra");
			
		}
		
		return $html;

	}
	
	function get_field_value($id_campo, $id_elemento, $tipo_matriz) {
		
		if($tipo_matriz == "rca"){
			$id_compromiso = $this->Values_compromises_rca_model->get_one($id_elemento)->id_compromiso;
			$id_proyecto = $this->Compromises_rca_model->get_one($id_compromiso)->id_proyecto;
		}else{
			$id_compromiso = $this->Values_compromises_reportables_model->get_one($id_elemento)->id_compromiso;
			$id_proyecto = $this->Compromises_reportables_model->get_one($id_compromiso)->id_proyecto;
		}
		
        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$datos_campo = $this->Fields_model->get_one($id_campo);
		$id_tipo_campo = $datos_campo->id_tipo_campo;
		$etiqueta = $datos_campo->nombre;
		$name = $datos_campo->html_name;
		$default_value = $datos_campo->default_value;
		
		$opciones = $datos_campo->opciones;
		$array_opciones = json_decode($opciones, true);
		$options = array();
		foreach($array_opciones as $opcion){
			$options[$opcion['value']] = $opcion['text'];
		}
		
		if($tipo_matriz == "rca"){
			$row_elemento = $this->Values_compromises_rca_model->get_details(array("id" => $id_elemento))->result();
		}else{
			$row_elemento = $this->Values_compromises_reportables_model->get_details(array("id" => $id_elemento))->result();
		}
		$decoded_default = json_decode($row_elemento[0]->datos_campos, true);
		
		$default_value = $decoded_default[$id_campo];
		if($id_tipo_campo == 5){
			$default_value1 = $default_value["start_date"]?$default_value["start_date"]:"";
			$default_value2 = $default_value["end_date"]?$default_value["end_date"]:"";
			$default_value = $default_value1.' - '.$default_value2;
		}
		if($id_tipo_campo == 11){
			$default_value = $datos_campo->default_value;
		}
		if($id_tipo_campo == 7){
			$default_value_multiple = (array)$default_value;
		}
		
		
		//Input text
		if($id_tipo_campo == 1){
			$html = $default_value;
		}
		
		//Texto Largo
		if($id_tipo_campo == 2){
			$html = $default_value;
		}
		
		//Número
		if($id_tipo_campo == 3){
			$html = $default_value;
		}
		
		//Fecha
		if($id_tipo_campo == 4){
			$html = get_date_format($default_value,$id_proyecto);
		}
		
		//Periodo
		if($id_tipo_campo == 5){
			 $html = $default_value;
		}
		
		//Selección
		if($id_tipo_campo == 6){
			$html = $default_value;// es el value, no el text
		}
		
		//Selección Múltiple
		if($id_tipo_campo == 7){
			$html = implode(", ", $default_value_multiple);//siempre es un arreglo, aunque tenga 1
		}
		
		//Rut
		if($id_tipo_campo == 8){
			$html = $default_value;
		}
		
		//Radio Buttons
		if($id_tipo_campo == 9){
			//$html = $value;// es el value, no la etiqueta
			$html = $default_value;
		}
		
		//Archivo
		if($id_tipo_campo == 10){
			
			if($default_value ){
				
				$html = '<div class="col-md-8">';
				$html .= $default_value;
				$html .= '</div>';
				
				$html .= '<div class="col-md-4">';
				$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
				$html .= '<tbody><tr><td class="option text-center">';
				$html .= anchor(get_uri("environmental_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
				$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
				$html .= '</td>';
				$html .= '</tr>';
				$html .= '</thead>';
				$html .= '</table>';
				$html .= '</div>';
				
			} else {
				
				$html = '<div class="col-md-8">';
				$html .= '-';
				$html .= '</div>';
			}
			
			
			
		}
		
		//Texto Fijo
		if($id_tipo_campo == 11){
			$html = $default_value;
		}
		
		//Divisor: Se muestra en la vista
		if($id_tipo_campo == 12){
			$html = "<hr>";
		}
		
		//Correo
		if($id_tipo_campo == 13){
			$html = $default_value;
		}
		
		//Hora
		if($id_tipo_campo == 14){
			$html = convert_to_general_settings_time_format($id_proyecto, $default_value);
		}
		
		///Unidad
		if($id_tipo_campo == 15){
			$simbolo = $array_opciones[0]["symbol"];
			$html = $default_value?$default_value:"-".' '.$simbolo;
		}
		
		//Selección desde Mantenedora
		if($id_tipo_campo == 16){
			
			$html = $default_value;
			
		}
		
		if($html == ""){$html = "-";}
		
		return $html;

    }

	function get_excel_template_of_compromise($id_compromiso_proyecto, $id_cliente, $id_proyecto, $tipo_matriz){
		
		if($tipo_matriz == "rca"){
			$Compromises_model = $this->Compromises_rca_model;
			$Values_compromises_model = $this->Values_compromises_rca_model;
		}else{
			$Compromises_model = $this->Compromises_reportables_model;
			$Values_compromises_model = $this->Values_compromises_reportables_model;
		}
		
		$columnas_campos = $Compromises_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
		$client_info = $this->Clients_model->get_one($id_cliente);
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$nombre_proyecto = $this->Projects_model->get_one($id_proyecto)->title;
		$id_proyecto = $project_info->id;

		if($tipo_matriz == "rca"){
			$filename = $client_info->sigla.'_'.$project_info->sigla.'_'.lang('compromise_rca_template_excel').'_'.date("Y-m-d");
		}else{
			$filename = $client_info->sigla.'_'.$project_info->sigla.'_'.lang('compromise_reportable_template_excel').'_'.date("Y-m-d");
		}
		
		
		$this->load->library('excel');		
		
		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle(lang("template_compromise"))
							 ->setSubject(lang("template_compromise"))
							 ->setDescription(lang("template_compromise"))
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");
		$doc->setActiveSheetIndex(0);
		
		// CREAR HOJA PARA OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN
		$doc->createSheet();
		// usar nueva hoja
		$doc->setActiveSheetIndex(1);
		//$doc->getActiveSheet()->setCellValue('A1', 'More data');
		$doc->getActiveSheet()->setTitle('options');
		//volver a usar la primera hoja
		$doc->setActiveSheetIndex(0);
		
		
		
		
		if($tipo_matriz == "rca"){
			$columna = 4;
			
			$doc->getActiveSheet()->setCellValue('A1', lang('compromise_number'));
			$doc->getActiveSheet()->setCellValue('B1', lang('name'));
			$doc->getActiveSheet()->setCellValue('C1', lang('phases'));
			$doc->getActiveSheet()->setCellValue('D1', lang('reportability'));
		}else{

			$doc->getActiveSheet()->setCellValue('A1', lang('n_activity'));
			$doc->getActiveSheet()->setCellValue('B1', lang('environmental_management_instrument'));
			$doc->getActiveSheet()->setCellValue('C1', lang('phase_reportable'));
			$doc->getActiveSheet()->setCellValue('D1', lang('compliance_type')); 
			$doc->getActiveSheet()->setCellValue('E1', lang('environmental_topic'));
			$doc->getActiveSheet()->setCellValue('F1', lang('impact_on_the_environment_due_to_non_compliance'));
			$doc->getActiveSheet()->setCellValue('G1', lang('action_type'));
			$doc->getActiveSheet()->setCellValue('H1', lang('responsible_area'));
			$doc->getActiveSheet()->setCellValue('I1', lang('environmental_commitment'));

			$doc->getActiveSheet()->getComment('H1')->setAuthor('Mimasoft');
			$comentario = $doc->getActiveSheet()->getComment('H1')->getText()->createTextRun(lang("info"));
			$comentario->getFont()->setBold(true);
			$doc->getActiveSheet()->getComment('H1')->getText()->createTextRun("\r\n");
			$doc->getActiveSheet()->getComment('H1')->getText()->createTextRun(' - ' . lang("multi_select_comment"))->getFont()->setBold(true);
			$array_responsible_area = array(
				lang("personal_administration"),
				lang("warehouse"),
				lang("quality"),
				lang("field"),
				lang("training"),
				lang("communications"),
				lang("crops"),
				lang("management"),
				lang("icp"),
				lang("maintenance"),
				lang("machinery"),
				lang("operations"),
				lang("packing"),
				lang("landscaping"),
				lang("pre_mix"),
				lang("projects"),
				lang("recruitment"),
				lang("social_responsability"),
				lang("irrigation"),
				lang("occupational_health"),
				lang("industrial_security"),
				lang("general_services"),
				lang("sig"),
				lang("transportation"),
				lang("environment")
			);
			$doc->getActiveSheet()->getComment('H1')->getText()->createTextRun("\r\n");
			foreach($array_responsible_area as $responsible_area){
				$doc->getActiveSheet()->getComment('H1')->getText()->createTextRun("\r\n");
				$doc->getActiveSheet()->getComment('H1')->getText()->createTextRun(' - ' . $responsible_area)->getFont()->setBold(true);
			}
			$doc->getActiveSheet()->getComment('H1')->setWidth("300px");
			$doc->getActiveSheet()->getComment('H1')->setHeight("580");

			$columna = 9;

		}
		
		foreach($columnas_campos as $cc){
			
			if($cc["id_tipo_campo"] == 10 || $cc["id_tipo_campo"] == 11 || $cc["id_tipo_campo"] == 12){
				continue;
			}
			
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', $cc["nombre_campo"]);
			
			if($cc["default_value"] && $cc["id_tipo_campo"] != 16){ //SI EL CAMPO TIENE VALOR POR DEFECTO Y NO ES SELECCIÓN DESDE MANTENEDORA
			
				if($cc["id_tipo_campo"] == 5){	
					$periodo = json_decode($cc["default_value"]);
					$valor_por_defecto = $periodo->start_date."/".$periodo->end_date;
				} else {
					$valor_por_defecto = $cc["default_value"];
				}
				
				$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
				$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
				$comentario->getFont()->setBold(true);
				$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
				$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("default_value_field") . ": ")->getFont()->setBold(true);
				$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun($valor_por_defecto);
				
				if($cc["habilitado"]){ //SI EL CAMPO ESTÁ DESHABILITADO
						
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);
					
					if($cc["obligatorio"]){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					} else {
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					}
					
				} else { //SI EL CAMPO ESTÁ HABILITADO
					
					if($cc["obligatorio"]){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					} else {
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					}
					
				}

			} else if(!$cc["default_value"] && $cc["id_tipo_campo"] != 16){
				
				if($cc["habilitado"]){ //SI EL CAMPO ESTÁ DESHABILITADO
						
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
					$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
					$comentario->getFont()->setBold(true);
				
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);

					if($cc["obligatorio"]){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					} else {
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					}
					
				} else { //SI EL CAMPO ESTÁ HABILITADO
					
					if($cc["obligatorio"]){
						
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
						$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
						$comentario->getFont()->setBold(true);
					
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					
					}
					
				}
	
			}
			
			$columna++;
		}
		
		if($tipo_matriz == "rca"){
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('compliance_action_control'));
			$columna++;
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('execution_frequency'));
			//$columna++;
		}else{
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('planning_description'));
			$columna++;
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('planning_date'));
		}
		
		if($tipo_matriz == "rca"){
			$columna = 4;
		}
		
		// CUERPO DEL EXCEL

		//$doc->getActiveSheet()->setCellValue('A2', lang('excel_number_example'));
		$doc->getActiveSheet()->setCellValueExplicit('A2', lang('excel_number_example'), PHPExcel_Cell_DataType::TYPE_STRING);
		
		if($tipo_matriz == "rca"){
			
			$doc->getActiveSheet()->setCellValue('B2', lang('excel_name_example'));
			
			// LISTA DEMO DE FASES A PARTIR DE LAS FASES REALES DEL SISTEMA
			$fases_disponibles = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
			$array_fases = array();
			foreach($fases_disponibles as $fase){
				$array_fases[] = lang($fase["nombre_lang"]);
			}
			
			$doc->getActiveSheet()->setCellValue('C2', implode(', ', $array_fases));
			
			//
			$array_opciones = array("Si", "No");
			$objValidation = $doc->getActiveSheet()->getCell('D2')->getDataValidation();     
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
			$objValidation->setAllowBlank(false);
			$objValidation->setShowInputMessage(true);
			$objValidation->setShowErrorMessage(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setErrorTitle(lang('excel_error_title'));
			$objValidation->setError(lang('excel_error_text'));
			//$objValidation->setPromptTitle(lang('excel_prompt_title').' "'.$campo->nombre.'"');
			//$objValidation->setPrompt(lang('excel_prompt_text').' "'.$info_mantenedora->nombre.'"');
			$objValidation->setFormula1('"'.implode(",", $array_opciones).'"');
	
			if($array_opciones[0]){
				$doc->getActiveSheet()->setCellValue('D2', $array_opciones[0]);
			}
			//
			
			
		}else{
					
			// OPCIONES PARA CELDA INSTRUMENTO DE GESTIÓN AMBIENTAL
			$array_environmental_management_instrument = array(
				lang("mpama"),
				lang("pama"),
				lang("pama_and_mpama"),
				lang("dia"),
				lang("mdia"),
				lang("dia_and_mdia"),
				lang("n/a")
			);
			
			// GUARDO OPCIONES DEL CAMPO INSTRUMENTO DE GESTIÓN AMBIENTAL
			$doc->setActiveSheetIndex(1);
			$fila_opcion = 1;
			foreach($array_environmental_management_instrument as $opcion){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber(0).$fila_opcion, $opcion); // COLUMNA A DE HOJA OPTIONS
				$fila_opcion++;
			}
			$doc->setActiveSheetIndex(0);

			$objValidation = $doc->getActiveSheet()->getCell('B2')->getDataValidation();     
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
			$objValidation->setAllowBlank(false);
			$objValidation->setShowInputMessage(true);
			$objValidation->setShowErrorMessage(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setErrorTitle(lang('excel_error_title'));
			$objValidation->setError(lang('excel_error_text'));
			
			$cantidad_environmental = count($array_environmental_management_instrument);
			$objValidation->setFormula1('options!$A$1:$A$'.$cantidad_environmental);
			$doc->getActiveSheet()->setCellValue('B2', $array_environmental_management_instrument[0]);
			
			// OPCIONES PARA CELDA ETAPA
			$array_phase = array(
				lang("construction"),
				lang("operation"),
				lang("closing")
			);

			// GUARDO OPCIONES DEL CAMPO TIPO DE CUMPLIMIENTO
			$doc->setActiveSheetIndex(1);
			$fila_opcion = 1;
			foreach($array_phase as $opcion){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber(1).$fila_opcion, $opcion); // COLUMNA B DE HOJA OPTIONS
				$fila_opcion++;
			}
			$doc->setActiveSheetIndex(0);

			$objValidation = $doc->getActiveSheet()->getCell('C2')->getDataValidation();     
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
			$objValidation->setAllowBlank(false);
			$objValidation->setShowInputMessage(true);
			$objValidation->setShowErrorMessage(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setErrorTitle(lang('excel_error_title'));
			$objValidation->setError(lang('excel_error_text'));
			
			$cantidad_phase = count($array_phase);
			$objValidation->setFormula1('options!$B$1:$B$'.$cantidad_phase);
			$doc->getActiveSheet()->setCellValue('C2', $array_phase[0]);

			// OPCIONES PARA CELDA TIPO DE CUMPLIMIENTO
			$array_compliance_types = array(
				lang("environmental_adaptation_actions"),
				lang("mitigation_measures_in_existing_components"),
				lang("prevention_control_and_mitigation_measures_in_projected_components"),
				lang("measures_for_the_prevention_control_and_mitigation_of_environmental_impacts"),
				lang("closure_or_abandonment_plan"),
				lang("contingency_plan"),
				lang("solid_waste_management_plan"),
				lang("main_environmental_obligations"),
				lang("environmental_education_program"),
				lang("environmental_monitoring_program"),
				lang("citizen_participation_program"),
				lang("environmental_signage_program"),
				lang("preventive_or_corrective_program"),
				lang("closing_activities_projected_components")
			);

			// GUARDO OPCIONES DEL CAMPO TIPO DE CUMPLIMIENTO
			$doc->setActiveSheetIndex(1);
			$fila_opcion = 1;
			foreach($array_compliance_types as $opcion){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber(2).$fila_opcion, $opcion); // COLUMNA C DE HOJA OPTIONS
				$fila_opcion++;
			}
			$doc->setActiveSheetIndex(0);

			$objValidation = $doc->getActiveSheet()->getCell('D2')->getDataValidation();     
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
			$objValidation->setAllowBlank(false);
			$objValidation->setShowInputMessage(true);
			$objValidation->setShowErrorMessage(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setErrorTitle(lang('excel_error_title'));
			$objValidation->setError(lang('excel_error_text'));
			
			$cantidad_compliance_types = count($array_compliance_types);
			$objValidation->setFormula1('options!$C$1:$C$'.$cantidad_compliance_types);
			$doc->getActiveSheet()->setCellValue('D2', $array_compliance_types[0]);


			// OPCIONES PARA CELDA TEMA AMBIENTAL
			$array_environmental_topic = array(
				lang("water"),
				lang("air"),
				lang("air/noise"),
				lang("control_of_agricultural_inputs"),
				lang("crops"),
				lang("environmental_education"),
				lang("industrial_and_domestic_effluents"),
				lang("social_environment"),
				lang("flora_and_fauna"),
				lang("non_hazardous_waste"),
				lang("hazardous_waste"),
				lang("hazardous_and_non_hazardous_waste"),
				lang("security_and_health_at_work"),
				lang("ground"),
				lang("other"),
			);

			// GUARDO OPCIONES DEL CAMPO TEMA AMBIENTAL
			$doc->setActiveSheetIndex(1);
			$fila_opcion = 1;
			foreach($array_environmental_topic as $opcion){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber(3).$fila_opcion, $opcion); // COLUMNA D DE HOJA OPTIONS
				$fila_opcion++;
			}
			$doc->setActiveSheetIndex(0);

			$objValidation = $doc->getActiveSheet()->getCell('E2')->getDataValidation();     
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
			$objValidation->setAllowBlank(false);
			$objValidation->setShowInputMessage(true);
			$objValidation->setShowErrorMessage(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setErrorTitle(lang('excel_error_title'));
			$objValidation->setError(lang('excel_error_text'));
			
			$cantidad_environmental_topic = count($array_environmental_topic);
			$objValidation->setFormula1('options!$D$1:$D$'.$cantidad_environmental_topic);
			$doc->getActiveSheet()->setCellValue('E2', $array_environmental_topic[0]);

			
			// OPCIONES PARA CELDA AFECTACIÓN AL MEDIO POR INCLUMPLIMIENTO
			$array_impact_on_the_environment_due_to_non_compliance = array(
				lang("water"),
				lang("air_and_noise"),
				lang("community/social_environment"),
				lang("flora_and_fauna"),
				lang("solid_waste"),
				lang("hazardous_waste"),
				lang("non_hazardous_waste"),
				lang("health"),
				lang("ground"),
				lang("physical_environment"),	
				lang("physical_and_socioeconomic_environment")
			);

			// GUARDO OPCIONES DEL CAMPO AFECTACIÓN AL MEDIO POR INCLUMPLIMIENTO
			$doc->setActiveSheetIndex(1);
			$fila_opcion = 1;
			foreach($array_impact_on_the_environment_due_to_non_compliance as $opcion){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber(4).$fila_opcion, $opcion); // COLUMNA E DE HOJA OPTIONS
				$fila_opcion++;
			}
			$doc->setActiveSheetIndex(0);

			$objValidation = $doc->getActiveSheet()->getCell('F2')->getDataValidation();     
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
			$objValidation->setAllowBlank(false);
			$objValidation->setShowInputMessage(true);
			$objValidation->setShowErrorMessage(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setErrorTitle(lang('excel_error_title'));
			$objValidation->setError(lang('excel_error_text'));
			
			$cantidad_impact = count($array_impact_on_the_environment_due_to_non_compliance);
			$objValidation->setFormula1('options!$E$1:$E$'.$cantidad_impact);
			$doc->getActiveSheet()->setCellValue('F2', $array_impact_on_the_environment_due_to_non_compliance[0]);

			// CAMPO TIPO DE ACCIÓN
			$doc->getActiveSheet()->setCellValue('G2', lang('action_type_example'));

			// OPCIONES PARA CELDA ÁREA RESPONSABLE
			/* El arreglo $array_responsible_area fue declarado más arriba  */

			// GUARDO OPCIONES DEL CAMPO ÁREA RESPONSABLE
			/* $doc->setActiveSheetIndex(1);
			$fila_opcion = 1;
			foreach($array_responsible_area as $opcion){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber(4).$fila_opcion, $opcion); // COLUMNA E DE HOJA OPTIONS
				$fila_opcion++;
			} 
			$doc->setActiveSheetIndex(0);

			$objValidation = $doc->getActiveSheet()->getCell('G2')->getDataValidation();     
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
			$objValidation->setAllowBlank(false);
			$objValidation->setShowInputMessage(true);
			$objValidation->setShowErrorMessage(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setErrorTitle(lang('excel_error_title'));
			$objValidation->setError(lang('excel_error_text'));
			
			$cantidad_area_responsible = count($array_responsible_area);
			$objValidation->setFormula1('options!$E$1:$E$'.$cantidad_area_responsible);
			$doc->getActiveSheet()->setCellValue('G2', $array_responsible_area[0]);
			*/

			// CAMPO ÁREA RESPONSABLE
			/* El arreglo $array_responsible_area fue declarado más arriba  */
			$doc->getActiveSheet()->setCellValue('H2', $array_responsible_area[0] . ';' . $array_responsible_area[1] . ';' . $array_responsible_area[2]);


			$doc->getActiveSheet()->setCellValue('I2', lang('commitment_description_example'));

			// $doc->getActiveSheet()->setCellValue('F2', lang('reportable_excel_short_description_example'));
			/*$doc->getActiveSheet()->setCellValue('B2', lang('reportable_excel_name_example'));
			$doc->getActiveSheet()->setCellValue('C2', lang('reportable_excel_short_description_example'));
			$doc->getActiveSheet()->setCellValue('D2', lang('reportable_excel_short_description_example'));
			$doc->getActiveSheet()->setCellValue('E2', lang('reportable_excel_short_description_example'));*/

			$columna = 9;
			$columna_opciones = 5;
			
		}

		$options = array("id_compromiso" => $columnas_campos["id_compromiso"]);
		$list_data = $Values_compromises_model->get_details($options)->result();
		
		if($tipo_matriz == "rca"){
			$columna_opciones = 5; // F
		}

		
		foreach($columnas_campos as $campo){
			
			if($campo["id_tipo_campo"] == 10 || $campo["id_tipo_campo"] == 11 || $campo["id_tipo_campo"] == 12){
				continue;
			}
			
			if($campo["id_tipo_campo"] == 1){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_text'));
			}
			if($campo["id_tipo_campo"] == 2){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_textarea'));
			}
			if($campo["id_tipo_campo"] == 3){
				//$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$numero_ejemplo = ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_number');
				$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $numero_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
			}
			if($campo["id_tipo_campo"] == 4){
				$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_date'));
			}
			if($campo["id_tipo_campo"] == 5){
				//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_period'));
				if($cc["default_value"]){
					$periodo = json_decode($cc["default_value"]);
					$valor_por_defecto = $periodo->start_date."/".$periodo->end_date;
				}
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($valor_por_defecto) ? $valor_por_defecto : lang('excel_test_period'));
			}
			
			if($campo["id_tipo_campo"] == 6){
				$datos_campo = json_decode($campo["opciones"]);
				$array_opciones = array();
				foreach($datos_campo as $row){
					$label = $row->text;
					$value = $row->value;
					$array_opciones[] = $label;
				}
				array_shift($array_opciones);
				
				//GUARDO OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN EN HOJA OPCIONES
				$doc->setActiveSheetIndex(1);
				
				$fila_opcion = 1;
				foreach($array_opciones as $opcion){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $opcion);
					$fila_opcion++;
				}

				$doc->setActiveSheetIndex(0);

				$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
				$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
				$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle(lang('excel_error_title'));
				$objValidation->setError(lang('excel_error_text'));
				//$objValidation->setPromptTitle(lang('excel_prompt_title').' "'.$campo->nombre.'"');
				//$objValidation->setPrompt(lang('excel_prompt_text').' "'.$info_mantenedora->nombre.'"');
				//$objValidation->setFormula1('"'.implode(",", $array_opciones).'"');
				
				$cantidad_opciones_seleccion = count($array_opciones);
				if($cantidad_opciones_seleccion > 0){
					$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_opciones_seleccion);
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_opciones[0]);
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : $array_opciones[0]);
				}

				//if($array_opciones[0]){
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_opciones[0]);
				//}
				
				$columna_opciones++;
			}
			
			if($campo["id_tipo_campo"] == 7){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_select_multiple'));
			}
			if($campo["id_tipo_campo"] == 8){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_rut'));
			}
			if($campo["id_tipo_campo"] == 9){
				//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_test_radio'));
				
				$datos_campo = json_decode($campo["opciones"]);
					
				$array_opciones = array();
				foreach($datos_campo as $row){
					$label = $row->text;
					$value = $row->value;
					$array_opciones[] = $label;
				}
				
				$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
				$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
				$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle(lang('excel_error_title'));
				$objValidation->setError(lang('excel_error_text'));
				$objValidation->setFormula1('"'.implode(",", $array_opciones).'"');
				
				if($array_opciones[0]){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : $array_opciones[0]);
				}
				
			}
			if($campo["id_tipo_campo"] == 13){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_mail'));
			}
			if($campo["id_tipo_campo"] == 14){
				$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_time'));
			}
			if($campo["id_tipo_campo"] == 15){
				$unidad_ejemplo = ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_unity');
				$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $unidad_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
			}
			
			$columna++;
		}
		
		if($tipo_matriz == "rca"){
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_compliance_action_control_example'));
			$columna++;
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_execution_frequency_example'));
			$columna++;
		}else{
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('reportable_excel_descriptions_example'));
			$columna++;
			$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('reportable_excel_plans_example'));
			$columna++;
		}

		foreach(range('A', $this->getNameFromNumber($columna)) as $columnID) {
			$doc->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		
		$nombre_hoja = strlen(lang("compromises").' '.$nombre_proyecto)>31?substr(lang("compromises").' '.$nombre_proyecto, 0, 28).'...':lang("compromises").' '.$nombre_proyecto;
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		// OCULTO HOJA OPTIONS
		$doc->getSheetByName('options')->setSheetState(PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN);
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		
		$objWriter->save('files/carga_masiva_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$filename.'.xlsx');
		
		if(!file_exists(__DIR__.'/../../files/carga_masiva_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$filename.'.xlsx')) {
			echo json_encode(array("success" => false, 'message' => lang('excel_error_occurred')));
			exit();
		}
		
		$html = '';		
		$html .= '<div class="col-md-12">';
		$html .= '<div class="fa fa-file-excel-o font-22 mr10"></div>';
		$html .= '<a href="'.get_uri("upload_compromises/download_compromise_template/".$id_compromiso_proyecto."/".$id_cliente."/".$id_proyecto."/".$tipo_matriz).'">'.$filename.'.xlsx</a>';
		$html .= '</div>';
		
		return $html;
		
	}

	function set_cell_as_list($doc, $array_list, $columna, $columna_opciones){
		// GUARDO OPCIONES DEL CAMPO EN LA HOJA OPCIONES
		$doc->setActiveSheetIndex(1);

		$col_opciones = $this->getNameFromNumber($columna_opciones);
		$fila_opcion = 1;

		foreach($array_list as $opcion){
			$doc->getActiveSheet()->setCellValue($col_opciones.$fila_opcion, $opcion); // COLUMNA DE HOJA OPTIONS
			$fila_opcion++;
		}
		$doc->setActiveSheetIndex(0);

		$celda = $this->getNameFromNumber($columna).'2';
		$objValidation = $doc->getActiveSheet()->getCell($celda)->getDataValidation();     
		$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
		$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
		$objValidation->setAllowBlank(false);
		$objValidation->setShowInputMessage(true);
		$objValidation->setShowErrorMessage(true);
		$objValidation->setShowDropDown(true);
		$objValidation->setErrorTitle(lang('excel_error_title'));
		$objValidation->setError(lang('excel_error_text'));
		
		$cantidad = count($array_list);
		$objValidation->setFormula1('options!$'.$col_opciones.'$1:$'.$col_opciones.'$'.$cantidad);
		$doc->getActiveSheet()->setCellValue($celda, $array_list[0]);
		
	}

	function clean($string){
	   $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
	   return strtolower(preg_replace('/[^A-Za-z0-9\_]/', '', $string)); // Removes special chars.
	}
	
	function download_compromise_template($id_compromiso_proyecto, $id_cliente, $id_proyecto, $tipo_matriz) {
		
		$client_info = $this->Clients_model->get_one($id_cliente);
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$nombre_proyecto = $this->Projects_model->get_one($id_proyecto)->title;	
		$nombre_hoja = strlen(lang("compromises").' '.$nombre_proyecto)>31?substr(lang("compromises").' '.$nombre_proyecto, 0, 28).'...':lang("compromises").' '.$nombre_proyecto;
		
		if(!$id_compromiso_proyecto && !$id_cliente && !$id_proyecto){
			redirect("forbidden");
		}
		
		//$nombre_archivo = $this->clean($nombre_hoja);
		if($tipo_matriz == "rca"){
			$filename = $client_info->sigla.'_'.$project_info->sigla.'_'.lang('compromise_rca_template_excel').'_'.date("Y-m-d");
		}else{
			$filename = $client_info->sigla.'_'.$project_info->sigla.'_'.lang('compromise_reportable_template_excel').'_'.date("Y-m-d");
		}
		//$filename = $client_info->sigla.'_'.$project_info->sigla.'_'.lang('compromise_template_excel').'_'.date("Y-m-d");
		
        $file_data = serialize(array(array("file_name" => $filename.".xlsx")));
        download_app_files("files/carga_masiva_compromisos/client_".$id_cliente."/project_".$id_proyecto."/", $file_data, false);
		
    }
	
	function getNameFromNumber($num){
		$numeric = $num % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval($num / 26);
		if ($num2 > 0) {
			return $this->getNameFromNumber($num2 - 1) . $letter;
		} else {
			return (string)$letter;
		}
	}
		
	function validateDate($date){
		$d = DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') == $date;
	}
	
	/* upload a post file */

    function upload_file() {
        upload_file_to_temp();
    }
	
	/* check valid file for client */

    function validate_file() {
		
		$file_name = $this->input->post("file_name");
		
		if (!$file_name){
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}

		$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
		if ($file_ext == 'xlsx') {
			echo json_encode(array("success" => true));
		}else{
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}
		
    }
	
}

