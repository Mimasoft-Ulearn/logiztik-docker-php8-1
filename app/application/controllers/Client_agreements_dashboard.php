<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Client_agreements_dashboard extends MY_Controller {

	function __construct() {
        parent::__construct();
    }

	function index($client_area = ""){

		// Se activa en el menú lateral el módulo de Acuerdos y se desactivan los demás.
		$this->session->set_userdata('menu_agreements_active', TRUE);
		$this->session->set_userdata('menu_project_active', NULL);
		$this->session->set_userdata('project_context', NULL);
		$this->session->set_userdata('menu_kpi_active', NULL);
		$this->session->set_userdata('menu_help_and_support_active', NULL);
		$this->session->set_userdata('menu_recordbook_active', NULL);
		$this->session->set_userdata('menu_ec_active', NULL);
		$this->session->set_userdata('menu_consolidated_impacts_active', NULL);
		$this->session->set_userdata('client_area', 'territory');
		
		$acuerdos_territorio_disponibilidad_modulo = $this->Client_module_availability_model->get_one_where(array(
			"id_cliente" => $this->login_user->client_id,
			"id_modulo" => 5,
			"deleted" => 0
		));
		if($client_area == "territory" && !$acuerdos_territorio_disponibilidad_modulo->disponible){
			// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Acuerdos Territorio esté deshabilitada.
			$this->block_url_client_context($id_cliente, 5);
		}
		
		$view_data["client_agreements_info"] = $this->AC_Client_agreements_info_model->get_one_where(array(
			"client_area" => $client_area,
			"deleted" => 0
		));
		
		if($client_area == "territory" || $client_area == "distribution"){
			$this->session->set_userdata('client_area', $client_area);	
			
		} else {
			redirect("client_agreements");
		}

		$client_area = $this->session->client_area;
		$view_data["client_area"] = $this->session->client_area;
		
		//A partir de lisado de usuarios del cliente PERFILADOS PARA ACCEDER AL MÓDULO (PENDIENTE MODIFICAR CONSULTA)
		$id_cliente = $this->login_user->client_id;
		$view_data["ejecutores"] = $this->Users_model->get_all_where(array(
			"client_id" => $id_cliente,
			"id_client_context_profile" => 2,
			"deleted" => 0
		))->result_array();
		

		$this->template->rander("client_agreements/client_agreements", $view_data);
	}

}