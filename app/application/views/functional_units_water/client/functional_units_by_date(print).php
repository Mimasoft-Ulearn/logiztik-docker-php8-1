<div class="col-sm-3 col-lg-2">
<ul class="nav nav-tabs vertical" role="tablist">
	<?php foreach($unidades_funcionales as $key => $unidad_funcional){ ?>
	<?php $active = ($key == 0)? "active":""; ?>
		<li class="<?php echo $active; ?>"><a data-toggle="tab" href="#<?php echo $unidad_funcional->id; ?>_unidad_funcional"><?php echo lang("environmental_impacts_by") . ' ' . $unidad_funcional->unidad. ' ' . lang("of") . ' ' . $unidad_funcional->nombre; ?></a></li>
	<?php } ?>
</ul>
</div>

<div role="tabpanel" class="tab-pane fade active in" id="graficos_procesos" style="min-height: 200px;">
	<div class="tab-content">
	<?php
	$array_meses_datos = array();
	$array_impactos_chart_data = array();
	$array_meses_huellas = array();

	$array_print = array();
	?>
	<?php foreach($unidades_funcionales as $key => $unidad_funcional){ ?>
	<?php $active = ($key == 0)? "active":""; ?>
		<div id="<?php echo $unidad_funcional->id; ?>_unidad_funcional" class="tab-pane fade in <?php echo $active; ?>">
			<div class="col-sm-9 col-lg-10 p0">
				<div class="panel">

					<div class="page-title clearfix">
						<h1><?php echo $unidad_funcional->nombre; ?></h1>
						<a href="#" class="btn btn-danger pull-right" id="functional_units_pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a>
					</div>
					<div class="panel-body p0">

						<div class="panel panel-default">
							<div class="page-title clearfix panel-success">
								<h1>Impactos mensuales</h1>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12 text-center">
										<div id="impactos_uf_<?php echo $unidad_funcional->id?>" style="" class="chart">
											<div style="padding:20px;"><div class="circle-loader"></div></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="page-title clearfix panel-success">
								<h1>Impactos mensuales</h1>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12 text-center">
										<div id="proporcion_uf_<?php echo $unidad_funcional->id; ?>" style="" class="chart">
											<div style="padding:20px;"><div class="circle-loader"></div></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="page-title clearfix panel-success">
								<h1><?php echo lang("environmental_impacts_by") . ' ' . $unidad_funcional->unidad. ' ' . lang("of") . ' ' . $unidad_funcional->nombre; ?></h1>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12 text-center">
									
							
						<?php

						$id_proyecto = $proyecto->id;
						//$ids_metodologia = json_decode($proyecto->id_metodologia);

						$nombre_uf = $unidad_funcional->nombre;
						$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
						$valor_uf = get_functional_unit_value($client_info->id, $proyecto->id, $unidad_funcional->id, $start_date, $end_date);
						
						$html = '';
						foreach($huellas as $huella){
							
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

									//foreach($ids_metodologia as $id_metodologia){
									
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

										// SOLO PARA MOTIVOS DEL PRINT
										$fecha_elemento = new DateTime($datos_decoded["fecha"]);
										$short_month = lang("short_".strtolower($fecha_elemento->format('F')));
										$start_date_dt = new DateTime($start_date);
										$end_date_dt = new DateTime($end_date);
										//
										
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

														// PRINT
														if($start_date && $end_date){
															if($fecha_elemento >= $start_date_dt && $fecha_elemento <= $end_date_dt){
																if($this->login_user->id == 4 && ($huella->id == 30 || $huella->id == 31)) {

																	$array_print[] = array(
																		$id_subproyecto_uf,
																		$huella->id,
																		$id_pu,
																		$criterio_calculo->id_campo_sp,
																		$criterio_calculo->id_campo_pu,
																		$criterio_calculo->tipo_by_criterio,
																		$valor_campo_sp,
																		$valor_campo_pu,
																		$tipo_asignacion_sp,
																		$tipo_asignacion_pu,
																		$datos_decoded["id_categoria"],
																		$short_month."-".$fecha_elemento->format("y"),
																		$mult,
																		$valor_factor,
																		$total_elemento_interno,
																		NULL,
																		NULL,
																	);
																}
															}
														}
														// END PRINT


													}
													
												}else if($tipo_asignacion_sp == "Porcentual" && $pu_destino == $id_pu){
													
													$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
													$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
													if($porcentaje_sp != 0){
														$porcentaje_sp = ($porcentaje_sp/100);
													}
													
													if($criterio_sp == $valor_campo_sp){
														$total_elemento += ($total_elemento_interno * $porcentaje_sp);

														// PRINT
														if($start_date && $end_date){
															if($fecha_elemento >= $start_date_dt && $fecha_elemento <= $end_date_dt){
																if($this->login_user->id == 4 && ($huella->id == 30 || $huella->id == 31)) {

																	$array_print[] = array(
																		$id_subproyecto_uf,
																		$huella->id,
																		$id_pu,
																		$criterio_calculo->id_campo_sp,
																		$criterio_calculo->id_campo_pu,
																		$criterio_calculo->tipo_by_criterio,
																		$valor_campo_sp,
																		$valor_campo_pu,
																		$tipo_asignacion_sp,
																		$tipo_asignacion_pu,
																		$datos_decoded["id_categoria"],
																		$short_month."-".$fecha_elemento->format("y"),
																		$mult,
																		$valor_factor,
																		$total_elemento_interno,
																		$porcentaje_sp,
																		NULL,
																	);
																	
																}
															}
														}
														// END PRINT

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

														// PRINT
														if($start_date && $end_date){
															if($fecha_elemento >= $start_date_dt && $fecha_elemento <= $end_date_dt){
																if($this->login_user->id == 4 && ($huella->id == 30 || $huella->id == 31)) {

																	$array_print[] = array(
																		$id_subproyecto_uf,
																		$huella->id,
																		$id_pu,
																		$criterio_calculo->id_campo_sp,
																		$criterio_calculo->id_campo_pu,
																		$criterio_calculo->tipo_by_criterio,
																		$valor_campo_sp,
																		$valor_campo_pu,
																		$tipo_asignacion_sp,
																		$tipo_asignacion_pu,
																		$datos_decoded["id_categoria"],
																		$short_month."-".$fecha_elemento->format("y"),
																		$mult,
																		$valor_factor,
																		$total_elemento_interno,
																		NULL,
																		NULL,
																	);
																}
															}
														}
														// END PRINT


													}
													
												}else if($tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
													
													$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
													$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
													if($porcentaje_pu != 0){
														$porcentaje_pu = ($porcentaje_pu/100);
													}
													
													if($criterio_pu == $valor_campo_pu){
														$total_elemento += ($total_elemento_interno * $porcentaje_pu);

														// PRINT
														if($start_date && $end_date){
															if($fecha_elemento >= $start_date_dt && $fecha_elemento <= $end_date_dt){
																if($this->login_user->id == 4 && ($huella->id == 30 || $huella->id == 31)) {

																	$array_print[] = array(
																		$id_subproyecto_uf,
																		$huella->id,
																		$id_pu,
																		$criterio_calculo->id_campo_sp,
																		$criterio_calculo->id_campo_pu,
																		$criterio_calculo->tipo_by_criterio,
																		$valor_campo_sp,
																		$valor_campo_pu,
																		$tipo_asignacion_sp,
																		$tipo_asignacion_pu,
																		$datos_decoded["id_categoria"],
																		$short_month."-".$fecha_elemento->format("y"),
																		$mult,
																		$valor_factor,
																		$total_elemento_interno,
																		NULL,
																		$porcentaje_pu,
																	);
			
																}
															}
														}
														// END PRINT
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

														// PRINT
														if($start_date && $end_date){
															if($fecha_elemento >= $start_date_dt && $fecha_elemento <= $end_date_dt){
																if($this->login_user->id == 4 && ($huella->id == 30 || $huella->id == 31)) {

																	$array_print[] = array(
																		$id_subproyecto_uf,
																		$huella->id,
																		$id_pu,
																		$criterio_calculo->id_campo_sp,
																		$criterio_calculo->id_campo_pu,
																		$criterio_calculo->tipo_by_criterio,
																		$valor_campo_sp,
																		$valor_campo_pu,
																		$tipo_asignacion_sp,
																		$tipo_asignacion_pu,
																		$datos_decoded["id_categoria"],
																		$short_month."-".$fecha_elemento->format("y"),
																		$mult,
																		$valor_factor,
																		$total_elemento_interno,
																		NULL,
																		NULL,
																	);

																}
															}
														}
														// END PRINT
													}
													
												}else if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
													$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
													$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
													if($porcentaje_pu != 0){
														$porcentaje_pu = ($porcentaje_pu/100);
													}
													
													if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
														$total_elemento += ($total_elemento_interno * $porcentaje_pu);

														// PRINT
														if($start_date && $end_date){
															if($fecha_elemento >= $start_date_dt && $fecha_elemento <= $end_date_dt){
																if($this->login_user->id == 4 && ($huella->id == 30 || $huella->id == 31)) {

																	$array_print[] = array(
																		$id_subproyecto_uf,
																		$huella->id,
																		$id_pu,
																		$criterio_calculo->id_campo_sp,
																		$criterio_calculo->id_campo_pu,
																		$criterio_calculo->tipo_by_criterio,
																		$valor_campo_sp,
																		$valor_campo_pu,
																		$tipo_asignacion_sp,
																		$tipo_asignacion_pu,
																		$datos_decoded["id_categoria"],
																		$short_month."-".$fecha_elemento->format("y"),
																		$mult,
																		$valor_factor,
																		$total_elemento_interno,
																		NULL,
																		$porcentaje_pu,
																	);
																}
															}
														}
														// END PRINT
													}
													
												}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Total" && $pu_destino == $id_pu){
													
													$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
													$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
													if($porcentaje_sp != 0){
														$porcentaje_sp = ($porcentaje_sp/100);
													}
													
													if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
														$total_elemento += ($total_elemento_interno * $porcentaje_sp);

														// PRINT
														if($start_date && $end_date){
															if($fecha_elemento >= $start_date_dt && $fecha_elemento <= $end_date_dt){
																if($this->login_user->id == 4 && ($huella->id == 30 || $huella->id == 31)) {

																	$array_print[] = array(
																		$id_subproyecto_uf,
																		$huella->id,
																		$id_pu,
																		$criterio_calculo->id_campo_sp,
																		$criterio_calculo->id_campo_pu,
																		$criterio_calculo->tipo_by_criterio,
																		$valor_campo_sp,
																		$valor_campo_pu,
																		$tipo_asignacion_sp,
																		$tipo_asignacion_pu,
																		$datos_decoded["id_categoria"],
																		$short_month."-".$fecha_elemento->format("y"),
																		$mult,
																		$valor_factor,
																		$total_elemento_interno,
																		$porcentaje_sp,
																		NULL,
																	);
																	
																}
															}
														}
														// END PRINT

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

														// PRINT
														if($start_date && $end_date){
															if($fecha_elemento >= $start_date_dt && $fecha_elemento <= $end_date_dt){
																if($this->login_user->id == 4 && ($huella->id == 30 || $huella->id == 31)) {

																	$array_print[] = array(
																		$id_subproyecto_uf,
																		$huella->id,
																		$id_pu,
																		$criterio_calculo->id_campo_sp,
																		$criterio_calculo->id_campo_pu,
																		$criterio_calculo->tipo_by_criterio,
																		$valor_campo_sp,
																		$valor_campo_pu,
																		$tipo_asignacion_sp,
																		$tipo_asignacion_pu,
																		$datos_decoded["id_categoria"],
																		$short_month."-".$fecha_elemento->format("y"),
																		$mult,
																		$valor_factor,
																		$total_elemento_interno,
																		$porcentaje_sp,
																		$porcentaje_pu,
																	);
																	
																}
															}
														}
														// END PRINT


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

													// PRINT
													if($start_date && $end_date){
														if($fecha_elemento >= $start_date_dt && $fecha_elemento <= $end_date_dt){
															if($this->login_user->id == 4 && ($huella->id == 30 || $huella->id == 31)) {

																$array_print[] = array(
																	$id_subproyecto_uf,
																	$huella->id,
																	$id_pu,
																	$criterio_calculo->id_campo_sp,
																	$criterio_calculo->id_campo_pu,
																	$criterio_calculo->tipo_by_criterio,
																	$valor_campo_sp,
																	$valor_campo_pu,
																	$tipo_asignacion_sp,
																	$tipo_asignacion_pu,
																	$datos_decoded["id_categoria"],
																	$short_month."-".$fecha_elemento->format("y"),
																	$mult,
																	$valor_factor,
																	$total_elemento_interno,
																	NULL,
																	NULL,
																);
																
															}
														}
													}
													// END PRINT

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

												$array_meses_huellas[$unidad_funcional->id][$short_month."-".$fecha_elemento->format("y")][$huella->id] += $total_elemento;

												if($this->login_user->id == 4 && ($huella->id == 30 || $huella->id == 31)) {

													//echo 'id_huella: '.$huella->id.' | total elemento: '.$mult.' (* '.$valor_factor.') = '.$total_elemento_interno.' | mes: '.$short_month."-".$fecha_elemento->format("y").' | id_campo_sp: '.$criterio_calculo->id_campo_sp.' | id_campo_pu: '.$criterio_calculo->id_campo_pu.' | valor_campo_sp: '.$valor_campo_sp.' | valor_campo_pu: '.$valor_campo_pu.' | Total: <strong>'.$total_elemento.'</strong> | '.$criterio_calculo->tipo_by_criterio.' | id_categoría: '.$datos_decoded["id_categoria"].'<br><br>';
													//echo 'Total: <strong>'.$total_elemento.'</strong><br><br>';
													/*$array_print[] = array(
														'',
														'',
														'',
														'',
														'',
														'',
														'',
														'',
														'',
														'',
														'',
														'',
														'',
														'',
														'',
														'',
														$total_elemento,
													);*/
													
												}
												
											}
										}else{
											if(in_array($short_month."-".$fecha_elemento->format("y"), $array_categorias_fechas)){
												$total_criterio += $total_elemento;
											}

											$array_meses_datos[$unidad_funcional->id][$huella->id][$short_month."-".$fecha_elemento->format("y")] += $total_elemento;

											$array_meses_huellas[$unidad_funcional->id][$short_month."-".$fecha_elemento->format("y")][$huella->id] += $total_elemento;
										}
										

										//$array_porcentaje_datos[$unidad_funcional->id][$short_month."-".$fecha_elemento->format("y")][$huella->id] += $total_elemento;


									}// FIN ELEMENTO

									//}// FIN METODOLOGIA
			
									$total_pu += $total_criterio;
									
								}// FIN CRITERIO-CALCULO
								
								$total_pu = $total_pu/$valor_uf;
								$total_huella += $total_pu;
							
							}// FIN PROCESO UNITARIO
							
							$total_huella *= $valor_transformacion;
							
							//$total_huella_por_uf = ($array_cifras_huellas[$id_huella])/$unidad_funcional->valor;
							//$total_huella_por_uf = ($total_huella)/$unidad_funcional->valor;
								
							$html .= '<div class="text-center p15">'.to_number_project_format($total_huella,$id_proyecto).'</div>';
							$html .= '<div class="pt10 pb10 b-b"> '.$huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
							$html .= '</div>';

							// ALIMENTAR GRÁFICO
							$array_impactos_data = array();
							foreach ($array_categorias_fechas as $key => $month) {

								if(isset($array_meses_datos[$unidad_funcional->id][$huella->id][$month])){
									$fecha_inicio_mes = $array_periodos[$month]['start_date'];
									$fecha_termino_mes = $array_periodos[$month]['end_date'];
									$valor_uf_mes = get_functional_unit_value($client_info->id, $proyecto->id, $unidad_funcional->id, $fecha_inicio_mes, $fecha_termino_mes);

									$valor_mensual = (float)$array_meses_datos[$unidad_funcional->id][$huella->id][$month];
									$array_impactos_data[] = ($valor_mensual/$valor_uf_mes) * $valor_transformacion;
								}else{
									$array_impactos_data[] = 0;
								}
							}
							$array_impactos_chart_data[$unidad_funcional->id][] = array(
								"name" => $huella->nombre, 
								"data" => $array_impactos_data
							);
							//

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

						}// FIN HUELLAS DÍNAMICAS

						// HUELLAS FIJAS

						//var_dump($array_meses_huellas[1]["Ene-20"][30]);

						// ALIMENTAR HUELLAS FIJAS EN GRAFICOS
						$array_impactos_data_ap = array();
						$array_impactos_data_ac = array();
						foreach ($array_categorias_fechas as $key => $month) {

							$fecha_inicio_mes = $array_periodos[$month]['start_date'];
							$fecha_termino_mes = $array_periodos[$month]['end_date'];
							$valor_uf_mes = get_functional_unit_value($client_info->id, $proyecto->id, $unidad_funcional->id, $fecha_inicio_mes, $fecha_termino_mes);

							//$valor_mensual = (float)$array_meses_datos[$unidad_funcional->id][$huella->id][$month];
							//$array_impactos_data[] = ($valor_mensual/$valor_uf_mes) * $valor_transformacion;

							if(isset($array_meses_huellas[$unidad_funcional->id][$month][30])){
								
								$mes_huella_ud = (float)$array_meses_huellas[$unidad_funcional->id][$month][30];
								$mes_huella_ud = ($mes_huella_ud/$valor_uf_mes) * $valor_transformacion;
							}else{
								$mes_huella_ud = 0;
							}
							if(isset($array_meses_huellas[$unidad_funcional->id][$month][31])){
								$mes_huella_ui = (float)$array_meses_huellas[$unidad_funcional->id][$month][31];
								$mes_huella_ui = ($mes_huella_ui/$valor_uf_mes) * $valor_transformacion;
							}else{
								$mes_huella_ui = 0;
							}
							if(isset($array_meses_huellas[$unidad_funcional->id][$month][32])){
								$mes_huella_sl = (float)$array_meses_huellas[$unidad_funcional->id][$month][32];
								$mes_huella_sl = ($mes_huella_sl/$valor_uf_mes) * $valor_transformacion;
							}else{
								$mes_huella_sl = 0;
							}
							if(isset($array_meses_huellas[$unidad_funcional->id][$month][33])){
								$mes_huella_se = (float)$array_meses_huellas[$unidad_funcional->id][$month][33];
								$mes_huella_se = ($mes_huella_se/$valor_uf_mes) * $valor_transformacion;
							}else{
								$mes_huella_se = 0;
							}

							$data_ap = ($mes_huella_ud + $mes_huella_ui) - ($mes_huella_sl + $mes_huella_se);
							$array_impactos_data_ap[] = $data_ap;
							$array_impactos_data_ac[] = $data_ap + $mes_huella_se;
							
						}
						$array_impactos_chart_data[$unidad_funcional->id][] = array(
							"name" => lang("water_in_product"), 
							"data" => $array_impactos_data_ap
						);
						$array_impactos_chart_data[$unidad_funcional->id][] = array(
							"name" => lang("consumed_water"), 
							"data" => $array_impactos_data_ac
						);
						//

						$icono = $huella->icono ? base_url("assets/images/impact-category/18 huellas-04.png") : base_url("assets/images/impact-category/empty.png");

						$huella_ap = ($total_huella_ud + $total_huella_ui) - ($total_huella_sl + $total_huella_se);
						$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
						$html .= '<div class="text-center p15"><img src="'.$icono.'" alt="..." height="50" width="50" class="mCS_img_loaded"></div>';
