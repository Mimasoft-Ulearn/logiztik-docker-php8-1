<?php
set_time_limit(200);

//ini_set('LimitRequestBody', 100024000);
//ini_set('post_max_size', 100024000);
//ini_set('upload_max_filesize', 100024000);
/*if (!defined('BASEPATH'))
    exit('No direct script access allowed');*/
    //header('Access-Control-Allow-Origin: *');

require(APPPATH.'/libraries/REST_Controller.php');

class Api extends REST_Controller {

	private $api_token;

    public function __construct() {
        parent::__construct();

		$this->api_token = $this->Settings_model->get_setting("api_token");
        //check permission to access this module
        //$this->init_permission_checker("client");
    }

    /* load clients list view */

    public function index() {
        /*$this->access_only_allowed_members();
        $this->template->rander("clients/index");*/
	}
	
	// ENDPOINT LOGIN - POST
    public function login_post() {
        $user = $this->post('email');
        $password = $this->post('password');

		$valido = $this->Users_model->authenticate($user, $password);
		$existing_user = $this->Users_model->is_email_exists($user);
		
		if($valido){
			$token = $this->generate_token();
			$respuesta = array("token" => $token, "message" => "Se ha logueado Correctamente", "success" => true, "user_id" => $existing_user->id);
			$users_api_session_row = array("user_id" => $existing_user->id, "token" => $token, "login_date" => get_current_utc_time());
			$save_id = $this->Api_model->save($users_api_session_row);
		}else{
			$respuesta = array("message" => "Fallo de autenticidad!", "success" => false);
		}

        return $this->response($respuesta);
	}


