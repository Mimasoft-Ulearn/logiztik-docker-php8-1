<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class AC_Beneficiaries_dashboard extends MY_Controller {
    
	private $id_client_context_module;
	private $id_client_context_submodule;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");	
		
		$client_area = $this->session->client_area;
		if($client_area == "territory"){
			$this->id_client_context_module = 6;
		} 
		$this->id_client_context_submodule = 19;
		
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

    }

    
    function index(){
        
		$id_usuario = $this->session->user_id;
		$client_area = $this->session->client_area;
		$id_cliente = $this->login_user->client_id;  
		
		$view_data["puede_ver"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
        $view_data["client_area"] = $client_area;

		//AÑO ACUTAL
		$current_year = date('Y');
		//AÑO ACTUAL + LOS ULTIMOS 4 AÑOS
		$years = range($current_year - 4, $current_year);
		$view_data['years'] = json_encode($years);

		//DATOS PARA RESUMENES
		$view_data['current_year'] = ($current_year);
		$disability_names_lows = array('CK Y KSA', 'CAM', 'AM', 'Corporativo Ley TEA');
		$view_data['names_lows'] = json_encode($disability_names_lows);



		$genders = array('M', 'F','Otro');


		// GENERO - MUJERES POR DOTACIÓN
		$view_data['women_per_staffing_data'] = json_encode($this->gen_chart_data_women_per_staffing($years, $genders));

		// GENERO - MUJERES POR CARGO (nombre DB "area_de_personal")
		$view_data['women_by_personnel_area'] = json_encode($this->gen_chart_data_women_by_personnel_area($years));

		// GENERO - MUJERES POR ÁREA (nombre DB "cargo")
		$cargos = $this->AC_Beneficiaries_model->get_cargos_subdivision();
		$array_cargos = array();
		foreach ($cargos as $cargo) {
			$array_cargos[] = $cargo->cargo;
		}
		$view_data['cargos'] = json_encode($array_cargos);
		$view_data['women_by_area_data'] = json_encode($this->gen_chart_data_women_by_area($years));

		// GENERO - MUJERES POR SUCURSAL
		$sucursales = $this->AC_Beneficiaries_model->get_sucursales_subdivision();
		$array_sucursales = array();
		foreach ($sucursales as $sucursal) {
			$array_sucursales[] = $sucursal->sucursal;
		}
		$view_data['sucursales'] = json_encode($array_sucursales);
		$view_data['women_by_branch_data'] = json_encode($this->gen_chart_data_women_by_branch($years));



		// GENERACIONES - GENERACIONES
		$view_data['generation_data'] = json_encode($this->gen_chart_data_amount_by_generation($years));

		// GENERACIONES - POSIBLES JUBILACIONES
		$view_data['posible_retirement_data'] = json_encode($this->gen_chart_data_posible_retirement($years));

		// GENERACIONES - PERSONAS CONTRATADAS MAYORES DE 45
		$view_data['hired_over_45_data'] = json_encode($this->gen_chart_data_hired_over_45($years));



		// DISCAPACIDAD - DISCAPACIDAD POR SUCURSAL
		$discapacidades_array = $this->AC_Beneficiaries_model->get_dropdown_discapacidad();
		$view_data['discapacidades'] = json_encode(array_values($discapacidades_array));
		$view_data['disability_by_branch'] = $this->gen_chart_data_disability_by_branch($years);

		// DISCAPACIDAD - 1% LEY CK Y KSA
		$view_data['ley_CK_KSA_data'] = json_encode($this->gen_chart_data_ley_CK_KSA($years));

		// DISCAPACIDAD - 1% LEY COMERCIAL ANDES MOTOR
		$view_data['ley_comercial_andes_motor_data'] = json_encode($this->gen_chart_data_ley_comercial_andes_motor($years));

		// DISCAPACIDAD - 1% LEY ANDES MOTOR
		$view_data['ley_andes_motor_data'] = json_encode($this->gen_chart_data_ley_andes_motor($years));

		// DISCAPACIDAD - 1% LEY TEA
		$view_data['ley_tea_data'] = json_encode($this->gen_chart_data_ley_tea($years));

		// DISCAPACIDAD RESUMEN 
		$view_data['disability_summary_data'] = json_encode($this->gen_chart_data_disability_summary($current_year));




		// MULTICULTURALIDAD - REGIONES
		$view_data['regiones_data'] = json_encode($this->gen_chart_data_regiones($years));

		// MULTICULTURALIDAD - NACIONALIDAD (EXTRANJEROS)
		$view_data['nacionalidad_data'] = $this->gen_chart_data_nacionalidad($years);

		// MULTICULTURALIDAD - NACIONALIDAD POR CARGO (% EXTRANJEROS) (Cargo = Area_de_personal en DB)
		$areas = $this->AC_Beneficiaries_model->get_dropdown_area_de_personal();
		array_shift($areas);
		$array_areas = array_values($areas);
		$view_data['areas'] = json_encode($array_areas);
		$view_data['nacionalidad_por_cargo_data'] = json_encode($this->gen_chart_data_nacionalidad_por_area_personal($years));

		// MULTICULTURALIDAD - NACIONALIDAD POR ÁREA (% EXTRANJEROS)	(Área = Cargo en DB)
		$cargos = $this->AC_Beneficiaries_model->get_cargos_subdivision();
		$array_cargos = array();
		foreach ($cargos as $cargo) {
			$array_cargos[] = $cargo->cargo;
		}
		$view_data['cargos'] = json_encode($array_cargos);
		$view_data['nacionalidad_por_area_data'] = json_encode($this->gen_chart_data_nacionalidad_por_cargo_subdiv($years));

		// MULTICULTURALIDAD - NACIONALIDAD POR SUCURSAL (% EXTRANJEROS)
		// $view_data['sucursales'] = json_encode($array_sucursales);
		$view_data['nacionalidad_por_sucursal_data'] = json_encode($this->gen_chart_data_nacionalidad_por_sucursal($years));
		

		
        $this->template->rander("ac_beneficiaries_dashboard/index", $view_data);
    }

	/* Función que genera el arreglo con datos para el gráfico "Mujeres por dotación" de pestaña Genero */
	function gen_chart_data_women_per_staffing($years, $genders){
		
		// Inicializar arreglo con valores 0 para cada año.
		$array_amount_by_gender = array();
		foreach ($genders as $gender) {
			foreach ($years as $year) {
				$array_amount_by_gender[$gender][$year] = 0; 
			}
		}

		// LLenar gráfico con valores reales.
		foreach ($years as $year) {
			$amounts = $this->AC_Beneficiaries_model->amount_per_gender($year)->result();

			foreach ($amounts as $amount) {

				$array_amount_by_gender[$amount->sexo][$year] = (int)$amount->cantidad;
			}
		}

		// Crear arreglo con datos para el gráfico
		$chart_data = array();
		foreach ($array_amount_by_gender as $gender => $amount_by_year) {
			
			if($gender == 'F'){
				$name = 'Mujer';
			} elseif($gender == 'M'){
				$name = 'Hombre';
			} elseif($gender == 'Otro'){
				$name = 'Otro';
			}

			$data_by_gender = array(
				'name' => $name,
				'data' => array()
			);

			foreach($amount_by_year as $year_amount){
				$data_by_gender['data'][] = $year_amount;
			}

			$chart_data[] = $data_by_gender;
		}

		
		// OBTENER LOS VALORES OBJETIVOS PARA LA SERIE % OBJETIVO		
		$id_cliente = $this->login_user->client_id;  
		
		$options = array(
			'id_cliente' => $id_cliente,
			'grafico' => 'women_per_staffing',
			'deleted' => 0
		);
		
		$objetivos_data = $this->AC_Feeders_beneficiary_objectives_model->get_all_where($options)->result();
		
		$objetivos = json_decode($objetivos_data[0]->objetivos);
		
		$array_objetivos = array();
		foreach ($years as $year) { 
			$array_objetivos[] = $objetivos->$year ? (int) $objetivos->$year : 0;
		}
		
		$chart_data[] = array(
			'name' => '% objetivo',
			'type' => 'spline',
			'data' => $array_objetivos
		);
		
		return $chart_data;
		
	}
	
	/* Función que genera el arreglo con datos para el gráfico "Mujeres por cargo" de pestaña Genero */
	function gen_chart_data_women_by_personnel_area($years){
		
		
		$areas = $this->AC_Beneficiaries_model->get_dropdown_area_de_personal();
		array_shift($areas);

		$chart_data = array();
		foreach ($areas as $area) {

			$data = array(
				'name' => $area,
				'data' => array()
			);
			foreach ($years as $year) {

				$data['data'][] = $this->AC_Beneficiaries_model->percentage_by_personnel_area($year, $area);
			}
			$chart_data[] = $data;
		}

		return $chart_data;
		
	}
	
	/* Función que genera el arreglo con datos para el gráfico "Mujeres por área" de pestaña Genero */
	function gen_chart_data_women_by_area($years){
		
		$results = $this->AC_Beneficiaries_model->percentage_by_cargo($years);

		$array_data = array();
		foreach($results as $result){
			// echo $result->year;
			$array_data[$result->year][$result->cargo] = $result->porcentaje;
		}

		$cargos = $this->AC_Beneficiaries_model->get_cargos_subdivision();
		
		$chart_data = array();
		foreach ($years as $year) {
			$data = array(
				'name' => (string)$year,
				'data' => array()
			);

			foreach ($cargos as $cargo) {
				$porcentaje = $array_data[$year][$cargo->cargo];
				$data['data'][] = is_null($porcentaje) ? 0 : (float)$porcentaje;
			}
			$chart_data[] = $data;
		}
		// echo '<pre>';echo json_encode($chart_data);exit;
		
		return $chart_data;
	}

	function gen_chart_data_women_by_branch($years){

		$results = $this->AC_Beneficiaries_model->percentage_by_branch($years);
		// echo '<pre>'; var_dump($results);exit;
		$array_data = array();
		foreach($results as $result){
			// echo $result->year;
			$array_data[$result->year][$result->sucursal] = $result->porcentaje;
		}

		$sucursales = $this->AC_Beneficiaries_model->get_sucursales_subdivision();
		
		$chart_data = array();
		foreach ($years as $year) {
			$data = array(
				'name' => (string)$year,
				'data' => array()
			);

			foreach ($sucursales as $sucursal) {
				$porcentaje = $array_data[$year][$sucursal->sucursal];
				$data['data'][] = is_null($porcentaje) ? 0 : (float)$porcentaje;
			}
			$chart_data[] = $data;
		}
		// echo '<pre>';echo json_encode($chart_data);exit;
		
		return $chart_data;
	}

	/* Función que genera el arreglo con datos para el gráfico "Generaciones" de pestaña Generaciones */
	function gen_chart_data_amount_by_generation($years){
		
		$generations = array(
			"baby_boomers",
			"generation_x",
			"millenials",
			"generation_z"
		);
		
		$array_data = array();
		foreach ($years as $year) {
		
			$result = $this->AC_Beneficiaries_model->amount_by_generation($year);

			foreach ($generations as $generation) {		
				$array_data[$generation][$year] = $result[0]->$generation;	
			}
		}

		$chart_data = array();
		foreach ($generations as $generation) {
			$data = array(
				'name' => lang($generation),
				'data' => array()
			);
			foreach ($years as $year) {
				$data['data'][] = (int)$array_data[$generation][$year];
			}
			$chart_data[] = $data;
		}
		// echo '<pre>';var_dump($chart_data); exit;
		return $chart_data;
	}

	// Generar datos para el gráfico "Posibles jubilaciones"
	function gen_chart_data_posible_retirement($years){

		$chart_data = array(
			array(
				'name' => 'n° personas',
				'type' => 'column',
				'data' => array()
			),
			 array(
				'name' => '% total',
				'type' => 'spline',
				'yAxis' => 1,
				'data' => array(),
				'tooltip' => array(
					'valueSuffix' => '%'
				)
			)
		);

		foreach ($years as $year) {
			
			$result = $this->AC_Beneficiaries_model->posible_retirement($year);

			$chart_data[0]['data'][] = (int)$result[0]->cant_prox_jubilar;
			$chart_data[1]['data'][] = (float)$result[0]->porcentaje;
		}

		// echo '<pre>'. json_encode($chart_data);exit;
		return $chart_data;
	}

	// Generar datos para gráfico "Personas contratadas mayores de 45 años"
	function gen_chart_data_hired_over_45($years){

		$chart_data = array(
			array(
				'name' => 'Actual',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => 'Objetivo',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => '% objetivo',
				'type' => 'spline',
				'yAxis' => 1,
				'data' => array(),
				'tooltip' => array(
					'valueSuffix' => '%'
				)
			)
		);

		foreach ($years as $year) {
			
			$result = $this->AC_Beneficiaries_model->hired_over_45($year);

			$chart_data[0]['data'][] = (int) $result['cant'];
			$chart_data[1]['data'][] = (int) $result['cant_objetivo'];
			$chart_data[2]['data'][] = (float) $result['porcentaje'];
			
		}
		
		// echo '<pre>'; var_dump($chart_data);exit;
		return $chart_data;
	}

	// Genera los datos para el gráfico "Discapacidad por sucursal" este tiene 2 niveles de despliegue el principal con columnas y el interior con lineas(drilldown)
	function gen_chart_data_disability_by_branch($years){

		$results = $this->AC_Beneficiaries_model->get_disability_by_branch($years);
		// echo '<pre>'; print_r($results);exit;

		$discapacidades_array = $this->AC_Beneficiaries_model->get_dropdown_discapacidad();
		$discapacidades_result = array_values($discapacidades_array);
		
		$sucursales_result = $this->AC_Beneficiaries_model->get_sucursales_subdivision();

		$array_data = array();
		foreach ($years as $year) {
			foreach ($discapacidades_result as $discapacidad) {
				
				if($discapacidad == 'No indica') continue;
				
				foreach ($sucursales_result as $sucursal) {
					$array_data[$year][$discapacidad][$sucursal->sucursal] = 0;
				}
			}
		}
		// echo '<pre>'; print_r($array_data);exit;
		
		foreach ($results as $result) {
			$array_data[$result->year][$result->discapacidad][$result->sucursal] = $result->cantidad;
		}
		// echo '<pre>'; print_r($array_data);exit;

		$series = array();
		foreach ($array_data as $year => $discapacidades) {
			$serie = array(
				'name' => $year,
				'data' => array(),
				'stack' => $year 	// año que aparece bajo la columna
			);
			// $data = array();
			foreach ($discapacidades as $discapacidad => $sucursal_values) {
				
				$data = array(
					'name' => $discapacidad,
					'y' => array_sum($sucursal_values),
					'drilldown' => $year.'-'.$discapacidad
				);
				$serie['data'][] = $data;
			}
			$series[] = $serie;
		}

		$drilldown = array();
		foreach ($array_data as $year => $discapacidades) {
			foreach ($discapacidades as $discapacidad => $sucursal_values) {
				$data = array(
					'id' => $year.'-'.$discapacidad,
					'name' => $year.' '.$discapacidad,
					'type' => 'line',
					'data' => array()
				);

				foreach ($sucursal_values as $sucursal => $value) {
					$data['data'][] = array($sucursal,(int)$value);
				}
				$drilldown[] = $data;
			}
		}

		/* echo json_encode($series);
		echo '<br><br>';
		echo json_encode($drilldown);
		exit; */
		return array(
			'data' => json_encode($series),
			'drilldown' => json_encode($drilldown)
		);
		
	}
	
	// Generar datos para gráfico "1% ley CK y KSA
	function gen_chart_data_ley_CK_KSA($years){

		$chart_data = array(
			array(
				'name' => 'Actual',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => 'Objetivo',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => '% objetivo',
				'type' => 'spline',
				'yAxis' => 1,
				'data' => array(),
				'tooltip' => array(
					'valueSuffix' => '%'
				)
			)
		);

		foreach ($years as $year) {
			
			$result = $this->AC_Beneficiaries_model->law_ck_ksa($year);

			$chart_data[0]['data'][] = (int) $result['cant'];
			$chart_data[1]['data'][] = (int) $result['cant_objetivo'];
			$chart_data[2]['data'][] = (float) $result['porcentaje'];
			
		}

		// echo '<pre>'; var_dump($chart_data);exit;
		return $chart_data;
	}
	
	// Generar datos para gráfico "1% ley Comercial Andes Motor
	function gen_chart_data_ley_comercial_andes_motor($years){

		$chart_data = array(
			array(
				'name' => 'Actual',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => 'Objetivo',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => '% objetivo',
				'type' => 'spline',
				'yAxis' => 1,
				'data' => array(),
				'tooltip' => array(
					'valueSuffix' => '%'
				)
			)
		);

		foreach ($years as $year) {
			
			$result = $this->AC_Beneficiaries_model->law_comercial_andes_motor($year);

			$chart_data[0]['data'][] = (int) $result['cant'];
			$chart_data[1]['data'][] = (int) $result['cant_objetivo'];
			$chart_data[2]['data'][] = (float) $result['porcentaje'];
			
		}

		// echo '<pre>'; var_dump($chart_data);exit;
		return $chart_data;
	}
	
	// Generar datos para gráfico "1% ley Comercial Andes Motor
	function gen_chart_data_ley_andes_motor($years){

		$chart_data = array(
			array(
				'name' => 'Actual',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => 'Objetivo',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => '% objetivo',
				'type' => 'spline',
				'yAxis' => 1,
				'data' => array(),
				'tooltip' => array(
					'valueSuffix' => '%'
				)
			)
		);

		foreach ($years as $year) {
			
			$result = $this->AC_Beneficiaries_model->law_andes_motor($year);

			$chart_data[0]['data'][] = (int) $result['cant'];
			$chart_data[1]['data'][] = (int) $result['cant_objetivo'];
			$chart_data[2]['data'][] = (float) $result['porcentaje'];
			
		}

		// echo '<pre>'; var_dump($chart_data);exit;
		return $chart_data;
	}
	
	// Generar datos para gráfico "Ley TEA"
	function gen_chart_data_ley_tea($years){

		$chart_data = array(
			array(
				'name' => 'Actual',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => 'Objetivo',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => '% objetivo',
				'type' => 'spline',
				'yAxis' => 1,
				'data' => array(),
				'tooltip' => array(
					'valueSuffix' => '%'
				)
			)
		);

		foreach ($years as $year) {
			
			$result = $this->AC_Beneficiaries_model->law_tea($year);

			$chart_data[0]['data'][] = (int) $result['cant'];
			$chart_data[1]['data'][] = (int) $result['cant_objetivo'];
			$chart_data[2]['data'][] = (float) $result['porcentaje'];
			
		}

		// echo '<pre>'; var_dump($chart_data);exit;
		return $chart_data;
	}

	// Genera los datos para gráfico resumen del último año de Discapacidad
	function gen_chart_data_disability_summary($current_year){

		$chart_data = array(
			array(
				'name' => 'Actual',
				'type' => 'column',
				'data' => array()
			),
			array(
				'name' => 'Objetivo',
				'type' => 'column',
				'data' => array()
			),
		);

		// DATOS CK - KSA
		$result_ck_ksa = $this->AC_Beneficiaries_model->law_ck_ksa($current_year);

		$chart_data[0]['data'][] = (int) $result_ck_ksa['cant'];
		$chart_data[1]['data'][] = (int) $result_ck_ksa['cant_objetivo'];


		// DATOS CAM
		$result_comercial_andes_motor = $this->AC_Beneficiaries_model->law_comercial_andes_motor($current_year);

		$chart_data[0]['data'][] = (int) $result_comercial_andes_motor['cant'];
		$chart_data[1]['data'][] = (int) $result_comercial_andes_motor['cant_objetivo'];
		
		//DATOS AM
		$result_andes_motor = $this->AC_Beneficiaries_model->law_andes_motor($current_year);

		$chart_data[0]['data'][] = (int) $result_andes_motor['cant'];
		$chart_data[1]['data'][] = (int) $result_andes_motor['cant_objetivo'];
		
		// DATOS LEY TEA
		$result_tea = $this->AC_Beneficiaries_model->law_tea($current_year);
		
		$chart_data[0]['data'][] = (int) $result_tea['cant'];
		$chart_data[1]['data'][] = (int) $result_tea['cant_objetivo'];
		
		
		return $chart_data;
				
	}


	// Genera los datos para el gráfico Regiones
	function gen_chart_data_regiones($years){

		$chart_data = array(
			array(
				'name' => 'Regiones',
				'data' => array()
			),
			array(
				'name' => 'Santiago',
				'data' => array()
			)
		);
		foreach ($years as $year) {
			$result = $this->AC_Beneficiaries_model->cant_regiones_stgo($year);
			$chart_data[0]['data'][] = (int)$result[0]->cant_regiones;
			$chart_data[1]['data'][] = (int)$result[0]->cant_santiago;
		}
		// echo '<pre>'; var_dump($chart_data);exit;
		return $chart_data;
	}
	
	// Genera la data para el gráfico "Nacionalidad (extranjeros)"
	function gen_chart_data_nacionalidad($years){

		$series_data = array(
			array(
				'name' => 'Chilenos',
				'data' => array()
			),
			array(
				'name' => 'Extranjeros',
				'data' => array()
			),
			array(
				'name' => '%',
				'type' => 'line',
				'data' => array()
			)
		);

		$drilldown = array();
		
		foreach ($years as $year) {
			
			$result = $this->AC_Beneficiaries_model->cant_nacionalidad($year);
			
			// chilenos
			$series_data[0]['data'][] = array(
				"name" => $year,
				"y" => (int)$result['cant_chilenos']
			);
			
			// extranjeros
			$series_data[1]['data'][] = array(
				"name" => $year,
				"y" => (int)$result['cant_extranjeros'],
				"drilldown" => $year.'-extranjeros'
			);
			
			// % porcentaje de extranjeos
			$series_data[2]['data'][] = array(
				"name" => $year,
				"y" => (int)$result['porc_extranjeros']
			);

			$drilldown[] = array(
				"id" => $year.'-extranjeros',
				"name" => $year.'-extranjeros',
				"data" => $result['drilldown'],
				"stacking" => "normal"
			);
		}

		// echo '<pre>'; var_dump($drilldown);exit;
		return array('series' => json_encode($series_data), 'drilldown' => json_encode($drilldown));
	}


	function gen_chart_data_nacionalidad_por_area_personal($years){

		$areas = $this->AC_Beneficiaries_model->get_dropdown_area_de_personal();
		array_shift($areas);

		$chart_data = array();
		foreach ($years as $year) {
			$results = $this->AC_Beneficiaries_model->cant_nacionalidad_por_area_personal($year, $areas);
			
			$serie = array(
				"name" => $year,
				"data" => array()
			);

			foreach ($results as $result) {
				$serie['data'][] = $result['porcentaje'];
			}

			$chart_data[] = $serie;
		}
		// echo '<pre>'; var_dump($chart_data);exit;

		return $chart_data;
	}


	function gen_chart_data_nacionalidad_por_cargo_subdiv($years){

		$cargos = $this->AC_Beneficiaries_model->get_cargos_subdivision();
		
		$chart_data = array();
		foreach ($years as $year) {
			$results = $this->AC_Beneficiaries_model->cant_nacionalidad_por_cargo_subdiv($year, $cargos);
			
			$serie = array(
				"name" => $year,
				"data" => array()
			);

			foreach ($results as $result) {
				$serie['data'][] = $result['porcentaje'];
			}

			$chart_data[] = $serie;
		}
		// echo '<pre>'; var_dump($chart_data);exit;

		return $chart_data;
	}

	function gen_chart_data_nacionalidad_por_sucursal($years){

		$sucursales = $this->AC_Beneficiaries_model->get_sucursales_subdivision();
		
		$chart_data = array();
		foreach ($years as $year) {
			$results = $this->AC_Beneficiaries_model->cant_nacionalidad_por_sucursal($year, $sucursales);
			
			$serie = array(
				"name" => $year,
				"data" => array()
			);

			foreach ($results as $result) {
				$serie['data'][] = $result['porcentaje'];
			}

			$chart_data[] = $serie;
		}
		// echo '<pre>'; var_dump($chart_data);exit;

		return $chart_data;
	}

}