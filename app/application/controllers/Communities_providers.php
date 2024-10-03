<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Communities_providers extends MY_Controller {

	private $id_modulo_cliente;
	private $id_submodulo_cliente;

    function __construct() {
        parent::__construct();
		$this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 9; // Comunidades
		$this->id_submodulo_cliente = 23; // Proveedores

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);

    }

    function index() {

		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$view_data["project_info"] = $proyecto;
        
        $view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
        $view_data["puede_agregar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "agregar");
        $this->template->rander("communities_providers/index", $view_data);
    }
	
	function modal_form() {
		
        $id_provider = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');
        $view_data['model_info'] = $this->Communities_providers_model->get_one($id_provider);

        $this->load->view('communities_providers/modal_form', $view_data);
    }
	
	function save() {

        $id_provider = $this->input->post('id');
        $id_client = $this->login_user->client_id;
        $id_project = $this->session->project_context;
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));
		
		// Archivos
		$ethical_social_audit_file = $this->input->post('ethical_social_audit_file');
        $ethical_social_audit_file_prefix = "ethical_social_audit_file"."-".$ethical_social_audit_file;
        
        $non_discrimination_policy_file = $this->input->post('non_discrimination_policy_file');
        $non_discrimination_policy_file_prefix = "non_discrimination_policy_file"."-".$non_discrimination_policy_file;

        $anti_corruption_and_transparency_policy_file = $this->input->post('anti_corruption_and_transparency_policy_file');
        $anti_corruption_and_transparency_policy_file_prefix = "anti_corruption_and_transparency_policy_file"."-".$anti_corruption_and_transparency_policy_file;

        $environmental_policy_file = $this->input->post('environmental_policy_file');
        $environmental_policy_file_prefix = "environmental_policy_file"."-".$environmental_policy_file;

        $ethical_policy_oit_file = $this->input->post('ethical_policy_oit_file');
        $ethical_policy_oit_file_prefix = "ethical_policy_oit_file"."-".$ethical_policy_oit_file;

        $accident_report_file = $this->input->post('accident_report_file');
        $accident_report_file_prefix = "accident_report_file"."-".$accident_report_file;

        $id_campo_archivo_eliminar = $this->input->post('id_campo_archivo_eliminar'); // id(s) de archivo(s) a eliminar

	
		$data = array(
            "id_client" => $id_client,
            "id_project" => $id_project,
			"date" => $this->input->post("date"),
			"name" => $this->input->post("name"),
            "responsible_name" => $this->input->post("responsible_name"),
            "responsible_email" => $this->input->post("responsible_email"),
            "ethical_social_audit" => $this->input->post("ethical_social_audit"),
            "non_discrimination_policy" => $this->input->post("non_discrimination_policy"),
            "anti_corruption_and_transparency_policy" => $this->input->post("anti_corruption_and_transparency_policy"),
            "environmental_policy" => $this->input->post("environmental_policy"),
            "promote_free_assoc_and_neg_rights" => $this->input->post("promote_free_assoc_and_neg_rights"),
            "comply_with_national_legislation" => $this->input->post("comply_with_national_legislation"),
            "workers_subjected_to_forced_labor" => $this->input->post("workers_subjected_to_forced_labor"),
            "workers_minimum_age" => $this->input->post("workers_minimum_age"),
            "workers_lower_remuneration" => $this->input->post("workers_lower_remuneration"),
            "max_hours_worked_per_week" => $this->input->post('max_hours_worked_per_week'),
            "overtime" => $this->input->post('overtime'),
            "max_overtime_hours_per_week" => $this->input->post('max_overtime_hours_per_week'),
            "employ_emmigrants" => $this->input->post('employ_emmigrants'),
            "ethical_policy_oit" => $this->input->post('ethical_policy_oit'),
            "comply_hygiene_and_safety_conditions" => $this->input->post('comply_hygiene_and_safety_conditions'),
            "risk_prevention_specialist" => $this->input->post('risk_prevention_specialist'),
            "mention_measures_taken_to_prevent_covid_19" => $this->input->post('mention_measures_taken_to_prevent_covid_19'),
		);
		
		if($id_provider){
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
			$save_id = $this->Communities_providers_model->save($data, $id_provider);
		} else {
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
			$save_id = $this->Communities_providers_model->save($data);
        }
        
        // Eliminar archivos borrados por el usuario antes de enviar el formulario.
		if($id_provider){
			if($id_campo_archivo_eliminar){
				$info_provider = $this->Communities_providers_model->get_one($id_provider);
				foreach($id_campo_archivo_eliminar as $id_archivo){
					$filename = $info_provider->$id_archivo;
					$file_path = "files/communities_providers/project_".$id_project."/provider_".$id_provider."/".$filename;
                    $save_id = $this->Communities_providers_model->update_where(array("$id_archivo" => NULL), array("id" => $id_provider));
					delete_file_from_directory($file_path);
				}
			}
        }
        
        // Archivos
        $crear_carpeta = $this->create_communities_providers_folder($id_project, $save_id);
                
        if($ethical_social_audit_file){
            $nombre_real_archivo = remove_file_prefix($ethical_social_audit_file_prefix);	
            $archivo_subido = move_temp_file("ethical_social_audit_file"."_".$nombre_real_archivo, "files/communities_providers/project_".$id_project."/provider_".$save_id."/", "", "", $ethical_social_audit_file_prefix);
            $data_provider = array("ethical_social_audit_file" => $archivo_subido);
            $save_id = $this->Communities_providers_model->save($data_provider, $save_id);
        }

        if($non_discrimination_policy_file){
            $nombre_real_archivo = remove_file_prefix($non_discrimination_policy_file_prefix);	
            $archivo_subido = move_temp_file("non_discrimination_policy_file"."_".$nombre_real_archivo, "files/communities_providers/project_".$id_project."/provider_".$save_id."/", "", "", $non_discrimination_policy_file_prefix);
            $data_provider = array("non_discrimination_policy_file" => $archivo_subido);
            $save_id = $this->Communities_providers_model->save($data_provider, $save_id);
        }

        if($anti_corruption_and_transparency_policy_file){
            $nombre_real_archivo = remove_file_prefix($anti_corruption_and_transparency_policy_file_prefix);	
            $archivo_subido = move_temp_file("anti_corruption_and_transparency_policy_file"."_".$nombre_real_archivo, "files/communities_providers/project_".$id_project."/provider_".$save_id."/", "", "", $anti_corruption_and_transparency_policy_file_prefix);
            $data_provider = array("anti_corruption_and_transparency_policy_file" => $archivo_subido);
            $save_id = $this->Communities_providers_model->save($data_provider, $save_id);
        }

        if($environmental_policy_file){
            $nombre_real_archivo = remove_file_prefix($environmental_policy_file_prefix);	
            $archivo_subido = move_temp_file("environmental_policy_file"."_".$nombre_real_archivo, "files/communities_providers/project_".$id_project."/provider_".$save_id."/", "", "", $environmental_policy_file_prefix);
            $data_provider = array("environmental_policy_file" => $archivo_subido);
            $save_id = $this->Communities_providers_model->save($data_provider, $save_id);
        }

        if($ethical_policy_oit_file){
            $nombre_real_archivo = remove_file_prefix($ethical_policy_oit_file_prefix);	
            $archivo_subido = move_temp_file("ethical_policy_oit_file"."_".$nombre_real_archivo, "files/communities_providers/project_".$id_project."/provider_".$save_id."/", "", "", $ethical_policy_oit_file_prefix);
            $data_provider = array("ethical_policy_oit_file" => $archivo_subido);
            $save_id = $this->Communities_providers_model->save($data_provider, $save_id);
        }

        if($accident_report_file){
            $nombre_real_archivo = remove_file_prefix($accident_report_file_prefix);	
            $archivo_subido = move_temp_file("accident_report_file"."_".$nombre_real_archivo, "files/communities_providers/project_".$id_project."/provider_".$save_id."/", "", "", $accident_report_file_prefix);
            $data_provider = array("accident_report_file" => $archivo_subido);
            $save_id = $this->Communities_providers_model->save($data_provider, $save_id);
        }
		
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	
	function delete() {

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Communities_providers_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Communities_providers_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function list_data() {

        $id_client = $this->login_user->client_id;
        $id_project = $this->session->project_context;
        $puede_ver = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
        
		$options = array(
			"id_client" => $id_client,
			"id_project" => $id_project
		);
		
        $list_data = $this->Communities_providers_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {			
            if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row($data);
			}
			if($puede_ver == 2){ //Propios
				if($this->session->user_id == $data->created_by){
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
		
        $data = $this->Communities_providers_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {

        $id_usuario = $this->session->user_id;
        $puede_editar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
		$puede_eliminar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");

        $row_data = array(
            $data->id,
            get_date_format($data->date, $data->id_project),
            $data->name,
            $data->responsible_name,
            $data->responsible_email
        );
        
        $view = modal_anchor(get_uri("communities_providers/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_provider'), "data-post-id" => $data->id));
        $edit = modal_anchor(get_uri("communities_providers/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_provider'), "data-post-id" => $data->id));
        $delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_provider'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("communities_providers/delete"), "data-action" => "delete-confirmation"));
        
        //Validaciones de Perfil
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
	
	function view($id_provider = 0) {
		
        if ($id_provider) {
            $options = array("id" => $id_provider);
            $model_info = $this->Communities_providers_model->get_details($options)->row();
            if ($model_info) {
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $model_info;
				$this->load->view('communities_providers/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    function get_fields_ethical_social_audit(){

        $ethical_social_audit = $this->input->post("ethical_social_audit");
        
        $html = "";
        if($ethical_social_audit == "yes"){
            $html = "<div class='form-group'>";
            $html .= '<label for="ethical_social_audit_file" class="col-md-3">'.lang('attach_ethical_audit_social').'</label>';
            $html .= '<div class="col-md-9">';		
            $html .= $this->load->view("includes/form_file_uploader", array(
                        "upload_url" =>get_uri("communities_providers/upload_file"),
                        "validation_url" =>get_uri("communities_providers/validate_file"),
                        "html_name" => "ethical_social_audit_file",
                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                        "id_campo" => "ethical_social_audit_file",
                    ),
                    true);
            $html .= '</div>';
            $html .= '</div>';
        }
		
		echo $html;
		
    }
    
    function get_fields_non_discrimination_policy(){

        $non_discrimination_policy = $this->input->post("non_discrimination_policy");
        
        $html = "";
        if($non_discrimination_policy == "yes"){
            $html = "<div class='form-group'>";
            $html .= '<label for="non_discrimination_policy_file" class="col-md-3">'.lang('attach_non_discrimination_policy').'</label>';
            $html .= '<div class="col-md-9">';		
            $html .= $this->load->view("includes/form_file_uploader", array(
                        "upload_url" =>get_uri("communities_providers/upload_file"),
                        "validation_url" =>get_uri("communities_providers/validate_file"),
                        "html_name" => "non_discrimination_policy_file",
                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                        "id_campo" => "non_discrimination_policy_file",
                    ),
                    true);
            $html .= '</div>';
            $html .= '</div>';
        }
		
		echo $html;
		
    }
    
    function get_fields_anti_corruption_and_transparency_policy(){

        $anti_corruption_and_transparency_policy = $this->input->post("anti_corruption_and_transparency_policy");
        
        $html = "";
        if($anti_corruption_and_transparency_policy == "yes"){
            $html = "<div class='form-group'>";
            $html .= '<label for="anti_corruption_and_transparency_policy_file" class="col-md-3">'.lang('attach_anti_corruption_and_transparency_policy').'</label>';
            $html .= '<div class="col-md-9">';		
            $html .= $this->load->view("includes/form_file_uploader", array(
                        "upload_url" =>get_uri("communities_providers/upload_file"),
                        "validation_url" =>get_uri("communities_providers/validate_file"),
                        "html_name" => "anti_corruption_and_transparency_policy_file",
                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                        "id_campo" => "anti_corruption_and_transparency_policy_file",
                    ),
                    true);
            $html .= '</div>';
            $html .= '</div>';
        }
		
        echo $html;
        
    }

    function get_fields_environmental_policy(){

        $environmental_policy = $this->input->post("environmental_policy");
        
        $html = "";
        if($environmental_policy == "yes"){
            $html = "<div class='form-group'>";
            $html .= '<label for="environmental_policy_file" class="col-md-3">'.lang('attach_environmental_policy').'</label>';
            $html .= '<div class="col-md-9">';		
            $html .= $this->load->view("includes/form_file_uploader", array(
                        "upload_url" =>get_uri("communities_providers/upload_file"),
                        "validation_url" =>get_uri("communities_providers/validate_file"),
                        "html_name" => "environmental_policy_file",
                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                        "id_campo" => "environmental_policy_file",
                    ),
                    true);
            $html .= '</div>';
            $html .= '</div>';
        }
		
        echo $html;

    }

    function get_fields_overtime(){

        $overtime = $this->input->post("overtime");

        $html = "";
        if($overtime == "yes"){
            $html = "<div class='form-group'>";
            $html .= '<label for="max_overtime_hours_per_week" class="col-md-3">'.lang('indicate_max_overtime_hours_per_week').'</label>';
            $html .= '<div class="col-md-9">';		
            $html .= form_input(array(
                        "id" => "max_overtime_hours_per_week",
                        "name" => "max_overtime_hours_per_week",
                        "value" => "",
                        "class" => "form-control",
                        "placeholder" => lang('indicate_max_overtime_hours_per_week'),
                        //"autofocus" => true,
                        //"data-rule-required" => true,
                        //"data-msg-required" => lang("field_required"),
                        "autocomplete"=> "off",
                        "maxlength" => "255"
                    ));
            $html .= '</div>';
            $html .= '</div>';
        }
		
        echo $html;

    }

    function get_fields_ethical_policy_oit(){

        $ethical_policy_oit = $this->input->post("ethical_policy_oit");
        
        $html = "";
        if($ethical_policy_oit == "yes"){
            $html = "<div class='form-group'>";
            $html .= '<label for="ethical_policy_oit_file" class="col-md-3">'.lang('attach_ethical_policy_based_on_oit').'</label>';
            $html .= '<div class="col-md-9">';		
            $html .= $this->load->view("includes/form_file_uploader", array(
                        "upload_url" =>get_uri("communities_providers/upload_file"),
                        "validation_url" =>get_uri("communities_providers/validate_file"),
                        "html_name" => "ethical_policy_oit_file",
                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                        "id_campo" => "ethical_policy_oit_file",
                    ),
                    true);
            $html .= '</div>';
            $html .= '</div>';
        }
		
        echo $html;

    }

    function create_communities_providers_folder($id_project, $id_provider) {
		
		if(!file_exists(__DIR__.'/../../files/communities_providers/project_'.$id_project."/provider_".$id_provider)) {
			if(mkdir(__DIR__.'/../../files/communities_providers/project_'.$id_project."/provider_".$id_provider, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
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

    function download_file($id, $tipo_archivo) {

		$file_info = $this->Communities_providers_model->get_one($id);

		if(!$file_info){
			redirect("forbidden");
		}
		
		$filename = $file_info->$tipo_archivo;
		$id_project = $file_info->id_project;
		$id_provider = $file_info->id;
		
        //serilize the path
        $file_data = serialize(array(array("file_name" => $filename)));
		download_app_files("files/communities_providers/project_".$id_project."/provider_".$id_provider."/", $file_data);
    
    }
    
    function delete_file(){
		
		$id = $this->input->post('id');
		$id_campo = $this->input->post('campo');
		$file_info = $this->Communities_providers_model->get_one($id);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$filename = $file_info->$id_campo;

		$campo_nuevo = $this->load->view("includes/form_file_uploader", array(
			"upload_url" => get_uri("communities_providers/upload_file"),
            "validation_url" =>get_uri("communities_providers/validate_file"),
            "html_name" => $id_campo,
			"obligatorio" => "",
			"id_campo" => $id_campo
		), true);
		
		echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo, 'id_campo' => $id_campo));
		
	}
	
}

