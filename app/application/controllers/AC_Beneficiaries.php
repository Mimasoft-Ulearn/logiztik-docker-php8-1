<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class AC_Beneficiaries extends MY_Controller {
	
	private $id_client_context_module;
	private $id_client_context_submodule;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");	
		
		$client_area = $this->session->client_area;
		if($client_area == "territory"){
			$this->id_client_context_module = 6;
		} 
		$this->id_client_context_submodule = 20;
		
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


    }

    function index() {
		
		$id_usuario = $this->session->user_id;
		$client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;  
		
		$view_data["puede_ver"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
		$view_data["puede_agregar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "agregar");
		$view_data["puede_eliminar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");
		
		if($client_area == "territory"){
			
			// SEXO
			$sexos = $this->AC_Beneficiaries_model->get_dropdown_sexos();
			array_shift($sexos); // Quitar 1er elemento, por que es "-"
			$sexo_dropdown[] = array("id" => "", "text" => "-".lang("sex")."-");
			foreach($sexos as $key => $value){
				$sexo_dropdown[] = array("id" => $key, "text" => $value);
			}
			$view_data['sexo_dropdown'] = json_encode($sexo_dropdown);

			// SOCIEDAD
			$sociedades = $this->AC_Beneficiaries_model->get_dropdown_sociedades($id_cliente);
			array_shift($sociedades); // Quitar 1er elemento, por que es "-"
			$sociedades_dropdown[] = array("id" => "", "text" => "-".lang("society")."-");
			foreach($sociedades as $key => $value){
				$sociedades_dropdown[] = array("id" => $key, "text" => $value);
			}
			$view_data["sociedad_dropdown"] =  json_encode($sociedades_dropdown);

			// DISCAPACIDAD
			$discapacidades = $this->AC_Beneficiaries_model->get_dropdown_discapacidad();
			$discapacidades_dropdown[] = array("id" => "", "text" => "-".lang("disability")."-");
			foreach($discapacidades as $key => $value){
				$discapacidades_dropdown[] = array("id" => $key, "text" => $value);
			}
			$view_data["discapacidad_dropdown"] = json_encode($discapacidades_dropdown);
			
			array_shift($discapacidades_dropdown);
			$view_data["discapacidad_dropdown_apptable"] = $discapacidades_dropdown;
				
		}else {
			redirect("forbidden");
		}

		$view_data["client_area"] = $client_area;
		
        $this->template->rander("ac_beneficiaries/index", $view_data);
    }
	
	// Editar
	function modal_form() {
		
		$id_cliente = $this->login_user->client_id;  
		$client_area = $this->session->client_area;
        $id_beneficiario = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));
		
		$view_data['client_area'] = $client_area;
        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');

		$model_info = $this->AC_Beneficiaries_model->get_one($id_beneficiario);
        $view_data['model_info'] = $model_info;

		// SEXO
		$view_data['dropdown_sex'] = $this->AC_Beneficiaries_model->get_dropdown_sexos();

		// SOCIEDAD DESC
		$view_data["dropdown_sociedad_desc"] =  $this->AC_Beneficiaries_model->get_dropdown_sociedades($id_cliente);

		// ESTADO		
		$view_data["dropdown_estado"] = $this->AC_Beneficiaries_model->get_dropdown_status();
		
		// TIPO CONTRATO
		$view_data["dropdown_tipo_contrato"] = $this->AC_Beneficiaries_model->get_dropdown_tipo_contrato();

		// ESTADO CIVIL
		$view_data["dropdown_estado_civil"] = $this->AC_Beneficiaries_model->get_dropdown_estado_civil();

		// ÁREA DE PERSONAL
		$view_data["dropdown_area_de_personal"] = $this->AC_Beneficiaries_model->get_dropdown_area_de_personal();

		// NACIONALIDAD
		$view_data['dropdown_nacionalidad'] = $this->AC_Beneficiaries_model->get_dropdown_nacionalidad();

		// PROVINCIA
		$view_data["dropdown_provincia"] = $this->AC_Beneficiaries_model->get_dropdown_provincia();
		
		// DISCAPACIDAD
		$view_data["dropdown_discapacidad"] = $this->AC_Beneficiaries_model->get_dropdown_discapacidad();

		// LEY TEA
		$view_data["dropdown_ley_tea"] = array(
			'0' => lang('no'),
			'1' => lang('yes')
		);

		// PUEBLOS ORIGINARIOS
		$view_data["dropdown_pueblos_originarios"] = array(
			'0' => lang('no'),
			'1' => lang('yes')
		);

        $this->load->view('ac_beneficiaries/modal_form', $view_data);
    }
	
	// Guardar/Actualizar
	function save() {
		
		$client_area = $this->session->client_area;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
        $id_beneficiario = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric",
			"id_nacionalidad" => "trim",
			"sexo" => "trim",
			"fecha_nacimiento" => "trim|date",
			"correo_organizativo" => "trim|valid_email",
			"sociedad" => "trim",
			"sociedad_desc" => "trim|numeric",
			"centro_de_costo_desc" => "trim",
			"posicion_desc" => "trim",
			"division_desc" => "trim",
			"subdivision_desc" => "trim",
			"sigla_sociedad" => "trim",
			"sucursal" => "trim",
			"cargo" => "trim",
			"fecha_inicio_contrato" => "trim|date",
			"fecha_fin_contrato" => "trim|date",
			"estado" => "trim",
			"tipo_contrato" => "trim",
			"division2_desc" => "trim",
			"posicion_jefe_desc" => "trim",
			"estado_civil" => "trim",
			"area_de_personal" => "trim",
			"departamento_desc" => "trim",
			"job_code_desc" => "trim",
			"nombre_completo" => "trim",
			"nacionalidad" => "trim",
			"comuna" => "trim",
			"provincia" => "trim",
			"discapacidad" => "trim",
        ));

		$fecha_fin_contrato = $this->input->post('fecha_fin_contrato') ? $this->input->post('fecha_fin_contrato') : null;
		$nacionalidad = $this->input->post('nacionalidad') ? $this->input->post('nacionalidad') : null;
		$comuna = $this->input->post('comuna') ? $this->input->post('comuna') : null;
		$provincia = $this->input->post('provincia') ? $this->input->post('provincia') : null;
		
		$data_beneficiario = array(
			"id_cliente" => $id_cliente,
			"id_nacionalidad" => $this->input->post('id_nacionalidad'),
			"sexo" => $this->input->post('sexo'),
			"fecha_nacimiento" => $this->input->post('fecha_nacimiento'),
			"correo_organizativo" => $this->input->post('correo_organizativo'),
			"sociedad" => $this->input->post('sociedad'),
			"sociedad_desc" => $this->input->post('sociedad_desc'),			
			"centro_de_costo_desc" => $this->input->post('centro_de_costo_desc'),			
			"posicion_desc" => $this->input->post('posicion_desc'),			
			"division_desc" => $this->input->post('division_desc'),			
			"subdivision_desc" => $this->input->post('subdivision_desc'),
			"sigla_sociedad" => $this->input->post('sigla_sociedad'),
			"sucursal" => $this->input->post('sucursal'),
			"cargo" => $this->input->post('cargo'),
			"fecha_inicio_contrato" => $this->input->post('fecha_inicio_contrato'),
			"fecha_fin_contrato" => $fecha_fin_contrato,
			"estado" => $this->input->post('estado'),
			"tipo_contrato" => $this->input->post('tipo_contrato'),			
			"division2_desc" => $this->input->post('division2_desc'),			
			"posicion_jefe_desc" => $this->input->post('posicion_jefe_desc'),			
			"estado_civil" => $this->input->post('estado_civil'),
			"area_de_personal" => $this->input->post('area_de_personal'),
			"departamento_desc" => $this->input->post('departamento_desc'),			
			"job_code_desc" => $this->input->post('job_code_desc'),			
			"nombre_completo" => $this->input->post('nombre_completo'),
			"nacionalidad" => $nacionalidad,
			"comuna" => $comuna,
			"provincia" => $provincia,
			"discapacidad" => $this->input->post('discapacidad'),
			"ley_tea" => $this->input->post('ley_tea'),
			"pueblos_originarios" => $this->input->post('pueblos_originarios')
		);
		
		
		if($id_beneficiario){
			$data_beneficiario["modified_by"] = $this->login_user->id;
			$data_beneficiario["modified"] = get_current_utc_time();
			$save_id = $this->AC_Beneficiaries_model->save($data_beneficiario, $id_beneficiario);
		} else {
			$data_beneficiario["created_by"] = $this->login_user->id;
			$data_beneficiario["created"] = get_current_utc_time();
			$save_id = $this->AC_Beneficiaries_model->save($data_beneficiario);
		}
		
        if ($save_id) {
			
			// Guardar histórico notificaciones
			/* $options = array(
				"id_client" => $id_cliente,
				"id_user" => $this->session->user_id,
				"module_level" => "general",
				"id_client_context_module" => $this->id_client_context_module,
				"id_client_context_submodule" => $this->id_client_context_submodule,
				"event" => ($id_beneficiario) ? "edit" : "add",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options); */

            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	
	
	function delete() {
		
		$client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
		/* $informaciones = $this->AC_Information_model->get_all_where(array(
			"id_beneficiario" => $id,
			"deleted" => 0
		))->result_array();
		
		if(count($informaciones)){
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
			exit();
		} */
		
		$actividades = $this->AC_Activities_model->get_all_where(array(
			"id_cliente" => $id_cliente, 
			// "client_area" => $client_area,
			"deleted" => 0
		))->result();
		
		$num_apariciones = 0;
		foreach($actividades as $actividad){
			$beneficiarios = json_decode($actividad->asistentes, true);
			if(in_array($id, $beneficiarios)){
				$num_apariciones++;
			}
		}
		
		if($num_apariciones){
			echo json_encode(array("success" => false, 'message' => lang('ac_beneficiary_cannot_be_deleted')));
			exit();
		}
		
        if ($this->input->post('undo')) {
            if ($this->AC_Beneficiaries_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->AC_Beneficiaries_model->delete($id)) {
				
				// Guardar histórico notificaciones
				/* $options = array(
					"id_client" => $id_cliente,
					"id_user" => $this->session->user_id,
					"module_level" => "general",
					"id_client_context_module" => $this->id_client_context_module,
					"id_client_context_submodule" => $this->id_client_context_submodule,
					"event" => "delete",
					"id_element" => $id
				);
				ayn_save_historical_notification($options); */

                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function delete_multiple(){
		$client_area = $this->session->client_area;
		$data_ids = json_decode($this->input->post('data_ids'));
		$id_user = $this->session->user_id;
		$id_cliente = $this->login_user->client_id;
		
		// VALIDACIÓN DE ELIMINACIÓN DE REGISTROS SEGÚN PERFIL
		$puede_eliminar = $this->general_profile_access($id_user, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		$eliminar = TRUE;
		foreach($data_ids as $id){
			if($puede_eliminar == 2){ // Propios
				$row = $this->AC_Beneficiaries_model->get_one($id);
				if($id_user != $row->created_by){
					$eliminar = FALSE;
					break;
				}
			}
			if($puede_eliminar == 3){ // Ninguno
				$eliminar = FALSE;
				break;
			}
		}
		
		if(!$eliminar){
			echo json_encode(array("success" => false, 'message' => lang("record_cannot_be_deleted_by_profile")));
			exit();
		}
		
		foreach($data_ids as $id){
		
			/* $informaciones = $this->AC_Information_model->get_all_where(array(
				"id_beneficiario" => $id,
				"deleted" => 0
			))->result_array();
			
			if(count($informaciones)){
				echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
				exit();
			} */
			
			$actividades = $this->AC_Activities_model->get_all_where(array(
				"id_cliente" => $id_cliente, 
				// "client_area" => $client_area,
				"deleted" => 0
			))->result();
			
			$num_apariciones = 0;
			foreach($actividades as $actividad){
				$beneficiarios = json_decode($actividad->asistentes, true);
				if(in_array($id, $beneficiarios)){
					$num_apariciones++;
				}
			}
			
			if($num_apariciones){
				echo json_encode(array("success" => false, 'message' => lang('ac_beneficiary_cannot_be_deleted')));
				exit();
			}	
		
		}
		
		$deleted_values = false;
		foreach($data_ids as $id){
			if($this->AC_Beneficiaries_model->delete($id)) {
				$deleted_values = true;
			} else {
				$deleted_values = false;
				break;
			}
		}
					
		if($deleted_values){
			
			// Guardar histórico notificaciones
			/* foreach($data_ids as $index => $id){
				$options = array(
					"id_client" => $id_cliente,
					"id_user" => $id_user,
					"module_level" => "general",
					"id_client_context_module" => $this->id_client_context_module,
					"id_client_context_submodule" => $this->id_client_context_submodule,
					"event" => "delete",
					"id_element" => $id,
					"massive" => (count($data_ids) > 1) ? 1 : 0,
					"is_email_sended" => ($index !== count($data_ids) -1) ? 1 : 0
				);
				ayn_save_historical_notification($options);
			} */

			echo json_encode(array("success" => true, 'message' => lang('multiple_record_deleted')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
		}	
		
	}
	
	
	function list_data() {
		
		$id_usuario = $this->session->user_id;
		$id_cliente = $this->login_user->client_id;
		$client_area = $this->session->client_area;
		
		$puede_ver = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
		
		$options = array(
			"id_cliente" => $id_cliente,
			"sexo" => $this->input->post("sexo"),
			"sociedad" => $this->input->post("sociedad"),
			"discapacidad" => $this->input->post("discapacidad")
		);
		
        $list_data = $this->AC_Beneficiaries_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
			if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row($data);
			}
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row($data);
				}
			}
			if($puede_ver == 3){ //Ninguno
				$result[1] = array();
			}
        }
        echo json_encode(array("data" => $result));
    }
	
	private function _row_data($id) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->AC_Beneficiaries_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$id_usuario = $this->session->user_id;
		$puede_editar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "editar");
		$puede_eliminar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		$row_data[] = $data->id;
		
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
		}

		// SOCIEDAD DESC
		$sociedad_desc = $this->AC_Feeders_societies_model->get_one($data->sociedad_desc)->nombre_sociedad;

		// DISCAPACIDAD
		$discapacidad = $data->discapacidad ? $data->discapacidad : '-';
		$disabilty_icon = '<i class="fa fa-wheelchair"></i>';
		$discapacidad_dropdown = js_anchor($discapacidad, array('title' => "", "class" => "discapacidad_apptable_dropdown", "data-id" => $data->id, "data-value" => $discapacidad, "data-act" => "update-disability"));

		$row_data[] = $data->id_nacionalidad ? $data->id_nacionalidad : '-';
		$row_data[] = $data->sexo ? $data->sexo : '-';
		$row_data[] = $data->fecha_nacimiento ? $data->fecha_nacimiento : '-';
		$row_data[] = $data->correo_organizativo ? $data->correo_organizativo : '-';
		$row_data[] = $data->sociedad ? $data->sociedad : '-';
		$row_data[] = $data->sociedad_desc ? $sociedad_desc : '-';
		$row_data[] = $data->centro_de_costo_desc ? $data->centro_de_costo_desc : '-';
		$row_data[] = $data->posicion_desc ? $data->posicion_desc : '-';
		$row_data[] = $data->division_desc ? $data->division_desc : '-';
		$row_data[] = $data->subdivision_desc ? $data->subdivision_desc : '-';
		$row_data[] = $data->fecha_inicio_contrato ? $data->fecha_inicio_contrato : '-';
		$row_data[] = $data->fecha_fin_contrato ? $data->fecha_fin_contrato : '-';
		$row_data[] = $data->estado ? $data->estado : '-';
		$row_data[] = $data->tipo_contrato ? $data->tipo_contrato : '-';
		$row_data[] = $data->division2_desc ? $data->division2_desc : '-';
		$row_data[] = $data->posicion_jefe_desc ? $data->posicion_jefe_desc : '-';
		$row_data[] = $data->estado_civil ? $data->estado_civil : '-';
		$row_data[] = $data->area_de_personal ? $data->area_de_personal : '-';
		$row_data[] = $data->departamento_desc ? $data->departamento_desc : '-';
		$row_data[] = $data->job_code_desc ? $data->job_code_desc : '-';
		$row_data[] = $data->nombre_completo ? $data->nombre_completo : '-';
		$row_data[] = $data->nacionalidad ? $data->nacionalidad : '-';
		$row_data[] = $data->comuna ? $data->comuna : '-';
		$row_data[] = $data->provincia ? $data->provincia : '-';
		$row_data[] = $discapacidad_dropdown;
		$row_data[] = $data->ley_tea ? lang('yes') : lang('no');
		$row_data[] = $data->pueblos_originarios ? lang('yes') : lang('no');
		
		//$row_data[] = ($data->created) ? format_to_datetime($data->created) : "-";
		// $row_data[] = ($data->created) ? format_to_date_clients($data->id_cliente, $data->created) : "-";
		
		// $row_data[] = ($data->modified) ? format_to_date_clients($data->id_cliente, $data->modified) : "-";
		
		$view = modal_anchor(get_uri("AC_Beneficiaries/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_beneficiary'), "data-post-id" => $data->id));
		$edit = modal_anchor(get_uri("AC_Beneficiaries/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_beneficiary'), "data-post-id" => $data->id));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_beneficiary'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("AC_Beneficiaries/delete"), "data-action" => "delete-confirmation"));
		
		// Validaciones de Perfil
		if($puede_editar == 1 && $puede_eliminar ==1){
			$row_data[] = $view.$edit.$delete;		
		} else if($puede_editar == 1 && $puede_eliminar == 2){
			$row_data[] = $view.$edit;
			if($id_usuario == $data->created_by){
				$botones = array_pop($row_data);
				$botones = $botones.$delete;
				$row_data[] = $botones;
			}
		} else if($puede_editar == 1 && $puede_eliminar == 3){
			$row_data[] = $view.$edit;
		} else if($puede_editar == 2 && $puede_eliminar == 1){
			$row_data[] = $view;
			$botones = array_pop($row_data);
			if($id_usuario == $data->created_by){
				$botones = $botones.$edit.$delete;
			} else {
				$botones = $botones.$delete;
			}
			$row_data[] = $botones;
		} else if($puede_editar == 2 && $puede_eliminar == 2){
			if($id_usuario == $data->created_by){
				$row_data[] = $view.$edit.$delete;
			} else {
				$row_data[] = $view;
			}
		} else if($puede_editar == 2 && $puede_eliminar == 3){
			if($id_usuario == $data->created_by){
				$row_data[] = $view.$edit;
			} else {
				$row_data[] = $view;
			}
		} else if($puede_editar == 3 && $puede_eliminar == 1){
			$row_data[] = $view.$delete;
		} else if($puede_editar == 3 && $puede_eliminar == 2){
			if($id_usuario == $data->created_by){
				$row_data[] = $view.$delete;
			} else {
				$row_data[] = $view;
			}
		} else if($puede_editar == 3 && $puede_eliminar == 3){
			$row_data[] = $view;
		}
		
        return $row_data;
    }
	
	function view($id_beneficiario = 0) {

        if ($id_beneficiario) {
            $options = array("id" => $id_beneficiario);
            $info_beneficiario = $this->AC_Beneficiaries_model->get_details($options)->row();
            if ($info_beneficiario) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $info_beneficiario;

				$view_data['sociedad_desc'] = $this->AC_Feeders_societies_model->get_one($info_beneficiario->sociedad_desc)->nombre_sociedad;

				$view_data['ley_tea'] = $info_beneficiario->ley_tea ? lang('yes') : lang('no');

				$view_data['pueblos_originarios'] = $info_beneficiario->pueblos_originarios ? lang('yes') : lang('no');

				$creado_por = $this->Users_model->get_one($info_beneficiario->created_by);
				$view_data["creado_por"] = ($creado_por->id) ? $creado_por->first_name . " " . $creado_por->last_name : "-";
				
				$modificado_por = $this->Users_model->get_one($info_beneficiario->modified_by);
				$view_data["modificado_por"] = ($modificado_por->id) ? $modificado_por->first_name . " " . $modificado_por->last_name : "-";
				
				$this->load->view('ac_beneficiaries/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /**
	 * save_disability
     * 
     * Función utilizada en el appTable de Colaboradores.
     * Modifica el valor del campo Discapacidad.
	 *
	 * @author Christopher Sam Venegas
	 * @access public
	 * @return resource HTML
	 */
    function save_disability($id_beneficiario){

        $data = array(
            "discapacidad" => $this->input->post("value"),
            "modified_by" => $this->login_user->id,
            "modified" => get_current_utc_time()
        );
        $save_id = $this->AC_Beneficiaries_model->save($data, $id_beneficiario);


        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }

    }
		
	function upload_file($file_type = "") {

		$id_campo = $this->input->post("cid");
		
		if($id_campo){
			upload_file_to_temp("file", array("id_campo" => $id_campo));
		}else {
			upload_file_to_temp();
		}
		
	}
	
	function validate_file() {
        return validate_post_file($this->input->post("file_name"));
    }
	
	function create_beneficiaries_folder($id_beneficiario) {
		
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
		if(!file_exists(__DIR__.'/../../files/beneficiarios/client_'.$id_cliente."/beneficiario_".$id_beneficiario)) {
			if(mkdir(__DIR__.'/../../files/beneficiarios/client_'.$id_cliente."/beneficiario_".$id_beneficiario, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
		
	}	
	
	function download_file($id, $tipo_archivo) {

		$file_info = $this->AC_Beneficiaries_model->get_one($id);

		if(!$file_info){
			redirect("forbidden");
		}
		
		$filename = $file_info->$tipo_archivo;
		$id_cliente = $file_info->id_cliente;
		$id_beneficiario = $file_info->id;
		
        //serilize the path
		$file_data = serialize(array(array("file_name" => $filename)));
		download_app_files("files/beneficiarios/client_".$id_cliente."/beneficiario_".$id_beneficiario."/", $file_data);
    
	}
	
	function delete_file(){
		
		$id = $this->input->post('id');
		$id_campo = $this->input->post('campo');
		$file_info = $this->AC_Beneficiaries_model->get_one($id);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$id_cliente = $file_info->id_cliente;
		$filename = $file_info->$id_campo;
		//$file_path = "files/beneficiarios/client_".$id_cliente."/beneficiario_".$id_beneficiario."/".$filename;

		$campo_nuevo = $this->load->view("includes/form_file_uploader", array(
			"upload_url" => get_uri("AC_Beneficiaries/upload_file"),
			"validation_url" =>get_uri("AC_Beneficiaries/validate_file"),
			"obligatorio" => "",
			"id_campo" => $id_campo
		), true);
				
		echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo, 'id_campo' => $id_campo));
		
	}
	
	function get_excel(){
		
		$id_cliente = $this->login_user->client_id;
		$client_info = $this->Clients_model->get_one($id_cliente);
		
		$options = array(
			"id_cliente" => $id_cliente
		);
		
        $list_data = $this->AC_Beneficiaries_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
			$id_nacionalidad = $data->id_nacionalidad ? $data->id_nacionalidad : '-';
			$sexo = $data->sexo ? $data->sexo : '-';
			$fecha_nacimiento = $data->fecha_nacimiento ? $data->fecha_nacimiento : '-';
			$correo_organizativo = $data->correo_organizativo ? $data->correo_organizativo : '-';
			$sociedad = $data->sociedad ? $data->sociedad : '-';
			$sociedad_desc = $data->sociedad_desc ? $data->sociedad_desc : '-';
			$centro_de_costo_desc = $data->centro_de_costo_desc ? $data->centro_de_costo_desc : '-';
			$posicion_desc = $data->posicion_desc ? $data->posicion_desc : '-';
			$division_desc = $data->division_desc ? $data->division_desc : '-';
			$subdivision_desc = $data->subdivision_desc ? $data->subdivision_desc : '-';
			$fecha_inicio_contrato = $data->fecha_inicio_contrato ? $data->fecha_inicio_contrato : '-';
			$fecha_fin_contrato = $data->fecha_fin_contrato ? $data->fecha_fin_contrato : '-';
			$estado = $data->estado ? $data->estado : '-';
			$tipo_contrato = $data->tipo_contrato ? $data->tipo_contrato : '-';
			$division2_desc = $data->division2_desc ? $data->division2_desc : '-';
			$posicion_jefe_desc = $data->posicion_jefe_desc ? $data->posicion_jefe_desc : '-';
			$estado_civil = $data->estado_civil ? $data->estado_civil : '-';
			$area_de_personal = $data->area_de_personal ? $data->area_de_personal : '-';
			$departamento_desc = $data->departamento_desc ? $data->departamento_desc : '-';
			$job_code_desc = $data->job_code_desc ? $data->job_code_desc : '-';
			$nombre_completo = $data->nombre_completo ? $data->nombre_completo : '-';
			$nacionalidad = $data->nacionalidad ? $data->nacionalidad : '-';
			$comuna = $data->comuna ? $data->comuna : '-';
			$provincia = $data->provincia ? $data->provincia : '-';
			$discapacidad = $data->discapacidad ? $data->discapacidad : '-';
			$ley_tea = $data->ley_tea ? lang('yes') : lang('no');
			$pueblos_originarios = $data->pueblos_originarios ? lang('yes') : lang('no');
			
			$creado_por = $this->Users_model->get_one($data->created_by);
			$creado_por = ($creado_por->id) ? $creado_por->first_name . " " . $creado_por->last_name : "-";
			$modificado_por = $this->Users_model->get_one($data->modified_by);
			$modificado_por = ($modificado_por->id) ? $modificado_por->first_name . " " . $modificado_por->last_name : "-";
			
			$created = ($data->created) ? format_to_datetime_clients($id_cliente, $data->created) : "-";
			$modified = ($data->modified) ? format_to_datetime_clients($id_cliente, $data->modified) : "-";
			
            $result[] = array(
				$id_nacionalidad,
				$sexo,
				$fecha_nacimiento,
				$correo_organizativo,
				$sociedad,
				$sociedad_desc,
				$centro_de_costo_desc,
				$posicion_desc,
				$division_desc,
				$subdivision_desc,
				$fecha_inicio_contrato,
				$fecha_fin_contrato,
				$estado,
				$tipo_contrato,
				$division2_desc,
				$posicion_jefe_desc,
				$estado_civil,
				$area_de_personal,
				$departamento_desc,
				$job_code_desc,
				$nombre_completo,
				$nacionalidad,
				$comuna,
				$provincia,
				$discapacidad,
				$ley_tea,
				$pueblos_originarios
				
				// $creado_por,
				// $modificado_por,
				
				// $created,
				// $modified
			);
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

		$nombre_columnas[] = array("nombre_columna" => lang("national_id"), "id_tipo_campo" => "id_nacionalidad");
		$nombre_columnas[] = array("nombre_columna" => lang("sex"), "id_tipo_campo" => "sexo");
		$nombre_columnas[] = array("nombre_columna" => lang("birthdate"), "id_tipo_campo" => "fecha_nacimiento");
		$nombre_columnas[] = array("nombre_columna" => lang("organizational_email"), "id_tipo_campo" => "correo_organizativo");
		$nombre_columnas[] = array("nombre_columna" => lang("society"), "id_tipo_campo" => "sociedad");
		$nombre_columnas[] = array("nombre_columna" => lang("society_desc"), "id_tipo_campo" => "sociedad_desc");
		$nombre_columnas[] = array("nombre_columna" => lang("cost_center_desc"), "id_tipo_campo" => "centro_de_costo_desc");
		$nombre_columnas[] = array("nombre_columna" => lang("position_desc"), "id_tipo_campo" => "posicion_desc");
		$nombre_columnas[] = array("nombre_columna" => lang("division_desc"), "id_tipo_campo" => "division_desc");
		$nombre_columnas[] = array("nombre_columna" => lang("subdivision_desc"), "id_tipo_campo" => "subdivision_desc");
		$nombre_columnas[] = array("nombre_columna" => lang("contract_start_date"), "id_tipo_campo" => "fecha_inicio_contrato");
		$nombre_columnas[] = array("nombre_columna" => lang("contract_end_date"), "id_tipo_campo" => "fecha_fin_contrato");
		$nombre_columnas[] = array("nombre_columna" => lang("status"), "id_tipo_campo" => "estado");
		$nombre_columnas[] = array("nombre_columna" => lang("contract_type"), "id_tipo_campo" => "tipo_contrato");
		$nombre_columnas[] = array("nombre_columna" => lang("division2_desc"), "id_tipo_campo" => "division2_desc");
		$nombre_columnas[] = array("nombre_columna" => lang("boss_position_desc"), "id_tipo_campo" => "posicion_jefe_desc");
		$nombre_columnas[] = array("nombre_columna" => lang("civil_status"), "id_tipo_campo" => "estado_civil");
		$nombre_columnas[] = array("nombre_columna" => lang("personnel_area"), "id_tipo_campo" => "area_de_personal");
		$nombre_columnas[] = array("nombre_columna" => lang("department_desc"), "id_tipo_campo" => "departamento_desc");
		$nombre_columnas[] = array("nombre_columna" => lang("jobcode_desc"), "id_tipo_campo" => "job_code_desc");
		$nombre_columnas[] = array("nombre_columna" => lang("fullname"), "id_tipo_campo" => "nombre_completo");
		$nombre_columnas[] = array("nombre_columna" => lang("nationality"), "id_tipo_campo" => "nacionalidad");
		$nombre_columnas[] = array("nombre_columna" => lang("commune"), "id_tipo_campo" => "comuna");
		$nombre_columnas[] = array("nombre_columna" => lang("province"), "id_tipo_campo" => "provincia");
		$nombre_columnas[] = array("nombre_columna" => lang("disability"), "id_tipo_campo" => "discapacidad");
		$nombre_columnas[] = array("nombre_columna" => lang("tea_law"), "id_tipo_campo" => "ley_tea");
		$nombre_columnas[] = array("nombre_columna" => lang("native_people"), "id_tipo_campo" => "pueblos_originarios");
		
		// $nombre_columnas[] = array("nombre_columna" => lang("created_by"), "id_tipo_campo" => "created_by");
		// $nombre_columnas[] = array("nombre_columna" => lang("modified_by"), "id_tipo_campo" => "modified_by");
		// $nombre_columnas[] = array("nombre_columna" => lang("created_date"), "id_tipo_campo" => "created_date");
		// $nombre_columnas[] = array("nombre_columna" => lang("modified_date"), "id_tipo_campo" => "modified_date");
		
		// HEADER
		$hoy = date('d-m-Y');
		$fecha = date(get_setting_client_mimasoft($client_info->id, "date_format"), strtotime($hoy));
		$hora = format_to_time_clients($client_info->id, get_current_utc_time("H:i:s"));
		
		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', lang("beneficiaries"))
			->setCellValue('C2', $client_info->company_name)
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
				$valor = $res[$index_columnas];
				
				if(!is_array($columna)){
					
					$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
					
				} else {
					
					if($columna["id_tipo_campo"] == "id_nacionalidad" || $columna["id_tipo_campo"] == "sexo" ||
					$columna["id_tipo_campo"] == "fecha_nacimiento" || $columna["id_tipo_campo"] == "correo_organizativo" ||
					$columna["id_tipo_campo"] == "sociedad" || $columna["id_tipo_campo"] == "sociedad_desc" ||
					$columna["id_tipo_campo"] == "centro_de_costo_desc" || $columna["id_tipo_campo"] == "posicion_desc" ||
					$columna["id_tipo_campo"] == "division_desc" || $columna["id_tipo_campo"] == "subdivision_desc" ||
					$columna["id_tipo_campo"] == "fecha_inicio_contrato" || $columna["id_tipo_campo"] == "fecha_fin_contrato" ||
					$columna["id_tipo_campo"] == "estado" || $columna["id_tipo_campo"] == "tipo_contrato" ||
					$columna["id_tipo_campo"] == "division2_desc" || $columna["id_tipo_campo"] == "posicion_jefe_desc" ||
					$columna["id_tipo_campo"] == "estado_civil" || $columna["id_tipo_campo"] == "area_de_personal" ||
					$columna["id_tipo_campo"] == "departamento_desc" || $columna["id_tipo_campo"] == "job_code_desc" ||
					$columna["id_tipo_campo"] == "nombre_completo" || $columna["id_tipo_campo"] == "nacionalidad" ||
					$columna["id_tipo_campo"] == "comuna" || $columna["id_tipo_campo"] == "provincia" ||
					$columna["id_tipo_campo"] == "discapacidad" || $columna["id_tipo_campo"] == "ley_tea" ||
					$columna["id_tipo_campo"] == "pueblos_originarios"){
					
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
				
				$doc->getActiveSheet()->getStyle($name_col.$row)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
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
		foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}
		
		// FORMATO TEXTO A TODAS LAS CELDAS DE CONTENIDO
		$doc->getActiveSheet()->getStyle('A6:AB'.$doc->getActiveSheet()->getHighestRow())->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		//$doc->getActiveSheet()->setCellValueExplicit('Z6', '30.45', PHPExcel_Cell_DataType::TYPE_STRING);
		
		$nombre_hoja = strlen(lang("beneficiaries")) > 31 ? substr(lang("beneficiaries"), 0, 28).'...' : lang("beneficiaries");
		$nombre_hoja = $nombre_hoja ? $nombre_hoja : " ";
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		$filename = $client_info->sigla."_".lang("beneficiaries")."_".date('Y-m-d');
		$filename = $filename.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');
		exit;
	
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
	
	/* function get_optional_phone_contact_field() {
		$html = form_input(array(
            "id" => "telefono_contacto",
            "name" => "telefono_contacto",
            "value" => "",
            "class" => "form-control",
            "placeholder" => lang('phone'),
            "autofocus" => true,
            "autocomplete"=> "off",
            "maxlength" => "255"
        ));
		
		echo $html;
	} */
	
	/* function get_communes_by_macrozone(){
		
		$client_area = $this->session->client_area;
		$id_macrozona = $this->input->post("id_macrozona");
		//$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		if($id_macrozona){
			$comunas = $this->AC_Communes_model->get_communes_of_macrozone($id_macrozona)->result();
			$array_comunas = array("" => "-");
			foreach($comunas as $comuna){
				$array_comunas[$comuna->id] = $comuna->nombre;
			}
		}else{
			$array_comunas = array("" => "-");
		}
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="comuna" class="col-md-3">'.lang('commune').'</label>';
		$html .= '<div class="col-md-9">';
		$html .= form_dropdown("comuna", array("" => "-") + $array_comunas, "", "id='comuna' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
		
	} */
	
	/* function get_legal_beneficiaries_by_comune(){
		
    	$client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;       
		$id_comuna = $this->input->post('id_comuna');
		
		$beneficiarios_comuna = $this->AC_Beneficiaries_model->get_all_where(array(
			"id_cliente" => $id_cliente,
			"client_area" => $client_area,
			"tipo_stakeholder" => "legal_person",
			"id_comuna" => $id_comuna,
			"deleted" => 0
		))->result();
		
		$array_beneficiarios_comuna = array();
		foreach($beneficiarios_comuna as $beneficiario){
			$array_beneficiarios_comuna[$beneficiario->id] = $beneficiario->nombre_beneficiario;
		}
		
		natcasesort($array_beneficiarios_comuna);
		
		$html = '';
        $html .= '<div class="form-group">';
            $html .= '<label for="organizacion" class="col-md-3">'.lang('legal_beneficiaries').'</label>';
            $html .= '<div class="col-md-9">';
			$html .= form_multiselect(
						"organizacion[]", 
						$array_beneficiarios_comuna, 
						"", 
						"id='organizacion' class='select2 validate-hidden' data-rule-required='true' data-msg-required='".lang('field_required')."'"
					);
			$html .= '</div>';
        $html .= '</div>';
		
		$view_data["opciones_beneficiarios"] = $opciones_beneficiarios;
		
        echo $html;

    } */
	
	/* function get_existing_rut(){
		
    	$client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;
		$rut = $this->input->post('rut');
		$id = $this->input->post('id');
		
		$beneficiarios = $this->AC_Beneficiaries_model->get_all_where(array(
			"id_cliente" => $id_cliente,
			"client_area" => $client_area,
			"rut_beneficiario" => $rut,
			"deleted" => 0
		))->result();
		
		$html = '';
		$cant_beneficiarios = count($beneficiarios);
		if($id){
			$rut_editando = $this->AC_Beneficiaries_model->get_one($id)->rut_beneficiario;
			if(($cant_beneficiarios == 0) || $cant_beneficiarios == 1 && $rut_editando == $rut){
				$html = '<label class="color-success"><i class="fa fa-check"></i> '.lang('beneficiarie_rut_doesnt_exist').'</label>';
			}else{
				$html = '<label class="color-danger"><i class="fa fa-exclamation-triangle"></i> '.lang('beneficiarie_rut_already_exist').'</label>';
			}
		}else{
			if($cant_beneficiarios > 0){
				$html = '<label class="color-danger"><i class="fa fa-exclamation-triangle"></i> '.lang('beneficiarie_rut_already_exist').'</label>';
			}else{
				$html = '<label class="color-success"><i class="fa fa-check"></i> '.lang('beneficiarie_rut_doesnt_exist').'</label>';
			}
		}
		
        echo $html;

    } */
}