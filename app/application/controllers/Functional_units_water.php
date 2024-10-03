<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Functional_units_water extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
	private $id_admin_module;
	private $id_admin_submodule;
	
    function __construct() {
        
		parent::__construct();
		//check permission to access this module
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 14;
		$this->id_submodulo_cliente = 28;
		
		//$this->id_admin_module = 7;
		//$this->id_admin_submodule = 24;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
    }

    /* load clients list view */

    function index() {
        //$this->access_only_allowed_members();
	
		if ($this->login_user->user_type === "staff") {
			
			// FILTRO CLIENTE
			$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
			$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
			foreach($clientes as $id => $company_name){
				$array_clientes[] = array("id" => $id, "text" => $company_name);
			}
			$view_data['clientes_dropdown'] = json_encode($array_clientes);
			
			// FILTRO PROYECTO
			$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
			$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
			foreach($proyectos as $id => $title){
				$array_proyectos[] = array("id" => $id, "text" => $title);
			}
			$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
			
			// FILTRO SUBPROYECTO
			$array_subproyectos[] = array("id" => "", "text" => "- ".lang("subproject")." -");
			$subproyectos = $this->Subprojects_model->get_dropdown_list(array("nombre"), 'id');
			foreach($subproyectos as $id => $nombre){
				$array_subproyectos[] = array("id" => $id, "text" => $nombre);
			}
			$view_data['subproyectos_dropdown'] = json_encode($array_subproyectos);

			$this->template->rander("functional_units/index", $view_data);
			
        } else {
            //client's dashboard    

			$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
			
            $options = array("id" => $this->login_user->client_id);
            $client_info = $this->Clients_model->get_details($options)->row();
			$id_proyecto = $this->session->project_context;
			
			$project_info = $this->Projects_model->get_details(array("id" => $id_proyecto))->row();
			//$subprojects = $this->Subprojects_model->get_details(array("id_proyecto" => $id_proyecto))->result();
			//$footprints = $this->Footprints_model->get_footprints_of_methodology(3)->result(); // Metodología con id 3: Huella de Agua
			//$footprint_ids = array();
			//foreach($footprints as $footprint){
			//	$footprint_ids[] = $footprint->id;
			//}
			//$options_footprint_ids = array("footprint_ids" => $footprint_ids);
			//$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto, $options_footprint_ids)->result();
			$unidades_funcionales = $this->Functional_units_model->get_details(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto))->result();
			$view_data['unidades_funcionales'] = $unidades_funcionales;

			$dropdown_functional_units = array("" => "-");
			foreach($unidades_funcionales as $uf){
				$dropdown_functional_units[$uf->id] = $uf->nombre;
			}
			$view_data["dropdown_functional_units"] = $dropdown_functional_units;
			//$view_data['criterios_calculos'] = $this->Unit_processes_model->get_rules_calculations_of_project($client_info->id, $project_info->id)->result();
			//$view_data['procesos_unitarios'] = $this->Unit_processes_model->get_pu_of_projects($project_info->id)->result_array();
			
			//$view_data['client_info'] = $client_info;
			$view_data['proyecto'] = $project_info;
			//$view_data['tecnologia'] = $tecnologia->nombre;
			//$view_data['subprojects'] = $subprojects;
			//$view_data['huellas'] = $huellas;
			//$view_data['client_id'] = $client_info->id;
			//$view_data['page_type'] = "dashboard";
			
			//$view_data['General_settings_model'] = $this->General_settings_model;
			//$view_data['Projects_model'] = $this->Projects_model;
			//$view_data['Project_rel_footprints_model'] = $this->Project_rel_footprints_model;
			//$view_data['Calculation_model'] = $this->Calculation_model;
			//$view_data['Fields_model'] = $this->Fields_model;
			//$view_data['Unity_model'] = $this->Unity_model;
			//$view_data["Forms_model"] = $this->Forms_model;
			//$view_data['Characterization_factors_model'] = $this->Characterization_factors_model;
			//$view_data['Form_rel_materiales_rel_categorias_model'] = $this->Form_rel_materiales_rel_categorias_model;
			//$view_data['Unit_processes_model'] = $this->Unit_processes_model;
			//$view_data['Assignment_model'] = $this->Assignment_model;
			//$view_data['Assignment_combinations_model'] = $this->Assignment_combinations_model;
			
			//$view_data['Module_footprint_units_model'] = $this->Module_footprint_units_model;
			//$view_data['Conversion_model'] = $this->Conversion_model;

			// MESES DE ACUERDO A RANGO DE FECHAS
			//$date_today = get_my_local_time('Y-m-'.'01');
            //$date_twelve_ago = new DateTime($date_today);
            //$date_twelve_ago->sub(new DateInterval('P11M'));
            //$date_twelve_ago = $date_twelve_ago->format('Y-m-').'01';

            //$array_categorias_fechas = array();
            //$interval = new DateInterval('P1M');
            //$realEnd = new DateTime($date_today);
            //$realEnd->add($interval);
            //$period = new DatePeriod(new DateTime($date_twelve_ago), $interval, $realEnd);
            //foreach($period as $date) {
            //    $array_categorias_fechas[] = lang("short_".strtolower($date->format('F'))).'-'.$date->format('y');
            //}

			//$view_data["array_categorias_fechas"] = $array_categorias_fechas;
			
			//$id_unidad_volumen = $this->Module_footprint_units_model->get_one_where(array(
			//	"id_cliente" => $client_info->id, 
			//	"id_proyecto" => $id_proyecto, 
			//	"id_tipo_unidad" => 2, 
			//	"deleted" => 0
			//))->id_unidad;
			//$unidad_volumen = $this->Unity_model->get_one($id_unidad_volumen)->nombre;
			//$unidad_volumen_nombre_real = $this->Unity_model->get_one($id_unidad_volumen)->nombre_real;
			//$view_data["unidad_volumen"] = $unidad_volumen;
			//$view_data["unidad_volumen_nombre_real"] = $unidad_volumen_nombre_real;

			if($client_info->habilitado){
				$this->template->rander("functional_units_water/client/index", $view_data);
			}else{
				$this->session->sess_destroy();
				redirect('signin/index/disabled');
			}
            
        }

    }

    /* load client add/edit modal */

    function modal_form() {
        $this->access_only_allowed_members();

        $functional_unit_id = $this->input->post('id');
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view'); //view='details' needed only when loding from the client's details view
        $view_data['model_info'] = $this->Functional_units_model->get_one($functional_unit_id);
        
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		$view_data["proyectos"] =  array("" => "-") + $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $view_data['model_info']->id_cliente));
		$view_data["subproyectos"] = array("" => "-") + $this->Subprojects_model->get_dropdown_list(array("nombre"), "id", array("id_proyecto" => $view_data['model_info']->id_proyecto));
        $this->load->view('functional_units/modal_form', $view_data);
    }

    function get_currency_dropdown_select2_data() {
        $currency = array(array("id" => "", "text" => "-"));
        foreach (get_international_currency_code_dropdown() as $value) {
            $currency[] = array("id" => $value, "text" => $value);
        }
        return $currency;
    }

    /* insert or update a client */

    function save() {
        $functional_unit_id = $this->input->post('id');

        validate_submitted_data(array(
            "id" => "numeric",
        ));

        $data = array(
            "nombre" => $this->input->post('functional_unit_name'),
			"id_cliente" => $this->input->post('client'),
			"id_proyecto" => $this->input->post('project'),
			"id_subproyecto" => $this->input->post('subproject'),
			"unidad" => $this->input->post('functional_unit_unit')
        );
		
		// VALIDACION DE NOMBRE REPETIDO
		//edit
		if($functional_unit_id){
			
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
			
			$titulo_uf = $this->input->post('functional_unit_name');
			$uf_same_name = $this->Functional_units_model->get_all_where(
				array(
					"id_cliente" => $this->input->post('client'),
					"id_proyecto" => $this->input->post('project'), 
					"id_subproyecto" => $this->input->post('subproject'), 
					"nombre" => $titulo_uf, 
					"deleted" => 0
				)
			);
			if($uf_same_name->num_rows() && $uf_same_name->row()->id != $functional_unit_id){
				echo json_encode(array("success" => false, 'message' => lang('uf_title_warning')));
				exit();
			}
			
		}else{//insert
		
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
		
			$titulo_uf = $this->input->post('functional_unit_name');
			$uf_same_name = $this->Functional_units_model->get_all_where(
				array(
					"id_cliente" => $this->input->post('client'),
					"id_proyecto" => $this->input->post('project'), 
					"id_subproyecto" => $this->input->post('subproject'), 
					"nombre" => $titulo_uf, 
					"deleted" => 0
				)
			)->result();
			if($uf_same_name){
				echo json_encode(array("success" => false, 'message' => lang('uf_title_warning')));
				exit();
			}
		}

        $save_id = $this->Functional_units_model->save($data, $functional_unit_id);
        if ($save_id) {
			
			// Guardar histórico notificaciones
			$options = array(
				"id_client" => $this->input->post('client'),
				"id_project" => $this->input->post('project'),
				"id_user" => $this->login_user->id,
				"module_level" => "admin",
				"id_admin_module" => $this->id_admin_module,
				"id_admin_submodule" => $this->id_admin_submodule,
				"event" => ($functional_unit_id) ? "uf_edit_element" : "uf_add_element",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options);
			
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved'))); // se usará en este caso el view?
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete or undo a client */

    function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		$functional_unit = $this->Functional_units_model->get_one($id);
        if ($this->input->post('undo')) {
            if ($this->Functional_units_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Functional_units_model->delete($id)) {
                
				// Guardar histórico notificaciones
				$options = array(
					"id_client" => $functional_unit->id_cliente,
					"id_project" => $functional_unit->id_proyecto,
					"id_user" => $this->login_user->id,
					"module_level" => "admin",
					"id_admin_module" => $this->id_admin_module,
					"id_admin_submodule" => $this->id_admin_submodule,
					"event" => "uf_delete_element",
					"id_element" => $id
				);
				ayn_save_historical_notification($options);
				
				echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of clients, prepared for datatable  */

    function list_data() {

        $this->access_only_allowed_members();
		
		$options = array(
			"id_cliente" => $this->input->post("id_cliente"),
			"id_proyecto" => $this->input->post("id_proyecto"),
			"id_subproyecto" => $this->input->post("id_subproyecto"),
		);
		
		
		
        $list_data = $this->Functional_units_model->get_details($options)->result();
		
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of client list  table */

    private function _row_data($id) {
        $options = array(
            "id" => $id
        );
        $data = $this->Functional_units_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    /* prepare a row of client list table */

    private function _make_row($data) {
		
		$cliente = $this->Clients_model->get_one($data->id_cliente);
		$proyecto = $this->Projects_model->get_one($data->id_proyecto);
		$subproyecto = $this->Subprojects_model->get_one($data->id_subproyecto);
		
        $row_data = array($data->id,
            modal_anchor(get_uri("functional_units/view/" . $data->id), $data->nombre, array("title" => lang('view_functional_unit'), "data-post-id" => $data->id)),
            $cliente->company_name, $proyecto->title, $subproyecto->nombre, $data->unidad,
        );

        $row_data[] =  modal_anchor(get_uri("functional_units/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_functional_unit'), "data-post-id" => $data->id)) 
				. modal_anchor(get_uri("functional_units/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_functional_unit'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_functional_unit'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("functional_units/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    function view($functional_unit_id = 0) {
        $this->access_only_allowed_members();

        if ($functional_unit_id) {
            $options = array("id" => $functional_unit_id);
            $functional_unit_info = $this->Functional_units_model->get_details($options)->row();
            if ($functional_unit_info) {
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $functional_unit_info;
				
				$cliente = $this->Clients_model->get_one($view_data['model_info']->id_cliente);
				$proyecto = $this->Projects_model->get_one($view_data['model_info']->id_proyecto);
				$subproyecto = $this->Subprojects_model->get_one($view_data['model_info']->id_subproyecto);
				
				$view_data["cliente"] = $cliente->company_name;
				$view_data["proyecto"] = $proyecto->title;
				$view_data["subproyecto"] = $subproyecto->nombre;
				
				$this->load->view('functional_units/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	function get_pdf(){
		
		// $id_unidad_funcional = $this->input->post("id_unidad_funcional");
		$ids_uf = $this->input->post("ids_uf");
		$view_data['ids_uf'] = $ids_uf;

		$start_date = $this->input->post("start_date");
		$end_date = $this->input->post("end_date");
		
		$view_data["start_date"] = $start_date;
		$view_data["end_date"] = $end_date;

		$imagenes_graficos_por_uf = $this->input->post("imagenes_graficos_por_uf");
		$view_data['imagenes_graficos_por_uf'] = $imagenes_graficos_por_uf;
		
		// $imagenes_graficos = $this->input->post("imagenes_graficos");
		// $view_data["grafico_impactos_por_huella"] = $imagenes_graficos["image_impactos_por_huella"];
		// $view_data["grafico_proporcion_mensual"] = $imagenes_graficos["image_proporcion_mensual"];

		// Datos de sección "Impactos Ambientales por..."
		$impacts_by_footprint = json_decode($this->input->post('impacts_by_footprint'), true);
		$view_data['impacts_by_footprint'] = $impacts_by_footprint;
				
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		
		$client_info = $this->Clients_model->get_one($id_cliente);
		$project_info = $this->Projects_model->get_one($id_proyecto);
		
		$subprojects = $this->Subprojects_model->get_details(array("id_proyecto" => $id_proyecto))->result();
		$footprints = $this->Footprints_model->get_footprints_of_methodology(3)->result(); // Metodología con id 3: Huella de Agua
		$footprint_ids = array();
		foreach($footprints as $footprint){
			$footprint_ids[] = $footprint->id;
		}
		$options_footprint_ids = array("footprint_ids" => $footprint_ids);
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto, $options_footprint_ids)->result();

		//$unidades_funcionales = $this->Functional_units_model->get_details(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto))->result();
		//$view_data['unidades_funcionales'] = $unidades_funcionales;

		foreach($ids_uf as $id_uf){
			$unidades_funcionales[$id_uf] = $this->Functional_units_model->get_details(array("id" => $id_uf))->row();
		}
		$view_data['unidades_funcionales'] = $unidades_funcionales;

		$unidad_funcional = $this->Functional_units_model->get_details(array("id" => $id_unidad_funcional))->row();
		$view_data['unidad_funcional'] = $unidad_funcional;

		$view_data['criterios_calculos'] = $this->Unit_processes_model->get_rules_calculations_of_project($client_info->id, $project_info->id)->result();
		$view_data['procesos_unitarios'] = $this->Unit_processes_model->get_pu_of_projects($project_info->id)->result_array();
		
		$view_data['client_info'] = $client_info;
		$view_data['proyecto'] = $project_info;
		$view_data['tecnologia'] = $tecnologia->nombre;
		$view_data['subprojects'] = $subprojects;
		$view_data['huellas'] = $huellas;
		$view_data['client_id'] = $client_info->id;
		$view_data['page_type'] = "dashboard";
		
		$view_data['General_settings_model'] = $this->General_settings_model;
		$view_data['Projects_model'] = $this->Projects_model;
		$view_data['Project_rel_footprints_model'] = $this->Project_rel_footprints_model;
		$view_data['Calculation_model'] = $this->Calculation_model;
		$view_data['Fields_model'] = $this->Fields_model;
		$view_data['Unity_model'] = $this->Unity_model;
		$view_data["Forms_model"] = $this->Forms_model;
		$view_data['Characterization_factors_model'] = $this->Characterization_factors_model;
		$view_data['Form_rel_materiales_rel_categorias_model'] = $this->Form_rel_materiales_rel_categorias_model;
		$view_data['Unit_processes_model'] = $this->Unit_processes_model;
		$view_data['Assignment_model'] = $this->Assignment_model;
		$view_data['Assignment_combinations_model'] = $this->Assignment_combinations_model;
		
		$view_data['Module_footprint_units_model'] = $this->Module_footprint_units_model;
		$view_data['Conversion_model'] = $this->Conversion_model;


		$date_today = get_my_local_time('Y-m-'.'01');
		$date_twelve_ago = new DateTime($date_today);
		$date_twelve_ago->sub(new DateInterval('P11M'));
		$date_twelve_ago = $date_twelve_ago->format('Y-m-').'01';

		$array_categorias_fechas = array();
		$interval = new DateInterval('P1M');
		$realEnd = new DateTime($date_today);
		$realEnd->add($interval);
		$period = new DatePeriod(new DateTime($date_twelve_ago), $interval, $realEnd);
		foreach($period as $date) {
			$array_categorias_fechas[] = lang("short_".strtolower($date->format('F'))).'-'.$date->format('y');
		}

		$view_data["array_categorias_fechas"] = $array_categorias_fechas;

		if($start_date && $end_date){
			$rango_fechas = get_date_format($start_date, $id_proyecto)." - ".get_date_format($end_date, $id_proyecto);
			$view_data["rango_fechas"] = $rango_fechas;
		}
		
		

		
		// create new PDF document
        $this->load->library('Pdf');
		
		// set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Autor');
        $this->pdf->SetTitle($client_info->sigla."_".$project_info->sigla."_".lang("functional_units_pdf")."_".date('Y-m-d'));
        $this->pdf->SetSubject($client_info->sigla."_".$project_info->sigla."_".lang("functional_units_pdf")."_".date('Y-m-d'));
        $this->pdf->SetKeywords('TCPDF, PDF');
		
		//$this->pdf->SetPrintHeader(false);
		//$this->pdf->SetPrintFooter(false);
		// set default header data
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', '', array(0, 64, 255), array(0, 64, 128));
        $this->pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));
		// set header and footer fonts
        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$this->pdf->SetAutoPageBreak(TRUE,PDF_MARGIN_BOTTOM);	
		//relación utilizada para ajustar la conversión de los píxeles
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		// ---------------------------------------------------------
		// set default font subsetting mode
        $this->pdf->setFontSubsetting(true);
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		//$this->SetFont('freemono', '', 14, '', true);        
		
		$this->pdf->AddPage();

		$image_file = get_file_uri('assets/images/mimasoft-logo-fondo.png');
		$this->pdf->Image($image_file, 160, 10, 35, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$html = $this->load->view('functional_units_water/client/pdf_view', $view_data, true);
		
		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf_file_name = $client_info->sigla."_".$project_info->sigla."_".lang("functional_units_pdf")."_".date('Y-m-d').".pdf";
		
		$tmp = get_setting("temp_file_path");
		$this->pdf->Output(getcwd() . '/' . $tmp.$pdf_file_name, "F");
		
		echo $pdf_file_name;
		
	}
	
	function borrar_temporal(){
		$uri = $this->input->post('uri');
		delete_file_from_directory($uri);
	}
	
	// Muestra el mismo contenido que al entrar al módulo pero con los datos filtrados por rango de fechas.
	function get_functional_units(){
		
		/*$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$view_data["start_date"] = $start_date;
		$view_data["end_date"] = $end_date;

		$id_unidad_funcional = $this->input->post('id_unidad_funcional');
		$unidad_funcional = $this->Functional_units_model->get_one($id_unidad_funcional);
		$view_data["unidad_funcional"] = $unidad_funcional;

		$options = array("id" => $this->login_user->client_id);
		$client_info = $this->Clients_model->get_details($options)->row();
		$id_proyecto = $this->session->project_context;
		
		$project_info = $this->Projects_model->get_details(array("id" => $id_proyecto))->row();
		$subprojects = $this->Subprojects_model->get_details(array("id_proyecto" => $id_proyecto))->result();
		$footprints = $this->Footprints_model->get_footprints_of_methodology(3)->result(); // Metodología con id 3: Huella de Agua
		$footprint_ids = array();
		foreach($footprints as $footprint){
			$footprint_ids[] = $footprint->id;
		}
		$options_footprint_ids = array("footprint_ids" => $footprint_ids);
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto, $options_footprint_ids)->result();
		$unidades_funcionales = $this->Functional_units_model->get_details(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto))->result();
		
		$view_data['unidades_funcionales'] = $unidades_funcionales;
		$view_data['criterios_calculos'] = $this->Unit_processes_model->get_rules_calculations_of_project($client_info->id, $project_info->id)->result();
		$view_data['procesos_unitarios'] = $this->Unit_processes_model->get_pu_of_projects($project_info->id)->result_array();
		
		$view_data['client_info'] = $client_info;
		$view_data['proyecto'] = $project_info;
		$view_data['tecnologia'] = $tecnologia->nombre;
		$view_data['subprojects'] = $subprojects;
		$view_data['huellas'] = $huellas;
		$view_data['client_id'] = $client_info->id;
		$view_data['page_type'] = "dashboard";
		
		$view_data['General_settings_model'] = $this->General_settings_model;
		$view_data['Projects_model'] = $this->Projects_model;
		$view_data['Project_rel_footprints_model'] = $this->Project_rel_footprints_model;
		$view_data['Calculation_model'] = $this->Calculation_model;
		$view_data['Fields_model'] = $this->Fields_model;
		$view_data['Unity_model'] = $this->Unity_model;
		$view_data["Forms_model"] = $this->Forms_model;
		$view_data['Characterization_factors_model'] = $this->Characterization_factors_model;
		$view_data['Form_rel_materiales_rel_categorias_model'] = $this->Form_rel_materiales_rel_categorias_model;
		$view_data['Unit_processes_model'] = $this->Unit_processes_model;
		$view_data['Assignment_model'] = $this->Assignment_model;
		$view_data['Assignment_combinations_model'] = $this->Assignment_combinations_model;
		
		$view_data['Module_footprint_units_model'] = $this->Module_footprint_units_model;
		$view_data['Conversion_model'] = $this->Conversion_model;

		// MESES DE ACUERDO A RANGO DE FECHAS
		if($start_date && $end_date){
			$start = new DateTime($start_date);
			$start->modify('first day of this month');
			$end = new DateTime($end_date);
			$end->modify('first day of next month');
			$interval = DateInterval::createFromDateString('1 month');
			$period = new DatePeriod($start, $interval, $end);
		}else{
			$date_today = get_my_local_time('Y-m-'.'01');
            $date_twelve_ago = new DateTime($date_today);
            $date_twelve_ago->sub(new DateInterval('P11M'));
            $date_twelve_ago = $date_twelve_ago->format('Y-m-').'01';

            $interval = new DateInterval('P1M');
            $realEnd = new DateTime($date_today);
            $realEnd->add($interval);
            $period = new DatePeriod(new DateTime($date_twelve_ago), $interval, $realEnd);
		}

		$array_categorias_fechas = array();
		$array_periodos = array();
		foreach($period as $date) {
			$array_categorias_fechas[] = lang("short_".strtolower($date->format('F'))).'-'.$date->format('y');

			$array_periodos[lang("short_".strtolower($date->format('F'))).'-'.$date->format('y')] = array(
				"start_date" => $date->modify('first day of this month')->format('Y-m-d'), 
				"end_date" => $date->modify('last day of this month')->format('Y-m-d')
			);
		}

		$view_data["array_periodos"] = $array_periodos;
		$view_data["array_categorias_fechas"] = $array_categorias_fechas;

		$id_unidad_volumen = $this->Module_footprint_units_model->get_one_where(array(
			"id_cliente" => $client_info->id, 
			"id_proyecto" => $id_proyecto, 
			"id_tipo_unidad" => 2, 
			"deleted" => 0
		))->id_unidad;
		$unidad_volumen = $this->Unity_model->get_one($id_unidad_volumen)->nombre;
		$unidad_volumen_nombre_real = $this->Unity_model->get_one($id_unidad_volumen)->nombre_real;
		$view_data["unidad_volumen"] = $unidad_volumen;
		$view_data["unidad_volumen_nombre_real"] = $unidad_volumen_nombre_real;*/


		// CÁLCULO 2.0
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$view_data["start_date"] = $start_date;
		$view_data["end_date"] = $end_date;

		$id_unidad_funcional = $this->input->post('id_unidad_funcional');
		$unidad_funcional = $this->Functional_units_model->get_one($id_unidad_funcional);
		$view_data["unidad_funcional"] = $unidad_funcional;

		$options = array("id" => $this->login_user->client_id);
		$client_info = $this->Clients_model->get_details($options)->row();

		$id_unidad_volumen = $this->Module_footprint_units_model->get_one_where(array(
			"id_cliente" => $client_info->id, 
			"id_proyecto" => $id_proyecto, 
			"id_tipo_unidad" => 2, 
			"deleted" => 0
		))->id_unidad;
		$unidad_volumen = $this->Unity_model->get_one($id_unidad_volumen)->nombre;
		$unidad_volumen_nombre_real = $this->Unity_model->get_one($id_unidad_volumen)->nombre_real;
		$view_data["unidad_volumen"] = $unidad_volumen;
		$view_data["unidad_volumen_nombre_real"] = $unidad_volumen_nombre_real;

		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		$project_info = $this->Projects_model->get_details(array("id" => $id_proyecto))->row();

		$footprints = $this->Footprints_model->get_footprints_of_methodology(3)->result(); // Metodología con id 3: Huella de Agua
		$footprint_ids = array();
		foreach($footprints as $footprint){
			$footprint_ids[] = $footprint->id;
		}
		$options_footprint_ids = array("footprint_ids" => $footprint_ids);
		
		//$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto, $options_footprint_ids)->result();

		$unidades_funcionales = $this->Functional_units_model->get_details(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto))->result();
		$view_data['unidades_funcionales'] = $unidades_funcionales;

		$dropdown_functional_units = array("" => "-");
		foreach($unidades_funcionales as $uf){
			$dropdown_functional_units[$uf->id] = $uf->nombre;
		}
		$view_data["dropdown_functional_units"] = $dropdown_functional_units;
		//$view_data['criterios_calculos'] = $this->Unit_processes_model->get_rules_calculations_of_project($client_info->id, $project_info->id)->result();
		//$view_data['procesos_unitarios'] = $this->Unit_processes_model->get_pu_of_projects($project_info->id)->result_array();
		
		$view_data['client_info'] = $client_info;
		$view_data['proyecto'] = $project_info;

		$array_factores = array();
		$factores = $this->Calculation_model->get_factores($id_proyecto)->result();
		foreach($factores as $factor) {
			$array_factores[$factor->id_bd][$factor->id_metodologia][$factor->id_huella][$factor->id_material][$factor->id_categoria][$factor->id_subcategoria][$factor->id_unidad] = (float)$factor->factor;
		}

		$array_transformaciones = array();
		$transformaciones = $this->Calculation_model->get_transformaciones($id_proyecto)->result();
		foreach($transformaciones as $transformacion) {
			$array_transformaciones[$transformacion->id] = (float)$transformacion->transformacion;
		}

		//$view_data["huellas"] = $this->Project_rel_footprints_model->get_dropdown_list(array("id_huella"), "id_huella", array("id_proyecto" => 1));
		$view_data["huellas"] = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto, $options_footprint_ids)->result();
		$view_data["sp_uf"] = $this->Functional_units_model->get_dropdown_list(array("id"), "id_subproyecto", array("id_proyecto" => $id_proyecto));
		$view_data["campos_unidad"] = $this->Fields_model->get_dropdown_list(array("opciones"), "id", array("id_proyecto" => $id_proyecto, "id_tipo_campo" => 15));
		$view_data["unidades"] = $this->Unity_model->get_dropdown_list(array("nombre"), 'id');
		$view_data["tipo_tratamiento"] = $this->Tipo_tratamiento_model->get_dropdown_list(array("nombre"), "id", array("deleted" => 0));
		$view_data["type_of_origin_matter"] = $this->EC_Types_of_origin_matter_model->get_dropdown_list(array("nombre"), 'id');
		$view_data["type_of_origin"] = $this->EC_Types_of_origin_model->get_dropdown_list(array("nombre"), 'id');
		$view_data["default_type"] = $this->EC_Types_no_apply_model->get_dropdown_list(array("nombre"), 'id');
		$view_data["array_factores"] = $array_factores;
		$view_data["array_transformaciones"] = $array_transformaciones;

		// MESES DE ACUERDO A RANGO DE FECHAS
		if($start_date && $end_date){
			$start = new DateTime($start_date);
			$start->modify('first day of this month');
			$end = new DateTime($end_date);
			$end->modify('first day of next month');
			$interval = DateInterval::createFromDateString('1 month');
			$period = new DatePeriod($start, $interval, $end);
		}else{
			$date_today = get_my_local_time('Y-m-'.'01');
            $date_twelve_ago = new DateTime($date_today);
            $date_twelve_ago->sub(new DateInterval('P11M'));
            $date_twelve_ago = $date_twelve_ago->format('Y-m-').'01';

            $interval = new DateInterval('P1M');
            $realEnd = new DateTime($date_today);
            $realEnd->add($interval);
            $period = new DatePeriod(new DateTime($date_twelve_ago), $interval, $realEnd);
		}

		$array_meses = array();
		$array_periodos = array();
		foreach($period as $date) {
			$array_meses[] = lang("short_".strtolower($date->format('F'))).'-'.$date->format('y');

			$array_periodos[lang("short_".strtolower($date->format('F'))).'-'.$date->format('y')] = array(
				"start_date" => $date->modify('first day of this month')->format('Y-m-d'), 
				"end_date" => $date->modify('last day of this month')->format('Y-m-d')
			);
		}
		
		$first = count($array_periodos) ? array_keys($array_periodos)[0] : null;
		$last = count($array_periodos) ? array_keys($array_periodos)[count($array_periodos)-1] : null;
		$from = $array_periodos[$first]["start_date"];
		$to = $array_periodos[$last]["end_date"];
		$view_data["array_periodos"] = $array_periodos;

		$nombre_huellas = array();
		// Inicializar arreglo dimensiones UF/HUELLA/MES
		$uf_huella_mes = array();
		foreach($view_data["sp_uf"] as $id_uf){
			foreach($view_data["huellas"] as $huella){
				$nombre_huellas[$huella->id] = $huella->nombre;

				foreach($array_meses as $mes){
					$fecha_inicio_mes = $array_periodos[$mes]['start_date'];
					$fecha_termino_mes = $array_periodos[$mes]['end_date'];
					$valor_uf_mes = get_functional_unit_value($client_info->id, $id_proyecto, $id_uf, $fecha_inicio_mes, $fecha_termino_mes);
					//var_dump(array($client_info->id, $id_proyecto, $id_uf, $fecha_inicio_mes, $fecha_termino_mes));
					$uf_huella_mes[$id_uf][$huella->id][$mes]["valor_mes"] = 0;
					$uf_huella_mes[$id_uf][$huella->id][$mes]["valor_uf"] = $valor_uf_mes;
				}
			}
		}
		$view_data["array_meses"] = $array_meses;
		$view_data["uf_huella_mes"] = $uf_huella_mes;
		if($id_unidad_funcional){
			$uf_sp = $this->Functional_units_model->get_dropdown_list(array("id_subproyecto"), "id", array("id_proyecto" => $id_proyecto));
			$view_data["calculos"] = $this->Calculation_model->get_calculos($id_proyecto, $id_cliente, $uf_sp[$unidad_funcional->id], $from, $to)->result();
		}else{
			$view_data["calculos"] = $this->Calculation_model->get_calculos($id_proyecto, $id_cliente, NULL, $from, $to)->result();
		}
		$view_data["nombre_huellas"] = $nombre_huellas;
		
		if($client_info->habilitado){
			//$this->template->rander("functional_units/client/index", $view_data);
			
			if($unidad_funcional->id){
				echo $this->load->view("functional_units_water/client/functional_unit_by_date2", $view_data, TRUE);
			} else {
				echo $this->load->view("functional_units_water/client/functional_units_by_date2", $view_data, TRUE);
			}
		}else{
			$this->session->sess_destroy();
			redirect('signin/index/disabled');
		}

	}

}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */