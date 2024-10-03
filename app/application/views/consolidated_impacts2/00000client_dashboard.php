<div id="page-content" class="p20 clearfix">
	
	<!--Breadcrumb section-->
	<nav class="breadcrumb">
	<a class="breadcrumb-item" href="<?php echo get_uri("consolidated_impacts"); ?>"><?php echo lang("consolidated_impacts"); ?> </a>
	</nav>

	<?php foreach($projects as $project) { ?>




	<?php } ?>




	<?php 
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
	?>

	<?php $disponibilidad_modulo_huellas = $this->Module_availability_model->get_one_where(array("id_cliente" => $client_id, "id_proyecto" => $id_proyecto, "id_modulo_cliente" => 1, "deleted" => 0))->available; ?>
	
	<?php if($disponibilidad_modulo_huellas) { ?>
		<?php if($visible_total_impacts) { ?>
			<div class="row">
				<div class="col-md-12 col-sm-12 widget-container">
					<div class="panel panel-white">
						<div class="panel-heading" style="background-color:#00b393;color:white;"><h3><?php echo lang("total_impacts"); ?></h3></div>
						<div class="panel-heading">
							<div class="panel-body">

								<?php

									$id_proyecto = $proyecto->id;
									//$id_metodologia = $proyecto->id_metodologia;
									$ids_metodologia = json_decode($proyecto->id_metodologia);

									//$html = '';
									$array_cifras_huellas = array();
									$array_unidades_proyecto = array();

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
													
													$icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");
													$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
														$html .= '<div class="text-center p15" style="height: 80px; width: 80px; margin: auto;"><img src="'.$icono.'" alt="..." class="mCS_img_loaded"></div>';
														
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
																	$form_data = $Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
																	$json_unidad_form = json_decode($form_data->unidad, true);
																	
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

															$elementos = $Calculation_model->get_records_of_forms_for_calculation($id_proyecto, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria, $first_date_current_year, $last_date_current_year)->result();
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
														
														$array_cifras_huellas[$id_huella] = $total_huella;
														$html .= '<div class="text-center p15">'.to_number_project_format($total_huella, $id_proyecto).'</div>';
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

									</div> <!-- FIN DIV HUELLAS ACV -->

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
													
													$icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");
													$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
														$html .= '<div class="text-center p15" style="height: 80px; width: 80px; margin: auto;"><img src="'.$icono.'" alt="..." class="mCS_img_loaded"></div>';
														
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
																	$form_data = $Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
																	$json_unidad_form = json_decode($form_data->unidad, true);
																	
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

															$elementos = $Calculation_model->get_records_of_forms_for_calculation($id_proyecto, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria, $first_date_current_year, $last_date_current_year)->result();
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
														
														$array_cifras_huellas[$id_huella] = $total_huella;
														$html .= '<div class="text-center p15">'.to_number_project_format($total_huella, $id_proyecto).'</div>';
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
													
													$icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");
													$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
														$html .= '<div class="text-center p15" style="height: 80px; width: 80px; margin: auto;"><img src="'.$icono.'" alt="..." class="mCS_img_loaded"></div>';
														
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
																	$form_data = $Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
																	$json_unidad_form = json_decode($form_data->unidad, true);
																	
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

															$elementos = $Calculation_model->get_records_of_forms_for_calculation($id_proyecto, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria, $first_date_current_year, $last_date_current_year)->result();
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
														
														$array_cifras_huellas[$id_huella] = $total_huella;
														$html .= '<div class="text-center p15">'.to_number_project_format($total_huella, $id_proyecto).'</div>';
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
												$html .= '<div class="text-center p15" style="height: 80px; width: 80px; margin: auto;"><img src="'.$icono.'" alt="..." class="mCS_img_loaded"></div>';
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
					</div>
				</div>
			</div>

		<?php } ?>
	<?php } ?>





















	<?php if($disponibilidad_modulo_huellas) { ?>

		<?php if($visible_impacts_by_functional_units) { ?>

			<?php

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

			}
			?>
			<div class="row">

				<?php foreach($unidades_funcionales as $unidad_funcional){ ?>

					<div class="col-md-12 col-sm-12 widget-container">
						<div class="panel panel-white">
							<div class="page-title clearfix panel-success">
								<h1><?php echo lang("environmental_impacts_by") . ' ' . $unidad_funcional->unidad. ' ' . lang("of") . ' ' . $unidad_funcional->nombre; ?></h1>
							</div>

							<div class="panel-heading">
							
								<div class="panel-body">

									<?php
										$id_proyecto = $proyecto->id;
										$ids_metodologia = json_decode($proyecto->id_metodologia);
										
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
						</div>
					</div>

				<?php } ?>

			</div>

		<?php } ?>
	
	<?php } ?>













	
	

	<!-- CONSUMO -->
	<div class="panel-group" id="accordion_consumos">
							
		<div class="panel panel-default">

			<div class="panel-heading p0">
				<a data-toggle="collapse" href="#collapse_consumos" data-parent="#accordion_consumos" class="accordion-toggle">
					<div class="row tab-title">
						<div class="col-md-5"></div>
						<div class="col-md-3" >
							<h4 style="float:unset !important;"style="text-align:left;"><strong><i class="fa fa-plus-circle font-16" style="padding-right:4em;"></i><?php echo lang('consumptions'); ?></strong></h4>
						</div>
						<div class="col-md-4"></div>
					</div>
				</a>
			</div>

			<div id="collapse_consumos" class="panel-collapse collapse">
				<div class="row">
					<div class="col-md-12">
						<div id="div_consumos" class="panel panel-body">
						
							<!-- GRAFICO Y TABLA CONSUMO VOLUMEN -->
							<div class="col-md-12" style="padding-left:0px; padding-right:0px;">

								<!-- GRÁFICO CONSUMO VOLUMEN -->
								<div id="grafico_consumo_volumen" class="col-md-12 p0 page-title">
									<div class="panel-body p20">
									<h4 style='float:unset !important; text-align:center;'><strong><?php echo lang('consumptions'); ?> (<?php echo $unidad_volumen; ?>)</strong></h4>
									</div>
									<div class="grafico page-title" id="consumo_volumen"></div>
								</div>

								<div id="tabla_consumo_volumen_total" class="col-md-12 p0">
									<div class="page-title p10" style="border-bottom: none !important;">
										<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-consumo_volumen_total"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
										<table id="table-consumo_volumen_total" class="table table-responsive table-striped">
											<thead>
												<tr>
													<th class="text-left"><?php echo lang('material'); ?></th>
												<?php foreach($years as $year){ ?>
													<th class="text-right"><?php echo $year; ?></th>
												<?php } ?>
												</tr>
											</thead>
											<tbody>
											<?php
												$html = '';
												foreach ($array_id_materiales_valores_volumen as $id_material => $array_id_categorias_valores_volumen){

													$nombre_material = $this->Materials_model->get_one($id_material)->nombre;
													$array_total_material_year = array();

													foreach ($array_id_categorias_valores_volumen as $id_categoria => $arreglo_valores){
													
														$row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
														if($row_alias->alias){
															$nombre_categoria = $row_alias->alias;
														}else{
															$row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
															$nombre_categoria = $row_categoria->nombre;
														}

														$row_categoria = $Client_consumptions_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));

														if($row_categoria->tabla){
															$valor = 0;
															// SE CALCULA EL VALOR TOTAL POR CATEGORÍA PARA CADA AÑO
															foreach($years as $year){
																$valor = array_sum($arreglo_valores[$year]);
																$array_total_material_year[$year] += $valor;
															}
														}
													}

													$html .= '<tr>';
													$html .= '<td class="text-left">'.$nombre_material.'</td>';
													foreach($years as $year){
														$html .= '<td class="text-right">'.to_number_project_format($array_total_material_year[$year], $id_proyecto).'</td>';
													}
													$html .= '</tr>';
												}
												echo $html;
											?>
											</tbody>
										</table>
									</div>
								</div>
								
								<!-- TABLA CONSUMO VOLUMEN -->
								<div id="tabla_consumo_volumen" class="col-md-12 p0">
									<div class="page-title p10" style="border-bottom: none !important;">
										<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-consumo_volumen"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
										<table id="table-consumo_volumen" class="table table-responsive table-striped">
											<thead>
												<tr>
													<th class="text-left"><?php echo lang('material'); ?></th>
													<th class="text-left"><?php echo lang('category'); ?></th>
												<?php foreach($years as $year){ ?>
													<th class="text-right"><?php echo $year; ?></th>
												<?php } ?>
												</tr>
											</thead>
											
											<tbody>
												<?php
							
													$array_grafico_consumos_volumen_categories = array();
													$html = '';
													foreach ($array_id_materiales_valores_volumen as $id_material => $array_id_categorias_valores_volumen){

														$nombre_material = $this->Materials_model->get_one($id_material)->nombre;

														foreach ($array_id_categorias_valores_volumen as $id_categoria => $arreglo_valores_by_year){
														
															$row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
															if($row_alias->alias){
																$nombre_categoria = $row_alias->alias;
															}else{
																$row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
																$nombre_categoria = $row_categoria->nombre;
															}
															
															// ACA VALIDAR SI CLIENTE/PROYECTO/ID_CATEGORIA ESTA HABILITADO PARA MOSTRARSE EN TABLA Y GRAFICO
															// UNA CATEGORIA ES UNICA A NIVEL DE FLUJO/TIPO-UNIDAD/UNIDAD
															// SI UNA CATEGORIA SE REPITE EN OTRO FORMULARIO (DEL MISMO FLUJO), SUMARLO SI TIENE LA MISMA UNIDAD
															// SI TIENE EL MISMO TIPO DE UNIDAD Y OTRA UNIDAD, CONVERTIRLA Y SUMARLA
															// NO PUEDE EXISTIR LA MISMA CATEGORIA EN UN FORMULARIO CON FLUJO CONSUMO Y RESIDUO
															// NO PUEDEN EXISTIR 1 CAMPO TIPO UNIDAD VOLUMEN Y OTRO MASA EN EL MISMO FORMULARIO
															
															// en el mismo form: no debiera poder tener 2 campos detipo unidad masa y volumen, son excuyentes
															// EXISTE UNA EXCEPCION, ENEL, LISTA 5
															
															$row_categoria = $Client_consumptions_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));
															
															if($row_categoria->tabla){
																$valor = 0;
																
																$html .= '<tr>';
																$html .= '<td class="text-left">'.$nombre_material.'</td>';
																$html .= '<td class="text-left">'.$nombre_categoria.'</td>';
																
																// SE CALCULA EL VALOR TOTAL POR CATEGORÍA PARA CADA AÑO
																foreach($years as $year){
																	$valor = array_sum($arreglo_valores_by_year[$year]);
																	$html .= '<td class="text-right">'.to_number_project_format($valor, $id_proyecto).'</td>';
																}
																$html .= '</tr>';
															}

														}
													}
													echo $html;
												?>
											</tbody>
										</table>
									</div>
								</div>

							</div>
							<!-- FIN GRAFICO Y TABLA CONSUMO VOLUMEN -->
							
							<!-- GRAFICO Y TABLA CONSUMO MASA -->
							<div class="col-md-12" style="padding-left:0px; padding-right:0px;">
											
								<div id="grafico_consumo_masa" class="col-md-12 p0 page-title">
									<div class="panel-body p20">
										<h4 style='float:unset !important; text-align:center;'><strong><?php echo lang('consumptions'); ?> (<?php echo $unidad_masa; ?>)</strong></h4>
									</div>
									<div class="grafico page-title" id="consumo_masa"></div>
								</div>

								<div id="tabla_consumo_masa_total" class="col-md-12 p0">
									<div class="page-title p10" style="border-bottom: none !important;">
										<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-consumo_masa_total"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
										<table id="table-consumo_masa_total" class="table table-responsive table-striped">
											<thead>
												<tr>
													<th class="text-left"><?php echo lang('material'); ?></th>
												<?php foreach($years as $year){ ?>
													<th class="text-right"><?php echo $year; ?></th>
												<?php } ?>
												</tr>
											</thead>
											<tbody>
											<?php
												$html = '';
												foreach ($array_id_materiales_valores_masa as $id_material => $array_id_categorias_valores_masa){

													$nombre_material = $this->Materials_model->get_one($id_material)->nombre;
													$array_total_material_year = array();

													foreach ($array_id_categorias_valores_masa as $id_categoria => $arreglo_valores){
													
														$row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
														if($row_alias->alias){
															$nombre_categoria = $row_alias->alias;
														}else{
															$row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
															$nombre_categoria = $row_categoria->nombre;
														}

														$row_categoria = $Client_consumptions_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));

														if($row_categoria->tabla){
															$valor = 0;
															// SE CALCULA EL VALOR TOTAL POR CATEGORÍA PARA CADA AÑO
															foreach($years as $year){
																$valor = array_sum($arreglo_valores[$year]);
																$array_total_material_year[$year] += $valor;
															}
														}
													}

													$html .= '<tr>';
													$html .= '<td class="text-left">'.$nombre_material.'</td>';
													foreach($years as $year){
														$html .= '<td class="text-right">'.to_number_project_format($array_total_material_year[$year], $id_proyecto).'</td>';
													}
													$html .= '</tr>';
												}
												echo $html;
											?>
											</tbody>
										</table>
									</div>
								</div>

								<div id="tabla_consumo_masa" class="col-md-12 p0">
									<div class="page-title p10" style="border-bottom: none !important;">
										<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-consumo_masa"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
										<table id="table-consumo_masa" class="table table-responsive table-striped">
											<thead>
												<tr>
													<th class="text-left"><?php echo lang('material'); ?></th>
													<th class="text-left"><?php echo lang('category'); ?></th>
												<?php foreach($years as $year){ ?>
													<th class="text-right"><?php echo $year; ?></th>
												<?php } ?>
												</tr>
											</thead>
											<tbody>
											<?php
											
											// $array_grafico_consumos_masa_categories = array();
											// $array_grafico_consumos_masa_data = array();
											$html = '';
											foreach ($array_id_materiales_valores_masa as $id_material => $array_id_categorias_valores_masa){

												$nombre_material = $this->Materials_model->get_one($id_material)->nombre;

												foreach ($array_id_categorias_valores_masa as $id_categoria => $arreglo_valores){
												
													$row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
													if($row_alias->alias){
														$nombre_categoria = $row_alias->alias;
													}else{
														$row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
														$nombre_categoria = $row_categoria->nombre;
													}
													
													// ACA VALIDAR SI CLIENTE/PROYECTO/ID_CATEGORIA ESTA HABILITADO PARA MOSTRARSE EN TABLA Y GRAFICO
													// UNA CATEGORIA ES UNICA A NIVEL DE FLUJO/TIPO-UNIDAD/UNIDAD
													// SI UNA CATEGORIA SE REPITE EN OTRO FORMULARIO (DEL MISMO FLUJO), SUMARLO SI TIENE LA MISMA UNIDAD
													// SI TIENE EL MISMO TIPO DE UNIDAD Y OTRA UNIDAD, CONVERTIRLA Y SUMARLA
													// NO PUEDE EXISTIR LA MISMA CATEGORIA EN UN FORMULARIO CON FLUJO CONSUMO Y RESIDUO
													// NO PUEDEN EXISTIR 1 CAMPO TIPO UNIDAD VOLUMEN Y OTRO MASA EN EL MISMO FORMULARIO
													
													// en el mismo form: no debiera poder tener 2 campos detipo unidad masa y volumen, son excuyentes
													// EXISTE UNA EXCEPCION, ENEL, LISTA 5
													
													$row_categoria = $Client_consumptions_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));
							
													// if($row_categoria->grafico){
													//     $array_grafico_consumos_masa_categories[] = $nombre_categoria;
													//     $array_grafico_consumos_masa_data[] = array_sum($arreglo_valores);
													// }
													
													if($row_categoria->tabla){
														$valor = 0;
																
														$html .= '<tr>';
														$html .= '<td class="text-left">'.$nombre_material.'</td>';
														$html .= '<td class="text-left">'.$nombre_categoria.'</td>';
														
														// SE CALCULA EL VALOR TOTAL POR CATEGORÍA PARA CADA AÑO
														foreach($years as $year){
															$valor = array_sum($arreglo_valores[$year]);
															$html .= '<td class="text-right">'.to_number_project_format($valor, $id_proyecto).'</td>';
														}
														$html .= '</tr>';
													}
												}
											}
											echo $html;
											?>
											</tbody>
										</table>
										
									</div>
								</div>
								
							</div>
							<!-- FIN GRAFICO Y TABLA CONSUMO MASA -->
							
							<!-- GRAFICO Y TABLA CONSUMO ENERGIA -->
							<div class="col-md-12" style="padding-left:0px; padding-right:0px;">
								
								<?php if(count($array_id_materiales_valores_energia) > 0){ ?>
								<div id="grafico_consumo_energia" class="col-md-12 p0 page-title">
									<div class="panel-body p20">
										<h4 style='float:unset !important; text-align:center;'><strong><?php echo lang('consumptions'); ?> (<?php echo $unidad_energia; ?>)</strong></h4>
									</div>
									<div class="grafico page-title" id="consumo_energia"></div>
								</div>

								<div id="tabla_consumo_energia_total" class="col-md-12 p0">
									<div class="page-title p10" style="border-bottom: none !important;">
										<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-consumo_energia_total"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
										<table id="table-consumo_energia_total" class="table table-responsive table-striped">
											<thead>
												<tr>
													<th class="text-left"><?php echo lang('material'); ?></th>
												<?php foreach($years as $year){ ?>
													<th class="text-right"><?php echo $year; ?></th>
												<?php } ?>
												</tr>
											</thead>
											<tbody>
											<?php
												$html = '';
												foreach ($array_id_materiales_valores_energia as $id_material => $array_id_categorias_valores_energia){

													$nombre_material = $this->Materials_model->get_one($id_material)->nombre;
													$array_total_material_year = array();

													foreach ($array_id_categorias_valores_energia as $id_categoria => $arreglo_valores){
													
														$row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
														if($row_alias->alias){
															$nombre_categoria = $row_alias->alias;
														}else{
															$row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
															$nombre_categoria = $row_categoria->nombre;
														}

														$row_categoria = $Client_consumptions_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));

														if($row_categoria->tabla){
															$valor = 0;
															// SE CALCULA EL VALOR TOTAL POR CATEGORÍA PARA CADA AÑO
															foreach($years as $year){
																$valor = array_sum($arreglo_valores[$year]);
																$array_total_material_year[$year] += $valor;
															}
														}
													}

													$html .= '<tr>';
													$html .= '<td class="text-left">'.$nombre_material.'</td>';
													foreach($years as $year){
														$html .= '<td class="text-right">'.to_number_project_format($array_total_material_year[$year], $id_proyecto).'</td>';
													}
													$html .= '</tr>';
												}
												echo $html;
											?>
											</tbody>
										</table>
									</div>
								</div>

								<div id="tabla_consumo_energia" class="col-md-12 p0">
									<div class="page-title p10" style="border-bottom: none !important;">
										<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-consumo_energia"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
										<table id="table-consumo_energia" class="table table-responsive table-striped">
											<thead>
												<tr>
													<th class="text-left"><?php echo lang('material'); ?></th>
													<th class="text-left"><?php echo lang('category'); ?></th>
												<?php foreach($years as $year){ ?>
													<th class="text-right"><?php echo $year; ?></th>
												<?php } ?>
												</tr>
											</thead>
											<tbody>
											<?php
											
											// $array_grafico_consumos_energia_categories = array();
											// $array_grafico_consumos_energia_data = array();
											$html = '';
											foreach ($array_id_materiales_valores_energia as $id_material => $array_id_categorias_valores_energia){

												$nombre_material = $this->Materials_model->get_one($id_material)->nombre;

												foreach ($array_id_categorias_valores_energia as $id_categoria => $arreglo_valores){
												
													$row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
													if($row_alias->alias){
														$nombre_categoria = $row_alias->alias;
													}else{
														$row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
														$nombre_categoria = $row_categoria->nombre;
													}
													
													// ACA VALIDAR SI CLIENTE/PROYECTO/ID_CATEGORIA ESTA HABILITADO PARA MOSTRARSE EN TABLA Y GRAFICO
													// UNA CATEGORIA ES UNICA A NIVEL DE FLUJO/TIPO-UNIDAD/UNIDAD
													// SI UNA CATEGORIA SE REPITE EN OTRO FORMULARIO (DEL MISMO FLUJO), SUMARLO SI TIENE LA MISMA UNIDAD
													// SI TIENE EL MISMO TIPO DE UNIDAD Y OTRA UNIDAD, CONVERTIRLA Y SUMARLA
													// NO PUEDE EXISTIR LA MISMA CATEGORIA EN UN FORMULARIO CON FLUJO CONSUMO Y RESIDUO
													// NO PUEDEN EXISTIR 1 CAMPO TIPO UNIDAD VOLUMEN Y OTRO MASA EN EL MISMO FORMULARIO
													
													// en el mismo form: no debiera poder tener 2 campos detipo unidad masa y volumen, son excuyentes
													// EXISTE UNA EXCEPCION, ENEL, LISTA 5
													
													$row_categoria = $Client_consumptions_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));
							
													// if($row_categoria->grafico){
													//     $array_grafico_consumos_energia_categories[] = $nombre_categoria;
													//     $array_grafico_consumos_energia_data[] = array_sum($arreglo_valores);
													// }
													
													if($row_categoria->tabla){
														$valor = 0;
																
														$html .= '<tr>';
														$html .= '<td class="text-left">'.$nombre_material.'</td>';
														$html .= '<td class="text-left">'.$nombre_categoria.'</td>';
														
														// SE CALCULA EL VALOR TOTAL POR CATEGORÍA PARA CADA AÑO
														foreach($years as $year){
															$valor = array_sum($arreglo_valores[$year]);
															$html .= '<td class="text-right">'.to_number_project_format($valor, $id_proyecto).'</td>';
														}
														$html .= '</tr>';
													}
												}
											}
											echo $html;
											?>
											</tbody>
										</table>
										
									</div>
								</div>
								<?php } ?>
								
							</div>
							<!-- FIN GRAFICO Y TABLA CONSUMO ENERGIA -->
							
						</div>
					</div>
				</div>
			</div>

		</div>
		
	</div>
	<!-- FIN CONSUMO -->

















	<!-- RESIDUO -->
	<div class="panel-group" id="accordion_residuos">
						
		<div class="panel panel-default">

			<div class="panel-heading p0">
				<a data-toggle="collapse" href="#collapse_residuos" data-parent="#accordion_residuos" class="accordion-toggle">
					<div class="row tab-title ">
						<div class="col-md-5"></div>
						<div class="col-md-3" >
							<h4 style="float:unset !important;" style="text-align:left;"><strong><i class="fa fa-plus-circle font-16" style="padding-right:4em;"></i><?php echo lang('waste'); ?></strong></h4>
						</div>
						<div class="col-md-4"></div>
					</div>
				</a>
			</div>

			<div id="collapse_residuos" class="panel-collapse collapse">
				<div class="row">
					<div class="col-md-12">
						<div id="div_residuos" class="panel panel-body mb0">

							<!-- GRAFICO Y TABLA RESIDUO VOLUMEN -->
							<div class="col-md-12" style="padding-left:0px; padding-right:0px;">
									
								<div id="grafico_residuo_volumen" class="col-md-12 p0 page-title">
									<div class="panel-body p20">
										<h4 style='float:unset !important; text-align:center;'><strong><?php echo lang('waste'); ?> (<?php echo $unidad_volumen; ?>)</strong></h4>
									</div>
									<div class="grafico page-title" id="residuo_volumen"></div>
								</div>

								<div id="tabla_residuo_volumen_total" class="col-md-12 p0">
									<div class="page-title p10" style="border-bottom: none !important;">
										<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-residuo_volumen_total"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
										<table id="table-residuo_volumen_total" class="table table-responsive table-striped">
											<thead>
												<tr>
													<th class="text-left"><?php echo lang('material'); ?></th>
												<?php foreach($years as $year){ ?>
													<th class="text-right"><?php echo $year; ?></th>
												<?php } ?>
												</tr>
											</thead>
											<tbody>
											<?php
												$html = '';
												foreach ($array_id_materiales_valores_volumen_residuo as $id_material => $array_id_categorias_valores_volumen_residuo){

													$nombre_material = $this->Materials_model->get_one($id_material)->nombre;
													$array_total_material_year = array();

													foreach ($array_id_categorias_valores_volumen_residuo as $id_categoria => $arreglo_valores){
													
														$row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
														if($row_alias->alias){
															$nombre_categoria = $row_alias->alias;
														}else{
															$row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
															$nombre_categoria = $row_categoria->nombre;
														}

														$row_categoria = $Client_waste_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));

														if($row_categoria->tabla){
															$valor = 0;
															// SE CALCULA EL VALOR TOTAL POR CATEGORÍA PARA CADA AÑO
															foreach($years as $year){
																$valor = array_sum($arreglo_valores[$year]);
																$array_total_material_year[$year] += $valor;
															}
														}
													}

													$html .= '<tr>';
													$html .= '<td class="text-left">'.$nombre_material.'</td>';
													foreach($years as $year){
														$html .= '<td class="text-right">'.to_number_project_format($array_total_material_year[$year], $id_proyecto).'</td>';
													}
													$html .= '</tr>';
												}
												echo $html;
											?>
											</tbody>
										</table>
									</div>
								</div>

								<div id="tabla_residuo_volumen" class="col-md-12 p0">
									<div class="page-title p10" style="border-bottom: none !important;">
										<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-residuo_volumen"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
										<table id="table-residuo_volumen" class="table table-responsive table-striped">
											<thead>
												<tr>
													<th class="text-left"><?php echo lang('material'); ?></th>
													<th class="text-left"><?php echo lang('category'); ?></th>
												<?php foreach($years as $year){ ?>
													<th class="text-right"><?php echo $year; ?></th>
												<?php } ?>
												</tr>
											</thead>
											<tbody>
											<?php
											
											// $array_grafico_residuos_volumen_categories = array();
											// $array_grafico_residuos_volumen_data = array();
											$html = '';
											foreach ($array_id_materiales_valores_volumen_residuo as $id_material => $array_id_categorias_valores_volumen_residuo){

												$nombre_material = $this->Materials_model->get_one($id_material)->nombre;

												foreach ($array_id_categorias_valores_volumen_residuo as $id_categoria => $arreglo_valores){
													
													$row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
													if($row_alias->alias){
														$nombre_categoria = $row_alias->alias;
													}else{
														$row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
														$nombre_categoria = $row_categoria->nombre;
													}
													
													// ACA VALIDAR SI CLIENTE/PROYECTO/ID_CATEGORIA ESTA HABILITADO PARA MOSTRARSE EN TABLA Y GRAFICO
													// UNA CATEGORIA ES UNICA A NIVEL DE FLUJO/TIPO-UNIDAD/UNIDAD
													// SI UNA CATEGORIA SE REPITE EN OTRO FORMULARIO (DEL MISMO FLUJO), SUMARLO SI TIENE LA MISMA UNIDAD
													// SI TIENE EL MISMO TIPO DE UNIDAD Y OTRA UNIDAD, CONVERTIRLA Y SUMARLA
													// NO PUEDE EXISTIR LA MISMA CATEGORIA EN UN FORMULARIO CON FLUJO CONSUMO Y RESIDUO
													// NO PUEDEN EXISTIR 1 CAMPO TIPO UNIDAD VOLUMEN Y OTRO MASA EN EL MISMO FORMULARIO
													
													// en el mismo form: no debiera poder tener 2 campos detipo unidad masa y volumen, son excuyentes
													// EXISTE UNA EXCEPCION, ENEL, LISTA 5
													
													$row_categoria = $Client_waste_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));
													
													// if($row_categoria->grafico){
													//     $array_grafico_residuos_volumen_categories[] = $nombre_categoria;
													//     $array_grafico_residuos_volumen_data[] = array_sum($arreglo_valores);
													// }
													
													if($row_categoria->tabla){
														$valor = 0;
																
														$html .= '<tr>';
														$html .= '<td class="text-left">'.$nombre_material.'</td>';
														$html .= '<td class="text-left">'.$nombre_categoria.'</td>';
														
														// SE CALCULA EL VALOR TOTAL POR CATEGORÍA PARA CADA AÑO
														foreach($years as $year){
															$valor = array_sum($arreglo_valores[$year]);
															$html .= '<td class="text-right">'.to_number_project_format($valor, $id_proyecto).'</td>';
														}
														$html .= '</tr>';
													}
												}
											}
											echo $html;
											
											?>
											</tbody>
										</table>
										
									</div>
								</div>
								

							</div>
							<!-- FIN GRAFICO Y TABLA RESIDUO VOLUMEN -->
							
							<!-- GRAFICO Y TABLA RESIDUO MASA -->
							<div class="col-md-12" style="padding-left:0px; padding-right:0px;">
									
								<div id="grafico_residuo_masa" class="col-md-12 p0 page-title">
									<div class="panel-body p20">
										<h4 style='float:unset !important; text-align:center;'><strong><?php echo lang('waste'); ?> (<?php echo $unidad_masa; ?>)</strong></h4>
									</div>
									<div class="grafico page-title" id="residuo_masa"></div>
								</div>


								<div id="tabla_residuo_masa_total" class="col-md-12 p0">
									<div class="page-title p10" style="border-bottom: none !important;">
										<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-residuo_masa_total"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
										<table id="table-residuo_masa_total" class="table table-responsive table-striped">
											<thead>
												<tr>
													<th class="text-left"><?php echo lang('material'); ?></th>
												<?php foreach($years as $year){ ?>
													<th class="text-right"><?php echo $year; ?></th>
												<?php } ?>
												</tr>
											</thead>
											<tbody>
											<?php
												$html = '';
												foreach ($array_id_materiales_valores_masa_residuo as $id_material => $array_id_categorias_valores_masa_residuo){

													$nombre_material = $this->Materials_model->get_one($id_material)->nombre;
													$array_total_material_year = array();

													foreach ($array_id_categorias_valores_masa_residuo as $id_categoria => $arreglo_valores){
													
														$row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
														if($row_alias->alias){
															$nombre_categoria = $row_alias->alias;
														}else{
															$row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
															$nombre_categoria = $row_categoria->nombre;
														}

														$row_categoria = $Client_waste_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));

														if($row_categoria->tabla){
															$valor = 0;
															// SE CALCULA EL VALOR TOTAL POR CATEGORÍA PARA CADA AÑO
															foreach($years as $year){
																$valor = array_sum($arreglo_valores[$year]);
																$array_total_material_year[$year] += $valor;
															}
														}
													}

													$html .= '<tr>';
													$html .= '<td class="text-left">'.$nombre_material.'</td>';
													foreach($years as $year){
														$html .= '<td class="text-right">'.to_number_project_format($array_total_material_year[$year], $id_proyecto).'</td>';
													}
													$html .= '</tr>';
												}
												echo $html;
											?>
											</tbody>
										</table>
									</div>
								</div>

								<div id="tabla_residuo_masa" class="col-md-12 p0">
									<div class="page-title p10" style="border-bottom: none !important;">
										<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-residuo_masa"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
										<table id="table-residuo_masa" class="table table-responsive table-striped">
											<thead>
												<tr>
													<th class="text-left"><?php echo lang('material'); ?></th>
													<th class="text-left"><?php echo lang('category'); ?></th>
												<?php foreach($years as $year){ ?>
													<th class="text-right"><?php echo $year; ?></th>
												<?php } ?>
												</tr>
											</thead>
											<tbody>
											<?php
											
											// $array_grafico_residuos_masa_categories = array();
											// $array_grafico_residuos_masa_data = array();
											$html = '';
											foreach ($array_id_materiales_valores_masa_residuo as $id_material => $array_id_categorias_valores_masa_residuo){

												$nombre_material = $this->Materials_model->get_one($id_material)->nombre;

												foreach ($array_id_categorias_valores_masa_residuo as $id_categoria => $arreglo_valores){
												
													$row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
													if($row_alias->alias){
														$nombre_categoria = $row_alias->alias;
													}else{
														$row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
														$nombre_categoria = $row_categoria->nombre;
													}
													
													// ACA VALIDAR SI CLIENTE/PROYECTO/ID_CATEGORIA ESTA HABILITADO PARA MOSTRARSE EN TABLA Y GRAFICO
													// UNA CATEGORIA ES UNICA A NIVEL DE FLUJO/TIPO-UNIDAD/UNIDAD
							
													// SI UNA CATEGORIA SE REPITE EN OTRO FORMULARIO (DEL MISMO FLUJO), SUMARLO SI TIENE LA MISMA UNIDAD
													// SI TIENE EL MISMO TIPO DE UNIDAD Y OTRA UNIDAD, CONVERTIRLA Y SUMARLA
													// NO PUEDE EXISTIR LA MISMA CATEGORIA EN UN FORMULARIO CON FLUJO CONSUMO Y RESIDUO
													// NO PUEDEN EXISTIR 1 CAMPO TIPO UNIDAD VOLUMEN Y OTRO MASA EN EL MISMO FORMULARIO
													
													// en el mismo form: no debiera poder tener 2 campos detipo unidad masa y volumen, son excuyentes
													// EXISTE UNA EXCEPCION, ENEL, LISTA 5
													
													$row_categoria = $Client_waste_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));
													
													// if($row_categoria->grafico){
													//     $array_grafico_residuos_masa_categories[] = $nombre_categoria;
													//     $array_grafico_residuos_masa_data[] = array_sum($arreglo_valores);
													// }
													
													if($row_categoria->tabla){
														$valor = 0;
																
														$html .= '<tr>';
														$html .= '<td class="text-left">'.$nombre_material.'</td>';
														$html .= '<td class="text-left">'.$nombre_categoria.'</td>';
														
														// SE CALCULA EL VALOR TOTAL POR CATEGORÍA PARA CADA AÑO
														foreach($years as $year){
															$valor = array_sum($arreglo_valores[$year]);
															$html .= '<td class="text-right">'.to_number_project_format($valor, $id_proyecto).'</td>';
														}
														$html .= '</tr>';
													}
												}
											}
											echo $html;
											
											?>
											</tbody>
										</table>
										
									</div>
								</div>

							</div>
							<!-- FIN GRAFICO Y TABLA RESIDUO MASA -->

						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	<!-- FIN RESIDUO -->
		








		
