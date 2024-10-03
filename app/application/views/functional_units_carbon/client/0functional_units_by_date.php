
  <div class="row">
        <?php foreach($unidades_funcionales as $unidad_funcional){ ?>
        <div class="col-md-12 col-sm-12">
          <div class="panel panel-default">
            <div class="page-title clearfix panel-success">
              <h1><?php echo lang("environmental_impacts_by") . ' ' . $unidad_funcional->unidad. ' ' . lang("of") . ' ' . $unidad_funcional->nombre; ?></h1>
            </div>
            <div class="panel-body">
            <?php
			
			$id_proyecto = $proyecto->id;
			//$id_metodologia = $proyecto->id_metodologia;
			$ids_metodologia = json_decode($proyecto->id_metodologia);
			
			$nombre_uf = $unidad_funcional->nombre;
			$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
			//$valor_uf = $unidad_funcional->valor;
			$valor_uf = get_functional_unit_value($client_info->id, $proyecto->id, $unidad_funcional->id, $start_date, $end_date);
			//var_dump($client_info->id);
			//var_dump($proyecto->id);
			//var_dump($unidad_funcional->id);
			//var_dump($start_date);
			//var_dump($end_date);
			//var_dump($valor_uf);
            
            $html = '';
            foreach($huellas as $huella){
				//var_dump($criterios_calculos);
				
                $id_huella = $huella->id;
				$total_huella = 0;
				//$nombre_unidad_huella = $this->Unity_model->get_one($huella->id_unidad)->nombre;
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
				
				$icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");
				$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
				$html .= '<div class="text-center p15"><img src="'.$icono.'" alt="..." height="50" width="50" class="mCS_img_loaded"></div>';
				
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
						$id_bd = $criterio_calculo->id_bd;
						
						/*
						$id_campo_sp = $criterio_calculo->id_campo_sp;
						$id_campo_pu = $criterio_calculo->id_campo_pu;
						$id_campo_fc = $criterio_calculo->id_campo_fc;
						$criterio_fc = $criterio_calculo->criterio_fc;
						*/
						
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
												//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.($total_elemento_interno).'<br>';
											}
											
										}else if($tipo_asignacion_sp == "Porcentual" && $pu_destino == $id_pu){
											
											$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
											$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
											if($porcentaje_sp != 0){
												$porcentaje_sp = ($porcentaje_sp/100);
											}
											
											if($criterio_sp == $valor_campo_sp){
												$total_elemento += ($total_elemento_interno * $porcentaje_sp);
												//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_sp.'<br>';
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
												//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.($total_elemento_interno).'<br>';
											}
											
										}else if($tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
											
											$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
											$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
											if($porcentaje_pu != 0){
												$porcentaje_pu = ($porcentaje_pu/100);
											}
											
											if($criterio_pu == $valor_campo_pu){
												$total_elemento += ($total_elemento_interno * $porcentaje_pu);
												//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_pu.'<br>';
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
												//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.'<br>';
											}
											
										}else if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
											$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
											$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
											if($porcentaje_pu != 0){
												$porcentaje_pu = ($porcentaje_pu/100);
											}
											
											if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
												$total_elemento += ($total_elemento_interno * $porcentaje_pu);
												//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_pu.'<br>';
											}
											
										}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Total" && $pu_destino == $id_pu){
											
											$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
											$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
											if($porcentaje_sp != 0){
												$porcentaje_sp = ($porcentaje_sp/100);
											}
											
											if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
												$total_elemento += ($total_elemento_interno * $porcentaje_sp);
												//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_sp.'<br>';
											}
											
										}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Porcentual"){
											
											//echo $porcentajes_sp.'|'.$porcentajes_pu.'<br>';

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
												//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_sp.' * '.$porcentaje_pu.'<br>';
											}
										}
									}
								}
								
								if(!$id_campo_sp && !$id_campo_pu){
									//var_dump($asignaciones_de_criterio);
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
											
											//if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
												$total_elemento += $total_elemento_interno;
											//}
											
										}
									}
								}
								
								$total_criterio += $total_elemento;
							}// FIN ELEMENTO

						}// FIN METODOLOGIA

						$total_pu += $total_criterio;
						
						
					}// FIN CRITERIO-CALCULO
					
					$total_pu = $total_pu/$valor_uf;
					$total_huella += $total_pu;
				
				}// FIN PROCESO UNITARIO
                
				$total_huella *= $valor_transformacion;
				
                //$total_huella_por_uf = ($array_cifras_huellas[$id_huella])/$unidad_funcional->valor;
				//$total_huella_por_uf = ($total_huella)/$unidad_funcional->valor;
                    
                //$html .= '<div class="text-center p15">2,07*10<sup>2</sup></div>';
                $html .= '<div class="text-center p15">'.to_number_project_format($total_huella,$id_proyecto).'</div>';
                $html .= '<div class="pt10 pb10 b-b"> '.$huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
                $html .= '</div>';
            }
            echo $html;
            ?>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
  
  
  
<script type="text/javascript">
    $(document).ready(function () {
	});
</script> 