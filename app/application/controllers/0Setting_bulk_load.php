<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Setting_bulk_load extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        parent::__construct();
		$this->load->helper('email');

        //check permission to access this module
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 11;
		$this->id_submodulo_cliente = 21;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
		
    }

    /* load clients list view */

    function index() {
        //$this->access_only_allowed_members();

        $id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		//$tipos_de_formularios = $this->Form_types_model->get_details()->result();
		$tipos_de_formularios = array("" => "-") + $this->Form_types_model->get_dropdown_list(array("nombre"), "id");
		
		$view_data["tipos_de_formularios"] = $tipos_de_formularios;
		$view_data["project_info"] = $proyecto;
		
		//Configuración perfil de usuario
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$view_data["puede_editar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
		
        $this->template->rander("setting_bulk_load/index", $view_data);
    }

    /* load client add/edit modal */

    function modal_form() {
        $this->access_only_allowed_members();

        $client_id = $this->input->post('id');
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view'); //view='details' needed only when loding from the client's details view
        $view_data['model_info'] = $this->Clients_model->get_one($client_id);
        //$view_data["currency_dropdown"] = $this->get_currency_dropdown_select2_data();

        //get custom fields
        //$view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("clients", $client_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

        $this->load->view('clients/modal_form', $view_data);
    }

    function get_forms_of_form_type() {
        $id_form_type = $this->input->post('id_form_type');
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
        
		//get_dropdown_list(array("nombre"), "id");a
        $forms = $this->Forms_model->get_forms_of_project(array("id_proyecto" => $id_proyecto, "id_tipo_formulario" => $id_form_type))->result();
        $formularios = array();
        $formularios[] = array("id" => "", "text" => "-");
        
		// Formularios fijos de proyecto
		$formularios_fijos_proyecto = $this->Fixed_field_rel_form_rel_project_model->get_fixed_forms_related_to_project(array(
			"id_proyecto" => $id_proyecto,
			"id_tipo_formulario" => $id_form_type
		))->result();
		
		
        if($id_proyecto){

			if($id_form_type == 2){
				$formularios[] = array("id" => "waste_transport_companies", "text" => lang("waste_transport_companies"));
				$formularios[] = array("id" => "waste_receiving_companies", "text" => lang("waste_receiving_companies"));
			}

            foreach($forms as $form){
                $formularios[] = array("id" => $form->id, "text" => $form->nombre);
            }
			
			foreach($formularios_fijos_proyecto as $form){
				$formularios[] = array("id" => $form->id, "text" => $form->nombre);
			}
        }
        
        echo json_encode($formularios);
		
    }
	
	function get_excel_template_of_form() {
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
        $id_formulario = $this->input->post('id_form');
		
		$info_cliente = $this->Clients_model->get_one($id_cliente);
		$info_proyecto = $this->Projects_model->get_one($id_proyecto);
		$info_formulario = $this->Forms_model->get_one($id_formulario);

		if($id_formulario == "waste_transport_companies"){
			$info_formulario = new stdClass();
			$info_formulario->id_tipo_formulario = 2;
			$info_formulario->nombre = lang("waste_transport_companies");
			$info_formulario->codigo = $this->clean(lang("waste_transport_companies"));
		}
		if($id_formulario == "waste_receiving_companies"){
			$info_formulario = new stdClass();
			$info_formulario->id_tipo_formulario = 2;
			$info_formulario->nombre = lang("waste_receiving_companies");
			$info_formulario->codigo = $this->clean(lang("waste_receiving_companies"));
		}
				
		if(!$info_cliente->id && !$info_proyecto->id && !$info_formulario->id) {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
			exit();
		}

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
        
		$this->load->library('excel');		
		
		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle($info_formulario->nombre)
							 ->setSubject($info_formulario->nombre)
							 ->setDescription($info_formulario->nombre)
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");
		$doc->setActiveSheetIndex(0);
		
		// CREAR HOJA PARA OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN
		$doc->createSheet();
		// APUNTAR A NUEVA HOJA
		$doc->setActiveSheetIndex(1);
		// CAMBIARLE NOMBRE A HOJA
		$doc->getActiveSheet()->setTitle('options');
		
		// VOLVER A APUNTAR A PRIMERA HOJA
		$doc->setActiveSheetIndex(0);
		
		// Obtención de campos dinamicos
		if(!$info_formulario->fijo){
			// Aunque su nombre hace referencia a fixed_feeders, tambien trae los campos de formularios no fijos (Forms_model)
			$campos_formulario = $this->get_fields_of_fixed_feeders($id_formulario);

		} else {
			$campos_formulario = $this->Fixed_fields_model->get_all_where(array(
				"codigo_formulario_fijo" => $info_formulario->codigo_formulario_fijo,
				"deleted" => 0
			))->result();
		}
		
		
		$num_columnas = 0;
		$columna = 0;
		
		// REGISTRO AMBIENTAL
		if($info_formulario->id_tipo_formulario == 1){

			// FILA NOMBRE COLUMNAS | -----------------------------------------------------

			// Si el proyecto es CPC se cambia el nombre del campo
			if($info_proyecto->id == CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('transfer_date_to_pick_up_point'));
			}else{
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('storage_date'));
			}
			$columna++;

			if($info_formulario->flujo == "Residuo"){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('month'));
				$columna++;
			}

			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('category'));
			$columna++;

			$formulario_unidad = json_decode($info_formulario->unidad, true);
			$unidad = $this->Unity_model->get_one($formulario_unidad["unidad_id"]);
			$campo_unidad = $formulario_unidad["nombre_unidad"];
			$nombre_unidad = $campo_unidad . " (" . $unidad->nombre. ")";
			//$doc->getActiveSheet()->setCellValue('C1', $nombre_unidad);

			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang("quantity"));
						
			// Poner como comentario el nombre de la unidad en el campo Cantidad
			$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
			$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
			$comentario->getFont()->setBold(true);
			$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
			$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . $nombre_unidad )->getFont()->setBold(true);
			$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
			$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("70px");

			$columna++;

			if($info_formulario->flujo == "Consumo"){

				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('type'));
				$columna++;

				$data_tipo_origen = json_decode($info_formulario->tipo_origen);
				$id_tipo_origen = $data_tipo_origen->type_of_origin;
				$disabled_field_tipo_origen = (boolean)$data_tipo_origen->disabled_field;
				$default_matter = ($data_tipo_origen->default_matter)?$data_tipo_origen->default_matter:NULL;
				
				if($id_tipo_origen == "1"){ // id 1: matter
					
					$array_tipos_origen = array("" => "-");
					$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
						"id_tipo_origen" => $id_tipo_origen,
						"deleted" => 0
					))->result();
					
					foreach($tipos_origen_materia as $tipo_origen_materia){
						$array_tipos_origen[$tipo_origen_materia->id] = lang($tipo_origen_materia->nombre);
					}
				}
				
				if($id_tipo_origen == "2"){ // id 2: energy
					
					$default_matter = 2;
					$array_tipos_origen = array("" => "-");
					$tipos_origen = $this->EC_Types_of_origin_model->get_all()->result();
					foreach($tipos_origen as $tipo_origen){
						$array_tipos_origen[$tipo_origen->id] = lang($tipo_origen->nombre);
					}
				}
				
				// $columna = 4;
				
			}elseif($info_formulario->flujo == "Residuo"){

				// Si el proyecto es CPC se omite el campo
				if($info_proyecto->id != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

					if($info_proyecto->id == CONST_ID_PROYECTO_QALI){	// ID proyecto QALI en dev
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('waste_generating_area'));
					}else{
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('source'));
					}
					$columna++;

					$array_sources = array();
					$sources = $this->Sources_model->get_details(array("id_proyecto" => $id_proyecto))->result();
					foreach($sources as $source){
						$array_sources[$source->id] = lang($source->name);
					}

				}
				
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('type_of_treatment'));
				$columna++;

				$array_tipo_tratamiento = array();
				$tipos_tratamiento = $this->Tipo_tratamiento_model->get_details(array("id_proyecto" => $id_proyecto))->result();
				foreach($tipos_tratamiento as $tipo_tratamiento){
					$array_tipo_tratamiento[$tipo_tratamiento->id] = $tipo_tratamiento->nombre;
				}
				/*$array_tipo_tratamiento["1"] = "Disposición";
				$array_tipo_tratamiento["2"] = "Reutilización";
				$array_tipo_tratamiento["3"] = "Reciclaje";*/

				//$doc->getActiveSheet()->setCellValue('E1', lang('retirement_date'));

				// $columna = 5;
			
			}elseif($info_formulario->flujo == "No Aplica"){
				
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('type'));
				$columna++;
				
				$data_tipo_no_aplica = json_decode($info_formulario->tipo_por_defecto);
				$default_type = ($data_tipo_no_aplica->default_type)?$data_tipo_no_aplica->default_type:NULL;
				$disabled_field_no_aplica = (boolean)$data_tipo_no_aplica->disabled_field;
				
				$array_tipos_no_aplica = array();
				$tipos_no_aplica = $this->EC_Types_no_apply_model->get_all()->result();
				
				foreach($tipos_no_aplica as $tipo_no_aplica){
					$array_tipos_no_aplica[$tipo_no_aplica->id] = lang($tipo_no_aplica->nombre);
				}
				
				// $columna = 4;
			}else{
				
			}
			
			// CAMPOS DINAMICOS
			foreach($campos_formulario as $campo){
				if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				if($campo->id_tipo_campo == 15){ // UNIDAD
					$column_options = json_decode($campo->opciones, true);
					$id_unidad = $column_options[0]["id_unidad"];
					$unidad = $this->Unity_model->get_one($id_unidad);
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', $campo->nombre.' ('.$unidad->nombre.')');
				} else {
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', $campo->nombre);
				}
				
				if($campo->default_value && $campo->id_tipo_campo != 16){ //SI EL CAMPO TIENE VALOR POR DEFECTO Y NO ES SELECCIÓN DESDE MANTENEDORA
					
					if($campo->id_tipo_campo == 5){	
						$periodo = json_decode($campo->default_value);
						$valor_por_defecto = $periodo->start_date."/".$periodo->end_date;
					} else {
						$valor_por_defecto = $campo->default_value;
					}
					
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
					$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
					$comentario->getFont()->setBold(true);
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("default_value_field") . ": ")->getFont()->setBold(true);
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun($valor_por_defecto);
					
					
					if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 13){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("input_text_maxlength_msg"));
					}
					if($campo->id_tipo_campo == 2){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("textarea_maxlength_msg"));
					}
					
					if($campo->habilitado){ //SI EL CAMPO ESTÁ DESHABILITADO
						
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);
						
						if($campo->obligatorio){
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						} else {
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}
						
					} else { //SI EL CAMPO ESTÁ HABILITADO
						
						if($campo->obligatorio){
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						} else {
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}
						
					}
					
				} else if(!$campo->default_value && $campo->id_tipo_campo != 16){
					
					if($campo->habilitado){ //SI EL CAMPO ESTÁ DESHABILITADO
						
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
						$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
						$comentario->getFont()->setBold(true);
					
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);

						if($campo->obligatorio){
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						} else {
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}
						
					} else { //SI EL CAMPO ESTÁ HABILITADO
						
						if($campo->obligatorio){
							
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
							$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
							$comentario->getFont()->setBold(true);
						
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							
							
							if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 13){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("input_text_maxlength_msg"));
							}
							
							if($campo->id_tipo_campo == 2){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("textarea_maxlength_msg"));
							}
							
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						
						}else{
	
							if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 13){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
								$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"))->getFont()->setBold(true);
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("input_text_maxlength_msg"));
							}
							
							if($campo->id_tipo_campo == 2){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
								$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"))->getFont()->setBold(true);
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("textarea_maxlength_msg"));
							}
							
						}
						
					}
					
					

				}

				// FORMULARIO TRANSPORTE DE PLANTULAS DE PROYECTO CPC
				// Se debe poner un commentario que le indique al usuario que el campo "Peso transportado" no se debe modificar a mano, 
				// ya que es un campo calculado automaticamente a partir de la multiplicación de los  campos "Peso promedio plantula" y "Unidades transportadas"
				if($id_proyecto == CONST_ID_PROYECTO_CPC && $id_formulario == CONST_ID_FORM_TRANSP_DE_PLANTULAS){
					// Campo 'Peso transportado'
					if($campo->id == CONST_ID_CAMPO_PESO_TRANSPORTADO_PLANTULAS){
						// Comentarios
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
						$comentario->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("plantulas_transported_weight_excel") )->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("160px");
						
					}
				}

				$columna++;
			}


			if($info_formulario->flujo == "Residuo"){
				
				// Si el proyecto es CPC se omite el campo
				if($info_proyecto->id != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('patent_plate'));
					$columna++;

					$array_patents = array();
					$patents = $this->Patents_model->get_all_where(array(
						"id_client" => $id_cliente,
						"id_project" => $id_proyecto,
						'deleted' => 0
					))->result();

					foreach($patents as $patent){
						$array_patents[] = $patent->patent;
					}
				}

				// Si el proyecto es CPC se omite el campo
				if($info_proyecto->id != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('waste_transport_company'));
					$columna++;

					$array_waste_transport_companies = array();
					$waste_transport_companies = $this->Fixed_feeder_waste_transport_companies_values_model->get_details(array(
						"id_client" => $id_cliente,
						"id_project" => $id_proyecto
					))->result();
					foreach($waste_transport_companies as $waste_transport_company){
						$array_waste_transport_companies[$waste_transport_company->id] = $waste_transport_company->company_name;
					}
				
				}
				
				// Si el proyecto es CPC se omite el campo
				if($info_proyecto->id != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('waste_receiving_company'));
					$columna++;

					$array_waste_receiving_companies = array();
					$waste_receiving_companies = $this->Fixed_feeder_waste_receiving_companies_values_model->get_details(array(
						"id_client" => $id_client,
						"id_project" => $id_project
					))->result();
					foreach($waste_receiving_companies as $company){
						$array_waste_receiving_companies[$company->id] = $company->company_name;
					}
				
				}

				// Si el proyecto es CPC se cambia el nombre del campo
				if($info_proyecto->id == CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('point_withdrawal_retirement_date'));
				}else{
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('retirement_date'));
				}
				// $columna++;


			}
			
			
			// FILA DEMO | -----------------------------------------------------
			$columna = 0;	//Se usa para avanzar por las celdas de la hoja principal
			$columna_opciones = 0;	//Se usa para avanzar por las celdas de la hoja opciones
			
			// COLUMNA FECHA
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_test_date'));
			// PARA DEJAR FECHA COMO TEXTO
			$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
			$columna++;

			// COLUMNA MES
			if($info_formulario->flujo == "Residuo"){

				// GUARDO OPCIONES DE SELECT MES EN HOJA OPCIONES
				$doc->setActiveSheetIndex(1);
				$fila_opcion = 1;

				foreach(CONST_ARRAY_MESES as $mes){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $mes);
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
				
				$cantidad_meses = count(CONST_ARRAY_MESES);

				$col_opt = $this->getNameFromNumber($columna_opciones);
				
				$objValidation->setFormula1('options!$'.$col_opt.'$1:$'.$col_opt.'$'.$cantidad_meses);
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', CONST_ARRAY_MESES[0]);

				$columna++;
				$columna_opciones++;
			}
			

			// COLUMNA CATEGORIA
			$cats = $this->Categories_model->get_categories_of_material_of_form($id_formulario)->result();
			$categorias = array();
			foreach($cats as $cat){
				$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $cat->id, 'id_cliente' => $id_cliente, "deleted" => 0));
				if($row_alias->alias){
					$categorias[] = $row_alias->alias;
				}else{
					$categorias[] = $cat->nombre;
				}
			}
			
			// GUARDO OPCIONES DE SELECT CATEGORIAS EN HOJA OPCIONES (ETIQUETAS)
			$doc->setActiveSheetIndex(1);
			$fila_opcion = 1;
			foreach($categorias as $categoria){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $categoria);
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
			
			$cantidad_categorias = count($categorias);

			$col_opt = $this->getNameFromNumber($columna_opciones);
			
			$objValidation->setFormula1('options!$'.$col_opt.'$1:$'.$col_opt.'$'.$cantidad_categorias);
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $categorias[0]);

			$columna++;
			$columna_opciones++;

			//COLUMNA UNIDAD
			$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
			$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', '30.45', PHPExcel_Cell_DataType::TYPE_STRING);

			$columna++;
			
			if($info_formulario->flujo == "Consumo"){
				
				// GUARDO OPCIONES DE SELECT TIPO EN HOJA OPCIONES
				$doc->setActiveSheetIndex(1);
				$fila_opcion = 1;
				
				// CONSULTO DEFINICION DE MATERIA O ENERGIA
				//$disabled_field = (boolean)$data_tipo_origen->disabled_field;
				//$default_matter = ($data_tipo_origen->default_matter)?$data_tipo_origen->default_matter:NULL;
				
				if($id_tipo_origen == 1){// MATERIA
				
					if($disabled_field_tipo_origen){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $array_tipos_origen[$default_matter]);
					}else{
						foreach($array_tipos_origen as $tipo_origen){
							$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $tipo_origen);
							$fila_opcion++;
						}
					}
				}elseif($id_tipo_origen == 2){// ENERGIA
					
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $array_tipos_origen[$id_tipo_origen]);
					
				}else{
					
				}
				
				$doc->setActiveSheetIndex(0);
				
				// COLUMNA TIPO
				$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
				$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
				$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle(lang('excel_error_title'));
				$objValidation->setError(lang('excel_error_text'));
				
				if($disabled_field_tipo_origen){
					$cantidad_tipo_origen = 1;

					$col_opt = $this->getNameFromNumber($columna_opciones);
					
					$objValidation->setFormula1('options!$'.$col_opt.'$1:$'.$col_opt.'$'.$cantidad_tipo_origen);
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_tipos_origen[$default_matter]);
				}else{
					$cantidad_tipo_origen = count($array_tipos_origen);

					$col_opt = $this->getNameFromNumber($columna_opciones);

					$objValidation->setFormula1('options!$'.$col_opt.'$1:$'.$col_opt.'$'.$cantidad_tipo_origen);
					
					if(!$default_matter){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_tipos_origen[""]);
					}else{
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_tipos_origen[$default_matter]);
					}
					
				}
				

				// $columna = 4;
				$columna++;
				$columna_opciones++;

			}elseif($info_formulario->flujo == "Residuo"){

				// Si el proyecto es CPC se omite el campo
				if($info_proyecto->id != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

					// GUARDO OPCIONES DE SELECT FUENTE EN HOJA OPCIONES
					$doc->setActiveSheetIndex(1);
					$fila_opcion = 1;
					foreach($array_sources as $source){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $source);
						$fila_opcion++;
					}

					$doc->setActiveSheetIndex(0);
					
					// COLUMNA FUENTE
					$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
					$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
					$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle(lang('excel_error_title'));
					$objValidation->setError(lang('excel_error_text'));

					$cantidad_source = count($array_sources);

					$col_opt = $this->getNameFromNumber($columna_opciones);

					$objValidation->setFormula1('options!$'.$col_opt.'$1:$'.$col_opt.'$'.$cantidad_source);
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_sources[1]);

					$columna++;
					$columna_opciones++;

				}
				
				// GUARDO OPCIONES DE SELECT TIPO TRATAMIENTO EN HOJA OPCIONES
				$doc->setActiveSheetIndex(1);
				$fila_opcion = 1;
				
				// CONSULTO LA DEFINICION DEL TIPO DE TRATAMIENTO PARA VER SI ESTÁ DESHABILITADO
				$data_tipo_tratamiento = json_decode($info_formulario->tipo_tratamiento);
				$id_tipo_tratamiento_defecto = $data_tipo_tratamiento->tipo_tratamiento;
				$disabled_field = (boolean)$data_tipo_tratamiento->disabled_field;
				if($disabled_field){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $array_tipo_tratamiento[$id_tipo_tratamiento_defecto]);
				}else{
					foreach($array_tipo_tratamiento as $tipo_tratamiento){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $tipo_tratamiento);
						$fila_opcion++;
					}
				}
				
				$doc->setActiveSheetIndex(0);
				
				// COLUMNA TIPO TRATAMIENTO
				$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();
				$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
				$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle(lang('excel_error_title'));
				$objValidation->setError(lang('excel_error_text'));
				
				//$objValidation->setFormula1('"'.implode(",", $array_tipo_tratamiento).'"');
				//$doc->getActiveSheet()->setCellValue('C2', $array_tipo_tratamiento[1]);
				if($disabled_field){
					$cantidad_tipo_tratamiento = 1;

					$col_opt = $this->getNameFromNumber($columna_opciones);

					$objValidation->setFormula1('options!$'.$col_opt.'$1:$'.$col_opt.'$'.$cantidad_tipo_tratamiento);
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_tipo_tratamiento[$id_tipo_tratamiento_defecto]);
				}else{
					
					$cantidad_tipo_tratamiento = count($array_tipo_tratamiento);

					$col_opt = $this->getNameFromNumber($columna_opciones);

					$objValidation->setFormula1('options!$'.$col_opt.'$1:$'.$col_opt.'$'.$cantidad_tipo_tratamiento);
					
					if($id_tipo_tratamiento_defecto == ""){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_tipo_tratamiento[1]);
					}else{
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_tipo_tratamiento[$id_tipo_tratamiento_defecto]);
					}
					
				}
				
				// $columna = 5;
				$columna++;
				$columna_opciones++;

			}elseif($info_formulario->flujo == "No Aplica"){
				
				// GUARDO OPCIONES DE SELECT TIPO EN HOJA OPCIONES
				$doc->setActiveSheetIndex(1);
				$fila_opcion = 1;
				
				if($disabled_field_no_aplica){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $array_tipos_no_aplica[$default_type]);
				}else{
					foreach($array_tipos_no_aplica as $tipo_no_aplica){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $tipo_no_aplica);
						$fila_opcion++;
					}
				}
				
				$doc->setActiveSheetIndex(0);
				
				// COLUMNA TIPO
				$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
				$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
				$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle(lang('excel_error_title'));
				$objValidation->setError(lang('excel_error_text'));
				
				if($disabled_field_no_aplica){
					$cantidad_tipo_no_aplica = 1;

					$col_opt = $this->getNameFromNumber($columna_opciones);

					$objValidation->setFormula1('options!$'.$col_opt.'$1:$'.$col_opt.'$'.$cantidad_tipo_no_aplica);
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_tipos_no_aplica[$default_type]);
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_tipos_no_aplica["1"]);
				}else{
					$cantidad_tipo_no_aplica = count($array_tipos_no_aplica);

					$col_opt = $this->getNameFromNumber($columna_opciones);

					$objValidation->setFormula1('options!$'.$col_opt.'$1:$'.$col_opt.'$'.$cantidad_tipo_no_aplica);
					
					/*if(!$default_type){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_tipos_no_aplica[""]);
					}else{
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_tipos_no_aplica[$default_type]);
					}*/
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_tipos_no_aplica["1"]);

				}
				
				// $columna = 4;
				$columna++;
				$columna_opciones++;

			}else{
				
			}

			// if($info_formulario->flujo == "Residuo"){
			// 	$columna_opciones = 3;
			// } else {
			// 	$columna_opciones = 2;
			// }
			
			
			
			foreach($campos_formulario as $campo){
				
				if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				if($campo->id_tipo_campo == 1){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_text'));
				}
				if($campo->id_tipo_campo == 2){	
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_textarea'));
				}
				if($campo->id_tipo_campo == 3){
					//$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					$numero_ejemplo = ($campo->default_value) ? $campo->default_value : lang('excel_test_number');
					$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $numero_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $numero_ejemplo);
				}
				if($campo->id_tipo_campo == 4){
					$doc->getActiveSheet()->setCellValue(
						$this->getNameFromNumber($columna).'2', 
						($campo->default_value) ? $campo->default_value : lang('excel_test_date')
					);
					// PARA DEJAR FECHA COMO TEXTO
					$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				}
				if($campo->id_tipo_campo == 5){
					
					if($campo->default_value){
						$periodo = json_decode($campo->default_value);
						$valor_por_defecto = $periodo->start_date."/".$periodo->end_date;
					}
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($valor_por_defecto) ? $valor_por_defecto : lang('excel_test_period'));
				
				}
				if($campo->id_tipo_campo == 6){
					
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_test_select'));
					$datos_campo = json_decode($campo->opciones);

					$array_opciones = array();
					foreach($datos_campo as $row){
						$label = $row->text;
						$value = $row->value;
						$array_opciones[] = $value;
					}
					
					array_shift($array_opciones);
					
					// GUARDO OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN EN HOJA OPCIONES
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
					
					$cantidad_opciones_seleccion = count($array_opciones);
					if($cantidad_opciones_seleccion > 0){
						$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_opciones_seleccion);
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : $array_opciones[0]);
					}
					
					$columna_opciones++;
					
				}
				if($campo->id_tipo_campo == 7){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_select_multiple'));
					
				}
				if($campo->id_tipo_campo == 8){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_rut'));
				}
				if($campo->id_tipo_campo == 9){
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_test_radio'));
					$datos_campo = json_decode($campo->opciones);
					
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
					//$objValidation->setPromptTitle(lang('excel_prompt_title').' "'.$campo->nombre.'"');
					//$objValidation->setPrompt(lang('excel_prompt_text').' "'.$info_mantenedora->nombre.'"');
					$objValidation->setFormula1('"'.implode(",", $array_opciones).'"');
					
					if($array_opciones[0]){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : $array_opciones[0]);
					}
					
					
				}
				/*if($campo->id_tipo_campo == 11){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_test_html'));
				}*/
				if($campo->id_tipo_campo == 13){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_mail'));
				}
				if($campo->id_tipo_campo == 14){
					$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_time'));
				}
				
				if($campo->id_tipo_campo == 15){
					$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					$unidad_ejemplo = ($campo->default_value) ? $campo->default_value : lang('excel_test_unity');
					$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $unidad_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
				}
				
				// CAMPO MANTENEDORA
				if($campo->id_tipo_campo == 16){
					$datos_campo = json_decode($campo->default_value);
					$id_mantenedora = $datos_campo->mantenedora;
					$id_campo_label = $datos_campo->field_label;
					$id_campo_value = $datos_campo->field_value;

					if($id_mantenedora == "waste_transport_companies"){
						$datos = $this->Fixed_feeder_waste_transport_companies_values_model->get_details(array(
							"id_client" => $id_cliente,
							"id_project" => $id_proyecto
						))->result();
					}elseif($id_mantenedora == "waste_receiving_companies"){
						$datos = $this->Fixed_feeder_waste_receiving_companies_values_model->get_details(array(
							"id_client" => $id_cliente,
							"id_project" => $id_proyecto
						))->result();
					}else{
						$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
						$info_mantenedora = $this->Forms_model->get_one($id_mantenedora);
					}
					
					$array_opciones = array();
					foreach($datos as $index => $row){

						if(!in_array($id_mantenedora, array("waste_transport_companies", "waste_receiving_companies"))){
							$fila = json_decode($row->datos, true);
							$label = $fila[$id_campo_label];
							$value = $fila[$id_campo_value];
							$array_opciones[] = $value;
						
						// Si el campo mantenedora es waste_receiving_companies o waste_transport_companies no tipo patente
						}elseif($id_mantenedora != "waste_transport_companies" && $id_campo_label != 'patent'){
							$label = $row->$id_campo_label;
							$value = $row->$id_campo_value;
							$array_opciones[] = $value;
						}
					
						//$array_opciones[$value] = $label;
					}
					// Si el campo mantenedora almacena Patentes del formulario Empresas transportistas de residuos (Como obtiene todas las patentes de una sola vez no es necesario que este dentro del loop anterior)
					if($id_mantenedora == "waste_transport_companies" && $id_campo_label == 'patent'){
						// $label = $row->$id_field_label;
						$patentes = $this->Patents_model->get_all_where(array(
							"id_client" => $id_cliente,
							"id_project" => $id_proyecto,
							'deleted' => 0
						))->result();
						
						foreach($patentes as $patente){
							$array_opciones[] = $patente->patent;
						}

					}
					
					
					// GUARDO OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN DESDE MANTENEDORA EN HOJA OPCIONES
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
					
					$cantidad_opciones_mantenedora = count($array_opciones);
					if($cantidad_opciones_mantenedora > 0){
						$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_opciones_mantenedora);
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_opciones[0]);
					}
					
					$columna_opciones++;
					
				}	

				// FORMULARIO TRANSPORTE DE PLANTULAS DE PROYECTO CPC
				// Se ponen datos de ejemplo para los campos Peso promedio plantula (kg) y Unidades transportadas
				// en el campo "Peso transportado" se pone una formula para que multiplique los otros 2
				if($id_proyecto == CONST_ID_PROYECTO_CPC && $id_formulario == CONST_ID_FORM_TRANSP_DE_PLANTULAS){
				
					// Campo 'Peso promedio plantula (kg)'
					if($campo->id == CONST_ID_CAMPO_PESO_PROMEDIO_PLANTULA){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', "4");
					}
					
					// Campo 'Unidades transportadas'
					if($campo->id == CONST_ID_CAMPO_UNIDADES_TRANSPORTADAS_PLANTULAS){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', "3");
					}

					// Campo 'Peso transportado'
					if($campo->id == CONST_ID_CAMPO_PESO_TRANSPORTADO_PLANTULAS){
					
						$cell_average_weight_plantula = $this->getNameFromNumber( $columna - 2 ).'2'; 
						
						$cell_transported_units_plantula = $this->getNameFromNumber( $columna - 1 ).'2';

						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', "=$cell_average_weight_plantula*$cell_transported_units_plantula");
					
					}
					
				}

				$columna++;
			}

			if($info_formulario->flujo == "Residuo"){

				// Si el proyecto es CPC se omite el campo
				if($info_proyecto->id != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

					// GUARDO OPCIONES DE SELECT PLACA PATENTE EN HOJA OPCIONES
					$doc->setActiveSheetIndex(1);
					$fila_opcion = 1;
					foreach($array_patents as $patent){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $patent);
						$fila_opcion++;
					}

					$doc->setActiveSheetIndex(0);
					
					// COLUMNA PLACA PATENTE
					$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
					$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
					$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle(lang('excel_error_title'));
					$objValidation->setError(lang('excel_error_text'));

					$cantidad_patents = count($array_patents);

					if($cantidad_patents > 0){
						$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_patents);
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_patents[0]);
					}

					$columna++;
					$columna_opciones++;

				}

				// Si el proyecto es CPC se omite el campo
				if($info_proyecto->id != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

					// GUARDO OPCIONES DE SELECT EMPRESA TRANSPORTISTA DE RESIDUOS EN HOJA OPCIONES
					$doc->setActiveSheetIndex(1);
					$fila_opcion = 1;
					foreach($array_waste_transport_companies as $waste_transport_company){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $waste_transport_company);
						$fila_opcion++;
					}

					$doc->setActiveSheetIndex(0);
					
					// COLUMNA EMPRESA TRANSPORTISTA DE RESIDUOS
					$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
					$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
					$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle(lang('excel_error_title'));
					$objValidation->setError(lang('excel_error_text'));

					$cantidad_waste_transport_companies = count($array_waste_transport_companies);

					if($cantidad_waste_transport_companies > 0){
						$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_waste_transport_companies);
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_waste_transport_companies[1]);
					}
					$columna++;
					$columna_opciones++;

				}

				// Si el proyecto es CPC se omite el campo
				if($info_proyecto->id != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev
						
					// GUARDO OPCIONES DE SELECT EMPRESA RECEPTORA DE RESIDUOS EN HOJA OPCIONES
					$doc->setActiveSheetIndex(1);
					$fila_opcion = 1;
					foreach($array_waste_receiving_companies as $waste_receiving_company){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $waste_receiving_company);
						$fila_opcion++;
					}

					$doc->setActiveSheetIndex(0);

					// COLUMNA EMPRESA RECEPTORA DE RESIDUOS
					$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
					$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
					$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle(lang('excel_error_title'));
					$objValidation->setError(lang('excel_error_text'));

					$cantidad_waste_receiving_companies = count($array_waste_receiving_companies);

					if($cantidad_waste_receiving_companies){
						$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_waste_receiving_companies);
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_waste_receiving_companies[1]);
					}
					$columna++;
					$columna_opciones++;

				}
				

				//COLUMNA FECHA DE RETIRO
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_test_date'));
				// PARA DEJAR FECHA COMO TEXTO
				$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
			}


		}else{// SI NO ES REGISTRO AMBIENTAL

			$columna = 0;
			$columna_opciones = 0; //A
			
			if($info_formulario->id_tipo_formulario == 3){
				if(!$info_formulario->fijo){
					$columna = 1;
					$doc->getActiveSheet()->setCellValue('A1', lang('date'));
					// COLUMNA FECHA
					$doc->getActiveSheet()->setCellValue('A2', lang('excel_test_date'));
					// PARA DEJAR FECHA COMO TEXTO
					$doc->getActiveSheet()->getStyle('A2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

				}elseif($info_formulario->codigo_formulario_fijo != 'or_unidades_funcionales'){
					//COLUMNA AÑO-SEMESTRE
					$columna = 1;
					$columna_opciones++;
					$doc->getActiveSheet()->setCellValue('A1', lang('year').'-'.lang('semester'));
					
					// GUARDO OPCIONES DE SELECT AÑO-SEMESTRE EN HOJA OPCIONES
					$doc->setActiveSheetIndex(1);
					$fila_opcion = 1;

					$array_year_semester = array(
						"2019-I",
						"2019-II",
						"2020-I",
						"2020-II",
						"2021-I",
						"2021-II"
					);
					foreach($array_year_semester as $year_semester){
						$doc->getActiveSheet()->setCellValue('A'.$fila_opcion, $year_semester);
						$fila_opcion++;
					}

					$doc->setActiveSheetIndex(0);
					
					$objValidation = $doc->getActiveSheet()->getCell('A2')->getDataValidation();     
					$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
					$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle(lang('excel_error_title'));
					$objValidation->setError(lang('excel_error_text'));

					$cantidad_year_semester = count($array_year_semester);
					$objValidation->setFormula1('options!$A$1:$A$'.$cantidad_year_semester);
					$doc->getActiveSheet()->setCellValue('A2', $array_year_semester[0]);
					
					//COLUMNA FECHA
					$columna = 2;
					$doc->getActiveSheet()->setCellValue('B1', lang('date'));
					$doc->getActiveSheet()->setCellValue('B2', lang('excel_test_date'));
					// PARA DEJAR FECHA COMO TEXTO
					$doc->getActiveSheet()->getStyle('B2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					
				}
			}
			
			foreach($campos_formulario as $campo){
				if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', $campo->nombre);
				
				if($campo->default_value && $campo->id_tipo_campo != 16){ //SI EL CAMPO TIENE VALOR POR DEFECTO Y NO ES SELECCIÓN DESDE MANTENEDORA
					
					if($campo->id_tipo_campo == 5){	
						$periodo = json_decode($campo->default_value);
						$valor_por_defecto = $periodo->start_date."/".$periodo->end_date;
					} else {
						$valor_por_defecto = $campo->default_value;
					}

					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
					$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
					$comentario->getFont()->setBold(true);
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("default_value_field") . ": ")->getFont()->setBold(true);
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun($valor_por_defecto);
					
					
					if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 13){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("input_text_maxlength_msg"));
					}
					if($campo->id_tipo_campo == 2){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("textarea_maxlength_msg"));
					}
					
					if($campo->habilitado){ //SI EL CAMPO ESTÁ DESHABILITADO
						
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);
						
						if($campo->obligatorio){
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						} else {
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}
						
					} else { //SI EL CAMPO ESTÁ HABILITADO
						
						if($campo->obligatorio){
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						} else {
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}
						
					}

				} else if(!$campo->default_value && $campo->id_tipo_campo != 16){

					if($campo->habilitado){ //SI EL CAMPO ESTÁ DESHABILITADO
						
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
						$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
						$comentario->getFont()->setBold(true);
					
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);

						if($campo->obligatorio){
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						} else {
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}
						
					} else { //SI EL CAMPO ESTÁ HABILITADO
						
						if($campo->obligatorio){
							
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
							$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
							$comentario->getFont()->setBold(true);
						
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							
							
							if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 13){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");

								if($campo->nombre_columna == 'patent'){
									$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("patent_excel_input_info"));
								}else{
									$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("input_text_maxlength_msg"));
								}
							}
							if($campo->id_tipo_campo == 2){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("textarea_maxlength_msg"));
							}
							
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}else{
							
							if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 13){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
								$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"))->getFont()->setBold(true);
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								if($campo->nombre_columna == 'patent'){
									$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("patent_excel_input_info"));
								}else{
									$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("input_text_maxlength_msg"));
								}
							}
							if($campo->id_tipo_campo == 2){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
								$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"))->getFont()->setBold(true);
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("textarea_maxlength_msg"));
							}
							
						}
						
					}

				}

				$columna++;
			}
			
			$columna = 0;
			if($info_formulario->id_tipo_formulario == 3){
				if(!$info_formulario->fijo){
					$columna = 1;
				}elseif($info_formulario->codigo_formulario_fijo != 'or_unidades_funcionales'){
					$columna = 2;
				}
			}
			
			
			foreach($campos_formulario as $campo){
				
				if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				
				if($campo->id_tipo_campo == 1){
					if($campo->nombre_columna == 'patent'){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : 'AA1234;AAAA12');
					}else{
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_text'));
					}
				}
				if($campo->id_tipo_campo == 2){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_textarea'));
				}
				if($campo->id_tipo_campo == 3){
					//$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					$numero_ejemplo = ($campo->default_value) ? $campo->default_value : lang('excel_test_number');
					$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $numero_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_number'));
				}
				if($campo->id_tipo_campo == 4){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_date'));
					// PARA DEJAR FECHA COMO TEXTO
					$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					
				}
				if($campo->id_tipo_campo == 5){
					if($campo->default_value){
						$periodo = json_decode($campo->default_value);
						$valor_por_defecto = $periodo->start_date."/".$periodo->end_date;
					}
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($valor_por_defecto) ? $valor_por_defecto : lang('excel_test_period'));
				}
				if($campo->id_tipo_campo == 6){
					
					if($campo->html_name == "2_or_unidades_funcionales"){
						
						$unidades_funcionales_proyecto = $this->Functional_units_model->get_all_where(array(
							"id_cliente" => $id_cliente,
							"id_proyecto" => $id_proyecto,
							"deleted" => 0
						))->result();
						
						$array_opciones = array();
						foreach($unidades_funcionales_proyecto as $uf){
							$label = $uf->nombre;
							$value = $uf->id;
							//$array_opciones[$value] = $label;
							$array_opciones[] = $label;
						}
						
						//array_shift($array_opciones);
						
					} else {
						
						$datos_campo = json_decode($campo->opciones);
						
						$array_opciones = array();
						foreach($datos_campo as $row){
							$label = $row->text;
							$value = $row->value;
							$array_opciones[] = $label;
						}
						
						array_shift($array_opciones);
						
					}

					// GUARDO OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN EN HOJA OPCIONES
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
					
					$cantidad_opciones_seleccion = count($array_opciones);
					if($cantidad_opciones_seleccion > 0){
						$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_opciones_seleccion);
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : $array_opciones[0]);
					}
					
					$columna_opciones++;
					
				}
				if($campo->id_tipo_campo == 7){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_select_multiple'));
				}
				if($campo->id_tipo_campo == 8){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_rut'));
				}
				if($campo->id_tipo_campo == 9){
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_test_radio'));
					
					$datos_campo = json_decode($campo->opciones);
					
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
					//$objValidation->setPromptTitle(lang('excel_prompt_title').' "'.$campo->nombre.'"');
					//$objValidation->setPrompt(lang('excel_prompt_text').' "'.$info_mantenedora->nombre.'"');
					$objValidation->setFormula1('"'.implode(",", $array_opciones).'"');
					
					if($array_opciones[0]){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : $array_opciones[0]);
					}
					
					
				}
				
				if($campo->id_tipo_campo == 13){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_mail'));
				}
				if($campo->id_tipo_campo == 14){
					$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_time'));
				}
				if($campo->id_tipo_campo == 15){
					$unidad_ejemplo = ($campo->default_value) ? $campo->default_value : lang('excel_test_unity');
					$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $unidad_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_unity'));
				}
				if($campo->id_tipo_campo == 16){
					$datos_campo = json_decode($campo->default_value);
					$id_mantenedora = $datos_campo->mantenedora;
					$id_campo_label = $datos_campo->field_label;
					$id_campo_value = $datos_campo->field_value;

					if($id_mantenedora == "waste_transport_companies"){
						$datos = $this->Fixed_feeder_waste_transport_companies_values_model->get_details(array(
							"id_client" => $id_cliente,
							"id_project" => $id_proyecto
						))->result();
						$info_mantenedora = lang("waste_transport_companies");
					}elseif($id_mantenedora == "waste_receiving_companies"){
						$datos = $this->Fixed_feeder_waste_receiving_companies_values_model->get_details(array(
							"id_client" => $id_cliente,
							"id_project" => $id_proyecto
						))->result();
						$info_mantenedora = lang("waste_receiving_companies");
					}else{
						$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
						$info_mantenedora = $this->Forms_model->get_one($id_mantenedora);
					}
					
					$array_opciones = array();
					foreach($datos as $index => $row){

						if(!in_array($id_mantenedora, array("waste_transport_companies", "waste_receiving_companies"))){
							$fila = json_decode($row->datos, true);
							$label = $fila[$id_campo_label];
							$value = $fila[$id_campo_value];
							$array_opciones[] = $value;
						// Si el campo mantenedora es waste_receiving_companies o waste_transport_companies no tipo patente
						}elseif($id_mantenedora != "waste_transport_companies" && $id_campo_label != 'patent'){
							$label = $row->$id_campo_label;
							$value = $row->$id_campo_value;
							$array_opciones[] = $value;
						}
				
						//$array_opciones[$value] = $label;
					}
					// Si el campo mantenedora almacena Patentes del formulario Empresas transportistas de residuos (Como obtiene todas las patentes de una sola vez no es necesario que este dentro del loop anterior)
					if($id_mantenedora == "waste_transport_companies" && $id_campo_label == 'patent'){
						// $label = $row->$id_field_label;
						$patentes = $this->Patents_model->get_all_where(array(
							"id_client" => $id_cliente,
							"id_project" => $id_proyecto,
							'deleted' => 0
						))->result();
						
						foreach($patentes as $patente){
							$array_opciones[] = $patente->patent;
						}

					}
					
					// GUARDO OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN DESDE MANTENEDORA EN HOJA OPCIONES
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
					$objValidation->setPromptTitle(lang('excel_prompt_title').' "'.$campo->nombre.'"');
					$info_mantenedora = $info_mantenedora->nombre ? $info_mantenedora->nombre : $info_mantenedora;
					$objValidation->setPrompt(lang('excel_prompt_text').' "'.$info_mantenedora.'"');
					
					$cantidad_opciones_mantenedora = count($array_opciones);
					if($cantidad_opciones_mantenedora > 0){
						$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_opciones_mantenedora);
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_opciones[0]);
					}
					
					$columna_opciones++;
					
				}
				$columna++;
			}
		}
		
		foreach(range('A', $this->getNameFromNumber($columna)) as $columnID) {
			$doc->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		
		//$doc->getActiveSheet()->getProtection()->setSheet(true);
		//$doc->getActiveSheet()->getStyle('A2:B2')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

		$nombre_hoja = strlen($info_formulario->nombre)>31?substr($info_formulario->nombre, 0, 28).'...':$info_formulario->nombre;
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		$nombre_archivo = $info_cliente->sigla.'_'.$info_formulario->codigo.'_plantilla';
		
		// OCULTO HOJA OPTIONS
		$doc->getSheetByName('options')->setSheetState(PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN);

		//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		//header('Content-Disposition: attachment;filename="bulk_load_template.xlsx"'); //tell browser what's the file name
		//header('Cache-Control: max-age=0'); //no cache
				
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007'); 
		
		if (!file_exists(__DIR__.'/../../files/mimasoft_files/client_'.$id_cliente.'/project_'.$id_proyecto.'/form_'.$id_formulario.'/')) {
			mkdir(__DIR__.'/../../files/mimasoft_files/client_'.$id_cliente.'/project_'.$id_proyecto.'/form_'.$id_formulario.'/', 0777, true);
		}
		
		$objWriter->save('files/mimasoft_files/client_'.$id_cliente.'/project_'.$id_proyecto.'/form_'.$id_formulario.'/'.$nombre_archivo.'.xlsx');

		if(!file_exists(__DIR__.'/../../files/mimasoft_files/client_'.$id_cliente.'/project_'.$id_proyecto.'/form_'.$id_formulario.'/'.$nombre_archivo.'.xlsx')) {
			echo json_encode(array("success" => false, 'message' => lang('excel_error_occurred')));
			exit();
		}
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<div class="col-md-12">';
		$html .= '<div class="fa fa-file-excel-o font-22 mr10"></div>';
		$html .= '<a href="'.get_uri("setting_bulk_load/download_form_template/".$id_cliente."/".$id_proyecto."/".$id_formulario).'">'.$nombre_archivo.'.xlsx</a>';
		$html .= '</div>';
		$html .= '</div>';
		
		echo json_encode($html);
		exit();
		
    }
	
	function clean($string){
	   $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
	   return strtolower(preg_replace('/[^A-Za-z0-9\_]/', '', $string)); // Removes special chars.	    
	}
	
	function download_form_template($id_cliente, $id_proyecto, $id_formulario) {
		
		if(!$id_cliente && !$id_proyecto && !$id_formulario){
			redirect("forbidden");
		}
		
		$info_cliente = $this->Clients_model->get_one($id_cliente);
		$info_formulario = $this->Forms_model->get_one($id_formulario);

		if($id_formulario == "waste_transport_companies"){
			$codigo = $this->clean(lang("waste_transport_companies"));
		}elseif($id_formulario == "waste_receiving_companies"){
			$codigo = $this->clean(lang("waste_receiving_companies"));
		}else{
			$codigo = $info_formulario->codigo;
		}

		$nombre_archivo = $info_cliente->sigla.'_'.$codigo.'_plantilla';
		$file_data = serialize(array(array("file_name" => $nombre_archivo.".xlsx")));
        download_app_files("files/mimasoft_files/client_".$id_cliente."/project_".$id_proyecto."/form_".$id_formulario."/", $file_data, false);
		
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


    function save() {
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$info_proyecto = $this->Projects_model->get_one($id_proyecto);
		$id_tipo_formulario = $this->input->post('form_type');
        $id_formulario = $this->input->post('form');
		$file = $this->input->post('archivo_importado');

		if($id_formulario == "waste_transport_companies"){
			$info_formulario = new stdClass();
			$info_formulario->id_tipo_formulario = 2;
			$info_formulario->nombre = lang("waste_transport_companies");
			$info_formulario->codigo = "";
		}elseif($id_formulario == "waste_receiving_companies"){
			$info_formulario = new stdClass();
			$info_formulario->id_tipo_formulario = 2;
			$info_formulario->nombre = lang("waste_receiving_companies");
			$info_formulario->codigo = "";
		}else{
			$info_formulario = $this->Forms_model->get_one($id_formulario);
		}

        //$this->access_only_allowed_members_or_client_contact($client_id);

        validate_submitted_data(array(
            "form_type" => "numeric",
			//"form" => "numeric",
			"file" => "required",
        ));
		
		$archivo_subido = move_temp_file($file, "files/carga_masiva/", "", "", $file);
		if($archivo_subido){
			$this->load->library('excel');
			
			$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);
			$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);
			$worksheet = $excelObj->getSheet(0);
			$lastRow = $worksheet->getHighestRow();
			
			// COMPROBACION DE DATOS CORRECTOS
			$num_errores = 0;
			$msg_obligatorio = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_obligatory_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_formato = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_format_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_columna = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_column_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_date_range = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_date_range_field').'"><i class="fa fa-question-circle"></i></span>';
			
			if(!$info_formulario->fijo){
				// Aunque su nombre hace referencia a fixed_feeders, tambien trae los campos de formularios no fijos (Forms_model)
				$campos_formulario = $this->get_fields_of_fixed_feeders($id_formulario);
				//$campos_formulario = $this->Forms_model->get_fields_of_form($id_formulario)->result();
			} else {
				$campos_formulario = $this->Fixed_fields_model->get_all_where(array(
					"codigo_formulario_fijo" => $info_formulario->codigo_formulario_fijo,
					"deleted" => 0
				))->result();
			}
			
			$html = '<table class="table table-responsive table-striped">';

			// Columna del excel
			$cont = 0;

			if($id_tipo_formulario == 1){// SI ES REGISTRO AMBIENTAL
			
				$cats = $this->Categories_model->get_categories_of_material_of_form($id_formulario)->result();
				$categorias = array();
				foreach($cats as $cat){
					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $cat->id, 'id_cliente' => $id_cliente, "deleted" => 0));
					if($row_alias->alias){
						$categorias[] = $row_alias->alias;
					}else{
						$categorias[] = $cat->nombre;
					}
				}
				
				//INFO FORMULARIO
				$info_formulario = $this->Forms_model->get_one($id_formulario);

				//$list_data = $this->Environmental_records_model->get_values_of_record($id_formulario)->result();
				$html .= '<thead><tr>';
				$html .= '<th></th>';
				
				// CAMPO FECHA DE ALMACENAMIENTO (EX FECHA DE REGISTRO)
				$letra_columna = $this->getNameFromNumber($cont);
				
				if(lang('storage_date') == $worksheet->getCell($letra_columna.'1')->getValue()){
				
					$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';
				
				}else if(lang('transfer_date_to_pick_up_point') == $worksheet->getCell($letra_columna.'1')->getValue() && $id_proyecto == CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev
					//El nombre de la columna es distinto si estamos en CPC
				
					$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';
				
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($letra_columna.'1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}

				$cont++;

				// CAMPO MES
				if($info_formulario->flujo == "Residuo"){
					
					$letra_columna = $this->getNameFromNumber($cont);

					if(lang('month') == $worksheet->getCell($letra_columna.'1')->getValue()){
				
						$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';
					
					}
					else{
						$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($letra_columna.'1')->getValue().' '.$msg_columna.'</th>';
						$num_errores++;
					}
					$cont++;
				}
				
				// CAMPO CATEGORIA
				$letra_columna = $this->getNameFromNumber($cont);

				if(lang('category') == $worksheet->getCell($letra_columna.'1')->getValue()){
						$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($letra_columna.'1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}

				$cont++;
				
				// CAMPO UNIDAD
				$letra_columna = $this->getNameFromNumber($cont);

				$formulario_unidad = json_decode($info_formulario->unidad, true);
				$unidad = $this->Unity_model->get_one($formulario_unidad["unidad_id"]);
				$campo_unidad = $formulario_unidad["nombre_unidad"];
				//$nombre_unidad = $campo_unidad . " (" . $unidad->nombre. ")";
				$nombre_unidad = lang("quantity");
				
				if($nombre_unidad == $worksheet->getCell($letra_columna.'1')->getValue()){
					$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($letra_columna.'1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}

				$cont++;
				
				if($info_formulario->flujo == "Consumo"){
					
					$data_tipo_origen = json_decode($info_formulario->tipo_origen);
					$id_tipo_origen = $data_tipo_origen->type_of_origin;
					$disabled_field_tipo_origen = (boolean)$data_tipo_origen->disabled_field;
					$default_matter = ($data_tipo_origen->default_matter)?$data_tipo_origen->default_matter:NULL;
					
					if($id_tipo_origen == "1"){ // id 1: matter
						
						$array_tipos_origen = array();//array("" => "-");
						$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
							"id_tipo_origen" => $id_tipo_origen,
							"deleted" => 0
						))->result();
						
						foreach($tipos_origen_materia as $tipo_origen_materia){
							$array_tipos_origen[$tipo_origen_materia->id] = lang($tipo_origen_materia->nombre);
						}
					}
					
					if($id_tipo_origen == "2"){ // id 2: energy
						
						$default_matter = 2;
						//$array_tipos_origen = array("" => "-");
						$tipos_origen = $this->EC_Types_of_origin_model->get_all()->result();
						foreach($tipos_origen as $tipo_origen){
							$array_tipos_origen[$tipo_origen->id] = lang($tipo_origen->nombre);
						}
					}
					
					$letra_columna = $this->getNameFromNumber($cont);

					if(lang('type') == $worksheet->getCell($letra_columna.'1')->getValue()){
						$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';
					}else{
						$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($letra_columna.'1')->getValue().' '.$msg_columna.'</th>';
						$num_errores++;
					}

					$cont++;
					// $cont = 4;

				}elseif($info_formulario->flujo == "Residuo"){


					// CAMPO FUENTE
					// Si el proyecto es CPC se omite el campo
					if($id_proyecto != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

						$array_sources = array();
						$sources = $this->Sources_model->get_details(array("id_proyecto" => $id_proyecto))->result();
						foreach($sources as $source){
							$array_sources[$source->id] = lang($source->name);
						}
						
						$letra_columna = $this->getNameFromNumber($cont);

						if(lang('source') == $worksheet->getCell($letra_columna.'1')->getValue()){

							$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';

						}else if(lang('waste_generating_area') == $worksheet->getCell($letra_columna.'1')->getValue() && $id_proyecto == CONST_ID_PROYECTO_QALI){	// ID proyecto QALI en dev
							
							$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';

						}else{
							$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($letra_columna.'1')->getValue().' '.$msg_columna.'</th>';
							$num_errores++;
						}

						$cont++;
					}

					// CAMPO TIPO DE TRATAMIENTO

					$array_tipo_tratamiento = array();
					$tipos_tratamiento = $this->Tipo_tratamiento_model->get_details(array("id_proyecto" => $id_proyecto))->result();
					foreach($tipos_tratamiento as $tipo_tratamiento){
						$array_tipo_tratamiento[$tipo_tratamiento->id] = $tipo_tratamiento->nombre;
					}

					$letra_columna = $this->getNameFromNumber($cont);

					if(lang('type_of_treatment') == $worksheet->getCell($letra_columna.'1')->getValue()){
						$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';
					}else{
						$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($letra_columna.'1')->getValue().' '.$msg_columna.'</th>';
						$num_errores++;
					}

					$cont++;
					
					/*
					// CAMPO FECHA DE RETIRO
					
					if(lang('retirement_date') == $worksheet->getCell('E1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('E1')->getValue().'</th>';
					}else{
						$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('E1')->getValue().' '.$msg_columna.'</th>';
						$num_errores++;
					}
					*/
					
					// $cont = 5;
				}elseif($info_formulario->flujo == "No Aplica"){
					
					$data_tipo_no_aplica = json_decode($info_formulario->tipo_por_defecto);
					$default_type = ($data_tipo_no_aplica->default_type)?$data_tipo_no_aplica->default_type:NULL;
					$disabled_field_no_aplica = (boolean)$data_tipo_no_aplica->disabled_field;
					
					$array_tipos_no_aplica = array();//array("" => "-");
					$tipos_no_aplica = $this->EC_Types_no_apply_model->get_all()->result();
					
					foreach($tipos_no_aplica as $tipo_no_aplica){
						$array_tipos_no_aplica[$tipo_no_aplica->id] = lang($tipo_no_aplica->nombre);
					}

					$letra_columna = $this->getNameFromNumber($cont);
					
					if(lang('type') == $worksheet->getCell($letra_columna.'1')->getValue()){
						$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';
					}else{
						$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($letra_columna.'1')->getValue().' '.$msg_columna.'</th>';
						$num_errores++;
					}
					
					$cont++;
					// $cont = 4;
				}else{
					
				}
				
				foreach($campos_formulario as $campo){
					if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
						continue;
					}
					$letra_columna = $this->getNameFromNumber($cont);
					$valor_columna = $worksheet->getCell($letra_columna.'1')->getValue();

					if($campo->id_tipo_campo == 15){ // UNIDAD
						$column_options = json_decode($campo->opciones, true);
						$id_unidad_column = $column_options[0]["id_unidad"];
						$unidad_column = $this->Unity_model->get_one($id_unidad_column);
						$campo->nombre .= ' ('.$unidad_column->nombre.')';
					}
					
					//echo "se compara valor excel:".$valor_columna." con valor base de datos:".$campo->nombre."<br>";
					if($campo->nombre == $valor_columna){
						$html .= '<th>'.$valor_columna.'</th>';
					}else{
						$html .= '<th class="error app-alert alert-danger">'.$valor_columna.' '.$msg_columna.'</th>';
						$num_errores++;
					}
					$cont++;
				}

				if($info_formulario->flujo == "Residuo"){

					// CAMPO PLACA PATENTE 
					// Si el proyecto es CPC se omite el campo
					if($id_proyecto != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev
					
						$letra_columna = $this->getNameFromNumber($cont);
						if(lang('patent_plate') == $worksheet->getCell($letra_columna.'1')->getValue()){
							$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';
						}else{
							$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($letra_columna.'1')->getValue().' '.$msg_columna.'</th>';
							$num_errores++;
						}
						$cont++;
					
					}


					// CAMPO EMP. TRANSPORTISTA DE RESIDUOS
					// Si el proyecto es CPC se omite el campo
					if($id_proyecto != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

						$letra_columna = $this->getNameFromNumber($cont);
						if(lang('waste_transport_company') == $worksheet->getCell($letra_columna.'1')->getValue()){
							$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';
						}else{
							$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($letra_columna.'1')->getValue().' '.$msg_columna.'</th>';
							$num_errores++;
						}
						$cont++;
					
					}

					// CAMPO EMPRESAS RECEPTORAS DE RESIDUOS
					// Si el proyecto es CPC se omite el campo
					if($id_proyecto != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

						$letra_columna = $this->getNameFromNumber($cont);
						if(lang('waste_receiving_company') == $worksheet->getCell($letra_columna.'1')->getValue()){
							$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';
						}else{
							$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($letra_columna.'1')->getValue().' '.$msg_columna.'</th>';
							$num_errores++;
						}
						$cont++;

					}
					
					// CAMPO FECHA DE RETIRO
					$letra_columna = $this->getNameFromNumber($cont);
					if(lang('retirement_date') == $worksheet->getCell($letra_columna.'1')->getValue()){
					
						$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';
					
					}else if(lang('point_withdrawal_retirement_date') == $worksheet->getCell($letra_columna.'1')->getValue() && $id_proyecto == CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev
					//El nombre de la columna es distinto si estamos en CPC
					
						$html .= '<th>'.$worksheet->getCell($letra_columna.'1')->getValue().'</th>';

					}else{
						$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($letra_columna.'1')->getValue().' '.$msg_columna.'</th>';
						$num_errores++;
					}
					$cont++;

				}


				$html .= '</tr></thead>';
				$html .= '<tbody>';
				
				// DATOS DEL CUERPO
				for($row = 2; $row <= $lastRow; $row++){
					$html .= '<tr>';
					$html .= '<td>'.$row.'</td>';
					
					// Columna del excel
					$cont = 0;

					// CELDA FECHA
					$letra_columna = $this->getNameFromNumber($cont);

					$fecha_excel = $worksheet->getCell($letra_columna.$row)->getValue();
					if($this->validateDate($fecha_excel)){
						$html .= '<td>'.$fecha_excel.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$fecha_excel.' '.$msg_formato.'</td>';
						$num_errores++;
					}

					$cont++;

					// CELDA MES
					if($info_formulario->flujo == "Residuo"){

						$letra_columna = $this->getNameFromNumber($cont);

						$mes_excel = $worksheet->getCell($letra_columna.$row)->getValue();
						
						if(in_array($mes_excel, CONST_ARRAY_MESES)){
							$html .= '<td>'.$mes_excel.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$mes_excel.' '.$msg_formato.'</td>';
							$num_errores++;
						}

						$cont++;
					}
					
					// CELDA CATEGORIA
					$letra_columna = $this->getNameFromNumber($cont);

					$categoria_excel = $worksheet->getCell($letra_columna.$row)->getValue();
					
					if(in_array($categoria_excel, $categorias)){
						$html .= '<td>'.$categoria_excel.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$categoria_excel.' '.$msg_formato.'</td>';
						$num_errores++;
					}

					$cont++;
					
					// CELDA UNIDAD
					$letra_columna = $this->getNameFromNumber($cont);

					$unidad_excel = $worksheet->getCell($letra_columna.$row)->getValue();
					if(strlen(trim($unidad_excel)) > 0){
						if(is_numeric($unidad_excel)){
							$html .= '<td>'.$unidad_excel.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$unidad_excel.' '.$msg_formato.'</td>';
							$num_errores++;
						}
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$unidad_excel.' '.$msg_formato.'</td>';
						$num_errores++;
					}

					$cont++;
					
					if($info_formulario->flujo == "Consumo"){
						
						// CONSULTO LA DEFINICION DEL TIPO DE ORIGEN PARA VER SI ESTÁ DESHABILITADO
						if($id_tipo_origen == "1"){ // id 1: matter
							if($disabled_field_tipo_origen){
								$array_tipos_origen = array($default_matter => $array_tipos_origen[$default_matter]);
							}
						}
						if($id_tipo_origen == "2"){ // id 2: energy
							$array_tipos_origen = array($id_tipo_origen => $array_tipos_origen[$id_tipo_origen]);
						}
						//
						
						// CELDA TIPO
						$letra_columna = $this->getNameFromNumber($cont);

						$tipo_origen_excel = $worksheet->getCell($letra_columna.$row)->getValue();
						if(in_array($tipo_origen_excel, $array_tipos_origen)){
							$html .= '<td>'.$tipo_origen_excel.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$tipo_origen_excel.' '.$msg_formato.'</td>';
							$num_errores++;
						}

						$cont++;
						
						// $cont = 4;
						
					}elseif($info_formulario->flujo == "Residuo"){

						// CELDA FUENTE
						// Si el proyecto es CPC se omite el campo
						if($id_proyecto != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

							$array_sources = array();
							$sources = $this->Sources_model->get_details(array("id_proyecto" => $id_proyecto))->result();
							foreach($sources as $source){
								$array_sources[$source->id] = lang($source->name);
							}
							
							$letra_columna = $this->getNameFromNumber($cont);

							$source_excel = $worksheet->getCell($letra_columna.$row)->getValue();
							if(in_array($source_excel, $array_sources)){
								$html .= '<td>'.$source_excel.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$source_excel.' '.$msg_formato.'</td>';
								$num_errores++;
							}

							$cont++;
						}

						
						// CONSULTO LA DEFINICION DEL TIPO DE TRATAMIENTO PARA VER SI ESTÁ DESHABILITADO
						$data_tipo_tratamiento = json_decode($info_formulario->tipo_tratamiento);
						$id_tipo_tratamiento_defecto = $data_tipo_tratamiento->tipo_tratamiento;
						$disabled_field = (boolean)$data_tipo_tratamiento->disabled_field;
						if($disabled_field){
							$array_tipo_tratamiento = array($id_tipo_tratamiento_defecto => $array_tipo_tratamiento[$id_tipo_tratamiento_defecto]);
						}
						//
						
						// CELDA TIPO DE TRATAMIENTO
						$letra_columna = $this->getNameFromNumber($cont);

						$tipo_tratamiento_excel = $worksheet->getCell($letra_columna.$row)->getValue();
						if(in_array($tipo_tratamiento_excel, $array_tipo_tratamiento)){
							$html .= '<td>'.$tipo_tratamiento_excel.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$tipo_tratamiento_excel.' '.$msg_formato.'</td>';
							$num_errores++;
						}

						$cont++;

						/*
						// CELDA FECHA DE RETIRO
						$fecha_excel = $worksheet->getCell('E'.$row)->getValue();
						if($this->validateDate($fecha_excel) || $fecha_excel == ""){
							$html .= '<td>'.$fecha_excel.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$fecha_excel.' '.$msg_formato.'</td>';
							$num_errores++;
						}
						*/
						
						// $cont = 5;
					}elseif($info_formulario->flujo == "No Aplica"){
						
						// CONSULTO LA DEFINICION DEL TIPO DE NO APLICA PARA VER SI ESTÁ DESHABILITADO
						if($disabled_field_no_aplica){
							$array_tipos_no_aplica = array($default_type => $array_tipos_no_aplica[$default_type]);
						}
						//
						
						// CELDA TIPO
						$letra_columna = $this->getNameFromNumber($cont);

						$tipo_no_aplica_excel = $worksheet->getCell($letra_columna.$row)->getValue();
						if(in_array($tipo_no_aplica_excel, $array_tipos_no_aplica)){
							$html .= '<td>'.$tipo_no_aplica_excel.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$tipo_no_aplica_excel.' '.$msg_formato.'</td>';
							$num_errores++;
						}
						
						$cont++;
						// $cont = 4;
						
					}else{
						
					}
					
					// OTRAS CELDAS
					
					foreach($campos_formulario as $campo){
						if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
							continue;
						}
						$letra_columna = $this->getNameFromNumber($cont);
						$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
						
						if($campo->id_tipo_campo == 1){
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
						if($campo->id_tipo_campo == 2){
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
						if($campo->id_tipo_campo == 3){
							
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
						if($campo->id_tipo_campo == 4){
							
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
						if($campo->id_tipo_campo == 5){
							
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
						if($campo->id_tipo_campo == 6){
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
								$opciones[] = $op->value;
							}
							
							if(in_array($valor_columna, $opciones)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
							
							
						}
						/*if($campo->id_tipo_campo == 7){//select_multiple
							
						}*/
						if($campo->id_tipo_campo == 8){
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
						if($campo->id_tipo_campo == 9){
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
						if($campo->id_tipo_campo == 13){
							
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
						if($campo->id_tipo_campo == 14){
							// ESTE TIPO DE CAMPO RECIBE EN INGRESO LA HORA EN FORMATO 24HRS SIEMPRE
							
							if($campo->obligatorio){
								if(strlen($valor_columna) == 5){// 12:00
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
						if($campo->id_tipo_campo == 15){
							
							// Código para FORMULARIO TRANSPORTE DE PLANTULAS DE PROYECTO CPC
							// En el campo "Peso transportado" se pone una formula por lo que se debe leer el valor que resulta de la aplicación de la formula
							if( $id_proyecto == CONST_ID_PROYECTO_CPC 
							&& $id_formulario == CONST_ID_FORM_TRANSP_DE_PLANTULAS 
							&& $campo->id == CONST_ID_CAMPO_PESO_TRANSPORTADO_PLANTULAS ){

								$valor_columna = $worksheet->getCell($letra_columna.$row)->getCalculatedValue();
							}

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
						
						if($campo->id_tipo_campo == 16){
							$datos_campo = json_decode($campo->default_value);
							$id_mantenedora = $datos_campo->mantenedora;
							$id_campo_label = $datos_campo->field_label;
							$id_campo_value = $datos_campo->field_value;

							if($id_mantenedora == "waste_transport_companies"){
								$datos = $this->Fixed_feeder_waste_transport_companies_values_model->get_details(array(
									"id_client" => $id_cliente,
									"id_project" => $id_proyecto
								))->result();
							}elseif($id_mantenedora == "waste_receiving_companies"){
								$datos = $this->Fixed_feeder_waste_receiving_companies_values_model->get_details(array(
									"id_client" => $id_cliente,
									"id_project" => $id_proyecto
								))->result();
							}else{
								$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
							}

							//$info_mantenedora = $this->Forms_model->get_one($id_mantenedora);
							
							$array_opciones = array();
							foreach($datos as $index => $fila){
								if(!in_array($id_mantenedora, array("waste_transport_companies", "waste_receiving_companies"))){
									$datos_decoded = json_decode($datos_decoded->datos, true);
									$label = $datos_decoded[$id_campo_label];
									$value = $datos_decoded[$id_campo_value];
									$array_opciones[] = $value;
								
								// Si el campo mantenedora es waste_receiving_companies o waste_transport_companies no tipo patente
								}elseif($id_mantenedora != "waste_transport_companies" && $id_campo_label != 'patent'){
									$label = $fila->$id_campo_label;
									$value = $fila->$id_campo_value;
									$array_opciones[] = $value;
								}
							
							}
		
							// Si el campo mantenedora almacena Patentes del formulario Empresas transportistas de residuos (Como obtiene todas las patentes de una sola vez no es necesario que este dentro del loop anterior)
							if($id_mantenedora == "waste_transport_companies" && $id_campo_label == 'patent'){
								// $label = $row->$id_field_label;
								$patentes = $this->Patents_model->get_all_where(array(
									"id_client" => $id_cliente,
									"id_project" => $id_proyecto,
									'deleted' => 0
								))->result();
								
								foreach($patentes as $patente){
									$array_opciones[] = $patente->patent;
								}

							}

							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									if(in_array($valor_columna, $array_opciones)){
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
								if($valor_columna == "" || in_array($valor_columna, $array_opciones)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}
							
						}
						
						$cont++;

					}

					if($info_formulario->flujo == "Residuo"){

						

						// PATENTE
						// Si el proyecto es CPC se omite el campo
						if($id_proyecto != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

							$letra_columna = $this->getNameFromNumber($cont);
							$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();

							$patentes = $this->Patents_model->get_all_where(array(
								"id_client" => $id_cliente,
								"id_project" => $id_proyecto,
								'deleted' => 0
							))->result();

							$array_patents = array();
							foreach($patentes as $patente){
								$array_patents[] = $patente->patent;
							}
						
							if(in_array($valor_columna, $array_patents)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
							$cont++;

						}


						// EMP. TRANSPORTISTA DE RESIDUOS
						// Si el proyecto es CPC se omite el campo
						if($id_proyecto != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

							$letra_columna = $this->getNameFromNumber($cont);
							$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();

							$waste_transport_companies = $this->Fixed_feeder_waste_transport_companies_values_model->get_details(array(
								"id_client" => $id_cliente,
								"id_project" => $id_proyecto
							))->result();
							$array_waste_transport_companies = array();
							foreach($waste_transport_companies as $company){
								$array_waste_transport_companies[$company->id] = $company->company_name;
							}
							if(in_array($valor_columna, $array_waste_transport_companies)){
								$html .= '<td>'.$valor_columna.'</td>';
							} elseif ($valor_columna == "" || $valor_columna == "-"){
								$valor_columna = "";
								$html .= '<td>'.$valor_columna.'</td>';
							} else {
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
							$cont++;

						}

						

						// EMP. RECEPTORA DE RESIDUOS
						
						// Si el proyecto es CPC se omite el campo
						if($id_proyecto != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

							$letra_columna = $this->getNameFromNumber($cont);
							$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();

							$waste_receiving_companies = $this->Fixed_feeder_waste_receiving_companies_values_model->get_details(array(
								"id_client" => $id_cliente,
								"id_project" => $id_proyecto
							))->result();
							$array_waste_receiving_companies = array();
							foreach($waste_receiving_companies as $company){
								$array_waste_receiving_companies[$company->id] = $company->company_name;
							}
							if(in_array($valor_columna, $array_waste_receiving_companies)){
								$html .= '<td>'.$valor_columna.'</td>';
							} elseif ($valor_columna == "" || $valor_columna == "-"){
								$valor_columna = "";
								$html .= '<td>'.$valor_columna.'</td>';
							} else {
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
							$cont++;
						
						}

						
						// FECHA DE RETIRO
						$letra_columna = $this->getNameFromNumber($cont);
						$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
						if($this->validateDate($fecha_excel) || $fecha_excel == ""){
							$html .= '<td>'.$valor_columna.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
							$num_errores++;
						}
						$cont++;

					}
					
					
					$html .= '</tr>';
					
					
					
				}
				
				$html .= '</tbody>';
				$html .= '</table>';

				
				
				if($num_errores > 0){
					echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => $html));
				}else{
					$this->bulk_load($id_cliente, $id_proyecto, $id_formulario, $archivo_subido);
					//echo json_encode(array("success" => true, 'message' => lang('record_saved'), 'table' => $html));
				}
				
				exit();
				
			}else{// SI NO ES REGISTRO AMBIENTAL
				
				
				$html .= '<thead><tr>';
				$html .= '<th></th>';

				$cont = 0;
				
				if($id_tipo_formulario == 3){
					if(!$info_formulario->fijo){
						if(lang('date') == $worksheet->getCell('A1')->getValue()){
							$html .= '<th>'.$worksheet->getCell('A1')->getValue().'</th>';
						}else{
							$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('A1')->getValue().' '.$msg_columna.'</th>';
							$num_errores++;
						}
						$cont = 1;

					}elseif($info_formulario->codigo_formulario_fijo != 'or_unidades_funcionales'){
						//COLUMNA AÑO-SEMESTRE
						if(lang('year').'-'.lang('semester') == $worksheet->getCell('A1')->getValue()){
							$html .= '<th>'.$worksheet->getCell('A1')->getValue().'</th>';
						}else{
							$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('A1')->getValue().' '.$msg_columna.'</th>';
							$num_errores++;
						}
						$cont = 1;

						//COLUMNA FECHA
						if(lang('date') == $worksheet->getCell('B1')->getValue()){
							$html .= '<th>'.$worksheet->getCell('B1')->getValue().'</th>';
						}else{
							$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('B1')->getValue().' '.$msg_columna.'</th>';
							$num_errores++;
						}
						$cont = 2;
					}
				}
				
				foreach($campos_formulario as $campo){
					if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
						continue;
					}
					$letra_columna = $this->getNameFromNumber($cont);
					$valor_columna = $worksheet->getCell($letra_columna.'1')->getValue();
					
					//echo "se compara valor excel:".$valor_columna." con valor base de datos:".$campo->nombre."<br>";
					if($campo->nombre == $valor_columna){
						$html .= '<th>'.$valor_columna.'</th>';
					}else{
						$html .= '<th class="error app-alert alert-danger">'.$valor_columna.' '.$msg_columna.'</th>';
						$num_errores++;
					}
					$cont++;
				}
				
				$html .= '</tr></thead>';
				$html .= '<tbody>';
				
				// DATOS DEL CUERPO
				for($row = 2; $row <= $lastRow; $row++){
					$html .= '<tr>';
					$html .= '<td>'.$row.'</td>';

					// OTRAS CELDAS
					$cont = 0;
					if($id_tipo_formulario == 3){
						
						if(!$info_formulario->fijo){
							
							// CELDA FECHA
							$fecha_excel = $worksheet->getCell('A'.$row)->getValue();
							if($this->validateDate($fecha_excel)){
								$html .= '<td>'.$fecha_excel.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$fecha_excel.' '.$msg_formato.'</td>';
								$num_errores++;
							}
							$cont = 1;
							
						}elseif($info_formulario->codigo_formulario_fijo != 'or_unidades_funcionales'){
							
							// CELDA AÑO-SEMESTRE
							$year_semester = $worksheet->getCell('A'.$row)->getValue();
							$array_year_semester = array(
								"2019-I",
								"2019-II",
								"2020-I",
								"2020-II",
								"2021-I",
								"2021-II"
							);
							
							if(in_array($year_semester, $array_year_semester)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
							$cont = 1;

							// CELDA FECHA
							$fecha_excel = $worksheet->getCell('B'.$row)->getValue();
							if($this->validateDate($fecha_excel)){
								$html .= '<td>'.$fecha_excel.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$fecha_excel.' '.$msg_formato.'</td>';
								$num_errores++;
							}
							$cont = 2;
						}

					}
				
					foreach($campos_formulario as $campo){
						if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
							continue;
						}
						$letra_columna = $this->getNameFromNumber($cont);
						$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
						
						if($campo->id_tipo_campo == 1){
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
						if($campo->id_tipo_campo == 2){
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
						if($campo->id_tipo_campo == 3){
							
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
						if($campo->id_tipo_campo == 4){
							
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
						if($campo->id_tipo_campo == 5){
							
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
						if($campo->id_tipo_campo == 6){
							
							if($campo->html_name == "2_or_unidades_funcionales"){
								
								//$nombre_uf = $valor_columna;
								$opciones = array();
								$ops = $this->Functional_units_model->get_all_where(array(
									"id_cliente" => $id_cliente,
									"id_proyecto" => $id_proyecto,
									"deleted" => 0
								))->result();
								
								foreach($ops as $op){
									if($campo->obligatorio){
										if($op->nombre == ""){continue;}
									}else{
										if($op->nombre == ""){
											$opciones[] = "";
											continue;
	
										}
									}
									$opciones[] = $op->nombre;
								}
								
								if(in_array($valor_columna, $opciones)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
								
								
							} else {

								if(!in_array($id_formulario, array("waste_transport_companies", "waste_receiving_companies"))){
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
										$opciones[] = $op->value;
									}
								}else{
									$opciones = json_decode($campo->opciones, true);
									$opciones = array_column($opciones, "text");
								}
								
								if(in_array($valor_columna, $opciones)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
								
							}
							
						}
						/*if($campo->id_tipo_campo == 7){//select_multiple
							
						}*/
						if($campo->id_tipo_campo == 8){
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
						if($campo->id_tipo_campo == 9){
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
						if($campo->id_tipo_campo == 13){
							
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
						if($campo->id_tipo_campo == 14){
							// ESTE TIPO DE CAMPO RECIBE EN INGRESO LA HORA EN FORMATO 24HRS SIEMPRE
							
							if($campo->obligatorio){
								if(strlen($valor_columna) == 5){// 12:00 PM
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
						if($campo->id_tipo_campo == 15){
							
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
						
						if($campo->id_tipo_campo == 16){
							
							$datos_campo = json_decode($campo->default_value);
							$id_mantenedora = $datos_campo->mantenedora;
							$id_campo_label = $datos_campo->field_label;
							$id_campo_value = $datos_campo->field_value;
							
							if($id_mantenedora == "waste_transport_companies"){
								$datos = $this->Fixed_feeder_waste_transport_companies_values_model->get_details(array(
									"id_client" => $id_cliente,
									"id_project" => $id_proyecto
								))->result();
							}elseif($id_mantenedora == "waste_receiving_companies"){
								$datos = $this->Fixed_feeder_waste_receiving_companies_values_model->get_details(array(
									"id_client" => $id_cliente,
									"id_project" => $id_proyecto
								))->result();
							}else{
								$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
							}
							//$info_mantenedora = $this->Forms_model->get_one($id_mantenedora);
							
							$array_opciones = array();
							foreach($datos as $index => $fila){
								
								if(!in_array($id_mantenedora, array("waste_transport_companies", "waste_receiving_companies"))){
									
									$datos_decoded = json_decode($fila->datos, true);
									$label = $datos_decoded[$id_campo_label];
									$value = $datos_decoded[$id_campo_value];
									$array_opciones[] = $value;

								// Si el campo mantenedora es waste_receiving_companies o waste_transport_companies no tipo patente
								}elseif($id_mantenedora != "waste_transport_companies" && $id_campo_label != 'patent'){
									$label = $fila->$id_campo_label;
									$value = $fila->$id_campo_value;
									$array_opciones[] = $value;
								}
							
							}
		
							// Si el campo mantenedora almacena Patentes del formulario Empresas transportistas de residuos (Como obtiene todas las patentes de una sola vez no es necesario que este dentro del loop anterior)
							if($id_mantenedora == "waste_transport_companies" && $id_campo_label == 'patent'){
								// $label = $row->$id_field_label;
								$patentes = $this->Patents_model->get_all_where(array(
									"id_client" => $id_cliente,
									"id_project" => $id_proyecto,
									'deleted' => 0
								))->result();
								
								foreach($patentes as $patente){
									$array_opciones[] = $patente->patent;
								}

							}
							
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									if(in_array($valor_columna, $array_opciones)){
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
								if($valor_columna == "" || in_array($valor_columna, $array_opciones)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}
							
						}
						
						$cont++;
					}
					
					
					$html .= '</tr>';
					
					
					
				}
				
				$html .= '</tbody>';
				$html .= '</table>';
				
				
				if($num_errores > 0){
					echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => $html));
				}else{
					$this->bulk_load($id_cliente, $id_proyecto, $id_formulario, $archivo_subido);
					//echo json_encode(array("success" => true, 'message' => lang('record_saved'), 'table' => $html));
				}
				
				exit();
				
				
			}
			
			

		}
		

    }
	
	function bulk_load($id_cliente, $id_proyecto, $id_formulario, $archivo_subido){

		$info_proyecto = $this->Projects_model->get_one($id_proyecto);
		
		//$this->load->library('excel');
		if($id_formulario == "waste_transport_companies"){
			$formulario = new stdClass();
			$formulario->id_tipo_formulario = 2;
			$formulario->nombre = lang("waste_transport_companies");
			$formulario->codigo = "";
		}elseif($id_formulario == "waste_receiving_companies"){
			$formulario = new stdClass();
			$formulario->id_tipo_formulario = 2;
			$formulario->nombre = lang("waste_receiving_companies");
			$formulario->codigo = "";
		}else{
			$formulario = $this->Forms_model->get_one($id_formulario);
		}
		
		$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);
		$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);
		$worksheet = $excelObj->getSheet(0);
		$lastRow = $worksheet->getHighestRow();

		$array_insert = array();
		
		if(!$formulario->fijo){
			$campos_formulario = $this->get_fields_of_fixed_feeders($id_formulario);
			//$campos_formulario = $this->Forms_model->get_fields_of_form($id_formulario)->result();
		} else {
			$campos_formulario = $this->Fixed_fields_model->get_all_where(array(
				"codigo_formulario_fijo" => $formulario->codigo_formulario_fijo,
				"deleted" => 0
			))->result();
		}
		
		if($formulario->id_tipo_formulario == 1){// SI ES REGISTRO AMBIENTAL

			$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $id_formulario, "id_proyecto" => $id_proyecto, "deleted" => 0));
			$id_formulario_rel_proyecto = $formulario_rel_proyecto->id;
			$info_formulario = $this->Forms_model->get_one($id_formulario);
			
			// POR CADA FILA
			for($row = 2; $row <= $lastRow; $row++){
				
				// Columna del excel
				$cont = 0;

				$array_row = array();
				$array_row["id_formulario_rel_proyecto"] = $id_formulario_rel_proyecto;
				
				$array_json = array();

				// CAMPO FECHA
				$letra_columna = $this->getNameFromNumber($cont);
				$valor_fecha = $worksheet->getCell($letra_columna.$row)->getValue();
				$array_json["fecha"] = $valor_fecha;
				$cont++;

				// CAMPO MES
				if($info_formulario->flujo == "Residuo"){
					$letra_columna = $this->getNameFromNumber($cont);
					$valor_mes = $worksheet->getCell($letra_columna.$row)->getValue();
					$array_json["month"] = month_to_number($valor_mes);
					$cont++;
				}

				// CAMPO CATEGORÍA
				$letra_columna = $this->getNameFromNumber($cont);
				$label_categoria = $worksheet->getCell($letra_columna.$row)->getValue();
				$cont++;
				
				// SE TRAE EL ID DE LA CATEGORIA ASOCIADA AL ALIAS EN $label_categoria, SI ESTE NO EXISTE SE BUSCA LA CATEGORIA QUE TENGA EL MISMO NOMBRE.
				$categoria = $this->Categories_alias_model->get_category_rel_to_form($label_categoria, $info_formulario->id, $id_cliente)->row();
				if(!$categoria->id_categoria){
					$categoria = $this->Categories_model->get_category_rel_to_form($label_categoria, $info_formulario->id, $id_cliente)->row();
					$id_categoria = $categoria->id;
				}else{
					$id_categoria = $categoria->id_categoria;
				}
				$array_json["id_categoria"] = (int)$id_categoria;
				
				// CAMPO UNIDAD
				$letra_columna = $this->getNameFromNumber($cont);

				$valor_unidad = $worksheet->getCell($letra_columna.$row)->getValue();
				$datos_unidad_form = json_decode($info_formulario->unidad, "true");
				$tipo_unidad = $this->Unity_type_model->get_one($datos_unidad_form["tipo_unidad_id"])->nombre;
				$unidad = $this->Unity_model->get_one($datos_unidad_form["unidad_id"])->nombre;
				
				$array_json["unidad_residuo"] = (float)$valor_unidad;
				$array_json["tipo_unidad"] = $tipo_unidad;
				$array_json["unidad"] = $unidad;

				$cont++;
				
				if($info_formulario->flujo == "Consumo"){
					
					$letra_columna = $this->getNameFromNumber($cont);
					$tipo = $worksheet->getCell($letra_columna.$row)->getValue();
					
					$data_tipo_origen = json_decode($info_formulario->tipo_origen);
					$id_tipo_origen = $data_tipo_origen->type_of_origin;
					
					if($id_tipo_origen == 1){
						// DEBO TRAER EL ID DEL MATERIAL DONDE EL NOMBRE SEA IGUAL AL INGRESADO
						$materias = $this->EC_Types_of_origin_matter_model->get_all_where(array(
							"id_tipo_origen" => $id_tipo_origen,
							//"nombre" => $tipo,
							"deleted" => 0
						))->result();
						
						foreach($materias as $materia){
							if($tipo == lang($materia->nombre)){
								$type_of_origin_matter = $materia->id;
							}
						}
						
					}

					$cont++;
					// $cont = 4;
					
				}elseif($info_formulario->flujo == "Residuo"){

					// CAMPO FUENTE
					// Si el proyecto es CPC se omite el campo
					if($id_proyecto != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

						$letra_columna = $this->getNameFromNumber($cont);

						$sources = $this->Sources_model->get_details(array("id_proyecto" => $id_proyecto))->result();
						foreach($sources as $source){
							if(lang($source->name) == $worksheet->getCell($letra_columna.$row)->getValue()){
								$id_source = $source->id;
							}
						}

						$cont++;
					}
					
					// CAMPO TIPO DE TRATAMIENTO
					$letra_columna = $this->getNameFromNumber($cont);

					$tipos_tratamiento = $this->Tipo_tratamiento_model->get_details(array("id_proyecto" => $id_proyecto))->result();
					foreach($tipos_tratamiento as $tipo_tratamiento){
						if($tipo_tratamiento->nombre == $worksheet->getCell($letra_columna.$row)->getValue()){ // ORIGINAL D
							$id_tipo_tratamiento = $tipo_tratamiento->id;
						}
					}
					
					$cont++;
					// CAMPO FECHA DE RETIRO
					//$fecha_retiro = $worksheet->getCell('E'.$row)->getValue();
					
					// $cont = 5;

				}elseif($info_formulario->flujo == "No Aplica"){
					
					$letra_columna = $this->getNameFromNumber($cont);

					$tipo = $worksheet->getCell($letra_columna.$row)->getValue();
					
					$data_tipo_no_aplica = json_decode($info_formulario->tipo_por_defecto);
					$default_type = ($data_tipo_no_aplica->default_type)?$data_tipo_no_aplica->default_type:NULL;
					
					// DEBO TRAER EL ID DEL TIPO DONDE EL NOMBRE SEA IGUAL AL INGRESADO
					$tipos_no_aplica = $this->EC_Types_no_apply_model->get_all()->result();
					
					foreach($tipos_no_aplica as $tipo_na){
						if($tipo == lang($tipo_na->nombre)){
							$type_id = $tipo_na->id;
						}
					}
					
					$cont++;
					// $cont = 4;
				}else{
					
				}
				
				// CAMPOS DINAMICOS
				foreach($campos_formulario as $campo){
					
					if($campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
						continue;
					}
					if($campo->id_tipo_campo == 10){// ARCHIVO (DEBE IR SI O SI EL ID DEL CAMPO, POR LO QUE LO AGREGAREMOS VACIO)
						$array_json["$campo->id"] = NULL;
						continue;
					}
					
					$letra_columna = $this->getNameFromNumber($cont);
					$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
					
					if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 2 || $campo->id_tipo_campo == 3 || $campo->id_tipo_campo == 4){
						// CAMPO DESHABILITADO = 1
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
					}
					if($campo->id_tipo_campo == 5){
						if($campo->obligatorio){
							$array_periodo = explode("/", $valor_columna);
							$fecha_desde = $array_periodo[0];
							$fecha_hasta = $array_periodo[1];
							$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
						}else{
							
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
						
						$array_json["$campo->id"] = $json_periodo;
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
						
						$array_json["$campo->id"] = $opciones[$valor_columna];*/
						
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
						
					}
					if($campo->id_tipo_campo == 8){// RUT
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
					}
					if($campo->id_tipo_campo == 9){// RADIO
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
					}
					if($campo->id_tipo_campo == 13){// CORREO
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
					}
					if($campo->id_tipo_campo == 14){// HORA
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
					}
					if($campo->id_tipo_campo == 15){// UNIDAD

						// Código para FORMULARIO TRANSPORTE DE PLANTULAS DE PROYECTO CPC
						// En el campo "Peso transportado" se pone una formula por lo que se debe leer el valor que resulta de la aplicación de la formula
						if( $id_proyecto == CONST_ID_PROYECTO_CPC 
						&& $id_formulario == CONST_ID_FORM_TRANSP_DE_PLANTULAS 
						&& $campo->id == CONST_ID_CAMPO_PESO_TRANSPORTADO_PLANTULAS ){
							$valor_columna = $worksheet->getCell($letra_columna.$row)->getCalculatedValue();
						}
						
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
					
					
					}
					if($campo->id_tipo_campo == 16){

						$patente = $this->Patents_model->get_one_where(array(
							'patent' => $valor_columna,
							'deleted' => 0
						));
						$id_patent = $patente->id;
						$array_json["$campo->id"] = $id_patent;
						
					}
					$cont++;

				}

				if($info_formulario->flujo == "Residuo"){

					// CAMPO PLACA PATENTE
					// Si el proyecto es CPC se omite el campo
					if($id_proyecto != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

						$letra_columna = $this->getNameFromNumber($cont);
						$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
						
						$patente = $this->Patents_model->get_one_where(array(
							'patent' => $valor_columna,
							'deleted' => 0
						));
						$id_patent = $patente->id;
						// $values = $this->Fixed_feeder_waste_transport_companies_values_model->get_details(array(
						// 	"id_client" => $id_cliente,
						// 	"id_project" => $id_proyecto
						// ))->result();

						// $array_patentes = array();
						// foreach($values as $value){
						// 	$patentes = json_decode($value->patent);
						// 	foreach($patentes as $patente){
						// 		$array_patentes[] = $patente;
						// 	}
						// }
						// $id_patent = in_array($valor_columna, $array_patentes) ? $valor_columna : '';
						$cont++;
					
					}

					// CAMPO EMP. TRANSPORTISTA DE RESIDUOS
					// Si el proyecto es CPC se omite el campo
					if($id_proyecto != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

						$letra_columna = $this->getNameFromNumber($cont);
						$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
						$waste_transport_companies = $this->Fixed_feeder_waste_transport_companies_values_model->get_details(array(
							"id_client" => $id_cliente,
							"id_project" => $id_proyecto
						))->result();
						foreach($waste_transport_companies as $company){
							if($company->company_name == $valor_columna){
								$id_waste_transport_company = $company->id;
							}
						}
						$cont++;
					}


					// CAMPO EMP. RECEPTORA DE RESIDUOS
					// Si el proyecto es CPC se omite el campo
					if($id_proyecto != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev

						$letra_columna = $this->getNameFromNumber($cont);
						$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
						$waste_receiving_companies = $this->Fixed_feeder_waste_receiving_companies_values_model->get_details(array(
							"id_client" => $id_cliente,
							"id_project" => $id_proyecto
						))->result();
						foreach($waste_receiving_companies as $company){
							if($company->company_name == $valor_columna){
								$id_waste_receiving_company = $company->id;
							}
							$array_waste_receiving_companies[$company->id] = $company->company_name;
						}
						$cont++;
					
					}

					// CAMPO FECHA DE RETIRO
					$letra_columna = $this->getNameFromNumber($cont);
					$fecha_retiro = $worksheet->getCell($letra_columna.$row)->getValue();

				}
				
				
				if($info_formulario->flujo == "Consumo"){
					
					$array_json['type_of_origin'] = $id_tipo_origen;
					if($type_of_origin_matter){
						$array_json['type_of_origin_matter'] = $type_of_origin_matter;
					}
					
				}elseif($info_formulario->flujo == "Residuo"){
					
					// Si el proyecto es CPC se omiten estos campos
					if($id_proyecto != CONST_ID_PROYECTO_CPC){	// ID proyecto CPC en dev
						$array_json["id_source"] = $id_source;
						$array_json["id_patent"] = $id_patent;
						$array_json["id_waste_transport_company"] = $id_waste_transport_company;
						$array_json["id_waste_receiving_company"] = $id_waste_receiving_company;
					}
					$array_json["tipo_tratamiento"] = $id_tipo_tratamiento;
					$array_json["fecha_retiro"] = $fecha_retiro;
					$array_json["nombre_archivo_retiro"] = NULL;
					$array_json["nombre_archivo_recepcion"] = NULL;
					
				}elseif($info_formulario->flujo == "No Aplica"){
					$array_json['default_type'] = $type_id;
				} else {
					
				}
				
				$json_datos = json_encode($array_json);
				$array_row["datos"] = $json_datos;
				$array_row["created_by"] = $this->login_user->id;
				$array_row["modified_by"] = NULL;
				$array_row["created"] = get_current_utc_time();
				$array_row["modified"] = NULL;
				$array_row["deleted"] = 0;
				
				$array_insert[] = $array_row;
			}// FIN FOR ROW
			
		}else{// SI NO ES REGISTRO AMBIENTAL
				
			if(!$formulario->fijo){

				if(!in_array($id_formulario, array("waste_transport_companies", "waste_receiving_companies"))){
					$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $id_formulario, "id_proyecto" => $id_proyecto, "deleted" => 0));
					$id_formulario_rel_proyecto = $formulario_rel_proyecto->id;
					
					// POR CADA FILA
					for($row = 2; $row <= $lastRow; $row++){
						
						$array_row = array();
						$array_row["id_formulario_rel_proyecto"] = $id_formulario_rel_proyecto;
						
						$array_json = array();
						$cont = 0;
						
						if($formulario->id_tipo_formulario == 3){
							$cont = 1;
							$valor_fecha = $worksheet->getCell('A'.$row)->getValue();
							$array_json["fecha"] = $valor_fecha;
						}
									
						foreach($campos_formulario as $campo){
							
							if($campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
								continue;
							}
							if($campo->id_tipo_campo == 10){// ARCHIVO (DEBE IR SI O SI EL ID DEL CAMPO, POR LO QUE LO AGREGAREMOS VACIO)
								$array_json["$campo->id"] = NULL;
								continue;
							}
							
							$letra_columna = $this->getNameFromNumber($cont);
							$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
							
							if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 2 || $campo->id_tipo_campo == 3 || $campo->id_tipo_campo == 4){
								// CAMPO DESHABILITADO = 1
								if($campo->habilitado == 1){
									$array_json["$campo->id"] = $campo->default_value;
								}else{
									$array_json["$campo->id"] = $valor_columna;
								}
							}
							if($campo->id_tipo_campo == 5){
								if($campo->obligatorio){
									$array_periodo = explode("/", $valor_columna);
									$fecha_desde = $array_periodo[0];
									$fecha_hasta = $array_periodo[1];
									$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
									
								}else{
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
								
								$array_json["$campo->id"] = $json_periodo;
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
								
								$array_json["$campo->id"] = $opciones[$valor_columna];*/
								if($campo->habilitado == 1){
									$array_json["$campo->id"] = $campo->default_value;
								}else{
									$array_json["$campo->id"] = $valor_columna;
								}
								
							}
							if($campo->id_tipo_campo == 8){// RUT
								if($campo->habilitado == 1){
									$array_json["$campo->id"] = $campo->default_value;
								}else{
									$array_json["$campo->id"] = $valor_columna;
								}
							}
							if($campo->id_tipo_campo == 9){// RADIO
								if($campo->habilitado == 1){
									$array_json["$campo->id"] = $campo->default_value;
								}else{
									$array_json["$campo->id"] = $valor_columna;
								}
							}
							if($campo->id_tipo_campo == 13){// CORREO
								if($campo->habilitado == 1){
									$array_json["$campo->id"] = $campo->default_value;
								}else{
									$array_json["$campo->id"] = $valor_columna;
								}
							}
							if($campo->id_tipo_campo == 14){// HORA
								if($campo->habilitado == 1){
									$array_json["$campo->id"] = $campo->default_value;
								}else{
									$array_json["$campo->id"] = $valor_columna;
								}
							}
							if($campo->id_tipo_campo == 15){// UNIDAD
								if($campo->habilitado == 1){
									$array_json["$campo->id"] = $campo->default_value;
								}else{
									$array_json["$campo->id"] = $valor_columna;
								}
							}
							if($campo->id_tipo_campo == 16){
								
								$patente = $this->Patents_model->get_one_where(array(
									'patent' => $valor_columna,
									'deleted' => 0
								));
								$id_patent = $patente->id;
								$array_json["$campo->id"] = $id_patent;
							}
							
							$cont++;
						}
						
						$json_datos = json_encode($array_json);
						$array_row["datos"] = $json_datos;
						$array_row["created_by"] = $this->login_user->id;
						$array_row["modified_by"] = NULL;
						$array_row["created"] = get_current_utc_time();
						$array_row["modified"] = NULL;
						$array_row["deleted"] = 0;
						
						$array_insert[] = $array_row;
						
					}// FIN FOR ROW
				}else{

					$array_patentes = array();

					for($row = 2; $row <= $lastRow; $row++){
						
						$array_row = array();
						$cont = 0;
									
						foreach($campos_formulario as $campo){
							
							$letra_columna = $this->getNameFromNumber($cont);
							$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
							
							if($campo->id_tipo_campo == 1){
								
								// Si el formulario al que se le cargan datos es Empresa transportista y el campo es de tipo patente, se guardarán las patentes como un array en formato json
								if($id_formulario == "waste_transport_companies" && $campo->nombre_columna == "patent"){

									// En cada fila viene una celda con las patentes que tiene una Empresa Transportista (patentes separadas por ;). El texto correspondiente a las patentes de la empresa se guarda en una poscición del arreglo $array_patentes, el cual tendrá un elemento con las patentes para cada empresa ingresada(fila en el excel).
									$array_patentes[] = $valor_columna;

								}else{
									$array_row[$campo->nombre_columna] = "$valor_columna";
								}
							}
							
							if($campo->id_tipo_campo == 6){
								$opciones = json_decode($campo->opciones, true);
								$arreglo_texto_id = array_column($opciones, "value", "text");
								$id_valor_columna = $arreglo_texto_id[$valor_columna];

								$array_row[$campo->nombre_columna] = $id_valor_columna;
							}
							
							$cont++;
						}
						
						$array_row["id_client"] = $id_cliente;
						$array_row["id_project"] = $id_proyecto;
						$array_row["created_by"] = $this->login_user->id;
						$array_row["modified_by"] = NULL;
						$array_row["created"] = get_current_utc_time();
						$array_row["modified"] = NULL;
						$array_row["deleted"] = 0;
						
						$array_insert[] = $array_row;
						
					}// FIN FOR ROW
				}
			
			} else { // Si el formulario es fijo
				
				// POR CADA FILA
				for($row = 2; $row <= $lastRow; $row++){
					
					$array_row = array();
					$array_row["id_formulario"] = $id_formulario;
					
					$array_json = array();
					$cont = 0;

					if($formulario->codigo_formulario_fijo != 'or_unidades_funcionales'){
						
						$valor_columna = $worksheet->getCell('A'.$row)->getValue();
						$array_json['year_semester'] = $valor_columna;

						$valor_columna = $worksheet->getCell('B'.$row)->getValue();
						$array_json['fecha'] = $valor_columna;

						$cont = 2;
					}
								
					foreach($campos_formulario as $campo){
						
						if($campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
							continue;
						}
						if($campo->id_tipo_campo == 10){// ARCHIVO (DEBE IR SI O SI EL ID DEL CAMPO, POR LO QUE LO AGREGAREMOS VACIO)
							$array_json["$campo->id"] = NULL;
							continue;
						}
						
						$letra_columna = $this->getNameFromNumber($cont);
						$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
						
						if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 2 || $campo->id_tipo_campo == 3 || $campo->id_tipo_campo == 4){
							// CAMPO DESHABILITADO = 1
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 5){
							if($campo->obligatorio){
								$array_periodo = explode("/", $valor_columna);
								$fecha_desde = $array_periodo[0];
								$fecha_hasta = $array_periodo[1];
								$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
								
							}else{
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
							
							$array_json["$campo->id"] = $json_periodo;
						}
						if($campo->id_tipo_campo == 6){
							
							if($campo->html_name == "2_or_unidades_funcionales"){
								//$nombre_uf = $valor_columna;
								$uf = $this->Functional_units_model->get_one_where(array(
									"id_cliente" => $id_cliente,
									"id_proyecto" => $id_proyecto,
									"nombre" => $valor_columna,
									"deleted" => 0
								));
								
								$array_json["$campo->id"] = $uf->id;
								
							} else {
								
								$array_json["$campo->id"] = $valor_columna;
							
							}
							
						}
						if($campo->id_tipo_campo == 8){// RUT
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 9){// RADIO
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 13){// CORREO
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 14){// HORA
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 15){// UNIDAD
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 16){

							$patente = $this->Patents_model->get_one_where(array(
								'patent' => $valor_columna,
								'deleted' => 0
							));
							$id_patent = $patente->id;
							$array_json["$campo->id"] = $id_patent;
							
						}
						
						$cont++;
					}
					
					$json_datos = json_encode($array_json);
					$array_row["datos"] = $json_datos;
					$array_row["created_by"] = $this->login_user->id;
					$array_row["modified_by"] = NULL;
					$array_row["created"] = get_current_utc_time();
					$array_row["modified"] = NULL;
					$array_row["deleted"] = 0;
					
					$array_insert[] = $array_row;
					
				}// FIN FOR ROW
				
			}

		}
		
		if(!$formulario->fijo){
			
			if($id_formulario == "waste_transport_companies"){
				$bulk_load = $this->Fixed_feeder_waste_transport_companies_values_model->bulk_load($array_insert);
			}elseif($id_formulario == "waste_receiving_companies"){
				$bulk_load = $this->Fixed_feeder_waste_receiving_companies_values_model->bulk_load($array_insert);
			}else{
				$bulk_load = $this->Form_values_model->bulk_load($array_insert);
			}

			if($bulk_load){

				// Inserción de patentes en la tabla Patentes
				if($id_formulario == "waste_transport_companies"){

					// Primer ID de los valores insertados por bulk_load en Fixed_feeder_waste_transport_companies.
					$first_id = $this->db->insert_id();
					
					// Cantidad de grupos de patentes a ser insertadas (un grupo por cada Empresa insertada en bulk_load)
					$cant_grupos_patentes = count($array_patentes);
					
					// Ultimo ID insertado por bulk_load
					$last_id = $first_id + $cant_grupos_patentes - 1;

					//Se calculara el ID de cada elemento insertado por bulk_load en Fixed_feeder_waste_transport_companies, estos id se usarán para vincular las patentes con la empresa a la que pertenece.
					$array_ides_waste_transport_company = array();
					for($i = $first_id; $i <= $last_id; $i++){
						$array_ides_waste_transport_company[] = $i;
					}
					
					// Los grupos de patentes en array_patentes tienen el mismo orden que los IDs de las empresas insertadas en fixed_feeder_waste trasnport_campanies, por lo tanto el indice de array_patentes se puede usar para recorrer el arreglo de IDs (array_ides_waste_transport_company).
					foreach($array_patentes as $index => $patentes){
						$patentes_por_empresa = array();
						$patentes_por_empresa = array_map('trim', explode(';', $patentes));

						foreach($patentes_por_empresa as $patente_empresa){
							$data_patente = array();
							$data_patente['id_client'] = $id_cliente;
							$data_patente['id_project'] = $id_proyecto;
							$data_patente['id_waste_transport_company'] = $array_ides_waste_transport_company[$index];
							$data_patente['patent'] = $patente_empresa;

							$this->Patents_model->save($data_patente);
						}
					}
					
					// // array_map llama a la funcion trim para cada elemento del arreglo
					// $array_patentes = array_map('trim', $array_patentes);
				}
				
				// Guardar histórico notificaciones
				$id_cliente = $this->login_user->client_id;
				$id_proyecto = $this->session->project_context;	
				$id_user = $this->session->user_id;
			
				$options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_user" => $id_user,
					"module_level" => "project",
					"id_client_module" => $this->id_modulo_cliente,
					"id_client_submodule" => $this->id_submodulo_cliente,
					"event" => "bulk_load",
					"id_element" => $formulario->id
				);
				ayn_save_historical_notification($options);
			
				echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
			}else{
				echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
			}
		} else { // Si el formulario es fijo
			$bulk_load = $this->Fixed_form_values_model->bulk_load($array_insert);
			if($bulk_load){
				
				// Guardar histórico notificaciones
				$id_cliente = $this->login_user->client_id;
				$id_proyecto = $this->session->project_context;	
				$id_user = $this->session->user_id;
			
				$options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_user" => $id_user,
					"module_level" => "project",
					"id_client_module" => $this->id_modulo_cliente,
					"id_client_submodule" => $this->id_submodulo_cliente,
					"event" => "bulk_load",
					"id_element" => $formulario->id
				);
				ayn_save_historical_notification($options);
				
				echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
			}else{
				echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
			}
		}

	}
	
	function create_client_folder($client_id) {
		if(!file_exists(__DIR__.'/../../files/client_'.$client_id)) {
			if(mkdir(__DIR__.'/../../files/client_'.$client_id, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
	}

    /* download a file */

    function download_file($id) {

        $file_info = $this->General_files_model->get_one($id);

        if (!$file_info->client_id) {
            redirect("forbidden");
        }
        //serilize the path
        $file_data = serialize(array(array("file_name" => $file_info->file_name)));

        download_app_files(get_general_file_path("client", $file_info->client_id), $file_data);
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

    /* delete a file */

    function delete_file() {

        $id = $this->input->post('id');
        $info = $this->General_files_model->get_one($id);

        if (!$info->client_id) {
            redirect("forbidden");
        }

        if ($this->General_files_model->delete($id)) {

            delete_file_from_directory(get_general_file_path("client", $info->client_id) . $info->file_name);

            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }
	
	/* get instructions for bulk load */

    function get_intructions() {
        //$this->access_only_allowed_members();
		$html = $this->load->view('setting_bulk_load/intructions', $view_data, true);
		echo $html;
    }

	function get_fields_of_fixed_feeders($id_formulario) {

		$id_project = $this->session->project_context;
		$project_info = $this->Projects_model->get_one($id_project);

        if($id_formulario == "waste_transport_companies"){
			$campos_formulario = array();

			$campo_1 = new stdClass();
			$campo_1->id_tipo_campo = 1;
			$campo_1->nombre = lang("company_name_2");
			$campo_1->nombre_columna = "company_name";
			$campo_1->obligatorio = 1;
			$campos_formulario[0] = $campo_1;

			$campo_2 = new stdClass();
			$campo_2->id_tipo_campo = 1;
			$campo_2->nombre = lang("company_ruc");
			$campo_2->nombre_columna = "company_rut";
			$campo_2->obligatorio = 1;
			$campos_formulario[1] = $campo_2;

			$campo_3 = new stdClass();
			$campo_3->id_tipo_campo = 1;
			$campo_3->nombre = lang("company_registration_code");
			$campo_3->nombre_columna = "company_registration_code";
			$campo_3->obligatorio = 1;
			$campos_formulario[2] = $campo_3;

			$campo_4 = new stdClass();
			$campo_4->id_tipo_campo = 1;
			$campo_4->nombre = lang("patent_plate");
			$campo_4->nombre_columna = "patent";
			$campo_4->obligatorio = 1;
			$campos_formulario[3] = $campo_4;

		}elseif($id_formulario == "waste_receiving_companies"){
			$campos_formulario = array();

			$campo_1 = new stdClass();
			$campo_1->id_tipo_campo = 1;
			$campo_1->nombre = lang("company_name_2");
			$campo_1->nombre_columna = "company_name";
			$campo_1->obligatorio = 1;
			$campos_formulario[0] = $campo_1;

			$campo_2 = new stdClass();
			$campo_2->id_tipo_campo = 1;
			$campo_2->nombre = lang("company_ruc");
			$campo_2->nombre_columna = "company_rut";
			$campos_formulario[1] = $campo_2;

			$campo_3 = new stdClass();
			$campo_3->id_tipo_campo = 1;
			$campo_3->nombre = lang("company_code");
			$campo_3->nombre_columna = "company_code";
			$campos_formulario[2] = $campo_3;

			/*$campo_4 = new stdClass();
			$campo_4->id_tipo_campo = 6;
			$campo_4->nombre = lang("sinader_treatment");
			$campo_4->nombre_columna = "id_treatment_sinader";
			$campo_4->obligatorio = 1;
			$sinader_treatments_dropdown = array(array("value" => "", "text" => "-"));
			$sinader_treatments = $this->Fixed_feeder_treatment_sinader_model->get_all()->result();
			foreach($sinader_treatments as $st){
				$sinader_treatments_dropdown[] = array("value" => $st->id, "text" => lang($st->name));
			}
			$campo_4->opciones = json_encode($sinader_treatments_dropdown);
			$campos_formulario[3] = $campo_4;*/

			/*$campo_5 = new stdClass();
			$campo_5->id_tipo_campo = 6;
			$campo_5->nombre = lang("sidrep_treatment");
			$campo_5->nombre_columna = "id_treatment_sidrep";
			$campo_5->obligatorio = 1;
			$sidrep_treatments_dropdown = array(array("value" => "", "text" => "-"));
			$sidrep_treatments = $this->Fixed_feeder_treatment_sidrep_model->get_all()->result();
			foreach($sidrep_treatments as $st){
				$sidrep_treatments_dropdown[] = array("value" => $st->id, "text" => lang($st->name));
			}
			$campo_5->opciones = json_encode($sidrep_treatments_dropdown);
			$campos_formulario[4] = $campo_5;*/

			/*$campo_6 = new stdClass();
			$campo_6->id_tipo_campo = 6;
			$campo_6->nombre = lang("management");
			$campo_6->nombre_columna = "id_management";
			$campo_6->obligatorio = 1;
			$managements_dropdown = array(array("value" => "", "text" => "-"));
			$managements = $this->Fixed_feeder_management_model->get_all()->result();
			foreach($managements as $st){
				$managements_dropdown[] = array("value" => $st->id, "text" => lang($st->name));
			}
			$campo_6->opciones = json_encode($managements_dropdown);
			$campos_formulario[5] = $campo_6;*/

			$campo_7 = new stdClass();
			$campo_7->id_tipo_campo = 1;
			$campo_7->nombre = lang("address");
			$campo_7->nombre_columna = "address";
			$campos_formulario[6] = $campo_7;

			$campo_8 = new stdClass();
			$campo_8->id_tipo_campo = 1;
			$campo_8->nombre = lang("district");
			$campo_8->nombre_columna = "city";
			$campos_formulario[7] = $campo_8;

			$campo_9 = new stdClass();
			$campo_9->id_tipo_campo = 1;
			$campo_9->nombre = lang("province");
			$campo_9->nombre_columna = "commune";
			$campos_formulario[8] = $campo_9;

			$campo_10 = new stdClass();
			$campo_10->id_tipo_campo = 1;
			$campo_10->nombre = lang("department");
			$campo_10->nombre_columna = "department";
			$campos_formulario[9] = $campo_10;
		}else{
			$campos_formulario = $this->Forms_model->get_fields_of_form($id_formulario)->result();
		}

		return $campos_formulario;
	}

}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */