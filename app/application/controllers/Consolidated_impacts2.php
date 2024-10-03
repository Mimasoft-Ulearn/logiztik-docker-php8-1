<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Consolidated_impacts2 extends MY_Controller {
	
	private $id_client_context_module;
	private $id_client_context_submodule;
	
	function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");

		$this->id_client_context_module = 10;
		$this->id_client_context_submodule = 0;
		
		$id_cliente = $this->login_user->client_id;
		$this->block_url_client_context($id_cliente, $this->id_client_context_module);
		
    }

    public function index() {

		ini_set('memory_limit', '4096M');

		$this->session->set_userdata('menu_consolidated_impacts_active', TRUE);
		$this->session->set_userdata('menu_kpi_active', NULL);
		$this->session->set_userdata('menu_project_active', NULL);
		$this->session->set_userdata('client_area', NULL);
		$this->session->set_userdata('project_context', NULL);
		$this->session->set_userdata('menu_agreements_active', NULL);	
		$this->session->set_userdata('menu_help_and_support_active', NULL);
		$this->session->set_userdata('menu_recordbook_active', NULL);
		$this->session->set_userdata('menu_ec_active', NULL);
		
        $view_data = array();
		$array_data_by_project = array();

		$id_cliente = $this->login_user->client_id;
		$view_data['client_id'] = $id_cliente;
		$projects = $this->Projects_model->get_details(array("client_id" => $id_cliente))->result();
		$view_data['projects'] = $projects;

		// PARA MOSTRAR LOS RESULTADOS DE HUELLAS SOLO DEL AÑO EN CURSO
		// $view_data["first_date_current_year"] = date('Y-01-01');
		// $view_data["last_date_current_year"] = date('Y-12-31');
		$view_data["first_date_current_year"] = date('2022-01-01');
		$view_data["last_date_current_year"] = date('2022-12-31');

		// ARREGLO DE LOS AÑOS QUE SE MOSTRARÁN EN LOS GRÁFICOS
		$years = array('2022', '2023');
		$view_data['years'] = $years;

		// ARREGLO DE LOS MESES QUE SE MOSTRARÁN EN LOS GRÁFICOS AL HACER CLICK EN UNA COLUMNA
		// $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

		// ARREGLO DE PROYECTOS QUE SE MOSTRARÁN EN LOS GRÁFICOS AL HACER CLICK EN UNA COLUMNA DE CATEGORÍA (TERCER NIVEL)
		$array_projects = array();
		$projects = $this->Projects_model->get_all_where(array("client_id" => $id_cliente, "deleted" => 0))->result();
		foreach($projects as $project){
			$array_projects[$project->id] = $project->title;
		}
		$view_data['array_projects'] = $array_projects;

		// ARREGLO DE SUBPROYECTOS QUE SE MOSTRARÁN EN LOS GRÁFICOS AL HACER CLICK EN UNA COLUMNA DE PROYECTO (CUARTO NIVEL)
		$array_subprojects = array();
		$subprojects = $this->Subprojects_model->get_all_where(array("id_cliente" => $id_cliente, "deleted" => 0))->result();
		foreach($subprojects as $subproject){
			$array_subprojects[$subproject->id] = $subproject->nombre;
		}
		$view_data['array_subprojects'] = $array_subprojects;

		$view_data["unidades"] = $this->Unity_model->get_dropdown_list(array("nombre"), 'id');
		$view_data["tipo_tratamiento"] = $this->Tipo_tratamiento_model->get_dropdown_list(array("nombre"), "id", array("deleted" => 0));
		$view_data["type_of_origin_matter"] = $this->EC_Types_of_origin_matter_model->get_dropdown_list(array("nombre"), 'id');
		$view_data["type_of_origin"] = $this->EC_Types_of_origin_model->get_dropdown_list(array("nombre"), 'id');
		$view_data["default_type"] = $this->EC_Types_no_apply_model->get_dropdown_list(array("nombre"), 'id');

		$id_unidad_volumen_cliente = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $id_cliente, "id_tipo_unidad" => 2, "deleted" => 0))->id_unidad;
		$unidad_volumen_cliente = $this->Unity_model->get_one($id_unidad_volumen_cliente);

		$id_unidad_masa_cliente = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $id_cliente, "id_tipo_unidad" => 1, "deleted" => 0))->id_unidad;
		$unidad_masa_cliente = $this->Unity_model->get_one($id_unidad_masa_cliente);

		$id_unidad_energia_cliente = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $id_cliente, "id_tipo_unidad" => 4, "deleted" => 0))->id_unidad;
		$unidad_energia_cliente = $this->Unity_model->get_one($id_unidad_energia_cliente);

		$view_data['unidad_volumen'] = $unidad_volumen_cliente->nombre;
		$view_data['unidad_volumen_nombre_real'] = $unidad_volumen_cliente->nombre_real;
		$view_data['unidad_masa'] = $unidad_masa_cliente->nombre;
		$view_data['unidad_masa_nombre_real'] = $unidad_masa_cliente->nombre_real;
		$view_data['unidad_energia'] = $unidad_energia_cliente->nombre;
		$view_data['unidad_energia_nombre_real'] = $unidad_energia_cliente->nombre_real;


		// Huella ACV
		$footprints = $this->Footprints_model->get_footprints_of_methodology(1)->result(); // Metodología con id 1: ReCiPe 2008, midpoint (H) [v1.11, December 2014
		$footprint_ids = array();
		foreach($footprints as $footprint){
			$footprint_ids[] = $footprint->id;
		}
		$options_footprint_ids = array("footprint_ids" => $footprint_ids);

		// Huella de Carbono
		$footprints_carbon = $this->Footprints_model->get_footprints_of_methodology(2)->result(); // Metodología con id 2: GHG Protocol
		$footprint_ids_carbon = array();
		foreach($footprints_carbon as $footprint_carbon){
			$footprint_ids_carbon[] = $footprint_carbon->id;
		}
		$options_footprint_ids_carbon = array("footprint_ids" => $footprint_ids_carbon);

		// Huella de Agua
		$footprints_water = $this->Footprints_model->get_footprints_of_methodology(3)->result(); // Metodología con id 2: Huella de Agua
		$footprint_ids_water = array();
		foreach($footprints_water as $footprint_water){
			$footprint_ids_water[] = $footprint_water->id;
		}
		$options_footprint_ids_water = array("footprint_ids" => $footprint_ids_water);
		
		foreach($projects as $project){

			$array_data_by_project[$project->id]['proyecto'] = $project;
			$array_data_by_project[$project->id]['environmental_footprints_settings'] = $this->Client_environmental_footprints_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $project->id, "deleted" => 0))->result();
			$array_data_by_project[$project->id]['huellas'] = $this->Project_rel_footprints_model->get_footprints_of_project($project->id, $options_footprint_ids)->result();
			$array_data_by_project[$project->id]['huellas_carbon'] = $this->Project_rel_footprints_model->get_footprints_of_project($project->id, $options_footprint_ids_carbon)->result();
			$array_data_by_project[$project->id]['huellas_water'] = $this->Project_rel_footprints_model->get_footprints_of_project($project->id, $options_footprint_ids_water)->result();
			$array_data_by_project[$project->id]['unidades_funcionales'] = $this->Functional_units_model->get_details(array("id_cliente" => $id_cliente, "id_proyecto" => $project->id))->result();
			$array_data_by_project[$project->id]['criterios_calculos'] = $this->Unit_processes_model->get_rules_calculations_of_project($id_cliente, $project->id)->result();

			/* UNIDADES FUNCIONALES - CÁLCULO 2.0*/
			$array_factores = array();
			$factores = $this->Calculation_model->get_factores($project->id)->result();
			foreach($factores as $factor) {
				$array_factores[$factor->id_bd][$factor->id_metodologia][$factor->id_huella][$factor->id_material][$factor->id_categoria][$factor->id_subcategoria][$factor->id_unidad] = (float)$factor->factor;
			}

			$array_transformaciones = array();
			$transformaciones = $this->Calculation_model->get_transformaciones($project->id)->result();
			foreach($transformaciones as $transformacion) {
				$array_transformaciones[$transformacion->id] = (float)$transformacion->transformacion;
			}

			$array_data_by_project[$project->id]["array_factores"] = $array_factores;
			$array_data_by_project[$project->id]["array_transformaciones"] = $array_transformaciones;

			$array_data_by_project[$project->id]["sp_uf"] = $this->Functional_units_model->get_dropdown_list(array("id"), "id_subproyecto", array("id_proyecto" => $project->id));
			$array_data_by_project[$project->id]["campos_unidad"] = $this->Fields_model->get_dropdown_list(array("opciones"), "id", array("id_proyecto" => $project->id, "id_tipo_campo" => 15));
			
			$array_data_by_project[$project->id]["calculos"] = $this->Calculation_model->get_calculos($project->id, $id_cliente, NULL, NULL, NULL)->result();
			$array_data_by_project[$project->id]["sucursales"] = $this->Subprojects_model->get_dropdown_list(array("nombre"), "id", array("id_proyecto" => $project->id, "deleted" => 0));

		}

		$view_data["array_data_by_project"] = $array_data_by_project;


		// GRÁFICOS Y TABLAS DE CONSUMO Y RESIDUO
		$campos_unidad_consumo_cliente = $this->Forms_model->get_details(array("id_cliente" => $id_cliente, "flujo" => "Consumo"))->result();
		$campos_unidad_residuo_cliente = $this->Forms_model->get_details(array("id_cliente" => $id_cliente, "flujo" => "Residuo"))->result();

		// CONSUMO
		$formularios_flujo_consumo = $campos_unidad_consumo_cliente;

		// DATOS PARA GRÁFICO Y TABLA CONSUMOS VOLUMEN
		$id_unidad_volumen_configuracion = $id_unidad_volumen_cliente;
		$array_id_materiales_valores_volumen = $this->calculo_valores_por_flujo_material($formularios_flujo_consumo, 2, 'Consumo', $id_unidad_volumen_configuracion, $years, $array_subprojects); // tipo_unidad 2 es Volumen
		
		$view_data['array_id_materiales_valores_volumen'] = $array_id_materiales_valores_volumen;
		$view_data['array_grafico_consumos_volumen_data'] = $this->generar_datos_grafico($array_id_materiales_valores_volumen, 'Consumo', $id_cliente, $years);

		// DATOS PARA GRÁFICO Y TABLA CONSUMOS MASA
		$id_unidad_masa_configuracion = $id_unidad_masa_cliente;
		$array_id_materiales_valores_masa = $this->calculo_valores_por_flujo_material($formularios_flujo_consumo, 1, 'Consumo', $id_unidad_masa_configuracion, $years, $array_subprojects); // tipo_unidad 1 es Masa
		$view_data['array_id_materiales_valores_masa'] = $array_id_materiales_valores_masa;
		$view_data['array_grafico_consumos_masa_data'] = $this->generar_datos_grafico($array_id_materiales_valores_masa,'Consumo', $id_cliente, $years);
		
		// echo "<pre>";
		// print_r($view_data['array_grafico_consumos_masa_data']);
		// exit();
		
		// FIN DATOS PARA GRÁFICO Y TABLA CONSUMOS MASA

		// DATOS PARA GRÁFICO Y TABLA CONSUMOS ENERGÍA
		$id_unidad_energia_configuracion = $id_unidad_energia_cliente;
		$array_id_materiales_valores_energia = $this->calculo_valores_por_flujo_material($formularios_flujo_consumo, 4, 'Consumo', $id_unidad_energia_configuracion, $years, $array_subprojects); // tipo_unidad 4 es Energia
		$view_data['array_id_materiales_valores_energia'] = $array_id_materiales_valores_energia;
		$view_data['array_grafico_consumos_energia_data'] = $this->generar_datos_grafico($array_id_materiales_valores_energia, 'Consumo', $id_cliente, $years);
		// FIN DATOS PARA GRÁFICO Y TABLA CONSUMOS ENERGÍA
		// FIN CONSUMO


		// RESIDUO
		$formularios_flujo_residuo = $campos_unidad_residuo_cliente;
		
		// DATOS PARA GRÁFICO Y TABLA RESIDUOS VOLUMEN
		$id_unidad_volumen_configuracion = $id_unidad_volumen_cliente;
		$array_id_materiales_valores_volumen_residuo = $this->calculo_valores_por_flujo_material($formularios_flujo_residuo, 2, 'Residuo', $id_unidad_volumen_configuracion, $years, $array_subprojects); // tipo_unidad 2 es Volumen
		$view_data['array_id_materiales_valores_volumen_residuo'] = $array_id_materiales_valores_volumen_residuo;
		$view_data['array_grafico_residuos_volumen_data'] = $this->generar_datos_grafico($array_id_materiales_valores_volumen_residuo, 'Residuo', $id_cliente, $years);
		// FIN DATOS PARA GRÁFICO Y TABLA RESIDUOS VOLUMEN

		// DATOS PARA GRÁFICO Y TABLA RESIDUOS MASA
		$id_unidad_masa_configuracion = $id_unidad_masa_cliente;
		$array_id_materiales_valores_masa_residuo = $this->calculo_valores_por_flujo_material($formularios_flujo_residuo, 1, 'Residuo', $id_unidad_masa_configuracion, $years, $array_subprojects); // tipo_unidad 1 es Masa
		$view_data['array_id_materiales_valores_masa_residuo'] = $array_id_materiales_valores_masa_residuo;
		$view_data['array_grafico_residuos_masa_data'] = $this->generar_datos_grafico($array_id_materiales_valores_masa_residuo, 'Residuo', $id_cliente, $years);
		// FIN DATOS PARA GRÁFICO Y TABLA RESIDUOS MASA
		// FIN RESIDUO

		$this->template->rander("consolidated_impacts2/client_dashboard", $view_data);

    }


	function calculo_valores_por_flujo_material(array $formularios_por_flujo, int $tipo_unidad, string $flujo, int $id_unidad_configuración, array $years, array $array_subprojects){

		$array_id_materiales_valores = array();	

		//ITERO ARREGLO CON FORMULARIOS DE FLUJO
		foreach($formularios_por_flujo as $formulario){ // $formulario->id_proyecto

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
						foreach($array_subprojects as $id_subproject => $name_subproject){
							$array_id_materiales_valores[$formulario->id_proyecto][$id_subproject][$mat_rel_cat->id_material][$mat_rel_cat->id_categoria][$year] = 0;
						}
					}
				}
			
			}
			
		}

	
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
	
					// SE OBTIENEN TODOS LOS VALORES_FORMULARIO DE CADA CATEGORIA ASOCIADA AL FORMULARIO
					$elementos_form = $this->Calculation_model->get_records_of_category_of_form($mat_rel_cat->id_categoria, $mat_rel_cat->id_formulario, $flujo)->result();
		
					// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
					foreach($elementos_form as $ef){
						// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
						if($id_unidad == $id_unidad_configuración){
												   
							$datos_decoded = json_decode($ef->datos, true);
	
							$fecha_almacenamiento_timestamp = strtotime($datos_decoded['fecha']);
							$agno = date('Y', $fecha_almacenamiento_timestamp);
							// $mes = date('n', $fecha_almacenamiento_timestamp); //se obtiene el numero del mes entre 1 y 12
							// $mes -= 1; // se le resta 1 para que sea equivalente a los indices del arreglo $meses

							$id_sucursal = $datos_decoded['id_sucursal'];
							
							// SE GUARDA EL VALOR INGRESADO EN EL CAMPO UNIDAD
							$valor = $datos_decoded["unidad_residuo"];
							$array_id_materiales_valores[$formulario->id_proyecto][$id_sucursal][$mat_rel_cat->id_material][$mat_rel_cat->id_categoria][$agno] += $valor;
							
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
							// $mes = date('n', $fecha_almacenamiento_timestamp); //se obtiene el numero del mes entre 1 y 12
							// $mes -= 1; // se le resta 1 para que sea equivalente a los indices del arreglo $meses
							
							$id_sucursal = $datos_decoded['id_sucursal'];

							$valor = $datos_decoded["unidad_residuo"];

							$array_id_materiales_valores[$formulario->id_proyecto][$id_sucursal][$mat_rel_cat->id_material][$mat_rel_cat->id_categoria][$agno] += $valor * $valor_transformacion;
						}
					}
				}
			}
		}
		
		return $array_id_materiales_valores;

	}

	function generar_datos_grafico(array $array_id_proyectos_valores, string $flujo, int $id_cliente, array $years){
    
		// SERIE DE DATOS PARA EL GRÁFICO
		$series = array();
		$array_grafico_flujo_data = array();

		foreach($years as $year){
			$serie = array(
				'name' => $year,
				'data' => array(),
				// 'stack' => $year
			);
			$array_series_material = array();
			foreach ($array_id_proyectos_valores as $id_proyecto => $array_id_subproyectos_valores){
				foreach($array_id_subproyectos_valores as $id_subproyecto => $array_id_materiales_valores){
					foreach ($array_id_materiales_valores as $id_material => $array_id_categorias_valores){
						foreach ($array_id_categorias_valores as $id_categoria => $arreglo_valores_by_year){
							$array_series_material[$id_material] += $arreglo_valores_by_year[$year];
						}
					}
				}
			}
			foreach($array_series_material as $id_material => $value){
				$nombre_material = $this->Materials_model->get_one($id_material)->nombre;
				$serie['data'][] = array(
					'name' => $nombre_material,
					'y' => $value,
					'drilldown' => 'id_drilldown_material_'.$id_material.'_'.$year
				);
			}
			$series[] = $serie;
		}
		$array_grafico_flujo_data['series'] = $series;
		// FIN SERIE DE DATOS PARA EL GRÁFICO



		$drilldown_series = array();

		// DATOS PARA DRILLDOWN (NIVEL 2 - CATEGORÍAS DE MATERIAL)
		foreach($years as $year){
			$array_series_categoria = array();
			foreach ($array_id_proyectos_valores as $id_proyecto => $array_id_subproyectos_valores){
				foreach($array_id_subproyectos_valores as $id_subproyecto => $array_id_materiales_valores){
					foreach ($array_id_materiales_valores as $id_material => $array_id_categorias_valores){
						foreach ($array_id_categorias_valores as $id_categoria => $arreglo_valores_by_year){
							$array_series_categoria[$id_material][$id_categoria] += $arreglo_valores_by_year[$year];
						}
					}
				}
			}
			foreach($array_series_categoria as $id_material => $valores_categoria){
				$nombre_material = $this->Materials_model->get_one($id_material)->nombre;
				$serie = array(
					'id' => 'id_drilldown_material_'.$id_material.'_'.$year,
					'name' => $nombre_material.' '.$year,
					'data' => array()
				);
				foreach($valores_categoria as $id_categoria => $value){
					// OBTENCION DEL ALIAS O NOMBRE DE LA CATEGORÍA
					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
					if($row_alias->alias){
						$nombre_categoria = $row_alias->alias;
					}else{
						$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
						$nombre_categoria = $row_categoria->nombre;
					}
					$serie['data'][] = array(
						'name' => $nombre_categoria,
						'y' => $value,
						'drilldown' => 'id_drilldown_categoria_'.$id_categoria.'_'.$year
					);
					$drilldown_series[] = $serie;
				}
			}
		}


		// DATOS PARA DRILLDOWN (NIVEL 3 - PROYECTOS)
		foreach($years as $year){
			$array_series_proyecto = array();
			foreach ($array_id_proyectos_valores as $id_proyecto => $array_id_subproyectos_valores){
				foreach($array_id_subproyectos_valores as $id_subproyecto => $array_id_materiales_valores){
					foreach ($array_id_materiales_valores as $id_material => $array_id_categorias_valores){
						foreach ($array_id_categorias_valores as $id_categoria => $arreglo_valores_by_year){
							$array_series_proyecto[$id_material][$id_categoria][$id_proyecto] += $arreglo_valores_by_year[$year];
						}
					}
				}
			}
			foreach($array_series_proyecto as $id_material => $valores_categoria){
				foreach($valores_categoria as $id_categoria => $valores_proyecto){
					// OBTENCION DEL ALIAS O NOMBRE DE LA CATEGORÍA
					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
					if($row_alias->alias){
						$nombre_categoria = $row_alias->alias;
					}else{
						$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
						$nombre_categoria = $row_categoria->nombre;
					}
					$serie = array(
						'id' => 'id_drilldown_categoria_'.$id_categoria.'_'.$year,
						'name' => $nombre_categoria.' '.$year,
						'data' => array()
					);
					foreach($valores_proyecto as $id_proyecto => $value){
						$nombre_proyecto = $this->Projects_model->get_one($id_proyecto)->title;
						$serie['data'][] = array(
							'name' => $nombre_proyecto,
							'y' => $value,
							'drilldown' => 'id_drilldown_project_'.$id_material.'_'.$id_categoria.'_'.$id_proyecto.'_'.$year
						);
						$drilldown_series[] = $serie;
					}
				}
			}
		}


		// DATOS PARA DRILLDOWN (NIVEL 4 - SUBPROYECTOS)
		foreach($years as $year){
			$array_series_subproyecto = array();
			foreach ($array_id_proyectos_valores as $id_proyecto => $array_id_subproyectos_valores){
				foreach($array_id_subproyectos_valores as $id_subproyecto => $array_id_materiales_valores){
					foreach ($array_id_materiales_valores as $id_material => $array_id_categorias_valores){
						foreach ($array_id_categorias_valores as $id_categoria => $arreglo_valores_by_year){
							$array_series_subproyecto[$id_material][$id_categoria][$id_proyecto][$id_subproyecto] += $arreglo_valores_by_year[$year];
						}
					}
				}
			}
			foreach($array_series_subproyecto as $id_material => $valores_categoria){
				foreach($valores_categoria as $id_categoria => $valores_proyecto){
					foreach($valores_proyecto as $id_proyecto => $valores_subproyecto){
						$proyecto = $this->Projects_model->get_one($id_proyecto);
						$nombre_proyecto = $proyecto->title;
						$serie = array(
							'id' => 'id_drilldown_project_'.$id_material.'_'.$id_categoria.'_'.$id_proyecto.'_'.$year,
							'name' => $nombre_proyecto.' '.$year,
							'data' => array()
						);
						foreach($valores_subproyecto as $id_subproyecto => $value){
							$subproyecto = $this->Subprojects_model->get_one($id_subproyecto);
							$nombre_subproyecto = $subproyecto->nombre;
							if($subproyecto->id_proyecto == $proyecto->id){
								$serie['data'][] = array($nombre_subproyecto, $value);
							}
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
