<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sidrep_codes extends MY_Controller {

    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
    }

    function index() {
		
        $this->access_only_allowed_members();
		
		// FILTRO CLIENTE		
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clients_dropdown'] = json_encode($array_clientes);

        //FILTRO PROYECTO
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
		
		// FILTRO CATEGORIA
        $array_categories[] =  array("id" => "", "text" => "- ".lang("category")." -");
        $categories = $this->Categories_model->get_categories_of_materials(array())->result();

        foreach($categories as $category){
            $array_categories[] = array("id" => $category->id, "text" => $category->nombre);
        }
        $view_data["categories_dropdown"] = json_encode($array_categories);

        $this->template->rander("sidrep_codes/index", $view_data);
		
    }

    function modal_form() {
        $this->access_only_allowed_members();

        $id = $this->input->post('id');
        $view_data['model_info'] = $this->Sidrep_codes_model->get_one($id);
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');
        $view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
        $view_data["proyectos"] = array("" => "-") + $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $view_data['model_info']->id_client));
        
        $categories_dropdown = array("" => "-");
        if($id){

            $client_categories = $this->Categories_model->get_categories_of_materials_related_to_client($view_data['model_info']->id_client, array())->result();
            foreach($client_categories as $cc){
                $categories_dropdown[$cc->id] = $cc->nombre;			
            }

            $sidrep_dangerous_characteristic = json_decode($view_data['model_info']->dangerous_characteristic);
			$multiselect_sidrep_dangerous_characteristic = array();
			foreach($sidrep_dangerous_characteristic as $dc){
				$multiselect_sidrep_dangerous_characteristic[] = $dc;
			}
            $view_data['sidrep_dangerous_characteristic'] = $multiselect_sidrep_dangerous_characteristic;
            
        }

        $view_data["categories_dropdown"] = $categories_dropdown;

        $array_dangerous_characteristic["ta"] = lang("dc_ta");
        $array_dangerous_characteristic["tc"] = lang("dc_tc");
        $array_dangerous_characteristic["tl"] = lang("dc_tl");
        $array_dangerous_characteristic["re"] = lang("dc_re");
        $array_dangerous_characteristic["in"] = lang("dc_in");
        $array_dangerous_characteristic["co"] = lang("dc_co");
        $array_dangerous_characteristic["ic"] = lang("dc_ic");

        $view_data["available_dangerous_characteristic"] = $array_dangerous_characteristic;

        $physical_status_dropdown = array(
            "" => "-",
            "solid" => lang("solid"),
            "liquid" => lang("liquid"),
            "other" => lang("other")
        );
        $view_data["physical_status_dropdown"] = $physical_status_dropdown;

        $this->load->view('sidrep_codes/modal_form', $view_data);

    }

    function save() {
        
        $id = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric",
        ));

        $data = array(
            "id_client" => $this->input->post('client'),
            "id_project" => $this->input->post('project'),
			"id_category" => $this->input->post('category'),
            "code_list_a" => $this->input->post('code_list_a'),
            "code_lists_i_ii_iii" => $this->input->post('code_lists_i_ii_iii'),
            "dangerous_characteristic" => json_encode($this->input->post('dangerous_characteristic')),
            "physical_status" => $this->input->post('physical_status')
        );
		
        if($id){
            $data["modified_by"] = $this->login_user->id;
            $data["modified"] = get_current_utc_time();
            
        }else{
            $data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
        }

        // Validamos que previamente no exista un code para cliente, categoria y proyecto ingresado
		$array_validacion = array(
            "id_client" => $this->input->post('client'),
            "id_project" => $this->input->post('project'),
			"id_category" => $this->input->post('category'),
			"deleted" => 0,
        );
		if ($this->Sidrep_codes_model->is_code_exists($array_validacion, $id)) {
			echo json_encode(array("success" => false, 'message' => lang('duplicate_sidrep_in_client')));
			exit();
		}
		
        $save_id = $this->Sidrep_codes_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    function delete() {
        //$this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric" 
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Sidrep_codes_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Sidrep_codes_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    function list_data() {

        $this->access_only_allowed_members();
        
		$options = array(
			"id_client" => $this->input->post("id_client"),
            "id_project" => $this->input->post("id_project"),
			"id_category" => $this->input->post("id_category"),
		);
		
        $list_data = $this->Sidrep_codes_model->get_details($options)->result();
        
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _row_data($id) {
        $options = array(
            "id" => $id
			
        );
        $data = $this->Sidrep_codes_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    private function _make_row($data) {
        
        $project = $this->Projects_model->get_one($data->id_project);

        $row_data = array(
            $data->id,
            $data->client_name ? $data->client_name : "-",
            $data->id_project ? $project->title : "-",
            $data->category_name ? $data->category_name : "-",
            $data->code_list_a ? $data->code_list_a : "-",
            $data->code_lists_i_ii_iii ? $data->code_lists_i_ii_iii : "-"
        );

        $data_dangerous_characteristic = json_decode($data->dangerous_characteristic);
        $list_dangerous_characteristic = "";
        if(count($data_dangerous_characteristic)){
            $list_dangerous_characteristic = "<ul>";
            foreach($data_dangerous_characteristic as $dc){
                $list_dangerous_characteristic .= "<li>" . lang("dc_".$dc). "</li>";
            }
            $list_dangerous_characteristic .= "</ul>";
        } else {
            $list_dangerous_characteristic = "-";
        }
        
        $row_data[] = $list_dangerous_characteristic; 
        $row_data[] = $data->physical_status ? lang($data->physical_status) : "-";

        $row_data[] = modal_anchor(get_uri("sidrep_codes/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_sidrep_code'))) .
        modal_anchor(get_uri("sidrep_codes/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_sidrep_code'), "data-post-id" => $data->id))
        . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_sidrep_code'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("sidrep_codes/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    function view($id = 0) {
       
        if ($id) {
            $options = array("id" => $id);
            $model_info = $this->Sidrep_codes_model->get_details($options)->row();
            if ($model_info){
                $view_data['model'] = $model_info;

                $proyecto = $this->Projects_model->get_one($view_data['model']->id_project);
				$view_data["proyecto"] = $proyecto->id ? $proyecto->title : "-";

				$dangerous_characteristic = json_decode($model_info->dangerous_characteristic);
				$html_dangerous_characteristic = (count($dangerous_characteristic) > 1) ? "<ul>" : "";
				foreach($dangerous_characteristic as $dc){
					$html_dangerous_characteristic .= (count($dangerous_characteristic) > 1) ? "<li>" . lang("dc_".$dc) . "</li>" : lang("dc_".$dc);
				}
				$html_dangerous_characteristic .= (count($dangerous_characteristic) > 1) ? "</ul>" : "";
                $view_data['html_dangerous_characteristic'] = (count($dangerous_characteristic)) ? $html_dangerous_characteristic : "-";
                
                $this->load->view('sidrep_codes/view', $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }

    }

    function get_projects_of_client(){
	
		$id_cliente = $this->input->post('id_client');
		$col_label = $this->input->post('col_label')?$this->input->post('col_label'):'col-md-3';
		$col_projects = $this->input->post('col_projects')?$this->input->post('col_projects'):'col-md-9';

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$proyectos_de_cliente = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente));
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="project" class="'.$col_label.'">'.lang('project').'</label>';
		$html .= '<div class="'.$col_projects.'">';
		$html .= form_dropdown("project", array("" => "-") + $proyectos_de_cliente, "", "id='project' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}

    function get_categories_of_client(){
		
		$id_client = $this->input->post('id_client');
		$categories_dropdown = array("" => "-");
		
		if($id_client){

            $client_categories = $this->Categories_model->get_categories_of_materials_related_to_client($id_client, array())->result();
            foreach($client_categories as $cc){
                $categories_dropdown[$cc->id] = $cc->nombre;			
            }

		}
		
		$html .= '<div class="form-group">';
			$html .= '<label for="category" class="col-md-3">'.lang('category').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= form_dropdown("category", $categories_dropdown, $model_info->id_category, "id='category' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
		
	}

}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */