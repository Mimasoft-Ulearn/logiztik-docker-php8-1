<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Public_forms extends CI_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->model("Pages_model");
    }

    function index($form_type = "") {

        $view_data["label_column"] = "col-md-3";
        $view_data["field_column"] = "col-md-9";

        $view_data['topbar'] = "includes/public/topbar";
        $view_data['left_menu'] = false;

        $dropdown_projects = array("" => "-");
        $projects = $this->Projects_model->get_all_where(array(
            "client_id" => 1, // Cliente Subsole
            "matriz_feedback" => 1, // que tenga su matriz de Feedback activada
            "deleted" => 0
        ))->result_array();
        foreach($projects as $project){
            $dropdown_projects[$project['id']] = $project["title"];
        }
        $view_data['dropdown_projects'] = $dropdown_projects;
        
        if($form_type == "feedback"){

            $dropdown_tipos_organizaciones = array("" => "-");
            $tipos_organizaciones = $this->Types_of_organization_model->get_all_where(array("deleted" => 0))->result_array();
            foreach($tipos_organizaciones as $tipo_organizacion){
                $dropdown_tipos_organizaciones[$tipo_organizacion['id']] = lang($tipo_organizacion['nombre']);
            }
            $view_data["dropdown_tipos_organizaciones"] = $dropdown_tipos_organizaciones;

            $this->template->rander("public_forms/feedback", $view_data);

        } elseif($form_type == "communities_providers"){

            $this->template->rander("public_forms/communities_providers", $view_data);
            
        } else {
            show_404();
        }

    }

    function get_responsables_dropdown(){

        $id_project = $this->input->post('id_project');

        $project_members = $this->Project_members_model->get_all_where(array("project_id" => $id_project, "deleted" => 0))->result_array();
        $responsables_dropdown = array("" => "-");
        foreach($project_members as $pm){
            $user = $this->Users_model->get_one($pm['user_id']);
            $responsables_dropdown[$user->id] = $user->first_name . " " . $user->last_name;
        }
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="responsible" class="col-md-3">'.lang('who_do_you_want_to_contact').'</label>';
		$html .= '<div class="col-md-9">';
		$html .=  form_dropdown("responsible", $responsables_dropdown, array(), "id='responsible' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;

    }

    function save_feedback(){

        $id_project = $this->input->post("id_project");
        $feedback_matrix_config = $this->Feedback_matrix_config_model->get_one_where(array(
            "id_proyecto" => $id_project
        ));

        $data = array(
			"id_feedback_matrix_config" => $feedback_matrix_config->id,
			"fecha" => date("Y-m-d"),
			"nombre" => $this->input->post('name'),
			"email" => $this->input->post('email'),
			"phone" => $this->input->post('phone'),
			"id_tipo_organizacion" => $this->input->post('type_of_stakeholder'),
			"proposito_visita" => $this->input->post('visit_purpose'),
			"comments" => $this->input->post('comments'),
			"responsable" => $this->input->post('responsible'),
            "requires_monitoring" => intval($this->input->post('requires_monitoring')),
            "created_by" => 4, // id del usuario Invitado
			"created" => get_current_utc_time()
		);
		
		$save_id = $this->Values_feedback_model->save($data);

        if ($save_id) {
            echo json_encode(array("success" => true, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
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

    function get_fields_accident_report(){

        $html = "<div class='form-group'>";
        $html .= '<label for="attach_accident_report" class="col-md-3">'.lang('attach_accident_report').'</label>';
        $html .= '<div class="col-md-9">';		
        $html .= $this->load->view("includes/page_file_uploader", array(
                    "upload_url" =>get_uri("Public_forms/upload_file"),
                    "validation_url" =>get_uri("Public_forms/validate_file"),
                    "html_name" => "accident_report_file",
                    //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                    "id_campo" => "accident_report_file",
                ),
                true);
        $html .= '</div>';
        $html .= '</div>';
		
        echo $html;

    }

    function save_communities_providers(){

        $id_project = $this->input->post("id_project");
        $project = $this->Projects_model->get_one($id_project);

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

        $data = array(
            "id_client" => $project->client_id,
            "id_project" => $id_project,
			"date" => date("Y-m-d"),
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
            "created_by" => 4, // id del usuario Invitado
			"created" => get_current_utc_time()
        );

        $save_id = $this->Communities_providers_model->save($data);

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
            echo json_encode(array("success" => true, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }

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

    function save_public_lang($language){
        if($language){
            $this->load->helper('cookie');
            $language_cookie = array(
                "name" => "public_language",
                "value" => $language,
                'expire' => 3600, // en segundos (1 hora)                                                                              
                'secure' => true
            );
            $this->input->set_cookie("public_language", $language, 300);
        }
    }

}
