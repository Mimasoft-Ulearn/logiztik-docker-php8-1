<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $proyecto->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo ucwords(lang("water_environmental_footprints"))." - ".lang("functional_units"); ?>
</h2>

<?php if($start_date && $end_date){ ?>
	<div align="center">
		<i><?php echo lang("corresponding_to_date_range")." ".$rango_fechas; ?></i>
	</div>
<?php } ?>

<div align="center">
	<?php $hora = convert_to_general_settings_time_format($proyecto->id, convert_date_utc_to_local(get_current_utc_time("H:i:s"), $format = "H:i:s", $proyecto->id));  ?>
	<?php echo lang("datetime_download") . ": " . get_date_format(date('Y-m-d'), $proyecto->id).' '.lang("at").' '.$hora; ?>
</div>

  <?php if($puede_ver == 1) { ?>

		<br><br>
		<table cellspacing="0" cellpadding="4" border="0">
			<tr>
				<td align="center"><img src="<?php echo $grafico_impactos_por_huella; ?>" style="height:380px; width:570px;" /></td>
			</tr>
		</table>
		<br pagebreak="true">
		<table cellspacing="0" cellpadding="4" border="0">
			<tr>
				<td align="center"><img src="<?php echo $grafico_proporcion_mensual ?>" style="height:380px; width:570px;" /></td>
			</tr>
		</table>
		<br pagebreak="true">
  
		<h2><?php echo lang("environmental_impacts_by") . ' ' . $unidad_funcional->unidad. ' ' . lang("of") . ' ' . $unidad_funcional->nombre; ?></h2>
		<br>

		<?php
		
		$id_proyecto = $proyecto->id;
		$ids_metodologia = json_decode($proyecto->id_metodologia);
		
		$nombre_uf = $unidad_funcional->nombre;
		$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
		$valor_uf = get_functional_unit_value($client_info->id, $proyecto->id, $unidad_funcional->id, $start_date, $end_date);
		
		$html = '';
		
		$html .= '<div style="width: 100%;">';
		$html .= '<table cellspacing="0" cellpadding="0" border="0">';
		
		$loop = 1;
		
		foreach($huellas as $huella){
			
			if($loop % 4 == 1){
				$html .= '<tr>';
			}
			
			$html .= '<td style="text-align: center;">';

			$html .= '<table style="float: left;" border="0">';
			$html .= '<tr>';
			$html .= '<td style="text-align: center;">';
			
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
			$fila_config_huella = $Module_footprint_units_model->get_one_where(
				array(
					"id_cliente" => $client_id,
					"id_proyecto" => $id_proyecto,
					"id_tipo_unidad" => $id_tipo_unidad_origen,
					"deleted" => 0
				)
			);
			$id_unidad_destino = $fila_config_huella->id_unidad;
			$fila_conversion = $Conversion_model->get_one_where(
				array(
					"id_tipo_unidad" => $id_tipo_unidad_origen,
					"id_unidad_origen" => $id_unidad_origen,
					"id_unidad_destino" => $id_unidad_destino
				)
			);
			$valor_transformacion = $fila_conversion->transformacion;
			// FIN VALOR DE CONVERSION
			
			//$icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");
			$icono = $huella->icono ? "assets/images/impact-category/".$huella->icono : "assets/images/impact-category/empty.png";
			$html .= '<img src="'.$icono.'" style="height:50px; width:50px;" />';
			$html .= "<br>";
			
			foreach($procesos_unitarios as $pu){
				
				$id_pu = $pu["id"];
				$nombre_pu = $pu["nombre"];
				$total_pu = 0;
				
				foreach($criterios_calculos as $criterio_calculo){
					
					$total_criterio = 0;
					
					$id_criterio = $criterio_calculo->id_criterio;
					$id_formulario = $criterio_calculo->id_formulario;
					$id_material = $criterio_calculo->id_material;
					$id_categoria = $criterio_calculo->id_categoria;
					$id_subcategoria = $criterio_calculo->id_subcategoria;
					$id_metodologia = $criterio_calculo->id_metodologia;
					$id_bd = $criterio_calculo->id_bd;
					
					$fields_criteria = get_fields_criteria($criterio_calculo);
					$id_campo_sp = $fields_criteria->id_campo_sp;
					$id_campo_pu = $fields_criteria->id_campo_pu;
					$id_campo_fc = $fields_criteria->id_campo_fc;
					$criterio_fc = $fields_criteria->criterio_fc;
					
					$ides_campo_unidad = json_decode($criterio_calculo->id_campo_unidad, true);
					
					// NUEVA ASIGNACION
					// CONSULTAR TODAS ASIGNACIONES DEL CRITERIO-CALCULO 
					$asignaciones_de_criterio = $Assignment_combinations_model->get_details(array("id_criterio" => $id_criterio))->result();
					
					// CONSULTAR CAMPOS UNIDAD DEL RA
					$array_unidades = array();
					$array_id_unidades = array();
					$array_id_tipo_unidades = array();
					
					foreach($ides_campo_unidad as $id_campo_unidad){
						
						if($id_campo_unidad == 0){
							$id_formulario = $criterio_calculo->id_formulario;
							$form_data = $Forms_model->get_one_where(array("id"=>$id_formulario, "deleted"=>0));
							$json_unidad_form = json_decode($form_data->unidad,true);
							
							$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
							$id_unidad = $json_unidad_form["unidad_id"];
							
							$fila_unidad = $Unity_model->get_one_where(array("id"=>$id_unidad, "deleted"=>0));
							$array_unidades[] = $fila_unidad->nombre;
							$array_id_unidades[] = $id_unidad;
							$array_id_tipo_unidades[] = $id_tipo_unidad;
						}else{
							$fila_campo = $Fields_model->get_one_where(array("id"=>$id_campo_unidad,"deleted"=>0));
							$info_campo = $fila_campo->opciones;
							$info_campo = json_decode($info_campo, true);
							
							$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
							$id_unidad = $info_campo[0]["id_unidad"];
							
							$fila_unidad = $Unity_model->get_one_where(array("id"=>$id_unidad,"deleted"=>0));
							$array_unidades[] = $fila_unidad->nombre;
							$array_id_unidades[] = $id_unidad;
							$array_id_tipo_unidades[] = $id_tipo_unidad;
						}
						// Para graficos
						//$array_unidades_proyecto[$id_unidad] = $fila_unidad->nombre;
					}
					
					
					// OBTENER UNIDAD FINAL
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

					foreach($ids_metodologia as $id_metodologia){
					
						// CONSULTAR FC
						$fila_factor = $Characterization_factors_model->get_one_where(
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
						
						// UNA VEZ QUE YA TENGO FC PARA A NIVEL DE CRITERIO(RA) - CALCULO, RECORRO LOS ELEMENTOS ASOCIADOS
						$elementos = $Calculation_model->get_records_of_forms_for_calculation($id_proyecto, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria, $start_date, $end_date)->result();

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
							// AL CALCULAR A NIVEL DE ELEMENTO, EL RESULTADO MULTIPLICARLO POR EL FC
							$total_elemento_interno = $mult * $valor_factor;
							// IF VALOR DE CAMPO DE CRITERIO SP EN CRITERIO = VALOR DE CRITERIO SP DE ARRAY DE ASIGNACIONES Y
							// VALOR DE CAMPO DE CITERIO PU EN CRITERIO = VALOR DE CRITERIO UF DE ARRAY DE ASIGNACIONES
							
							if($id_campo_sp && !$id_campo_pu){
								
								if($id_campo_sp == "tipo_tratamiento"){
									$value= $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_sp]);
									$valor_campo_sp = $value->nombre;
								}elseif($id_campo_sp == "type_of_origin_matter"){
									$value= $this->EC_Types_of_origin_matter_model->get_one($datos_decoded[$id_campo_sp]);
									$valor_campo_sp = lang($value->nombre);
								}elseif($id_campo_sp == "type_of_origin"){
									$value= $this->EC_Types_of_origin_model->get_one($datos_decoded[$id_campo_sp]);
									$valor_campo_sp = lang($value->nombre);
								}elseif($id_campo_sp == "default_type"){
									$value= $this->EC_Types_no_apply_model->get_one($datos_decoded[$id_campo_sp]);
									$valor_campo_sp = lang($value->nombre);
								}elseif($id_campo_sp == "id_source"){
									$value = $this->Sources_model->get_one($datos_decoded[$id_campo_sp]);
									$valor_campo_sp = lang($value->name);
								}else{
									$valor_campo_sp = $datos_decoded[$id_campo_sp];
								}
								
								foreach($asignaciones_de_criterio as $obj_asignacion){
									
									$criterio_sp = $obj_asignacion->criterio_sp;
									$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
									$sp_destino = $obj_asignacion->sp_destino;
									$porcentajes_sp = $obj_asignacion->porcentajes_sp;
									
									$criterio_pu = $obj_asignacion->criterio_pu;
									$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
									$pu_destino = $obj_asignacion->pu_destino;
									$porcentajes_pu = $obj_asignacion->porcentajes_pu;
									
									if($tipo_asignacion_sp == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
										
										if($criterio_sp == $valor_campo_sp){
											$total_elemento += $total_elemento_interno;
										}
										
									}else if($tipo_asignacion_sp == "Porcentual" && $pu_destino == $id_pu){
										
										$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
										$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
										if($porcentaje_sp != 0){
											$porcentaje_sp = ($porcentaje_sp/100);
										}
										
										if($criterio_sp == $valor_campo_sp){
											$total_elemento += ($total_elemento_interno * $porcentaje_sp);
										}
									}
								}
							}
							
							if(!$id_campo_sp && $id_campo_pu){
								
								if($id_campo_pu == "tipo_tratamiento"){
									$value= $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_pu]);
									$valor_campo_pu = $value->nombre;
								}elseif($id_campo_pu == "type_of_origin_matter"){
									$value= $this->EC_Types_of_origin_matter_model->get_one($datos_decoded[$id_campo_pu]);
									$valor_campo_pu = lang($value->nombre);
								}elseif($id_campo_pu == "type_of_origin"){
									$value= $this->EC_Types_of_origin_model->get_one($datos_decoded[$id_campo_pu]);
									$valor_campo_pu = lang($value->nombre);
								}elseif($id_campo_pu == "default_type"){
									$value= $this->EC_Types_no_apply_model->get_one($datos_decoded[$id_campo_pu]);
									$valor_campo_pu = lang($value->nombre);
								}elseif($id_campo_pu == "id_source"){
									$value = $this->Sources_model->get_one($datos_decoded[$id_campo_pu]);
									$valor_campo_pu = lang($value->name);
								}else{
									$valor_campo_pu = $datos_decoded[$id_campo_pu];
								}
								
								foreach($asignaciones_de_criterio as $obj_asignacion){
									
									
									$criterio_sp = $obj_asignacion->criterio_sp;
									$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
									$sp_destino = $obj_asignacion->sp_destino;
									$porcentajes_sp = $obj_asignacion->porcentajes_sp;
									
									$criterio_pu = $obj_asignacion->criterio_pu;
									$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
									$pu_destino = $obj_asignacion->pu_destino;
									$porcentajes_pu = $obj_asignacion->porcentajes_pu;
									
									if($tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
										
										if($criterio_pu == $valor_campo_pu){
											$total_elemento += $total_elemento_interno;
										}
										
									}else if($tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
										
										$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
										$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
										if($porcentaje_pu != 0){
											$porcentaje_pu = ($porcentaje_pu/100);
										}
										
										if($criterio_pu == $valor_campo_pu){
											$total_elemento += ($total_elemento_interno * $porcentaje_pu);
										}
										
									}
									
									
								}
							}
							
							
							if($id_campo_sp && $id_campo_pu){
								
								if($id_campo_pu == "tipo_tratamiento"){
									$value_pu= $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_pu]);
									$valor_campo_pu = $value_pu->nombre;
								}elseif($id_campo_pu == "type_of_origin_matter"){
									$value_pu= $this->EC_Types_of_origin_matter_model->get_one($datos_decoded[$id_campo_pu]);
									$valor_campo_pu = lang($value_pu->nombre);
								}elseif($id_campo_pu == "type_of_origin"){
									$value_pu= $this->EC_Types_of_origin_model->get_one($datos_decoded[$id_campo_pu]);
									$valor_campo_pu = lang($value_pu->nombre);
								}elseif($id_campo_pu == "default_type"){
									$value_pu= $this->EC_Types_no_apply_model->get_one($datos_decoded[$id_campo_pu]);
									$valor_campo_pu = lang($value_pu->nombre);
								} elseif($id_campo_pu == "id_source"){
									$value_pu = $this->Sources_model->get_one($datos_decoded[$id_campo_pu]);
									$valor_campo_pu = lang($value_pu->name);
								} else {
									$valor_campo_pu = $datos_decoded[$id_campo_pu];
								}

								if($id_campo_sp == "tipo_tratamiento"){
									$value_sp= $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_sp]);
									$valor_campo_sp = $value_sp->nombre;
								}elseif($id_campo_sp == "type_of_origin_matter"){
									$value_sp= $this->EC_Types_of_origin_matter_model->get_one($datos_decoded[$id_campo_sp]);
									$valor_campo_sp = lang($value_sp->nombre);
								}elseif($id_campo_sp == "type_of_origin"){
									$value_sp= $this->EC_Types_of_origin_model->get_one($datos_decoded[$id_campo_sp]);
									$valor_campo_sp = lang($value_sp->nombre);
								}elseif($id_campo_sp == "default_type"){
									$value_sp= $this->EC_Types_no_apply_model->get_one($datos_decoded[$id_campo_sp]);
									$valor_campo_sp = lang($value_sp->nombre);
								} elseif($id_campo_sp == "id_source"){
									$value_sp = $this->Sources_model->get_one($datos_decoded[$id_campo_sp]);
									$valor_campo_sp = lang($value_sp->name);
								} else {
									$valor_campo_sp = $datos_decoded[$id_campo_sp];
								}
								
								foreach($asignaciones_de_criterio as $obj_asignacion){
									
									$criterio_sp = $obj_asignacion->criterio_sp;
									$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
									$sp_destino = $obj_asignacion->sp_destino;
									$porcentajes_sp = $obj_asignacion->porcentajes_sp;
									
									$criterio_pu = $obj_asignacion->criterio_pu;
									$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
									$pu_destino = $obj_asignacion->pu_destino;
									$porcentajes_pu = $obj_asignacion->porcentajes_pu;
									
									if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
										
										if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
											$total_elemento += $total_elemento_interno;
										}
										
									}else if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
										$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
										$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
										if($porcentaje_pu != 0){
											$porcentaje_pu = ($porcentaje_pu/100);
										}
										
										if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
											$total_elemento += ($total_elemento_interno * $porcentaje_pu);
										}
										
									}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Total" && $pu_destino == $id_pu){
										
										$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
										$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
										if($porcentaje_sp != 0){
											$porcentaje_sp = ($porcentaje_sp/100);
										}
										
										if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
											$total_elemento += ($total_elemento_interno * $porcentaje_sp);
										}
										
									}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Porcentual"){

										$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
										$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
										if($porcentaje_sp != 0){
											$porcentaje_sp = ($porcentaje_sp/100);
										}

										$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
										$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
										if($porcentaje_pu != 0){
											$porcentaje_pu = ($porcentaje_pu/100);
										}
										
										if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
											$total_elemento += ($total_elemento_interno * $porcentaje_sp * $porcentaje_pu);
										}
									}
								}
							}
							
							if(!$id_campo_sp && !$id_campo_pu){
								
								foreach($asignaciones_de_criterio as $obj_asignacion){
									
									$criterio_sp = $obj_asignacion->criterio_sp;
									$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
									$sp_destino = $obj_asignacion->sp_destino;
									$porcentajes_sp = $obj_asignacion->porcentajes_sp;
									
									$criterio_pu = $obj_asignacion->criterio_pu;
									$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
									$pu_destino = $obj_asignacion->pu_destino;
									$porcentajes_pu = $obj_asignacion->porcentajes_pu;
									
									if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
										$total_elemento += $total_elemento_interno;
									}
								}
							}

							$fecha_elemento = new DateTime($datos_decoded["fecha"]);
							$short_month = lang("short_".strtolower($fecha_elemento->format('F')));

							if($start_date && $end_date){
								$start_date_dt = new DateTime($start_date);
								$end_date_dt = new DateTime($end_date);
								if($fecha_elemento >= $start_date_dt && $fecha_elemento <= $end_date_dt){
									$total_criterio += $total_elemento;

									$array_meses_datos[$unidad_funcional->id][$huella->id][$short_month."-".$fecha_elemento->format("y")] += $total_elemento;
								}
							}else{
								if(in_array($short_month."-".$fecha_elemento->format("y"), $array_categorias_fechas)){
									$total_criterio += $total_elemento;
								}

								$array_meses_datos[$unidad_funcional->id][$huella->id][$short_month."-".$fecha_elemento->format("y")] += $total_elemento;
							}
							
							
						}// FIN ELEMENTO

					}// FIN METODOLOGÍA


					$total_pu += $total_criterio;
					
				}// FIN CRITERIO-CALCULO
				
				$total_pu = $total_pu/$valor_uf;
				$total_huella += $total_pu;
			
			}// FIN PROCESO UNITARIO
			
			$total_huella *= $valor_transformacion;

			$html .= to_number_project_format($total_huella,$id_proyecto).'<br>';
			$html .= $huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.') <br><br>';

			$html .= '</td>';
			$html .= '</tr>';
			$html .= '</table>';
			
			$html .= '</td>';
			
			if($loop % 4 == 0 || $loop == count($huellas)){
				$html .= '</tr>';
			}
			
			$loop++;

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
			//
			
		}

		$icono = "assets/images/impact-category/18 huellas-04.png";

		$html .= '<tr>';
		$html .= '<td style="text-align: center;">';
		$html .= '<table style="float: left;" border="0">';
		$html .= '<tr>';
		$huella_ap = ($total_huella_ud + $total_huella_ui) - ($total_huella_sl + $total_huella_se);
		$html .= '<td style="text-align: center;">';
		$html .= '<img src="'.$icono.'" style="height:50px; width:50px;" />';
		$html .= '<br>';
		$html .= to_number_project_format($huella_ap, $id_proyecto).'<br>';
		$html .= lang("water_in_product").' ('.$nombre_unidad_huella.' '.$huella->indicador.') <br><br>';
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';
		$html .= '</td>';

		$html .= '<td style="text-align: center;">';
		$html .= '<table style="float: left;" border="0">';
		$html .= '<tr>';
		$huella_ac = ($huella_ap + $total_huella_se);
		$html .= '<td style="text-align: center;">';
		$html .= '<img src="'.$icono.'" style="height:50px; width:50px;" />';
		$html .= '<br>';
		$html .= to_number_project_format($huella_ac, $id_proyecto).'<br>';
		$html .= lang("consumed_water").' ('.$nombre_unidad_huella.' '.$huella->indicador.') <br><br>';
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';
		$html .= '</td>';

		
		
		$html .= '</tr>';

		
		$html .= '</table>';
		$html .= '</div>';
		echo $html;
		?>
		
	<!-- <br pagebreak="true"> -->
	


  <?php } else { ?>
  
  <div style="width: 100%;"> 
  	<?php echo lang("content_disabled"); ?>
  </div>
  
  <?php } ?>

</body>