	public function get_form_get(){

		$this->_token_validation($this->input->get_request_header("Authorization", true));

		$id_form = $this->input->get('id_form');
        $first_date = $this->input->get('first_date');
        $last_date = $this->input->get('last_date');

		// VALIDACIÓN DE CAMPOS
		if(!$id_form){
			$array_errors[] = "El campo id_form es obligatorio.";
		} else {
			$form_info = $this->Forms_model->get_one($id_form);
			if(!$form_info->id){
				$array_errors[] = "El Formulario con id $id_form no existe";
			}
		}

		// TRAER EL PROYECTO ASOCIADO AL FORMULARIO
		$id_project = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $form_info->id, "deleted" => 0))->id_proyecto; 
		$project = $this->Projects_model->get_one($id_project);
		$array_project = array("id" => $project->id, "name" => $project->title);

		$form_type = $this->Form_types_model->get_one($form_info->id_tipo_formulario);
		$form_type_label = ($form_info->id_tipo_formulario == 1) ? $form_type->nombre." (".$form_info->flujo.")" : $form_type->nombre;
			
		$response = array();

		if($form_info->id_tipo_formulario == 1){ // REGISTRO AMBIENTAL
			
			if($first_date && $last_date){

				// VALIDA QUE LAS FECHAS VENGAN EN FORMATO CORRECTO
				if( $this->validate_date($first_date) && $this->validate_date($last_date) ){

					// VALIDA QUE LA FECHA DE INICIO SEA MENOR O IGUAL A LA DE TÉRMINO
					if( $this->validate_date_range($first_date, $last_date)){
						$options = array("id_formulario" => $form_info->id, "first_date" => $first_date, "last_date" => $last_date);
					} else {
						$array_errors[] = "La fecha de inicio debe ser menor o igual a la fecha de término";
					}

				} else {
					$array_errors[] = "El formato de fechas debe ser YYYY-MM-DD";
				}

				// SI NO HAY ERRORES DE VALIDACIÓN
				if(count($array_errors)){
					$this->response(array("success" => false, "errors" => $array_errors));
					exit();
				}
	
			} else if( ($first_date && !$last_date) || (!$first_date && $last_date) ){
				$array_errors[] = "Para consultar los datos por rango de fechas, se debe incluir la fecha de inicio y la de término";
			} else {
				$options = array("id_formulario" => $form_info->id);
			}

			$valores_de_formulario = $this->Api_model->get_values_of_form($options)->result();

			// CONTAR REGISTROS DEL FORMULARIO
			// $n_records = $this->Form_values_model->get_forms_values_of_form($id_form)->num_rows();
			$n_records = (string)count($valores_de_formulario);
			// $n_records_label = ($n_records == 1) ? "1 Registro" : $n_records." Registros";
			
			if($form_info->flujo == "Residuo"){


				foreach($valores_de_formulario as $valor){

					$array_valores = array();
					$array_valores["id"] = $valor->id;
					$datos_decoded = json_decode($valor->datos);

					foreach($datos_decoded as $id => $value){
						
						if($id == "fecha"){
							$array_valores["storage_date"] = $value;
						} elseif($id == "month"){
							$array_valores["month"] = (string)$value;
						} elseif($id == "id_categoria"){
							$category = $this->Categories_model->get_one($value);
							$array_valores["category"] = ($category->id) ? array(
								"id" => $category->id,
								"name" => $category->nombre
							) : null;
						} elseif($id == "id_sucursal"){
							$subproject = $this->Subprojects_model->get_one($value);
							$array_valores["branch_office"] = ($subproject->id) ? array(
								"id" => $subproject->id,
								"name" => $subproject->nombre
							) : null;
						} elseif($id == "unidad_residuo"){
							$array_valores["quantity"] = (string)$value;
						} elseif($id == "tipo_tratamiento"){
							$tipo_tratamiento = $this->Tipo_tratamiento_model->get_one($value);
							$array_valores["type_of_treatment"] = ($tipo_tratamiento->id) ? array(
								"id" => $tipo_tratamiento->id,
								"name" => $tipo_tratamiento->nombre
							) : null;
						} elseif($id == "carrier_rut"){
							$array_valores["carrier_rut"] = $value;
						} elseif($id == "id_patent"){
							$patentes = $this->Patents_model->get_one($value);
							$array_valores["patent"] = ($patentes->id) ? array(
								"id" => $patentes->id,
								"name" => $patentes->patent
							) : null;
						} elseif($id == "id_waste_transport_company"){
							$waste_transport_company = $this->Fixed_feeder_waste_transport_companies_values_model->get_one($value);
							$array_valores["waste_transport_company"] = ($waste_transport_company->id) ? array(
								"id" => $waste_transport_company->id,
								"name" => $waste_transport_company->company_name
							) : null;
						} elseif($id == "id_waste_receiving_company"){
							$waste_receiving_company = $this->Fixed_feeder_waste_receiving_companies_values_model->get_one($value);
							$array_valores["waste_receiving_company"] = ($waste_receiving_company->id) ? array(
								"id" => $waste_receiving_company->id,
								"name" => $waste_receiving_company->company_name
							) : null;
						} elseif($id == "fecha_retiro"){
							$array_valores["retirement_date"] = $value;
						} elseif($id == "nombre_archivo_retiro"){
							$array_valores['retirement_evidence'] = $value?get_uri("api/download_file/".$valor->id."/nombre_archivo_retiro"):$value;
						} elseif($id == "nombre_archivo_recepcion"){
							$array_valores["reception_evidence"] = $value?get_uri("api/download_file/".$valor->id."/nombre_archivo_recepcion"):$value;
						} elseif($id == "nombre_archivo_waste_manifest"){
							$array_valores["waste_manifest"] = $value?get_uri("api/download_file/".$valor->id."/nombre_archivo_waste_manifest"):$value;							
						} elseif($id == "tipo_unidad"){
							
						} elseif($id == "unidad"){
							
						} else{
							
							// COMPROBAR SI EL CAMPO DEL BUCLE ES DE TIPO ARCHIVO, HTML O DIVIDER
							$campo_info = $this->Fields_model->get_one($id);
							if($campo_info->id){
								
								if($campo_info->id_tipo_campo == 5){
						
									$fecha_decoded = json_decode($campo_info->html_name, true);
									$name_inicio = $fecha_decoded["start_name"];
									$name_final = $fecha_decoded["end_name"];
									
									$date_inicio = $value->start_date;
									$date_final = $value->end_date;
									
									$array_valores[$name_inicio] = $date_inicio;
									$array_valores[$name_final] = $date_final;
									
								}elseif($campo_info->id_tipo_campo == 10){
									if($value){
										$array_valores[$campo_info->html_name] = get_uri("api/download_file/".$valor->id."/".$id);
									}else{
										$array_valores[$campo_info->html_name] = $value;
									}
									
								}else if($campo_info->id_tipo_campo == 11){// html
									//$array_valores[$campo_info->html_name] = $campo_info->default_value;
								}else if($campo_info->id_tipo_campo == 12){// divider
									//$array_valores[$campo_info->html_name] = $campo_info->default_value;
								}else{
									$array_valores[$campo_info->html_name] = $value;
								}
								
							}else{
								
							}
							
						}
						
					}

					$response[] = $array_valores;
				}


			} else if(($form_info->flujo == "Consumo")||($form_info->flujo == "No Aplica")){


				foreach($valores_de_formulario as $valor){

					$array_valores = array();
					$array_valores["id"] = $valor->id;
					$datos_decoded = json_decode($valor->datos);
					
					foreach($datos_decoded as $id => $value){
						
						if($id == "fecha"){
							$array_valores["date_filed"] = $value;
						}elseif($id == "id_categoria"){
							$category = $this->Categories_model->get_one($value);
							$array_valores["category"] = ($category->id) ? array(
								"id" => $category->id,
								"name" => $category->nombre
							) : null;
						}elseif($id == "unidad_residuo"){
							$array_valores["quantity"] = (string)$value;
						}elseif($id == "id_sucursal"){
							$subproject = $this->Subprojects_model->get_one($value);
							$array_valores["branch_office"] = ($subproject->id) ? array(
								"id" => $subproject->id,
								"name" => $subproject->nombre
							) : null;
						}elseif($id == "type_of_origin"){
							$type_of_origin = $this->EC_Types_of_origin_model->get_one($value);
							$array_valores["type_of_origin"] = ($type_of_origin->id) ? array(
								"id" => $type_of_origin->id,
								"name" => lang($type_of_origin->nombre)
							) : null;
						}elseif($id == "type_of_origin_matter"){
							$type_of_origin_matter = $this->EC_Types_of_origin_matter_model->get_one($value);
							$array_valores["type_of_origin_matter"] = ($type_of_origin_matter->id) ? array(
								"id" => $type_of_origin_matter->id,
								"name" => lang($type_of_origin_matter->nombre)
							) : null;
						}elseif($id == "tipo_unidad"){
							
						}elseif($id == "unidad"){
							
						}else{
							
							// COMPROBAR SI EL CAMPO DEL BUCLE ES DE TIPO ARCHIVO, HTML O DIVIDER
							$campo_info = $this->Fields_model->get_one($id);
							if($campo_info->id){
								
								if($campo_info->id_tipo_campo == 5){
						
									$fecha_decoded = json_decode($campo_info->html_name, true);
									$name_inicio = $fecha_decoded["start_name"];
									$name_final = $fecha_decoded["end_name"];
									
									$date_inicio = $value->start_date;
									$date_final = $value->end_date;
									
									$array_valores[$name_inicio] = $date_inicio;
									$array_valores[$name_final] = $date_final;
									
								}elseif($campo_info->id_tipo_campo == 10){
									if($value){
										$array_valores[$campo_info->html_name] = get_uri("api/download_file/".$valor->id."/".$id);
									}else{
										$array_valores[$campo_info->html_name] = $value;
									}
									
								}else if($campo_info->id_tipo_campo == 11){// html
									//$array_valores[$campo_info->html_name] = $campo_info->default_value;
								}else if($campo_info->id_tipo_campo == 12){// divider
									//$array_valores[$campo_info->html_name] = $campo_info->default_value;
								}else{
									$array_valores[$campo_info->html_name] = $value;
								}
								
							}else{
								
							}

						}

					}

					$response[] = $array_valores;
				}

			} else {

			}
			
		} else if($form_info->id_tipo_formulario == 2){ // MANTENEDORAS
			$array_errors[] = "Tipo de formulario Mantenedora. Aun no implementado";
		} else if($form_info->id_tipo_formulario == 3){ // OTROS REGISTROS
			$array_errors[] = "Tipo de formulario Otros Registros. Aun no implementado";
		}
		
		// SI NO HAY ERRORES DE VALIDACIÓN
		if(!count($array_errors)){
			$data = array("zone" => $array_project, "form_name" => $form_info->nombre, "form_type" => $form_type_label, "n_records" => $n_records, "data" => $response);
			$this->response(array("success" => true, "data" => $data));
		} else {
			$this->response(array("success" => false, "errors" => $array_errors));
		}
		
	}


	public function get_categories_get($id_category = 0){

		$this->_token_validation($this->input->get_request_header("Authorization", true));
		
		$id_category = $this->input->get('id_category');

		// VALIDACIÓN DE CAMPOS
		if($id_category){
			$category = $this->Categories_model->get_one($id_category);
			if(!$category->id){
				$array_errors[] = "La Categoría con id $id_category no existe";
			}
			$data = array("id" => $category->id, "name" => $category->nombre);
		} else {
			$categories = $this->Categories_model->get_all_where(array("deleted" => 0))->result();
			$data = array();
			foreach($categories as $category){
				$data[] = array("id" => $category->id, "name" => $category->nombre);
			}
			if(!count($data)){
				$array_errors[] = "No hay categorías disponibles";
			}
		}

		// SI NO HAY ERRORES DE VALIDACIÓN
		if(!count($array_errors)){
			$this->response(array("success" => true, "data" => $data));
		} else {
			$this->response(array("success" => false, "errors" => $array_errors));
		}

	}

	/*
		El cliente necesita identificar la zona en la API (Proyecto).
		Entonces necesita:
		- El total de cada categoría por Zona (proyecto)
		- El total de cada categoría por cliente

		Sería entonces: nombre_zona -> nombre_formulario -> nombre_categoría -> total de categoría en el formulario
	*/
	public function get_categories_of_form_get(){

		$this->_token_validation($this->input->get_request_header("Authorization", true));
		$id_form = $this->input->get('id_form');

		// VALIDACIÓN DE CAMPOS
		if(!$id_form){
			$array_errors[] = "El campo id_form es obligatorio.";
		} else {
			$form_info = $this->Forms_model->get_one($id_form);
			if(!$form_info->id){
				$array_errors[] = "El Formulario con id $id_form no existe";
			} else {

				$form_type = $this->Form_types_model->get_one($form_info->id_tipo_formulario);
				$form_type_label = ($form_info->id_tipo_formulario == 1) ? $form_type->nombre." (".$form_info->flujo.")" : $form_info->nombre;

				// TRAER EL PROYECTO ASOCIADO AL FORMULARIO
				$id_project = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $form_info->id, "deleted" => 0))->id_proyecto; 
				$project = $this->Projects_model->get_one($id_project);
				$array_project = array("id" => $project->id, "name" => $project->title);

				$categories = $this->Categories_model->get_categories_of_material_of_form($form_info->id)->result();
				$array_categories = array();
				foreach($categories as $category){
					$array_categories[] = array("id" => $category->id, "name" => $category->nombre);
				}

				$data = array(
					"zone" => $project->id ? $array_project : null,
					"id" => $form_info->id,
					"name" => $form_info->nombre,
					"type" => $form_type_label,
					"categories" => $array_categories
				);

			}
		}

		// SI NO HAY ERRORES DE VALIDACIÓN
		if(!count($array_errors)){
			$this->response(array("success" => true, "data" => $data));
		} else {
			$this->response(array("success" => false, "errors" => $array_errors));
		}

	}

	public function get_entity_codes_get(){

		$this->_token_validation($this->input->get_request_header("Authorization", true));

		$array_entity_codes = array(
			"form" => lang("form"),
			"category" => lang("category"),
			"type_of_treatment" => lang("type_of_treatment"),
			"branch_office" => lang("branch_office"),
			"patent" => lang("patent"),
			"waste_transport_company" => lang("waste_transport_company"),
			"waste_receiving_company" => lang("waste_receiving_company")
		);

		return $this->response(array("success" => true, "data" => $array_entity_codes));

	}

	public function get_entity_data_get(){

		$this->_token_validation($this->input->get_request_header("Authorization", true));

		$entity_code = $this->input->get('entity_code');
		$id = $this->input->get('id');

		$data = array();

		if($entity_code == "form"){ // FORMULARIO

			if($id){

				// VALIDACIÓN DE CAMPOS			
				$entity = $this->Forms_model->get_one($id);
				if(!$entity->id){
					$array_errors[] = "El Formulario con id $id no existe";
				} else {
	
					$form_type = $this->Form_types_model->get_one($entity->id_tipo_formulario);
					$form_type_label = ($entity->id_tipo_formulario == 1) ? $form_type->nombre." (".$entity->flujo.")" : $entity->nombre;
	
					// TRAER EL PROYECTO ASOCIADO AL FORMULARIO
					$id_project = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $entity->id, "deleted" => 0))->id_proyecto; 
					$project = $this->Projects_model->get_one($id_project);
					$array_project = array("id" => $project->id, "name" => $project->title);

					$data = array(
						"zone" => $project->id ? $array_project : null,
						"id" => $entity->id, 
						"name" => $entity->nombre,
						"type" => $form_type_label,
					);

				}
				
			} else {

				$entities = $this->Forms_model->get_all()->result();
				foreach($entities as $entity){
					$form_type = $this->Form_types_model->get_one($entity->id_tipo_formulario);
					$form_type_label = ($entity->id_tipo_formulario == 1) ? $form_type->nombre." (".$entity->flujo.")" : $entity->nombre;

					// TRAER EL PROYECTO ASOCIADO AL FORMULARIO
					$id_project = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $entity->id, "deleted" => 0))->id_proyecto; 
					$project = $this->Projects_model->get_one($id_project);
					$array_project = array("id" => $project->id, "name" => $project->title);

					$data[] = array(
						"zone" => $project->id ? $array_project : null,
						"id" => $entity->id, 
						"name" => $entity->nombre,
						"type" => $form_type_label,
					);

				}
			}

		} elseif($entity_code == "category"){ // CATEGORÍA

			if($id){
				$entity = $this->Categories_model->get_one($id);
				if(!$entity->id){
					$array_errors[] = "La Categoría con id $id no existe";
				} else {
					$data = array("id" => $entity->id, "name" => $entity->nombre);
				}
			} else {
				$entities = $this->Categories_model->get_all()->result();
				foreach($entities as $entity){
					$data[] = array("id" => $entity->id, "name" => $entity->nombre);
				}
			}

		} elseif($entity_code == "type_of_treatment") { // TIPO DE TRATAMIENTO

			if($id){
				$entity = $this->Tipo_tratamiento_model->get_one($id);
				if(!$entity->id){
					$array_errors[] = "El Tipo de Tratamiento con id $id no existe";
				} else {
					$data = array("id" => $entity->id, "name" => $entity->nombre);
				}
			} else {
				$entities = $this->Tipo_tratamiento_model->get_all()->result();
				foreach($entities as $entity){
					$data[] = array("id" => $entity->id, "name" => $entity->nombre);
				}
			}
		
		} elseif($entity_code == "branch_office") { // SUCURSAL

			if($id){
				$entity = $this->Subprojects_model->get_one($id);
				if(!$entity->id){
					$array_errors[] = "La Sucursal con id $id no existe";
				} else {

					// TRAER EL PROYECTO ASOCIADO A LA SUCURSAL
					$id_project = $entity->id_proyecto; 
					$project = $this->Projects_model->get_one($id_project);
					$array_project = array("id" => $project->id, "name" => $project->title);
					$data = array("zone" => $array_project, "id" => $entity->id, "name" => $entity->nombre);
				}
			} else {
				$entities = $this->Subprojects_model->get_all()->result();
				foreach($entities as $entity){

					// TRAER EL PROYECTO ASOCIADO A LA SUCURSAL
					$id_project = $entity->id_proyecto; 
					$project = $this->Projects_model->get_one($id_project);
					$array_project = array("id" => $project->id, "name" => $project->title);
					$data[] = array("zone" => $array_project, "id" => $entity->id, "name" => $entity->nombre);
				}
			}

		} elseif($entity_code == "patent") { // PATENTE

			if($id){
				$entity = $this->Patents_model->get_one($id);
				if(!$entity->id){
					$array_errors[] = "La Patente con id $id no existe";
				} else {

					// TRAER EL PROYECTO ASOCIADO A LA PATENTE
					$id_project = $entity->id_project; 
					$project = $this->Projects_model->get_one($id_project);
					$array_project = array("id" => $project->id, "name" => $project->title);

					// TRAER LA EMPRESA TRANSOPORTISTA DE RESIDUOS DE LA PATENTE
					$waste_transport_company = $this->Fixed_feeder_waste_transport_companies_values_model->get_one($entity->id_waste_transport_company);
					$array_waste_transport_company = array(
						"id" => $waste_transport_company->id,
						"name" => $waste_transport_company->company_name
					);

					$data = array("zone" => $array_project, "id" => $entity->id, "name" => $entity->patent, "waste_transport_company" => $array_waste_transport_company);
				}
			} else {
				$entities = $this->Patents_model->get_all()->result();
				foreach($entities as $entity){

					// TRAER EL PROYECTO ASOCIADO A LA PATENTE
					$id_project = $entity->id_project; 
					$project = $this->Projects_model->get_one($id_project);
					$array_project = array("id" => $project->id, "name" => $project->title);

					// TRAER LA EMPRESA TRANSOPORTISTA DE RESIDUOS DE LA PATENTE
					$waste_transport_company = $this->Fixed_feeder_waste_transport_companies_values_model->get_one($entity->id_waste_transport_company);
					$array_waste_transport_company = array(
						"id" => $waste_transport_company->id,
						"name" => $waste_transport_company->company_name
					);
					$data[] = array("zone" => $array_project, "id" => $entity->id, "name" => $entity->patent, "waste_transport_company" => $array_waste_transport_company);
				}
			}

		} elseif($entity_code == "waste_transport_company") { // EMPRESA TRANSPORTISTA DE RESIDUOS

			if($id){
				$entity = $this->Fixed_feeder_waste_transport_companies_values_model->get_one($id);
				if(!$entity->id){
					$array_errors[] = "Empresa Transportista de Residuos con id $id no existe";
				} else {
					// TRAER EL PROYECTO ASOCIADO A LA PATENTE
					$id_project = $entity->id_project; 
					$project = $this->Projects_model->get_one($id_project);
					$array_project = array("id" => $project->id, "name" => $project->title);

					$data = array("zone" => $array_project, "id" => $entity->id, "name" => $entity->company_name);
				}
			} else {
				$entities = $this->Fixed_feeder_waste_transport_companies_values_model->get_all()->result();
				foreach($entities as $entity){
					// TRAER EL PROYECTO ASOCIADO A LA PATENTE
					$id_project = $entity->id_project; 
					$project = $this->Projects_model->get_one($id_project);
					$array_project = array("id" => $project->id, "name" => $project->title);

					$data[] = array("zone" => $array_project, "id" => $entity->id, "name" => $entity->company_name);
				}
			}

		} elseif($entity_code == "waste_receiving_company") { // EMPRESA RECEPTORA DE RESIDUOS

			if($id){
				$entity = $this->Fixed_feeder_waste_receiving_companies_values_model->get_one($id);
				if(!$entity->id){
					$array_errors[] = "Empresa Receptora de Residuos con id $id no existe";
				} else {
					// TRAER EL PROYECTO ASOCIADO A LA PATENTE
					$id_project = $entity->id_project; 
					$project = $this->Projects_model->get_one($id_project);
					$array_project = array("id" => $project->id, "name" => $project->title);

					$data = array("zone" => $array_project, "id" => $entity->id, "name" => $entity->company_name);
				}
			} else {
				$entities = $this->Fixed_feeder_waste_receiving_companies_values_model->get_all()->result();
				foreach($entities as $entity){
					// TRAER EL PROYECTO ASOCIADO A LA PATENTE
					$id_project = $entity->id_project; 
					$project = $this->Projects_model->get_one($id_project);
					$array_project = array("id" => $project->id, "name" => $project->title);

					$data[] = array("zone" => $array_project, "id" => $entity->id, "name" => $entity->company_name);
				}
			}

		} elseif(!$entity_code){
			$array_errors[] = "El campo entity_code es obligatorio";
		} else {
			$array_errors[] = "La entidad con código $entity_code no existe";
		}

		$n_records = ($id) ? "1" : (string)count($data);

		// SI NO HAY ERRORES DE VALIDACIÓN
		if(!count($array_errors)){
			$this->response(array("success" => true, "n_records" => $n_records, "data" => $data));
		} else {
			$this->response(array("success" => false, "errors" => $array_errors));
		}

	}


	function get_total_form_values_get(){

		$this->_token_validation($this->input->get_request_header("Authorization", true));

		$first_date = $this->input->get('first_date');
        $last_date = $this->input->get('last_date');
		$group_by = $this->input->get('group_by');
		$options = array();

		// VALIDACIÓN DE CAMPOS	
		if($first_date && $last_date){

			// VALIDA QUE LAS FECHAS VENGAN EN FORMATO CORRECTO
			if( $this->validate_date($first_date) && $this->validate_date($last_date) ){

				// VALIDA QUE LA FECHA DE INICIO SEA MENOR O IGUAL A LA DE TÉRMINO
				if( $this->validate_date_range($first_date, $last_date)){
					$options["first_date"] = $first_date;
					$options["last_date"] = $last_date;
				} else {
					$array_errors[] = "La fecha de inicio debe ser menor o igual a la fecha de término";
				}

			} else {
				$array_errors[] = "El formato de fechas debe ser YYYY-MM-DD";
			}

		} else if( ($first_date && !$last_date) || (!$first_date && $last_date) ){
			$array_errors[] = "Para consultar los datos por rango de fechas, se debe incluir la fecha de inicio y la de término";
		} 
		
		if($group_by){

			if(in_array($group_by, array("zone", "form", "material", "category"))){
				$options["group_by"] = $group_by;
			} else {
				$array_errors[] = "No se puede agrupar por '$group_by'. Las opciones disponibles para el campo 'group_by' son: 'zone' | 'form' | 'material' | 'category'. Si no se envía el campo 'group_by', el resultado devuelto será el total de todas las zonas.";
			}

		}

		// SI NO HAY ERRORES DE VALIDACIÓN
		if(!count($array_errors)){

			$total_form_values = $this->Api_model->get_total_form_values($options)->result();
			$data = array();

			if($group_by == "zone"){

				foreach($total_form_values as $total_form_value){
					$data_zone = array();
					$data_zone["zone"] = array("id" => $total_form_value->id_project, "name" => $total_form_value->project_name);
					$data_zone["total"] = $total_form_value->total;
					array_push($data, $data_zone);
				}

			} elseif($group_by == "form"){

				foreach($total_form_values as $total_form_value){
					$data_form = array();
					$data_form["zone"] = array("id" => $total_form_value->id_project, "name" => $total_form_value->project_name);
					$data_form["form"] = array("id" => $total_form_value->id_form, "name" => $total_form_value->form_name." (".$total_form_value->form_flujo.")");
					$data_form["total"] = $total_form_value->total;
					array_push($data, $data_form);
				}

			} elseif($group_by == "material"){

				foreach($total_form_values as $total_form_value){
					$data_form = array();
					$data_form["zone"] = array("id" => $total_form_value->id_project, "name" => $total_form_value->project_name);
					$data_form["form"] = array("id" => $total_form_value->id_form, "name" => $total_form_value->form_name." (".$total_form_value->form_flujo.")");
					$data_form["material"] = array("id" => $total_form_value->id_material, "name" => $total_form_value->material_name);
					$data_form["total"] = $total_form_value->total;
					array_push($data, $data_form);
				}

			} elseif($group_by == "category"){

				foreach($total_form_values as $total_form_value){
					$data_form = array();
					$data_form["zone"] = array("id" => $total_form_value->id_project, "name" => $total_form_value->project_name);
					$data_form["form"] = array("id" => $total_form_value->id_form, "name" => $total_form_value->form_name." (".$total_form_value->form_flujo.")");
					$data_form["material"] = array("id" => $total_form_value->id_material, "name" => $total_form_value->material_name);
					$data_form["category"] = array("id" => $total_form_value->id_category, "name" => $total_form_value->category_name);
					$data_form["total"] = $total_form_value->total;
					array_push($data, $data_form);
				}

			} else {
				$data["total"] = $total_form_values[0]->total;
			}

			$this->response(array("success" => true, "data" => $data));
		} else {
			$this->response(array("success" => false, "errors" => $array_errors));
		}

		$data = array();

	}


	// ------------------------------------- METODOS SECUNDARIOS ---------------------------------------------

	protected static function generate_token($length = 24) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTU';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
	}

    public function api_upload_file_to_temp($file_name) {
		
        if (!empty($file_name)) {
			
            if (!is_valid_file_to_upload($file_name)){
				return false;
			} 
				
			$server_temp_file = sys_get_temp_dir().'/'.$file_name;
            $temp_file_path = get_setting("temp_file_path");
            $target_path = getcwd() . '/' . $temp_file_path;
            if (!is_dir($target_path)) {
                if (!mkdir($target_path, 0777, true)) {
                    die('Failed to create file folders.');
                }
            }
            $target_file = $target_path . $file_name;
            copy($server_temp_file, $target_file);
        }
    }

	private function validate_date($date){
		$d = DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') == $date;
	}

	private function validate_date_range($first_date, $last_date) {
		$first_date = DateTime::createFromFormat('Y-m-d', $first_date);
		$last_date = DateTime::createFromFormat('Y-m-d', $last_date);
	
		if ($first_date <= $last_date) {
			return true;
		} else {
			return false;
		}
	}

	private function _token_validation($token = ""){

		// GENERAR UN TOKEN
		// $token = bin2hex(random_bytes(20));
		// return $this->response($token);

		//$token = $this->get('Authorization');
		// $array_errores = array();
		
		// // VÁLIDO SI VIENE EL TOKEN
		// if(!$token){
		// 	return $this->response(array("message" => "Fallo de autenticidad! No se identifico un token de acceso.", "success" => false));
		// 	exit();
		// }
		
		// // VÁLIDO SI EXISTE UN USUARIO ASOCIADO AL TOKEN
		// $existe_token = $this->Api_model->get_one_where(array("token" => $token, "deleted" => 0));
		// if(!$existe_token->id){
		// 	return $this->response(array("message" => "Fallo de autenticidad!", "success" => false));
		// 	exit();
		// }

		// $id_usuario_cliente_token = $existe_token->user_id;
		// $usuario_cliente = $this->Users_model->get_one_where(array("id" => $id_usuario_cliente_token, "deleted" => 0));
		// $id_usuario_cliente = $usuario_cliente->id;
		// $id_cliente = $usuario_cliente->client_id;

		if(!$token){
			return $this->response(array("message" => "Fallo de autenticidad! No se identifico un token de acceso.", "success" => false));
			exit();
		}
		if($token != $this->api_token){
			return $this->response(array("message" => "Fallo de autenticidad! token incorrecto.", "success" => false));
			exit();
		}

	}

}
