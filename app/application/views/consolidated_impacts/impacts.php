<?php 

	$array_data_total_impacts = array(); // PARA GUARDAR LA DATA DE IMPACTOS TOTALES DE TODOS LOS PROYECTOS
	$array_data_impacts_by_functional_units = array(); // PARA GUARDAR LA DATA DE IMPACTOS TOTALES POR UNIDAD FUNCIONAL DE TODOS LOS PROYECTOS

	foreach($projects as $project){

		$environmental_footprints_settings = $array_data_by_project[$project->id]["environmental_footprints_settings"];
		$campos_unidad = $array_data_by_project[$project->id]["campos_unidad"];
		$huellas = $array_data_by_project[$project->id]["huellas"];
		$huellas_carbon = $array_data_by_project[$project->id]["huellas_carbon"];
		$huellas_water = $array_data_by_project[$project->id]["huellas_water"];
		$criterios_calculos = $array_data_by_project[$project->id]["criterios_calculos"];
		$array_factores = $array_data_by_project[$project->id]["array_factores"];
		// $array_transformaciones = $array_data_by_project[$project->id]["array_transformaciones"];
		$sp_uf = $array_data_by_project[$project->id]["sp_uf"];
		$campos_unidad = $array_data_by_project[$project->id]["campos_unidad"];
		$calculos = $array_data_by_project[$project->id]["calculos"];
		$sucursales = $array_data_by_project[$project->id]["sucursales"];
		// $unidades_funcionales = $array_data_by_project[$project->id]['unidades_funcionales'];

		$visible_total_impacts;
		$visible_impacts_by_functional_units;
		foreach($environmental_footprints_settings as $setting) { 
			if($setting->informacion == "total_impacts"){
				$visible_total_impacts = ($setting->habilitado == 1) ? TRUE : FALSE;
			}
			if($setting->informacion == "impacts_by_functional_units"){
				$visible_impacts_by_functional_units = ($setting->habilitado == 1) ? TRUE : FALSE;
			}
		}

		$ids_metodologia = json_decode($project->id_metodologia);
		$disponibilidad_modulo_huellas = $this->Module_availability_model->get_one_where(array("id_cliente" => $client_id, "id_proyecto" => $project->id, "id_modulo_cliente" => 1, "deleted" => 0))->available;

		if($disponibilidad_modulo_huellas) {

			// DATA IMPACTOS TOTALES
			if($visible_total_impacts){

				$id_proyecto = $project->id;
				// $ids_metodologia = json_decode($project->id_metodologia);

				// Si el proyecto tiene la metodología con id 1 (ReCiPe 2008, midpoint (H) [v1.11, December 2014])
				if(in_array(1, $ids_metodologia)){

					if(count($huellas)){

						foreach($huellas as $huella){

							$id_huella = $huella->id;
							$total_huella = 0;
							
							$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
								"id_cliente" => $client_id, 
								"id_proyecto" => $id_proyecto, 
								"id_tipo_unidad" => $huella->id_tipo_unidad, 
								"deleted" => 0
							))->id_unidad;

							$nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;
							
							// VALOR DE CONVERSION
							$id_tipo_unidad_origen = $huella->id_tipo_unidad;
							$id_unidad_origen = $huella->id_unidad;
							$fila_config_huella = $this->Module_footprint_units_model->get_one_where(
								array(
									"id_cliente" => $client_id,
									"id_proyecto" => $id_proyecto,
									"id_tipo_unidad" => $id_tipo_unidad_origen,
									"deleted" => 0
								)
							);
							$id_unidad_destino = $fila_config_huella->id_unidad;

							$fila_conversion = $this->Conversion_model->get_one_where(
								array(
									"id_tipo_unidad" => $id_tipo_unidad_origen,
									"id_unidad_origen" => $id_unidad_origen,
									"id_unidad_destino" => $id_unidad_destino
								)
							);
							$valor_transformacion = $fila_conversion->transformacion;

							$icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");

							foreach($criterios_calculos as $calculo){
														
								$total_calculo = 0;
								
								$id_material = $calculo->id_material;
								$id_categoria = $calculo->id_categoria;
								$id_subcategoria = $calculo->id_subcategoria;
								$id_metodologia = $calculo->id_metodologia;
								$id_formulario = $calculo->id_formulario;
								$id_bd = $calculo->id_bd;

								$fields_criteria = get_fields_criteria($calculo);
								$id_campo_sp = $fields_criteria->id_campo_sp;
								$id_campo_pu = $fields_criteria->id_campo_pu;
								$id_campo_fc = $fields_criteria->id_campo_fc;
								$criterio_fc = $fields_criteria->criterio_fc;
								
								$ides_campo_unidad = json_decode($calculo->id_campo_unidad, true);
								
								//Deduzco el id de unidad al que debe consultar para el factor
								$array_unidades = array();
								$array_id_unidades = array();
								$array_id_tipo_unidades = array();
								
								// POR CADA CAMPO UNIDAD SELECCIONADO EN EL CALCULO
								foreach($ides_campo_unidad as $id_campo_unidad){

									if($id_campo_unidad == 0){
										$id_formulario = $calculo->id_formulario;
										$form_data = $this->Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
										$json_unidad_form = json_decode($form_data->unidad, true);
										
										$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
										$id_unidad = $json_unidad_form["unidad_id"];
										
										$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
										$array_unidades[] = $fila_unidad->nombre;
										$array_id_unidades[] = $id_unidad;
										$array_id_tipo_unidades[] = $id_tipo_unidad;
									}else{
										$fila_campo = $this->Fields_model->get_one_where(array("id" => $id_campo_unidad, "deleted" => 0));
										$info_campo = $fila_campo->opciones;
										$info_campo = json_decode($info_campo, true);

										$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
										$id_unidad = $info_campo[0]["id_unidad"];
										
										$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
										$array_unidades[] = $fila_unidad->nombre;
										$array_id_unidades[] = $id_unidad;
										$array_id_tipo_unidades[] = $id_tipo_unidad;
									}

								}
								
								// Se ampliaron unidades de cálculo
								if(count($array_id_unidades) == 1){
									$id_unidad = $array_id_unidades[0];
								}elseif(count($array_id_unidades) == 2){
									if($array_id_unidades[0] == 18 && $array_id_unidades[1] != 18){
										$id_unidad = $array_id_unidades[1];
									}elseif($array_id_unidades[0] != 18 && $array_id_unidades[1] == 18){
										$id_unidad = $array_id_unidades[0];
									}elseif(in_array(9, $array_id_unidades) && in_array(1, $array_id_unidades)){
										$id_unidad = 5;
									}elseif(in_array(9, $array_id_unidades) && in_array(2, $array_id_unidades)){
										$id_unidad = 6;
									}elseif(in_array(3, $array_id_unidades) && in_array(14, $array_id_unidades)){// m3 x hectarea
										$id_unidad = 3;
									}else{
										$id_unidad = $array_id_unidades[0];
									}
								}elseif(count($array_id_unidades) == 3){
									if(
										in_array(18, $array_id_unidades) && 
										in_array(9, $array_id_unidades) && 
										in_array(1, $array_id_unidades)
									){
										$id_unidad = 5;
									}elseif(
										in_array(18, $array_id_unidades) && 
										in_array(9, $array_id_unidades) && 
										in_array(2, $array_id_unidades)
									){
										$id_unidad = 6;
									}else{
										
									}
								}else{
									
								}

								// Al total hay que multiplicarlo por el factor correspondiente
								$fila_factor = $this->Characterization_factors_model->get_one_where(
									array(
										"id_bd" => $id_bd,
										"id_metodologia" => $id_metodologia,
										"id_huella" => $id_huella,
										"id_material" => $id_material,
										"id_categoria" => $id_categoria,
										"id_subcategoria" => $id_subcategoria,
										"id_unidad" => $id_unidad,
										"deleted" => 0
									)
								);

								$valor_factor = 0;
								if($fila_factor->id){
									$valor_factor = $fila_factor->factor;
								}

								$elementos = $this->Calculation_model->get_records_of_forms_for_calculation($id_proyecto, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria, $first_date_current_year, $last_date_current_year)->result();
								foreach($elementos as $elemento){
									$total_elemento = 0;
									$datos_decoded = json_decode($elemento->datos, true);
									
									$mult = 1;
									foreach($ides_campo_unidad as $id_campo_unidad){
										if($id_campo_unidad == 0){
											$mult *= $datos_decoded["unidad_residuo"];
										}else{
											$mult *= $datos_decoded[$id_campo_unidad];
										}
									}
									
									$total_elemento = $mult * $valor_factor;
									$total_calculo += $total_elemento;
									
								}

								$total_huella += $total_calculo;
								
							}// FIN EACH CALCULO

							$total_huella *= $valor_transformacion;

							$array_data_total_impacts["environmental_footprints"][$id_huella]["nombre"] = $huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.')';
							$array_data_total_impacts["environmental_footprints"][$id_huella]["icono"] = $icono;
							$array_data_total_impacts["environmental_footprints"][$id_huella]["total_huella"] += $total_huella;

						}

					}

				}

				// Si el proyecto tiene la metodología con id 2 (GHG Protocol)
				if(in_array(2, $ids_metodologia)){

					if(count($huellas_carbon)){
					
						foreach($huellas_carbon as $huella){

							$id_huella = $huella->id;
							$total_huella = 0;
							
							$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
								"id_cliente" => $client_id, 
								"id_proyecto" => $id_proyecto, 
								"id_tipo_unidad" => $huella->id_tipo_unidad, 
								"deleted" => 0
							))->id_unidad;

							$nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;
							
							// VALOR DE CONVERSION
							$id_tipo_unidad_origen = $huella->id_tipo_unidad;
							$id_unidad_origen = $huella->id_unidad;
							$fila_config_huella = $this->Module_footprint_units_model->get_one_where(
								array(
									"id_cliente" => $client_id,
									"id_proyecto" => $id_proyecto,
									"id_tipo_unidad" => $id_tipo_unidad_origen,
									"deleted" => 0
								)
							);
							$id_unidad_destino = $fila_config_huella->id_unidad;

							$fila_conversion = $this->Conversion_model->get_one_where(
								array(
									"id_tipo_unidad" => $id_tipo_unidad_origen,
									"id_unidad_origen" => $id_unidad_origen,
									"id_unidad_destino" => $id_unidad_destino
								)
							);
							$valor_transformacion = $fila_conversion->transformacion;

							$icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");

							foreach($criterios_calculos as $calculo){
								
								$total_calculo = 0;
								
								$id_material = $calculo->id_material;
								$id_categoria = $calculo->id_categoria;
								$id_subcategoria = $calculo->id_subcategoria;
								$id_metodologia = $calculo->id_metodologia;
								$id_formulario = $calculo->id_formulario;
								$id_bd = $calculo->id_bd;

								$fields_criteria = get_fields_criteria($calculo);
								$id_campo_sp = $fields_criteria->id_campo_sp;
								$id_campo_pu = $fields_criteria->id_campo_pu;
								$id_campo_fc = $fields_criteria->id_campo_fc;
								$criterio_fc = $fields_criteria->criterio_fc;
								
								$ides_campo_unidad = json_decode($calculo->id_campo_unidad, true);
								
								//Deduzco el id de unidad al que debe consultar para el factor
								$array_unidades = array();
								$array_id_unidades = array();
								$array_id_tipo_unidades = array();
								
								// POR CADA CAMPO UNIDAD SELECCIONADO EN EL CALCULO
								foreach($ides_campo_unidad as $id_campo_unidad){

									if($id_campo_unidad == 0){
										$id_formulario = $calculo->id_formulario;
										$form_data = $this->Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
										$json_unidad_form = json_decode($form_data->unidad, true);
										
										$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
										$id_unidad = $json_unidad_form["unidad_id"];
										
										$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
										$array_unidades[] = $fila_unidad->nombre;
										$array_id_unidades[] = $id_unidad;
										$array_id_tipo_unidades[] = $id_tipo_unidad;
									}else{
										$fila_campo = $this->Fields_model->get_one_where(array("id" => $id_campo_unidad, "deleted" => 0));
										$info_campo = $fila_campo->opciones;
										$info_campo = json_decode($info_campo, true);

										$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
										$id_unidad = $info_campo[0]["id_unidad"];
										
										$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
										$array_unidades[] = $fila_unidad->nombre;
										$array_id_unidades[] = $id_unidad;
										$array_id_tipo_unidades[] = $id_tipo_unidad;
									}

								}
								
								// Se ampliaron unidades de cálculo
								if(count($array_id_unidades) == 1){
									$id_unidad = $array_id_unidades[0];
								}elseif(count($array_id_unidades) == 2){
									if($array_id_unidades[0] == 18 && $array_id_unidades[1] != 18){
										$id_unidad = $array_id_unidades[1];
									}elseif($array_id_unidades[0] != 18 && $array_id_unidades[1] == 18){
										$id_unidad = $array_id_unidades[0];
									}elseif(in_array(9, $array_id_unidades) && in_array(1, $array_id_unidades)){
										$id_unidad = 5;
									}elseif(in_array(9, $array_id_unidades) && in_array(2, $array_id_unidades)){
										$id_unidad = 6;
									}elseif(in_array(3, $array_id_unidades) && in_array(14, $array_id_unidades)){// m3 x hectarea
										$id_unidad = 3;
									}else{
										$id_unidad = $array_id_unidades[0];
									}
								}elseif(count($array_id_unidades) == 3){
									if(
										in_array(18, $array_id_unidades) && 
										in_array(9, $array_id_unidades) && 
										in_array(1, $array_id_unidades)
									){
										$id_unidad = 5;
									}elseif(
										in_array(18, $array_id_unidades) && 
										in_array(9, $array_id_unidades) && 
										in_array(2, $array_id_unidades)
									){
										$id_unidad = 6;
									}else{
										
									}
								}else{
									
								}

								// Al total hay que multiplicarlo por el factor correspondiente
								$fila_factor = $this->Characterization_factors_model->get_one_where(
									array(
										"id_bd" => $id_bd,
										"id_metodologia" => $id_metodologia,
										"id_huella" => $id_huella,
										"id_material" => $id_material,
										"id_categoria" => $id_categoria,
										"id_subcategoria" => $id_subcategoria,
										"id_unidad" => $id_unidad,
										"deleted" => 0
									)
								);

								$valor_factor = 0;
								if($fila_factor->id){
									$valor_factor = $fila_factor->factor;
								}

								$elementos = $this->Calculation_model->get_records_of_forms_for_calculation($id_proyecto, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria, $first_date_current_year, $last_date_current_year)->result();
								foreach($elementos as $elemento){
									$total_elemento = 0;
									$datos_decoded = json_decode($elemento->datos, true);
									
									$mult = 1;
									foreach($ides_campo_unidad as $id_campo_unidad){
										if($id_campo_unidad == 0){
											$mult *= $datos_decoded["unidad_residuo"];
										}else{
											$mult *= $datos_decoded[$id_campo_unidad];
										}
									}
									
									$total_elemento = $mult * $valor_factor;
									$total_calculo += $total_elemento;
									
								}

								$total_huella += $total_calculo;
								
							}// FIN EACH CALCULO
							
							$total_huella *= $valor_transformacion;

							$array_data_total_impacts["carbon_environmental_footprints"][$id_huella]["nombre"] = $huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.')';
							$array_data_total_impacts["carbon_environmental_footprints"][$id_huella]["icono"] = $icono;
							$array_data_total_impacts["carbon_environmental_footprints"][$id_huella]["total_huella"] += $total_huella;

						}

					}

				}

				// Si el proyecto tiene la metodología con id 3 (Huella de Agua)
				if(in_array(3, $ids_metodologia)){

					if(count($huellas_water)){

						foreach($huellas_water as $huella){

							$id_huella = $huella->id;
							$total_huella = 0;
							
							$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
								"id_cliente" => $client_id, 
								"id_proyecto" => $id_proyecto, 
								"id_tipo_unidad" => $huella->id_tipo_unidad, 
								"deleted" => 0
							))->id_unidad;

							$nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;
							
							// VALOR DE CONVERSION
							$id_tipo_unidad_origen = $huella->id_tipo_unidad;
							$id_unidad_origen = $huella->id_unidad;
							$fila_config_huella = $this->Module_footprint_units_model->get_one_where(
								array(
									"id_cliente" => $client_id,
									"id_proyecto" => $id_proyecto,
									"id_tipo_unidad" => $id_tipo_unidad_origen,
									"deleted" => 0
								)
							);
							$id_unidad_destino = $fila_config_huella->id_unidad;

							$fila_conversion = $this->Conversion_model->get_one_where(
								array(
									"id_tipo_unidad" => $id_tipo_unidad_origen,
									"id_unidad_origen" => $id_unidad_origen,
									"id_unidad_destino" => $id_unidad_destino
								)
							);
							$valor_transformacion = $fila_conversion->transformacion;
							
							$icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");
			
							foreach($criterios_calculos as $calculo){
								
								$total_calculo = 0;
								
								$id_material = $calculo->id_material;
								$id_categoria = $calculo->id_categoria;
								$id_subcategoria = $calculo->id_subcategoria;
								$id_metodologia = $calculo->id_metodologia;
								$id_formulario = $calculo->id_formulario;
								$id_bd = $calculo->id_bd;

								$fields_criteria = get_fields_criteria($calculo);
								$id_campo_sp = $fields_criteria->id_campo_sp;
								$id_campo_pu = $fields_criteria->id_campo_pu;
								$id_campo_fc = $fields_criteria->id_campo_fc;
								$criterio_fc = $fields_criteria->criterio_fc;
								
								$ides_campo_unidad = json_decode($calculo->id_campo_unidad, true);
								
								//Deduzco el id de unidad al que debe consultar para el factor
								$array_unidades = array();
								$array_id_unidades = array();
								$array_id_tipo_unidades = array();
								
								// POR CADA CAMPO UNIDAD SELECCIONADO EN EL CALCULO
								foreach($ides_campo_unidad as $id_campo_unidad){

									if($id_campo_unidad == 0){
										$id_formulario = $calculo->id_formulario;
										$form_data = $this->Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
										$json_unidad_form = json_decode($form_data->unidad, true);
										
										$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
										$id_unidad = $json_unidad_form["unidad_id"];
										
										$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
										$array_unidades[] = $fila_unidad->nombre;
										$array_id_unidades[] = $id_unidad;
										$array_id_tipo_unidades[] = $id_tipo_unidad;
									}else{
										$fila_campo = $this->Fields_model->get_one_where(array("id" => $id_campo_unidad, "deleted" => 0));
										$info_campo = $fila_campo->opciones;
										$info_campo = json_decode($info_campo, true);

										$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
										$id_unidad = $info_campo[0]["id_unidad"];
										
										$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
										$array_unidades[] = $fila_unidad->nombre;
										$array_id_unidades[] = $id_unidad;
										$array_id_tipo_unidades[] = $id_tipo_unidad;
									}
									// Para graficos
									$array_unidades_proyecto[$id_unidad] = $fila_unidad->nombre;
								}
								
								// Se ampliaron unidades de cálculo
								if(count($array_id_unidades) == 1){
									$id_unidad = $array_id_unidades[0];
								}elseif(count($array_id_unidades) == 2){
									if($array_id_unidades[0] == 18 && $array_id_unidades[1] != 18){
										$id_unidad = $array_id_unidades[1];
									}elseif($array_id_unidades[0] != 18 && $array_id_unidades[1] == 18){
										$id_unidad = $array_id_unidades[0];
									}elseif(in_array(9, $array_id_unidades) && in_array(1, $array_id_unidades)){
										$id_unidad = 5;
									}elseif(in_array(9, $array_id_unidades) && in_array(2, $array_id_unidades)){
										$id_unidad = 6;
									}elseif(in_array(3, $array_id_unidades) && in_array(14, $array_id_unidades)){// m3 x hectarea
										$id_unidad = 3;
									}else{
										$id_unidad = $array_id_unidades[0];
									}
								}elseif(count($array_id_unidades) == 3){
									if(
										in_array(18, $array_id_unidades) && 
										in_array(9, $array_id_unidades) && 
										in_array(1, $array_id_unidades)
									){
										$id_unidad = 5;
									}elseif(
										in_array(18, $array_id_unidades) && 
										in_array(9, $array_id_unidades) && 
										in_array(2, $array_id_unidades)
									){
										$id_unidad = 6;
									}else{
										
									}
								}else{
									
								}

								// Al total hay que multiplicarlo por el factor correspondiente
								$fila_factor = $this->Characterization_factors_model->get_one_where(
									array(
										"id_bd" => $id_bd,
										"id_metodologia" => $id_metodologia,
										"id_huella" => $id_huella,
										"id_material" => $id_material,
										"id_categoria" => $id_categoria,
										"id_subcategoria" => $id_subcategoria,
										"id_unidad" => $id_unidad,
										"deleted" => 0
									)
								);

								$valor_factor = 0;
								if($fila_factor->id){
									$valor_factor = $fila_factor->factor;
								}

								$elementos = $this->Calculation_model->get_records_of_forms_for_calculation($id_proyecto, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria, $first_date_current_year, $last_date_current_year)->result();
								foreach($elementos as $elemento){
									$total_elemento = 0;
									$datos_decoded = json_decode($elemento->datos, true);
									
									$mult = 1;
									foreach($ides_campo_unidad as $id_campo_unidad){
										if($id_campo_unidad == 0){
											$mult *= $datos_decoded["unidad_residuo"];
										}else{
											$mult *= $datos_decoded[$id_campo_unidad];
										}
									}
									
									$total_elemento = $mult * $valor_factor;
									$total_calculo += $total_elemento;
									
								}

								$total_huella += $total_calculo;
								
							}// FIN EACH CALCULO
							
							$total_huella *= $valor_transformacion;
							
							$array_data_total_impacts["water_environmental_footprints"][$id_huella]["nombre"] = $huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.')';
							$array_data_total_impacts["water_environmental_footprints"][$id_huella]["icono"] = $icono;
							$array_data_total_impacts["water_environmental_footprints"][$id_huella]["total_huella"] += $total_huella;

							// PARA CALCULO DE HUELLAS FIJAS
							if($huella->id == 30){
								$total_huella_ud = $total_huella;
							}
							if($huella->id == 31){
								$total_huella_ui = $total_huella;
							}
							if($huella->id == 32){
								$total_huella_sl = $total_huella;
							}
							if($huella->id == 33){
								$total_huella_se = $total_huella;
							}

						} // FIN HUELLAS DINÁMICAS

						$icono = base_url("assets/images/impact-category/18 huellas-04.png");

						$huella_ap = ($total_huella_ud + $total_huella_ui) - ($total_huella_sl + $total_huella_se);
						$array_data_total_impacts["water_environmental_footprints"]["water_in_product"]["nombre"] = lang("water_in_product").' ('.$nombre_unidad_huella.' '.$huella->indicador.')';
						$array_data_total_impacts["water_environmental_footprints"]["water_in_product"]["icono"] = $icono;
						$array_data_total_impacts["water_environmental_footprints"]["water_in_product"]["total_huella"] += $huella_ap;

						$huella_ac = ($huella_ap + $total_huella_se);
						$array_data_total_impacts["water_environmental_footprints"]["consumed_water"]["nombre"] = lang("consumed_water").' ('.$nombre_unidad_huella.' '.$huella->indicador.')';
						$array_data_total_impacts["water_environmental_footprints"]["consumed_water"]["icono"] = $icono;
						$array_data_total_impacts["water_environmental_footprints"]["consumed_water"]["total_huella"] += $huella_ac;

					}

				}

			} // FIN DATA IMPACTOS TOTALES


			// DATA IMPACTOS TOTALES POR UNIDAD FUNCIONAL
			if($visible_impacts_by_functional_units) {

				$uf_huella = array();
				$uf_huella_carbon = array();
				$uf_huella_water = array();

				foreach($calculos as $calculo){

					$total_elemento = 0;
					$datos_decoded = json_decode($calculo->datos, true);

					// VALORES Y UNIDAD FINAL
					$ides_campo_unidad = json_decode($calculo->id_campo_unidad, true);
					$array_unidades = array();
					$array_id_unidades = array();
					$array_id_tipo_unidades = array();

					foreach($ides_campo_unidad as $id_campo_unidad){
				
						if($id_campo_unidad == 0){
							$id_formulario = $calculo->id_formulario;
							$json_unidad_form = json_decode($calculo->formulario_unidad, true);
							
							$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
							$id_unidad = $json_unidad_form["unidad_id"];
							
							$array_unidades[] = $unidades[$id_unidad];;
							$array_id_unidades[] = $id_unidad;
							$array_id_tipo_unidades[] = $id_tipo_unidad;
						}else{
							$info_campo = json_decode($campos_unidad[$id_campo_unidad], true);
							$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
							$id_unidad = $info_campo[0]["id_unidad"];
							
							$array_unidades[] = $unidades[$id_unidad];;
							$array_id_unidades[] = $id_unidad;
							$array_id_tipo_unidades[] = $id_tipo_unidad;
						}
					}

					if(count($array_id_unidades) == 1){
						$id_unidad = $array_id_unidades[0];
					}elseif(count($array_id_unidades) == 2){
						if($array_id_unidades[0] == 18 && $array_id_unidades[1] != 18){
							$id_unidad = $array_id_unidades[1];
						}elseif($array_id_unidades[0] != 18 && $array_id_unidades[1] == 18){
							$id_unidad = $array_id_unidades[0];
						}elseif(in_array(9, $array_id_unidades) && in_array(1, $array_id_unidades)){
							$id_unidad = 5;
						}elseif(in_array(9, $array_id_unidades) && in_array(2, $array_id_unidades)){
							$id_unidad = 6;
						}elseif(in_array(3, $array_id_unidades) && in_array(14, $array_id_unidades)){// m3 x hectarea
							$id_unidad = 3;
						}else{
							$id_unidad = $array_id_unidades[0];
						}
					}elseif(count($array_id_unidades) == 3){
						
						if(in_array(18, $array_id_unidades) && in_array(9, $array_id_unidades) && in_array(1, $array_id_unidades)){
							$id_unidad = 5;
						}elseif(in_array(18, $array_id_unidades) && in_array(9, $array_id_unidades) && in_array(2, $array_id_unidades)){
							$id_unidad = 6;
						}else{
							
						}
					}else{
						
					}

					// MULTIPLICACIÓN DE MULTIPLES VALORES
					$mult = 1;
					foreach($ides_campo_unidad as $id_campo_unidad){
						if($id_campo_unidad == 0){
							$mult *= $calculo->valor;
						}else{
							$mult *= $datos_decoded[$id_campo_unidad];
						}
					}

					$id_campo_sp = $calculo->id_campo_sp;
					$id_campo_pu = $calculo->id_campo_pu;
					$id_campo_fc = $calculo->id_campo_fc;

					$criterio_sp = $calculo->criterio_sp;
					$tipo_asignacion_sp = $calculo->tipo_asignacion_sp;
					$sp_destino = $calculo->sp_destino;
					$porcentajes_sp = $calculo->porcentajes_sp;

					$criterio_pu = $calculo->criterio_pu;
					$tipo_asignacion_pu = $calculo->tipo_asignacion_pu;
					$pu_destino = $calculo->pu_destino;
					$porcentajes_pu = $calculo->porcentajes_pu;

					

					if(!$id_campo_sp && !$id_campo_pu && $tipo_asignacion_sp == "Total"){

						if(in_array(1, $ids_metodologia)){
							foreach($huellas as $huella){
	
								$valor_factor = 0;
								if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
									$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
								}
	
								$uf_huella[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);
							}
						}
						if(in_array(2, $ids_metodologia)){
							foreach($huellas_carbon as $huella){
								
								$valor_factor = 0;
								if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
									$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
								}
	
								$uf_huella_carbon[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);
							}
						}
						if(in_array(3, $ids_metodologia)){
							foreach($huellas_water as $huella){
								
								$valor_factor = 0;
								if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
									$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
								}
	
								$uf_huella_water[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);
							}
						}

					}

					if($id_campo_sp && !$id_campo_pu){

						if($id_campo_sp == "tipo_tratamiento"){
							$valor_campo_sp = $tipo_tratamiento[$datos_decoded[$id_campo_sp]];
						}elseif($id_campo_sp == "id_sucursal"){
							$valor_campo_sp = $sucursales[$datos_decoded[$id_campo_sp]];
						}elseif($id_campo_sp == "type_of_origin_matter"){
							$valor_campo_sp = lang($type_of_origin_matter[$datos_decoded[$id_campo_sp]]);
						}elseif($id_campo_sp == "type_of_origin"){
							$valor_campo_sp = lang($type_of_origin[$datos_decoded[$id_campo_sp]]);
						}elseif($id_campo_sp == "default_type"){
							$valor_campo_sp = lang($default_type[$datos_decoded[$id_campo_sp]]);
						}else{
							$valor_campo_sp = $datos_decoded[$id_campo_sp];
						}
							
						if($tipo_asignacion_sp == "Total" && $criterio_sp == $valor_campo_sp){
							
							if(in_array(1, $ids_metodologia)){
								foreach($huellas as $huella){
		
									$valor_factor = 0;
									if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
										$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
									}
		
									$uf_huella[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);
								}
							}
							if(in_array(2, $ids_metodologia)){
								foreach($huellas_carbon as $huella){
		
									$valor_factor = 0;
									if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
										$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
									}
		
									$uf_huella_carbon[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);
								}
							}
							if(in_array(3, $ids_metodologia)){
								foreach($huellas_water as $huella){
		
									$valor_factor = 0;
									if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
										$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
									}
		
									$uf_huella_water[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);
								}
							}
							
						}else if($tipo_asignacion_sp == "Porcentual" && $criterio_sp == $valor_campo_sp){
							
							if(in_array(1, $ids_metodologia)){
								foreach($huellas as $huella){
		
									$valor_factor = 0;
									if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
										$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
									}
		
									$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
									foreach($porcentajes_sp_decoded as $id_sp => $porcentaje_sp){
										if($porcentaje_sp != 0){
											$porcentaje_sp = ($porcentaje_sp/100);
										}
		
										$uf_huella[$sp_uf[$id_sp]][$huella->id][] = (float)(($mult * $valor_factor) * $porcentaje_sp);
									}
								}
							}
							if(in_array(2, $ids_metodologia)){
								foreach($huellas_carbon as $huella){
		
									$valor_factor = 0;
									if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
										$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
									}
		
									$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
									foreach($porcentajes_sp_decoded as $id_sp => $porcentaje_sp){
										if($porcentaje_sp != 0){
											$porcentaje_sp = ($porcentaje_sp/100);
										}
		
										$uf_huella_carbon[$sp_uf[$id_sp]][$huella->id][] = (float)(($mult * $valor_factor) * $porcentaje_sp);
									}
								}
							}
							if(in_array(3, $ids_metodologia)){
								foreach($huellas_water as $huella){
		
									$valor_factor = 0;
									if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
										$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
									}
		
									$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
									foreach($porcentajes_sp_decoded as $id_sp => $porcentaje_sp){
										if($porcentaje_sp != 0){
											$porcentaje_sp = ($porcentaje_sp/100);
										}
		
										$uf_huella_water[$sp_uf[$id_sp]][$huella->id][] = (float)(($mult * $valor_factor) * $porcentaje_sp);
									}
								}
							}
							
						}
					}

					if(!$id_campo_sp && $id_campo_pu){
			
						if($id_campo_pu == "tipo_tratamiento"){
							$valor_campo_pu = $tipo_tratamiento[$datos_decoded[$id_campo_pu]];
						}elseif($id_campo_pu == "id_sucursal"){
							$valor_campo_pu = $sucursales[$datos_decoded[$id_campo_pu]];
						}elseif($id_campo_pu == "type_of_origin_matter"){
							$valor_campo_pu = lang($type_of_origin_matter[$datos_decoded[$id_campo_pu]]);
						}elseif($id_campo_pu == "type_of_origin"){
							$valor_campo_pu = lang($type_of_origin[$datos_decoded[$id_campo_pu]]);
						}elseif($id_campo_pu == "default_type"){
							$valor_campo_pu = lang($default_type[$datos_decoded[$id_campo_pu]]);
						}else{
							$valor_campo_pu = $datos_decoded[$id_campo_pu];
						}
						
						if($criterio_pu == $valor_campo_pu){
							
							if(in_array(1, $ids_metodologia)){
								foreach($huellas as $huella){
		
									$valor_factor = 0;
									if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
										$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
									}
		
									$uf_huella[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);
								}
							}
							if(in_array(2, $ids_metodologia)){
								foreach($huellas_carbon as $huella){
		
									$valor_factor = 0;
									if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
										$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
									}
		
									$uf_huella_carbon[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);
								}
							}
							if(in_array(3, $ids_metodologia)){
								foreach($huellas_water as $huella){
		
									$valor_factor = 0;
									if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
										$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
									}
		
									$uf_huella_water[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);
								}
							}
						}
					}

					if($id_campo_sp && $id_campo_pu){
			
						if($id_campo_sp == "tipo_tratamiento"){
							$valor_campo_sp = $tipo_tratamiento[$datos_decoded[$id_campo_sp]];
						}elseif($id_campo_sp == "id_sucursal"){
							$valor_campo_sp = $sucursales[$datos_decoded[$id_campo_sp]];
						}elseif($id_campo_sp == "type_of_origin_matter"){
							$valor_campo_sp = lang($type_of_origin_matter[$datos_decoded[$id_campo_sp]]);
						}elseif($id_campo_sp == "type_of_origin"){
							$valor_campo_sp = lang($type_of_origin[$datos_decoded[$id_campo_sp]]);
						}elseif($id_campo_sp == "default_type"){
							$valor_campo_sp = lang($default_type[$datos_decoded[$id_campo_sp]]);
						}else{
							$valor_campo_sp = $datos_decoded[$id_campo_sp];
						}
		
						if($id_campo_pu == "tipo_tratamiento"){
							$valor_campo_pu = $tipo_tratamiento[$datos_decoded[$id_campo_pu]];
						}elseif($id_campo_pu == "id_sucursal"){
							$valor_campo_pu = $sucursales[$datos_decoded[$id_campo_pu]];
						}elseif($id_campo_pu == "type_of_origin_matter"){
							$valor_campo_pu = lang($type_of_origin_matter[$datos_decoded[$id_campo_pu]]);
						}elseif($id_campo_pu == "type_of_origin"){
							$valor_campo_pu = lang($type_of_origin[$datos_decoded[$id_campo_pu]]);
						}elseif($id_campo_pu == "default_type"){
							$valor_campo_pu = lang($default_type[$datos_decoded[$id_campo_pu]]);
						}else{
							$valor_campo_pu = $datos_decoded[$id_campo_pu];
						}
						
						if($tipo_asignacion_sp == "Total"){
							
							if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
								
								if(in_array(1, $ids_metodologia)){
									foreach($huellas as $huella){
		
										$valor_factor = 0;
										if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
											$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
										}
		
										$uf_huella[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);
									}
								}
								if(in_array(2, $ids_metodologia)){
									foreach($huellas_carbon as $huella){
		
										$valor_factor = 0;
										if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
											$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
										}
		
										$uf_huella_carbon[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);
									}
								}
								if(in_array(3, $ids_metodologia)){
									foreach($huellas_water as $huella){
		
										$valor_factor = 0;
										if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
											$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
										}
		
										$uf_huella_water[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);
									}
								}
							}
							
						}else if($tipo_asignacion_sp == "Porcentual"){
		
							if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
								
								if(in_array(1, $ids_metodologia)){
									foreach($huellas as $huella){
		
										$valor_factor = 0;
										if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
											$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
										}
		
										$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
										foreach($porcentajes_sp_decoded as $id_sp => $porcentaje_sp){
											if($porcentaje_sp != 0){
												$porcentaje_sp = ($porcentaje_sp/100);
											}
		
											$uf_huella[$sp_uf[$id_sp]][$huella->id][] = (float)(($mult * $valor_factor) * $porcentaje_sp);
										}
									}
								}
								if(in_array(2, $ids_metodologia)){
									foreach($huellas_carbon as $huella){
		
										$valor_factor = 0;
										if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
											$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
										}
		
										$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
										foreach($porcentajes_sp_decoded as $id_sp => $porcentaje_sp){
											if($porcentaje_sp != 0){
												$porcentaje_sp = ($porcentaje_sp/100);
											}
		
											$uf_huella_carbon[$sp_uf[$id_sp]][$huella->id][] = (float)(($mult * $valor_factor) * $porcentaje_sp);
										}
									}
								}
								if(in_array(3, $ids_metodologia)){
									foreach($huellas_water as $huella){
		
										$valor_factor = 0;
										if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
											$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
										}
		
										$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
										foreach($porcentajes_sp_decoded as $id_sp => $porcentaje_sp){
											if($porcentaje_sp != 0){
												$porcentaje_sp = ($porcentaje_sp/100);
											}
		
											$uf_huella_water[$sp_uf[$id_sp]][$huella->id][] = (float)(($mult * $valor_factor) * $porcentaje_sp);
										}
									}
								}
							}
							
						}

					}

				} // FIN FOREACH CÁLCULOS

				$array_data_impacts_by_functional_units[$project->id]["uf_huella"] = $uf_huella;
				$array_data_impacts_by_functional_units[$project->id]["uf_huella_carbon"] = $uf_huella_carbon;
				$array_data_impacts_by_functional_units[$project->id]["uf_huella_water"] = $uf_huella_water;

			} // FIN DATA IMPACTOS TOTALES POR UNIDAD FUNCIONAL

		} // FIN IF DISPONIBILIDAD MÓDULO HUELLAS

	} // FIN FOREACH PROYECTOS

