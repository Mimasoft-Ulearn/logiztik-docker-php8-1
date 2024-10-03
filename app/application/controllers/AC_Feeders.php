<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class AC_Feeders extends MY_Controller {
	
	private $id_client_context_module;
	private $id_client_context_submodule;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$client_area = $this->session->client_area;
		if($client_area == "territory"){
			$this->id_client_context_module = 9;
		} 
		// if($client_area == "distribution"){
		// 	$this->id_client_context_module = 9;
		// }
		$this->id_client_context_submodule = 0;
		
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
		
    }

    function index() {
		
		$client_area = $this->session->client_area;
		
		$view_data["client_area"] = $client_area;
		
		$view_data["client_agreements_info"] = $this->AC_Client_agreements_info_model->get_one_where(array(
			"client_area" => $client_area,
			"deleted" => 0
		));
		
        $this->template->rander("ac_feeders/index", $view_data);
    }
	
	function societies(){
		
		$id_usuario = $this->session->user_id;
		$client_area = $this->session->client_area;
		$view_data["client_area"] = $client_area;
		
		$view_data["puede_ver"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
		$view_data["puede_agregar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "agregar");
		$view_data["puede_eliminar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");
		
		/* $view_data["client_agreements_info"] = $this->AC_Client_agreements_info_model->get_one_where(array(
			"client_area" => $client_area,
			"deleted" => 0
		)); */
		
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		$view_data["id_cliente"] = $id_cliente;
		
		$opciones_registro = array(
			"id_cliente" => $id_cliente,
			// "client_area" => $client_area
		);
		$registros = $this->AC_Feeders_societies_model->get_details($opciones_registro)->result();
		$num_registros = count($registros);
		$view_data["num_registros"] = $num_registros;
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = max($arrayFechas);
		$view_data["fecha_modificacion"] = $fecha_modificacion;
		$view_data["descripcion"] = lang("ac_feeder_society_description");
		
        $this->template->rander("ac_feeders/societies/index", $view_data);
		
	}

	function modal_form_societies(){
	
		$client_area = $this->session->client_area;
		$id_ac_feeder_society = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric"
        ));
		
		$view_data['client_area'] = $client_area;
        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		
		$view_data["view"] = $this->input->post('view');
        $view_data['model_info'] = $this->AC_Feeders_societies_model->get_one($id_ac_feeder_society);
		
		$this->load->view('ac_feeders/societies/modal_form', $view_data);
		
	}
	
	function save_societies(){
		
		$client_area = $this->session->client_area;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
        $id_ac_feeder_society = $this->input->post('id');
		
		$data_feeder_society = array(
			"id_cliente" => $id_cliente,
			"nombre_sociedad" => $this->input->post("nombre_sociedad"),
			"observaciones" => $this->input->post("observaciones"),
			"client_area" => $client_area
		);
		
		if($id_ac_feeder_society){
			$data_feeder_society["modified_by"] = $this->login_user->id;
			$data_feeder_society["modified"] = get_current_utc_time();
			$save_id = $this->AC_Feeders_societies_model->save($data_feeder_society, $id_ac_feeder_society);
		} else {
			$data_feeder_society["created_by"] = $this->login_user->id;
			$data_feeder_society["created"] = get_current_utc_time();
			$save_id = $this->AC_Feeders_societies_model->save($data_feeder_society);
		}
		
		$opciones_registro = array(
			"id_cliente" => $id_cliente,
			"client_area" => $client_area
		);
		
		$registros = $this->AC_Feeders_societies_model->get_details($opciones_registro)->result();
		$num_registros = count($registros);
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = format_to_datetime_clients($id_cliente, max($arrayFechas));
		
		if ($save_id) {
			
			// Guardar histórico notificaciones
			$options = array(
				"id_client" => $id_cliente,
				"id_user" => $this->session->user_id,
				"module_level" => "general",
				"id_client_context_module" => $this->id_client_context_module,
				"id_client_context_submodule" => $this->id_client_context_submodule,
				"event" => $id_ac_feeder_society ? "society_edit" : "society_add",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options);
			
            echo json_encode(array("success" => true, "data" => $this->_row_data_societies($save_id), 'id' => $save_id, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros,'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
	}
	
	function list_data_societies(){
		
		$id_usuario = $this->session->user_id;
		$client_area = $this->session->client_area;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
		$puede_ver = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
		
		$options = array(
			"id_cliente" => $id_cliente,
            "client_area" => $client_area,
        );
		
        $list_data = $this->AC_Feeders_societies_model->get_details($options)->result();
		
        $result = array();
        foreach ($list_data as $data) {
			if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row_societies($data);
			}
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row_societies($data);
				}
			}
			if($puede_ver == 3){ //Ninguno
				$result[1] = array();
			}
        }
        echo json_encode(array("data" => $result));
		
	}
	
	private function _row_data_societies($id) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->AC_Feeders_societies_model->get_details($options)->row();
        return $this->_make_row_societies($data);
    }
	
	private function _make_row_societies($data) {
		
		$id_usuario = $this->session->user_id;
		$puede_editar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "editar");
		$puede_eliminar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		
		$row_data[] = $data->id;
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
		}
		
		$row_data[] = $data->nombre_sociedad;
		
		$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->observaciones.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$row_data[] = ($data->observaciones) ? $tooltip_observaciones : "-";
		
		/*
        $row_data[] =  modal_anchor(get_uri("AC_Feeders/view_societies/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_society'), "data-post-id" => $data->id))
				.  modal_anchor(get_uri("AC_Feeders/modal_form_societies"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_society'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_society'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("AC_Feeders/delete_societies"), "data-action" => "delete-confirmation"));
		
		*/
		$view = modal_anchor(get_uri("AC_Feeders/view_societies/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_society'), "data-post-id" => $data->id));
		$edit = modal_anchor(get_uri("AC_Feeders/modal_form_societies"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_society'), "data-post-id" => $data->id));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_society'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("AC_Feeders/delete_societies"), "data-action" => "delete-confirmation", "data-custom" => true));
		
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
	
	function view_societies($id_ac_feeder_society = 0){
		
		if($id_ac_feeder_society){
			
			$options = array("id" => $id_ac_feeder_society);
			$info_feeder_society = $this->AC_Feeders_societies_model->get_details($options)->row();
			
			if ($info_feeder_society) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $info_feeder_society;
				
				$creado_por = $this->Users_model->get_one($info_feeder_society->created_by);
				$view_data["creado_por"] = ($creado_por->id) ? $creado_por->first_name . " " . $creado_por->last_name : "-";
				
				$modificado_por = $this->Users_model->get_one($info_feeder_society->modified_by);
				$view_data["modificado_por"] = ($modificado_por->id) ? $modificado_por->first_name . " " . $modificado_por->last_name : "-";
				
				$this->load->view('ac_feeders/societies/view', $view_data);
				
			} else {
				show_404();
			}
			
		} else {
			show_404();
		}
		
	}
	
	function delete_societies() {
		
		$client_area = $this->session->client_area;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
		$opciones_registro = array(
			"id_cliente" => $id_cliente,
			"client_area" => $client_area
		);
		
		$registros = $this->AC_Feeders_societies_model->get_details($opciones_registro)->result();
		//$num_registros = count($registros);
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = format_to_date_clients($id_cliente, max($arrayFechas));
		
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
		/* $informaciones = $this->AC_Information_model->get_all_where(array(
			"id_feeder_sociedad" => $id,
			"deleted" => 0
		))->result_array();
		
		if(count($informaciones)){
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
			exit();
		} */

		$actividad = $this->AC_Activities_model->get_all_where(array(
			"id_cliente" => $id_cliente, 
			"id_feeder_sociedad" => $id,
			"deleted" => 0
		))->result();
		
		
		if($actividad){
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
			exit();
		}

		
        if ($this->input->post('undo')) {
            if ($this->AC_Feeders_societies_model->delete($id, true)) {
				
				$registros = $this->AC_Feeders_societies_model->get_details($opciones_registro)->result();
				$num_registros = count($registros);
				
                echo json_encode(array("success" => true, "data" => $this->_row_data_societies($id), 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->AC_Feeders_societies_model->delete($id)) {
				
				$registros = $this->AC_Feeders_societies_model->get_details($opciones_registro)->result();
				$num_registros = count($registros);
				
				// Guardar histórico notificaciones
				$options = array(
					"id_client" => $id_cliente,
					"id_user" => $this->session->user_id,
					"module_level" => "general",
					"id_client_context_module" => $this->id_client_context_module,
					"id_client_context_submodule" => $this->id_client_context_submodule,
					"event" => "society_delete",
					"id_element" => $id
				);
				ayn_save_historical_notification($options);
				
                echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function delete_societies_multiple(){
		
		$data_ids = json_decode($this->input->post('data_ids'));
		$id_user = $this->session->user_id;
		$client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;
		
		// VALIDACIÓN DE ELIMINACIÓN DE REGISTROS SEGÚN PERFIL
		$puede_eliminar = $this->general_profile_access($id_user, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		$eliminar = TRUE;
		foreach($data_ids as $id){
			if($puede_eliminar == 2){ // Propios
				$row = $this->AC_Feeders_societies_model->get_one($id);
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
				"id_feeder_sociedad" => $id,
				"deleted" => 0
			))->result_array();
			
			if(count($informaciones)){
				echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
				exit();
			} */

			
			$actividad = $this->AC_Activities_model->get_all_where(array(
				"id_cliente" => $id_cliente, 
				"id_feeder_sociedad" => $id,
				"deleted" => 0
			))->result();
			
			
			if($actividad){
				echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
				exit();
			}
			
		}
		
		$deleted_values = false;
		foreach($data_ids as $id){
			if($this->AC_Feeders_societies_model->delete($id)) {
				$deleted_values = true;
			} else {
				$deleted_values = false;
				break;
			}
		}
					
		if($deleted_values){

			// Guardar histórico notificaciones
			foreach($data_ids as $index => $id){
				$options = array(
					"id_client" => $id_cliente,
					"id_user" => $id_user,
					"module_level" => "general",
					"id_client_context_module" => $this->id_client_context_module,
					"id_client_context_submodule" => $this->id_client_context_submodule,
					"event" => "society_delete",
					"id_element" => $id,
					"massive" => (count($data_ids) > 1) ? 1 : 0,
					"is_email_sended" => ($index !== count($data_ids) -1) ? 1 : 0
				);
				ayn_save_historical_notification($options);
			}
			
			$opciones_registro = array(
				"id_cliente" => $id_cliente,
				"client_area" => $client_area
			);
			$registros = $this->AC_Feeders_societies_model->get_details($opciones_registro)->result();
			
			$arrayFechas = array();
			foreach($registros as $index => $reg){
				if(!$reg->modified){
					$arrayFechas[$index] = $reg->created;
				} else {
					$arrayFechas[$index] = $reg->modified;
				}
			}
			$fecha_modificacion = format_to_datetime(max($arrayFechas));
			$num_registros = count($registros);
			
			echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('multiple_record_deleted')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
		}	
		
	}
	
	function centrals(){
		
		$id_usuario = $this->session->user_id;
		$client_area = $this->session->client_area;
		$view_data["client_area"] = $client_area;
		
		$view_data["puede_ver"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
		$view_data["puede_agregar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "agregar");	
		$view_data["puede_eliminar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");
		
		$view_data["client_agreements_info"] = $this->AC_Client_agreements_info_model->get_one_where(array(
			"client_area" => $client_area,
			"deleted" => 0
		));
		
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		$view_data["id_cliente"] = $id_cliente;
		
		$opciones_registro = array(
			"id_cliente" => $id_cliente,
			"client_area" => $client_area
		);
		$registros = $this->AC_Feeders_centrals_model->get_details($opciones_registro)->result();
		$num_registros = count($registros);
		$view_data["num_registros"] = $num_registros;
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = max($arrayFechas);
		$view_data["fecha_modificacion"] = $fecha_modificacion;
		$view_data["descripcion"] = lang("ac_feeder_central_description");
		
        $this->template->rander("ac_feeders/centrals/index", $view_data);
		
	}
	
	function modal_form_centrals(){
	
		$client_area = $this->session->client_area;
		$id_ac_feeder_centrals = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric"
        ));
		
		$view_data['client_area'] = $client_area;
        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		
		$view_data["view"] = $this->input->post('view');
        $view_data['model_info'] = $this->AC_Feeders_centrals_model->get_one($id_ac_feeder_centrals);
		$view_data["macrozonas"] = array("" => "-") + $this->AC_Macrozones_model->get_dropdown_list(array("nombre"), 'id', array("client_area" => $client_area));
		
		$this->load->view('ac_feeders/centrals/modal_form', $view_data);
		
	}
	
	function save_centrals(){
		
		$client_area = $this->session->client_area;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
        $id_ac_feeder_central = $this->input->post('id');
		
		$data_feeder_central = array(
			"id_cliente" => $id_cliente,
			"id_macrozona" => $this->input->post("id_macrozona"),
			"nombre_central" => $this->input->post("nombre_central"),
			"observaciones" => $this->input->post("observaciones"),
			"client_area" => $client_area
		);
		
		if($id_ac_feeder_central){
			$data_feeder_central["modified_by"] = $this->login_user->id;
			$data_feeder_central["modified"] = get_current_utc_time();
			$save_id = $this->AC_Feeders_centrals_model->save($data_feeder_central, $id_ac_feeder_central);
		} else {
			// $data_feeder_central["color"] = '#'.random_color();
			$data_feeder_central["created_by"] = $this->login_user->id;
			$data_feeder_central["created"] = get_current_utc_time();
			$save_id = $this->AC_Feeders_centrals_model->save($data_feeder_central);
		}
		
		$opciones_registro = array(
			"id_cliente" => $id_cliente,
			"client_area" => $client_area
		);
		
		$registros = $this->AC_Feeders_centrals_model->get_details($opciones_registro)->result();
		$num_registros = count($registros);
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = format_to_datetime_clients($id_cliente, max($arrayFechas));
		
		if ($save_id) {
			
			// Guardar histórico notificaciones
			$options = array(
				"id_client" => $id_cliente,
				"id_user" => $this->session->user_id,
				"module_level" => "general",
				"id_client_context_module" => $this->id_client_context_module,
				"id_client_context_submodule" => $this->id_client_context_submodule,
				"event" => $id_ac_feeder_central ? "central_edit" : "central_add",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options);
			
            echo json_encode(array("success" => true, "data" => $this->_row_data_centrals($save_id), 'id' => $save_id, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros,'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
	}
	
	function list_data_centrals(){
		
		$id_usuario = $this->session->user_id;
		$client_area = $this->session->client_area;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
		$puede_ver = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");

		$options = array(
			"id_cliente" => $id_cliente,
            "client_area" => $client_area,
        );
		
        $list_data = $this->AC_Feeders_centrals_model->get_details($options)->result();
		
        $result = array();
        foreach ($list_data as $data) {
			if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row_centrals($data);
			}
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row_centrals($data);
				}
			}
			if($puede_ver == 3){ //Ninguno
				$result[1] = array();
			}
        }
        echo json_encode(array("data" => $result));
		
	}
	
	private function _row_data_centrals($id) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->AC_Feeders_centrals_model->get_details($options)->row();
        return $this->_make_row_centrals($data);
    }
	
	private function _make_row_centrals($data) {
		
		$id_usuario = $this->session->user_id;
		$puede_editar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "editar");
		$puede_eliminar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");
		
		$row_data[] = $data->id;
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
		}
		
		$row_data[] = $data->nombre_central;
		
		$macrozona = $this->AC_Macrozones_model->get_one($data->id_macrozona)->nombre;
		$row_data[] = $macrozona;
		
		$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->observaciones.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$row_data[] = ($data->observaciones) ? $tooltip_observaciones : "-";
		
		/*
        $row_data[] =  modal_anchor(get_uri("AC_Feeders/view_centrals/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_central'), "data-post-id" => $data->id))
				.  modal_anchor(get_uri("AC_Feeders/modal_form_centrals"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_central'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_central'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("AC_Feeders/delete_centrals"), "data-action" => "delete-confirmation"));
		*/
	
		$view = modal_anchor(get_uri("AC_Feeders/view_centrals/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_central'), "data-post-id" => $data->id));
		$edit = modal_anchor(get_uri("AC_Feeders/modal_form_centrals"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_central'), "data-post-id" => $data->id));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_central'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("AC_Feeders/delete_centrals"), "data-action" => "delete-confirmation", "data-custom" => true));
		
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
	
	function view_centrals($id_ac_feeder_central = 0){
		
		if($id_ac_feeder_central){
			
			$options = array("id" => $id_ac_feeder_central);
			$info_feeder_central = $this->AC_Feeders_centrals_model->get_details($options)->row();
			
			if ($info_feeder_central) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $info_feeder_central;
				
				$macrozona = $this->AC_Macrozones_model->get_one($info_feeder_central->id_macrozona)->nombre;
				$view_data["macrozona"] = $macrozona;
				
				$creado_por = $this->Users_model->get_one($info_feeder_central->created_by);
				$view_data["creado_por"] = ($creado_por->id) ? $creado_por->first_name . " " . $creado_por->last_name : "-";
				
				$modificado_por = $this->Users_model->get_one($info_feeder_central->modified_by);
				$view_data["modificado_por"] = ($modificado_por->id) ? $modificado_por->first_name . " " . $modificado_por->last_name : "-";
				
				$this->load->view('ac_feeders/centrals/view', $view_data);
				
			} else {
				show_404();
			}
			
		} else {
			show_404();
		}
		
	}
	
	function delete_centrals() {
		
		$client_area = $this->session->client_area;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
		$opciones_registro = array(
			"id_cliente" => $id_cliente,
			"client_area" => $client_area
		);
		
		$registros = $this->AC_Feeders_centrals_model->get_details($opciones_registro)->result();
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = format_to_date_clients($id_cliente, max($arrayFechas));
		
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
		$informaciones = $this->AC_Information_model->get_all_where(array(
			"id_feeder_central" => $id,
			"deleted" => 0
		))->result_array();
		
		if(count($informaciones)){
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
			exit();
		}
		
        if ($this->input->post('undo')) {
            if ($this->AC_Feeders_centrals_model->delete($id, true)) {
				
				$registros = $this->AC_Feeders_centrals_model->get_details($opciones_registro)->result();
				$num_registros = count($registros);
				
                echo json_encode(array("success" => true, "data" => $this->_row_data_centrals($id), 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->AC_Feeders_centrals_model->delete($id)) {
				
				$registros = $this->AC_Feeders_centrals_model->get_details($opciones_registro)->result();
				$num_registros = count($registros);
				
				// Guardar histórico notificaciones
				$options = array(
					"id_client" => $id_cliente,
					"id_user" => $this->session->user_id,
					"module_level" => "general",
					"id_client_context_module" => $this->id_client_context_module,
					"id_client_context_submodule" => $this->id_client_context_submodule,
					"event" => "central_delete",
					"id_element" => $id
				);
				ayn_save_historical_notification($options);
				
                echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function delete_centrals_multiple() {
		
		$data_ids = json_decode($this->input->post('data_ids'));
		$id_user = $this->session->user_id;
		$client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;
		
		// VALIDACIÓN DE ELIMINACIÓN DE REGISTROS SEGÚN PERFIL
		$puede_eliminar = $this->general_profile_access($id_user, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		$eliminar = TRUE;
		foreach($data_ids as $id){
			if($puede_eliminar == 2){ // Propios
				$row = $this->AC_Feeders_centrals_model->get_one($id);
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
			
			$informaciones = $this->AC_Information_model->get_all_where(array(
				"id_feeder_central" => $id,
				"deleted" => 0
			))->result_array();
			
			if(count($informaciones)){
				echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
				exit();
			}
			
		}
		
		$deleted_values = false;
		foreach($data_ids as $id){
			if($this->AC_Feeders_centrals_model->delete($id)) {
				$deleted_values = true;
			} else {
				$deleted_values = false;
				break;
			}
		}
					
		if($deleted_values){
			
			// Guardar histórico notificaciones
			foreach($data_ids as $index => $id){
				$options = array(
					"id_client" => $id_cliente,
					"id_user" => $id_user,
					"module_level" => "general",
					"id_client_context_module" => $this->id_client_context_module,
					"id_client_context_submodule" => $this->id_client_context_submodule,
					"event" => "central_delete",
					"id_element" => $id,
					"massive" => (count($data_ids) > 1) ? 1 : 0,
					"is_email_sended" => ($index !== count($data_ids) -1) ? 1 : 0
				);
				ayn_save_historical_notification($options);
			}
			
			$opciones_registro = array(
				"id_cliente" => $id_cliente,
				"client_area" => $client_area
			);
			$registros = $this->AC_Feeders_centrals_model->get_details($opciones_registro)->result();
			
			$arrayFechas = array();
			foreach($registros as $index => $reg){
				if(!$reg->modified){
					$arrayFechas[$index] = $reg->created;
				} else {
					$arrayFechas[$index] = $reg->modified;
				}
			}
			$fecha_modificacion = format_to_date_clients($id_cliente, max($arrayFechas));
			$num_registros = count($registros);
			
			echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('multiple_record_deleted')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
		}	
		
	}
	
	function type_of_activities(){
		
		$id_usuario = $this->session->user_id;
		$client_area = $this->session->client_area;
		$view_data["client_area"] = $client_area;
		
		$view_data["puede_ver"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
		$view_data["puede_agregar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "agregar");
		$view_data["puede_eliminar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		// $view_data["client_agreements_info"] = $this->AC_Client_agreements_info_model->get_one_where(array(
		// 	"client_area" => $client_area,
		// 	"deleted" => 0
		// ));
		
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		$view_data["id_cliente"] = $id_cliente;
		
		$opciones_registro = array(
			"id_cliente" => $id_cliente,
		);
		$registros = $this->AC_Feeders_type_of_activities_model->get_details($opciones_registro)->result();
		$num_registros = count($registros);
		$view_data["num_registros"] = $num_registros;
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = max($arrayFechas);
		$view_data["fecha_modificacion"] = $fecha_modificacion;
		$view_data["descripcion"] = lang("ac_feeder_type_of_activities_description");
		
       $this->template->rander("ac_feeders/type_of_activities/index", $view_data);
		
	}
	
	function modal_form_type_of_activities(){
			
		$client_area = $this->session->client_area;
		$id_ac_feeder_type_of_activity = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric"
        ));
		
		$view_data['client_area'] = $client_area;
        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		
		$view_data["view"] = $this->input->post('view');
        $view_data['model_info'] = $this->AC_Feeders_type_of_activities_model->get_one($id_ac_feeder_type_of_activity);
		
		$array_tipos_actividad = array("" => "-");
		$tipos_actividad = $this->AC_Types_of_activities_model->get_all()->result();
		foreach($tipos_actividad as $tipo_actividad){
			$array_tipos_actividad[$tipo_actividad->id] = lang($tipo_actividad->name);
		}
		$view_data["tipos_actividad"] = $array_tipos_actividad;
		
		$this->load->view('ac_feeders/type_of_activities/modal_form', $view_data);
		
	}
	
	function save_type_of_activities(){
		
		// $client_area = $this->session->client_area;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
        $id_ac_feeder_type_of_activity = $this->input->post('id');
		
		$data_feeder_type_of_activity = array(
			"id_cliente" => $id_cliente,
			"id_tipo_actividad" => $this->input->post("id_tipo_actividad"),
			"actividad" => $this->input->post("actividad"),
			"descripcion_actividad" => $this->input->post("descripcion_actividad")
		);
		
		if($id_ac_feeder_type_of_activity){
			$data_feeder_type_of_activity["modified_by"] = $this->login_user->id;
			$data_feeder_type_of_activity["modified"] = get_current_utc_time();
			$save_id = $this->AC_Feeders_type_of_activities_model->save($data_feeder_type_of_activity, $id_ac_feeder_type_of_activity);
		} else {
			$data_feeder_type_of_activity["created_by"] = $this->login_user->id;
			$data_feeder_type_of_activity["created"] = get_current_utc_time();
			$save_id = $this->AC_Feeders_type_of_activities_model->save($data_feeder_type_of_activity);
		}
		
		$opciones_registro = array(
			"id_cliente" => $id_cliente
		);
		
		$registros = $this->AC_Feeders_type_of_activities_model->get_details($opciones_registro)->result();
		$num_registros = count($registros);
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = format_to_datetime_clients($id_cliente, max($arrayFechas));
		
		if ($save_id) {
			
			// Guardar histórico notificaciones
			/* $options = array(
				"id_client" => $id_cliente,
				"id_user" => $this->session->user_id,
				"module_level" => "general",
				"id_client_context_module" => $this->id_client_context_module,
				"id_client_context_submodule" => $this->id_client_context_submodule,
				"event" => $id_ac_feeder_type_of_agreement ? "type_of_agreement_edit" : "type_of_agreement_add",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options); */
			
            echo json_encode(array("success" => true, "data" => $this->_row_data_type_of_activities($save_id), 'id' => $save_id, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros,'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
	}

	function list_data_type_of_activities(){
		
		$id_usuario = $this->session->user_id;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
		$puede_ver = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");

		$options = array(
			"id_cliente" => $id_cliente,
        );
		
        $list_data = $this->AC_Feeders_type_of_activities_model->get_details($options)->result();
		
        $result = array();
        foreach ($list_data as $data) {
			if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row_type_of_activities($data);
			}
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row_type_of_activities($data);
				}
			}
			if($puede_ver == 3){ //Ninguno
				$result[1] = array();
			}
        }
        echo json_encode(array("data" => $result));
		
	}
	
	private function _row_data_type_of_activities($id) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->AC_Feeders_type_of_activities_model->get_details($options)->row();
        return $this->_make_row_type_of_activities($data);
    }
	
	private function _make_row_type_of_activities($data) {
		
		$id_usuario = $this->session->user_id;
		$puede_editar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "editar");
		$puede_eliminar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		$row_data[] = $data->id;
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
		}
		

		$tipo_actividad = $this->AC_Types_of_activities_model->get_one($data->id_tipo_actividad);

		$row_data[] = lang($tipo_actividad->name);
		$row_data[] = $data->actividad;
		
		$tooltip_descripcion = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->descripcion_actividad.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$row_data[] = ($data->descripcion_actividad) ? $tooltip_descripcion : "-";
		
		$acciones = "";
		$view = modal_anchor(get_uri("AC_Feeders/view_type_of_activities/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_type_of_activity'), "data-post-id" => $data->id));

		
		$edit = modal_anchor(get_uri("AC_Feeders/modal_form_type_of_activities"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_type_of_activity'), "data-post-id" => $data->id));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_type_of_activity'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("AC_Feeders/delete_type_of_activities"), "data-action" => "delete-confirmation", "data-custom" => true));

		if($puede_editar == 1){
			$acciones .= $edit;
		}
		if($puede_editar == 2){
			if($data->created_by == $id_usuario){
				$acciones .= $edit;
			}
		}
		if($puede_eliminar == 1){
			$acciones .= $delete;
		}
		if($puede_eliminar == 2){
			if($data->created_by == $id_usuario){
				$acciones .= $delete;
			}
		}				
		
		$acciones = $view.$acciones;

        $row_data[] =  $acciones;
				

        return $row_data;
    }
	
	function view_type_of_activities($id_ac_feeder_type_of_activity = 0){
		
		if($id_ac_feeder_type_of_activity){
			
			$options = array("id" => $id_ac_feeder_type_of_activity);
			$info_feeder_type_of_activity = $this->AC_Feeders_type_of_activities_model->get_details($options)->row();
			
			if ($info_feeder_type_of_activity) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $info_feeder_type_of_activity;

				$tipo_actividad = $this->AC_Types_of_activities_model->get_one($info_feeder_type_of_activity->id_tipo_actividad);
				$view_data["tipo_actividad"] = lang($tipo_actividad->name);
				
				$creado_por = $this->Users_model->get_one($info_feeder_type_of_activity->created_by);
				$view_data["creado_por"] = ($creado_por->id) ? $creado_por->first_name . " " . $creado_por->last_name : "-";
				
				$modificado_por = $this->Users_model->get_one($info_feeder_type_of_activity->modified_by);
				$view_data["modificado_por"] = ($modificado_por->id) ? $modificado_por->first_name . " " . $modificado_por->last_name : "-";
				
				$this->load->view('ac_feeders/type_of_activities/view', $view_data);
				
			} else {
				show_404();
			}
			
		} else {
			show_404();
		}
		
	}
	
	function delete_type_of_activities() {
		
		// $client_area = $this->session->client_area;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
		$opciones_registro = array(
			"id_cliente" => $id_cliente,
		);
		
		$registros = $this->AC_Feeders_type_of_activities_model->get_details($opciones_registro)->result();
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = format_to_date_clients($id_cliente, max($arrayFechas));
		
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
		// CONSULTAR SI ESTA SIENDO UTILIZADO EN CONVENIOS/INFORMACIÓN
		/* $informaciones = $this->AC_Information_model->get_all_where(array(
			"id_cliente" => $id_cliente,
			"client_area" => $client_area,
			"id_feeder_tipo_acuerdo" => $id,
			"deleted" => 0
		))->result_array();
		
		if(count($informaciones)){
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
			exit();
		} */
		
		// CONSULTAR SI ESTA SIENDO UTILIZADO EN ACTIVIDADES
		$actividades = $this->AC_Activities_model->get_all_where(array(
			"id_cliente" => $id_cliente,
			// "client_area" => $client_area,
			"id_feeder_tipo_actividad" => $id,
			"deleted" => 0
		))->result_array();
		
		if(count($actividades)){
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
			exit();
		}
		
        if ($this->input->post('undo')) {
            if ($this->AC_Feeders_type_of_activities_model->delete($id, true)) {
				
				$registros = $this->AC_Feeders_type_of_activities_model->get_details($opciones_registro)->result();
				$num_registros = count($registros);
				
                echo json_encode(array("success" => true, "data" => $this->_row_data_type_of_activities($id), 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->AC_Feeders_type_of_activities_model->delete($id)) {
				
				$registros = $this->AC_Feeders_type_of_activities_model->get_details($opciones_registro)->result();
				$num_registros = count($registros);
				
				// Guardar histórico notificaciones
				/* $options = array(
					"id_client" => $id_cliente,
					"id_user" => $this->session->user_id,
					"module_level" => "general",
					"id_client_context_module" => $this->id_client_context_module,
					"id_client_context_submodule" => $this->id_client_context_submodule,
					"event" => "type_of_agreement_delete",
					"id_element" => $id
				);
				ayn_save_historical_notification($options); */
				
                echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function delete_type_of_activities_multiple() {
		
		$data_ids = json_decode($this->input->post('data_ids'));
		$id_user = $this->session->user_id;
		// $client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;
		
		// VALIDACIÓN DE ELIMINACIÓN DE REGISTROS SEGÚN PERFIL
		$puede_eliminar = $this->general_profile_access($id_user, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		$eliminar = TRUE;
		foreach($data_ids as $id){
			if($puede_eliminar == 2){ // Propios
				$row = $this->AC_Feeders_type_of_activities_model->get_one($id);
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
		
		// foreach($data_ids as $id){
			
			// CONSULTAR SI ESTA SIENDO UTILIZADO EN CONVENIOS/INFORMACIÓN
			/* $informaciones = $this->AC_Information_model->get_all_where(array(
				"id_cliente" => $id_cliente,
				"client_area" => $client_area,
				"id_feeder_tipo_acuerdo" => $id,
				"deleted" => 0
			))->result_array();
			
			if(count($informaciones)){
				echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
				exit();
			} */
			
			// CONSULTAR SI ESTA SIENDO UTILIZADO EN ACTIVIDADES
			$actividades = $this->AC_Activities_model->get_all_where(array(
				"id_cliente" => $id_cliente,
				// "client_area" => $client_area,
				"id_feeder_tipo_actividad" => $id,
				"deleted" => 0
			))->result_array();
			
			if(count($actividades)){
				echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
				exit();
			}
			
		// }
		
		$deleted_values = false;
		foreach($data_ids as $id){
			if($this->AC_Feeders_type_of_activities_model->delete($id)) {
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
					"event" => "type_of_agreement_delete",
					"id_element" => $id,
					"massive" => (count($data_ids) > 1) ? 1 : 0,
					"is_email_sended" => ($index !== count($data_ids) -1) ? 1 : 0
				);
				ayn_save_historical_notification($options);
			} */
			
			$opciones_registro = array(
				"id_cliente" => $id_cliente,
				// "client_area" => $client_area
			);
			$registros = $this->AC_Feeders_type_of_activities_model->get_details($opciones_registro)->result();
			
			$arrayFechas = array();
			foreach($registros as $index => $reg){
				if(!$reg->modified){
					$arrayFechas[$index] = $reg->created;
				} else {
					$arrayFechas[$index] = $reg->modified;
				}
			}
			$fecha_modificacion = format_to_date_clients($id_cliente, max($arrayFechas));
			$num_registros = count($registros);
			
			echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('multiple_record_deleted')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
		}	
		
	}

	function beneficiary_objectives(){
		
		$id_usuario = $this->session->user_id;
		
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		$view_data["id_cliente"] = $id_cliente;

		$view_data["puede_ver"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
		$view_data["puede_agregar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "agregar");
		$view_data["puede_eliminar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");


		$registros = $this->AC_Feeders_beneficiary_objectives_model->get_all()->result();
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = max($arrayFechas);
		$view_data["fecha_modificacion"] = $fecha_modificacion;

		$this->template->rander('ac_feeders/beneficiary_objectives/index', $view_data);
	}
	
	function modal_form_beneficiary_objectives(){
		
		$id = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric"
        ));
		
        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		
		$view_data["view"] = $this->input->post('view');
		$model_info = $this->AC_Feeders_beneficiary_objectives_model->get_one($id);
        $view_data['model_info'] = $model_info;

		// Gráfico
		$charts_dropdown = array(
			"" => "-",
			"women_per_staffing" => lang("women_per_staffing"),
			"hired_over_45" => lang("hired_over_45"),
			"CK_KSA_law" => lang("CK_KSA_law"),
			"comercial_andes_motor_law" => lang("comercial_andes_motor_law"),
			"andes_motor_law" => lang("andes_motor_law"),
			"tea_law" => lang("tea_law")
		);
		$view_data["charts_dropdown"] = $charts_dropdown;

		if($id){
			// Objetivos
			$array_objetivos = json_decode($model_info->objetivos);
			$view_data["array_objetivos"] = $array_objetivos;
		}
		
		$this->load->view('ac_feeders/beneficiary_objectives/modal_form', $view_data);
		
	}

	function save_beneficiary_objectives(){

		validate_submitted_data(array(
            "grafico" => "trim|required"
        ));

		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;

		$id = $this->input->post('id');

		$grafico = $this->input->post('grafico');

		$years = $this->input->post('year');
		$objetivos = $this->input->post('objetivo');

		if(!$id){ // Si se esta guardando y no editando validar si ya existen objetivos para el gráfico
			$options = array(
				"grafico" => $grafico,
				"deleted" => 0
			);
			$id_objetivo_actividad = $this->AC_Feeders_beneficiary_objectives_model->get_one_where($options)->id;

			if($id_objetivo_actividad){
				echo json_encode(array("success" => false, 'message' => lang('duplicated_objectives_2')));
				exit;	
			}
		}

		if( count($years) > count(array_unique($years)) ){ // No pueden existir años duplicados
			echo json_encode(array("success" => false, 'message' => lang('duplicated_years')));
			exit;
		}

		$array_objetivos = array();
		for ($i=0; $i < count($years); $i++) { 
			$year = $years[$i];
			$objetivo = $objetivos[$i];

			if( $year == "" ||  $objetivo == "") continue;

			$array_objetivos[$year] = $objetivo;
		}
		$json_objetivos = json_encode($array_objetivos);

		$data = array(
			"id_cliente" => $id_cliente,
			"grafico" => $grafico,
			"objetivos" => $json_objetivos
		);

		if($id){
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
			$save_id = $this->AC_Feeders_beneficiary_objectives_model->save($data, $id);
		}else{
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
			$save_id = $this->AC_Feeders_beneficiary_objectives_model->save($data);
		}
		
		$opciones_registro = array(
			"id_cliente" => $id_cliente,
			"deleted" => 0
		);
		
		$registros = $this->AC_Feeders_beneficiary_objectives_model->get_all_where($opciones_registro)->result();
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = format_to_datetime_clients($id_cliente, max($arrayFechas));
		
		if ($save_id) {
			
			// Guardar histórico notificaciones
			/* $options = array(
				"id_client" => $id_cliente,
				"id_user" => $this->session->user_id,
				"module_level" => "general",
				"id_client_context_module" => $this->id_client_context_module,
				"id_client_context_submodule" => $this->id_client_context_submodule,
				"event" => $id_ac_feeder_society ? "society_edit" : "society_add",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options); */
			
            echo json_encode(array("success" => true, "data" => $this->_row_data_beneficiary_objectives($save_id), 'id' => $save_id, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros,'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
	}

	function list_data_beneficiary_objectives(){
			
		$id_usuario = $this->session->user_id;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
		$puede_ver = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
		
		$options = array(
			"id_cliente" => $id_cliente,
			"deleted" => 0
        );
		
        $list_data = $this->AC_Feeders_beneficiary_objectives_model->get_all_where($options)->result();
		
        $result = array();
        foreach ($list_data as $data) {
			if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row_beneficiary_objectives($data);
			}
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row_beneficiary_objectives($data);
				}
			}
			if($puede_ver == 3){ //Ninguno
				$result[1] = array();
			}
        }
        echo json_encode(array("data" => $result));
	}

	function _row_data_beneficiary_objectives($id){

        $data = $this->AC_Feeders_beneficiary_objectives_model->get_one($id);
        return $this->_make_row_beneficiary_objectives($data);
	}

	function _make_row_beneficiary_objectives($data){
		$id_usuario = $this->session->user_id;
		$puede_editar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "editar");
		$puede_eliminar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		
		$row_data[] = $data->id;
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
		}
		
		$row_data[] = lang($data->grafico);

		$objetivos = json_decode($data->objetivos);
		$html = '';
		foreach ($objetivos as $year => $objetivo) {
			$html .= '&bull; '.$year.':'.' '. to_number_client_format($objetivo, $data->id_cliente).'<br>';
		}
		$row_data[] = $html;

		$acciones = "";
		$view = modal_anchor(get_uri("AC_Feeders/view_beneficiary_objectives/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_beneficiary_objectives'), "data-post-id" => $data->id));

		$edit = modal_anchor(get_uri("AC_Feeders/modal_form_beneficiary_objectives"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_beneficiary_objectives'), "data-post-id" => $data->id));

		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_beneficiary_objectives'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("AC_Feeders/delete_beneficiary_objectives"), "data-action" => "delete-confirmation", "data-custom" => true));

		if($puede_editar == 1){
			$acciones .= $edit;
		}
		if($puede_editar == 2){
			if($data->created_by == $id_usuario){
				$acciones .= $edit;
			}
		}			
		if($puede_eliminar == 1){
			$acciones .= $delete;
		}
		if($puede_eliminar == 2){
			if($data->created_by == $id_usuario){
				$acciones .= $delete;
			}
		}	
		
		$acciones = $view.$acciones;

        $row_data[] =  $acciones;
				

        return $row_data;
	}

	function view_beneficiary_objectives($id_beneficiary_objectives = 0){
		if($id_beneficiary_objectives){
			
			// $options = array("id" => $id_beneficiary_objectives);
			$info_beneficiary_objectives = $this->AC_Feeders_beneficiary_objectives_model->get_one($id_beneficiary_objectives);
			
			if ($info_beneficiary_objectives) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$model_info = $info_beneficiary_objectives;
				$view_data['model_info'] = $model_info;
				// Grafico
				$view_data["grafico"] = lang($model_info->grafico);

				// Objetivos
				$objetivos = json_decode($model_info->objetivos);
				$html = '';
				foreach ($objetivos as $year => $objetivo) {
					$html .= '&bull; '.$year.': '. to_number_client_format($objetivo, $model_info->id_cliente).'<br>';
				}
				$view_data["objetivos"] = $html;
				
				$creado_por = $this->Users_model->get_one($info_beneficiary_objectives->created_by);
				$view_data["creado_por"] = ($creado_por->id) ? $creado_por->first_name . " " . $creado_por->last_name : "-";
				
				$modificado_por = $this->Users_model->get_one($info_beneficiary_objectives->modified_by);
				$view_data["modificado_por"] = ($modificado_por->id) ? $modificado_por->first_name . " " . $modificado_por->last_name : "-";
				
				$this->load->view('ac_feeders/beneficiary_objectives/view', $view_data);
				
			} else {
				show_404();
			}
			
		} else {
			show_404();
		}
		
	}

	function delete_beneficiary_objectives(){

		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
		$opciones_registro = array(
			"id_cliente" => $id_cliente,
			"deleted" => 0
		);
		
		$registros = $this->AC_Feeders_beneficiary_objectives_model->get_all_where($opciones_registro)->result();
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = format_to_date_clients($id_cliente, max($arrayFechas));
		
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
        if ($this->input->post('undo')) {
            if ($this->AC_Feeders_beneficiary_objectives_model->delete($id, true)) {
				
				$registros = $this->AC_Feeders_beneficiary_objectives_model->get_all_where($opciones_registro)->result();
				$num_registros = count($registros);
				
                echo json_encode(array("success" => true, "data" => $this->_row_data_activity_objectives($id), 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->AC_Feeders_beneficiary_objectives_model->delete($id)) {
				
				$registros = $this->AC_Feeders_beneficiary_objectives_model->get_all_where($opciones_registro)->result();
				$num_registros = count($registros);
				
				// Guardar histórico notificaciones
				/* $options = array(
					"id_client" => $id_cliente,
					"id_user" => $this->session->user_id,
					"module_level" => "general",
					"id_client_context_module" => $this->id_client_context_module,
					"id_client_context_submodule" => $this->id_client_context_submodule,
					"event" => "central_delete",
					"id_element" => $id
				);
				ayn_save_historical_notification($options); */
				
                echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
	}

	function delete_beneficiary_objectives_multiple(){
	
		$data_ids = json_decode($this->input->post('data_ids'));
		$id_user = $this->session->user_id;
		// $client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;
		
		// VALIDACIÓN DE ELIMINACIÓN DE REGISTROS SEGÚN PERFIL
		$puede_eliminar = $this->general_profile_access($id_user, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		$eliminar = TRUE;
		foreach($data_ids as $id){
			if($puede_eliminar == 2){ // Propios
				$row = $this->AC_Feeders_beneficiary_objectives_model->get_one($id);
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
		
		
		$deleted_values = false;
		foreach($data_ids as $id){
			if($this->AC_Feeders_beneficiary_objectives_model->delete($id)) {
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
					"event" => "type_of_agreement_delete",
					"id_element" => $id,
					"massive" => (count($data_ids) > 1) ? 1 : 0,
					"is_email_sended" => ($index !== count($data_ids) -1) ? 1 : 0
				);
				ayn_save_historical_notification($options);
			} */
			
			$opciones_registro = array(
				"id_cliente" => $id_cliente,
				"deleted" => 0
				// "client_area" => $client_area
			);
			$registros = $this->AC_Feeders_beneficiary_objectives_model->get_all_where($opciones_registro)->result();
			
			$arrayFechas = array();
			foreach($registros as $index => $reg){
				if(!$reg->modified){
					$arrayFechas[$index] = $reg->created;
				} else {
					$arrayFechas[$index] = $reg->modified;
				}
			}
			$fecha_modificacion = format_to_date_clients($id_cliente, max($arrayFechas));
			$num_registros = count($registros);
			
			echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('multiple_record_deleted')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
		}	
	}

	function activity_objectives(){
		
		$id_usuario = $this->session->user_id;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		$view_data["id_cliente"] = $id_cliente;
		
		$view_data["puede_ver"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
		$view_data["puede_agregar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "agregar");
		$view_data["puede_eliminar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		$registros = $this->AC_Feeders_activity_objectives_model->get_all()->result();
		$num_registros = count($registros);
		$view_data["num_registros"] = $num_registros;
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = max($arrayFechas);
		$view_data["fecha_modificacion"] = $fecha_modificacion;

		$this->template->rander('ac_feeders/activity_objectives/index', $view_data);
	}
	
	function modal_form_activity_objectives(){
		
		$id = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric"
        ));
		
        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		
		$view_data["view"] = $this->input->post('view');
		$model_info = $this->AC_Feeders_activity_objectives_model->get_one($id);
        $view_data['model_info'] = $model_info;

		// Tipo de actividades
		$type_of_activities_dropdown = array("" => "-");
		$tipos_actividad = $this->AC_Types_of_activities_model->get_all()->result();
		foreach($tipos_actividad as $tipo_actividad){
			$type_of_activities_dropdown[$tipo_actividad->id] = lang($tipo_actividad->name);
		}
		$view_data["type_of_activities_dropdown"] = $type_of_activities_dropdown;

		// Actividad
		$view_data["activities_dropdown"] = array("" => "-");

		// Gráfico
		$charts_dropdown = array(
			"" => "-",
			"activities_executed" => lang("activities_executed"),
			"benefited_collaborators" => lang("benefited_collaborators"),
			"executed_amount" => lang("executed_amount")
		);
		$view_data["charts_dropdown"] = $charts_dropdown;

		if($id){
			// Actividad
			$options = array(
				'id_tipo_actividad' => $model_info->id_tipo_actividad,
				'deleted' => 0
			);
			$ac_feeder_actividades = $this->AC_Feeders_type_of_activities_model->get_all_where($options)->result();

			$activities_dropdown = array("" => "-");
			foreach ($ac_feeder_actividades as $feeder_actividad) {
				$activities_dropdown[$feeder_actividad->id] = $feeder_actividad->actividad;
			}

			$view_data["activities_dropdown"] = $activities_dropdown;

			// Objetivos
			$array_objetivos = json_decode($model_info->objetivos);
			$view_data["array_objetivos"] = $array_objetivos;
		}
		
		$this->load->view('ac_feeders/activity_objectives/modal_form', $view_data);
		
	}

	function save_activity_objectives(){

		validate_submitted_data(array(
            "id_tipo_actividad" => "required|numeric",
            "id_actividad" => "required|numeric",
            "grafico" => "trim|required"
        ));

		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;

		$id = $this->input->post('id');

		$id_tipo_actividad = $this->input->post('id_tipo_actividad');
		$id_actividad = $this->input->post('id_actividad');
		$grafico = $this->input->post('grafico');

		$years = $this->input->post('year');
		$objetivos = $this->input->post('objetivo');

		if(!$id){ // Si se esta guardando y no editando validar si ya existen objetivos por combinación de id_tipo_actividad - id_actividad - gráfico
			$options = array(
				"id_tipo_actividad" => $id_tipo_actividad,
				"id_actividad" => $id_actividad,
				"grafico" => $grafico,
				"deleted" => 0
			);
			$id_objetivo_actividad = $this->AC_Feeders_activity_objectives_model->get_one_where($options)->id;

			if($id_objetivo_actividad){
				echo json_encode(array("success" => false, 'message' => lang('duplicated_objectives')));
				exit;	
			}
		}

		if( count($years) > count(array_unique($years)) ){ // No pueden existir años duplicados
			echo json_encode(array("success" => false, 'message' => lang('duplicated_years')));
			exit;
		}

		$array_objetivos = array();
		for ($i=0; $i < count($years); $i++) { 
			$year = $years[$i];
			$objetivo = $objetivos[$i];

			if( $year == "" ||  $objetivo == "") continue;

			$array_objetivos[$year] = $objetivo;
		}
		$json_objetivos = json_encode($array_objetivos);

		$data = array(
			"id_cliente" => $id_cliente,
			"id_tipo_actividad" => $id_tipo_actividad,
			"id_actividad" => $id_actividad,
			"grafico" => $grafico,
			"objetivos" => $json_objetivos
		);

		if($id){
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
			$save_id = $this->AC_Feeders_activity_objectives_model->save($data, $id);
		}else{
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
			$save_id = $this->AC_Feeders_activity_objectives_model->save($data);
		}
		
		$opciones_registro = array(
			"id_cliente" => $id_cliente,
			"deleted" => 0
		);
		
		$registros = $this->AC_Feeders_activity_objectives_model->get_all_where($opciones_registro)->result();
		$num_registros = count($registros);
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = format_to_datetime_clients($id_cliente, max($arrayFechas));
		
		if ($save_id) {
			
			// Guardar histórico notificaciones
			/* $options = array(
				"id_client" => $id_cliente,
				"id_user" => $this->session->user_id,
				"module_level" => "general",
				"id_client_context_module" => $this->id_client_context_module,
				"id_client_context_submodule" => $this->id_client_context_submodule,
				"event" => $id_ac_feeder_society ? "society_edit" : "society_add",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options); */
			
            echo json_encode(array("success" => true, "data" => $this->_row_data_activity_objectives($save_id), 'id' => $save_id, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros,'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
	}

	function list_data_activity_objectives(){
		
		$id_usuario = $this->session->user_id;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
		$puede_ver = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
		
		$options = array(
			"id_cliente" => $id_cliente,
			"deleted" => 0
        );
		
        $list_data = $this->AC_Feeders_activity_objectives_model->get_all_where($options)->result();
		
        $result = array();
        foreach ($list_data as $data) {
			if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row_activity_objectives($data);
			}
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row_activity_objectives($data);
				}
			}
			if($puede_ver == 3){ //Ninguno
				$result[1] = array();
			}
        }
        echo json_encode(array("data" => $result));
	}

	function _row_data_activity_objectives($id){

        $data = $this->AC_Feeders_activity_objectives_model->get_one($id);
        return $this->_make_row_activity_objectives($data);
	}

	function _make_row_activity_objectives($data){
		$id_usuario = $this->session->user_id;
		$puede_editar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "editar");
		$puede_eliminar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		$row_data[] = $data->id;
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
		}
		
		$tipo_actividad = $this->AC_Types_of_activities_model->get_one($data->id_tipo_actividad);

		$row_data[] = lang($tipo_actividad->name);

		$feeder_actividad = $this->AC_Feeders_type_of_activities_model->get_one($data->id_actividad)->actividad;
		$row_data[] = $feeder_actividad;

		$row_data[] = lang($data->grafico);

		$objetivos = json_decode($data->objetivos);
		$html = '';
		foreach ($objetivos as $year => $objetivo) {
			$html .= '&bull; '.$year.':'.' '. to_number_client_format($objetivo, $data->id_cliente).'<br>';
		}
		$row_data[] = $html;

		$acciones = "";
		$view = modal_anchor(get_uri("AC_Feeders/view_activity_objectives/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_activity_objectives'), "data-post-id" => $data->id));

		$edit = modal_anchor(get_uri("AC_Feeders/modal_form_activity_objectives"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_activity_objectives'), "data-post-id" => $data->id));

		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_activity_objectives'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("AC_Feeders/delete_activity_objectives"), "data-action" => "delete-confirmation", "data-custom" => true));

		if($puede_editar == 1){
			$acciones .= $edit;
		}
		if($puede_editar == 2){
			if($data->created_by == $id_usuario){
				$acciones .= $edit;
			}
		}
		if($puede_eliminar == 1){
			$acciones .= $delete;
		}
		if($puede_eliminar == 2){
			if($data->created_by == $id_usuario){
				$acciones .= $delete;
			}
		}				
		
		$acciones = $view.$acciones;

        $row_data[] =  $acciones;
				

        return $row_data;
	}

	function view_activity_objectives($id_activity_objectives = 0){
		if($id_activity_objectives){
			
			// $options = array("id" => $id_activity_objectives);
			$info_activity_objectives = $this->AC_Feeders_activity_objectives_model->get_one($id_activity_objectives);
			
			if ($info_activity_objectives) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$model_info = $info_activity_objectives;
				$view_data['model_info'] = $model_info;

				// Tipo de actividad
				$tipo_actividad = $this->AC_Types_of_activities_model->get_one($model_info->id_tipo_actividad)->name;
				$view_data["tipo_actividad"] = lang($tipo_actividad);

				// Actividad
				$view_data["actividad"] = $this->AC_Feeders_type_of_activities_model->get_one($model_info->id_actividad)->actividad;

				// Grafico
				$view_data["grafico"] = lang($model_info->grafico);

				// Objetivos
				$objetivos = json_decode($model_info->objetivos);
				$html = '';
				foreach ($objetivos as $year => $objetivo) {
					$html .= '&bull; '.$year.': '. to_number_client_format($objetivo, $model_info->id_cliente).'<br>';
				}
				$view_data["objetivos"] = $html;
				
				$creado_por = $this->Users_model->get_one($info_activity_objectives->created_by);
				$view_data["creado_por"] = ($creado_por->id) ? $creado_por->first_name . " " . $creado_por->last_name : "-";
				
				$modificado_por = $this->Users_model->get_one($info_activity_objectives->modified_by);
				$view_data["modificado_por"] = ($modificado_por->id) ? $modificado_por->first_name . " " . $modificado_por->last_name : "-";
				
				$this->load->view('ac_feeders/activity_objectives/view', $view_data);
				
			} else {
				show_404();
			}
			
		} else {
			show_404();
		}
		
	}

	function delete_activity_objectives(){

		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
		$opciones_registro = array(
			"id_cliente" => $id_cliente,
			"deleted" => 0
		);
		
		$registros = $this->AC_Feeders_activity_objectives_model->get_all_where($opciones_registro)->result();
		
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = format_to_date_clients($id_cliente, max($arrayFechas));
		
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
        if ($this->input->post('undo')) {
            if ($this->AC_Feeders_activity_objectives_model->delete($id, true)) {
				
				$registros = $this->AC_Feeders_activity_objectives_model->get_all_where($opciones_registro)->result();
				$num_registros = count($registros);
				
                echo json_encode(array("success" => true, "data" => $this->_row_data_activity_objectives($id), 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->AC_Feeders_activity_objectives_model->delete($id)) {
				
				$registros = $this->AC_Feeders_activity_objectives_model->get_all_where($opciones_registro)->result();
				$num_registros = count($registros);
				
				// Guardar histórico notificaciones
				/* $options = array(
					"id_client" => $id_cliente,
					"id_user" => $this->session->user_id,
					"module_level" => "general",
					"id_client_context_module" => $this->id_client_context_module,
					"id_client_context_submodule" => $this->id_client_context_submodule,
					"event" => "central_delete",
					"id_element" => $id
				);
				ayn_save_historical_notification($options); */
				
                echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
	}

	function delete_activity_objectives_multiple(){
	
		$data_ids = json_decode($this->input->post('data_ids'));
		$id_user = $this->session->user_id;
		// $client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;
		
		// VALIDACIÓN DE ELIMINACIÓN DE REGISTROS SEGÚN PERFIL
		$puede_eliminar = $this->general_profile_access($id_user, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		$eliminar = TRUE;
		foreach($data_ids as $id){
			if($puede_eliminar == 2){ // Propios
				$row = $this->AC_Feeders_activity_objectives_model->get_one($id);
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
		
		
		$deleted_values = false;
		foreach($data_ids as $id){
			if($this->AC_Feeders_activity_objectives_model->delete($id)) {
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
					"event" => "type_of_agreement_delete",
					"id_element" => $id,
					"massive" => (count($data_ids) > 1) ? 1 : 0,
					"is_email_sended" => ($index !== count($data_ids) -1) ? 1 : 0
				);
				ayn_save_historical_notification($options);
			} */
			
			$opciones_registro = array(
				"id_cliente" => $id_cliente,
				"deleted" => 0
				// "client_area" => $client_area
			);
			$registros = $this->AC_Feeders_activity_objectives_model->get_all_where($opciones_registro)->result();
			
			$arrayFechas = array();
			foreach($registros as $index => $reg){
				if(!$reg->modified){
					$arrayFechas[$index] = $reg->created;
				} else {
					$arrayFechas[$index] = $reg->modified;
				}
			}
			$fecha_modificacion = format_to_date_clients($id_cliente, max($arrayFechas));
			$num_registros = count($registros);
			
			echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('multiple_record_deleted')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
		}	
	}
	
	function get_activities_for_objectives(){
		$id_tipo_actividad = $this->input->post('id_tipo_actividad');

		$options = array(
			'id_tipo_actividad' => $id_tipo_actividad,
			'deleted' => 0
		);
		$ac_feeder_actividades = $this->AC_Feeders_type_of_activities_model->get_all_where($options)->result();

		$activities_dropdown = array("" => "-");
		foreach ($ac_feeder_actividades as $feeder_actividad) {
			$activities_dropdown[$feeder_actividad->id] = $feeder_actividad->actividad;
		}

		$html = '<div class="form-group">';
        $html .= 	'<label for="actividad" class="col-md-3">'.lang('activity').'</label>';
        $html .= 	'<div class="col-md-9">';
        $html .= 		form_dropdown("id_actividad", $activities_dropdown, '', "id='id_actividad' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            
        $html .= 	'</div>';
		$html .= '</div>';

		echo $html;

	}

	function get_excel($feeder_type = ""){
		
		$id_usuario = $this->session->user_id;
		// $client_area = $this->session->client_area;
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		$client_info = $this->Clients_model->get_one($id_cliente);
		$puede_ver = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
		
		$nombre_columnas = array();
		$options = array(
			"id_cliente" => $id_cliente,
            // "client_area" => $client_area,
        );

		if($feeder_type == "societies"){
			$list_data = $this->AC_Feeders_societies_model->get_details($options)->result();
			$nombre_columnas[] = array("nombre_columna" => lang("society_name"), "id_tipo_campo" => "society_name");
			$nombre_columnas[] = array("nombre_columna" => lang("observations"), "id_tipo_campo" => "observations");
		}
		
		/* if($feeder_type == "centrals"){
			$list_data = $this->AC_Feeders_centrals_model->get_details($options)->result();
			$nombre_columnas[] = array("nombre_columna" => lang("central_name"), "id_tipo_campo" => "central_name");
			$nombre_columnas[] = array("nombre_columna" => lang("macrozone"), "id_tipo_campo" => "macrozone");
			$nombre_columnas[] = array("nombre_columna" => lang("observations"), "id_tipo_campo" => "observations");
		} */
		
		if($feeder_type == "type_of_activities"){
			$list_data = $this->AC_Feeders_type_of_activities_model->get_details($options)->result();
			$nombre_columnas[] = array("nombre_columna" => lang("ac_type_of_activity"), "id_tipo_campo" => "ac_type_of_activity");
			$nombre_columnas[] = array("nombre_columna" => lang("activity"), "id_tipo_campo" => "activity");
			$nombre_columnas[] = array("nombre_columna" => lang("activity_description"), "id_tipo_campo" => "activity_description");
		}

		if($feeder_type == "ac_activity_objectives"){
			$list_data = $this->AC_Feeders_activity_objectives_model->get_all_where(array("id_cliente" => $id_cliente, "deleted" => 0))->result();
			$nombre_columnas[] = array("nombre_columna" => lang("ac_type_of_activity"), "id_tipo_campo" => "ac_type_of_activity");
			$nombre_columnas[] = array("nombre_columna" => lang("activity"), "id_tipo_campo" => "activity");
			$nombre_columnas[] = array("nombre_columna" => lang("ac_chart"), "id_tipo_campo" => "ac_chart");
			$nombre_columnas[] = array("nombre_columna" => lang("objectives"), "id_tipo_campo" => "objectives");
		}
		
		$result = array();
		foreach ($list_data as $data) {
			if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row_excel($data, $feeder_type);
			}
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row_excel($data, $feeder_type);
				}
			}
			if($puede_ver == 3){ //Ninguno
				$result[1] = array();
			}
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

		// HEADER
		$hoy = date('d-m-Y');
		$fecha = date(get_setting_client_mimasoft($client_info->id, "date_format"), strtotime($hoy));
		$hora = format_to_time_clients($client_info->id, get_current_utc_time("H:i:s"));
		
		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', lang("agreements")." ".lang($client_area))
			->setCellValue('C2', lang($feeder_type))
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
					
					if($columna["id_tipo_campo"] == "society_name" || $columna["id_tipo_campo"] == "central_name"
					|| $columna["id_tipo_campo"] == "macrozone" || $columna["id_tipo_campo"] == "type_of_agreement"
					|| $columna["id_tipo_campo"] == "type_of_administration"
					|| $columna["id_tipo_campo"] == "ac_type_of_activity" || $columna["id_tipo_campo"] == "activity" || $columna["id_tipo_campo"] == "ac_chart"){
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
					
					} elseif($columna["id_tipo_campo"] == "observations" || $columna["id_tipo_campo"] == "activity_description"){
						
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
		for ($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;	
		}
		foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}
		
		$nombre_hoja = strlen(lang("feeders").'_'.lang($feeder_type)) > 31 ? substr(lang("feeders").'_'.lang($feeder_type), 0, 28).'...' : lang("feeders").'_'.lang($feeder_type);
		
		$nombre_hoja = $nombre_hoja ? $nombre_hoja : " ";
		
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		$filename = $client_info->sigla."_".lang("feeders").'_'.lang($feeder_type).'_'.date('Y-m-d');
		$filename = $filename.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');
		exit;
		
		
		
	}
	
	function _make_row_excel($data, $feeder_type){
		
		if($feeder_type == "societies"){
			$row_data[] = $data->nombre_sociedad;
			$row_data[] = ($data->observaciones) ? $data->observaciones : "-";
		}
		
		/* if($feeder_type == "centrals"){
			$row_data[] = $data->nombre_central;
			$macrozona = $this->AC_Macrozones_model->get_one($data->id_macrozona)->nombre;
			$row_data[] = $macrozona;
			$row_data[] = ($data->observaciones) ? $data->observaciones : "-";
		} */
		
		if($feeder_type == "type_of_activities"){
			$row_data[] = $data->tipo_actividad;
			$row_data[] = $data->actividad;
			$row_data[] = ($data->descripcion_actividad) ? $data->descripcion_actividad : "-";
		}

		if($feeder_type == "ac_activity_objectives"){
			$tipo_actividad = $this->AC_Types_of_activities_model->get_one($data->id_tipo_actividad);

			$row_data[] = lang($tipo_actividad->name);
	
			$feeder_actividad = $this->AC_Feeders_type_of_activities_model->get_one($data->id_actividad)->actividad;
			$row_data[] = $feeder_actividad;
	
			$row_data[] = lang($data->grafico);
	
			$objetivos = json_decode($data->objetivos, true);
			$valores = '';
			$cont = 0; 
			$cant = count($objetivos);
			foreach ($objetivos as $year => $objetivo) {
				$valores .= $year.':'.' '. to_number_client_format($objetivo, $data->id_cliente);
				$cont++;
				$valores .= ($cont != $cant) ? ' ; ' : '';
			}
			$row_data[] = $valores;

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
	
}