<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends MY_Controller {
	
	function __construct() {
        
		parent::__construct();
		$this->load->helper('currency');
		//$this->access_only_allowed_members();
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$id_cliente = $this->login_user->client_id;

		/*if($this->login_user->user_type === "client") {
			$this->block_url_client_context($id_cliente, 14);
		}*/
    }
	
    public function index() {
		
        if ($this->login_user->user_type === "staff") {
            //check which widgets are viewable to current logged in user
			
            $show_timeline = get_setting("module_timeline") ? true : false;
            $show_attendance = get_setting("module_attendance") ? true : false;
            $show_event = get_setting("module_event") ? true : false;
            $show_invoice = get_setting("module_invoice") ? true : false;
            $show_expense = get_setting("module_expense") ? true : false;
            $show_ticket = get_setting("module_ticket") ? true : false;
            $show_project_timesheet = get_setting("module_project_timesheet") ? true : false;

            $view_data["show_timeline"] = $show_timeline;
            $view_data["show_attendance"] = $show_attendance;
            $view_data["show_event"] = $show_event;
            $view_data["show_project_timesheet"] = $show_project_timesheet;

            $access_expense = $this->get_access_info("expense");
            $access_invoice = $this->get_access_info("invoice");

            $access_ticket = $this->get_access_info("ticket");
            $access_timecards = $this->get_access_info("attendance");

            $view_data["show_invoice_statistics"] = false;
            $view_data["show_ticket_status"] = false;
            $view_data["show_income_vs_expenses"] = false;
            $view_data["show_clock_status"] = false;
            
            //check module availability and access permission to show any widget

            if ($show_invoice && $show_expense && $access_expense->access_type === "all" && $access_invoice->access_type === "all") {
                $view_data["show_income_vs_expenses"] = true;
            }

            if ($show_invoice && $access_invoice->access_type === "all") {
                $view_data["show_invoice_statistics"] = true;
            }

            if ($show_ticket && $access_ticket->access_type === "all") {
                $view_data["show_ticket_status"] = true;
            }

            if ($show_attendance && $access_timecards->access_type === "all") {
                $view_data["show_clock_status"] = true;
            }

            $this->template->rander("dashboard/index", $view_data);
        } else {
            //client's dashboard
			if($this->session->project_context){
				redirect('home');
			}else{
				redirect('inicio_projects');
			}
			
            /*$options = array("id" => $this->login_user->client_id);
			$client_info = $this->Clients_model->get_details($options)->row();
			$project_info = $this->Projects_model->get_details(array("id" => $id_proyecto))->row();
			$rubro = $this->Industries_model->get_one($project_info->id_industria);
			$subrubro = $this->Subindustries_model->get_one($project_info->id_tecnologia);
			$subprojects = $this->Subprojects_model->get_details(array("id_proyecto" => $id_proyecto))->result();
			$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto)->result();
			$unidades_funcionales = $this->Functional_units_model->get_details(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto))->result();
			
			$view_data['unidades_funcionales'] = $unidades_funcionales;
			$view_data['procesos_unitarios'] = $this->Unit_processes_model->get_pu_of_projects($project_info->id)->result_array();
			$view_data['criterios_calculos'] = $this->Unit_processes_model->get_rules_calculations_of_project($client_info->id, $project_info->id)->result();
			
			$view_data['id_proyecto'] = $id_proyecto;
			$view_data['client_info'] = $client_info;
			$view_data['proyecto'] = $project_info;
			$view_data['rubro'] = $rubro->nombre;
			$view_data['subrubro'] = $subrubro->nombre;
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
			$view_data['Assignment_model'] = $this->Assignment_model;
			$view_data['Assignment_combinations_model'] = $this->Assignment_combinations_model;
			$view_data['Form_rel_materiales_rel_categorias_model'] = $this->Form_rel_materiales_rel_categorias_model;
			$view_data['Module_footprint_units_model'] = $this->Module_footprint_units_model;
			$view_data['Conversion_model'] = $this->Conversion_model;
			
			$view_data['Client_consumptions_settings_model'] = $this->Client_consumptions_settings_model;
			$view_data['Client_waste_settings_model'] = $this->Client_waste_settings_model;
			$view_data['environmental_footprints_settings'] = $this->Client_environmental_footprints_settings_model->get_all_where(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
			//$view_data['consumptions_settings'] = $this->Client_consumptions_settings_model->get_all_where(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
			//$view_data['waste_settings'] = $this->Client_waste_settings_model->get_all_where(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
			
			
			$view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0));
			
			$view_data["Categories_alias_model"] = $this->Categories_alias_model;
			$this->template->rander("dashboard/client_dashboard", $view_data);
			*/
        }
    }

    public function save_sticky_note() {
        $note_data = array("sticky_note" => $this->input->post("sticky_note"));
        $this->Users_model->save($note_data, $this->login_user->id);
    }
	
	function view($id_proyecto = 0){

		ini_set('memory_limit', '4096M');
		
		$this->member_allowed($id_proyecto);
	
		if($id_proyecto){
			$this->session->set_userdata('project_context', $id_proyecto);
		}
		
		$id_cliente = $this->login_user->client_id;
		$project_info = $this->Projects_model->get_details(array("id" => $id_proyecto))->row();
		//$rubro = $this->Industries_model->get_one($project_info->id_industria);
		//$subrubro = $this->Subindustries_model->get_one($project_info->id_tecnologia);

		// Huella ACV
		$footprints = $this->Footprints_model->get_footprints_of_methodology(1)->result(); // Metodología con id 1: ReCiPe 2008, midpoint (H) [v1.11, December 2014
		$footprint_ids = array();
		foreach($footprints as $footprint){
			$footprint_ids[] = $footprint->id;
		}
		$options_footprint_ids = array("footprint_ids" => $footprint_ids);
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto, $options_footprint_ids)->result();


		// Huella de Carbono
		$footprints_carbon = $this->Footprints_model->get_footprints_of_methodology(2)->result(); // Metodología con id 2: GHG Protocol
		$footprint_ids_carbon = array();
		foreach($footprints_carbon as $footprint_carbon){
			$footprint_ids_carbon[] = $footprint_carbon->id;
		}
		$options_footprint_ids_carbon = array("footprint_ids" => $footprint_ids_carbon);
		$huellas_carbon = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto, $options_footprint_ids_carbon)->result();

		// Huella de Agua
		$footprints_water = $this->Footprints_model->get_footprints_of_methodology(3)->result(); // Metodología con id 2: Huella de Agua
		$footprint_ids_water = array();
		foreach($footprints_water as $footprint_water){
			$footprint_ids_water[] = $footprint_water->id;
		}
		$options_footprint_ids_water = array("footprint_ids" => $footprint_ids_water);
		$huellas_water = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto, $options_footprint_ids_water)->result();

		$unidades_funcionales = $this->Functional_units_model->get_details(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto))->result();
		
		$view_data['id_proyecto'] = $id_proyecto;
		$view_data['rubro'] = $rubro->nombre;
		$view_data['subrubro'] = $subrubro->nombre;
		
		$view_data['unidades_funcionales'] = $unidades_funcionales;
		$view_data['criterios_calculos'] = $this->Unit_processes_model->get_rules_calculations_of_project($id_cliente, $project_info->id)->result();
		$view_data['procesos_unitarios'] = $this->Unit_processes_model->get_pu_of_projects($project_info->id)->result_array();
		$view_data['proyecto'] = $project_info;

		$view_data['huellas'] = $huellas;
		$view_data['huellas_carbon'] = $huellas_carbon;
		$view_data['huellas_water'] = $huellas_water;

		$view_data['client_id'] = $id_cliente;
		
		$view_data['Calculation_model'] = $this->Calculation_model;
		$view_data['Fields_model'] = $this->Fields_model;
		$view_data['Unity_model'] = $this->Unity_model;
		$view_data["Forms_model"] = $this->Forms_model;
		$view_data['Characterization_factors_model'] = $this->Characterization_factors_model;
		$view_data['Form_rel_materiales_rel_categorias_model'] = $this->Form_rel_materiales_rel_categorias_model;
		//$view_data['Unit_processes_model'] = $this->Unit_processes_model;
		//$view_data['Assignment_model'] = $this->Assignment_model;
		$view_data['Assignment_combinations_model'] = $this->Assignment_combinations_model;
		$view_data['Module_footprint_units_model'] = $this->Module_footprint_units_model;
		$view_data['Conversion_model'] = $this->Conversion_model;
		$view_data['Tipo_tratamiento_model'] = $this->Tipo_tratamiento_model;
		
		$view_data['id_unidad_volumen'] = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 2, "deleted" => 0))->id_unidad;
		$view_data['id_unidad_masa'] = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 1, "deleted" => 0))->id_unidad;
		$view_data['id_unidad_energia'] = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 4, "deleted" => 0))->id_unidad;
		
		$view_data['unidad_volumen'] = $this->Unity_model->get_one($view_data['id_unidad_volumen'])->nombre;
		$view_data['unidad_masa'] = $this->Unity_model->get_one($view_data['id_unidad_masa'])->nombre;
		$view_data['unidad_energia'] = $this->Unity_model->get_one($view_data['id_unidad_energia'])->nombre;
		
		$view_data['unidad_volumen_nombre_real'] = $this->Unity_model->get_one($view_data['id_unidad_volumen'])->nombre_real;
		$view_data['unidad_masa_nombre_real'] = $this->Unity_model->get_one($view_data['id_unidad_masa'])->nombre_real;
		$view_data['unidad_energia_nombre_real'] = $this->Unity_model->get_one($view_data['id_unidad_energia'])->nombre_real;
		
		//$view_data['campos_unidad_consumo'] = $this->Fields_model->get_unity_fields_of_ra($client_info->id, $id_proyecto, "Consumo")->result();
		//$view_data['campos_unidad_residuo'] = $this->Fields_model->get_unity_fields_of_ra($client_info->id, $id_proyecto, "Residuo")->result();
		
		$view_data['campos_unidad_consumo'] = $this->Forms_model->get_details(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "flujo" => "Consumo"))->result();
		$view_data['campos_unidad_residuo'] = $this->Forms_model->get_details(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "flujo" => "Residuo"))->result();
		
		$view_data['environmental_footprints_settings'] = $this->Client_environmental_footprints_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
		$view_data['Client_consumptions_settings_model'] = $this->Client_consumptions_settings_model;
		$view_data['Client_waste_settings_model'] = $this->Client_waste_settings_model;
		
		$view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0));
		
		$view_data["Categories_alias_model"] = $this->Categories_alias_model;
		$view_data["Categories_model"] = $this->Categories_model;

		// PARA MOSTRAR LOS RESULTADOS DE HUELLAS SOLO DEL AÑO EN CURSO
		$view_data["first_date_current_year"] = date('Y-01-01');
		$view_data["last_date_current_year"] = date('Y-12-31');
		
	

		// ARREGLO DE LOS AÑOS QUE SE MOSTRARÁN EN LOS GRÁFICOS
		$current_year = date('Y');

		//AÑO ACTUAL + LOS ULTIMOS 2 AÑOS
		$years = range($current_year - 2, $current_year);
		
		$view_data['years'] = $years;
		
		// ARREGLO DE LOS MESES QUE SE MOSTRARÁN EN LOS GRÁFICOS AL HACER CLICK EN UNA COLUMNA
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$view_data['meses'] = $meses;

		// CONSUMO
		// DATOS PARA GRÁFICO Y TABLA CONSUMOS VOLUMEN
		$formularios_flujo_consumo = $view_data['campos_unidad_consumo'];
		$id_unidad_volumen_configuracion = $view_data['id_unidad_volumen'];
		$array_id_materiales_valores_volumen = $this->calculo_valores_por_flujo_material($formularios_flujo_consumo, 2, 'Consumo', $id_unidad_volumen_configuracion, $years, $meses); // tipo_unidad 2 es Volumen
		
		$view_data['array_id_materiales_valores_volumen'] = $array_id_materiales_valores_volumen;

		$array_grafico_consumos_volumen_data = $this->generar_datos_grafico($array_id_materiales_valores_volumen, 'Consumo', $id_cliente, $id_proyecto, $years, $meses);
		
		$view_data['array_grafico_consumos_volumen_data'] = $array_grafico_consumos_volumen_data;
		// FIN DATOS PARA GRÁFICO Y TABLA CONSUMOS VOLUMEN

		// DATOS PARA GRÁFICO Y TABLA CONSUMOS MASA
		$id_unidad_masa_configuracion = $view_data['id_unidad_masa'];
		$array_id_materiales_valores_masa = $this->calculo_valores_por_flujo_material($formularios_flujo_consumo, 1, 'Consumo', $id_unidad_masa_configuracion, $years, $meses); // tipo_unidad 1 es Masa
		
		$view_data['array_id_materiales_valores_masa'] = $array_id_materiales_valores_masa;
		// echo '<pre>'; var_dump($array_id_materiales_valores_masa); exit;

		$array_grafico_consumos_masa_data = $this->generar_datos_grafico($array_id_materiales_valores_masa, 'Consumo', $id_cliente, $id_proyecto, $years, $meses);
		
		// echo '<pre>'; var_dump($array_grafico_consumos_masa_data); exit;
		$view_data['array_grafico_consumos_masa_data'] = $array_grafico_consumos_masa_data;
		
		// if($this->login_user->id == 5){
		// 	echo "<pre>";
		// 	var_dump($view_data['array_grafico_consumos_masa_data']);
		// 	echo "</pre>";
		// 	exit();
		// }
		

		// FIN DATOS PARA GRÁFICO Y TABLA CONSUMOS MASA

		// DATOS PARA GRÁFICO Y TABLA CONSUMOS ENERGÍA
		$id_unidad_energia_configuracion = $view_data['id_unidad_energia'];
		$array_id_materiales_valores_energia = $this->calculo_valores_por_flujo_material($formularios_flujo_consumo, 4, 'Consumo', $id_unidad_energia_configuracion, $years, $meses); // tipo_unidad 4 es Energia
		
		$view_data['array_id_materiales_valores_energia'] = $array_id_materiales_valores_energia;
		// echo '<pre>'; var_dump($array_id_materiales_valores_energia); exit;

		$array_grafico_consumos_energia_data = $this->generar_datos_grafico($array_id_materiales_valores_energia, 'Consumo', $id_cliente, $id_proyecto, $years, $meses);
		
		// echo '<pre>'; var_dump($array_grafico_consumos_energia_data); exit;
		$view_data['array_grafico_consumos_energia_data'] = $array_grafico_consumos_energia_data;
		// FIN DATOS PARA GRÁFICO Y TABLA CONSUMOS ENERGÍA
		// FIN CONSUMO

		
		// RESIDUO
		// DATOS PARA GRÁFICO Y TABLA RESIDUOS VOLUMEN
		$formularios_flujo_residuo = $view_data['campos_unidad_residuo'];
		
		$id_unidad_volumen_configuracion = $view_data['id_unidad_volumen'];
		$array_id_materiales_valores_volumen_residuo = $this->calculo_valores_por_flujo_material($formularios_flujo_residuo, 2, 'Residuo', $id_unidad_volumen_configuracion, $years, $meses); // tipo_unidad 2 es Volumen
		
		// echo '<pre>'; var_dump($array_id_materiales_valores_volumen_residuo); exit;
		$view_data['array_id_materiales_valores_volumen_residuo'] = $array_id_materiales_valores_volumen_residuo;
		
		$array_grafico_residuos_volumen_data = $this->generar_datos_grafico($array_id_materiales_valores_volumen_residuo, 'Residuo', $id_cliente, $id_proyecto, $years, $meses);
		
		// echo '<pre>'; var_dump($array_grafico_residuos_volumen_data);exit;
		$view_data['array_grafico_residuos_volumen_data'] = $array_grafico_residuos_volumen_data;
		// FIN DATOS PARA GRÁFICO Y TABLA RESIDUOS VOLUMEN

		// DATOS PARA GRÁFICO Y TABLA RESIDUOS MASA
		$id_unidad_masa_configuracion = $view_data['id_unidad_masa'];
		$array_id_materiales_valores_masa_residuo = $this->calculo_valores_por_flujo_material($formularios_flujo_residuo, 1, 'Residuo', $id_unidad_masa_configuracion, $years, $meses); // tipo_unidad 1 es Masa
		
		$view_data['array_id_materiales_valores_masa_residuo'] = $array_id_materiales_valores_masa_residuo;
		// echo '<pre>'; var_dump($array_id_materiales_valores_masa_residuo); exit;

		$array_grafico_residuos_masa_data = $this->generar_datos_grafico($array_id_materiales_valores_masa_residuo, 'Residuo', $id_cliente, $id_proyecto, $years, $meses);
		
		// echo '<pre>'; var_dump($array_grafico_residuos_masa_data); exit;
		$view_data['array_grafico_residuos_masa_data'] = $array_grafico_residuos_masa_data;
		// FIN DATOS PARA GRÁFICO Y TABLA RESIDUOS MASA
		// FIN RESIDUO

		// if($this->login_user->id == 5){
		// 	exit();
		// }

		/* PARA CONFIGURACIÓN PANEL PRINCIPAL */

		$array_id_categorias_valores_consumo_masa = array();
		$array_id_categorias_valores_consumo_volumen = array();
		$array_id_categorias_valores_consumo_energia = array();
		foreach($view_data['campos_unidad_consumo'] as $formulario_campo){
			
			$datos_campo = json_decode($formulario_campo->unidad, true);
			$id_tipo_unidad = $datos_campo["tipo_unidad_id"];
			
			if($id_tipo_unidad == 1){ //MASA
				$id_formulario = $formulario_campo->id;
				$categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario))->result();
				foreach($categorias as $cat){
					$array_id_categorias_valores_consumo_masa[$cat->id_categoria] = $cat->id_categoria;
				}
			}
			
			if($id_tipo_unidad == 2){ //VOLUMEN
				$id_formulario = $formulario_campo->id;
				$categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario))->result();
				foreach($categorias as $cat){
					$array_id_categorias_valores_consumo_volumen[$cat->id_categoria] = $cat->id_categoria;
				}
			}
			
			if($id_tipo_unidad == 4){ //ENERGIA
				$id_formulario = $formulario_campo->id;
				$categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario))->result();
				foreach($categorias as $cat){
					$array_id_categorias_valores_consumo_energia[$cat->id_categoria] = $cat->id_categoria;
				}
			}
			
		}
		
		$array_id_categorias_valores_residuo_masa = array();
		$array_id_categorias_valores_residuo_volumen = array();
		foreach($view_data['campos_unidad_residuo'] as $formulario_campo){
			
			$datos_campo = json_decode($formulario_campo->unidad, true);
			$id_tipo_unidad = $datos_campo["tipo_unidad_id"];
			
			if($id_tipo_unidad == 1){ //MASA
				$id_formulario = $formulario_campo->id;
				$categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario))->result();
				foreach($categorias as $cat){
					$array_id_categorias_valores_residuo_masa[$cat->id_categoria] = $cat->id_categoria;
				}
			}
			
			if($id_tipo_unidad == 2){ //VOLUMEN
				$id_formulario = $formulario_campo->id;
				$categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario))->result();
				foreach($categorias as $cat){
					$array_id_categorias_valores_residuo_volumen[$cat->id_categoria] = $cat->id_categoria;
				}
			}
			
		}

		$client_consumption_settings = $this->Client_consumptions_settings_model->get_all_where(array(
			"id_cliente" => $this->login_user->client_id,
			"id_proyecto" => $id_proyecto,
			"deleted" => 0
		))->result_array();
		
		$ocultar_tabla_consumos_volumen = TRUE;	
		foreach($client_consumption_settings as $setting){
			if(in_array($setting["id_categoria"], $array_id_categorias_valores_consumo_volumen)){
				if($setting["tabla"]){
					$ocultar_tabla_consumos_volumen = FALSE;
					break;
				}
			}
		}
		
		$ocultar_grafico_consumos_volumen = TRUE;
		foreach($client_consumption_settings as $setting){
			if(in_array($setting["id_categoria"], $array_id_categorias_valores_consumo_volumen)){
				if($setting["grafico"]){
					$ocultar_grafico_consumos_volumen = FALSE;
					break;
				}
			}			
		}
			
		$ocultar_tabla_consumos_masa = TRUE;
		foreach($client_consumption_settings as $setting){
			if(in_array($setting["id_categoria"], $array_id_categorias_valores_consumo_masa)){
				if($setting["tabla"]){
					$ocultar_tabla_consumos_masa = FALSE;
					break;
				}
			}
		}
				
		$ocultar_grafico_consumos_masa = TRUE;
		foreach($client_consumption_settings as $setting){
			if(in_array($setting["id_categoria"], $array_id_categorias_valores_consumo_masa)){
				if($setting["grafico"]){
					$ocultar_grafico_consumos_masa = FALSE;
					break;
				}
			}			
		}
		
		$ocultar_tabla_consumos_energia = TRUE;
		foreach($client_consumption_settings as $setting){
			if(in_array($setting["id_categoria"], $array_id_categorias_valores_consumo_energia)){
				if($setting["tabla"]){
					$ocultar_tabla_consumos_energia = FALSE;
					break;
				}
			}
		}
				
		$ocultar_grafico_consumos_energia = TRUE;
		foreach($client_consumption_settings as $setting){
			if(in_array($setting["id_categoria"], $array_id_categorias_valores_consumo_energia)){
				if($setting["grafico"]){
					$ocultar_grafico_consumos_energia = FALSE;
					break;
				}
			}			
		}
		
		$client_waste_settings = $this->Client_waste_settings_model->get_all_where(array(
			"id_cliente" => $this->login_user->client_id,
			"id_proyecto" => $id_proyecto,
			"deleted" => 0
		))->result_array();
		
		$ocultar_tabla_residuos_volumen = TRUE;		
		foreach($client_waste_settings as $setting){
			if(in_array($setting["id_categoria"], $array_id_categorias_valores_residuo_volumen)){
				if($setting["tabla"]){
					$ocultar_tabla_residuos_volumen = FALSE;
					break;
				}
			}
		}

		$ocultar_grafico_residuos_volumen = TRUE;		
		foreach($client_waste_settings as $setting){
			if(in_array($setting["id_categoria"], $array_id_categorias_valores_residuo_volumen)){
				if($setting["grafico"]){
					$ocultar_grafico_residuos_volumen = FALSE;
					break;
				}
			}
		}
		
		
		$ocultar_tabla_residuos_masa = TRUE;		
		foreach($client_waste_settings as $setting){
			if(in_array($setting["id_categoria"], $array_id_categorias_valores_residuo_masa)){
				if($setting["tabla"]){
					$ocultar_tabla_residuos_masa = FALSE;
					break;
				}
			}
		}
		
		$ocultar_grafico_residuos_masa = TRUE;		
		foreach($client_waste_settings as $setting){
			if(in_array($setting["id_categoria"], $array_id_categorias_valores_residuo_masa)){
				if($setting["grafico"]){
					$ocultar_grafico_residuos_masa = FALSE;
					break;
				}
			}
		}
		
		$view_data["ocultar_tabla_consumos_volumen"] = $ocultar_tabla_consumos_volumen;
		$view_data["ocultar_grafico_consumos_volumen"] = $ocultar_grafico_consumos_volumen;
		$view_data["ocultar_tabla_consumos_masa"] = $ocultar_tabla_consumos_masa;
		$view_data["ocultar_grafico_consumos_masa"] = $ocultar_grafico_consumos_masa;
		$view_data["ocultar_tabla_consumos_energia"] = $ocultar_tabla_consumos_energia;
		$view_data["ocultar_grafico_consumos_energia"] = $ocultar_grafico_consumos_energia;
		
		$view_data["ocultar_tabla_residuos_volumen"] = $ocultar_tabla_residuos_volumen;
		$view_data["ocultar_grafico_residuos_volumen"] = $ocultar_grafico_residuos_volumen;
		$view_data["ocultar_tabla_residuos_masa"] = $ocultar_tabla_residuos_masa;
		$view_data["ocultar_grafico_residuos_masa"] = $ocultar_grafico_residuos_masa;
		
		// Compromises
		$id_compromiso_rca = $this->Compromises_rca_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
		if($id_compromiso_rca){
			
			// COMPROMISOS AMBIENTALES - RCA

			// EVALUADOS
			$evaluados = $this->Evaluated_rca_compromises_model->get_all_where(
				array(
					"id_compromiso" => $id_compromiso_rca, 
					"deleted" => 0
				)
			)->result();
			
			// ESTADOS RCA
			$estados_cliente = $this->Compromises_compliance_status_model->get_details(
				array(
					"id_cliente" => $id_cliente, 
					"tipo_evaluacion" => "rca",
				)
			)->result();
			
			// ULTIMAS EVALUACIONES
			$ultimas_evaluaciones = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluations_of_project(
				$id_proyecto, 
				NULL
			)->result();
			
			// PROCESAR TABLA
			$array_estados = array();
			$total = 0;
			
			foreach($estados_cliente as $estado) {
				
				$id_estado = $estado->id;
				
				if($estado->categoria == "No Aplica"){
					continue;
				}
				$array_estados[$estado->id] = array(
					"nombre_estado" => $estado->nombre_estado,
					"categoria" => $estado->categoria,
					"color" => $estado->color,
					"evaluaciones" => array(),
					"cantidad_categoria" => 0,
				);
				
				$cant = 0;
				foreach($evaluados as $evaluado) {
					
					$id_evaluado = $evaluado->id;
					
					foreach($ultimas_evaluaciones as $ultima_evaluacion) {
						if(
							$ultima_evaluacion->id_estados_cumplimiento_compromiso == $id_estado && 
							$ultima_evaluacion->id_evaluado == $id_evaluado
						){
							$array_estados_evaluados[$id_estado]["evaluaciones"][] = $ultima_evaluacion;
							$cant++;
						}
					}
				}
				
				$array_estados[$id_estado]["cantidad_categoria"] = $cant;
				$total += $cant;
				
			}
			
			$view_data["total_compromisos_aplicables"] = $total;
			$view_data["total_cantidades_estados_evaluados"] = $array_estados;
			
			//Compromises settings
			$view_data["Client_compromises_settings_model"] = $this->Client_compromises_settings_model;
			//traer perfilamiento del módulo
			$view_data["puede_ver_compromisos"] = $this->profile_access($this->session->user_id, 6, 3, "ver");
			//traer disponibilidad del módulo
			$view_data["disponibilidad_modulo_compromisos"] = $this->Module_availability_model->get_one_where(array("id_cliente" => $this->login_user->client_id, "id_proyecto" => $id_proyecto, "id_modulo_cliente" => 6, "deleted" => 0))->available;
			
		}

		
		// Permittings
		$id_permiso = $this->Permitting_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;	
		if($id_permiso){
			
			// EVALUADOS
			$evaluados = $this->Evaluated_permitting_model->get_all_where(
				array(
					"id_permiso" => $id_permiso, 
					"deleted" => 0
				)
			)->result();
			
			// ESTADOS
			$estados_cliente = $this->Permitting_procedure_status_model->get_details(
				array(
					"id_cliente" => $id_cliente,
				)
			)->result();
			
			// ULTIMAS EVALUACIONES
			$ultimas_evaluaciones = $this->Permitting_procedure_evaluation_model->get_last_evaluations_of_project(
				$id_proyecto, 
				NULL
			)->result();
			
			// PROCESAR TABLA
			$array_estados = array();
			$total = 0;
			
			foreach($estados_cliente as $estado) {
				
				$id_estado = $estado->id;
				
				if($estado->categoria == "No Aplica"){
					continue;
				}
				$array_estados[$estado->id] = array(
					"nombre_estado" => $estado->nombre_estado,
					"categoria" => $estado->categoria,
					"color" => $estado->color,
					"evaluaciones" => array(),
					"cantidad_categoria" => 0,
				);
				
				$cant = 0;
				foreach($evaluados as $evaluado) {
					
					$id_evaluado = $evaluado->id;
					
					foreach($ultimas_evaluaciones as $ultima_evaluacion) {
						if(
							$ultima_evaluacion->id_estados_tramitacion_permisos == $id_estado && 
							$ultima_evaluacion->id_evaluado == $id_evaluado
						){
							$array_estados[$id_estado]["evaluaciones"][] = $ultima_evaluacion;
							$cant++;
						}
					}
				}
				
				$array_estados[$id_estado]["cantidad_categoria"] = $cant;
				$total += $cant;
			}
			
			$view_data["total_permisos_aplicables"] = $total;
			$view_data["total_cantidades_estados_evaluados_permisos"] = $array_estados;

			//Permitting settings 
			$view_data["Client_permitting_settings_model"] = $this->Client_permitting_settings_model;
			//traer perfilamiento del módulo
			$view_data["puede_ver_permisos"] = $this->profile_access($this->session->user_id, 7, 5, "ver");
			//traer disponibilidad del módulo
			$view_data["disponibilidad_modulo_permisos"] = $this->Module_availability_model->get_one_where(array("id_cliente" => $this->login_user->client_id, "id_proyecto" => $id_proyecto, "id_modulo_cliente" => 7, "deleted" => 0))->available;
		}

		/* UNIDADES FUNCIONALES - CÁLCULO 2.0*/
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
		$view_data["huellas"] = $huellas;
		$view_data["huellas_carbon"] = $huellas_carbon;
		$view_data["huellas_water"] = $huellas_water;
		$view_data["sp_uf"] = $this->Functional_units_model->get_dropdown_list(array("id"), "id_subproyecto", array("id_proyecto" => $id_proyecto));
		$view_data["campos_unidad"] = $this->Fields_model->get_dropdown_list(array("opciones"), "id", array("id_proyecto" => $id_proyecto, "id_tipo_campo" => 15));
		$view_data["unidades"] = $this->Unity_model->get_dropdown_list(array("nombre"), 'id');
		$view_data["tipo_tratamiento"] = $this->Tipo_tratamiento_model->get_dropdown_list(array("nombre"), "id", array("deleted" => 0));
		$view_data["type_of_origin_matter"] = $this->EC_Types_of_origin_matter_model->get_dropdown_list(array("nombre"), 'id');
		$view_data["type_of_origin"] = $this->EC_Types_of_origin_model->get_dropdown_list(array("nombre"), 'id');
		$view_data["default_type"] = $this->EC_Types_no_apply_model->get_dropdown_list(array("nombre"), 'id');
		$view_data["array_factores"] = $array_factores;
		$view_data["array_transformaciones"] = $array_transformaciones;
		$view_data["calculos"] = $this->Calculation_model->get_calculos($id_proyecto, $id_cliente, NULL, NULL, NULL)->result();
		$view_data["sucursales"] = $this->Subprojects_model->get_dropdown_list(array("nombre"), "id", array("id_proyecto" => $id_proyecto, "deleted" => 0));
		
		$this->template->rander("dashboard/client_dashboard", $view_data);
	}
	
	/*Valida si usuario tiene permiso*/
	function member_allowed($project_id){
		$user_id = $this->login_user->id;
		$project_rel_member = (array)$this->Project_members_model->get_one_where(array("user_id" =>$user_id ,"project_id" => $project_id,"deleted" => 0));
		if(empty(array_filter($project_rel_member))){
			redirect("forbidden");
		}
	}

	function calculo_valores_por_flujo_material(array $formularios_por_flujo, int $tipo_unidad, string $flujo, int $id_unidad_configuración, array $years, array $meses){

		$array_id_materiales_valores = array();	
	
		//ITERO ARREGLO CON FORMULARIOS DE FLUJO
		foreach($formularios_por_flujo as $formulario){
			
			$datos_campo_unidad = json_decode($formulario->unidad, true);
			$id_tipo_unidad = $datos_campo_unidad["tipo_unidad_id"];
			$id_unidad = $datos_campo_unidad["unidad_id"];
					
			
			// MANTENGO FORMULARIOS CON $ID_TIPO_UNIDAD = A $TIPO_UNIDAD (EJ: TIPO_UNIDAD VOLUMEN)
			if($id_tipo_unidad == $tipo_unidad/* && $id_unidad == $id_unidad_configuración*/){
				
				$id_formulario = $formulario->id;
				$materiales_rel_categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario, "deleted" => 0))->result();
				
				// POR CADA CATEGORIA DEL FORMULARIO
				foreach($materiales_rel_categorias as $mat_rel_cat){
	
					// INICIALIZO EL VALOR PARA CADA CATEGORÍA, AÑO, MES NECESARIO
					foreach($years as $year){
						foreach($meses as $index => $mes){
							$array_id_materiales_valores[$mat_rel_cat->id_material][$mat_rel_cat->id_categoria][$year][$index] = 0;
						}
					}
	
					// SE OBTIENEN TODOS LOS VALORES_FORMULARIO DE CADA CATEGORIA ASOCIADA AL FORMULARIO
					$elementos_form = $this->Calculation_model->get_records_of_category_of_form($mat_rel_cat->id_categoria, $mat_rel_cat->id_formulario, $flujo)->result();
					
					
					// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
					foreach($elementos_form as $ef){
						// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
						if($id_unidad == $id_unidad_configuración){
												   
							$datos_decoded = json_decode($ef->datos, true);
	
							$fecha_almacenamiento_timestamp = strtotime($datos_decoded['fecha']);
							$agno = date('Y', $fecha_almacenamiento_timestamp);
							$mes = date('n', $fecha_almacenamiento_timestamp); //se obtiene el numero del mes entre 1 y 12
							$mes -= 1; // se le resta 1 para que sea equivalente a los indices del arreglo $meses
							
							// SE GUARDA EL VALOR INGRESADO EN EL CAMPO UNIDAD
							$valor = $datos_decoded["unidad_residuo"];
							$array_id_materiales_valores[$mat_rel_cat->id_material][$mat_rel_cat->id_categoria][$agno][$mes] += $valor;
							
							
						}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
							$fila_conversion = $this->Conversion_model->get_one_where(
								array(
									"id_tipo_unidad" => $id_tipo_unidad, // Por ej: 2 (VOLUMEN)
									"id_unidad_origen" => $id_unidad,	// Ej: Kg
									"id_unidad_destino" => $id_unidad_configuración	// Ej: Ton
								)
							);
							$valor_transformacion = $fila_conversion->transformacion;
							
							$datos_decoded = json_decode($ef->datos, true);
							
							$fecha_almacenamiento_timestamp = strtotime($datos_decoded['fecha']);
							$agno = date('Y', $fecha_almacenamiento_timestamp);
							$mes = date('n', $fecha_almacenamiento_timestamp); //se obtiene el numero del mes entre 1 y 12
							$mes -= 1; // se le resta 1 para que sea equivalente a los indices del arreglo $meses
							
							$valor = $datos_decoded["unidad_residuo"];

                            $valor = (float)$valor;
                            $valor_transformacion = (float)$valor_transformacion;

                            $array_id_materiales_valores[$mat_rel_cat->id_material][$mat_rel_cat->id_categoria][$agno][$mes] += $valor * $valor_transformacion;
							
						}
					}
				}
			}
		}

		return $array_id_materiales_valores;
	}

	function generar_datos_grafico(array $array_id_materiales_valores_por_flujo, string $flujo, int $id_cliente, int $id_proyecto, array $years, array $meses){
    
		// SERIE DE DATOS PARA EL GRÁFICO
		$series = array();
		$array_grafico_flujo_data = array();

		foreach($years as $year){

			$serie = array(
				'name' => $year,
				'data' => array(),
				// 'stack' => $year
			);

			foreach ($array_id_materiales_valores_por_flujo as $id_material => $array_id_categorias_valores_por_flujo){

				$valor_categorias_material_por_agno = 0;
				foreach ($array_id_categorias_valores_por_flujo as $id_categoria => $arreglo_valores_by_year){

					// OBTENGO LA CONFIGURACIÓN PARA VERIFICAR SI DEBE MOSTRARSE LA CATEGORÍA EN EL GRÁFICO
					if($flujo == 'Consumo'){
						$row_categoria = $this->Client_consumptions_settings_model->get_one_where(array('id_cliente' => $id_cliente, 'id_proyecto' => $id_proyecto, 'id_categoria' => $id_categoria, 'deleted' => 0));
					}elseif($flujo == 'Residuo'){
						$row_categoria = $this->Client_waste_settings_model->get_one_where(array('id_cliente' => $id_cliente, 'id_proyecto' => $id_proyecto, 'id_categoria' => $id_categoria, 'deleted' => 0));
					}

					if($row_categoria->grafico){
						$valor_categoria_por_agno = array_sum($arreglo_valores_by_year[$year]);
						$valor_categorias_material_por_agno += $valor_categoria_por_agno;
					}
				}

				$nombre_material = $this->Materials_model->get_one($id_material)->nombre;

				$serie['data'][] = array(
					'name' => $nombre_material,
					'y' => $valor_categorias_material_por_agno,
					'drilldown' => 'id_drilldown_material_'.$id_material.'_'.$year
				);

			}
			$series[] = $serie;

		}
		$array_grafico_flujo_data['series'] = $series;
		// FIN SERIE DE DATOS PARA EL GRÁFICO

		// DATOS PARA DRILLDOWN (NIVEL 2 - CATEGORÍAS DE MATERIAL)
		$drilldown_series = array();
		foreach($years as $year){

			foreach ($array_id_materiales_valores_por_flujo as $id_material => $array_id_categorias_valores_por_flujo){

				$nombre_material = $this->Materials_model->get_one($id_material)->nombre;

				$serie = array(
					'id' => 'id_drilldown_material_'.$id_material.'_'.$year,
					'name' => $nombre_material.' '.$year,
					'data' => array()
				);

				foreach ($array_id_categorias_valores_por_flujo as $id_categoria => $arreglo_valores_by_year){
					
					// OBTENCION DEL ALIAS O NOMBRE DE LA CATEGORÍA
					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
					if($row_alias->alias){
						$nombre_categoria = $row_alias->alias;
					}else{
						$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
						$nombre_categoria = $row_categoria->nombre;
					}

					// OBTENGO LA CONFIGURACIÓN PARA VERIFICAR SI DEBE MOSTRARSE LA CATEGORÍA EN EL GRÁFICO
					if($flujo == 'Consumo'){
						$row_categoria = $this->Client_consumptions_settings_model->get_one_where(array('id_cliente' => $id_cliente, 'id_proyecto' => $id_proyecto, 'id_categoria' => $id_categoria, 'deleted' => 0));
					}elseif($flujo == 'Residuo'){
						$row_categoria = $this->Client_waste_settings_model->get_one_where(array('id_cliente' => $id_cliente, 'id_proyecto' => $id_proyecto, 'id_categoria' => $id_categoria, 'deleted' => 0));
					}
					
					if($row_categoria->grafico){
						$valor_categoria_por_agno = array_sum($arreglo_valores_by_year[$year]);

						$valor_mes = $arreglo_valores_by_year[$year][$index];
						// $serie['data'][] = array($nombre_categoria, $valor_categoria_por_agno);
						$serie['data'][] = array(
							'name' => $nombre_categoria,
							'y' => $valor_categoria_por_agno,
							'drilldown' => 'id_drilldown_categoria_'.$id_categoria.'_'.$year
						);
			
						$drilldown_series[] = $serie;

					}
				}
			}

		}

		// DATOS PARA DRILLDOWN (NIVEL 3 - VALORES MENSUALES POR CATEGORÍA)
		foreach($years as $year){

			foreach ($array_id_materiales_valores_por_flujo as $id_material => $array_id_categorias_valores_por_flujo){
				foreach ($array_id_categorias_valores_por_flujo as $id_categoria => $arreglo_valores_by_year){
					
					// OBTENCION DEL ALIAS O NOMBRE DE LA CATEGORÍA
					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
					if($row_alias->alias){
						$nombre_categoria = $row_alias->alias;
					}else{
						$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
						$nombre_categoria = $row_categoria->nombre;
					}

					// OBTENGO LA CONFIGURACIÓN PARA VERIFICAR SI DEBE MOSTRARSE LA CATEGORÍA EN EL GRÁFICO
					if($flujo == 'Consumo'){
						$row_categoria = $this->Client_consumptions_settings_model->get_one_where(array('id_cliente' => $id_cliente, 'id_proyecto' => $id_proyecto, 'id_categoria' => $id_categoria, 'deleted' => 0));
					}elseif($flujo == 'Residuo'){
						$row_categoria = $this->Client_waste_settings_model->get_one_where(array('id_cliente' => $id_cliente, 'id_proyecto' => $id_proyecto, 'id_categoria' => $id_categoria, 'deleted' => 0));
					}
					
					if($row_categoria->grafico){
			
						$serie = array(
							'id' => 'id_drilldown_categoria_'.$id_categoria.'_'.$year,
							'name' => $nombre_categoria.' '.$year,
							'data' => array()
						);
			
						foreach($meses as $index => $mes){
							$valor_mes = $arreglo_valores_by_year[$year][$index];
							$serie['data'][] = array($mes, $valor_mes);
						}
						$drilldown_series[] = $serie;
					}
		
				}
			}
		}
		
		$array_grafico_flujo_data['drilldown'] = $drilldown_series;
		// FIN DATOS PARA DRILLDOWN	

		return $array_grafico_flujo_data;
	}

}

/* End of file dashboard.php */
/* Location: ./application/controllers/dashboard.php */