?>

<!-- SECCIÓN IMPACTOS POR UNIDAD FUNCIONAL -->
<div class="row">
	<div class="col-md-12 col-sm-12 widget-container">
		<div class="panel panel-white">
			<div class="panel-heading" style="background-color:#00b393;color:white;"><h3><?php echo lang("impacts_by_functional_units"); ?></h3></div>
			<div class="panel-group" id="accordion_impacts_by_uf-<?php echo $id_proyecto; ?>">

				<?php foreach($array_data_impacts_by_functional_units as $id_proyecto => $data_footprint){ ?>

					<?php // echo "<pre>";print_r($data_footprint); echo "</pre>";echo "<br><br>"; ?>
					<?php
						// $id_proyecto = $proyecto->id;
						$project = $this->Projects_model->get_one($id_proyecto);
						$unidades_funcionales = $array_data_by_project[$project->id]['unidades_funcionales'];
						$ids_metodologia = json_decode($project->id_metodologia);

						$huellas = $array_data_by_project[$project->id]["huellas"];
						$huellas_carbon = $array_data_by_project[$project->id]["huellas_carbon"];
						$huellas_water = $array_data_by_project[$project->id]["huellas_water"];
						$array_transformaciones = $array_data_by_project[$project->id]["array_transformaciones"];
						$uf_huella = $data_footprint["uf_huella"];
						$uf_huella_carbon = $data_footprint["uf_huella_carbon"];
						$uf_huella_water = $data_footprint["uf_huella_water"];

					?>

					<div class="panel panel-white">

						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" href="#collapse-<?php echo $id_proyecto; ?>" data-parent="#" class="accordion-toggle">

									<div class="row">
										<div class="col-md-8">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> 
												<?php echo $project->title; ?>
											</h4>
										</div>
									</div>

								</a>
							</h4>
						</div>

						<div id="collapse-<?php echo $id_proyecto; ?>" class="panel-collapse collapse ">
						
							<div id="div_slide_impacts_by_uf-<?php echo $id_proyecto; ?>" class="panel-body p30 hide">
								
								<?php // echo $id_proyecto; ?>

								<?php foreach($unidades_funcionales as $unidad_funcional){ ?>

									<div class="widget-container">
										
											<div class="page-title clearfix panel-success">
												<h1><?php echo lang("environmental_impacts_by") . ' ' . $unidad_funcional->unidad. ' ' . lang("of") . ' ' . $unidad_funcional->nombre; ?></h1>
											</div>

											
											
												<div class="panel-body">
									
													<?php
														$nombre_uf = $unidad_funcional->nombre;
														$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
														$valor_uf = get_functional_unit_value($client_id, $id_proyecto, $unidad_funcional->id, $first_date_current_year, $last_date_current_year);
													?>

													<div class="slider_total_impacts slider">


														<!-- Si el proyecto tiene la metodología con id 1 (ReCiPe 2008, midpoint (H) [v1.11, December 2014]) -->
														<?php if(in_array(1, $ids_metodologia)){ ?>

															<div> <!-- INICIO DIV HUELLAS ACV -->

																<div class="col-md-12 p0">
																	<h4><?php echo lang("environmental_footprints"); ?></h4>
																</div>

																<?php
																
																	$html = '';

																	if(count($huellas)){
																		foreach($huellas as $huella){

																			$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
																				"id_cliente" => $client_id, 
																				"id_proyecto" => $id_proyecto, 
																				"id_tipo_unidad" => $huella->id_tipo_unidad, 
																				"deleted" => 0
																			))->id_unidad;
							
																			$nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;

																			$valor = (string)((array_sum($uf_huella[$unidad_funcional->id][$huella->id])/$valor_uf) * $array_transformaciones[$huella->id]);
																			$icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");
																			$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
																			$html .= '<div class="text-center p15" style="height: 80px; width: 80px; margin: auto;"><img src="'.$icono.'" alt="..." height="50" width="50" class="mCS_img_loaded"></div>';
																			$html .= '<div class="text-center p15">'.to_number_project_format($valor, $id_proyecto).'</div>';
																			$html .= '<div class="pt10 pb10 b-b"> '.$huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
																			$html .= '</div>';
																		}
																	
																	} else {
																		
																		$html .= '<div class="app-alert alert alert-warning alert-dismissible mb0 col-md-12" style="float: left;">';
																		$html .= lang("project_without_footprints");
																		$html .= '</div>';

																	}
																	echo $html;
																?>

															</div> <!-- FIN DIV HUELLAS AMBIENTALES -->

														<?php } ?>

														<!-- Si el proyecto tiene la metodología con id 2 (GHG Protocol) -->
														<?php if(in_array(2, $ids_metodologia)){ ?>

															<div> <!-- INICIO DIV HUELLA DE CARBONO -->

																<div class="col-md-12 p0">
																	<h4><?php echo lang("carbon_environmental_footprints"); ?></h4>
																</div>

																<?php
																
																	$html = '';
																	if(count($huellas_carbon)){
																		foreach($huellas_carbon as $huella){

																			$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
																				"id_cliente" => $client_id, 
																				"id_proyecto" => $id_proyecto, 
																				"id_tipo_unidad" => $huella->id_tipo_unidad, 
																				"deleted" => 0
																			))->id_unidad;
							
																			$nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;

																			$valor = (string)((array_sum($uf_huella_carbon[$unidad_funcional->id][$huella->id])/$valor_uf) * $array_transformaciones[$huella->id]);
																			$icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");
																			$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
																			$html .= '<div class="text-center p15" style="height: 80px; width: 80px; margin: auto;"><img src="'.$icono.'" alt="..." height="50" width="50" class="mCS_img_loaded"></div>';
																			$html .= '<div class="text-center p15">'.to_number_project_format($valor, $id_proyecto).'</div>';
																			$html .= '<div class="pt10 pb10 b-b"> '.$huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
																			$html .= '</div>';
																		}

																	} else {

																		$html .= '<div class="app-alert alert alert-warning alert-dismissible mb0 col-md-12" style="float: left;">';
																		$html .= lang("project_without_footprints_carbon");
																		$html .= '</div>';

																	}
																	echo $html;
																?>

															</div> <!-- FIN DIV HUELLA DE CARBONO -->

														<?php } ?>

														<!-- Si el proyecto tiene la metodología con id 3 (Huella de Agua) -->
														<?php if(in_array(3, $ids_metodologia)){ ?>

															<div> <!-- INICIO DIV HUELLA DE AGUA -->

																<div class="col-md-12 p0">
																	<h4><?php echo lang("water_environmental_footprints"); ?></h4>
																</div>

																<?php
																
																	$html = '';

																	if(count($huellas_water)){

																		foreach($huellas_water as $huella){

																			$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
																				"id_cliente" => $client_id, 
																				"id_proyecto" => $id_proyecto, 
																				"id_tipo_unidad" => $huella->id_tipo_unidad, 
																				"deleted" => 0
																			))->id_unidad;
							
																			$nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;

																			$valor = (string)((array_sum($uf_huella_water[$unidad_funcional->id][$huella->id])/$valor_uf) * $array_transformaciones[$huella->id]);
																			$icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");
																			$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
																			$html .= '<div class="text-center p15" style="height: 80px; width: 80px; margin: auto;"><img src="'.$icono.'" alt="..." height="50" width="50" class="mCS_img_loaded"></div>';
																			
																			$html .= '<div class="text-center p15">'.to_number_project_format($valor, $id_proyecto).'</div>';
																			$html .= '<div class="pt10 pb10 b-b"> '.$huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
																			$html .= '</div>';

																			// PARA CALCULO DE HUELLAS FIJAS
																			if($huella->id == 30){
																				$total_huella_ud = array_sum($uf_huella_water[$unidad_funcional->id][$huella->id]);
																			}
																			if($huella->id == 31){
																				$total_huella_ui = array_sum($uf_huella_water[$unidad_funcional->id][$huella->id]);
																			}
																			if($huella->id == 32){
																				$total_huella_sl = array_sum($uf_huella_water[$unidad_funcional->id][$huella->id]);
																			}
																			if($huella->id == 33){
																				$total_huella_se = array_sum($uf_huella_water[$unidad_funcional->id][$huella->id]);
																			}
																			//

																		} // FIN HUELLAS DINÁMICAS


																		$icono = base_url("assets/images/impact-category/18 huellas-04.png");

																		$huella_ap = ($total_huella_ud + $total_huella_ui) - ($total_huella_sl + $total_huella_se);
																		$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
																		$html .= '<div class="text-center p15" style="height: 80px; width: 80px; margin: auto;"><img src="'.$icono.'" alt="..." class="mCS_img_loaded"></div>';
																		$html .= '<div class="text-center p15">'.to_number_project_format($huella_ap, $id_proyecto).'</div>';
																		$html .= '<div class="pt10 pb10 b-b"> '.lang("water_in_product").' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
																		$html .= '</div>';

																		$huella_ac = ($huella_ap + $total_huella_se);
																		$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
																		$html .= '<div class="text-center p15" style="height: 80px; width: 80px; margin: auto;""><img src="'.$icono.'" alt="..." class="mCS_img_loaded"></div>';
																		$html .= '<div class="text-center p15">'.to_number_project_format($huella_ac, $id_proyecto).'</div>';
																		$html .= '<div class="pt10 pb10 b-b"> '.lang("consumed_water").' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
																		$html .= '</div>';

																	} else {

																		$html .= '<div class="app-alert alert alert-warning alert-dismissible mb0 col-md-12" style="float: left;">';
																		$html .= lang("project_without_footprints_water");
																		$html .= '</div>';

																	}
																	echo $html;
																?>

															</div> <!-- FIN DIV HUELLA DE AGUA -->

														<?php } ?>

													</div>

												</div>
											
										
									</div>

								<?php } ?>
						
							</div>
							
						</div>

					</div>

				<?php } ?>

			</div>
		</div>
	</div>
</div>
<!-- FIN SECCIÓN IMPACTOS POR UNIDAD FUNCIONAL -->

<!-- SECCIÓN IMPACTOS TOTALES -->
<div class="row">
	<div class="col-md-12 col-sm-12 widget-container">
		<div class="panel panel-white">
			<div class="panel-heading" style="background-color:#00b393;color:white;"><h3><?php echo lang("total_impacts"); ?></h3></div>
			<div class="panel-heading">
				<div class="panel-body">

					<div id="div_slide_total_impacts" class="slider_total_impacts slider">

						
						<?php foreach($array_data_total_impacts as $footprint => $data_footprint){ ?>

							<div> <!-- Un div por cada slider (por cada tipo de huella) -->
								<div class="col-md-12 p0">
									<h4><?php echo lang($footprint); ?></h4>
								</div>

								<?php foreach($data_footprint as $id_footprint => $data){ ?>

									<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">
										<div class="text-center p15" style="height: 80px; width: 80px; margin: auto;"><img src="<?php echo $data["icono"]; ?>" alt="..." class="mCS_img_loaded"></div>
										<div class="text-center p15"><?php echo to_number_client_format($data["total_huella"], $client_id); ?></div>
										<div class="pt10 pb10 b-b"><?php echo $data["nombre"]; ?></div>
									</div>

								<?php } ?>
							</div>

						<?php } ?>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<!-- FIN SECCIÓN IMPACTOS TOTALES -->

