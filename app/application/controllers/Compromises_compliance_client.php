<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Compromises_compliance_client extends MY_Controller {

	private $id_modulo_cliente;
	private $id_submodulo_cliente;

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");

		$this->id_modulo_cliente = 6;
		$this->id_submodulo_cliente = 3;

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);

		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
    }

    function index() {

		//$access_info = $this->get_access_info("invoice");
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$id_compromiso_rca = $this->Compromises_rca_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
		$id_compromiso_reportables = $this->Compromises_reportables_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;

		$view_data = array();

		$cliente = $this->Clients_model->get_one($id_cliente);
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$view_data["project_info"] = $proyecto;
		$view_data["nombre_proyecto"] = $proyecto->title;

		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");

		if($id_compromiso_rca){

			$view_data["id_cliente"] = $id_cliente;
			$view_data["id_compromiso_rca"] = $id_compromiso_rca;
			$view_data["id_compromiso_reportables"] = $id_compromiso_reportables;
			$view_data["id_proyecto"] = $id_proyecto;
			$view_data["Compromises_compliance_client_controller"] = $this;

			/* SECCIÓN RESUMEN POR EVALUADO */

			// COMPROMISOS AMBIENTALES - RCA

			// EVALUADOS
			$evaluados = $this->Evaluated_rca_compromises_model->get_all_where(array(
				"id_compromiso" => $id_compromiso_rca,
				"deleted" => 0
			))->result();

			// ESTADOS RCA
			$estados_cliente = $this->Compromises_compliance_status_model->get_details(array(
				"id_cliente" => $id_cliente,
				"tipo_evaluacion" => "rca",
			))->result();

			// ULTIMAS EVALUACIONES
			$ultimas_evaluaciones = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluations_of_project($id_proyecto)->result();

			// PROCESAR TABLA
			$array_estados_evaluados = array();
			$array_evaluados_estados = array();
			$array_total_por_evaluado = array();
			$array_total_por_estado = array();
			$array_compromisos_evaluaciones_no_cumple = array();
			$total = 0;

			foreach($estados_cliente as $estado) {

				$id_estado = $estado->id;

				if($estado->categoria == "No Aplica"){
					continue;
				}
				$array_estados_evaluados[$estado->id] = array(
					"nombre_estado" => $estado->nombre_estado,
					"categoria" => $estado->categoria,
					"color" => $estado->color,
					"evaluados" => array(),
					"cantidad_categoria" => 0,
				);

				$cant_estado = 0;
				foreach($evaluados as $evaluado) {

					$id_evaluado = $evaluado->id;
					$cant = 0;

					$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado] = array("cant" => 0, "evaluaciones" => array());

					foreach($ultimas_evaluaciones as $ultima_evaluacion) {
						if(
							$ultima_evaluacion->id_estados_cumplimiento_compromiso == $id_estado &&
							$ultima_evaluacion->id_evaluado == $id_evaluado
						){
							$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["evaluaciones"][] = $ultima_evaluacion;
							$array_evaluados_estados[$id_evaluado][$id_estado][] = 1;
							$cant++;
							$cant_estado++;

							if($estado->categoria == "No Cumple"){
								$criticidad_info = $this->Critical_levels_model->get_one($ultima_evaluacion->id_criticidad);
								$ultima_evaluacion->criticidad = $criticidad_info->nombre;
								$array_compromisos_evaluaciones_no_cumple[] = $ultima_evaluacion;
							}
						}
					}

					$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["cant"] = $cant;
					$array_total_por_evaluado[$id_evaluado][] = $cant;
					$array_total_por_estado[$id_estado][] = $cant;
				}

				$array_estados_evaluados[$id_estado]["cantidad_categoria"] = $cant_estado;
				$total += $cant_estado;
			}

			$view_data["evaluados_rca"] = $evaluados;
			$view_data["total_compromisos_aplicables_rca"] = $total;
			$view_data["total_cantidades_estados_evaluados_rca"] = $array_estados_evaluados;
			$view_data["total_cantidades_evaluados_estados_rca"] = $array_evaluados_estados;
			$view_data["array_total_por_evaluado_rca"] = $array_total_por_evaluado;

			/* FIN SECCIÓN RESUMEN POR EVALUADO */


			/* SECCIÓN ESTADOS DE CUMPLIMIENTO */

			$json_string_columnas = ',{"title":"' . lang("name") .'", "class": "text-left dt-head-center"}';
			$traer_columnas = $this->Compromises_rca_model->get_fields_of_compliance_status($id_compromiso_rca)->result_array();

			foreach($traer_columnas as $columnas){
				$json_string_columnas .= ',{"title":"' .$columnas["nombre_evaluado"] . '", "class": "text-center dt-head-center no_breakline", render: function (data, type, row) {return "<center>"+data+"</center>";}}';
			}

			$json_string_columnas .= ',{"title":"' . lang("evidence") .'", "class":"text-center option"}';
			$json_string_columnas .= ',{"title":"' . lang("observations") .'", "class":"text-center option"}';
			$view_data["columnas"] = $json_string_columnas;

			// Filtro Reportabilidad
			$array_reportabilidad[] = array("id" => "", "text" => "- ".lang("reportability")." -");
			$array_reportabilidad[] = array("id" => "si", "text" => lang("yes"));
			$array_reportabilidad[] = array("id" => "no", "text" => lang("no"));
			$view_data['reportabilidad_dropdown'] = json_encode($array_reportabilidad);

			/* FIN SECCIÓN ESTADOS DE CUMPLIMIENTO */
		}

		if($id_compromiso_reportables){
			/* SECCIÓN COMPROMISOS REPORTABLES */

			$view_data["id_compromiso_reportables"] = $id_compromiso_reportables;
			$view_data["id_cliente"] = $id_cliente;
			$view_data["id_proyecto"] = $id_proyecto;

			// ESTADOS REPORTABLES
			$estados_cliente = $this->Compromises_compliance_status_model->get_details(array(
				"id_cliente" => $id_cliente,
				"tipo_evaluacion" => "reportable",
			))->result();

			// ULTIMAS EVALUACIONES
			$ultimas_evaluaciones = $this->Compromises_compliance_evaluation_reportables_model->get_last_evaluations_of_project($id_proyecto)->result();

			// PROCESAR TABLA
			$array_estados_evaluados = array();
			$total_evaluado = 0;
			$array_compromisos_reportables_evaluaciones_no_cumple = array();
			$max_dates = array();
			foreach($estados_cliente as $estado) {

				$id_estado = $estado->id;
				if($estado->categoria == "No Aplica"){
					continue;
				}

				$array_estados_evaluados[$id_estado] = array(
					"nombre_estado" => $estado->nombre_estado,
					"categoria" => $estado->categoria,
					"color" => $estado->color,
					"planificaciones_evaluaciones" => array(),
					"cant" => 0,
				);

				$cant = 0;
				foreach($ultimas_evaluaciones as $ultima_evaluacion) {
					if($ultima_evaluacion->id_estados_cumplimiento_compromiso == $id_estado){

						$array_estados_evaluados[$id_estado]["planificaciones_evaluaciones"] = $ultima_evaluacion;
						$cant++;

						if($estado->categoria == "No Cumple"){
							$criticidad_info = $this->Critical_levels_model->get_one($ultima_evaluacion->id_criticidad);
							$ultima_evaluacion->criticidad = $criticidad_info->nombre;
							$id_valor_compromiso = $ultima_evaluacion->id_valor_compromiso;

							if(is_null($max_dates[$id_valor_compromiso])){
								$max_dates[$id_valor_compromiso] = $ultima_evaluacion->planificacion;
								$array_compromisos_reportables_evaluaciones_no_cumple[$id_valor_compromiso] = $ultima_evaluacion;
							}elseif(strtotime($max_dates[$id_valor_compromiso]) < strtotime($ultima_evaluacion->planificacion)){
								$max_dates[$id_valor_compromiso] = $ultima_evaluacion->planificacion;
								$array_compromisos_reportables_evaluaciones_no_cumple[$id_valor_compromiso] = $ultima_evaluacion;
							}

						}
					}
				}

				$array_estados_evaluados[$id_estado]["cant"] = $cant;
				$total_evaluado += $cant;

			}
			
			$view_data["compromisos_reportables"] = $array_estados_evaluados;
			$view_data["total_reportables"] = $total_evaluado;

			// GRAFICO RESUMEN DE CUMPLIMIENTO
			$array_grafico_reportables = array();
			foreach($array_estados_evaluados as $id_estado => $array_estado){
				$array_grafico_reportables[] = array(
					'nombre_estado' => $array_estado["nombre_estado"],
					'porcentaje' => $total_evaluado == 0?0:(($array_estado["cant"] * 100) / ($total_evaluado)),
					'color' => $array_estado["color"]
				);
			}

			$view_data["grafico_reportables"] = $array_grafico_reportables;

			/*$compromisos_reportables = $this->Compromises_reportables_model->get_reportable_compromises($id_compromiso_reportables)->result_array();

			$array_compromisos_reportables = array();
			foreach($compromisos_reportables as $cr){
				$cr["sub_total"] = 1;
				$array_compromisos_reportables[] = $cr;
			}

			$result_acr = array();
			$cantidad_total_reportables = 0;
			foreach($array_compromisos_reportables as $acr){
				$repeat = false;
				for($i = 0; $i < count($result_acr); $i++){
					if($result_acr[$i]['id_estado'] == $acr['id_estado']){
						$result_acr[$i]['sub_total'] += $acr['sub_total'];
						$repeat = true;
						break;
					}
				}
				if($repeat == false){
					$result_acr[] = array('id_estado' => $acr['id_estado'], 'nombre_estado' => $acr['nombre_estado'], 'sub_total' => $acr['sub_total'], 'porcentaje' => 0, 'color' => $acr['color']);
				}
				$cantidad_total_reportables++;
			}

			$array_result_acr = array();
			foreach($result_acr as $result){

				$array_result_acr[] = array(
					'id_estado' => $result['id_estado'],
					'nombre_estado' => $result['nombre_estado'],
					'sub_total' => $result['sub_total'],
					'porcentaje' => ($result['sub_total'] * 100) / $cantidad_total_reportables,
					'color' => $result['color']
				);

			}

			$view_data["compromisos_reportables"] = $array_result_acr;*/

			/* FIN SECCIÓN COMPROMISOS REPORTABLES */


			/* SECCIÓN ESTADOS DE CUMPLIMIENTO REPORTABLES */

			// FILTRO TIPO DE CUMPLIMIENTO
			$array_compliance_types[] = array("id" => "", "text" => "- ".lang("compliance_type")." -");
			$array_compliance_types[] = array("id" => "environmental_adaptation_actions", "text" => lang("environmental_adaptation_actions"));
			$array_compliance_types[] = array("id" => "mitigation_measures_in_existing_components", "text" => lang("mitigation_measures_in_existing_components"));
			$array_compliance_types[] = array("id" => "prevention_control_and_mitigation_measures_in_projected_components", "text" => lang("prevention_control_and_mitigation_measures_in_projected_components"));
			$array_compliance_types[] = array("id" => "measures_for_the_prevention_control_and_mitigation_of_environmental_impacts", "text" => lang("measures_for_the_prevention_control_and_mitigation_of_environmental_impacts"));
			$array_compliance_types[] = array("id" => "closure_or_abandonment_plan", "text" => lang("closure_or_abandonment_plan"));
			$array_compliance_types[] = array("id" => "contingency_plan", "text" => lang("contingency_plan"));
			$array_compliance_types[] = array("id" => "solid_waste_management_plan", "text" => lang("solid_waste_management_plan"));
			$array_compliance_types[] = array("id" => "main_environmental_obligations", "text" => lang("main_environmental_obligations"));
			$array_compliance_types[] = array("id" => "environmental_education_program", "text" => lang("environmental_education_program"));
			$array_compliance_types[] = array("id" => "environmental_monitoring_program", "text" => lang("environmental_monitoring_program"));
			$array_compliance_types[] = array("id" => "citizen_participation_program", "text" => lang("citizen_participation_program"));
			$array_compliance_types[] = array("id" => "environmental_signage_program", "text" => lang("environmental_signage_program"));
			$array_compliance_types[] = array("id" => "preventive_or_corrective_program", "text" => lang("preventive_or_corrective_program"));
			$view_data["compliance_types_dropdown"] = json_encode($array_compliance_types);

			// FILTRO TEMA AMBIENTAL
			$array_environmental_topic[] = array("id" => "", "text" => "- ".lang("environmental_topic")." -");
			$array_environmental_topic[] = array("id" => "water", "text" => lang("water"));
			$array_environmental_topic[] = array("id" => "air", "text" => lang("air"));
			$array_environmental_topic[] = array("id" => "air/noise", "text" => lang("air/noise"));
			$array_environmental_topic[] = array("id" => "control_of_agricultural_inputs", "text" => lang("control_of_agricultural_inputs"));
			$array_environmental_topic[] = array("id" => "crops", "text" => lang("crops"));
			$array_environmental_topic[] = array("id" => "environmental_education", "text" => lang("environmental_education"));
			$array_environmental_topic[] = array("id" => "industrial_and_domestic_effluents", "text" => lang("industrial_and_domestic_effluents"));
			$array_environmental_topic[] = array("id" => "social_environment", "text" => lang("social_environment"));
			$array_environmental_topic[] = array("id" => "flora_and_fauna", "text" => lang("flora_and_fauna"));
			$array_environmental_topic[] = array("id" => "non_hazardous_waste", "text" => lang("non_hazardous_waste"));
			$array_environmental_topic[] = array("id" => "hazardous_waste", "text" => lang("hazardous_waste"));
			$array_environmental_topic[] = array("id" => "hazardous_and_non_hazardous_waste", "text" => lang("hazardous_and_non_hazardous_waste"));
			$array_environmental_topic[] = array("id" => "security_and_health_at_work", "text" => lang("security_and_health_at_work"));
			$array_environmental_topic[] = array("id" => "ground", "text" => lang("ground"));
			$array_environmental_topic[] = array("id" => "other", "text" => lang("other"));
			$view_data["environmental_topic_dropdown"] = json_encode($array_environmental_topic);

			// FILTRO INSTRUMENTO DE GESTIÓN AMBIENTAL
			$array_environmental_management_instrument[] = array("id" => "", "text" => "- ".lang("environmental_management_instrument")." -");
			$array_environmental_management_instrument[] = array("id" => "mpama", "text" => lang("mpama"));
			$array_environmental_management_instrument[] = array("id" => "pama", "text" => lang("pama"));
			$array_environmental_management_instrument[] = array("id" => "pama_and_mpama", "text" => lang("pama_and_mpama"));
			$array_environmental_management_instrument[] = array("id" => "dia", "text" => lang("dia"));
			$array_environmental_management_instrument[] = array("id" => "mdia", "text" => lang("mdia"));
			$array_environmental_management_instrument[] = array("id" => "dia_and_mdia", "text" => lang("dia_and_mdia"));
			$array_environmental_management_instrument[] = array("id" => "n/a", "text" => lang("n/a"));
			$view_data["environmental_management_instrument_dropdown"] = json_encode($array_environmental_management_instrument);

			// FILTRO AFECTACIÓN AL MEDIO POR INCUMPLIMIENTO
			$array_impact_on_the_environment[] = array("id" => "", "text" => "- ".lang("impact_on_the_environment_due_to_non_compliance")." -");
			$array_impact_on_the_environment[] = array("id" => "water", "text" => lang("water"));
			$array_impact_on_the_environment[] = array("id" => "air_and_noise", "text" => lang("air_and_noise"));
			$array_impact_on_the_environment[] = array("id" => "community/social_environment", "text" => lang("community/social_environment"));
			$array_impact_on_the_environment[] = array("id" => "flora_and_fauna", "text" => lang("flora_and_fauna"));
			$array_impact_on_the_environment[] = array("id" => "solid_waste", "text" => lang("solid_waste"));
			$array_impact_on_the_environment[] = array("id" => "hazardous_waste", "text" => lang("hazardous_waste"));
			$array_impact_on_the_environment[] = array("id" => "non_hazardous_waste", "text" => lang("non_hazardous_waste"));
			$array_impact_on_the_environment[] = array("id" => "health", "text" => lang("health"));
			$array_impact_on_the_environment[] = array("id" => "ground", "text" => lang("ground"));
			$array_impact_on_the_environment[] = array("id" => "physical_environment", "text" => lang("physical_environment"));
			$array_impact_on_the_environment[] = array("id" => "physical_and_socioeconomic_environment", "text" => lang("physical_and_socioeconomic_environment"));
			$view_data["impact_on_the_environment_dropdown"] = json_encode($array_impact_on_the_environment);
		
			

			/* FIN SECCIÓN ESTADOS DE CUMPLIMIENTO REPORTABLES */

			$array_estados_cumplimiento = array(
				'Cumple',
				'No Cumple',
				'Pendiente',
				'No Aplica'
			);
			
			// Se obtienen los colores asociados a cada estado de cumplimiento creado para el cliente
			$options = array('id_cliente' => $id_cliente, 'tipo_evaluacion' => 'reportable');
			$result_colores_por_estado = $this->Compromises_compliance_evaluation_reportables_model->get_colors_by_client($options)->result();
			$colores_por_estado = array();
			
			foreach($result_colores_por_estado as $color){
			// foreach($estados_cliente as $estado){
				$colores_por_estado[$color->categoria] = $color->color;
				// $colores_por_estado[$estado->nombre] = $estado->color;
			}

			/* SECCIÓN RESUMEN POR IGA */
			$array_instrumento_gestion_ambiental = array(
				lang("mpama"),
				lang("pama"),
				lang("pama_and_mpama"),
				lang("dia"),
				lang("mdia"),
				lang("dia_and_mdia"),
				lang("n/a")
			);
			
			$columna_instrumento_gestion_ambiental = 'instrumento_gestion_ambiental';
			$data_summary_by_iga = $this->get_data_summary_chart($id_compromiso_reportables, $array_instrumento_gestion_ambiental, $columna_instrumento_gestion_ambiental, $estados_cliente);

			$view_data['grafico_resumen_por_iga'] = $data_summary_by_iga;
			// echo '<pre>'; var_dump($data_summary_by_iga);exit;
			$view_data['array_instrumento_gestion_ambiental'] = $array_instrumento_gestion_ambiental;
			// echo '<pre>'; var_dump($array_instrumento_gestion_ambiental);exit;
			/* FIN SECCIÓN RESUMEN POR IGA */

			/* SECCIÓN RESUMEN POR TIPO DE CUMPLIMIENTO */
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
			
			$columna_compliance_types = 'tipo_cumplimiento';
			$data_summary_by_compliance_type = $this->get_data_summary_chart($id_compromiso_reportables, $array_compliance_types, $columna_compliance_types, $estados_cliente);

			$view_data['grafico_resumen_por_tipo_cumplimiento'] = $data_summary_by_compliance_type;
			// echo '<pre>'; var_dump($data_summary_by_compliance_type);exit;
			$view_data['array_compliance_types'] = $array_compliance_types;
			// echo '<pre>'; var_dump($array_compliance_types);exit;
			/* FIN SECCIÓN RESUMEN POR TIPO DE CUMPLIMIENTO */

			
			/* SECCIÓN RESUMEN POR TEMA AMBIENTAL */
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
				lang("other")
			);
			
			$columna_environmental_topic = 'tema_ambiental';
			$data_summary_by_environmental_topic = $this->get_data_summary_chart($id_compromiso_reportables, $array_environmental_topic, $columna_environmental_topic, $estados_cliente);

			$view_data['grafico_resumen_por_tema_ambiental'] = $data_summary_by_environmental_topic;
			// echo '<pre>'; var_dump($data_summary_by_environmental_topic);exit;
			$view_data['array_environmental_topic'] = $array_environmental_topic;
			// echo '<pre>'; var_dump($array_environmental_topic);exit; 
			/* FIN SECCIÓN RESUMEN POR TEMA AMBIENTAL */

			/* SECCIÓN RESUMEN POR ÁREA RESPONSABLE */
			$array_responsible_area = array(
				lang("personal_administration"),
				lang("warehouse"),
				lang("quality"),
				// lang("field"),
				lang("training"),
				// lang("communications"),
				lang("crops"),
				// lang("management"),
				lang("icp"),
				lang("maintenance"),
				lang("machinery"),
				lang("operations"),
				// lang("packing"),
				// lang("landscaping"),
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
			
			$columna_area_responsable = 'area_responsable';

			$data_summary_by_responsible_area = $this->get_data_summary_categories_area_responsible($id_compromiso_reportables, $array_responsible_area, $columna_area_responsable, $estados_cliente);

			$view_data['grafico_resumen_por_area_responsable'] = $data_summary_by_responsible_area;
			$view_data['colores_por_estado'] = $colores_por_estado;

			$view_data['array_responsible_area'] = $array_responsible_area; 
			/* FIN SECCIÓN RESUMEN POR ÁREA RESPONSABLE */


		}


		if(!$id_compromiso_rca && !$id_compromiso_reportables){
			$proyecto = $this->Projects_model->get_one($id_proyecto);
			$view_data["nombre_proyecto"] = $proyecto->title;
		}

		$view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));

		// PARA NOMBRE DE ARCHIVOS EXPORTABLES
		$view_data['sigla_cliente'] = $cliente->sigla;
		$view_data['sigla_proyecto'] = $proyecto->sigla;

        $this->template->rander("compromises_compliance_client/index", $view_data);

	}


	/** get_data_summary_chart
	 * 
	 * Funcion que crea un arreglo con los datos necesarios para enviar a la vista Detalles Cumplimiento y construir gráficos de "Resumen por ...".
	*/
	function get_data_summary_chart($id_compromiso_reportables, $array_valores_columna, $nombre_columna, $estados_cliente, $start_date = NULL, $end_date = NULL){  
		
		//Creo arreglo en el que cada valor en $array_valores_columna (lista de valores que pueden ingresarse en la columna) posee a cada estado en $array_estodos_cumplimiento, estos se inicializan con un valor igual a 0.
		$cant_compromisos_por_columna_estado = array();
		
		foreach($array_valores_columna as $valor){
		
			foreach($estados_cliente as $estado){
				if($estado->categoria == "No Aplica"){
					continue;
				}
		
				$cant_compromisos_por_columna_estado[$valor][$estado->nombre_estado] = 0;
			}
		}

		// Se obtienen de cada valor de compromisos reportables (tabla valores_compromisos_reportables) el valor almacenado en la columna $nombre_columna junto con la categoría a la que pertenezca el estado de cumplimiento de compromiso al que este asociado
		$options = array(
			'id_compromiso_reportable' => $id_compromiso_reportables, 
			'nombre_columna' => $nombre_columna,
			'start_date' => $start_date,
			'end_date' => $end_date
		);
		$result_summary = $this->Compromises_compliance_evaluation_reportables_model->get_value_compromise_cant_by_column_and_compliance_status($options)->result();

		//Version que Llena el arreglo con la cantidad de compromisos que hay por $nombre_columna y estado, no cuenta más de una vez el mismo valor_compromiso con el mismo estado/categoria.
		/* $temp_id_valor_compromiso = 0;
		$temp_categoria = '';
		foreach($result_summary as $result){
			if($result->id_valor_compromiso == $temp_id_valor_compromiso && $result->categoria == $temp_categoria){
				continue;
			}else{
				$cant_compromisos_por_columna_estado[lang($result->$nombre_columna)][$result->categoria] += 1;
				$temp_id_valor_compromiso = $result->id_valor_compromiso;
				$temp_categoria = $result->categoria;
			}
		}
 		*/
		 
		// Version que cuenta todas las evaluaciones por estado por valor alamacenado en columna $nombre_columna para un compromiso_reportable

		foreach($result_summary as $result){
			
			// Saltarse la iteración si la categoría es No Aplica o el valor guardado en la columna consultada es NULL o no esta en el arreglo de posibles valores ($array_valores_columna)
			if($result->categoria == "No Aplica" || is_null($result->$nombre_columna) || !in_array(lang($result->$nombre_columna), $array_valores_columna)){
				continue;
			}
			$cant_compromisos_por_columna_estado[lang($result->$nombre_columna)][$result->nombre_estado] += 1;
		}

		 // Se llena el arreglo que se enviará a la vista para crear un gráfico de tipo column stacked
		$data_summary = array();
		
		foreach($estados_cliente as $index => $estado){
			
			if($estado->categoria == "No Aplica"){
				continue;
			}
			$serie = array(
				'name' => $estado->nombre_estado,
				'color' => $estado->color,
				'data' => array()
			);
			foreach($cant_compromisos_por_columna_estado as $summary){
					$serie['data'][] = $summary[$estado->nombre_estado];
			}
			$data_summary[] = $serie;
		}
		return $data_summary;
	}

	/** get_data_summary_chart_for_multiselect
	 * 
	 * Funcion que crea un arreglo con los datos necesarios para construir un gráfico en la sección "Resumen por Área Responsable" de la vista Detalles Cumplimiento.
	*/
	function get_data_summary_chart_for_multiselect($id_compromiso_reportables, $array_valores_columna, $nombre_columna, $estados_cliente, $start_date = NULL, $end_date = NULL){ 
                
		//Creo arreglo en el que cada valor en $array_valores_columna (lista de valores que pueden ingresarse en la columna) posee a cada estado en $array_estados_cumplimiento, estos se inicializan con un valor igual a 0.
		$cant_compromisos_por_columna_estado = array();
		
		foreach($array_valores_columna as $valor){
		
			foreach($estados_cliente as $estado){
				if($estado->categoria == "No Aplica"){
					continue;
				}
		
				$cant_compromisos_por_columna_estado[$valor][$estado->nombre_estado] = 0;
			}
		}
		
		// Se obtienen de cada valor de compromisos reportables (tabla valores_compromisos_reportables) el valor almacenado en la columna $nombre_columna junto con la categoría a la que pertenezca el estado de cumplimiento de compromiso al que este asociado
		$options = array(
			'id_compromiso_reportable' => $id_compromiso_reportables, 
			'nombre_columna' => $nombre_columna,
			'start_date' => $start_date,
			'end_date' => $end_date
		);
		$result_summary = $this->Compromises_compliance_evaluation_reportables_model->get_value_compromise_cant_by_column_and_compliance_status($options)->result();
		
		// Cuenta todas las evaluaciones por estado por valor almacenado en columna $nombre_columna para un compromiso_reportable
		// $array=array();
		foreach($result_summary as $result){

			$array_valores_json = json_decode($result->$nombre_columna);
			
			foreach($array_valores_json as $valor){

				// Saltarse la iteración si la categoría es No Aplica o el valor guardado en la columna consultada es NULL o no esta en el arreglo de posibles valores ($array_valores_columna)
				if($result->categoria == "No Aplica" || is_null($valor) || !in_array(lang($valor), $array_valores_columna)){
					continue;
				}
				
				$cant_compromisos_por_columna_estado[lang($valor)][$result->nombre_estado] += 1;
			}
		}
		
		// Se llena el arreglo que se enviará a la vista para crear un gráfico de tipo column stacked
	   $data_summary = array();
	   
	   foreach($estados_cliente as $index => $estado){
		   
		   if($estado->categoria == "No Aplica"){
			   continue;
		   }
		   $serie = array(
			   'name' => $estado->nombre_estado,
			   'color' => $estado->color,
			   'data' => array()
		   );
		   foreach($cant_compromisos_por_columna_estado as $summary){
				$serie['data'][] = $summary[$estado->nombre_estado];	
		   }
		   $data_summary[] = $serie;
	   }
	   
	   return $data_summary;
	}
	

	/** get_data_summary_categories_area_responsible
	 * 
	 * Funcion que crea un arreglo con los datos necesarios para construir un gráfico de 2 niveles (drilldown) en la seccion "Resumen por Área Responsable" de la vista Detalles Cumplimiento.
	*/
	function get_data_summary_categories_area_responsible($id_compromiso_reportables, $array_valores_columna, $nombre_columna, $estados_cliente, $start_date = NULL, $end_date = NULL){

		$array_gerencias = array(
			'agricultural' => lang('agricultural'),
			'administration_and_finance' => lang('administration_and_finance'),
			'human_management' => lang('human_management'),
			'cerro_prieto_irrigator' => lang('cerro_prieto_irrigator'),
			'packaging_plant_and_projects' => lang('packaging_plant_and_projects'),
			'sustainability' => lang('sustainability')	// ex General
		);

		// Arreglo que almacena la cantidad de estados que tienen los Valores_compromisos por Gerencia. Se inicializa cada valor con 0.
		$compromisos_por_gerencia_estado = array();
		foreach($array_gerencias as $key => $gerencia){
			foreach($estados_cliente as $estado){
				if($estado->categoria == "No Aplica"){
					continue;
				}
				$compromisos_por_gerencia_estado[$gerencia][$estado->nombre_estado] = 0;
			}
		}

		$options = array(
			'id_compromiso_reportable' => $id_compromiso_reportables, 
			'nombre_columna' => $nombre_columna,
			'start_date' => $start_date,
			'end_date' => $end_date
		);
		// Obtener los valores_compromisos_reportables ( IDs y columna $nombre_columna y datos de estados de las evaluaciones asociadas) correspondientes al $id_compromiso_reportables (matriz de compromiso) y dentro del rango de fecha (si es que se definen).
		$result_summary = $this->Compromises_compliance_evaluation_reportables_model->get_value_compromise_cant_by_column_and_compliance_status($options)->result();
		
		// Se ponen las areas dentro de las gerencía que les correspondan.
		$array_agricola = array(
			lang('machinery'),
			lang('crops'),
			lang('irrigation')
		);
		$array_adm_finanzas = array(
			lang('warehouse'),
			lang('operations'),
			lang('pre_mix'),
			lang('personal_administration')
		);
		$array_gestion_humana = array(
			lang('training'),
			lang('occupational_health'),
			lang('industrial_security'),
			lang('general_services'),
			lang('transportation'),
			lang('recruitment')
		);
		$array_irrigadora_cerro_prieto = array(
			lang('icp'),
			lang('social_responsability')
		);
		$array_planta_empaque = array(
			lang('projects'),
			lang('quality'),
			lang('maintenance')
		);
		$array_sostenibilidad = array(
			lang('sig'),
			lang('environment')
		);

		// Se cuentan los compromisos (valores_compromisos_reportables) por estado de cada gerencia.
		foreach($result_summary as $result){

			if($result->categoria == "No Aplica"){
				continue;
			}

			$array_area_responsable_compromiso = json_decode($result->area_responsable);
			
			foreach($array_area_responsable_compromiso as $area_responsable){
				
				if(in_array(lang($area_responsable), $array_agricola)){
					$compromisos_por_gerencia_estado[lang('agricultural')][$result->nombre_estado] += 1;
				}elseif(in_array(lang($area_responsable), $array_adm_finanzas)){
					$compromisos_por_gerencia_estado[lang('administration_and_finance')][$result->nombre_estado] += 1;
				}elseif(in_array(lang($area_responsable), $array_gestion_humana)){
					$compromisos_por_gerencia_estado[lang('human_management')][$result->nombre_estado] += 1;
				}elseif(in_array(lang($area_responsable), $array_irrigadora_cerro_prieto)){
					$compromisos_por_gerencia_estado[lang('cerro_prieto_irrigator')][$result->nombre_estado] += 1;
				}elseif(in_array(lang($area_responsable), $array_planta_empaque)){
					$compromisos_por_gerencia_estado[lang('packaging_plant_and_projects')][$result->nombre_estado] += 1;
				}elseif(in_array(lang($area_responsable), $array_sostenibilidad)){
					$compromisos_por_gerencia_estado[lang('sustainability')][$result->nombre_estado] += 1;
				}
			}
		}

		// Se llena un arreglo con los datos para el primer nivel del gráfico
		// Se muestran la cantidad de compromisos por estado en cada gerencia.
		$data_gerencia = array();

		foreach($estados_cliente as $index => $estado){
			
			if($estado->categoria == "No Aplica"){
				continue;
			}

			$serie = array(
				'name' => $estado->nombre_estado,
				'color' => $estado->color,
				'data' => array()	//en data se guardan las cantidades de todas las gerencias para un mismo estado
			);
			// Se recorren todas las gerencias y se saca el valor del estado correspondiente a la iteración
			foreach($compromisos_por_gerencia_estado as $gerencia => $cant_por_estado){
				$serie['data'][] = array('name' => $gerencia, 'y' => $cant_por_estado[$estado->nombre_estado], 'drilldown' => $gerencia.'-'.$estado->nombre_estado);
			}
			$data_gerencia[] = $serie;
		}
		 
		// DRILLDOWN
		// Se llenan los datos para el segundo nivel del gráfico
		// En este se muestran la cantidad de compromisos por estado en cada Área responsable.
		$compromisos_por_area_estado = array();
		foreach($array_valores_columna as $area_responsable){//array_valores_columna = arreglo de areas_responsable ($array_responsible_area)
			foreach($estados_cliente as $estado){
				if($estado->categoria == "No Aplica"){
					continue;
				}
				// Se inicializa en 0 todas las combinaciones entre area_responsable y estados.
				$compromisos_por_area_estado[$area_responsable][$estado->nombre_estado] = 0;
			}
		}

		foreach($result_summary as $result){
			
			$array_area_responsable_compromiso = json_decode($result->area_responsable);
			// Se recorren todas las areas que se hayan ingresado en el valor_compromiso (se ingresan en un multi-select)
			foreach($array_area_responsable_compromiso as $area_responsable){
				
				if(!in_array(lang($area_responsable), $array_valores_columna)) continue;
				
				if($result->categoria == "No Aplica"){
					continue;
				}

				$compromisos_por_area_estado[lang($area_responsable)][$result->nombre_estado] += 1;
			
			}
		}

		$series_drilldown = array();
		foreach($estados_cliente as $estado){
			if($estado->categoria == "No Aplica"){
				continue;
			}
			
			$serie_agricola = array(
				'id' => lang('agricultural').'-'.$estado->nombre_estado,
				'name' => $estado->nombre_estado,
				'data' => array()
			);

			$serie_adm_finanzas = array(
				'id' => lang('administration_and_finance').'-'.$estado->nombre_estado,
				'name' => $estado->nombre_estado,
				'data' => array()
			);
			$serie_gestion_humana = array(
				'id' => lang('human_management').'-'.$estado->nombre_estado,
				'name' => $estado->nombre_estado,
				'data' => array()
			);
			$serie_irrigadora_cerro_prieto = array(
				'id' => lang('cerro_prieto_irrigator').'-'.$estado->nombre_estado,
				'name' => $estado->nombre_estado,
				'data' => array()
			);
			$serie_planta_empaque = array(
				'id' => lang('packaging_plant_and_projects').'-'.$estado->nombre_estado,
				'name' => $estado->nombre_estado,
				'data' => array()
			);
			$serie_sostenibilidad = array(
				'id' => lang('sustainability').'-'.$estado->nombre_estado,
				'name' => $estado->nombre_estado,
				'data' => array()
			);

			foreach($compromisos_por_area_estado as $area => $cant_por_estado){	
			
				if(in_array($area, $array_agricola)){

					$serie_agricola['data'][] = array($area, $cant_por_estado[$estado->nombre_estado]); 
					
				}elseif(in_array($area, $array_adm_finanzas)){
					
					$serie_adm_finanzas['data'][] = array($area, $cant_por_estado[$estado->nombre_estado]); 

				}elseif(in_array($area, $array_gestion_humana)){

					$serie_gestion_humana['data'][] = array($area, $cant_por_estado[$estado->nombre_estado]); 

				}elseif(in_array($area, $array_irrigadora_cerro_prieto)){

					$serie_irrigadora_cerro_prieto['data'][] = array($area, $cant_por_estado[$estado->nombre_estado]); 

				}elseif(in_array($area, $array_planta_empaque)){

					$serie_planta_empaque['data'][] = array($area, $cant_por_estado[$estado->nombre_estado]); 

				}elseif(in_array($area, $array_sostenibilidad)){

					$serie_sostenibilidad['data'][] = array($area, $cant_por_estado[$estado->nombre_estado]); 

				}
			
			}

			$series_drilldown[] = $serie_agricola;
			$series_drilldown[] = $serie_adm_finanzas;
			$series_drilldown[] = $serie_gestion_humana;
			$series_drilldown[] = $serie_irrigadora_cerro_prieto;
			$series_drilldown[] = $serie_planta_empaque;
			$series_drilldown[] = $serie_sostenibilidad;
		}
		
		$chart_data = array('x_axis' => array_values($array_gerencias), 'series' => $data_gerencia, 'drilldown' => $series_drilldown);
		return $chart_data;

	}

	

	/* Para AppTable de sección ESTADOS DE CUMPLIMIENTO */
	function list_data($id_compromiso_rca) {

		$reportabilidad = $this->input->post("reportabilidad");
		$options = array(
			"reportabilidad" => $reportabilidad
		);

		$list_data = $this->Compromises_rca_model->get_data_of_compliance_status($id_compromiso_rca, $options)->result_array(); //traer consulta

		$new_list_data = array();
		/*
		foreach($list_data as $row){
			$new_list_data[$row["id_valor_compromiso"]][$row["id_evaluado"]] = array(
															"id_evaluacion" => $row["id_evaluacion"],
															"id_evaluado" => $row["id_evaluado"],
															"nombre_evaluado" => $row["nombre_evaluado"],
															"id_estado" => $row["id_estado"],
															"nombre_estado" => $row["nombre_estado"],
															"fecha_evaluacion" => $row["fecha_evaluacion"]);
		}
		*/

		foreach($list_data as $row){

			//consultar por la combinacion de id_valor_compromiso e id_evaluado del row más reciente por fecha_evaluacion y guardar esa en el new list data
			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $row["id_evaluado"], "id_valor_compromiso" => $row["id_valor_compromiso"]))->result_array();

			if($row["id_evaluacion"] == $ultima_evaluacion[0]["id"]){

				$new_list_data[$row["id_valor_compromiso"]][$row["id_evaluado"]] = array(
															"id_evaluacion" => $row["id_evaluacion"],
															"id_evaluado" => $row["id_evaluado"],
															"nombre_evaluado" => $row["nombre_evaluado"],
															"id_estado" => $row["id_estado"],
															"nombre_estado" => $row["nombre_estado"],
															"fecha_evaluacion" => $row["fecha_evaluacion"]);

			}

		}

		//var_dump($new_list_data);

		$array_columnas = array();
		$traer_columnas = $this->Compromises_rca_model->get_fields_of_compliance_status($id_compromiso_rca)->result_array();

		foreach($traer_columnas as $columnas){
			$array_columnas[$columnas["id"]] = $columnas["nombre_evaluado"];
		}

        $result = array();
        foreach ($new_list_data as $id_valor_compromiso => $data) {
            $result[] = $this->_make_row(array($id_valor_compromiso => $data), $array_columnas);
        }

        echo json_encode(array("data" => $result));

    }

	/* Para AppTable de sección ESTADOS DE CUMPLIMIENTO */
	private function _make_row($data, $array_columnas) {

		$row_data = array();
		//$row_data[] = key($data);
		$row_data[] = $this->Values_compromises_rca_model->get_one(key($data))->numero_compromiso;
		$reportabilidad = $this->Values_compromises_rca_model->get_one(key($data))->reportabilidad;
		$row_data[] = ($reportabilidad == 1) ? '<i class="fa fa-check" aria-hidden="true"></i>' : '<i class="fa fa-times" aria-hidden="true"></i>';

		$row_data[] = $this->Values_compromises_rca_model->get_one(key($data))->nombre_compromiso;

		foreach($data as $key_evaluado => $array_evaluado){
			ksort($array_evaluado);
			if(count($array_columnas) != count($array_evaluado)){ //Si la cantidad de columnas es distinta a la cantidad de evaluados

				foreach($array_columnas as $id_evaluado => $columna){ //Loop sobre las columnas (Evaluado 1, Evaluado N)

					if(in_array($id_evaluado, $array_evaluado[$id_evaluado])){
						$distintos = false;
					} else {
						$distintos = true;
					}

					if($distintos){
						$row_data[] = "-";
					} else {

						//var_dump($array_evaluado);

						$id_estado_cumplimiento_compromiso = $array_evaluado[$id_evaluado]["id_estado"];
						$estado = $this->Compromises_compliance_status_model->get_one($id_estado_cumplimiento_compromiso);
						$nombre_estado = $estado->nombre_estado;
						$color_estado = $estado->color;

						$html_estado = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado .= $nombre_estado;
						$html_estado .= '</div>';

						$row_data[] = $html_estado;
					}

				}

			} else {

				foreach($array_evaluado as $evaluado){

					$id_estado_cumplimiento_compromiso = $evaluado["id_estado"];
					$estado = $this->Compromises_compliance_status_model->get_one($id_estado_cumplimiento_compromiso);
					$nombre_estado = $estado->nombre_estado;
					$color_estado = $estado->color;

					$html_estado = '<div class="text-center" style="text-align: -webkit-center;">';
					$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
					$html_estado .= $nombre_estado;
					$html_estado .= '</div>';

					$row_data[] = $html_estado;

				}

			}

		}

		$hay_evidencia = false;
		$hay_observaciones = false;
		$evaluaciones = $this->Compromises_compliance_evaluation_rca_model->get_all_where(array("id_valor_compromiso" => key($data), "deleted" => 0))->result_array();

		foreach($evaluaciones as $evaluacion){

			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $evaluacion["id_evaluado"], "id_valor_compromiso" => $evaluacion["id_valor_compromiso"]))->result_array();

			$evidencias_evaluacion = $this->Compromises_compliance_evidences_model->get_all_where(
				array(
					"id_evaluacion_cumplimiento_compromiso" => $ultima_evaluacion[0]["id"],
					"tipo_evaluacion" => "rca",
					"deleted" => 0
				)
			)->result_array();
			if($evidencias_evaluacion){
				$hay_evidencia = true;
			}

			if($evaluacion["id"] == $ultima_evaluacion[0]["id"]){
				if($evaluacion["observaciones"] || $evaluacion["observaciones"] != ""){
					//var_dump("evaluacion: " . $evaluacion["id"] . " | observaciones: " . $evaluacion["observaciones"]);
					$hay_observaciones = true;
				}
			}

		}

		$modal_evidencias = modal_anchor(get_uri("compromises_compliance_client/view_all_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_valor_compromiso" => key($data)));
		//$row_data[] = ($evidencia) ? $modal_evidencias : "-";
		$row_data[] = ($hay_evidencia) ? $modal_evidencias : "-";
		//$row_data[] = $modal_evidencias;

		/*
		//Observaciones con ToolTip

		$evaluacion = array();
		$nombre_compromiso = $this->Values_compromises_model->get_one(key($data))->nombre_compromiso;

		$html_observaciones = $nombre_compromiso;
		$html_observaciones .= "<div style='text-align: left;'>";

		foreach($array_evaluado as $evaluado){

			$evaluacion = $this->Compromises_compliance_evaluation_model->get_all_where(array("id" => $evaluado["id_evaluacion"], "deleted" => 0))->result_array();

			if($evaluacion){
				foreach($evaluacion as $row){

					$nombre_evaluado = $this->Evaluated_compromises_model->get_one($row["id_evaluado"])->nombre_evaluado;
					$observaciones = $row["observaciones"];
					if(!$observaciones || $observaciones == ""){
						$observaciones = "Sin observaciones";
					}

					$html_observaciones .= '<br>';
					$html_observaciones .= $nombre_evaluado . ": " . $observaciones;

				}
			}

		}

		$html_observaciones .= '</div>';
		$tooltip_observaciones = '<span class="help" data-container="body" data-html="true" data-toggle="tooltip" title="'.$html_observaciones.'"><i class="fa fa-info tooltips"></i></span>';
		$tooltip_observaciones .= '<script type="text/javascript">';
		$tooltip_observaciones .= '$(document).ready(function(){';
		$tooltip_observaciones .= '$(\'[data-toggle="tooltip"]\').tooltip();';
		$tooltip_observaciones .= '})';
		$tooltip_observaciones .= '</script>';
		$row_data[] = $tooltip_observaciones;
		*/

		$modal_observaciones = modal_anchor(get_uri("compromises_compliance_client/view_all_observations/"), "<i class='fas fa-info-circle fa-lg'></i>", array("class" => "edit", "title" => lang('view_observations'), "data-post-id_valor_compromiso" => key($data)));
		$row_data[] = ($hay_observaciones) ? $modal_observaciones : "-";

        return $row_data;

    }

	function view_all_evidences(){

		$id_valor_compromiso = $this->input->post("id_valor_compromiso");
		$evaluaciones = $this->Compromises_compliance_evaluation_rca_model->get_all_where(array("id_valor_compromiso" => $id_valor_compromiso, "deleted" => 0))->result_array();
		$nombre_compromiso = $this->Values_compromises_rca_model->get_one($id_valor_compromiso)->nombre_compromiso;

		$html_titulo_archivos_evidencia = '<div class="form-group">';
		$html_titulo_archivos_evidencia .= '<label for="nombre_compromiso" class="col-md-3">'.lang("compromise_name").'</label>';
		$html_titulo_archivos_evidencia .= '<div class="col-md-9">'.$nombre_compromiso.'</div>';
		$html_titulo_archivos_evidencia .= '</div>';
		$html_final = "";

		$this->array_sort_by_column($evaluaciones, 'id_evaluado');

		foreach($evaluaciones as $evaluacion){

			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $evaluacion["id_evaluado"], "id_valor_compromiso" => $evaluacion["id_valor_compromiso"]))->result_array();

			if($evaluacion["id"] == $ultima_evaluacion[0]["id"]){

				$evidencias_evaluacion = $this->Compromises_compliance_evidences_model->get_all_where(
					array(
						"id_evaluacion_cumplimiento_compromiso" => $ultima_evaluacion[0]["id"],
						"tipo_evaluacion" => "rca",
						"deleted" => 0
					)
				)->result_array();

				$nombre_evaluado = $this->Evaluated_rca_compromises_model->get_one($evaluacion["id_evaluado"])->nombre_evaluado;

				$html_archivos_evidencia = "<hr>";
				$html_archivos_evidencia .= '<div class="form-group">';
				$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">'.$nombre_evaluado.'</label>';
				$html_archivos_evidencia .= '<div class="col-md-9">';

				if($evidencias_evaluacion){

					foreach($evidencias_evaluacion as $evidencia){

						$html_archivos_evidencia .= '<div class="col-md-8">';
						$html_archivos_evidencia .= remove_file_prefix($evidencia["archivo"]);
						$html_archivos_evidencia .= '</div>';
						$html_archivos_evidencia .= '<div class="col-md-4">';
						$html_archivos_evidencia .= '<table id="table_delete_'.$evidencia["id"].'" class="table_delete"><thead><tr><th></th></tr></thead>';
						$html_archivos_evidencia .= '<tbody><tr><td class="option text-center">';
						$html_archivos_evidencia .= anchor(get_uri("compromises_rca_evaluation/download_file/".$evaluacion["id"]. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
						//$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id_evaluacion" => $compliance_evaluation_info->id, "data-id_evidencia" => $evidencia["id"], "data-action-url" => get_uri("compromises_compliance_evaluation/delete_file"), "data-action" => "delete-confirmation"));
						//$html_archivos_evidencia .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';
						$html_archivos_evidencia .= '</td>';
						$html_archivos_evidencia .= '</tr>';
						$html_archivos_evidencia .= '</thead>';
						$html_archivos_evidencia .= '</table>';
						$html_archivos_evidencia .= '</div>';
						$html_archivos_evidencia .= '</div>';
						$html_archivos_evidencia .= '</div>';
						$html_archivos_evidencia .= '<div class="form-group">';
						$html_archivos_evidencia .= '<label for="archivos" class="col-md-3"></label>';
						$html_archivos_evidencia .= '<div class="col-md-9">';

					}

					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '</div>';


				} else {

					$html_archivos_evidencia .= '<div class="col-md-8">';
					$html_archivos_evidencia .= lang("no_evidence_files");
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '<div class="col-md-4">';
					$html_archivos_evidencia .= '<table id="table_delete_'.$evidencia["id"].'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html_archivos_evidencia .= '<tbody><tr><td class="option text-center">';
					//$html_archivos_evidencia .= anchor(get_uri("compromises_compliance_evaluation/download_file/".$evaluacion["id"]. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					//$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id_evaluacion" => $compliance_evaluation_info->id, "data-id_evidencia" => $evidencia["id"], "data-action-url" => get_uri("compromises_compliance_evaluation/delete_file"), "data-action" => "delete-confirmation"));
					//$html_archivos_evidencia .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';
					$html_archivos_evidencia .= '</td>';
					$html_archivos_evidencia .= '</tr>';
					$html_archivos_evidencia .= '</thead>';
					$html_archivos_evidencia .= '</table>';
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '<div class="form-group">';
					$html_archivos_evidencia .= '<label for="archivos" class="col-md-3"></label>';
					$html_archivos_evidencia .= '<div class="col-md-9">';
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '</div>';
				}

				$html_final .= $html_archivos_evidencia;

			}

		}

		$view_data["html_titulo_archivos_evidencia"] = $html_titulo_archivos_evidencia;
		$view_data["html_archivos_evidencia"] = $html_final;

		$this->load->view('compromises_compliance_client/view_all_evidences', $view_data);

	}

	function view_all_observations(){

		$id_valor_compromiso = $this->input->post("id_valor_compromiso");
		$evaluaciones = $this->Compromises_compliance_evaluation_rca_model->get_all_where(array("id_valor_compromiso" => $id_valor_compromiso, "deleted" => 0))->result_array();
		$nombre_compromiso = $this->Values_compromises_rca_model->get_one($id_valor_compromiso)->nombre_compromiso;

		$html_titulo_observaciones = '<div class="form-group">';
		$html_titulo_observaciones .= '<label for="nombre_compromiso" class="col-md-3">'.lang("compromise_name").'</label>';
		$html_titulo_observaciones .= '<div class="col-md-9">'.$nombre_compromiso.'</div>';
		$html_titulo_observaciones .= '</div>';
		$html_final = "";

		$this->array_sort_by_column($evaluaciones, 'id_evaluado');

		foreach($evaluaciones as $evaluacion){

			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $evaluacion["id_evaluado"], "id_valor_compromiso" => $evaluacion["id_valor_compromiso"]))->result_array();

			if($evaluacion["id"] == $ultima_evaluacion[0]["id"]){

				$nombre_evaluado = $this->Evaluated_rca_compromises_model->get_one($evaluacion["id_evaluado"])->nombre_evaluado;

				$html_observaciones = "<hr>";
				$html_observaciones .= '<div class="form-group">';
				$html_observaciones .= '<label for="archivos" class="col-md-3">'.$nombre_evaluado.'</label>';
				$html_observaciones .= '<div class="col-md-9">';
				$html_observaciones .= ((!$evaluacion["observaciones"]) || $evaluacion["observaciones"] == "") ? "-" : $evaluacion["observaciones"];
				$html_observaciones .= '</div>';
				$html_observaciones .= '</div>';
				$html_final .= $html_observaciones;

			}

		}

		$view_data["html_titulo_observaciones"] = $html_titulo_observaciones;
		$view_data["html_observaciones"] = $html_final;

		$this->load->view('compromises_compliance_client/view_all_observations', $view_data);

	}

	function get_quantity_of_status_evaluated($id_estado, $id_evaluado){

		$cantidad = 0;
		$evaluaciones = $this->Compromises_rca_model->get_quantity_of_status_evaluated($id_estado, $id_evaluado)->result_array();

		foreach($evaluaciones as $evaluacion){
			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $evaluacion["id_evaluado"], "id_valor_compromiso" => $evaluacion["id_valor_compromiso"]))->result_array();
			if($ultima_evaluacion[0]["id"] == $evaluacion["id"]){
				$cantidad++;
			}
		}

		return $cantidad;
		//$cantidad = $this->Compromises_model->get_quantity_of_status_evaluated($id_estado, $id_evaluado)->row();
		//return $cantidad->cantidad;
	}

	function get_percentage_of_status_evaluated($cantidad_compromisos, $id_estado, $id_evaluado){

		$compromisos_por_evaluado = $this->Compromises_rca_model->get_total_applicable_compromises_by_evaluated($id_evaluado)->result_array();
		$total_compromisos_por_evaluado = 0;

		foreach($compromisos_por_evaluado as $cpe){
			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $cpe["id_evaluado"], "id_valor_compromiso" => $cpe["id_valor_compromiso"]))->result_array();
			if($ultima_evaluacion[0]["id"] == $cpe["id_evaluacion"]){
				$total_compromisos_por_evaluado++;
			}
		}

		if($cantidad_compromisos == 0){
			$porcentaje = 0;
		} else {
			$porcentaje = ($cantidad_compromisos * 100) / $total_compromisos_por_evaluado;
		}

		return $porcentaje;
		//$porcentaje = $this->Compromises_model->get_percentage_of_status_evaluated($id_estado, $id_evaluado)->row();
		//return $porcentaje->porcentaje;
	}

	function get_color_of_status($id_estado){
		$estado = $this->Compromises_compliance_status_model->get_one($id_estado);
		return $estado->color;
	}

	/* Función para ordenar un array multidimensional especificando el index ($col) */
	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}
		array_multisort($sort_col, $dir, $arr);
	}

	function get_excel_compliance_status(){

		$id_usuario = $this->session->user_id;
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;

		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);

		$matriz_compromisos = $this->Compromises_rca_model->get_one_where(array(
			"id_proyecto" => $id_proyecto,
			"deleted" => 0
		));

		$id_compromiso = $matriz_compromisos->id;
		$list_data = $this->Compromises_rca_model->get_data_of_compliance_status($id_compromiso)->result_array(); //traer consulta

		$new_list_data = array();
		foreach($list_data as $row){

			//consultar por la combinacion de id_valor_compromiso e id_evaluado del row más reciente por fecha_evaluacion y guardar esa en el new list data
			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $row["id_evaluado"], "id_valor_compromiso" => $row["id_valor_compromiso"]))->result_array();

			if($row["id_evaluacion"] == $ultima_evaluacion[0]["id"]){

				$new_list_data[$row["id_valor_compromiso"]][$row["id_evaluado"]] = array(
															"id_evaluacion" => $row["id_evaluacion"],
															"id_evaluado" => $row["id_evaluado"],
															"nombre_evaluado" => $row["nombre_evaluado"],
															"id_estado" => $row["id_estado"],
															"nombre_estado" => $row["nombre_estado"],
															"fecha_evaluacion" => $row["fecha_evaluacion"]);

			}

		}

		$array_columnas = array();
		$traer_columnas = $this->Compromises_rca_model->get_fields_of_compliance_status($id_compromiso)->result_array();

		foreach($traer_columnas as $columnas){
			$array_columnas[$columnas["id"]] = $columnas["nombre_evaluado"];
		}

		$columnas_campos_matriz = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso)->result();

        $result = array();
        foreach ($new_list_data as $id_valor_compromiso => $data) {
            $result[] = $this->_make_row_excel_compliance_status(array($id_valor_compromiso => $data), $array_columnas, $columnas_campos_matriz, $id_proyecto);
        }

		$this->load->library('excel');

		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle("")
							 ->setSubject("")
							 ->setDescription("")
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");

		if($client_info->color_sitio){
			$color_sitio = str_replace('#', '', $client_info->color_sitio);
		} else {
			$color_sitio = "00b393";
		}

		// ESTILOS
		$styleArray = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
			'fill' => array(
				'rotation' => 90,
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => $color_sitio)
			),
		);

		// LOGO
		if($client_info->id){
			if($client_info->logo){
				$url_logo = "files/mimasoft_files/client_".$client_info->id."/".$client_info->logo.".png";
			} else {
				$url_logo = "files/system/default-site-logo.png";
			}
		} else {
			$url_logo = "files/system/default-site-logo.png";
		}

		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo');
		$objDrawing->setDescription('Logo');
		$objDrawing->setPath('./'.$url_logo);
		$objDrawing->setHeight(35);
		$objDrawing->setOffsetY(6);
		$objDrawing->setOffsetX(20);
		$objDrawing->setWorksheet($doc->getActiveSheet());
		$doc->getActiveSheet()->mergeCells('A1:B3');
		$doc->getActiveSheet()->getStyle('A1:B3')->applyFromArray($styleArray);

		$nombre_columnas = array();
		$nombre_columnas[] = array("nombre_columna" => lang("n_compromise"), "id_tipo_campo" => "n_compromise");
		$nombre_columnas[] = array("nombre_columna" => lang("name"), "id_tipo_campo" => "name");
		$nombre_columnas[] = array("nombre_columna" => lang("phases"), "id_tipo_campo" => "phases");
		$nombre_columnas[] = array("nombre_columna" => lang("reportability"), "id_tipo_campo" => "reportability");

		foreach($columnas_campos_matriz as $columna_matriz){
			if(($columna_matriz->id_tipo_campo == 11)||($columna_matriz->id_tipo_campo == 12)){
				continue;
			}
			$nombre_columnas[] = array("nombre_columna" => $columna_matriz->nombre_campo, "id_tipo_campo" => $columna_matriz->id_tipo_campo);
		}

		foreach($array_columnas as $nombre_evaluado){
			$nombre_columnas[] = array("nombre_columna" => $nombre_evaluado, "id_tipo_campo" => "evaluated_name");
		}

		// HEADER
		$fecha = get_date_format(date('Y-m-d'), $id_proyecto);
		$hora = convert_to_general_settings_time_format($id_proyecto, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $id_proyecto));

		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', lang("compliance_status"))
			->setCellValue('C2', $project_info->title)
			->setCellValue('C3', lang("date").': '.$fecha.' '.lang("at").' '.$hora);

		$doc->setActiveSheetIndex(0);

		// SETEO DE CABECERAS DE CONTENIDO A LA HOJA DE EXCEL
		//$doc->getActiveSheet()->fromArray($nombre_columnas, NULL,"A5");
		$col = 0; // EMPEZANDO DE LA COLUMNA 'A'
		foreach($nombre_columnas as $index => $columna){
			$valor = (!is_array($columna)) ? $columna : $columna["nombre_columna"];
			$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row = 5, $valor);
			$col++;
		}

		// CARGA DE CONTENIDO A LA HOJA DE EXCEL
		$col = 0; // EMPEZANDO DE LA COLUMNA 'A'
		$row = 6; // EMPEZANDO DE LA FILA 6
		foreach($result as $res){

			foreach($nombre_columnas as $index_columnas => $columna){

				$name_col = PHPExcel_Cell::stringFromColumnIndex($col);
				$doc->getActiveSheet()->getColumnDimension($name_col)->setAutoSize(true);
				$valor = $res[$index_columnas];

				if(!is_array($columna)){

					$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);

				} else {

					if($columna["id_tipo_campo"] == 1){ // INPUT TEXT

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == 2){ // TEXTO LARGO

						$doc->getActiveSheet()->getColumnDimension($name_col)->setAutoSize(false);
						$doc->getActiveSheet()->getColumnDimension($name_col)->setWidth(50);
						$doc->getActiveSheet()->getStyle($name_col.$row)->getAlignment()->setWrapText(true);

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == 3){ // NÚMERO

						$doc->getActiveSheet()->setCellValueExplicitByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == 4){ // FECHA

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == 5){ // PERIODO

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] >= 6 && $columna["id_tipo_campo"] <= 9){ // SELECCIÓN, SELECCIÓN MÚLTIPLE, RUT, RADIO BUTTONS

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == 10){ // ARCHIVO

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == 11 || $columna["id_tipo_campo"] == 12){ // TEXTP FIJO, DIVISOR
						continue;
					} elseif($columna["id_tipo_campo"] == 13 || $columna["id_tipo_campo"] == 14){ // CORREO, HORA

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == 15){ // UNIDAD

						$doc->getActiveSheet()->setCellValueExplicitByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == 16){ // SELECCIÓN DESDE MANTENEDORA

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == "n_compromise"){ // NÚMERO COMPROMISO

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == "name"){ // NOMBRE

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == "phases"){ // FASES

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == "reportability"){ // REPORTABILIDAD

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == "evaluated_name"){ // EVALUADO

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == "created_date" || $columna["id_tipo_campo"] == "modified_date"){

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} else {
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);

					}

				}

				//if($columna["id_tipo_campo"] != "unity"){
					$doc->getActiveSheet()->getStyle($name_col.$row)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
				//}
				$col++;
			}

			$col = 0;
			$row++;

		}
		//$doc->getActiveSheet()->fromArray($result, NULL,"A6");


		// FILTROS
		$doc->getActiveSheet()->setAutoFilter('A5:'.$letra.'5');

		// ANCHO COLUMNAS
		$lastColumn = $doc->getActiveSheet()->getHighestColumn();
		$lastColumn++;
		$cells = array();
		for($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;
		}
		/*foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}*/

		$nombre_hoja = strlen(lang("compromises_compliance_excel")) > 31 ? substr(lang("compromises_compliance_excel"), 0, 28).'...' : lang("compromises_compliance_excel");
		$nombre_hoja = $nombre_hoja ? $nombre_hoja : " ";
		$doc->getActiveSheet()->setTitle($nombre_hoja);

		$filename = $client_info->sigla."_".$project_info->sigla."_".lang("compromises_compliance_excel")."_".date('Y-m-d');
		$filename = $filename.'.xlsx'; //save our workbook as this file name

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache

		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');
		$objWriter->save('php://output');
		exit;

	}

	private function _make_row_excel_compliance_status($data, $array_columnas, $columnas_campos_matriz, $id_proyecto) {

		$valor_compromiso = $this->Values_compromises_rca_model->get_one(key($data));

		$row_data = array();
		$row_data[] = $valor_compromiso->numero_compromiso;
		$row_data[] = $valor_compromiso->nombre_compromiso;

		/*$fases_decoded = json_decode($valor_compromiso->fases);
		$html_fases = "";
		foreach($fases_decoded as $fase){
			$html_fases .= $fase.", ";
		}
		$row_data[] = rtrim($html_fases, ", ");*/

		$fases_decoded = json_decode($valor_compromiso->fases);
		$html_fases = "";
		$array_fases = array();
		foreach($fases_decoded as $id_fase){
			$nombre_lang = $this->Phases_model->get_one($id_fase)->nombre_lang;
			$nombre_fase = lang($nombre_lang);
			$array_fases[] = $nombre_fase;
		}
		$row_data[] = implode(', ', $array_fases);

		$row_data[] = ($valor_compromiso->reportabilidad == 1) ? lang("yes") : lang("no");

		if($valor_compromiso->datos_campos){

			$arreglo_fila = json_decode($valor_compromiso->datos_campos, true);
			$cont = 0;

			foreach($columnas_campos_matriz as $columna) {
				$cont++;

				// Si existe el campo dentro de los valores del registro
				if(isset($arreglo_fila[$columna->id_campo])){

					if($columna->id_tipo_campo == 4){//si es fecha.
						$valor_campo = get_date_format($arreglo_fila[$columna->id_campo],$id_proyecto);
					}elseif($columna->id_tipo_campo == 5){// si es periodo
						$start_date = $arreglo_fila[$columna->id_campo]['start_date'];
						$end_date = $arreglo_fila[$columna->id_campo]['end_date'];
						$valor_campo = $start_date.' - '.$end_date;
					}elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
						continue;
					}elseif($columna->id_tipo_campo == 14){
						$valor_campo = convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id_campo]);
					}else{
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

		// Evaluaciones
		foreach($data as $key_evaluado => $array_evaluado){
			ksort($array_evaluado);
			if(count($array_columnas) != count($array_evaluado)){ //Si la cantidad de columnas es distinta a la cantidad de evaluados

				foreach($array_columnas as $id_evaluado => $columna){ //Loop sobre las columnas (Evaluado 1, Evaluado N)

					if(in_array($id_evaluado, $array_evaluado[$id_evaluado])){
						$distintos = false;
					} else {
						$distintos = true;
					}

					if($distintos){
						$row_data[] = "-";
					} else {
						$id_estado_cumplimiento_compromiso = $array_evaluado[$id_evaluado]["id_estado"];
						$estado = $this->Compromises_compliance_status_model->get_one($id_estado_cumplimiento_compromiso);
						$row_data[] = $estado->nombre_estado;
					}

				}

			} else {

				foreach($array_evaluado as $evaluado){
					$id_estado_cumplimiento_compromiso = $evaluado["id_estado"];
					$estado = $this->Compromises_compliance_status_model->get_one($id_estado_cumplimiento_compromiso);
					$row_data[] = $estado->nombre_estado;
				}

			}

		}

		return $row_data;

	}

	private function getNameFromNumber($num){
		$numeric = $num % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval($num / 26);
		if ($num2 > 0) {
			return $this->getNameFromNumber($num2 - 1) . $letter;
		} else {
			return (string)$letter;
		}
	}

	function get_pdf(){

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$start_date = $this->input->post("start_date");
		$end_date = $this->input->post("end_date");

		$info_cliente = $this->Clients_model->get_one($id_cliente);
		$info_proyecto = $this->Projects_model->get_one($id_proyecto);
		$id_compromiso_rca = $this->Compromises_rca_model->get_one_where(array('id_proyecto' => $info_proyecto->id, 'deleted' => 0))->id;
		$id_compromiso_reportables = $this->Compromises_reportables_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;

		$view_data["info_cliente"] = $info_cliente;
		$view_data["info_proyecto"] = $info_proyecto;
		$view_data["id_proyecto"] = $id_proyecto;
		$view_data["Compromises_compliance_client_controller"] = $this;

		$imagenes_graficos = $this->input->post("imagenes_graficos");

		/* $view_data["grafico_cumplimientos_totales"] = $imagenes_graficos["image_cumplimientos_totales"];
		$view_data["graficos_resumen_evaluados"] = $imagenes_graficos["graficos_resumen_evaluados"]; // Array con los gráficos de los evaluados */
		$view_data["grafico_resumen_cumplimiento"] = $imagenes_graficos["grafico_resumen_cumplimiento"];
		$view_data["grafico_resumen_por_iga"] = $imagenes_graficos["grafico_resumen_por_iga"];
		$view_data["grafico_resumen_por_tipo_cumplimiento"] = $imagenes_graficos["grafico_resumen_por_tipo_cumplimiento"];
		$view_data["grafico_resumen_por_tema_ambiental"] = $imagenes_graficos["grafico_resumen_por_tema_ambiental"];
		$view_data["grafico_resumen_por_area_responsable"] = $imagenes_graficos["grafico_resumen_por_area_responsable"];

		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");

		if($id_compromiso_rca){

			$view_data["id_compromiso_rca"] = $id_compromiso_rca;

			/* SECCIÓN RESUMEN POR EVALUADO */

			// COMPROMISOS AMBIENTALES - RCA

			// EVALUADOS
			$evaluados = $this->Evaluated_rca_compromises_model->get_all_where(array(
				"id_compromiso" => $id_compromiso_rca,
				"deleted" => 0
			))->result();

			// ESTADOS RCA
			$estados_cliente = $this->Compromises_compliance_status_model->get_details(array(
				"id_cliente" => $id_cliente,
				"tipo_evaluacion" => "rca",
			))->result();

			// ULTIMAS EVALUACIONES
			$ultimas_evaluaciones = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluations_of_project($id_proyecto)->result();

			// PROCESAR TABLA
			$array_estados_evaluados = array();
			$array_evaluados_estados = array();
			$array_total_por_evaluado = array();
			$array_total_por_estado = array();
			$array_compromisos_evaluaciones_no_cumple = array();
			$total = 0;

			foreach($estados_cliente as $estado) {

				$id_estado = $estado->id;

				if($estado->categoria == "No Aplica"){
					continue;
				}
				$array_estados_evaluados[$estado->id] = array(
					"nombre_estado" => $estado->nombre_estado,
					"categoria" => $estado->categoria,
					"color" => $estado->color,
					"evaluados" => array(),
					"cantidad_categoria" => 0,
				);

				$cant_estado = 0;
				foreach($evaluados as $evaluado) {

					$id_evaluado = $evaluado->id;
					$cant = 0;

					$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado] = array("cant" => 0, "evaluaciones" => array());

					foreach($ultimas_evaluaciones as $ultima_evaluacion) {
						if(
							$ultima_evaluacion->id_estados_cumplimiento_compromiso == $id_estado &&
							$ultima_evaluacion->id_evaluado == $id_evaluado
						){
							$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["evaluaciones"][] = $ultima_evaluacion;
							$array_evaluados_estados[$id_evaluado][$id_estado][] = 1;
							$cant++;
							$cant_estado++;

							if($estado->categoria == "No Cumple"){
								$criticidad_info = $this->Critical_levels_model->get_one($ultima_evaluacion->id_criticidad);
								$ultima_evaluacion->criticidad = $criticidad_info->nombre;
								$array_compromisos_evaluaciones_no_cumple[] = $ultima_evaluacion;
							}
						}
					}

					$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["cant"] = $cant;
					$array_total_por_evaluado[$id_evaluado][] = $cant;
					$array_total_por_estado[$id_estado][] = $cant;
				}

				$array_estados_evaluados[$id_estado]["cantidad_categoria"] = $cant_estado;
				$total += $cant_estado;
			}

			$view_data["evaluados_rca"] = $evaluados;
			$view_data["total_compromisos_aplicables_rca"] = $total;
			$view_data["total_cantidades_estados_evaluados_rca"] = $array_estados_evaluados;
			$view_data["total_cantidades_evaluados_estados_rca"] = $array_evaluados_estados;
			$view_data["array_total_por_evaluado_rca"] = $array_total_por_evaluado;

			/* FIN SECCIÓN RESUMEN POR EVALUADO */


			/* SECCIÓN ESTADOS DE CUMPLIMIENTO */
			$list_data = $this->Compromises_rca_model->get_data_of_compliance_status($id_compromiso_rca)->result_array();
			$new_list_data = array();

			foreach($list_data as $row){

				//consultar por la combinacion de id_valor_compromiso e id_evaluado del row más reciente por fecha_evaluacion y guardar esa en el new list data
				$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $row["id_evaluado"], "id_valor_compromiso" => $row["id_valor_compromiso"]))->result_array();

				if($row["id_evaluacion"] == $ultima_evaluacion[0]["id"]){

					$new_list_data[$row["id_valor_compromiso"]][$row["id_evaluado"]] = array(
																"id_evaluacion" => $row["id_evaluacion"],
																"id_evaluado" => $row["id_evaluado"],
																"nombre_evaluado" => $row["nombre_evaluado"],
																"id_estado" => $row["id_estado"],
																"nombre_estado" => $row["nombre_estado"],
																"fecha_evaluacion" => $row["fecha_evaluacion"]);

				}

				$array_columnas = array();
				$traer_columnas = $this->Compromises_rca_model->get_fields_of_compliance_status($id_compromiso_rca)->result_array();

				foreach($traer_columnas as $columnas){
					$array_columnas[$columnas["id"]] = $columnas["nombre_evaluado"];
				}

				$result = array();
				foreach ($new_list_data as $id_valor_compromiso => $data) {
					$result[] = $this->_make_row_compliance_status_pdf(array($id_valor_compromiso => $data), $array_columnas);
				}

			}

			$columnas_evaluados_estados_cumplimiento = $this->Compromises_rca_model->get_fields_of_compliance_status($id_compromiso_rca)->result_array();

			$view_data["columnas_evaluados_estados_cumplimiento"] = $columnas_evaluados_estados_cumplimiento;
			$view_data["result"] = $result;
			//var_dump($result);
			//exit();
			/* FIN SECCIÓN ESTADOS DE CUMPLIMIENTO */

		}

		if($id_compromiso_reportables){

			$view_data["id_compromiso_reportables"] = $id_compromiso_reportables;

			/* SECCIÓN COMPROMISOS REPORTABLES */


			/*$compromisos_reportables =  $this->Compromises_reportables_model->get_reportable_compromises($id_compromiso_reportables)->result_array();

			$array_compromisos_reportables = array();
			foreach($compromisos_reportables as $cr){
				//$ultima_evaluacion = $this->Compromises_compliance_evaluation_model->get_last_evaluation(array("id_evaluado" => $cr["id_evaluado"], "id_valor_compromiso" => $cr["id_valor_compromiso"]))->result_array();
				//if($ultima_evaluacion[0]["id"] == $cr["id_evaluacion"]){
					$cr["sub_total"] = 1;
					$array_compromisos_reportables[] = $cr;
				//}
			}

			$result_acr = array();
			$cantidad_total_reportables = 0;
			foreach($array_compromisos_reportables as $acr){
				$repeat = false;
				for($i = 0; $i < count($result_acr); $i++){
					if($result_acr[$i]['id_estado'] == $acr['id_estado']){
						$result_acr[$i]['sub_total'] += $acr['sub_total'];
						$repeat = true;
						break;
					}
				}
				if($repeat == false){
					$result_acr[] = array('id_estado' => $acr['id_estado'], 'nombre_estado' => $acr['nombre_estado'], 'sub_total' => $acr['sub_total'], 'porcentaje' => 0, 'color' => $acr['color']);
				}
				$cantidad_total_reportables++;
			}

			$array_result_acr = array();
			foreach($result_acr as $result){

				$array_result_acr[] = array(
					'id_estado' => $result['id_estado'],
					'nombre_estado' => $result['nombre_estado'],
					'sub_total' => $result['sub_total'],
					'porcentaje' => ($result['sub_total'] * 100) / $cantidad_total_reportables,
					'color' => $result['color']
				);

			}

			$view_data["compromisos_reportables"] = $array_result_acr;*/


			// ESTADOS REPORTABLES
			$estados_cliente = $this->Compromises_compliance_status_model->get_details(array(
				"id_cliente" => $id_cliente,
				"tipo_evaluacion" => "reportable",
			))->result();

			// ULTIMAS EVALUACIONES
			$ultimas_evaluaciones = $this->Compromises_compliance_evaluation_reportables_model->get_last_evaluations_of_project(
				$id_proyecto,
				NULL,
				$start_date,
				$end_date
			)->result();

			// PROCESAR TABLA
			$array_estados_evaluados = array();
			$total_evaluado = 0;
			$array_compromisos_reportables_evaluaciones_no_cumple = array();
			$max_dates = array();
			foreach($estados_cliente as $estado) {

				$id_estado = $estado->id;
				if($estado->categoria == "No Aplica"){
					continue;
				}

				$array_estados_evaluados[$id_estado] = array(
					"nombre_estado" => $estado->nombre_estado,
					"categoria" => $estado->categoria,
					"color" => $estado->color,
					"planificaciones_evaluaciones" => array(),
					"cant" => 0,
				);

				$cant = 0;
				foreach($ultimas_evaluaciones as $ultima_evaluacion) {
					if($ultima_evaluacion->id_estados_cumplimiento_compromiso == $id_estado){

						$array_estados_evaluados[$id_estado]["planificaciones_evaluaciones"] = $ultima_evaluacion;
						$cant++;

						if($estado->categoria == "No Cumple"){
							$criticidad_info = $this->Critical_levels_model->get_one($ultima_evaluacion->id_criticidad);
							$ultima_evaluacion->criticidad = $criticidad_info->nombre;
							$id_valor_compromiso = $ultima_evaluacion->id_valor_compromiso;

							if(is_null($max_dates[$id_valor_compromiso])){
								$max_dates[$id_valor_compromiso] = $ultima_evaluacion->planificacion;
								$array_compromisos_reportables_evaluaciones_no_cumple[$id_valor_compromiso] = $ultima_evaluacion;
							}elseif(strtotime($max_dates[$id_valor_compromiso]) < strtotime($ultima_evaluacion->planificacion)){
								$max_dates[$id_valor_compromiso] = $ultima_evaluacion->planificacion;
								$array_compromisos_reportables_evaluaciones_no_cumple[$id_valor_compromiso] = $ultima_evaluacion;
							}

						}
					}
				}

				$array_estados_evaluados[$id_estado]["cant"] = $cant;
				$total_evaluado += $cant;

			}

			$view_data["compromisos_reportables"] = $array_estados_evaluados;
			$view_data["total_reportables"] = $total_evaluado;



			// Tabla Estados de Cumplimiento
			$id_proyecto = $this->session->project_context;
			$matriz = $this->Compromises_reportables_model->get_one_where(array(
				"id_proyecto" => $id_proyecto,
				"deleted" => 0
			));

			$list_data = $this->Values_compromises_reportables_model->get_details(array("id_compromiso" => $matriz->id))->result();

			$result_reportables = array();
			foreach ($list_data as $data) {
				$result_reportables[] = $this->_make_row_reportables_pdf($data);
			}
			$view_data["result_reportables"] = $result_reportables;
			// var_dump($result_reportables);exit;

			/* FIN SECCIÓN COMPROMISOS REPORTABLES */

		}

		if(!$id_compromiso_rca && !$id_compromiso_reportables){
			$view_data["nombre_proyecto"] = $info_proyecto->title;
		}

		// create new PDF document
        $this->load->library('Pdf');

		// set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Autor');
        $this->pdf->SetTitle($info_cliente->sigla."_".$info_proyecto->sigla."_".lang("compromises")."_".date('Y-m-d'));
        $this->pdf->SetSubject($info_cliente->sigla."_".$info_proyecto->sigla."_".lang("compromises")."_".date('Y-m-d'));
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
		$html = $this->load->view('compromises_compliance_client/pdf_view', $view_data, true);
		
		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		$this->pdf->writeHTML($html, true, false, true, false, '');

		$pdf_file_name = $info_cliente->sigla."_".$info_proyecto->sigla."_".lang("compromises")."_".date('Y-m-d').".pdf";

		$tmp = get_setting("temp_file_path");
		$this->pdf->Output(getcwd() . '/' . $tmp.$pdf_file_name, "F");

		echo $pdf_file_name;

	}

	private function _make_row_compliance_status_pdf($data, $array_columnas){
		//var_dump(key($data));
		$row_data = array();
		$row_data["numero_compromiso"] = $this->Values_compromises_rca_model->get_one(key($data))->numero_compromiso;
		$reportabilidad = $this->Values_compromises_rca_model->get_one(key($data))->reportabilidad;
		$row_data["reportabilidad"] = ($reportabilidad == 1) ? "&#xf00c;" : "&#xf00d;";
		$row_data["nombre_compromiso"] = $this->Values_compromises_rca_model->get_one(key($data))->nombre_compromiso;


		foreach($data as $key_evaluado => $array_evaluado){
			ksort($array_evaluado);
			if(count($array_columnas) != count($array_evaluado)){ //Si la cantidad de columnas es distinta a la cantidad de evaluados

				foreach($array_columnas as $id_evaluado => $columna){ //Loop sobre las columnas (Evaluado 1, Evaluado N)

					if(in_array($id_evaluado, $array_evaluado[$id_evaluado])){
						$distintos = false;
					} else {
						$distintos = true;
					}

					if($distintos){
						$row_data[$id_evaluado] = "-";
					} else {

						$id_estado_cumplimiento_compromiso = $array_evaluado[$id_evaluado]["id_estado"];
						$estado = $this->Compromises_compliance_status_model->get_one($id_estado_cumplimiento_compromiso);
						$nombre_estado = $estado->nombre_estado;
						$color_estado = $estado->color;

						$html_estado = '<span style="color:'.$color_estado.';">';
						$html_estado .= '&#xf111;'; // círculo (fontawesome)
						$html_estado .= '</span>';
						$html_estado .= "nombre_estado:".$nombre_estado;

						$row_data[$id_evaluado] = $html_estado;
					}

				}

			} else {

				foreach($array_evaluado as $evaluado){
					$id_estado_cumplimiento_compromiso = $evaluado["id_estado"];
					$estado = $this->Compromises_compliance_status_model->get_one($id_estado_cumplimiento_compromiso);
					$nombre_estado = $estado->nombre_estado;
					$color_estado = $estado->color;

					$html_estado = '<span style="color:'.$color_estado.';">';
					$html_estado .= '&#xf111;'; // círculo (fontawesome)
					$html_estado .= '</span>';
					$html_estado .= "nombre_estado:".$nombre_estado;

					$row_data[$evaluado["id_evaluado"]] = $html_estado;

				}

			}

		}




        return $row_data;

	}

	function borrar_temporal(){
		$uri = $this->input->post('uri');
		delete_file_from_directory($uri);
	}

	function list_data_reportables($id_compromiso_reportables, $start_date = null, $end_date = null){

		$environmental_topic = $this->input->post("environmental_topic");
		$environmental_management_instrument = $this->input->post("environmental_management_instrument");
		$compliance_type = $this->input->post("compliance_type");
		$impact_on_the_environment_due_to_non_compliance = $this->input->post("impact_on_the_environment_due_to_non_compliance"); 

		$id_proyecto = $this->session->project_context;
		$matriz = $this->Compromises_reportables_model->get_one_where(
			array(
				"id_proyecto" => $id_proyecto,
				"deleted" => 0
			)
		);
		$id_matriz = $matriz->id;

		if($id_matriz){
			$options = array(
				"id_compromiso" => $id_matriz,
				"tema_ambiental" => $environmental_topic,
				"tipo_cumplimiento" => $compliance_type,
				"instrumento_gestion_ambiental" => $environmental_management_instrument,
				"afectacion_medio_por_incumplimiento" => $impact_on_the_environment_due_to_non_compliance,
				"start_date" => $start_date,
				"end_date" => $end_date
			);

			$list_data = $this->Values_compromises_reportables_model->get_details($options)->result();
		}else{
			$list_data = array();
		}

		$result = array();
		foreach ($list_data as $data) {
            $result[] = $this->_make_row_reportables($data);
        }

		echo json_encode(array("data" => $result));

	}

	function _make_row_reportables($data = array()){

		$id_proyecto = $this->session->project_context;
		$nombre_compromiso = $data->nombre_compromiso;

		// TRAER INFORMACION DE LA ULTIMA PLANIFICACION, ES DECIR, EL DE LA FECHA DE PLANIFICACION MAS TARDÍA
		// COMO SE PUEDEN REPETIR LAS FECHAS, PUEDE HABER MAS DE 1
		$evaluaciones = $this->Plans_reportables_compromises_model->get_evaluations_of_compromise($data->id)->result();
		$ultima_fecha_modificacion = NULL;
		$id_evaluacion_fecha_modificacion = NULL;

		foreach($evaluaciones as $evaluacion){
			$id_planificacion = $evaluacion->id_planificacion;
			$fecha_modificacion = $evaluacion->modified;

			if(($fecha_modificacion) > $ultima_fecha_modificacion){
				$ultima_fecha_modificacion = $fecha_modificacion;
				$id_evaluacion_fecha_modificacion = $evaluacion->id;
			}
		}

		$ultima_fecha_plan = NULL;
		$id_ultimo_plan = NULL;
		if(!$id_evaluacion_fecha_modificacion){

			$planes = $this->Plans_reportables_compromises_model->get_all_where(
				array(
					"id_compromiso" => $data->id,
					"deleted" => 0
				)
			)->result();

			foreach($planes as $plan){
				$planificacion = $plan->planificacion;

				if(($planificacion) > $ultima_fecha_plan){
					$ultima_fecha_plan = $planificacion;
					$id_ultimo_plan = $plan->id;
				}
			}

			$evaluacion = $this->Compromises_compliance_evaluation_reportables_model->get_one_where(
				array(
					"id_valor_compromiso" => $data->id,
					"id_planificacion" => $id_ultimo_plan,
				)
			);

			$id_evaluacion_fecha_modificacion = $evaluacion->id;

		}

		$evaluacion_info = $this->Compromises_compliance_evaluation_reportables_model->get_one($id_evaluacion_fecha_modificacion);

		//var_dump($evaluacion_info);

		// TRAER ESTADO
		$estado = $this->Compromises_compliance_status_model->get_one($evaluacion_info->id_estados_cumplimiento_compromiso);
		$nombre_estado = $estado->nombre_estado;
		$color_estado = $estado->color;
		$html_estado = '<div class="text-center" style="text-align: -webkit-center;">';
		$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
		$html_estado .= $nombre_estado;
		$html_estado .= '</div>';

		// BOTON EVIDENCIAS
		$existen_evaluaciones_con_archivos = FALSE;
		$evaluaciones = $this->Compromises_compliance_evaluation_reportables_model->get_all_where(
			array(
				"id_valor_compromiso" => $data->id,
				"deleted" => 0,
			)
		)->result();

		foreach($evaluaciones as $evaluacion){
			$evidencias = $this->Compromises_compliance_evidences_model->get_all_where(
				array(
					"id_evaluacion_cumplimiento_compromiso" => $evaluacion->id,
					"tipo_evaluacion" => "reportable",
					"deleted" => 0
				)
			)->result_array();

			if($evidencias){
				$existen_evaluaciones_con_archivos = TRUE;
			}
		}
		if($existen_evaluaciones_con_archivos){
			$modal_evidencias = modal_anchor(get_uri("compromises_reportables_evaluation/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $evaluacion_info->id, "data-post-id_compromiso" => $data->id));
		} else {
			$modal_evidencias = "-";
		}

		// OBSERVACION
		$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.htmlspecialchars($evaluacion_info->observaciones, ENT_QUOTES).'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$observaciones = ((!$evaluacion_info->observaciones) || $evaluacion_info->observaciones == "") ? "-" : $tooltip_observaciones;

		// FECHA DE EVALUACION
		$fecha_evaluacion = ($evaluacion_info->fecha_evaluacion) ? get_date_format($evaluacion_info->fecha_evaluacion, $id_proyecto) : "-";

		// RESPONSABLE
		$responsable = $this->Users_model->get_one($evaluacion_info->responsable);
		$nombre_responsable = $responsable->first_name." ".$responsable->last_name;

		$tooltip_condicion_o_compromiso = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->condicion_o_compromiso.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$condicion_o_compromiso = ((!$data->condicion_o_compromiso) || $data->condicion_o_compromiso == "") ? "-" : $tooltip_condicion_o_compromiso;

		$tooltip_descripcion_compromiso = '<span class="help" data-container="body" data-toggle="tooltip" title="'.htmlspecialchars($data->descripcion_compromiso, ENT_QUOTES).'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$descripcion_compromiso = ((!$data->descripcion_compromiso) || $data->descripcion_compromiso == "") ? "-" : $tooltip_descripcion_compromiso;

		$areas_responsables = json_decode($data->area_responsable);
		$html_areas_responsables = '';
		foreach($areas_responsables as $area){
			$html_areas_responsables .= '&bull;&nbsp;' . lang($area) .'<br>';
		}

		$row_data = array(
			$data->numero_actividad,
			$data->instrumento_gestion_ambiental ? lang($data->instrumento_gestion_ambiental) : '-',
			$data->tipo_cumplimiento ? lang($data->tipo_cumplimiento) : '-',
			$data->tema_ambiental ? lang($data->tema_ambiental) : '-',
			$data->afectacion_medio_por_incumplimiento ? lang($data->afectacion_medio_por_incumplimiento) : '-',
			$descripcion_compromiso,
			$html_areas_responsables,
			$html_estado,
			$modal_evidencias,
			$observaciones,
			$nombre_responsable
		);

		return $row_data;

	}

	function get_excel_compliance_status_reportables($start_date = null, $end_date = null){

		$id_usuario = $this->session->user_id;
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;

		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);

		$matriz_compromisos = $this->Compromises_reportables_model->get_one_where(array(
			"id_proyecto" => $id_proyecto,
			"deleted" => 0
		));

		$id_compromiso = $matriz_compromisos->id;
		$list_data = $this->Values_compromises_reportables_model->get_details(array(
			"id_compromiso" => $id_compromiso,
			"start_date" => $start_date,
			"end_date" => $end_date
		))->result();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row_excel_compliance_status_reportables($data); // crear método
        }

		$this->load->library('excel');

		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle("")
							 ->setSubject("")
							 ->setDescription("")
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");

		if($client_info->color_sitio){
			$color_sitio = str_replace('#', '', $client_info->color_sitio);
		} else {
			$color_sitio = "00b393";
		}

		// ESTILOS
		$styleArray = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
			'fill' => array(
				'rotation' => 90,
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => $color_sitio)
			),
		);

		// LOGO
		if($client_info->id){
			if($client_info->logo){
				$url_logo = "files/mimasoft_files/client_".$client_info->id."/".$client_info->logo.".png";
			} else {
				$url_logo = "files/system/default-site-logo.png";
			}
		} else {
			$url_logo = "files/system/default-site-logo.png";
		}

		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo');
		$objDrawing->setDescription('Logo');
		$objDrawing->setPath('./'.$url_logo);
		$objDrawing->setHeight(35);
		$objDrawing->setOffsetY(6);
		$objDrawing->setOffsetX(20);
		$objDrawing->setWorksheet($doc->getActiveSheet());
		$doc->getActiveSheet()->mergeCells('A1:B3');
		$doc->getActiveSheet()->getStyle('A1:B3')->applyFromArray($styleArray);

		$nombre_columnas = array();
		$nombre_columnas[] = array("nombre_columna" => lang("n_activity"), "id_tipo_campo" => "n_activity");
		$nombre_columnas[] = array("nombre_columna" => lang("environmental_management_instrument"), "id_tipo_campo" => "environmental_management_instrument");
		$nombre_columnas[] = array("nombre_columna" => lang("compliance_type"), "id_tipo_campo" => "compliance_type");
		$nombre_columnas[] = array("nombre_columna" => lang("environmental_topic"), "id_tipo_campo" => "environmental_topic");
		$nombre_columnas[] = array("nombre_columna" => lang("impact_on_the_environment_due_to_non_compliance"), "id_tipo_campo" => "impact_on_the_environment_due_to_non_compliance");
		$nombre_columnas[] = array("nombre_columna" => lang("commitment_description"), "id_tipo_campo" => "commitment_description");
		$nombre_columnas[] = array("nombre_columna" => lang("responsible_area"), "id_tipo_campo" => "responsible_area");
		$nombre_columnas[] = array("nombre_columna" => lang("status"), "id_tipo_campo" => "status");
		$nombre_columnas[] = array("nombre_columna" => lang("observations"), "id_tipo_campo" => "observations");

		// HEADER
		$fecha = get_date_format(date('Y-m-d'), $id_proyecto);
		$hora = convert_to_general_settings_time_format($id_proyecto, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $id_proyecto));

		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', lang("compliance_status"))
			->setCellValue('C2', $project_info->title)
			->setCellValue('C3', lang("date").': '.$fecha.' '.lang("at").' '.$hora);

		$doc->setActiveSheetIndex(0);

		// SETEO DE CABECERAS DE CONTENIDO A LA HOJA DE EXCEL
		//$doc->getActiveSheet()->fromArray($nombre_columnas, NULL,"A5");
		$col = 0; // EMPEZANDO DE LA COLUMNA 'A'
		foreach($nombre_columnas as $index => $columna){
			$valor = (!is_array($columna)) ? $columna : $columna["nombre_columna"];
			$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row = 5, $valor);
			$col++;
		}

		// CARGA DE CONTENIDO A LA HOJA DE EXCEL
		$col = 0; // EMPEZANDO DE LA COLUMNA 'A'
		$row = 6; // EMPEZANDO DE LA FILA 6
		foreach($result as $res){

			foreach($nombre_columnas as $index_columnas => $columna){

				$name_col = PHPExcel_Cell::stringFromColumnIndex($col);
				$doc->getActiveSheet()->getColumnDimension($name_col)->setAutoSize(true);
				$valor = $res[$index_columnas];

				if(!is_array($columna)){

					$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);

				} else {
	
					if($columna["id_tipo_campo"] == "n_activity"){

						$doc->getActiveSheet()->setCellValueExplicitByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
						
					}elseif($columna["id_tipo_campo"] == "environmental_management_instrument"){

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					}elseif($columna["id_tipo_campo"] == "compliance_type"){

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == "environmental_topic"){

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					}elseif($columna["id_tipo_campo"] == "impact_on_the_environment_due_to_non_compliance"){

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == "commitment_description"){

						$doc->getActiveSheet()->getColumnDimension($name_col)->setAutoSize(false);
						$doc->getActiveSheet()->getColumnDimension($name_col)->setWidth(50);
						$doc->getActiveSheet()->getStyle($name_col.$row)->getAlignment()->setWrapText(true);

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == "responsible_area"){

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == "status"){

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == "observations"){

						$doc->getActiveSheet()->getColumnDimension($name_col)->setAutoSize(false);
						$doc->getActiveSheet()->getColumnDimension($name_col)->setWidth(50);
						$doc->getActiveSheet()->getStyle($name_col.$row)->getAlignment()->setWrapText(true);

						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} else {
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);

					}

				}

				//if($columna["id_tipo_campo"] != "unity"){
					$doc->getActiveSheet()->getStyle($name_col.$row)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
				//}
				$col++;
			}

			$col = 0;
			$row++;

		}
		//$doc->getActiveSheet()->fromArray($result, NULL,"A6");


		// FILTROS
		$doc->getActiveSheet()->setAutoFilter('A5:'.$letra.'5');

		// ANCHO COLUMNAS
		$lastColumn = $doc->getActiveSheet()->getHighestColumn();
		$lastColumn++;
		$cells = array();
		for($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;
		}
		/*foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}*/

		$nombre_hoja = strlen(lang("compromises_compliance_excel")) > 31 ? substr(lang("compromises_compliance_excel"), 0, 28).'...' : lang("compromises_compliance_excel");
		$nombre_hoja = $nombre_hoja ? $nombre_hoja : " ";
		$doc->getActiveSheet()->setTitle($nombre_hoja);

		$filename = $client_info->sigla."_".$project_info->sigla."_".lang("compromises_compliance_excel")."_".date('Y-m-d');
		$filename = $filename.'.xlsx'; //save our workbook as this file name

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache

		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');
		$objWriter->save('php://output');
		exit;

	}

	function _make_row_excel_compliance_status_reportables($data = array()){

		$id_proyecto = $this->session->project_context;
		$nombre_compromiso = $data->nombre_compromiso;

		// TRAER INFORMACION DE LA ULTIMA PLANIFICACION, ES DECIR, EL DE LA FECHA DE PLANIFICACION MAS TARDÍA
		// COMO SE PUEDEN REPETIR LAS FECHAS, PUEDE HABER MAS DE 1
		$evaluaciones = $this->Plans_reportables_compromises_model->get_evaluations_of_compromise($data->id)->result();
		$ultima_fecha_modificacion = NULL;
		$id_evaluacion_fecha_modificacion = NULL;

		foreach($evaluaciones as $evaluacion){
			$id_planificacion = $evaluacion->id_planificacion;
			$fecha_modificacion = $evaluacion->modified;

			if(($fecha_modificacion) > $ultima_fecha_modificacion){
				$ultima_fecha_modificacion = $fecha_modificacion;
				$id_evaluacion_fecha_modificacion = $evaluacion->id;
			}
		}

		$ultima_fecha_plan = NULL;
		$id_ultimo_plan = NULL;
		if(!$id_evaluacion_fecha_modificacion){

			$planes = $this->Plans_reportables_compromises_model->get_all_where(
				array(
					"id_compromiso" => $data->id,
					"deleted" => 0
				)
			)->result();

			foreach($planes as $plan){
				$planificacion = $plan->planificacion;

				if(($planificacion) > $ultima_fecha_plan){
					$ultima_fecha_plan = $planificacion;
					$id_ultimo_plan = $plan->id;
				}
			}

			$evaluacion = $this->Compromises_compliance_evaluation_reportables_model->get_one_where(
				array(
					"id_valor_compromiso" => $data->id,
					"id_planificacion" => $id_ultimo_plan,
				)
			);

			$id_evaluacion_fecha_modificacion = $evaluacion->id;

		}

		$evaluacion_info = $this->Compromises_compliance_evaluation_reportables_model->get_one($id_evaluacion_fecha_modificacion);

		//var_dump($evaluacion_info);

		// TRAER ESTADO
		$estado = $this->Compromises_compliance_status_model->get_one($evaluacion_info->id_estados_cumplimiento_compromiso);

		// OBSERVACION
		$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$evaluacion_info->observaciones.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$observaciones = ((!$evaluacion_info->observaciones) || $evaluacion_info->observaciones == "") ? "-" : $tooltip_observaciones;

		// FECHA DE EVALUACION
		$fecha_evaluacion = ($evaluacion_info->fecha_evaluacion) ? get_date_format($evaluacion_info->fecha_evaluacion, $id_proyecto) : "-";

		// RESPONSABLE
		//$responsable = $this->Users_model->get_one($evaluacion_info->responsable);
		//$nombre_responsable = $responsable->first_name." ".$responsable->last_name;

		$areas_responsables = json_decode($data->area_responsable);
		$lista_responsables = array();
		foreach($areas_responsables as $area){
			$lista_responsables[] = lang($area);
		}
		$string_responsables = implode(',',$lista_responsables);

		$row_data = array(
			$data->numero_actividad,
			lang($data->instrumento_gestion_ambiental),
			lang($data->tipo_cumplimiento),
			lang($data->tema_ambiental),
			lang($data->afectacion_medio_por_incumplimiento),
			$data->descripcion_compromiso,
			$string_responsables,
			($estado->nombre_estado) ? $estado->nombre_estado : "-",
			($evaluacion_info->observaciones) ? $evaluacion_info->observaciones : "-",
			//$nombre_responsable,
		);
		
		return $row_data;

	}

	function _make_row_reportables_pdf($data = array()){

		$id_proyecto = $this->session->project_context;
		$nombre_compromiso = $data->nombre_compromiso;

		// TRAER INFORMACION DE LA ULTIMA PLANIFICACION, ES DECIR, EL DE LA FECHA DE PLANIFICACION MAS TARDÍA
		// COMO SE PUEDEN REPETIR LAS FECHAS, PUEDE HABER MAS DE 1
		$evaluaciones = $this->Plans_reportables_compromises_model->get_evaluations_of_compromise($data->id)->result();
		$ultima_fecha_modificacion = NULL;
		$id_evaluacion_fecha_modificacion = NULL;

		foreach($evaluaciones as $evaluacion){
			$id_planificacion = $evaluacion->id_planificacion;
			$fecha_modificacion = $evaluacion->modified;

			if(($fecha_modificacion) > $ultima_fecha_modificacion){
				$ultima_fecha_modificacion = $fecha_modificacion;
				$id_evaluacion_fecha_modificacion = $evaluacion->id;
			}
		}

		$ultima_fecha_plan = NULL;
		$id_ultimo_plan = NULL;
		if(!$id_evaluacion_fecha_modificacion){

			$planes = $this->Plans_reportables_compromises_model->get_all_where(
				array(
					"id_compromiso" => $data->id,
					"deleted" => 0
				)
			)->result();

			foreach($planes as $plan){
				$planificacion = $plan->planificacion;

				if(($planificacion) > $ultima_fecha_plan){
					$ultima_fecha_plan = $planificacion;
					$id_ultimo_plan = $plan->id;
				}
			}

			$evaluacion = $this->Compromises_compliance_evaluation_reportables_model->get_one_where(
				array(
					"id_valor_compromiso" => $data->id,
					"id_planificacion" => $id_ultimo_plan,
				)
			);

			$id_evaluacion_fecha_modificacion = $evaluacion->id;

		}

		$evaluacion_info = $this->Compromises_compliance_evaluation_reportables_model->get_one($id_evaluacion_fecha_modificacion);

		//var_dump($evaluacion_info);

		// TRAER ESTADO
		$estado = $this->Compromises_compliance_status_model->get_one($evaluacion_info->id_estados_cumplimiento_compromiso);
		$nombre_estado = $estado->nombre_estado;
		$color_estado = $estado->color;

		$html_estado = '<span style="color:'.$color_estado.';">';
		$html_estado .= '&#xf111;'; // círculo (fontawesome)
		$html_estado .= '</span>';
		$html_estado .= "nombre_estado:".$nombre_estado;

		// BOTON EVIDENCIAS
		/*$existen_evaluaciones_con_archivos = FALSE;
		$evaluaciones = $this->Compromises_compliance_evaluation_reportables_model->get_all_where(
			array(
				"id_valor_compromiso" => $data->id,
				"deleted" => 0,
			)
		)->result();

		foreach($evaluaciones as $evaluacion){
			$evidencias = $this->Compromises_compliance_evidences_model->get_all_where(
				array(
					"id_evaluacion_cumplimiento_compromiso" => $evaluacion->id,
					"tipo_evaluacion" => "reportable",
					"deleted" => 0
				)
			)->result_array();

			if($evidencias){
				$existen_evaluaciones_con_archivos = TRUE;
			}
		}
		if($existen_evaluaciones_con_archivos){
			$modal_evidencias = modal_anchor(get_uri("compromises_reportables_evaluation/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $evaluacion_info->id, "data-post-id_compromiso" => $data->id));
		} else {
			$modal_evidencias = "-";
		} */

		// OBSERVACION
		/* $tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$evaluacion_info->observaciones.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$observaciones = ((!$evaluacion_info->observaciones) || $evaluacion_info->observaciones == "") ? "-" : $tooltip_observaciones;
 */
		$areas_responsables = json_decode($data->area_responsable);
		$html_areas_responsables = '';
		foreach($areas_responsables as $area){
			$html_areas_responsables .= '&bull;&nbsp;' . lang($area) .'<br>';
		}

		$row_data["n_activity"] = htmlspecialchars($data->numero_actividad, ENT_QUOTES);
		$row_data["environmental_management_instrument"] = lang($data->instrumento_gestion_ambiental);
		$row_data["compliance_type"] = lang($data->tipo_cumplimiento);
		$row_data["environmental_topic"] = lang($data->tema_ambiental);
		$row_data["impact_on_the_environment_due_to_non_compliance"] = lang($data->afectacion_medio_por_incumplimiento);
		$row_data["commitment_description"] = htmlspecialchars($data->descripcion_compromiso, ENT_QUOTES);
		$row_data["area_responsible"] = $html_areas_responsables;
		$row_data["html_estado"] = $html_estado;
		$row_data["observaciones"] = ($evaluacion_info->observaciones) ? htmlspecialchars($evaluacion_info->observaciones, ENT_QUOTES) : "-";


		return $row_data;

	}

	// Muestra el mismo contenido que al entrar al módulo pero con los datos filtrados por rango de fechas.
	function get_compliance_details(){
		
		$id_cliente = $this->input->post("id_cliente") ? $this->input->post("id_cliente") : $this->login_user->client_id;
		$id_proyecto = $this->input->post("id_proyecto") ? $this->input->post("id_proyecto") : $this->session->project_context;
		$start_date = $this->input->post("start_date");
		$end_date = $this->input->post("end_date");

		$view_data = array();
		$view_data["start_date"] = $start_date;
		$view_data["end_date"] = $end_date;

		$id_compromiso_rca = $this->Compromises_rca_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
		$id_compromiso_reportables = $this->Compromises_reportables_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;

		$cliente = $this->Clients_model->get_one($id_cliente);
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$view_data["project_info"] = $proyecto;
		$view_data["nombre_proyecto"] = $proyecto->title;

		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");

		if($id_compromiso_rca){

			$view_data["id_cliente"] = $id_cliente;
			$view_data["id_compromiso_rca"] = $id_compromiso_rca;
			$view_data["id_compromiso_reportables"] = $id_compromiso_reportables;
			$view_data["id_proyecto"] = $id_proyecto;
			$view_data["Compromises_compliance_client_controller"] = $this;

			/* SECCIÓN RESUMEN POR EVALUADO */

			// COMPROMISOS AMBIENTALES - RCA

			// EVALUADOS
			$evaluados = $this->Evaluated_rca_compromises_model->get_all_where(array(
				"id_compromiso" => $id_compromiso_rca,
				"deleted" => 0
			))->result();

			// ESTADOS RCA
			$estados_cliente = $this->Compromises_compliance_status_model->get_details(array(
				"id_cliente" => $id_cliente,
				"tipo_evaluacion" => "rca",
			))->result();

			// ULTIMAS EVALUACIONES
			$ultimas_evaluaciones = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluations_of_project($id_proyecto)->result();

			// PROCESAR TABLA
			$array_estados_evaluados = array();
			$array_evaluados_estados = array();
			$array_total_por_evaluado = array();
			$array_total_por_estado = array();
			$array_compromisos_evaluaciones_no_cumple = array();
			$total = 0;

			foreach($estados_cliente as $estado) {

				$id_estado = $estado->id;

				if($estado->categoria == "No Aplica"){
					continue;
				}
				$array_estados_evaluados[$estado->id] = array(
					"nombre_estado" => $estado->nombre_estado,
					"categoria" => $estado->categoria,
					"color" => $estado->color,
					"evaluados" => array(),
					"cantidad_categoria" => 0,
				);

				$cant_estado = 0;
				foreach($evaluados as $evaluado) {

					$id_evaluado = $evaluado->id;
					$cant = 0;

					$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado] = array("cant" => 0, "evaluaciones" => array());

					foreach($ultimas_evaluaciones as $ultima_evaluacion) {
						if(
							$ultima_evaluacion->id_estados_cumplimiento_compromiso == $id_estado &&
							$ultima_evaluacion->id_evaluado == $id_evaluado
						){
							$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["evaluaciones"][] = $ultima_evaluacion;
							$array_evaluados_estados[$id_evaluado][$id_estado][] = 1;
							$cant++;
							$cant_estado++;

							if($estado->categoria == "No Cumple"){
								$criticidad_info = $this->Critical_levels_model->get_one($ultima_evaluacion->id_criticidad);
								$ultima_evaluacion->criticidad = $criticidad_info->nombre;
								$array_compromisos_evaluaciones_no_cumple[] = $ultima_evaluacion;
							}
						}
					}

					$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["cant"] = $cant;
					$array_total_por_evaluado[$id_evaluado][] = $cant;
					$array_total_por_estado[$id_estado][] = $cant;
				}

				$array_estados_evaluados[$id_estado]["cantidad_categoria"] = $cant_estado;
				$total += $cant_estado;
			}

			$view_data["evaluados_rca"] = $evaluados;
			$view_data["total_compromisos_aplicables_rca"] = $total;
			$view_data["total_cantidades_estados_evaluados_rca"] = $array_estados_evaluados;
			$view_data["total_cantidades_evaluados_estados_rca"] = $array_evaluados_estados;
			$view_data["array_total_por_evaluado_rca"] = $array_total_por_evaluado;

			/* FIN SECCIÓN RESUMEN POR EVALUADO */


			/* SECCIÓN ESTADOS DE CUMPLIMIENTO */

			$json_string_columnas = ',{"title":"' . lang("name") .'", "class": "text-left dt-head-center"}';
			$traer_columnas = $this->Compromises_rca_model->get_fields_of_compliance_status($id_compromiso_rca)->result_array();

			foreach($traer_columnas as $columnas){
				$json_string_columnas .= ',{"title":"' .$columnas["nombre_evaluado"] . '", "class": "text-center dt-head-center no_breakline", render: function (data, type, row) {return "<center>"+data+"</center>";}}';
			}

			$json_string_columnas .= ',{"title":"' . lang("evidence") .'", "class":"text-center option"}';
			$json_string_columnas .= ',{"title":"' . lang("observations") .'", "class":"text-center option"}';
			$view_data["columnas"] = $json_string_columnas;

			// Filtro Reportabilidad
			$array_reportabilidad[] = array("id" => "", "text" => "- ".lang("reportability")." -");
			$array_reportabilidad[] = array("id" => "si", "text" => lang("yes"));
			$array_reportabilidad[] = array("id" => "no", "text" => lang("no"));
			$view_data['reportabilidad_dropdown'] = json_encode($array_reportabilidad);

			/* FIN SECCIÓN ESTADOS DE CUMPLIMIENTO */
		}

		if($id_compromiso_reportables){
			/* SECCIÓN COMPROMISOS REPORTABLES */

			$view_data["id_compromiso_reportables"] = $id_compromiso_reportables;
			$view_data["id_cliente"] = $id_cliente;
			$view_data["id_proyecto"] = $id_proyecto;

			// ESTADOS REPORTABLES
			$estados_cliente = $this->Compromises_compliance_status_model->get_details(array(
				"id_cliente" => $id_cliente,
				"tipo_evaluacion" => "reportable",
			))->result();

			// ULTIMAS EVALUACIONES
			$ultimas_evaluaciones = $this->Compromises_compliance_evaluation_reportables_model->get_last_evaluations_of_project(
				$id_proyecto,
				NULL,
				$start_date,
				$end_date
			)->result();

			// PROCESAR TABLA
			$array_estados_evaluados = array();
			$total_evaluado = 0;
			$array_compromisos_reportables_evaluaciones_no_cumple = array();
			$max_dates = array();
			foreach($estados_cliente as $estado) {

				$id_estado = $estado->id;
				if($estado->categoria == "No Aplica"){
					continue;
				}

				$array_estados_evaluados[$id_estado] = array(
					"nombre_estado" => $estado->nombre_estado,
					"categoria" => $estado->categoria,
					"color" => $estado->color,
					"planificaciones_evaluaciones" => array(),
					"cant" => 0,
				);

				$cant = 0;
				foreach($ultimas_evaluaciones as $ultima_evaluacion) {
					if($ultima_evaluacion->id_estados_cumplimiento_compromiso == $id_estado){

						$array_estados_evaluados[$id_estado]["planificaciones_evaluaciones"] = $ultima_evaluacion;
						$cant++;

						if($estado->categoria == "No Cumple"){
							$criticidad_info = $this->Critical_levels_model->get_one($ultima_evaluacion->id_criticidad);
							$ultima_evaluacion->criticidad = $criticidad_info->nombre;
							$id_valor_compromiso = $ultima_evaluacion->id_valor_compromiso;

							if(is_null($max_dates[$id_valor_compromiso])){
								$max_dates[$id_valor_compromiso] = $ultima_evaluacion->planificacion;
								$array_compromisos_reportables_evaluaciones_no_cumple[$id_valor_compromiso] = $ultima_evaluacion;
							}elseif(strtotime($max_dates[$id_valor_compromiso]) < strtotime($ultima_evaluacion->planificacion)){
								$max_dates[$id_valor_compromiso] = $ultima_evaluacion->planificacion;
								$array_compromisos_reportables_evaluaciones_no_cumple[$id_valor_compromiso] = $ultima_evaluacion;
							}

						}
					}
				}

				$array_estados_evaluados[$id_estado]["cant"] = $cant;
				$total_evaluado += $cant;

			}
			
			$view_data["compromisos_reportables"] = $array_estados_evaluados;
			$view_data["total_reportables"] = $total_evaluado;

			// GRAFICO RESUMEN DE CUMPLIMIENTO
			$array_grafico_reportables = array();
			foreach($array_estados_evaluados as $id_estado => $array_estado){
				$array_grafico_reportables[] = array(
					'nombre_estado' => $array_estado["nombre_estado"],
					'porcentaje' => $total_evaluado == 0?0:(($array_estado["cant"] * 100) / ($total_evaluado)),
					'color' => $array_estado["color"]
				);
			}

			$view_data["grafico_reportables"] = $array_grafico_reportables;

			/*$compromisos_reportables = $this->Compromises_reportables_model->get_reportable_compromises($id_compromiso_reportables)->result_array();

			$array_compromisos_reportables = array();
			foreach($compromisos_reportables as $cr){
				$cr["sub_total"] = 1;
				$array_compromisos_reportables[] = $cr;
			}

			$result_acr = array();
			$cantidad_total_reportables = 0;
			foreach($array_compromisos_reportables as $acr){
				$repeat = false;
				for($i = 0; $i < count($result_acr); $i++){
					if($result_acr[$i]['id_estado'] == $acr['id_estado']){
						$result_acr[$i]['sub_total'] += $acr['sub_total'];
						$repeat = true;
						break;
					}
				}
				if($repeat == false){
					$result_acr[] = array('id_estado' => $acr['id_estado'], 'nombre_estado' => $acr['nombre_estado'], 'sub_total' => $acr['sub_total'], 'porcentaje' => 0, 'color' => $acr['color']);
				}
				$cantidad_total_reportables++;
			}

			$array_result_acr = array();
			foreach($result_acr as $result){

				$array_result_acr[] = array(
					'id_estado' => $result['id_estado'],
					'nombre_estado' => $result['nombre_estado'],
					'sub_total' => $result['sub_total'],
					'porcentaje' => ($result['sub_total'] * 100) / $cantidad_total_reportables,
					'color' => $result['color']
				);

			}

			$view_data["compromisos_reportables"] = $array_result_acr;*/

			/* FIN SECCIÓN COMPROMISOS REPORTABLES */


			/* SECCIÓN ESTADOS DE CUMPLIMIENTO REPORTABLES */

			// FILTRO TIPO DE CUMPLIMIENTO
			$array_compliance_types[] = array("id" => "", "text" => "- ".lang("compliance_type")." -");
			$array_compliance_types[] = array("id" => "environmental_adaptation_actions", "text" => lang("environmental_adaptation_actions"));
			$array_compliance_types[] = array("id" => "mitigation_measures_in_existing_components", "text" => lang("mitigation_measures_in_existing_components"));
			$array_compliance_types[] = array("id" => "prevention_control_and_mitigation_measures_in_projected_components", "text" => lang("prevention_control_and_mitigation_measures_in_projected_components"));
			$array_compliance_types[] = array("id" => "measures_for_the_prevention_control_and_mitigation_of_environmental_impacts", "text" => lang("measures_for_the_prevention_control_and_mitigation_of_environmental_impacts"));
			$array_compliance_types[] = array("id" => "closure_or_abandonment_plan", "text" => lang("closure_or_abandonment_plan"));
			$array_compliance_types[] = array("id" => "contingency_plan", "text" => lang("contingency_plan"));
			$array_compliance_types[] = array("id" => "solid_waste_management_plan", "text" => lang("solid_waste_management_plan"));
			$array_compliance_types[] = array("id" => "main_environmental_obligations", "text" => lang("main_environmental_obligations"));
			$array_compliance_types[] = array("id" => "environmental_education_program", "text" => lang("environmental_education_program"));
			$array_compliance_types[] = array("id" => "environmental_monitoring_program", "text" => lang("environmental_monitoring_program"));
			$array_compliance_types[] = array("id" => "citizen_participation_program", "text" => lang("citizen_participation_program"));
			$array_compliance_types[] = array("id" => "environmental_signage_program", "text" => lang("environmental_signage_program"));
			$array_compliance_types[] = array("id" => "preventive_or_corrective_program", "text" => lang("preventive_or_corrective_program"));
			$view_data["compliance_types_dropdown"] = json_encode($array_compliance_types);

			// FILTRO TEMA AMBIENTAL
			$array_environmental_topic[] = array("id" => "", "text" => "- ".lang("environmental_topic")." -");
			$array_environmental_topic[] = array("id" => "water", "text" => lang("water"));
			$array_environmental_topic[] = array("id" => "air", "text" => lang("air"));
			$array_environmental_topic[] = array("id" => "air/noise", "text" => lang("air/noise"));
			$array_environmental_topic[] = array("id" => "control_of_agricultural_inputs", "text" => lang("control_of_agricultural_inputs"));
			$array_environmental_topic[] = array("id" => "crops", "text" => lang("crops"));
			$array_environmental_topic[] = array("id" => "environmental_education", "text" => lang("environmental_education"));
			$array_environmental_topic[] = array("id" => "industrial_and_domestic_effluents", "text" => lang("industrial_and_domestic_effluents"));
			$array_environmental_topic[] = array("id" => "social_environment", "text" => lang("social_environment"));
			$array_environmental_topic[] = array("id" => "flora_and_fauna", "text" => lang("flora_and_fauna"));
			$array_environmental_topic[] = array("id" => "non_hazardous_waste", "text" => lang("non_hazardous_waste"));
			$array_environmental_topic[] = array("id" => "hazardous_waste", "text" => lang("hazardous_waste"));
			$array_environmental_topic[] = array("id" => "hazardous_and_non_hazardous_waste", "text" => lang("hazardous_and_non_hazardous_waste"));
			$array_environmental_topic[] = array("id" => "security_and_health_at_work", "text" => lang("security_and_health_at_work"));
			$array_environmental_topic[] = array("id" => "ground", "text" => lang("ground"));
			$array_environmental_topic[] = array("id" => "other", "text" => lang("other"));
			$view_data["environmental_topic_dropdown"] = json_encode($array_environmental_topic);

			// FILTRO INSTRUMENTO DE GESTIÓN AMBIENTAL
			$array_environmental_management_instrument[] = array("id" => "", "text" => "- ".lang("environmental_management_instrument")." -");
			$array_environmental_management_instrument[] = array("id" => "mpama", "text" => lang("mpama"));
			$array_environmental_management_instrument[] = array("id" => "pama", "text" => lang("pama"));
			$array_environmental_management_instrument[] = array("id" => "pama_and_mpama", "text" => lang("pama_and_mpama"));
			$array_environmental_management_instrument[] = array("id" => "dia", "text" => lang("dia"));
			$array_environmental_management_instrument[] = array("id" => "mdia", "text" => lang("mdia"));
			$array_environmental_management_instrument[] = array("id" => "dia_and_mdia", "text" => lang("dia_and_mdia"));
			$array_environmental_management_instrument[] = array("id" => "n/a", "text" => lang("n/a"));
			$view_data["environmental_management_instrument_dropdown"] = json_encode($array_environmental_management_instrument);

			// FILTRO AFECTACIÓN AL MEDIO POR INCUMPLIMIENTO
			$array_impact_on_the_environment[] = array("id" => "", "text" => "- ".lang("impact_on_the_environment_due_to_non_compliance")." -");
			$array_impact_on_the_environment[] = array("id" => "water", "text" => lang("water"));
			$array_impact_on_the_environment[] = array("id" => "air_and_noise", "text" => lang("air_and_noise"));
			$array_impact_on_the_environment[] = array("id" => "community/social_environment", "text" => lang("community/social_environment"));
			$array_impact_on_the_environment[] = array("id" => "flora_and_fauna", "text" => lang("flora_and_fauna"));
			$array_impact_on_the_environment[] = array("id" => "solid_waste", "text" => lang("solid_waste"));
			$array_impact_on_the_environment[] = array("id" => "hazardous_waste", "text" => lang("hazardous_waste"));
			$array_impact_on_the_environment[] = array("id" => "non_hazardous_waste", "text" => lang("non_hazardous_waste"));
			$array_impact_on_the_environment[] = array("id" => "health", "text" => lang("health"));
			$array_impact_on_the_environment[] = array("id" => "ground", "text" => lang("ground"));
			$array_impact_on_the_environment[] = array("id" => "physical_environment", "text" => lang("physical_environment"));
			$array_impact_on_the_environment[] = array("id" => "physical_and_socioeconomic_environment", "text" => lang("physical_and_socioeconomic_environment"));
			$view_data["impact_on_the_environment_dropdown"] = json_encode($array_impact_on_the_environment);
		
			/* FIN SECCIÓN ESTADOS DE CUMPLIMIENTO REPORTABLES */

			$array_estados_cumplimiento = array(
				'Cumple',
				'No Cumple',
				'Pendiente',
				'No Aplica'
			);
			
			// Se obtienen los colores asociados a cada estado de cumplimiento creado para el cliente
			$options = array('id_cliente' => $id_cliente, 'tipo_evaluacion' => 'reportable');
			$result_colores_por_estado = $this->Compromises_compliance_evaluation_reportables_model->get_colors_by_client($options)->result();
			$colores_por_estado = array();
			foreach($result_colores_por_estado as $color){
				$colores_por_estado[$color->categoria] = $color->color;
			}

			/* SECCIÓN RESUMEN POR IGA */
			$array_instrumento_gestion_ambiental = array(
				lang("mpama"),
				lang("pama"),
				lang("pama_and_mpama"),
				lang("dia"),
				lang("mdia"),
				lang("dia_and_mdia"),
				lang("n/a")
			);

			$columna_instrumento_gestion_ambiental = 'instrumento_gestion_ambiental';
			$data_summary_by_iga = $this->get_data_summary_chart(
				$id_compromiso_reportables, 
				$array_instrumento_gestion_ambiental, 
				$columna_instrumento_gestion_ambiental, 
				$estados_cliente,
				$start_date,
				$end_date
			);

			$view_data['grafico_resumen_por_iga'] = $data_summary_by_iga;
			$view_data['array_instrumento_gestion_ambiental'] = $array_instrumento_gestion_ambiental;
			/* FIN SECCIÓN RESUMEN POR IGA */

			/* SECCIÓN RESUMEN POR TIPO DE CUMPLIMIENTO */
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
			
			$columna_compliance_types = 'tipo_cumplimiento';
			$data_summary_by_compliance_type = $this->get_data_summary_chart(
				$id_compromiso_reportables, 
				$array_compliance_types, 
				$columna_compliance_types, 
				$estados_cliente,
				$start_date,
				$end_date
			);

			$view_data['grafico_resumen_por_tipo_cumplimiento'] = $data_summary_by_compliance_type;
			$view_data['array_compliance_types'] = $array_compliance_types;
			/* FIN SECCIÓN RESUMEN POR TIPO DE CUMPLIMIENTO */

			
			/* SECCIÓN RESUMEN POR TEMA AMBIENTAL */
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
				lang("other")
			);
			
			$columna_environmental_topic = 'tema_ambiental';
			$data_summary_by_environmental_topic = $this->get_data_summary_chart(
				$id_compromiso_reportables, 
				$array_environmental_topic, 
				$columna_environmental_topic, 
				$estados_cliente,
				$start_date,
				$end_date
			);

			$view_data['grafico_resumen_por_tema_ambiental'] = $data_summary_by_environmental_topic;
			$view_data['array_environmental_topic'] = $array_environmental_topic; 
			/* FIN SECCIÓN RESUMEN POR TEMA AMBIENTAL */

			/* SECCIÓN RESUMEN POR ÁREA RESPONSABLE */
			$array_responsible_area = array(
				lang("personal_administration"),
				lang("warehouse"),
				lang("quality"),
				// lang("field"),
				lang("training"),
				// lang("communications"),
				lang("crops"),
				// lang("management"),
				lang("icp"),
				lang("maintenance"),
				lang("machinery"),
				lang("operations"),
				// lang("packing"),
				// lang("landscaping"),
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
			
			
			$columna_area_responsable = 'area_responsable';

			$data_summary_by_responsible_area = $this->get_data_summary_categories_area_responsible($id_compromiso_reportables, $array_responsible_area, $columna_area_responsable, $estados_cliente, $start_date, $end_date);

			$view_data['grafico_resumen_por_area_responsable'] = $data_summary_by_responsible_area;
			$view_data['colores_por_estado'] = $colores_por_estado;

			$view_data['array_responsible_area'] = $array_responsible_area; 
			/* FIN SECCIÓN RESUMEN POR ÁREA RESPONSABLE */


		}


		if(!$id_compromiso_rca && !$id_compromiso_reportables){
			$proyecto = $this->Projects_model->get_one($id_proyecto);
			$view_data["nombre_proyecto"] = $proyecto->title;
		}

		$view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));

		// PARA NOMBRE DE ARCHIVOS EXPORTABLES
		$view_data['sigla_cliente'] = $cliente->sigla;
		$view_data['sigla_proyecto'] = $proyecto->sigla;

		echo $this->load->view("compromises_compliance_client/compliance_details_by_date", $view_data, TRUE);

	}

}

