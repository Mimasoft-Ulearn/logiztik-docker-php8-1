<!-- CONSUMO -->
<?php 

	$array_total_material_values_year_volumen_consumo = array(); // PARA GUARDAR CONSUMOS VOLUMEN TOTALES POR MATERIAL PARA TODOS LOS PROYECTOS
	$array_material_categories_values_year_volumen_consumo = array(); // PARA GUARDAR CONSUMOS VOLUMEN POR MATERIAL / CATEGORÍA PARA TODOS LOS PROYECTOS
	$array_material_subprojects_values_year_volumen_consumo = array(); // PARA GUARDAR CONSUMOS VOLUMEN POR MATERIAL / SUBPROYECTO PARA TODOS LOS PROYECTOS

	$array_total_material_values_year_masa_consumo = array(); // PARA GUARDAR CONSUMOS MASA TOTALES POR MATERIAL PARA TODOS LOS PROYECTOS
	$array_material_categories_values_year_masa_consumo = array(); // PARA GUARDAR CONSUMOS MASA POR MATERIAL / CATEGORÍA PARA TODOS LOS PROYECTOS
	$array_material_subprojects_values_year_masa_consumo = array(); // PARA GUARDAR CONSUMOS MASA POR MATERIAL / SUBPROYECTO PARA TODOS LOS PROYECTOS

	$array_total_material_values_year_energia_consumo = array(); // PARA GUARDAR CONSUMOS ENERGÍA TOTALES POR MATERIAL PARA TODOS LOS PROYECTOS
	$array_material_categories_values_year_energia_consumo = array(); // PARA GUARDAR CONSUMOS ENERGÍA POR MATERIAL / CATEGORÍA PARA TODOS LOS PROYECTOS
	$array_material_subprojects_values_year_energia_consumo = array(); // PARA GUARDAR CONSUMOS ENERGÍA POR MATERIAL / SUBPROYECTO PARA TODOS LOS PROYECTOS

	// CONSUMO VOLUMEN
	foreach($array_id_materiales_valores_volumen as $id_proyecto => $datos_proyectos_volumen){
		foreach($datos_proyectos_volumen as $id_subproyecto => $datos_materiales_volumen){
			$subproyecto = $this->Subprojects_model->get_one($id_subproyecto);
			foreach($datos_materiales_volumen as $id_material => $datos_categorias_volumen){
				$nombre_material = $this->Materials_model->get_one($id_material)->nombre;
				foreach($datos_categorias_volumen as $id_categoria => $arreglo_valores){
					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $client_id, 'deleted' => 0));
					if($row_alias->alias){
						$nombre_categoria = $row_alias->alias;
					}else{
						$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
						$nombre_categoria = $row_categoria->nombre;
					}
					$valor = 0;
					foreach($years as $year){
						$valor = $arreglo_valores[$year];
						$array_total_material_values_year_volumen_consumo[$nombre_material][$year] += $valor;
						$array_material_categories_values_year_volumen_consumo[$nombre_material][$nombre_categoria][$year] += $valor;
						if($subproyecto->id_proyecto == $id_proyecto){
							$array_material_subprojects_values_year_volumen_consumo[$nombre_material][$nombre_categoria][$array_projects[$id_proyecto]][$array_subprojects[$id_subproyecto]][$year] += $valor;
						}
					}
				}
			}
		}
	}


	// CONSUMO MASA
	foreach($array_id_materiales_valores_masa as $id_proyecto => $datos_proyectos_masa){
		foreach($datos_proyectos_masa as $id_subproyecto => $datos_materiales_masa){
			$subproyecto = $this->Subprojects_model->get_one($id_subproyecto);
			foreach($datos_materiales_masa as $id_material => $datos_categorias_masa){
				$nombre_material = $this->Materials_model->get_one($id_material)->nombre;
				foreach($datos_categorias_masa as $id_categoria => $arreglo_valores){
					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $client_id, 'deleted' => 0));
					if($row_alias->alias){
						$nombre_categoria = $row_alias->alias;
					}else{
						$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
						$nombre_categoria = $row_categoria->nombre;
					}
					$valor = 0;
					foreach($years as $year){
						$valor = $arreglo_valores[$year];
						$array_total_material_values_year_masa_consumo[$nombre_material][$year] += $valor;
						$array_material_categories_values_year_masa_consumo[$nombre_material][$nombre_categoria][$year] += $valor;
						if($subproyecto->id_proyecto == $id_proyecto){
							$array_material_subprojects_values_year_masa_consumo[$nombre_material][$nombre_categoria][$array_projects[$id_proyecto]][$array_subprojects[$id_subproyecto]][$year] += $valor;
						}
					}
				}
			}
		}
	}


	// CONSUMO ENERGÍA
	foreach($array_id_materiales_valores_energia as $id_proyecto => $datos_proyectos_energia){
		foreach($datos_proyectos_energia as $id_subproyecto => $datos_materiales_energia){
			$subproyecto = $this->Subprojects_model->get_one($id_subproyecto);
			foreach($datos_materiales_energia as $id_material => $datos_categorias_energia){
				$nombre_material = $this->Materials_model->get_one($id_material)->nombre;
				foreach($datos_categorias_energia as $id_categoria => $arreglo_valores){
					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $client_id, 'deleted' => 0));
					if($row_alias->alias){
						$nombre_categoria = $row_alias->alias;
					}else{
						$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
						$nombre_categoria = $row_categoria->nombre;
					}
					$valor = 0;
					foreach($years as $year){
						$valor = $arreglo_valores[$year];
						$array_total_material_values_year_energia_consumo[$nombre_material][$year] += $valor;
						$array_material_categories_values_year_energia_consumo[$nombre_material][$nombre_categoria][$year] += $valor;
						if($subproyecto->id_proyecto == $id_proyecto){
							$array_material_subprojects_values_year_energia_consumo[$nombre_material][$nombre_categoria][$array_projects[$id_proyecto]][$array_subprojects[$id_subproyecto]][$year] += $valor;
						}
					}
				}
			}
		}
	}