<script type="text/javascript">
$(document).ready(function () {

	$('.slider_total_impacts').slick({
		dots: false,
		infinite: false,
		speed: 300,
		slidesToShow: 1,
		// variableWidth: true,
		slidesToScroll: 1
	});

	// SE REFRESCA EL SLIDER DESPUÉS DE UN TIEMPO PARA QUE SE DESPLIEGUE DE FORMA CORRECTA
	$(document).on("show.bs.collapse", ".collapse", function(e) {

		var id_project = e.target.id.split('-')[1];

		if ($(this).is(e.target)) {
			setTimeout(function(){
				$("#div_slide_impacts_by_uf-"+id_project).removeClass("hide");
				$(".slick-slider").slick("refresh");
			}, 100);
		}
	});

	$(document).on("hide.bs.collapse", ".collapse", function(e) {

		var id_project = e.target.id.split('-')[1];

		if ($(this).is(e.target)) {
			setTimeout(function(){
				$("#div_slide_impacts_by_uf-"+id_project).addClass("hide");
			}, 100);
		}
	});

	setTimeout(function(){
		$("#div_slide_total_impacts").slick("refresh");
	}, 100);
	
	var maxHeight = Math.max.apply(null, $("#page-content .huella").map(function (){
		return $(this).find("div.b-b").height();
	}));
	$("#page-content .huella > div.b-b").height(maxHeight);
	
});
</script> 