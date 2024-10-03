<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class What_is_mimasoft extends MY_Controller {
	
	//private $id_modulo_cliente;
	//private $id_submodulo_cliente;
	private $id_modulo_contexto_cliente;
	private $id_submodulo_contexto_cliente;
		
	function __construct() {
		
        parent::__construct();

		//$this->id_modulo_cliente = 10;
		//$this->id_submodulo_cliente = 18;
		$this->id_modulo_contexto_cliente = 1;
		$this->id_submodulo_contexto_cliente = 3;
		
    }
	
    public function index() {

        if ($this->login_user->user_type === "staff") {
            
			$view_data['page_type'] = "dashboard";
			$view_data['mimasoft'] = $this->Mimasoft_model->get_all()->result();
			$view_data["puede_ver"] = 1;
			$this->template->rander("what_is_mimasoft/inicio", $view_data);
			
        } else {
			//client's dashboard
			
			$this->session->set_userdata('menu_help_and_support_active', TRUE);
			$this->session->set_userdata('menu_kpi_active', NULL);
			$this->session->set_userdata('menu_project_active', NULL);
			$this->session->set_userdata('client_area', NULL);
			$this->session->set_userdata('project_context', NULL);
			$this->session->set_userdata('menu_ec_active', NULL);
			$this->session->set_userdata('menu_agreements_active', NULL);
			$this->session->set_userdata('menu_recordbook_active', NULL);
			$this->session->set_userdata('menu_consolidated_impacts_active', NULL);
			
			//Si el módulo no está disponible para el usuario, bloquea la url.
			$id_cliente = $this->login_user->client_id;
			$id_proyecto = $this->session->project_context;
			if($id_proyecto){
				$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
			} else {
				$this->block_url_client_context($id_cliente, $this->id_modulo_contexto_cliente);
			}
			
			//$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
			$view_data["puede_ver"] = $this->general_profile_access($this->session->user_id, $this->id_modulo_contexto_cliente, $this->id_submodulo_contexto_cliente, "ver");

            $options = array("id" => $this->login_user->client_id);
            $client_info = $this->Clients_model->get_details($options)->row();
			
            $view_data['page_type'] = "dashboard";
			$view_data['mimasoft'] = $this->Mimasoft_model->get_all()->result();
			
			$proyecto = $this->Projects_model->get_one($this->session->project_context);
			$view_data["project_info"] = $proyecto;
			
			if($client_info->habilitado){
				$this->template->rander("what_is_mimasoft/inicio", $view_data);
			}else{
				$this->session->sess_destroy();
				redirect('signin/index/disabled');
			}
            
        }
    }
	
	/* carga el contexto del proyecto en la constante */

    function load_project_context($project_id = 0) {
		$this->session->set_userdata('project_context', $project_id);
		redirect('faq');
	}

}

