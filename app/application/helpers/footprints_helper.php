<?php

if (!function_exists('get_transformed_value')) {
	
    function get_transformed_value($valor, $id_tipo_unidad_origen, $id_unidad_origen, $id_unidad_destino) {
		
		$ci = get_instance();
		$fila_conversion = $ci->Conversion_model->get_one_where(
			array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			)
		);
		$valor_transformacion = $fila_conversion->transformacion;
		$valor_final = $valor * $valor_transformacion;
		
		return $valor_final;

    }

}


/**
Retorna el total del valor de UF
**/
if (!function_exists('get_functional_unit_value')) {

    function get_functional_unit_value($id_cliente, $id_proyecto, $id_uf, $start_date = NULL, $end_date = NULL) {
		
		$ci = get_instance();

		$valor_final = 0;
		$elem_fuera_rango = 0;

		// obtengo el id del campo fijo creado dinamicamente del uf de UF
		$uf_field_fijo_uf = $ci->Fixed_fields_model->get_fixed_fields(array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"codigo_formulario_fijo" => 'or_unidades_funcionales',
			"nombre_campo" => 'Unidad Funcional',
		))->row();
		$id_campo_uf_uf = (int)$uf_field_fijo_uf->id;

		// obtengo el id del campo fijo creado dinamicamente del Valor de UF
		$valor_field_fijo_uf = $ci->Fixed_fields_model->get_fixed_fields(array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"codigo_formulario_fijo" => 'or_unidades_funcionales',
			"nombre_campo" => 'Valor',
		))->row();
		$id_campo_valor_uf = (int)$valor_field_fijo_uf->id;

		// obtengo el id del campo fijo creado dinamicamente del Periodo de UF
		$periodo_field_fijo_uf = $ci->Fixed_fields_model->get_fixed_fields(array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"codigo_formulario_fijo" => 'or_unidades_funcionales',
			"nombre_campo" => 'Periodo',
		))->row();
		$id_campo_periodo_uf = (int)$periodo_field_fijo_uf->id;

		// Consultar el formulario fijo de tipo "Unidades Funcionales" del cliente / proyecto y traer sus valores
		$valores_form_fijo_uf = $ci->Fixed_form_values_model->get_functional_unit_value(array(
			"id_tipo_formulario" => 3,
			"campo_uf_uf" => $id_campo_uf_uf,
			"id_uf" => $id_uf,
		))->result();
		
		if(count($valores_form_fijo_uf)){
		
			foreach($valores_form_fijo_uf as $index => $valor){
				
				$datos = json_decode($valor->datos, TRUE);
				$periodo_form_fijo_uf 	= $datos[$id_campo_periodo_uf]; // 23: id campo fijo "Periodo" (rango de fechas (array))
				$valor_form_fijo_uf 	= $datos[$id_campo_valor_uf]; // 25: id campo fijo "Valor"
					
				if(!$start_date && !$end_date){ // Si no llega el rango de fechas (Como en el módulo de Panel Principal)
				
					$valor_final = $valor_final + $valor_form_fijo_uf;
					
				} else {
				
					$start_date_query = strtotime($start_date);
					$end_date_query = strtotime($end_date);
					$cant_dias_rango_consulta = $end_date_query - $start_date_query;
					$cant_dias_rango_consulta = (round($cant_dias_rango_consulta / (60 * 60 * 24)) + 1);

					$start_date_form_fijo_uf = strtotime($periodo_form_fijo_uf["start_date"]);
					$end_date_form_fijo_uf = strtotime($periodo_form_fijo_uf["end_date"]);
					$cant_dias_periodo_elemento = $end_date_form_fijo_uf - $start_date_form_fijo_uf;
					$cant_dias_periodo_elemento = (round($cant_dias_periodo_elemento / (60 * 60 * 24))) + 1;

					// Si la fecha de inicio del elemento está entre las fechas de consulta
					if ( ($periodo_form_fijo_uf["start_date"] >= $start_date) && ($periodo_form_fijo_uf["start_date"] <= $end_date) ){
													
						// Si la fecha de término del elemento está entre las fechas de la consulta
						if(($periodo_form_fijo_uf["end_date"] >= $start_date) && ($periodo_form_fijo_uf["end_date"] <= $end_date)){
								
							// cantidad de días que hay entre la fecha de inicio del rango del elemento y la fecha final del rango de la consulta
							$cant_dias_entran_rango_consulta = $cant_dias_periodo_elemento;
							$valor_final = $valor_final + (($valor_form_fijo_uf / $cant_dias_periodo_elemento) * $cant_dias_entran_rango_consulta);
															
						} else { // Si la fecha de término del elemento NO está entre las fechas de la consulta
								
							// cantidad de días que hay entre la fecha de inicio del rango del periodo y la fecha final del rango de la consulta
							$cant_dias_entran_rango_consulta = $end_date_query - $start_date_form_fijo_uf;
							$cant_dias_entran_rango_consulta = (round($cant_dias_entran_rango_consulta / (60 * 60 * 24)) + 1);
							$valor_final = $valor_final + (($valor_form_fijo_uf / $cant_dias_periodo_elemento) * $cant_dias_entran_rango_consulta);

						}
						
					}
					
					// Si la fecha de termino del elemento está entre las fechas consultadas (incluyéndolas)
					elseif(($periodo_form_fijo_uf["end_date"] >= $start_date) && ($periodo_form_fijo_uf["end_date"] <= $end_date)){

						// cantidad de días que hay entre la fecha de término del elemento y la fecha de inicio del rango de la consulta
						$cant_dias_entran_rango_consulta = $end_date_form_fijo_uf - $start_date_query;
						$cant_dias_entran_rango_consulta = (round($cant_dias_entran_rango_consulta / (60 * 60 * 24)) + 1);
						$valor_final = $valor_final + (($valor_form_fijo_uf / $cant_dias_periodo_elemento) * $cant_dias_entran_rango_consulta);

					} else {

						if(($start_date >= $periodo_form_fijo_uf["start_date"]) && ($end_date <= $periodo_form_fijo_uf["end_date"])){
							$cant_dias_entran_rango_consulta = $cant_dias_rango_consulta;
							$valor_final = $valor_final + (($valor_form_fijo_uf / $cant_dias_periodo_elemento) * $cant_dias_entran_rango_consulta);
						} else {
							$elem_fuera_rango++;
						}
						
					}
	
				} // FIN else de if(!$start_date && !$end_date)
				
			} // FIN foreach($valores_form_fijo_uf as $valor)
			
		} else {// FIN if(count($valores_form_fijo_uf))
			$valor_final = 1;
		}
		
		if(count($valores_form_fijo_uf) == $elem_fuera_rango){
			$valor_final = 1;
		}
		
		//echo $valor_final;
		return $valor_final;

    }

}