</div>
<script type="text/javascript">
$(document).ready(function () {

	$('.slider_total_impacts').slick({
		dots: false,
		infinite: false,
		speed: 300,
		slidesToShow: 1,
		slidesToScroll: 1
	});
	
	//CONSUMOS
	
	<?php if($ocultar_tabla_consumos_volumen) {?>
		$("#tabla_consumo_volumen").remove();
		$("#tabla_consumo_volumen_total").remove();
	<?php } ?>
	
	<?php if($ocultar_grafico_consumos_volumen) {?>
		$("#grafico_consumo_volumen").remove();
		//$("#titulo_tabla_consumo_volumen").append("<h4 style='float:unset !important; text-align:center;'><strong><?php //echo lang('consumptions'); ?> (<?php //echo $unidad_volumen; ?>)</strong></h4>");
	<?php } ?>
	
	<?php if($ocultar_tabla_consumos_masa) {?>
		$("#tabla_consumo_masa").remove();
		$("#tabla_consumo_masa_total").remove();
	<?php } ?>
	
	<?php if($ocultar_grafico_consumos_masa) {?>
		$("#grafico_consumo_masa").remove();
		//$("#titulo_tabla_consumo_masa").append("<h4 style='float:unset !important; text-align:center;'><strong><?php //echo lang('consumptions'); ?> (<?php //echo $unidad_masa; ?>)</strong></h4>");
	<?php } ?>
	
	//RESIDUOS
	<?php if($ocultar_tabla_residuos_volumen) {?>
		$("#tabla_residuo_volumen").remove();
		$("#tabla_residuo_volumen_total").remove();
	<?php } ?>
	
	<?php if($ocultar_grafico_residuos_volumen) {?>
		$("#grafico_residuo_volumen").remove();
		//$("#titulo_tabla_residuo_volumen").append("<h4 style='float:unset !important; text-align:center;'><strong><?php //echo lang('waste'); ?> (<?php //echo $unidad_volumen; ?>)</strong></h4>");
	<?php } ?>
	
	<?php if($ocultar_tabla_residuos_masa) {?>
		$("#tabla_residuo_masa").remove();
		$("#tabla_residuo_masa_total").remove();
	<?php } ?>
	
	<?php if($ocultar_grafico_residuos_masa) {?>
		
		//$("#titulo_tabla_residuo_masa").append("<h4 style='float:unset !important; text-align:center;'><strong><?php //echo lang('waste'); ?> (<?php //echo $unidad_masa; ?>)</strong></h4>");
	<?php } ?>
	
	<?php if($ocultar_tabla_consumos_volumen && $ocultar_grafico_consumos_volumen && $ocultar_tabla_consumos_masa && $ocultar_grafico_consumos_masa) { ?>
		$("#div_consumos").remove();
	<?php } ?>
	
	<?php if($ocultar_tabla_residuos_volumen && $ocultar_grafico_residuos_volumen && $ocultar_tabla_residuos_masa && $ocultar_grafico_residuos_masa) { ?>
		$("#div_residuos").remove();
	<?php } ?>
	
	
	//General Settings
	var decimals_separator = AppHelper.settings.decimalSeparator;
	var thousands_separator = AppHelper.settings.thousandSeparator;
	var decimal_numbers = AppHelper.settings.decimalNumbers;	
	
	var maxHeight = Math.max.apply(null, $("#page-content .huella").map(function (){
		return $(this).find("div.b-b").height();
	}));
	$("#page-content .huella > div.b-b").height(maxHeight);
	

	// CONSUMO VOLUMEN (CV)
	var col_step_cv = 6;  //Cantidad de columnas de desplazamiento en el eje X al presionar el botón '<' o '>'
	var col_numb_cv = col_step_cv - 1  // Cantidad de columnas en el gráfico. ej: 6 -1  (-1 se usa por que se cargan las columnas desde la posición 0 hasta la 5)
	var leftArrow_cv = '';
	var rightArrow_cv = '';

	$('#consumo_volumen').highcharts({
		chart: {
			panning: true,
			reflow: true,
			type: 'column',
			events: {
				load: function () {
					const chart = this;
					
					let {min, max, dataMin, dataMax} = chart.xAxis[0].getExtremes();

					if(col_numb_cv > dataMax) col_numb_cv = dataMax;

					chart.xAxis[0].setExtremes(min, col_numb_cv);
					max = col_numb_cv; // se supone que setExtremes debería dejar max igual a col_numb_cv, pero no funciona.
						// console.log(chart.xAxis[0].getExtremes());

					// Función del botón '<' que ayuda a desplazarse hacia la izquirda por el eje X
					function moveLeft(){
						if (min - col_step_cv >= dataMin) {
							min -= col_step_cv;
							max -= col_step_cv;
						}else{
							min = dataMin;
							max = dataMin + col_numb_cv;
							if(max > dataMax) max = dataMax;
						}
						chart.xAxis[0].setExtremes(min, max);
							// console.log(chart.xAxis[0].getExtremes());
					}
					// Función del botón '>' que ayuda a desplazarse hacia la derecha por el eje X
					function moveRight(){
						if (max + col_step_cv <= dataMax) {
							min += col_step_cv;
							max += col_step_cv;
						}else{
							min = dataMax - col_numb_cv;
							max = dataMax;
							if(min < dataMin) min = dataMin;
						}
						chart.xAxis[0].setExtremes(min, max);
							// console.log(chart.xAxis[0].getExtremes());
					}
					
					// Se crean los botones y agregan sus eventos
					leftArrow_cv = chart.renderer.button('<', chart.plotLeft, 150, 30, 30).attr({ zIndex: 10 });
					rightArrow_cv = chart.renderer.button('>', chart.plotWidth, 150, 30, 30).attr({ zIndex: 10 });
					leftArrow_cv.on('click', moveLeft).add();
					rightArrow_cv.on('click', moveRight).add();

				},	
				drilldown: function(e) {	//evento que se ejecuta al presionar y entrar en una columna
					leftArrow_cv.hide();
					rightArrow_cv.hide();
				},
				drillup: function(e) {		//evento que se ejecuta al devolverse a las columnas externas
					leftArrow_cv.show();
					rightArrow_cv.show();
				}
			} 	
		},
		title: {
			//text: '<strong><?php echo lang('consumptions'); ?> (<?php echo $unidad_volumen; ?>)</strong>'
			text: ''
		},
		subtitle: {
			text: ''
		},
		exporting:{
			enabled: false
		},
		xAxis: {
			type: 'category',
			labels: {
				y: 50,
			},
			min: 0,
			// categories: <?php echo json_encode($array_grafico_consumos_volumen_categories); ?>,
			// crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '<?php echo $unidad_volumen_nombre_real.' ('.$unidad_volumen.')'; ?>'
			},
			stackLabels: {
				enabled: true,
				verticalAlign: 'bottom',
				crop: false,
				overflow: 'none',
				y: 20,
				rotation: -90,
				formatter: function() {
					return this.stack;
				},
				style: {
					// fontSize: '9px'
				}
			},
			// labels:{
			// 	formatter: function(){
			// 		return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
			// 	}
			// },
		},
		credits: {
			enabled: false
		},
		tooltip: {
			headerFormat: '<span style="/*font-size:10px*/">{point.key}</span><table>',
			//pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>'+'<td style="padding:0"><b>{point.y:.1f} m³</b></td></tr>',
			pointFormatter: function(){
				return '<tr><td style="color:'+this.series.color+';padding:0">'+this.series.name+': </td>'+'<td style="padding:0"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+' <?php echo $unidad_volumen; ?></b></td></tr>';
			},
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				stacking: 'normal',
				pointPadding: 0.2,
				borderWidth: 0,
				minPointLength: 12, //altura minima para las columnas, incluye los valores 0
				dataLabels: {
					enabled: true,
					color: '#000000',
					align: 'center',
					//format: '{point.y:.0f}', // one decimal
					formatter: function(){
						return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
					},
					//y: -2, // 10 pixels down from the top
					style: {
						// fontSize: '10px',
						fontFamily: 'Segoe ui, sans-serif'
					}
				}
			}
		},
		// colors: ['#4CD2B1','#5C6BC0'],
		series: <?php echo json_encode($array_grafico_consumos_volumen_data['series']); ?>,
		drilldown: {
			allowPointDrilldown: true,
			series: <?php echo json_encode($array_grafico_consumos_volumen_data['drilldown']); ?>
		}
	
	});
	// FIN CONSUMO VOLUMEN (CV)

	// EXPORTAR TABLA HTML CONSUMO VOLUMEN
	$("#export_table-consumo_volumen").click(function(){
		var id_table = "table-consumo_volumen";
		var file_name = "<?php echo lang('consumptions'); ?> (<?php echo $unidad_volumen; ?>)";
		sheetjs_exportData(id_table, file_name);
	});

	// EXPORTAR TABLA HTML CONSUMO VOLUMEN TOTAL
	$("#export_table-consumo_volumen_total").click(function(){
		var id_table = "table-consumo_volumen_total";
		var file_name = "<?php echo lang("total")." ".lang('consumptions'); ?> (<?php echo $unidad_volumen; ?>)";
		sheetjs_exportData(id_table, file_name);
	});


	// CONSUMO MASA (CM)
	var col_step_cm = 6;  //Cantidad de columnas de desplazamiento en el eje X al presionar el botón '<' o '>'
	var col_numb_cm = col_step_cm - 1  // Cantidad de columnas en el gráfico. ej: 6 -1  (-1 se usa por que se cargan las columnas desde la posición 0 hasta la 5)
	var leftArrow_cm = '';
	var rightArrow_cm = '';

	$('#consumo_masa').highcharts({
		chart: {
			panning: true,
			reflow: true,
			type: 'column',
			events: {
				load: function () {
					const chart = this;
					
					let {min, max, dataMin, dataMax} = chart.xAxis[0].getExtremes();

					if(col_numb_cm > dataMax) col_numb_cm = dataMax;

					chart.xAxis[0].setExtremes(min, col_numb_cm);
					max = col_numb_cm; // se supone que setExtremes debería dejar max igual a col_numb_cm, pero no funciona.
						// console.log(chart.xAxis[0].getExtremes());

					// Función del botón '<' que ayuda a desplazarse hacia la izquirda por el eje X
					function moveLeft(){
						if (min - col_step_cm >= dataMin) {
							min -= col_step_cm;
							max -= col_step_cm;
						}else{
							min = dataMin;
							max = dataMin + col_numb_cm;
							if(max > dataMax) max = dataMax;
						}
						chart.xAxis[0].setExtremes(min, max);
							// console.log(chart.xAxis[0].getExtremes());
					}
					// Función del botón '>' que ayuda a desplazarse hacia la derecha por el eje X
					function moveRight(){
						if (max + col_step_cm <= dataMax) {
							min += col_step_cm;
							max += col_step_cm;
						}else{
							min = dataMax - col_numb_cm;
							max = dataMax;
							if(min < dataMin) min = dataMin;
						}
						chart.xAxis[0].setExtremes(min, max);
							// console.log(chart.xAxis[0].getExtremes());
					}
					
					// Se crean los botones y agregan sus eventos
					leftArrow_cm = chart.renderer.button('<', chart.plotLeft, 150, 30, 30).attr({ zIndex: 10 });
					rightArrow_cm = chart.renderer.button('>', chart.plotWidth, 150, 30, 30).attr({ zIndex: 10 });
					leftArrow_cm.on('click', moveLeft).add();
					rightArrow_cm.on('click', moveRight).add();

				},	
				drilldown: function(e) {	//evento que se ejecuta al presionar y entrar en una columna
					leftArrow_cm.hide();
					rightArrow_cm.hide();
				},
				drillup: function(e) {		//evento que se ejecuta al devolverse a las columnas externas
					leftArrow_cm.show();
					rightArrow_cm.show();
				}
			} 	
		},
		title: {
			//text: '<strong><?php echo lang('consumptions'); ?> (<?php echo $unidad_masa; ?>)</strong>'
			text: ''
		},
		subtitle: {
			text: ''
		},
		exporting:{
			enabled: false
		},
		xAxis: {
			type: 'category',
			labels: {
				y: 50,
			},
			min: 0,
			// categories: <?php echo json_encode($array_grafico_consumos_masa_categories); ?>,
			// crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.')'; ?>'
			},
			stackLabels: {
				enabled: true,
				verticalAlign: 'bottom',
				crop: false,
				overflow: 'none',
				y: 20,
				rotation: -90,
				formatter: function() {
					return this.stack;
				},
				style: {
					// fontSize: '9px'
				}
			},
			// labels:{
			// 	formatter: function(){
			// 		return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
			// 		//return (this.value);
			// 	}
			// },
		},
		credits: {
			enabled: false
		},
		tooltip: {
			headerFormat: '<span style="/*font-size:10px*/">{point.key}</span><table>',
			//pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>'+'<td style="padding:0"><b>{point.y:.1f} m³</b></td></tr>',
			pointFormatter: function(){
				return '<tr><td style="color:'+this.series.color+';padding:0">'+this.series.name+': </td>'+'<td style="padding:0"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+' <?php echo $unidad_masa; ?></b></td></tr>';
			},
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				stacking: 'normal',
				pointPadding: 0.2,
				borderWidth: 0,
				dataLabels: {
					enabled: true,
					//rotation: -90,
					color: '#000000',
					align: 'center',
					//format: '{point.y:.0f}', // one decimal
					formatter: function(){
						return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
					},
					//y: -2, // 10 pixels down from the top
					style: {
						// fontSize: '10px',
						fontFamily: 'Segoe ui, sans-serif'
					}
				}
			}
		},
		// colors: ['#4CD2B1','#5C6BC0'],
		series: <?php echo json_encode($array_grafico_consumos_masa_data['series']); ?>,
		drilldown: {
			allowPointDrilldown: true,
			series: <?php echo json_encode($array_grafico_consumos_masa_data['drilldown']); ?>
		}
	});
	// FIN CONSUMO MASA (CM)

	// EXPORTAR TABLA HTML CONSUMO MASA
	$("#export_table-consumo_masa").click(function(){
		var id_table = "table-consumo_masa";
		var file_name = "<?php echo lang('consumptions'); ?> (<?php echo $unidad_masa; ?>)";
		sheetjs_exportData(id_table, file_name);
	});

	// EXPORTAR TABLA HTML CONSUMO MASA TOTAL
	$("#export_table-consumo_masa_total").click(function(){
		var id_table = "table-consumo_masa_total";
		var file_name = "<?php echo lang("total")." ".lang('consumptions'); ?> (<?php echo $unidad_masa; ?>)";
		sheetjs_exportData(id_table, file_name);
	});
	

	// CONSUMO ENERGÍA (CE)
	var col_step_ce = 6;  //Cantidad de columnas de desplazamiento en el eje X al presionar el botón '<' o '>'
	var col_numb_ce = col_step_ce - 1  // Cantidad de columnas en el gráfico. ej: 6 -1  (-1 se usa por que se cargan las columnas desde la posición 0 hasta la 5)
	var leftArrow_ce = '';
	var rightArrow_ce = '';

	$('#consumo_energia').highcharts({
		chart: {
			panning: true,
			reflow: true,
			type: 'column',
			events: {
				load: function () {
					const chart = this;
					
					let {min, max, dataMin, dataMax} = chart.xAxis[0].getExtremes();

					if(col_numb_ce > dataMax) col_numb_ce = dataMax;

					chart.xAxis[0].setExtremes(min, col_numb_ce);
					max = col_numb_ce; // se supone que setExtremes debería dejar max igual a col_numb_ce, pero no funciona.
						// console.log(chart.xAxis[0].getExtremes());

					// Función del botón '<' que ayuda a desplazarse hacia la izquirda por el eje X
					function moveLeft(){
						if (min - col_step_ce >= dataMin) {
							min -= col_step_ce;
							max -= col_step_ce;
						}else{
							min = dataMin;
							max = dataMin + col_numb_ce;
							if(max > dataMax) max = dataMax;
						}
						chart.xAxis[0].setExtremes(min, max);
							// console.log(chart.xAxis[0].getExtremes());
					}
					// Función del botón '>' que ayuda a desplazarse hacia la derecha por el eje X
					function moveRight(){
						if (max + col_step_ce <= dataMax) {
							min += col_step_ce;
							max += col_step_ce;
						}else{
							min = dataMax - col_numb_ce;
							max = dataMax;
							if(min < dataMin) min = dataMin;
						}
						chart.xAxis[0].setExtremes(min, max);
							// console.log(chart.xAxis[0].getExtremes());
					}
					
					// Se crean los botones y agregan sus eventos
					leftArrow_ce = chart.renderer.button('<', chart.plotLeft, 150, 30, 30).attr({ zIndex: 10 });
					rightArrow_ce = chart.renderer.button('>', chart.plotWidth, 150, 30, 30).attr({ zIndex: 10 });
					leftArrow_ce.on('click', moveLeft).add();
					rightArrow_ce.on('click', moveRight).add();

				},	
				drilldown: function(e) {	//evento que se ejecuta al presionar y entrar en una columna
					leftArrow_ce.hide();
					rightArrow_ce.hide();
				},
				drillup: function(e) {		//evento que se ejecuta al devolverse a las columnas externas
					leftArrow_ce.show();
					rightArrow_ce.show();
				}
			} 	
		},
		title: {
			//text: '<strong><?php echo lang('consumptions'); ?> (<?php echo $unidad_energia; ?>)</strong>'
			text: ''
		},
		subtitle: {
			text: ''
		},
		exporting:{
			enabled: false
		},
		xAxis: {
			type: 'category',
			labels: {
				y: 50,
			},
			min: 0,
			// categories: <?php echo json_encode($array_grafico_consumos_energia_categories); ?>,
			// crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '<?php echo $unidad_energia_nombre_real.' ('.$unidad_energia.')'; ?>'
			},
			stackLabels: {
				enabled: true,
				verticalAlign: 'bottom',
				crop: false,
				overflow: 'none',
				y: 20,
				rotation: -90,
				formatter: function() {
					return this.stack;
				},
				style: {
					// fontSize: '9px'
				}
			},
			// labels:{
			// 	formatter: function(){
			// 		return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
			// 		//return (this.value);
			// 	}
			// },
		},
		credits: {
			enabled: false
		},
		tooltip: {
			headerFormat: '<span style="/*font-size:10px*/">{point.key}</span><table>',
			//pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>'+'<td style="padding:0"><b>{point.y:.1f} m³</b></td></tr>',
			pointFormatter: function(){
				return '<tr><td style="color:'+this.series.color+';padding:0">'+this.series.name+': </td>'+'<td style="padding:0"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+' <?php echo $unidad_energia; ?></b></td></tr>';
			},
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				stacking: 'normal',
				pointPadding: 0.2,
				borderWidth: 0,
				dataLabels: {
					enabled: true,
					//rotation: -90,
					color: '#000000',
					align: 'center',
					//format: '{point.y:.0f}', // one decimal
					formatter: function(){
						return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
					},
					//y: -2, // 10 pixels down from the top
					style: {
						// fontSize: '10px',
						fontFamily: 'Segoe ui, sans-serif'
					}
				}
			}
		},
		// colors: ['#4CD2B1','#5C6BC0'],
		series: <?php echo json_encode($array_grafico_consumos_energia_data['series']); ?>,
		drilldown: {
			allowPointDrilldown: true,
			series: <?php echo json_encode($array_grafico_consumos_energia_data['drilldown']); ?>
		}
	});
	// FIN CONSUMO ENERGÍA (CE)

	// EXPORTAR TABLA HTML CONSUMO ENERGIA
	$("#export_table-consumo_energia").click(function(){
		var id_table = "table-consumo_energia";
		var file_name = "<?php echo lang('consumptions'); ?> (<?php echo $unidad_energia; ?>)";
		sheetjs_exportData(id_table, file_name);
	});

	// EXPORTAR TABLA HTML CONSUMO ENERGIA TOTAL
	$("#export_table-consumo_energia_total").click(function(){
		var id_table = "table-consumo_energia_total";
		var file_name = "<?php echo lang("total")." ".lang('consumptions'); ?> (<?php echo $unidad_energia; ?>)";
		sheetjs_exportData(id_table, file_name);
	});


	// RESIDUOS VOLUMEN (RV)
	var col_step_rv = 6;  //Cantidad de columnas de desplazamiento en el eje X al presionar el botón '<' o '>'
	var col_numb_rv = col_step_rv - 1  // Cantidad de columnas en el gráfico. ej: 6 -1  (-1 se usa por que se cargan las columnas desde la posición 0 hasta la 5)
	var leftArrow_rv = '';
	var rightArrow_rv = '';

	$('#residuo_volumen').highcharts({
		chart: {
			panning: true,
			reflow: true,
			type: 'column',
			events: {
				load: function () {
					const chart = this;
					
					let {min, max, dataMin, dataMax} = chart.xAxis[0].getExtremes();

					if(col_numb_rv > dataMax) col_numb_rv = dataMax;

					chart.xAxis[0].setExtremes(min, col_numb_rv);
					max = col_numb_rv; // se supone que setExtremes debería dejar max igual a col_numb_rv, pero no funciona.
						// console.log(chart.xAxis[0].getExtremes());

					// Función del botón '<' que ayuda a desplazarse hacia la izquirda por el eje X
					function moveLeft(){
						if (min - col_step_rv >= dataMin) {
							min -= col_step_rv;
							max -= col_step_rv;
						}else{
							min = dataMin;
							max = dataMin + col_numb_rv;
							if(max > dataMax) max = dataMax;
						}
						chart.xAxis[0].setExtremes(min, max);
							// console.log(chart.xAxis[0].getExtremes());
					}
					// Función del botón '>' que ayuda a desplazarse hacia la derecha por el eje X
					function moveRight(){
						if (max + col_step_rv <= dataMax) {
							min += col_step_rv;
							max += col_step_rv;
						}else{
							min = dataMax - col_numb_rv;
							max = dataMax;
							if(min < dataMin) min = dataMin;
						}
						chart.xAxis[0].setExtremes(min, max);
							// console.log(chart.xAxis[0].getExtremes());
					}
					
					// Se crean los botones y agregan sus eventos
					leftArrow_rv = chart.renderer.button('<', chart.plotLeft, 150, 30, 30).attr({ zIndex: 10 });
					rightArrow_rv = chart.renderer.button('>', chart.plotWidth, 150, 30, 30).attr({ zIndex: 10 });
					leftArrow_rv.on('click', moveLeft).add();
					rightArrow_rv.on('click', moveRight).add();

				},	
				drilldown: function(e) {	//evento que se ejecuta al presionar y entrar en una columna
					leftArrow_rv.hide();
					rightArrow_rv.hide();
				},
				drillup: function(e) {		//evento que se ejecuta al devolverse a las columnas externas
					leftArrow_rv.show();
					rightArrow_rv.show();
				}
			} 	
		},
		title: {
			//text: '<strong><?php echo lang('waste'); ?> (<?php echo $unidad_volumen; ?>)</strong>'
			text: ''
		},
		subtitle: {
			text: ''
		},
		exporting:{
			enabled: false
		},
		xAxis: {
			type: 'category',
			labels: {
				y: 50,
			},
			min: 0,
			// categories: <?php echo json_encode($array_grafico_residuos_volumen_categories); ?>,
			// crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '<?php echo $unidad_volumen_nombre_real.' ('.$unidad_volumen.')'; ?>'
			},
			stackLabels: {
				enabled: true,
				verticalAlign: 'bottom',
				crop: false,
				overflow: 'none',
				y: 20,
				rotation: -90,
				formatter: function() {
					return this.stack;
				},
				style: {
					// fontSize: '9px'
				}
			},
			// labels:{
			// 	formatter: function(){
			// 		return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator)
			// 		//return (this.value);
			// 	}
			// },
		},
		credits: {
			enabled: false
		},
		tooltip: {
			headerFormat: '<span style="/*font-size:10px*/">{point.key}</span><table>',
			//pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>'+'<td style="padding:0"><b>{point.y:.1f} m³</b></td></tr>',
			pointFormatter: function(){
				return '<tr><td style="color:'+this.series.color+';padding:0">'+this.series.name+': </td>'+'<td style="padding:0"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+' <?php echo $unidad_volumen; ?></b></td></tr>';
			},
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				stacking: 'normal',
				pointPadding: 0.2,
				borderWidth: 0,
				dataLabels: {
					enabled: true,
					//rotation: -90,
					color: '#000000',
					align: 'center',
					//format: '{point.y:.0f}', // one decimal
					formatter: function(){
						return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
					},
					//y: -2, // 10 pixels down from the top
					style: {
						// fontSize: '10px',
						fontFamily: 'Segoe ui, sans-serif'
					}
				}
			}
		},
		// colors: ['#4CD2B1','#5C6BC0'],
		series: <?php echo json_encode($array_grafico_residuos_volumen_data['series']); ?>,
		drilldown: {
			allowPointDrilldown: true,
			series: <?php echo json_encode($array_grafico_residuos_volumen_data['drilldown']); ?>
		}
	});
	// FIN RESIDUOS VOLUMEN (RV)

	// EXPORTAR TABLA HTML RESIDUOS VOLUMEN
	$("#export_table-residuo_volumen").click(function(){
		var id_table = "table-residuo_volumen";
		var file_name = "<?php echo lang('waste'); ?> (<?php echo $unidad_volumen; ?>)";
		sheetjs_exportData(id_table, file_name);
	});

	// EXPORTAR TABLA HTML RESIDUOS VOLUMEN TOTAL
	$("#export_table-residuo_volumen_total").click(function(){
		var id_table = "table-residuo_volumen_total";
		var file_name = "<?php echo lang("total")." ".lang('waste'); ?> (<?php echo $unidad_volumen; ?>)";
		sheetjs_exportData(id_table, file_name);
	});
	

	// RESIDUOS MASA
	col_step = 6;  //Cantidad de columnas de desplazamiento en el eje X al presionar el botón '<' o '>'
	col_numb = col_step - 1  // Cantidad de columnas en el gráfico. ej: 6 -1  (-1 se usa por que se cargan las columnas desde la posición 0 hasta la 5)
	leftArrow = '';
	rightArrow = '';

	$('#residuo_masa').highcharts({
		chart: {
			panning: true,
			reflow: true,
			type: 'column',
			events: {
				load: function () {
					const chart = this;
					
					let {min, max, dataMin, dataMax} = chart.xAxis[0].getExtremes();

					if(col_numb > dataMax) col_numb = dataMax;

					chart.xAxis[0].setExtremes(min, col_numb);
					max = col_numb; // se supone que setExtremes debería dejar max igual a col_numb, pero no funciona.

					// Función del botón '<' que ayuda a desplazarse hacia la izquirda por el eje X
					function moveLeft(){
						if (min - col_step >= dataMin) {
							min -= col_step;
							max -= col_step;
						}else{
							min = dataMin;
							max = dataMin + col_numb;
							if(max > dataMax) max = dataMax;
						}
						chart.xAxis[0].setExtremes(min, max);
						// console.log(chart.xAxis[0].getExtremes());
					}
					// Función del botón '>' que ayuda a desplazarse hacia la derecha por el eje X
					function moveRight(){
						if (max + col_step <= dataMax) {
							min += col_step;
							max += col_step;
						}else{
							min = dataMax - col_numb;
							max = dataMax;
							if(min < dataMin) min = dataMin;
						}
						chart.xAxis[0].setExtremes(min, max);
						// console.log(chart.xAxis[0].getExtremes());
					}

					// Se crean los botones y agregan sus eventos
					leftArrow = chart.renderer.button('<', chart.plotLeft, 150, 30, 30).attr({ zIndex: 10 });
					rightArrow = chart.renderer.button('>', chart.plotWidth, 150, 30, 30).attr({ zIndex: 10 });
					leftArrow.on('click', moveLeft).add();
					rightArrow.on('click', moveRight).add();
					
				},	
				drilldown: function(e) {	//evento que se ejecuta al presionar y entrar en una columna
					leftArrow.hide();
					rightArrow.hide();
				},
				drillup: function(e) {		//evento que se ejecuta al devolverse a las columnas externas
					leftArrow.show();
					rightArrow.show();
				}
			} 	
		},
		title: {
			//text: '<strong><?php echo lang('waste'); ?> (<?php echo $unidad_masa; ?>)</strong>'
			text: ''
		},
		subtitle: {
			text: ''
		},
		exporting:{
			enabled: false
		},
		xAxis: {
			type: 'category',
			labels: {
				y: 50,
			}, 
			min: 0,
			// categories: <?php echo json_encode($array_grafico_residuos_masa_categories); ?>,
			// crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.')'; ?>'
			},
			stackLabels: {
				enabled: true,
				verticalAlign: 'bottom',
				crop: false,
				overflow: 'none',
				y: 20,
				rotation: -90,
				formatter: function() {
					return this.stack;
				},
				style: {
					// fontSize: '9px'
				}
			},
			// labels:{
			// 	formatter: function(){
			// 		return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
			// 		//return (this.value);
			// 	}
			// },
		},
		credits: {
			enabled: false
		},
		tooltip: {
			headerFormat: '<span style="/*font-size:10px*/">{point.key}</span><table>',
			//pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>'+'<td style="padding:0"><b>{point.y:.1f} m³</b></td></tr>',
			pointFormatter: function(){
				return '<tr><td style="color:'+this.series.color+';padding:0">'+this.series.name+': </td>'+'<td style="padding:0"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+' <?php echo $unidad_masa; ?></b></td></tr>';
			},
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				stacking: 'normal',
				pointPadding: 0.2,
				borderWidth: 0,
				dataLabels: {
					enabled: true,
					//rotation: -90,
					color: '#000000',
					align: 'center',
					//format: '{point.y:.0f}', // one decimal
					formatter: function(){
						return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
					},
					//y: -2, // 10 pixels down from the top
					style: {
						// fontSize: '10px',
						fontFamily: 'Segoe ui, sans-serif'
					}
				}
			}
		},
		// colors: ['#4CD2B1','#5C6BC0'],
		series: <?php echo json_encode($array_grafico_residuos_masa_data['series']); ?>,
		drilldown: {
			allowPointDrilldown: true,
			series: <?php echo json_encode($array_grafico_residuos_masa_data['drilldown']); ?>
		}
	});
	// FIN RESIDUOS MASA

	// EXPORTAR TABLA HTML RESIDUOS MASA
	$("#export_table-residuo_masa").click(function(){
		var id_table = "table-residuo_masa";
		var file_name = "<?php echo lang('waste'); ?> (<?php echo $unidad_masa; ?>)";
		sheetjs_exportData(id_table, file_name);
	});

	// EXPORTAR TABLA HTML RESIDUOS MASA TOTAL
	$("#export_table-residuo_masa_total").click(function(){
		var id_table = "table-residuo_masa_total";
		var file_name = "<?php echo lang("total")." ".lang('waste'); ?> (<?php echo $unidad_masa; ?>)";
		sheetjs_exportData(id_table, file_name);
	});
	
	$(document).on('click', 'a.accordion-toggle', function () {
		
		var icon = $(this).find('i');
		
		if($(this).hasClass('collapsed')){
			icon.removeClass('fa fa-minus-circle font-16');
			icon.addClass('fa fa-plus-circle font-16');
		} else {
			icon.removeClass('fa fa-plus-circle font-16');
			icon.addClass('fa fa-minus-circle font-16');
		}

	});
	
});
</script> 