$html .= '<div class="text-center p15">'.to_number_project_format($huella_ap, $id_proyecto).'</div>';
						$html .= '<div class="pt10 pb10 b-b"> '.lang("water_in_product").' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
						$html .= '</div>';

						$huella_ac = ($huella_ap + $total_huella_se);
						$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
						$html .= '<div class="text-center p15"><img src="'.$icono.'" alt="..." height="50" width="50" class="mCS_img_loaded"></div>';
$html .= '<div class="text-center p15">'.to_number_project_format($huella_ac, $id_proyecto).'</div>';
						$html .= '<div class="pt10 pb10 b-b"> '.lang("consumed_water").' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
						$html .= '</div>';


						echo $html;
						?>
									</div>
								</div>
							</div>
						</div>


						<?php if($this->login_user->id == 4 && $id_subproyecto_uf == 1) { ?>
							<div class="panel panel-default">
								<div class="table-responsive">
									<table id="calculos" class="display" cellspacing="0" width="100%"></table>
								</div>
							</div>
						<?php } ?>

					</div>
				</div>
			</div>
		</div>
	<?php } ?>

	





	</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

		/*var datos = <?php echo json_encode($array_print); ?>;

		$("#calculos").dataTable({
			data: datos,
			lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
			columns: [
				{title: "id_subproyecto_uf", "class": "text-center w50"},
				{title: "id_huella", "class": "text-center w50"},
				{title: "id_pu", "class": "text-center w50"},
				{title: "CRITERIO - id_campo_sp", "class": "text-center w50"},
				{title: "CRITERIO - id_campo_pu", "class": "text-center w50"},
				{title: "CRITERIO - tipo_by_criterio", "class": "text-center w50"},
				{title: "valor_campo_sp", "class": "text-center w50"},
				{title: "valor_campo_pu", "class": "text-center w50"},
				{title: "tipo_asignacion_sp", "class": "text-center w50"},
				{title: "tipo_asignacion_pu", "class": "text-center w50"},
				{title: "id_categoria", "class": "text-center w50"},
				{title: "mes", "class": "text-center w50"},
				{title: "valor", "class": "text-center w50"},
				{title: "*factor", "class": "text-center w50"},
				{title: "total", "class": "text-center w50"},
				{title: "% SP", "class": "text-center w50"},
				{title: "% PU", "class": "text-center w50"},
			],
		});*/

		$('#page-content a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('.chart').each(function() { 
                $(this).highcharts().reflow();
            });
        });

		//General Settings
        var decimals_separator = AppHelper.settings.decimalSeparator;
        var thousands_separator = AppHelper.settings.thousandSeparator;
        var decimal_numbers = AppHelper.settings.decimalNumbers;

		<?php foreach($unidades_funcionales as $key => $unidad_funcional){ ?>
			<?php
			$nombre_grafico = $client_info->sigla.'_'.$project_info->sigla.'_FU_impacts_'.date("Y-m-d");
			?>

			$('#impactos_uf_<?php echo $unidad_funcional->id; ?>').highcharts({
				chart: {
					type: 'line'
					/*plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					events: {
						load: function() {
							if (this.options.chart.forExport) {
								Highcharts.each(this.series, function (series) {
									series.update({
										dataLabels: {
											enabled: true,
										}
									}, false);
								});
								this.redraw();
							}
						}
					}*/
				},
				title: {
					text: 'Impactos por Huella'
				},
				credits: {
					enabled: false
				},
				tooltip: {
					valueSuffix: ' <?php echo $unidad_volumen.' eq / '.$unidad_funcional->nombre; ?>',
					shared: true,
					headerFormat: "<small>{point.key}</small><table>",
					pointFormatter: function(){
						var valueSuffix = this.series.tooltipOptions.valueSuffix || "";
						return '<tr><td style="color:'+this.series.color+';padding:0">● <span style="color:#333333;">'+this.series.name+': </span> </td>'+'<td style="padding:0; font-weight:bold;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+' '+valueSuffix+'</b></td></tr>';
					},
					footerFormat:"</table>",
					useHTML: true
				},
				xAxis: {
					categories: <?php echo json_encode($array_categorias_fechas); ?>
				},
				yAxis: {
					title: {
						text: "<?php echo $unidad_volumen.' eq / '.$unidad_funcional->nombre; ?>",
						useHTML: true,
					}
				},
				plotOptions: {
					line: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							format: '{point.y:.' + decimal_numbers + 'f}',
							/*style: {
								//color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
								fontSize: "9px",
								distance: -30
							},*/
							crop: false
						},
						showInLegend: true
					}
				},
				exporting: {
					filename: "<?php echo $nombre_grafico; ?>",
					buttons: {
						contextButton: {
							menuItems: [{
								text: "<?php echo lang('export_to_png'); ?>",
								onclick: function() {
									this.exportChart();
								},
								separator: false
							}]
						}
					}
				},
				series: <?php echo json_encode($array_impactos_chart_data[$unidad_funcional->id]); ?>,
			});

			$('#proporcion_uf_<?php echo $unidad_funcional->id; ?>').highcharts({
				chart: {
					type: 'column',
					events: {
					load: function() {
						if (this.options.chart.forExport) {
							Highcharts.each(this.series, function (series) {
								series.update({
									dataLabels: {
										enabled: true,
										}
									}, false);
								});
								this.redraw();
							}
						}
					}
				},
				title: {
					text: 'Proporción mensual',
				},
				xAxis: {
					categories: <?php echo json_encode($array_categorias_fechas); ?>
				},
				yAxis: {
					min: 0,
					title: '',
					labels: {
						style: {
							fontSize:'11px'
						},
						format: "{value:,." + decimal_numbers + "f} %",
					},
				},
				credits: {
					enabled: false
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.x +'</b>: <br>' + this.series.name + ': ' + numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator) + ' (' + numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +'%' + ')';
					},
				},
				plotOptions: {
					column: {
						stacking: 'percent',
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							format: '{point.percentage:.' + decimal_numbers + 'f} %',
						},
					}
				},
				legend: {
					enabled: true,
				},
				exporting: {
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("compliance").'_'.clean(lang("summary_by_iga")).'_'.date("Y-m-d"); ?>
					filename: "<?php echo $filename; ?>",
					buttons: {
						contextButton: {
							menuItems: [{
								text: "<?php echo lang('export_to_png'); ?>",
								onclick: function() {
									this.exportChart();
								},
								separator: false
							}]
						}
					}
				},
				series: <?php echo json_encode($array_impactos_chart_data[$unidad_funcional->id]); ?>,
			});


			/*var graficos_huellas = {};

			var id = "grafico_uf_<?php echo $unidad_funcional->id; ?>";

			$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = true;
			$('#' + id).highcharts().options.title.text = "<?php echo $huella->nombre.'<br /> ('.$nombre_unidad_huella.' '.$huella->indicador.')'; ?>";
			$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = true;
			$('#' + id).highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "15px";
			$('#' + id).highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
			$('#' + id).highcharts().options.plotOptions.pie.size = 150;
			$('#' + id).highcharts().options.legend.itemStyle.fontSize = "15px";
			$('#' + id).highcharts().options.title.style.fontSize = "23px";

			var chart = $('#' + id).highcharts().options.chart;
			var series = $('#' + id).highcharts().options.series;
			var title = $('#' + id).highcharts().options.title;
			var plotOptions = $('#' + id).highcharts().options.plotOptions;
			var colors = $('#' + id).highcharts().options.colors;
			var exporting = $('#' + id).highcharts().options.exporting;
			var credits = $('#' + id).highcharts().options.credits;
			var legend = $('#' + id).highcharts().options.legend;

			var obj = {};
			obj.options = JSON.stringify({
				"chart":chart,
				"title":title,
				"series":series,
				"plotOptions":plotOptions,
				"colors":colors,
				"exporting":exporting,
				"credits":credits,
				"legend":legend,
			});

			obj.type = 'image/png';
			obj.width = '1600';
			obj.scale = '2';
			obj.async = true;

			var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
			obj.globaloptions = JSON.stringify(globalOptions);

			var imagen_grafico = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';

			$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = false;
			$('#' + id).highcharts().options.plotOptions.pie.size = null;
			$('#' + id).highcharts().options.legend.itemStyle.fontSize = "9px;";
				
			graficos_huellas[<?php echo $huella->id; ?>] = imagen_grafico;

			graficos_huellas_unidades_funcionales[<?php echo $unidad_funcional->id; ?>] = graficos_huellas;*/

			<?php } ?>

	});
</script> 