?>

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
											<?php foreach($array_total_material_values_year_volumen_consumo as $nombre_material => $array_data) { ?>
												<tr>
													<td class="text-left"><?php echo $nombre_material; ?></td>
													<?php foreach($years as $year){ ?>
														<td class="text-right"><?php echo to_number_client_format($array_data[$year], $client_id); ?></td>
													<?php } ?>
												</tr>
											<?php } ?>
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
											<?php foreach($array_material_categories_values_year_volumen_consumo as $nombre_material => $array_data_categories) { ?>
												<?php foreach($array_data_categories as $nombre_categoria => $array_data) { ?>
													<tr>
														<td class="text-left"><?php echo $nombre_material; ?></td>
														<td class="text-left"><?php echo $nombre_categoria; ?></td>
														<?php foreach($years as $year){ ?>
															<td class="text-right"><?php echo to_number_client_format($array_data[$year], $client_id); ?></td>
														<?php } ?>
													</tr>
												<?php } ?>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>

							<!-- TABLA CONSUMO VOLUMEN SUCURSALES -->
							<div id="tabla_consumo_volumen_subprojects" class="col-md-12 p0">
								<div class="page-title p10" style="border-bottom: none !important;">
									<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-consumo_volumen_subproject"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
									<table id="table-consumo_volumen_subproject" class="table table-responsive table-striped">
										<thead>
											<tr>
												<th class="text-left"><?php echo lang('material'); ?></th>
												<th class="text-left"><?php echo lang('category'); ?></th>
												<th class="text-left"><?php echo lang('project'); ?></th>
												<th class="text-left"><?php echo lang('subproject'); ?></th>
												<?php foreach($years as $year){ ?>
													<th class="text-right"><?php echo $year; ?></th>
												<?php } ?>
											</tr>
										</thead>
										<tbody>
											<?php foreach($array_material_subprojects_values_year_volumen_consumo as $nombre_material => $array_data_categories) { ?>
												<?php foreach($array_data_categories as $name_category => $array_data_projects) { ?>
													<?php foreach($array_data_projects as $name_project => $array_data_subprojects) { ?>
														<?php foreach($array_data_subprojects as $name_subproject => $array_data) { ?>
															<tr>
																<td class="text-left"><?php echo $nombre_material; ?></td>
																<td class="text-left"><?php echo $name_category; ?></td>
																<td class="text-left"><?php echo $name_project; ?></td>
																<td class="text-left"><?php echo $name_subproject; ?></td>
																<?php foreach($years as $year){ ?>
																	<td class="text-right"><?php echo to_number_client_format($array_data[$year], $client_id); ?></td>
																<?php } ?>
															</tr>
														<?php } ?>
													<?php } ?>
												<?php } ?>
											<?php } ?>
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
											<?php foreach($array_total_material_values_year_masa_consumo as $nombre_material => $array_data) { ?>
												<tr>
													<td class="text-left"><?php echo $nombre_material; ?></td>
													<?php foreach($years as $year){ ?>
														<td class="text-right"><?php echo to_number_client_format($array_data[$year], $client_id); ?></td>
													<?php } ?>
												</tr>
											<?php } ?>
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
											<?php foreach($array_material_categories_values_year_masa_consumo as $nombre_material => $array_data_categories) { ?>
												<?php foreach($array_data_categories as $nombre_categoria => $array_data) { ?>
													<tr>
														<td class="text-left"><?php echo $nombre_material; ?></td>
														<td class="text-left"><?php echo $nombre_categoria; ?></td>
														<?php foreach($years as $year){ ?>
															<td class="text-right"><?php echo to_number_client_format($array_data[$year], $client_id); ?></td>
														<?php } ?>
													</tr>
												<?php } ?>
											<?php } ?>
										</tbody>
									</table>
									
								</div>
							</div>

							<div id="tabla_consumo_masa_subprojects" class="col-md-12 p0">
								<div class="page-title p10" style="border-bottom: none !important;">
									<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-consumo_masa_subproject"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
									<table id="table-consumo_masa_subproject" class="table table-responsive table-striped">
										<thead>
											<tr>
												<th class="text-left"><?php echo lang('material'); ?></th>
												<th class="text-left"><?php echo lang('category'); ?></th>
												<th class="text-left"><?php echo lang('project'); ?></th>
												<th class="text-left"><?php echo lang('subproject'); ?></th>
												<?php foreach($years as $year){ ?>
													<th class="text-right"><?php echo $year; ?></th>
												<?php } ?>
											</tr>
										</thead>
										<tbody>
											<?php foreach($array_material_subprojects_values_year_masa_consumo as $nombre_material => $array_data_categories) { ?>
												<?php foreach($array_data_categories as $name_category => $array_data_projects) { ?>
													<?php foreach($array_data_projects as $name_project => $array_data_subprojects) { ?>
														<?php foreach($array_data_subprojects as $name_subproject => $array_data) { ?>
															<tr>
																<td class="text-left"><?php echo $nombre_material; ?></td>
																<td class="text-left"><?php echo $name_category; ?></td>
																<td class="text-left"><?php echo $name_project; ?></td>
																<td class="text-left"><?php echo $name_subproject; ?></td>
																<?php foreach($years as $year){ ?>
																	<td class="text-right"><?php echo to_number_client_format($array_data[$year], $client_id); ?></td>
																<?php } ?>
															</tr>
														<?php } ?>
													<?php } ?>
												<?php } ?>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
							
						</div>
						<!-- FIN GRAFICO Y TABLA CONSUMO MASA -->
						
						<!-- GRAFICO Y TABLA CONSUMO ENERGIA -->
						<div class="col-md-12" style="padding-left:0px; padding-right:0px;">
							
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
											<?php foreach($array_total_material_values_year_energia_consumo as $nombre_material => $array_data) { ?>
												<tr>
													<td class="text-left"><?php echo $nombre_material; ?></td>
													<?php foreach($years as $year){ ?>
														<td class="text-right"><?php echo to_number_client_format($array_data[$year], $client_id); ?></td>
													<?php } ?>
												</tr>
											<?php } ?>
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
											<?php foreach($array_material_categories_values_year_energia_consumo as $nombre_material => $array_data_categories) { ?>
												<?php foreach($array_data_categories as $nombre_categoria => $array_data) { ?>
													<tr>
														<td class="text-left"><?php echo $nombre_material; ?></td>
														<td class="text-left"><?php echo $nombre_categoria; ?></td>
														<?php foreach($years as $year){ ?>
															<td class="text-right"><?php echo to_number_client_format($array_data[$year], $client_id); ?></td>
														<?php } ?>
													</tr>
												<?php } ?>
											<?php } ?>
										</tbody>
									</table>
									
								</div>
							</div>

							<div id="tabla_consumo_energia_subprojects" class="col-md-12 p0">
								<div class="page-title p10" style="border-bottom: none !important;">
									<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-consumo_energia_subproject"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
									<table id="table-consumo_energia_subproject" class="table table-responsive table-striped">
										<thead>
											<tr>
												<th class="text-left"><?php echo lang('material'); ?></th>
												<th class="text-left"><?php echo lang('category'); ?></th>
												<th class="text-left"><?php echo lang('project'); ?></th>
												<th class="text-left"><?php echo lang('subproject'); ?></th>
												<?php foreach($years as $year){ ?>
													<th class="text-right"><?php echo $year; ?></th>
												<?php } ?>
											</tr>
										</thead>
										<tbody>
											<?php foreach($array_material_subprojects_values_year_energia_consumo as $nombre_material => $array_data_categories) { ?>
												<?php foreach($array_data_categories as $name_category => $array_data_projects) { ?>
													<?php foreach($array_data_projects as $name_project => $array_data_subprojects) { ?>
														<?php foreach($array_data_subprojects as $name_subproject => $array_data) { ?>
															<tr>
																<td class="text-left"><?php echo $nombre_material; ?></td>
																<td class="text-left"><?php echo $name_category; ?></td>
																<td class="text-left"><?php echo $name_project; ?></td>
																<td class="text-left"><?php echo $name_subproject; ?></td>
																<?php foreach($years as $year){ ?>
																	<td class="text-right"><?php echo to_number_client_format($array_data[$year], $client_id); ?></td>
																<?php } ?>
															</tr>
														<?php } ?>
													<?php } ?>
												<?php } ?>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
							
						</div>
						<!-- FIN GRAFICO Y TABLA CONSUMO ENERGIA -->
						
					</div>
				</div>
			</div>
		</div>

	</div>
	
</div>
<!-- FIN CONSUMO -->

<script type="text/javascript">
$(document).ready(function () {

	let obj_language = {
		lengthMenu: "_MENU_",
		zeroRecords: AppLanugage.noRecordFoundText,
		info: "_START_-_END_ / _TOTAL_",
		sInfo: "_START_-_END_ / _TOTAL_",
		infoFiltered: "(_MAX_)",
		search: "",
		searchPlaceholder: AppLanugage.search,
		sInfoEmpty: "0-0 / 0",
		sInfoFiltered: "(_MAX_)",
		sInfoPostFix: "",
		sInfoThousands: ",",
		sProcessing: "<div class='table-loader'><span class='loading'></span></div>",
		"oPaginate": {
			"sPrevious": "<i class='fa fa-angle-double-left'></i>",
			"sNext": "<i class='fa fa-angle-double-right'></i>"
		},
		sInfoEmpty: "<?php echo lang("no_record_found"); ?>",
		sZeroRecords: "<?php echo lang("no_record_found"); ?>",
		sEmptyTable: "<?php echo lang("no_record_found"); ?>",
	};

	$("#table-consumo_volumen_total").dataTable({
		language: obj_language
	});
	$("select[name='table-consumo_volumen_total_length']").select2();

	$("#table-consumo_volumen").dataTable({
		language: obj_language
	});
	$("select[name='table-consumo_volumen_length']").select2();

	$("#table-consumo_volumen_subproject").dataTable({
		language: obj_language
	});
	$("select[name='table-consumo_volumen_subproject_length']").select2();

	$("#table-consumo_masa_total").dataTable({
		language: obj_language
	});
	$("select[name='table-consumo_masa_total_length']").select2();

	$("#table-consumo_masa").dataTable({
		language: obj_language
	});
	$("select[name='table-consumo_masa_length']").select2();

	$("#table-consumo_masa_subproject").dataTable({
		language: obj_language
	});
	$("select[name='table-consumo_masa_subproject_length']").select2();

	$("#table-consumo_energia_total").dataTable({
		language: obj_language
	});
	$("select[name='table-consumo_energia_total_length']").select2();

	$("#table-consumo_energia").dataTable({
		language: obj_language
	});
	$("select[name='table-consumo_energia_length']").select2();

	$("#table-consumo_energia_subproject").dataTable({
		language: obj_language
	});
	$("select[name='table-consumo_energia_subproject_length']").select2();


	

	// General Settings
	var decimals_separator = AppHelper.settings.decimalSeparatorClient;
	var thousands_separator = AppHelper.settings.thousandSeparatorClient;
	var decimal_numbers = AppHelper.settings.decimalNumbersClient;

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

	// EXPORTAR TABLA HTML CONSUMO VOLUMEN SUBPROYECTOS TOTAL
	$("#export_table-consumo_volumen_subproject").click(function(){
		var id_table = "table-consumo_volumen_subproject";
		var file_name = "<?php echo lang("branch_office")." ".lang('consumptions'); ?> (<?php echo $unidad_volumen; ?>)";
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
	
	// EXPORTAR TABLA HTML CONSUMO MASA SUBPROYECTOS TOTAL
	$("#export_table-consumo_masa_subproject").click(function(){
		var id_table = "table-consumo_masa_subproject";
		var file_name = "<?php echo lang("branch_office")." ".lang('consumptions'); ?> (<?php echo $unidad_masa; ?>)";
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

	// EXPORTAR TABLA HTML CONSUMO ENERGÍA SUBPROYECTOS TOTAL
	$("#export_table-consumo_energia_subproject").click(function(){
		var id_table = "table-consumo_energia_subproject";
		var file_name = "<?php echo lang("branch_office")." ".lang('consumptions'); ?> (<?php echo $unidad_energia; ?>)";
		sheetjs_exportData(id_table, file_name);
	});
	
});
</script> 