if (!function_exists('get_fields_criteria')) {

	function get_fields_criteria($calculation_data = NULL) {

		$ci = get_instance();

		/*SECCION NUEVA DE CODIGO TIPOS DE TRATAMIENTO */
		if(isset($calculation_data->tipo_by_criterio)){
			$j_datos = json_decode($calculation_data->tipo_by_criterio, true);
			$form_info = $ci->Forms_model->get_one($calculation_data->id_formulario);

			if($form_info->flujo == 'Residuo'){
				
				if($j_datos["id_campo_sp"] == "tipo_tratamiento"){
					$id_campo_sp ="tipo_tratamiento";
				
				}elseif($j_datos["id_campo_sp"] == "month"){
					$id_campo_sp ="month";

				}else{
					$id_campo_sp = $calculation_data->id_campo_sp;
				}
				
				if($j_datos["id_campo_pu"] == "tipo_tratamiento"){
					$id_campo_pu ="tipo_tratamiento";
				}else{
					$id_campo_pu = $calculation_data->id_campo_pu;
				}
				
				if($j_datos["id_campo_fc"] == "tipo_tratamiento"){
					$id_campo_fc ="tipo_tratamiento";
				}else{
					$id_campo_fc = $calculation_data->id_campo_fc;
				}

			}elseif($form_info->flujo == 'Consumo'){
				$tipo_origen_array = json_decode($form_info->tipo_origen, true);
				$tipo_origen = $tipo_origen_array["type_of_origin"];
				if($tipo_origen == 1){// Materia

					if($j_datos["id_campo_sp"] == "type_of_origin_matter"){
						$id_campo_sp ="type_of_origin_matter";
					}else{
						$id_campo_sp = $calculation_data->id_campo_sp;
					}
					if($j_datos["id_campo_pu"] == "type_of_origin_matter"){
						$id_campo_pu ="type_of_origin_matter";
					}else{
						$id_campo_pu = $calculation_data->id_campo_pu;
					}
					if($j_datos["id_campo_fc"] == "type_of_origin_matter"){
						$id_campo_fc ="type_of_origin_matter";
					}else{
						$id_campo_fc = $calculation_data->id_campo_fc;
					}
				}
				if($tipo_origen == 2){// Energía

					if($j_datos["id_campo_sp"] == "type_of_origin"){
						$id_campo_sp ="type_of_origin";
					}else{
						$id_campo_sp = $calculation_data->id_campo_sp;
					}
					if($j_datos["id_campo_pu"] == "type_of_origin"){
						$id_campo_pu ="type_of_origin";
					}else{
						$id_campo_pu = $calculation_data->id_campo_pu;
					}
					if($j_datos["id_campo_fc"] == "type_of_origin"){
						$id_campo_fc ="type_of_origin";
					}else{
						$id_campo_fc = $calculation_data->id_campo_fc;
					}
					
				}
			}elseif($form_info->flujo == 'No Aplica'){
				if($j_datos["id_campo_sp"] == "default_type"){
					$id_campo_sp ="default_type";
				}else{
					$id_campo_sp = $calculation_data->id_campo_sp;
				}
				
				if($j_datos["id_campo_pu"] == "default_type"){
					$id_campo_pu ="default_type";
				}else{
					$id_campo_pu = $calculation_data->id_campo_pu;
				}
				
				if($j_datos["id_campo_fc"] == "default_type"){
					$id_campo_fc ="default_type";
				}else{
					$id_campo_fc = $calculation_data->id_campo_fc;
				}
			}
		
		}else{
			$id_campo_sp = $calculation_data->id_campo_sp;
			$id_campo_pu = $calculation_data->id_campo_pu;
			$id_campo_fc = $calculation_data->id_campo_fc;
		}


		if(isset($calculation_data->tipo_by_criterio)){

			if($id_campo_fc == "tipo_tratamiento"){
				$tipos_de_tratamiento = $ci->Tipo_tratamiento_model->get_all_where(array("deleted"=>0))->result();
				foreach($tipos_de_tratamiento as $tipo_tratamiento){
					// Antes se guardaba en criterio_fc el nombre de un tipo de tratamiento.
					// if($calculation_data->criterio_fc == $tipo_tratamiento->nombre){
					if($calculation_data->criterio_fc == $tipo_tratamiento->id){
						$criterio_fc = $tipo_tratamiento->id;
						break;
					}
				}
			}else if($id_campo_fc == "type_of_origin_matter"){
				$tipos_origen_materia = $ci->EC_Types_of_origin_matter_model->get_all()->result();
				foreach($tipos_origen_materia as $tipo_origen){
					// if($calculation_data->criterio_fc == lang($tipo_origen->nombre)){
					if($calculation_data->criterio_fc == $tipo_origen->id){
						$criterio_fc = $tipo_origen->id;
						break;
					}
				}
			}else if($id_campo_fc == "type_of_origin"){
				$value = $ci->EC_Types_of_origin_model->get_one_where(array("nombre" => "energy" ,"deleted"=>0));
				$criterio_fc = $value->id;
			}else if($id_campo_fc == "default_type"){
				$tipos_no_aplica = $ci->EC_Types_no_apply_model->get_all()->result();
				foreach($tipos_no_aplica as $tipo_no_aplica){
					// if($calculation_data->criterio_fc == lang($tipo_no_aplica->nombre)){
					if($calculation_data->criterio_fc == $tipo_no_aplica->id){
						$criterio_fc = $tipo_no_aplica->id;
						break;
					}
				}
			}else{
				$criterio_fc = $calculation_data->criterio_fc;
			}
		}else{
			$criterio_fc = $calculation_data->criterio_fc;
		}
		/*FIN SECCION NUEVA DE CODIGO TIPOS DE TRATAMIENTO */


		$object = new stdClass();
		$object->id_campo_sp = $id_campo_sp;
		$object->id_campo_pu = $id_campo_pu;
		$object->id_campo_fc = $id_campo_fc;
		$object->criterio_fc = $criterio_fc;

		return $object;

	}

}
