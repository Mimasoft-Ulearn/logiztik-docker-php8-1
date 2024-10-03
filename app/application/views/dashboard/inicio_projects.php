<div id="page-content" class="p20 clearfix">
    <div class="row">
        <?php //$this->load->view("clients/info_widgets"); ?>
    </div>
    
    <div class="">
        <?php if (isset($page_type) && $page_type === "full") { ?>
            <div id="page-content" class="m20 clearfix">
            <?php } ?>
            
			<?php echo form_open('#', array("id" => "search-form", "class" => "general-form", "role" => "form")); ?>
            <div class="panel panel-default">
            
                <div class="panel-body">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="">
                                <h1 style="padding:0;font-size:20px;color:#000;"><?php echo lang("projects");?></h1>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="">
                                <?php 
                                    echo form_input(array(
                                        "id" => "search",
                                        "name" => "search",
                                        "value" => "",
                                        "class" => "form-control",
                                        "placeholder" => lang('search'),
                                        "autocomplete" => "off",
                                    ));
                                ?>
                            </div>           
                        </div>
                    </div>
                    
                </div>
            </div>
            <?php echo form_close(); ?>
                
                <?php
                if(!$this->session->project_context){
                    
					if($projects){
						foreach($projects as $proyecto){
							
							$id_proyecto = $proyecto->id;
							//$id_metodologia = $proyecto->id_metodologia ? $proyecto->id_metodologia : "";
							$ids_metodologia = $proyecto->id_metodologia ? json_decode($proyecto->id_metodologia) : array();
							//$tecnologia = $proyecto->tecnologia ? $proyecto->tecnologia : "-"; // con esto no aparecera la tecnologia del proyecto
							//$start_date = $proyecto->start_date * 1 ? time_date_zone_format($proyecto->start_date, $proyecto->id) : "-";
							//$end_date = $proyecto->deadline * 1 ? time_date_zone_format($proyecto->deadline, $proyecto->id) : "-";
							$start_date = $proyecto->start_date * 1 ? $proyecto->start_date : "-";
							$end_date = $proyecto->deadline * 1 ? $proyecto->deadline : "-";
							$descripcion = $proyecto->description ? $proyecto->description : "-";
							$status = $proyecto->status ? $proyecto->status : "-";
							
							if($status == "open"){
								$status = '<span class="label label-success">'.lang("open").'</span>';
							}
							if($status == "closed"){
								$status = '<span class="label label-warning">'.lang("closed").'</span>';
							}
							if($status == "canceled"){
								$status = '<span class="label label-danger">'.lang("canceled").'</span>';
							}
	
							
							// $avatar = $proyecto->icono ? base_url("assets/images/icons/".$proyecto->icono) : base_url("assets/images/icons/empty.png");
							
							$config = $General_settings_model->get_setting($client_id, $proyecto->id);
							
							$html .= '<div class="row project" style="margin-bottom: 20px;">';
							
								$html .= '<div class="col-md-12 col-sm-12 widget-container">';
									$html .= '<a href="'.get_uri("dashboard/view/".$proyecto->id).'" class="white-link">';
									if(!$proyecto->background_color || !$proyecto->font_color){
										$html .= '<div class="panel panel-sky mb0">';
									} else {
										$html .= '<div class="panel mb0" style="background-color: '.$proyecto->background_color.'; color: '.$proyecto->font_color.'">';
									}
										$html .= '<div class="panel-body">';
											// $html .= '<div class="col-md-2">';
											// 	$html .= '<div class="">';
											// 		$html .= '<span class="avatar avatar-lg">';
											// 		$html .= '<img src="'.$avatar.'" alt="..." style="background-color:#FFF;" class="mCS_img_loaded">';
											// 		$html .= '</span>';
											// 	$html .= '</div>';
											// $html .= '</div>';
											$html .= '<div class="col-md-3">';
												$html .= '<h2 class="project_title">'.$proyecto->title.'</h2>';
												$html .= '<p>'. lang("start").": ".get_date_format($start_date, $id_proyecto).'</p>';
												$html .= '<p>'. lang("term").": ".get_date_format($end_date, $id_proyecto).'</p>';
												$html .= '<p>'. lang("status").": ".$status.'</p>';
											$html .= '</div>';
											$html .= '<div class="col-md-9">';
												$html .= '<h3>'.$tecnologia.'</h3>';
												$html .= '<p class="project_desc" style="text-align:justify;">'.$descripcion.'</p>';
											$html .= '</div>';
										$html .= '</div>';
									$html .= '</div>';
									$html .= '</a>';
								$html .= '</div>';
								
								// Huella ACV
								$footprints = $this->Footprints_model->get_footprints_of_methodology(1)->result(); // Metodología con id 1: ReCiPe 2008, midpoint (H) [v1.11, December 2014
								$footprint_ids = array();
								foreach($footprints as $footprint){
									$footprint_ids[] = $footprint->id;
								}
								$options_footprint_ids = array("footprint_ids" => $footprint_ids);
								$huellas = $Project_rel_footprints_model->get_footprints_of_project($id_proyecto, $options_footprint_ids)->result();

								// Huella de Carbono
								$footprints_carbon = $this->Footprints_model->get_footprints_of_methodology(2)->result(); // Metodología con id 2: GHG Protocol
								$footprint_ids_carbon = array();
								foreach($footprints_carbon as $footprint_carbon){
									$footprint_ids_carbon[] = $footprint_carbon->id;
								}
								$options_footprint_ids_carbon = array("footprint_ids" => $footprint_ids_carbon);
								$huellas_carbon = $Project_rel_footprints_model->get_footprints_of_project($id_proyecto, $options_footprint_ids_carbon)->result();

								// Huella de Agua
								$footprints_water = $this->Footprints_model->get_footprints_of_methodology(3)->result(); // Metodología con id 2: Huella de Agua
								$footprint_ids_water = array();
								foreach($footprints_water as $footprint_water){
									$footprint_ids_water[] = $footprint_water->id;
								}
								$options_footprint_ids_water = array("footprint_ids" => $footprint_ids_water);
								$huellas_water = $Project_rel_footprints_model->get_footprints_of_project($id_proyecto, $options_footprint_ids_water)->result();

								$criterios_calculos = $Unit_processes_model->get_rules_calculations_of_project($client_id, $proyecto->id)->result();
								
								$disponibilidad_modulo_huellas = $this->Module_availability_model->get_one_where(array("id_cliente" => $client_id, "id_proyecto" => $id_proyecto, "id_modulo_cliente" => 1, "deleted" => 0))->available;
								
								if($disponibilidad_modulo_huellas){
								
									$html .= '<div class="col-md-12 col-sm-12 widget-container">';

										$html .= '<div class="panel panel-white mb0">';
											$html.='<div class="panel-heading"><h3>'.lang("total_impacts").'</h3>';
												$html .= '<div class="panel-body">';
													$html.= '<div class="slider_total_impacts slider">';

													// Si el proyecto tiene la metodología con id 1 (ReCiPe 2008, midpoint (H) [v1.11, December 2014])
													if(in_array(1, $ids_metodologia)){
													
														$html.= '<div>'; // INICIO DIV HUELLAS ACV
													
															$html .= '<div class="col-md-12 p0">';
																$html .= '<h4>'.lang("environmental_footprints").'</h4>';
															$html .= '</div>';
															
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
																	$fila_config_huella = $Module_footprint_units_model->get_one_where(
																		array(
																			"id_cliente" => $client_id,
																			"id_proyecto" => $id_proyecto,
																			"id_tipo_unidad" => $id_tipo_unidad_origen,
																			"deleted" => 0
																		)
																	);
																	$id_unidad_destino = $fila_config_huella->id_unidad;
																	//print_r($Conversion_model);
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
																		$html .= '<div class="text-center p15" style="height: 80px; width: 80px; margin: auto;"><img src="'.$icono.'" alt="..." class="mCS_img_loaded"></div>';
																		
																		// Calculos del cliente y proyecto
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
																			
																			foreach($ides_campo_unidad as $id_campo_unidad){
												
																				if($id_campo_unidad == 0){
																					$id_formulario = $calculo->id_formulario;
																					$form_data = $Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
																					$json_unidad_form = json_decode($form_data->unidad,true);
																					
																					$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
																					$id_unidad = $json_unidad_form["unidad_id"];
																					
																					$fila_unidad = $Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
																					$array_unidades[] = $fila_unidad->nombre;
																					$array_id_unidades[] = $id_unidad;
																					$array_id_tipo_unidades[] = $id_tipo_unidad;
																				}else{
																					$fila_campo = $Fields_model->get_one_where(array("id" => $id_campo_unidad, "deleted" => 0));
																					$info_campo = $fila_campo->opciones;
																					$info_campo = json_decode($info_campo, true);
												
																					$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
																					$id_unidad = $info_campo[0]["id_unidad"];
																					
																					$fila_unidad = $Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
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
				
																			$elementos = $Calculation_model->get_records_of_forms_for_calculation($proyecto->id, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria)->result();
				
																			// Elemento del formulario de calculo y con el criterio fc y categoria corresp
																			foreach($elementos as $elemento){
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
	
																		$html .= '<div class="text-center p15">'.to_number_project_format($total_huella, $id_proyecto)./*' * '.$valor_transformacion.*/'</div>';
																		$html .= '<div class="pt10 pb10 b-b"> '.$huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
																	$html .= '</div>';
																}

															} else {
																$html .= '<div class="app-alert alert alert-warning alert-dismissible mb0 col-md-12" style="float: left;">';
																	$html .= lang("project_without_footprints");
																$html .= '</div>';
															}

															

														$html.= '</div>'; // FIN DIV HUELLAS ACV

													}

													// Si el proyecto tiene la metodología con id 2 (GHG Protocol)
													if(in_array(2, $ids_metodologia)){

														$html.= '<div>'; // INICIO DIV HUELLA DE CARBONO
													
															$html .= '<div class="col-md-12 p0">';
																$html .= '<h4>'.lang("carbon_environmental_footprints").'</h4>';
															$html .= '</div>';

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
																	$fila_config_huella = $Module_footprint_units_model->get_one_where(
																		array(
																			"id_cliente" => $client_id,
																			"id_proyecto" => $id_proyecto,
																			"id_tipo_unidad" => $id_tipo_unidad_origen,
																			"deleted" => 0
																		)
																	);
																	$id_unidad_destino = $fila_config_huella->id_unidad;
																	//print_r($Conversion_model);
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
																		$html .= '<div class="text-center p15" style="height: 80px; width: 80px; margin: auto;"><img src="'.$icono.'" alt="..." class="mCS_img_loaded"></div>';
																		
																		// Calculos del cliente y proyecto
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
																			
																			foreach($ides_campo_unidad as $id_campo_unidad){
												
																				if($id_campo_unidad == 0){
																					$id_formulario = $calculo->id_formulario;
																					$form_data = $Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
																					$json_unidad_form = json_decode($form_data->unidad,true);
																					
																					$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
																					$id_unidad = $json_unidad_form["unidad_id"];
																					
																					$fila_unidad = $Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
																					$array_unidades[] = $fila_unidad->nombre;
																					$array_id_unidades[] = $id_unidad;
																					$array_id_tipo_unidades[] = $id_tipo_unidad;
																				}else{
																					$fila_campo = $Fields_model->get_one_where(array("id" => $id_campo_unidad, "deleted" => 0));
																					$info_campo = $fila_campo->opciones;
																					$info_campo = json_decode($info_campo, true);
												
																					$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
																					$id_unidad = $info_campo[0]["id_unidad"];
																					
																					$fila_unidad = $Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
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
				
																			$elementos = $Calculation_model->get_records_of_forms_for_calculation($proyecto->id, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria)->result();
				
																			// Elemento del formulario de calculo y con el criterio fc y categoria corresp
																			foreach($elementos as $elemento){
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
	
																		$html .= '<div class="text-center p15">'.to_number_project_format($total_huella, $id_proyecto)./*' * '.$valor_transformacion.*/'</div>';
																		$html .= '<div class="pt10 pb10 b-b"> '.$huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
																	$html .= '</div>';
																}

															} else {
																$html .= '<div class="app-alert alert alert-warning alert-dismissible mb0 col-md-12" style="float: left;">';
																	$html .= lang("project_without_footprints_carbon");
																$html .= '</div>';
															}

															

														$html.= '</div>'; // FIN DIV HUELLA DE CARBONO

													}

													// Si el proyecto tiene la metodología con id 3 (Huella de Agua)
													if(in_array(3, $ids_metodologia)){

														$html.= '<div>'; // INICIO DIV HUELLA DE AGUA
													
															$html .= '<div class="col-md-12 p0">';
																$html .= '<h4>'.lang("water_environmental_footprints").'</h4>';
															$html .= '</div>';

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
																	$fila_config_huella = $Module_footprint_units_model->get_one_where(
																		array(
																			"id_cliente" => $client_id,
																			"id_proyecto" => $id_proyecto,
																			"id_tipo_unidad" => $id_tipo_unidad_origen,
																			"deleted" => 0
																		)
																	);
																	$id_unidad_destino = $fila_config_huella->id_unidad;
																	//print_r($Conversion_model);
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
																		$html .= '<div class="text-center p15" style="height: 80px; width: 80px; margin: auto;"><img src="'.$icono.'" alt="..." class="mCS_img_loaded"></div>';
																		
																		// Calculos del cliente y proyecto
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
																			
																			foreach($ides_campo_unidad as $id_campo_unidad){
												
																				if($id_campo_unidad == 0){
																					$id_formulario = $calculo->id_formulario;
																					$form_data = $Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
																					$json_unidad_form = json_decode($form_data->unidad,true);
																					
																					$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
																					$id_unidad = $json_unidad_form["unidad_id"];
																					
																					$fila_unidad = $Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
																					$array_unidades[] = $fila_unidad->nombre;
																					$array_id_unidades[] = $id_unidad;
																					$array_id_tipo_unidades[] = $id_tipo_unidad;
																				}else{
																					$fila_campo = $Fields_model->get_one_where(array("id" => $id_campo_unidad, "deleted" => 0));
																					$info_campo = $fila_campo->opciones;
																					$info_campo = json_decode($info_campo, true);
												
																					$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
																					$id_unidad = $info_campo[0]["id_unidad"];
																					
																					$fila_unidad = $Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
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
				
																			$elementos = $Calculation_model->get_records_of_forms_for_calculation($proyecto->id, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria)->result();
				
																			// Elemento del formulario de calculo y con el criterio fc y categoria corresp
																			foreach($elementos as $elemento){
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
	
																		$html .= '<div class="text-center p15">'.to_number_project_format($total_huella, $id_proyecto)./*' * '.$valor_transformacion.*/'</div>';
																		$html .= '<div class="pt10 pb10 b-b"> '.$huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
																	$html .= '</div>';

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

														$html.= '</div>'; // FIN DIV HUELLA DE AGUA

													}

													$html.= '</div>';
												$html .= '</div>';
											$html.= '</div>';
										$html .= '</div>';

									$html .= '</div>';
								
								} else {
									$html .= '';
								}
								
	
							$html .= '</div>';  
							
						}
						
					}else{
						$html = '<div class="app-alert alert alert-warning alert-dismissible mb0 col-md-12" style="float: left;">'.lang("not_projects_assigned").'</div>';
						
					}
                    
                    
                    echo $html;
                }
                ?>
                
                <?php ?>
        
            
            
            <?php if (isset($page_type) && $page_type === "full") { ?>
            </div>
        <?php } ?>
        
        <?php
        if (!isset($project_labels_dropdown)) {
            $project_labels_dropdown = "0";
        }
        ?>
        
        
        <script type="text/javascript">
            $(document).ready(function () {
                var hideTools = "<?php
        if (isset($page_type) && $page_type === 'dashboard') {
            echo 1;
        }
        ?>" || 0;
        
        
                var filters = [{name: "project_label", class: "w200", options: <?php echo $project_labels_dropdown; ?>}];
        
                //don't show filters if hideTools is true or $project_labels_dropdown is empty
                if (hideTools || !<?php echo $project_labels_dropdown; ?>) {
                    filters = false;
                }
        
            });
        </script>
        
        
        
    </div>
</div>

<script type="text/javascript">

	$(document).ready(function(){

		$('.slider_total_impacts').slick({
			dots: false,
			infinite: false,
			speed: 300,
			slidesToShow: 1,
			slidesToScroll: 1
		});
	
		var maxHeight_uf = Math.max.apply(null, $("#page-content > div:nth-child(2) div.huella").map(function (){
			return $(this).find("div.b-b").height();
			//return $(this).height();
		}).get());
		$("div.huella > div:nth-child(3)").height(maxHeight_uf);
		
		$("#search-form").submit(function(e){
			e.preventDefault();
		});
		
		$('#page-content').lookingfor({
			input: $("#search"),
			items: '.project .project_title',
			highlight: true,
			onFound: function(element, query){
				$(element).closest('.project').show();
			},
			onNotFound: function(element, query){
				$(element).closest('.project').hide();
			}
		});
		
		$("#search").keyup(function(){
			var txt = $('#search').val();
			if(txt === ''){
				$('.project').show();
			}
		});
		
	});

</script>

<script type="text/javascript">

	$(document).ready(function(){
		
		<?php if(!$this->session->project_context){ ?>
                    
       		<?php foreach($projects as $proyecto){ ?>
		
				<?php 
					
					//COMPROMISO
					//$id_compromiso = $Compromises_model->get_one_where(array('id_proyecto' => $proyecto->id, 'deleted' => 0))->id;
					$id_compromiso = $Compromises_rca_model->get_one_where(array('id_proyecto' => $proyecto->id, 'deleted' => 0))->id;

					if($id_compromiso){
						
						//SE TRAE LA CANTIDAD TOTAL DE COMPROMISOS APLICABLES
						//$total_compromisos_aplicables = $Compromises_model->get_total_applicable_compromises($id_compromiso)->result_array();
						$total_compromisos_aplicables = $Compromises_rca_model->get_total_applicable_compromises($id_compromiso)->result_array();
						$cant_total_compromisos_aplicables = 0;
						foreach($total_compromisos_aplicables as $compromiso_aplicable){
							//$ultima_evaluacion = $Compromises_compliance_evaluation_model->get_last_evaluation(array("id_evaluado" => $compromiso_aplicable["id_evaluado"], "id_valor_compromiso" => $compromiso_aplicable["id_valor_compromiso"]))->result_array();
							$ultima_evaluacion = $Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $compromiso_aplicable["id_evaluado"], "id_valor_compromiso" => $compromiso_aplicable["id_valor_compromiso"]))->result_array();
							if($ultima_evaluacion[0]["id"] == $compromiso_aplicable["id"]){
								$cant_total_compromisos_aplicables++;
							}
						}
	
						//TRAER CADA COMPROMISO APLICABLE (ESTADOS Y CANTIDADES) DE LAS ÚLTIMAS EVALUACIONES
						//$total_cantidades_estados_evaluados = $Compromises_model->get_total_quantities_of_status_evaluated($id_compromiso)->result_array();
						$total_cantidades_estados_evaluados = $Compromises_rca_model->get_total_quantities_of_status_evaluated($id_compromiso)->result_array();
						$array_total_cantidades_estados_evaluados = array();
						$total_cantidades_ultimas_evaluaciones = array();
						
						foreach($total_cantidades_estados_evaluados as $tcee){
							//$ultima_evaluacion = $Compromises_compliance_evaluation_model->get_last_evaluation(array("id_evaluado" => $tcee["id_evaluado"], "id_valor_compromiso" => $tcee["id_valor_compromiso"]))->result_array();			
							$ultima_evaluacion = $Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $tcee["id_evaluado"], "id_valor_compromiso" => $tcee["id_valor_compromiso"]))->result_array();
							if($ultima_evaluacion[0]["id"] == $tcee["id_evaluacion"]){
								$array_total_cantidades_estados_evaluados[] = array("cantidad_categoria" => 1, "nombre_estado" => $tcee["nombre_estado"], "color" => $tcee["color"]);
							}
						}
						
						
						
						//SE AGRUPA $array_total_cantidades_estados_evaluados POR nombre_estado Y SE SUMA cantidad_categoria POR CADA nombre_estado
						$result_atcee = array();
						foreach($array_total_cantidades_estados_evaluados as $atcee){
							$repeat = false;
							for($i = 0; $i < count($result_atcee); $i++){
								if($result_atcee[$i]['nombre_estado'] == $atcee['nombre_estado']){
									$result_atcee[$i]['cantidad_categoria'] += $atcee['cantidad_categoria'];
									$repeat = true;
									break;
								}
							}
							if($repeat == false){
								$result_atcee[] = array('nombre_estado' => $atcee['nombre_estado'], 'cantidad_categoria' => $atcee['cantidad_categoria'], 'color' => $atcee['color']);
							}		
						}
						
					}
					
					$id_permiso = $Permitting_model->get_one_where(array('id_proyecto' => $proyecto->id, 'deleted' => 0))->id;
					
					if($id_permiso){
						
						//SE TRAE LA CANTIDAD TOTAL DE PERMISOS APLICABLES
						$total_permisos_aplicables = $Permitting_model->get_total_applicable_permitting($id_permiso)->result_array();
						$cant_total_permisos_aplicables = 0;
						foreach($total_permisos_aplicables as $permiso_aplicable){
							$ultima_evaluacion_permisos = $Permitting_procedure_evaluation_model->get_last_evaluation(array("id_evaluado" => $permiso_aplicable["id_evaluado"], "id_valor_permiso" => $permiso_aplicable["id_valor_permiso"]))->result_array();
							if($ultima_evaluacion_permisos[0]["id"] == $permiso_aplicable["id"]){
								$cant_total_permisos_aplicables++;
							}
						}
						
						//TRAER CADA PERMISO APLICABLE (ESTADOS Y CANTIDADES) DE LAS ÚLTIMAS EVALUACIONES
						$total_cantidades_estados_evaluados_permisos = $Permitting_model->get_total_quantities_of_status_evaluated($id_permiso)->result_array();
						$array_total_cantidades_estados_evaluados_permisos = array();
						$total_cantidades_ultimas_evaluaciones_permisos = array();
						
						foreach($total_cantidades_estados_evaluados_permisos as $tcee){
							$ultima_evaluacion_permisos = $Permitting_procedure_evaluation_model->get_last_evaluation(array("id_evaluado" => $tcee["id_evaluado"], "id_valor_permiso" => $tcee["id_valor_permiso"]))->result_array();			
							if($ultima_evaluacion_permisos[0]["id"] == $tcee["id_evaluacion"]){
								$array_total_cantidades_estados_evaluados_permisos[] = array("cantidad_categoria" => 1, "nombre_estado" => $tcee["nombre_estado"], "color" => $tcee["color"]);
							}
						}
						
						//SE AGRUPA $array_total_cantidades_estados_evaluados POR nombre_estado Y SE SUMA cantidad_categoria POR CADA nombre_estado
						$result_atcee_permisos = array();
						foreach($array_total_cantidades_estados_evaluados_permisos as $atcee){
							$repeat = false;
							for($i = 0; $i < count($result_atcee_permisos); $i++){
								if($result_atcee_permisos[$i]['nombre_estado'] == $atcee['nombre_estado']){
									$result_atcee_permisos[$i]['cantidad_categoria'] += $atcee['cantidad_categoria'];
									$repeat = true;
									break;
								}
							}
							if($repeat == false){
								$result_atcee_permisos[] = array('nombre_estado' => $atcee['nombre_estado'], 'cantidad_categoria' => $atcee['cantidad_categoria'], 'color' => $atcee['color']);
							}		
						}

					}
				
				?>
				
				<?php if($id_compromiso) { ?>
				
					<?php if(!empty(array_filter($result_atcee))){ ?>
					
					$('#grafico_cumplimientos_totales_<?php echo $proyecto->id; ?>').highcharts({
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false,
							type: 'pie',
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
							text: '',
						},
						credits: {
							enabled: false
						},
						tooltip: {
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
							},
							//pointFormat: '{series.name}: <b>{point.y}%</b>'
						},
						plotOptions: {
							pie: {
							//size: 80,
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: false,
								format: '<b>{point.name}</b>: {point.percentage:.1f} %',
								style: {
									color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
									fontSize: "9px",
									distance: -30
								},
								crop: false
							},
							showInLegend: true
							}
						},
						legend: {
							enabled: true,
							itemStyle:{
								fontSize: "9px"
							}
						},
						exporting: {
							filename: "<?php echo lang("total_compliances"); ?>",
							buttons: {
								contextButton: {
									enabled: false,
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
						colors: [
							<?php 
								foreach($result_atcee as $estado) { 
									echo "'".$estado["color"]."',";
								}
							?>
						],
						//colors: ['#398439', '#ac2925', '#d58512'],
						series: [{
							name: 'Porcentaje',
							colorByPoint: true,
							data: [
							<?php foreach($result_atcee as $estado) { ?>
								{
									name: '<?php echo $estado["nombre_estado"]; ?>',
									y: <?php echo ($estado["cantidad_categoria"] * 100) / $cant_total_compromisos_aplicables; /*echo to_number_project_format($estado["porcentaje"], $id_proyecto);*/ ?>
								},
							<?php } ?>
							
							]
						}]
					});
					
					
					<?php }else{?>
							$('#grafico_cumplimientos_totales_<?php echo $proyecto->id; ?>').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
					<?php } ?>
					
				<?php } else { ?>
					
					$('#grafico_cumplimientos_totales_<?php echo $proyecto->id; ?>').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
					
				<?php } ?>
				
				
				<?php if($id_permiso){ ?>
				
				
					<?php if(!empty(array_filter($result_atcee_permisos))){ ?>
						
						$('#grafico_tramitaciones_totales_<?php echo $proyecto->id; ?>').highcharts({
							chart: {
								plotBackgroundColor: null,
								plotBorderWidth: null,
								plotShadow: false,
								type: 'pie',
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
								text: '',
							},
							credits: {
								enabled: false
							},
							tooltip: {
								formatter: function() {
									return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
								},
								//pointFormat: '{series.name}: <b>{point.y}%</b>'
							},
							plotOptions: {
								pie: {
								//size: 80,
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: false,
									format: '<b>{point.name}</b>: {point.percentage:.1f} %',
									style: {
										color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
										fontSize: "9px",
										distance: -30
									},
									crop: false
								},
								showInLegend: true
								}
							},
							legend: {
								enabled: true,
								itemStyle:{
									fontSize: "9px"
								}
							},
							exporting: {
								filename: "<?php echo lang("total_procedures"); ?>",
								buttons: {
									contextButton: {
										enabled: false,
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
							colors: [
								<?php 
									foreach($result_atcee_permisos as $estado) { 
										echo "'".$estado["color"]."',";
									}
								?>
							],
							//colors: ['#398439', '#ac2925', '#d58512'],
							series: [{
								name: 'Porcentaje',
								colorByPoint: true,
								data: [
								<?php foreach($result_atcee_permisos as $estado) { ?>
									{
										name: '<?php echo $estado["nombre_estado"]; ?>',
										y: <?php echo ($estado["cantidad_categoria"] * 100) / $cant_total_permisos_aplicables; /*echo to_number_project_format($estado["porcentaje"], $id_proyecto);*/ ?>
									},
								<?php } ?>
								]
							}]
						});
						
						<?php }else{?>
								$('#grafico_tramitaciones_totales_<?php echo $proyecto->id; ?>').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
						<?php } ?>

				<?php } else { ?>
					$('#grafico_tramitaciones_totales_<?php echo $proyecto->id; ?>').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
				<?php } ?>
				
			<?php } ?>
		
		<?php } ?>
		
	});

</script>