<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class AC_Activities extends MY_Controller {
	
	private $id_client_context_module;
	
	private $id_client_context_submodule;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;
		
		
			
		$this->id_client_context_module = 7;
		$this->id_client_context_submodule = 18;
		
		
		
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
		
		$client_area = $this->session->client_area;
		$id_usuario = $this->session->user_id;
		$id_cliente = $this->login_user->client_id;
		
		if($client_area == "territory"){
			$view_data["puede_ver"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
			$view_data["puede_agregar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "agregar");
			$view_data["puede_eliminar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");
		}
		
		// Tipo de actividad
		$array_tipo_actividades[] = array("id" => "", "text" => "- ".lang("activity_type")." -");
		$actividades_dropdown = $this->AC_Feeders_type_of_activities_model->get_dropdown_list(
			array("actividad"), 
			"id", 
			array(
				"id_cliente" => $id_cliente, 
				"deleted" => 0
			)
		);
		foreach($actividades_dropdown as $id => $nombre){
			$array_tipo_actividades[] = array("id" => $id, "text" => $nombre);
		}
		$view_data["activity_type_dropdown"] = json_encode($array_tipo_actividades);

		// Sociedad
		$array_sociedad[] = array("id" => "", "text" => "- ".lang("society")." -");
		$sociedad_dropdown = $this->AC_Feeders_societies_model->get_dropdown_list(
			array("nombre_sociedad"), 
			"id", 
			array(
				"id_cliente" => $id_cliente, 
				"deleted" => 0
			)
		);
		foreach($sociedad_dropdown as $id => $nombre){
			$array_sociedad[] = array("id" => $id, "text" => $nombre);
		}
		$view_data["sociedad_dropdown"] = json_encode($array_sociedad);

		$view_data["client_agreements_info"] = $this->AC_Client_agreements_info_model->get_details(array(
			"client_area" => $client_area
		))->row();
		
		$view_data["client_area"] = $client_area;
		
        $this->template->rander("ac_activities/index", $view_data);
    }
	
	//modificar
	function modal_form() {

		$client_area = $this->session->client_area;
		$id_usuario = $this->session->user_id;
		$id_cliente = $this->login_user->client_id;
        $id_actividad = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));
		
		$view_data['client_area'] = $client_area;
        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');

		$model_info = $this->AC_Activities_model->get_one($id_actividad);
        $view_data['model_info'] = $model_info;
		
		// Tipo de actividad
		$actividades_dropdown = array("" => "-") + $this->AC_Feeders_type_of_activities_model->get_dropdown_list(
			array("actividad"), 
			"id", 
			array(
				"id_cliente" => $id_cliente,
				"deleted" => 0
			)
		);
		$view_data["activity_type_dropdown"] = $actividades_dropdown;


		// Sociedad
		$society_dropdown = array("" => "-") + $this->AC_Feeders_societies_model->get_dropdown_list(
			array("nombre_sociedad"),
			"id",
			array(
				"id_cliente" => $id_cliente,
				"deleted" => 0
			)
		);
		$view_data["society_dropdown"] = $society_dropdown;
	
		
		$view_data["puede_agregar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "agregar");
		
		// Si estamos tratando de agregar no editar
		if(!$id_actividad && $view_data["puede_agregar"] == 3){
			redirect("forbidden");
		}

		if($id_actividad){

					
			if($client_area == "territory"){
				
				$view_data["puede_editar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "editar");
				
				if($view_data["puede_editar"] == 3){
					redirect("forbidden");
				}
				

				$asistentes = $this->AC_Beneficiaries_model->get_all_where(array(
					'sociedad_desc' => $model_info->id_feeder_sociedad,
					'id_cliente' => $id_cliente,
					'deleted' => 0
				))->result();

				$opciones_asistentes = array();
				foreach ($asistentes as $asistente) {
					$opciones_asistentes[$asistente->id] = $asistente->id_nacionalidad;
				}

				$opciones_asistentes_seleccionados = json_decode($model_info->asistentes);

				$view_data["opciones_asistentes"] = $opciones_asistentes;
				$view_data["opciones_asistentes_seleccionados"] = $opciones_asistentes_seleccionados;
	
			} else {
				redirect("forbidden");
			}
			
		}
		
        $this->load->view('ac_activities/modal_form', $view_data);
    }
	
	
	function save() {
				
		// $client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;
        $id_actividad = $this->input->post('id');
		
		// Archivos
		$file_name_registro = $this->input->post('registro');
		$file_name_registro_prefix = "registro"."-".$file_name_registro;
		
		$file_name_otros_archivos = $this->input->post('otros_archivos');
		$file_name_otros_archivos_prefix = "otros_archivos"."-".$file_name_otros_archivos;
		
		$id_campo_archivo_eliminar = $this->input->post('id_campo_archivo_eliminar'); // id(s) de archivo(s) a eliminar
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));
		
		$data_actividad = array(
			"id_cliente" => $id_cliente,
			"fecha" => $this->input->post('fecha'),
			"id_feeder_tipo_actividad" => $this->input->post('tipo_actividad'),
			"id_feeder_sociedad" => $this->input->post('sociedad'),
			"asistentes" => json_encode($this->input->post('asistentes')),
			"cant_hijos_beneficiados" => $this->input->post('cant_hijos_beneficiados'),
			"inversion" => ($this->input->post('inversion')) ? $this->input->post('inversion') : NULL,
			"observaciones" => ($this->input->post('observaciones')) ? $this->input->post('observaciones') : NULL,
		);
		
		if($id_actividad){
			$data_actividad["modified_by"] = $this->login_user->id;
			$data_actividad["modified"] = get_current_utc_time();
			$save_id = $this->AC_Activities_model->save($data_actividad, $id_actividad);
		} else {
			$data_actividad["created_by"] = $this->login_user->id;
			$data_actividad["created"] = get_current_utc_time();
			$save_id = $this->AC_Activities_model->save($data_actividad);
		}
		
		// Eliminar archivos borrados por el usuario antes de enviar el formulario.
		if($id_actividad){
			if($id_campo_archivo_eliminar){
				$info_actividad = $this->AC_Activities_model->get_one($id_actividad);
				foreach($id_campo_archivo_eliminar as $id_archivo){
					$filename = $info_actividad->$id_archivo;
					$file_path = "files/actividades/client_".$id_cliente."/actividad_".$save_id."/".$filename;
					$save_id = $this->AC_Activities_model->update_where(array("$id_archivo" => NULL), array("id" => $id_actividad));

					// LOG - ACTION deleted_file
					// $client_area = $this->session->client_area;
					// if($client_area == "territory"){
					// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_territory, NULL, "deleted_file", $id_actividad, $id_archivo);
					// }
					// if($client_area == "distribution"){
					// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_distribution, NULL, "deleted_file", $id_actividad, $id_archivo);

					// }

					delete_file_from_directory($file_path);
				}
			}
		}
		
		// Archivos
		$crear_carpeta = $this->create_activities_folder($save_id);
		
		if($file_name_registro){
			$nombre_real_archivo = remove_file_prefix($file_name_registro_prefix);		
			$archivo_subido = move_temp_file("registro"."_".$nombre_real_archivo, "files/actividades/client_".$id_cliente."/actividad_".$save_id."/", "", "", $file_name_registro_prefix);
			$data_actividad = array("registro" => $archivo_subido);
			$save_id = $this->AC_Activities_model->save($data_actividad, $save_id);
		}
		
		if($file_name_otros_archivos){
			$nombre_real_archivo = remove_file_prefix($file_name_otros_archivos_prefix);		
			$archivo_subido = move_temp_file("otros_archivos"."_".$nombre_real_archivo, "files/actividades/client_".$id_cliente."/actividad_".$save_id."/", "", "", $file_name_otros_archivos_prefix);
			$data_actividad = array("otros_archivos" => $archivo_subido);
			$save_id = $this->AC_Activities_model->save($data_actividad, $save_id);
		}

		
        if ($save_id) {
			
			// Guardar histórico notificaciones
			$options = array(
				"id_client" => $id_cliente,
				"id_user" => $this->session->user_id,
				"module_level" => "general",
				"id_client_context_module" => $this->id_client_context_module,
				"id_client_context_submodule" => $this->id_client_context_submodule,
				"event" => ($id_actividad) ? "edit" : "add",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options);

			// LOGS
			// if(!$id_actividad){
			// 	// AGREGAR LOG - ACTION added
			// 	if($client_area == "territory"){
			// 		$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_territory, NULL, "added", $save_id, 'element');
			// 	}
			// 	if($client_area == "distribution"){
			// 		$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_distribution, NULL, "added", $save_id, 'element');

			// 	}
			// }elseif($id_actividad ){
			// 	// AGREGAR LOG - ACTION edited
			// 	if($client_area == "territory"){
			// 		$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_territory, NULL, "edited", $save_id, 'element');
			// 	}
			// 	if($client_area == "distribution"){
			// 		$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_distribution, NULL, "edited", $save_id, 'element');

			// 	}
			// }
			
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	
	//modificar
	function delete() {
		
		$client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;
		
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
        if ($this->input->post('undo')) {
            if ($this->AC_Activities_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->AC_Activities_model->delete($id)) {
				
				// Guardar histórico notificaciones
				$options = array(
					"id_client" => $id_cliente,
					"id_user" => $this->session->user_id,
					"module_level" => "general",
					"id_client_context_module" => $this->id_client_context_module,
					"id_client_context_submodule" => $this->id_client_context_submodule,
					"event" => "delete",
					"id_element" => $id
				);
				ayn_save_historical_notification($options);

				// LOG - ACTION deleted
				// if($client_area == "territory"){
				// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_territory, NULL, "deleted", $id, 'element');
				// }
				// if($client_area == "distribution"){
				// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_distribution, NULL, "deleted", $id, 'element');
				// }
				
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function delete_multiple(){
		
		$data_ids = json_decode($this->input->post('data_ids'));
		$id_user = $this->session->user_id;
		$client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;
		
		// VALIDACIÓN DE ELIMINACIÓN DE REGISTROS SEGÚN PERFIL
		$puede_eliminar = $this->general_profile_access($id_user, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");

		$eliminar = TRUE;
		foreach($data_ids as $id){
			if($puede_eliminar == 2){ // Propios
				$row = $this->AC_Activities_model->get_one($id);
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
			if($this->AC_Activities_model->delete($id)) {
				$deleted_values = true;
			} else {
				$deleted_values = false;
				break;
			}
		}
					
		if($deleted_values){
			
			// Guardar histórico notificaciones
			$id_client_context_submodule = $this->id_client_context_submodule;
			
			
			// Guardar histórico notificaciones
			foreach($data_ids as $index => $id){
				$options = array(
					"id_client" => $id_cliente,
					"id_user" => $id_user,
					"module_level" => "general",
					"id_client_context_module" => $this->id_client_context_module,
					"id_client_context_submodule" => $id_client_context_submodule,
					"event" => "delete",
					"id_element" => $id,
					"massive" => (count($data_ids) > 1) ? 1 : 0,
					"is_email_sended" => ($index !== count($data_ids) -1) ? 1 : 0
				);
				ayn_save_historical_notification($options);
			}

			// LOG ACTION bulk_deleted
			// if($client_area == 'territory'){
			// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_territory, NULL, 'bulk_deleted');
			// }elseif($client_area == 'distribution'){
			// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_distribution, NULL, 'bulk_deleted');
			// }
			
			echo json_encode(array("success" => true, 'message' => lang('multiple_record_deleted')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
		}	

	}
	
	function list_data() {
		
		$client_area = $this->session->client_area;
		$id_usuario = $this->session->user_id;
		$id_cliente = $this->login_user->client_id;
		
		$options = array(
			"tipo_actividad" => $this->input->post("tipo_actividad"),
			"sociedad" => $this->input->post("sociedad"),
			"id_cliente" => $id_cliente
		);
		
		if($client_area == "territory"){
			
			$puede_ver = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
			
			$list_data = $this->AC_Activities_model->get_details($options)->result();
			$result = array();
			foreach ($list_data as $data) {

				// Todos: 1
				// Propios: 2 
				// Ninguno: 3
				
				if($puede_ver == 1 ){
					$result[] = $this->_make_row($data);
				}
				
				if($puede_ver == 2 && $id_usuario == $data->created_by){
					$result[] = $this->_make_row($data);
				}

				if($puede_ver == 3 ){
					$result[1] = array();
				}
				
			}

		}

        echo json_encode(array("data" => $result));
    }
	
	private function _row_data($id) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->AC_Activities_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$id_usuario = $this->session->user_id;		
		//$id_cliente = $this->login_user->client_id;
		$client_area = $this->session->client_area;

		$row_data[] = $data->id;
		$row_data[] = $data->created_by;
		
		if($client_area == "territory"){
			
			$puede_eliminar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");
			
			$row_data[] = $puede_eliminar;
		}
		
		
		$row_data[] = date(get_setting_client_mimasoft($data->id_cliente, "date_format"), strtotime($data->fecha));
		$tipo_actividad = $this->AC_Feeders_type_of_activities_model->get_one($data->id_feeder_tipo_actividad);
		$row_data[] = $tipo_actividad->actividad;
		
		$sociedad = $this->AC_Feeders_societies_model->get_one($data->id_feeder_sociedad);
		$row_data[] = $sociedad->nombre_sociedad;
		$row_data[] = $data->inversion ? to_number_client_format($data->inversion, $data->id_cliente) : '-';
		
		if($client_area == "territory"){
			
			$puede_editar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "editar");
			$puede_eliminar = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "eliminar");
			
			$view = modal_anchor(get_uri("AC_Activities/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_activity'), "data-post-id" => $data->id));
			$edit = modal_anchor(get_uri("AC_Activities/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_activity'), "data-post-id" => $data->id));
			$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_activity'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("AC_Activities/delete"), "data-action" => "delete-confirmation"));

			$accion = "";

			
			if($puede_editar == 1){
				$accion .= $edit;
			}
			if($puede_editar == 2){
				if($data->created_by == $id_usuario){
					$accion .= $edit;
				}
			}
			if($puede_eliminar == 1){
				$accion .= $delete;
			}
			if($puede_eliminar == 2){
				if($data->created_by == $id_usuario){
					$accion .= $delete;
				}
			}
		

			$row_data[] = $view.$accion;
				
		}
		
        return $row_data;
    }
	
	function view($id_actividad = 0) {
		
		$client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;
		
		$view_data["client_area"] = $client_area;
		
        if ($id_actividad) {
            $options = array("id" => $id_actividad);
            $model_info = $this->AC_Activities_model->get_details($options)->row();
            if ($model_info) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $model_info;
				
				// Actividad
				$view_data["tipo_actividad"] = $this->AC_Feeders_type_of_activities_model->get_one($model_info->id_feeder_tipo_actividad)->actividad;

				// Sociedad
				$view_data["sociedad"] = $this->AC_Feeders_societies_model->get_one($model_info->id_feeder_sociedad)->nombre_sociedad;

				// Asistentes
				$html_asistentes = '';
				$array_asistentes = json_decode($model_info->asistentes);
				foreach($array_asistentes as $id_asistente){
					$asistente = $this->AC_Beneficiaries_model->get_one($id_asistente)->id_nacionalidad;
					$html_asistentes .= "&bull;$asistente<br>";
				}
				$view_data["asistentes"] = $html_asistentes;

				
				$creado_por = $this->Users_model->get_one($model_info->created_by);
				$view_data["creado_por"] = ($creado_por->id) ? $creado_por->first_name . " " . $creado_por->last_name : "-";
				
				$modificado_por = $this->Users_model->get_one($model_info->modified_by);
				$view_data["modificado_por"] = ($modificado_por->id) ? $modificado_por->first_name . " " . $modificado_por->last_name : "-";
				
				$view_data["id_cliente"] = $id_cliente;

				// LOG ACTION viewed
				// if($client_area == "territory"){
				// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_territory, NULL, "viewed", $id_actividad, 'element');
				// }
				// if($client_area == "distribution"){
				// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_distribution, NULL, "viewed", $id_actividad, 'element');
				// }

				$this->load->view('ac_activities/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
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
	
	function create_activities_folder($id_actividad) {
		
		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
		if(!file_exists(__DIR__.'/../../files/actividades/client_'.$id_cliente."/actividad_".$id_actividad)) {
			if(mkdir(__DIR__.'/../../files/actividades/client_'.$id_cliente."/actividad_".$id_actividad, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
		
	}	
	
	function download_file($id, $tipo_archivo) {

		$file_info = $this->AC_Activities_model->get_one($id);

		if(!$file_info){
			redirect("forbidden");
		}
		
		$filename = $file_info->$tipo_archivo;
		$id_cliente = $file_info->id_cliente;
		$id_actividad = $file_info->id;
		
        //serilize the path
		$file_data = serialize(array(array("file_name" => $filename)));
		
		// LOG ACTION downloaded_file
		// $client_area = $this->session->client_area;
		
		// if($client_area == "territory"){
		// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_territory, NULL, "downloaded_file", $file_info->id, 'element');
		// }
		// if($client_area == "distribution"){
		// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_distribution, NULL, "downloaded_file", $file_info->id, 'element');
		// }
		
		download_app_files("files/actividades/client_".$id_cliente."/actividad_".$id_actividad."/", $file_data);
    
	}
	
	function delete_file(){
		
		$id = $this->input->post('id');
		$id_campo = $this->input->post('campo');
		$file_info = $this->AC_Activities_model->get_one($id);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$id_cliente = $file_info->id_cliente;
		$filename = $file_info->$id_campo;

		$campo_nuevo = $this->load->view("includes/form_file_uploader", array(
			"upload_url" => get_uri("AC_Activities/upload_file"),
			"validation_url" =>get_uri("AC_Activities/validate_file"),
			"obligatorio" => "",
			"id_campo" => $id_campo
		), true);
		
		echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo, 'id_campo' => $id_campo));
		
	}

	function get_attendees(){

		$id_cliente = $this->Users_model->get_one($this->session->user_id)->client_id;
		
        if (!$this->login_user->id) {
            redirect("forbidden");
        }

		$id_sociedad = $this->input->post('id_sociedad');

		$asistentes = $this->AC_Beneficiaries_model->get_all_where(array(
			'sociedad_desc' => $id_sociedad,
			'id_cliente' => $id_cliente,
			'deleted' => 0
		))->result();

		$array_asistentes = array();
		foreach ($asistentes as $asistente) {
			$array_asistentes[$asistente->id] = $asistente->id_nacionalidad;
		}
		
		$obligatorio = "data-rule-required='true' data-msg-required='" . lang('field_required') . "'";
		$html = '';
		$html .= '<div class="form-group">';
			$html .= '<label for="asistentes" class="col-md-3">'.lang('attendees').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= form_multiselect(
						"asistentes[]", 
						$array_asistentes, 
						"", 
						"id='asistentes' class='select2 validate-hidden' $obligatorio"
					);
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;

	}
	
	function get_excel(){
		
		$client_area = $this->session->client_area;
		$id_usuario = $this->session->user_id;
		$id_cliente = $this->login_user->client_id;
		
		$options = array(
			"id_cliente" => $id_cliente
		);
		
		if($client_area == "territory"){
			
			$puede_ver = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
			
			$list_data = $this->AC_Activities_model->get_details($options)->result();
			$result = array();
			foreach ($list_data as $data) {

				// Todos: 1
				// Propios: 2 
				// Ninguno: 3
				
				if($puede_ver == 1){
					$result[] = $this->_make_row_excel($data);
				}
				
				if($puede_ver == 2 && $id_usuario == $data->created_by){
					$result[] = $this->_make_row_excel($data);
				}
				
				if($puede_ver == 3){
					$result[1] = array();
				}
				
			}

		}
		
		/* if($client_area == "distribution"){
			
			$puede_ver = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
			
			$list_data = $this->AC_Activities_model->get_details($options)->result();
			$result = array();
			foreach ($list_data as $data) {
				
				if($puede_ver == 1){ //Todos
					$result[] = $this->_make_row_excel($data);
				}
				if($puede_ver == 2){ //Propios
					if($id_usuario == $data->created_by){
						$result[] = $this->_make_row_excel($data);
					}
				}
				if($puede_ver == 3){ //Ninguno
					$result[1] = array();
				}

			}
			
		} */

		$nombre_columnas = array();
		$nombre_columnas[] = array("nombre_columna" => lang("date"), "id_tipo_campo" => "date");
		$nombre_columnas[] = array("nombre_columna" => lang("activity"), "id_tipo_campo" => "activity");
		$nombre_columnas[] = array("nombre_columna" => lang("society"), "id_tipo_campo" => "society");
		$nombre_columnas[] = array("nombre_columna" => lang("attendees"), "id_tipo_campo" => "attendees");
		$nombre_columnas[] = array("nombre_columna" => lang("benefited_sons_daughters"), "id_tipo_campo" => "benefited_sons_daughters");
		$nombre_columnas[] = array("nombre_columna" => lang("ac_inversion"), "id_tipo_campo" => "ac_inversion");
		$nombre_columnas[] = array("nombre_columna" => lang("ac_record"), "id_tipo_campo" => "ac_record");
		$nombre_columnas[] = array("nombre_columna" => lang("ac_observations"), "id_tipo_campo" => "ac_observations");
		$nombre_columnas[] = array("nombre_columna" => lang("ac_other_files"), "id_tipo_campo" => "ac_other_files");
		
		
		$this->load->library('excel');		
		
		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle(lang("activities"))
							 ->setSubject(lang("activities"))
							 ->setDescription(lang("activities"))
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");
							 
		$client_info = $this->Clients_model->get_one($id_cliente);
		//$project_info = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $record_info->id, "deleted" => 0));
		
		if($client_info->id){
			if($client_info->color_sitio){
				$color_sitio = str_replace('#', '', $client_info->color_sitio);
			} else {
				$color_sitio = "00b393";
			}
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
            ->setCellValue('C1', lang("activities"))
			// ->setCellValue('C2', lang($client_area))
            ->setCellValue('C2', lang("date").': '.$fecha.' '.lang("at").' '.$hora);
		
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
					
					if($columna["id_tipo_campo"] == "date"
					|| $columna["id_tipo_campo"] == "activity"
					|| $columna["id_tipo_campo"] == "society"
					|| $columna["id_tipo_campo"] == "attendees"
					|| $columna["id_tipo_campo"] == "ac_record"
					|| $columna["id_tipo_campo"] == "ac_other_files"
					|| $columna["id_tipo_campo"] == "benefited_sons_daughters"
					|| $columna["id_tipo_campo"] == "ac_inversion"
					){
					
						$doc->getActiveSheet()->setCellValueExplicitByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);	

					} elseif($columna["id_tipo_campo"] == "ac_observations"){

						
						$doc->getActiveSheet()->setCellValueExplicitByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
								'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);	
						
						$doc->getActiveSheet()->getColumnDimension($name_col)->setAutoSize(false);
						$doc->getActiveSheet()->getColumnDimension($name_col)->setWidth(45);
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
		/* $lastColumn = $doc->getActiveSheet()->getHighestColumn();	
		$lastColumn++;
		$cells = array();
		for ($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;	
		} */
		/*foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}*/
		
		$nombre_hoja = strlen(lang("activities")) > 31 ? substr(lang("activities"), 0, 28).'...' : lang("activities");
		$nombre_hoja = $nombre_hoja ? $nombre_hoja : " ";
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		$filename = lang("activities").'_'.date("Y-m-d");
		$filename = $filename.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');

		// LOG ACTION downloaded_excel
		// if($client_area == "territory"){
		// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_territory, NULL, "downloaded_excel");
		// }
		// if($client_area == "distribution"){
		// 	$this->Logs_model->add_log($this->login_user->client_id, $this->id_home_modules_info, $this->id_module_distribution, NULL, "downloaded_excel");
		// }

		exit;

	}
	
	private function _make_row_excel($data) {
		
		$id_usuario = $this->session->user_id;		
		$client_area = $this->session->client_area;

		$row_data[] = date(get_setting_client_mimasoft($data->id_cliente, "date_format"), strtotime($data->fecha));

		$row_data[] = $this->AC_Feeders_type_of_activities_model->get_one($data->id_feeder_tipo_actividad)->actividad;

		$row_data[] = $this->AC_Feeders_societies_model->get_one($data->id_feeder_sociedad)->nombre_sociedad;

		$array_asistentes = json_decode($data->asistentes);
		$asistentes = '';
		foreach ($array_asistentes as $id_asistente) {
			$asistentes .= $this->AC_Beneficiaries_model->get_one($id_asistente)->id_nacionalidad;
		}
		$row_data[] = $asistentes;

		$row_data[] = $data->cant_hijos_beneficiados;

		$row_data[] = $data->inversion ? $data->inversion : '-';

		$row_data[] = $data->registro ? $data->registro : '-';

		$row_data[] = $data->observaciones ? $data->observaciones : '-';

		$row_data[] = $data->otros_archivos ? $data->otros_archivos : '-';
		
        return $row_data;
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
	
}