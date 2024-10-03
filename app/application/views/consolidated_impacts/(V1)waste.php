<!-- RESIDUO -->
<?php 

	$array_total_material_values_year_volumen_residuo = array(); // PARA GUARDAR RESIDUOS VOLUMEN TOTALES POR MATERIAL PARA TODOS LOS PROYECTOS
	$array_material_categories_values_year_volumen_residuo = array(); // PARA GUARDAR RESIDUOS VOLUMEN POR MATERIAL / CATEGORÍA PARA TODOS LOS PROYECTOS
	$array_material_subprojects_values_year_volumen_residuo = array(); // PARA GUARDAR RESIDUOS VOLUMEN POR MATERIAL / SUBPROYECTO PARA TODOS LOS PROYECTOS

	$array_total_material_values_year_masa_residuo = array(); // PARA GUARDAR RESIDUOS MASA TOTALES POR MATERIAL PARA TODOS LOS PROYECTOS
	$array_material_categories_values_year_masa_residuo = array(); // PARA GUARDAR RESIDUOS MASA POR MATERIAL / CATEGORÍA PARA TODOS LOS PROYECTOS
	$array_material_subprojects_values_year_masa_residuo = array(); // PARA GUARDAR RESIDUOS MASA POR MATERIAL / SUBPROYECTO PARA TODOS LOS PROYECTOS

	// RESIDUO VOLUMEN
	foreach($array_id_materiales_valores_volumen_residuo as $id_material => $array_id_categorias_valores_volumen){

		$nombre_material = $this->Materials_model->get_one($id_material)->nombre;

		foreach($array_id_categorias_valores_volumen as $id_categoria => $arreglo_valores){
											
			$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $client_id, 'deleted' => 0));
			if($row_alias->alias){
				$nombre_categoria = $row_alias->alias;
			}else{
				$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
				$nombre_categoria = $row_categoria->nombre;
			}
			
			$valor = 0;
			// SE CALCULA EL VALOR TOTAL POR CATEGORÍA PARA CADA AÑO
			foreach($years as $year){
				$valor = array_sum($arreglo_valores[$year]);
				$array_total_material_values_year_volumen_residuo[$nombre_material][$year] += $valor;
				$array_material_categories_values_year_volumen_residuo[$nombre_material][$nombre_categoria][$year] += $valor;

				foreach($arreglo_valores[$year] as $id_sucursal => $valor_sucursal){
					$array_material_subprojects_values_year_volumen_residuo[$nombre_material][$array_subprojects[$id_sucursal]][$year] += $valor_sucursal;
				}

			}
			
		}

	}

	// RESIDUO MASA
	foreach($array_id_materiales_valores_masa_residuo as $id_material => $array_id_categorias_valores_masa){

		$nombre_material = $this->Materials_model->get_one($id_material)->nombre;
		
		foreach ($array_id_categorias_valores_masa as $id_categoria => $arreglo_valores){
		
			$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $client_id, 'deleted' => 0));
			if($row_alias->alias){
				$nombre_categoria = $row_alias->alias;
			}else{
				$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
				$nombre_categoria = $row_categoria->nombre;
			}

			$valor = 0;
			// SE CALCULA EL VALOR TOTAL POR CATEGORÍA PARA CADA AÑO
			foreach($years as $year){
				$valor = array_sum($arreglo_valores[$year]);
				$array_total_material_values_year_masa_residuo[$nombre_material][$year] += $valor;
				$array_material_categories_values_year_masa_residuo[$nombre_material][$nombre_categoria][$year] += $valor;

				foreach($arreglo_valores[$year] as $id_sucursal => $valor_sucursal){
					$array_material_subprojects_values_year_masa_residuo[$nombre_material][$array_subprojects[$id_sucursal]][$year] += $valor_sucursal;
				}

			}
			
		}

	}

?>
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
						<?php if(false){ // OCULTAR SECCIÓN RESIDUO VOLUMEN ?>
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
										<?php foreach($array_total_material_values_year_volumen_residuo as $nombre_material => $array_data) { ?>
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
											<?php foreach($array_material_categories_values_year_volumen_residuo as $nombre_material => $array_data_categories) { ?>
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

							<div id="tabla_residuo_volumen_subprojects" class="col-md-12 p0">
								<div class="page-title p10" style="border-bottom: none !important;">
									<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-residuo_volumen_subproject"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
									<table id="table-residuo_volumen_subproject" class="table table-responsive table-striped">
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
										<?php foreach($array_material_subprojects_values_year_volumen_residuo as $nombre_material => $array_data_subprojects) { ?>
												<?php foreach($array_data_subprojects as $name_subproject => $array_data) { ?>
													<tr>
														<td class="text-left"><?php echo $nombre_material; ?></td>
														<td class="text-left"><?php echo $name_subproject; ?></td>
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
							
						</div>
						<?php } ?>
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
											<?php foreach($array_total_material_values_year_masa_residuo as $nombre_material => $array_data) { ?>
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
											<?php foreach($array_material_categories_values_year_masa_residuo as $nombre_material => $array_data_categories) { ?>
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

							<div id="tabla_residuo_masa_subprojects" class="col-md-12 p0">
								<div class="page-title p10" style="border-bottom: none !important;">
									<button type="button" class="btn btn-xs btn-success pull-right" id="export_table-residuo_masa_subproject"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
									<table id="table-residuo_masa_subproject" class="table table-responsive table-striped">
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
											<?php foreach($array_material_subprojects_values_year_masa_residuo as $nombre_material => $array_data_subprojects) { ?>
												<?php foreach($array_data_subprojects as $name_subproject => $array_data) { ?>
													<tr>
														<td class="text-left"><?php echo $nombre_material; ?></td>
														<td class="text-left"><?php echo $name_subproject; ?></td>
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

						</div>
						<!-- FIN GRAFICO Y TABLA RESIDUO MASA -->

					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<!-- FIN RESIDUO -->
		



<script type="text/javascript">
$(document).ready(function () {
	
	//General Settings
	var decimals_separator = AppHelper.settings.decimalSeparatorClient;
	var thousands_separator = AppHelper.settings.thousandSeparatorClient;
	var decimal_numbers = AppHelper.settings.decimalNumbersClient;	

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

	// EXPORTAR TABLA HTML RESIDUOS VOLUMEN SUBPROYECTOS TOTAL
	$("#export_table-residuo_volumen_subproject").click(function(){
		var id_table = "table-residuo_volumen_subproject";
		var file_name = "<?php echo lang("branch_office")." ".lang('waste'); ?> (<?php echo $unidad_volumen; ?>)";
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

	// EXPORTAR TABLA HTML RESIDUOS MASA SUBPROYECTOS TOTAL
	$("#export_table-residuo_masa_subproject").click(function(){
		var id_table = "table-residuo_masa_subproject";
		var file_name = "<?php echo lang("branch_office")." ".lang('waste'); ?> (<?php echo $unidad_masa; ?>)";
		sheetjs_exportData(id_table, file_name);
	});
	
});
</script> 