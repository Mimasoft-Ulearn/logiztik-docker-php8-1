<?php if($id_compromiso_reportables) { ?>
	
	<div class="panel panel-default mb15">
		<div class="page-title clearfix">
			<h1><?php echo lang('compliance_summary'); ?></h1>
			<?php if($puede_ver == 1 && $id_compromiso_reportables) { ?>
				<?php echo modal_anchor(get_uri("compromises_reportables_matrix_config/view/" . $id_compromiso_reportables), lang('view_matrix')." "."<i class='fa fa-eye'></i>", array("class" => "btn btn-default pull-right", "title" => lang('view_matrix'), "data-post-id_compromiso" => $id_compromiso_reportables)); ?>
			<?php } ?>
		</div>

		<div class="panel-body">
												
			<div class="col-md-6">
			
				<table class="table table-striped">
					<thead>
						<tr>
							<th rowspan="2" class="text-center" style="vertical-align:middle;"><?php echo lang("general_compliance_status"); ?></th>
							<th colspan="2" class="text-center"><?php echo lang("sub_total"); ?></th>
						</tr>
						<tr>
							<th class="text-center">N°</th>
							<th class="text-center">%</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($compromisos_reportables as $cr) { ?>
							<?php
								if($total_reportables == 0){
									$porcentaje = 0;
								} else {
									$porcentaje = ($cr["cant"] * 100) / ($total_reportables);
								}
							?>
							<tr>
								<td class="text-left"><?php echo $cr["nombre_estado"]; ?></td>
								<td class="text-right"><?php echo to_number_project_format($cr["cant"], $id_proyecto); ?></td>
								<td class="text-right"><?php echo to_number_project_format($porcentaje, $id_proyecto); ?> %</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			
			</div>
			
			<div class="col-md-6">
				<div class="panel panel-default">
				<div class="page-title clearfix panel-success">
					<div class="pt10 pb10 text-center"><?php echo lang("compliance_summary"); ?></div>
				</div>
				<div class="panel-body">
					<div id="grafico_cumplimientos_reportables" style="height: 240px;"></div>
				</div>
				</div>
			</div>
			
		</div> 

	</div>


	<div class="panel panel-default mb15">
		<div class="page-title clearfix">
			<h1><?php echo lang('summary_by_iga'); ?></h1>
		</div>

		<div class="panel-body">
			<div id="grafico_resumen_por_iga" style="height: 480px;"></div>
		</div>

	</div>

	<div class="panel panel-default mb15">
		<div class="page-title clearfix">
			<h1><?php echo lang('summary_by_compliance_type'); ?></h1>
		</div>

		<div class="panel-body">
			<div id="grafico_resumen_por_tipo_cumplimiento" style="height: 480px;"></div>
		</div>

	</div>

	<div class="panel panel-default mb15">
		<div class="page-title clearfix">
			<h1><?php echo lang('summary_by_environmental_topic'); ?></h1>
		</div>

		<div class="panel-body">
			<div id="grafico_resumen_por_tema_ambiental" style="height: 480px;"></div>
		</div>

	</div>

	<div class="panel panel-default mb15">
		<div class="page-title clearfix">
			<h1><?php echo lang('summary_by_managements_and_areas'); ?></h1>
		</div>

		<div class="panel-body">
			<div id="grafico_resumen_por_area_responsable" style="height: 480px;"></div>
		</div>

	</div>

	<div class="panel panel-default mb15">
		<div class="page-title clearfix">
			<h1><?php echo lang('compliance_status'); ?></h1>
			<div class="btn-group pull-right" role="group">
				<button type="button" class="btn btn-success" id="excel_compliance_status_reportables"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
			</div>
		</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table id="compliance_status_reportables-table" class="display" cellspacing="0" width="100%">
				</table>
			</div>
		</div>
	</div>
	
<?php } else { ?>

	<div class="panel panel-default mb15">
		<div class="panel-body">              
			<div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
				<?php echo lang('the_project').' "'.$nombre_proyecto.'" '.lang('compromise_matrix_not_enabled'); ?>
			</div>
		</div>	  
	</div>

<?php } ?>

<script type="text/javascript">
	$(document).ready(function(){

		//General Settings
		var decimals_separator = AppHelper.settings.decimalSeparator;
		var thousands_separator = AppHelper.settings.thousandSeparator;
		var decimal_numbers = AppHelper.settings.decimalNumbers;

		<?php if($start_date && $end_date){ ?>
			var source_url = '<?php echo_uri("compromises_compliance_client/list_data_reportables/".$id_compromiso_reportables."/".$start_date."/".$end_date); ?>';
		<?php } else { ?>
			var source_url = '<?php echo_uri("compromises_compliance_client/list_data_reportables/".$id_compromiso_reportables); ?>';
		<?php } ?>

		$("#compliance_status_reportables-table").appTable({
            source: source_url,
			filterDropdown: [
				{name: "impact_on_the_environment_due_to_non_compliance", class: "w200", options: <?php echo $impact_on_the_environment_dropdown; ?>},
				{name: "environmental_topic", class: "w200", options: <?php echo $environmental_topic_dropdown; ?>},
				{name: "environmental_management_instrument", class: "w200", options: <?php echo $environmental_management_instrument_dropdown; ?>},
				{name: "compliance_type", class: "w200", options: <?php echo $compliance_types_dropdown; ?>}
			],
            columns: [
				{title: "<?php echo lang("n_activity"); ?>", "class": "text-right dt-head-center w50"},
				{title: "<?php echo lang("environmental_management_instrument"); ?>", "class": "text-right dt-head-center w50"},
				{title: "<?php echo lang("compliance_type"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("environmental_topic"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("impact_on_the_environment_due_to_non_compliance"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("commitment_description"); ?>", "class": "text-center dt-head-center"},
				{title: "<?php echo lang("responsible_area"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("status"); ?>", "class": "text-center dt-head-center"},
				{title: "<?php echo lang("evidence"); ?>", "class": "text-center dt-head-center option"},
				{title: "<?php echo lang("observations"); ?>", "class": "text-center dt-head-center option"}
				//{title: '<i class="fa fa-bars" style="padding: 0px 70px"; ></i>', "class": "text-center option w150p"}
			],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            	$(nRow).find('[data-toggle="tooltip"]').tooltip();
			}
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
		});
		
		$('#excel_compliance_status_reportables').click(function(){
			var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("compromises_compliance_client/get_excel_compliance_status_reportables"."/".$start_date."/".$end_date)?>').attr('method','POST').attr('target', '_self').appendTo('body');
			$form.submit();
		});

		$('[data-toggle="tooltip"]').tooltip();

		 
		<?php if($total_compromisos_aplicables_rca){ ?>


			$('#grafico_cumplimientos_totales').highcharts({
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
						return '<b>'+ this.point.name +'</b>: '+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +' %';
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
						format: '<b>{point.name}</b>: {point.percentage:.' + decimal_numbers + 'f} %',
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
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("compromises").'_'.clean(lang("total_compliances")).'_'.date("Y-m-d"); ?>
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
				colors: [
					<?php
						foreach($total_cantidades_estados_evaluados_rca as $estado) {
							echo "'".$estado["color"]."',";
						}
					?>
				],
				series: [{
					name: 'Porcentaje',
					colorByPoint: true,
					data: [
					<?php foreach($total_cantidades_estados_evaluados_rca as $estado) { ?>
						{
							name: '<?php echo $estado["nombre_estado"]; ?>',
							y: <?php echo ($estado["cantidad_categoria"] * 100) / $total_compromisos_aplicables_rca; ?>
						},
					<?php } ?>

					]
				}]
			});

		<?php }else{?>
				$('#grafico_cumplimientos_totales').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
		<?php } ?>

		<?php if(!empty(array_filter($compromisos_reportables))){ ?>

			$('#grafico_cumplimientos_reportables').highcharts({
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
						return '<b>'+ this.point.name +'</b>: '+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +' %';
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
						format: '<b>{point.name}</b>: {point.percentage:.' + decimal_numbers + 'f} %',
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
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("compromises").'_'.clean(lang("reportable_compliances")).'_'.date("Y-m-d"); ?>
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
				colors: [
					<?php
						foreach($grafico_reportables as $cr) {
							echo "'".$cr["color"]."',";
						}
					?>
				],

				//colors: ['#398439', '#ac2925', '#d58512'],
				series: [{
					name: 'Porcentaje',
					colorByPoint: true,
					data: [
					<?php foreach($grafico_reportables as $cr) { ?>
						{
							name: '<?php echo $cr["nombre_estado"]; ?>',
							y: <?php echo $cr["porcentaje"]; ?>
						},
					<?php } ?>

					]
				}]
			});

		<?php }else{ ?>
			$('#grafico_cumplimientos_reportables').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
		<?php }?>

		// SECCIÓN GRÁFICO RESUMEN POR IGA
		<?php if(!empty(array_filter($compromisos_reportables))){ ?>
			$('#grafico_resumen_por_iga').highcharts({
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
					text: '',
				},
				xAxis: {
					categories: <?php echo json_encode($array_instrumento_gestion_ambiental); ?>
				},
				yAxis: {
					min: 0,
					title: '',
					labels: {
						style: {
							fontSize:'11px'
						},
						format: "{value:,." + decimal_numbers + "f} %",
						/*formatter: function () {
							return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' %';
						}*/
					},
					/* lineColor: '#FF0000',
					lineWidth: 1 */
				},
				credits: {
					enabled: false
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.x +'</b>: <br>' + this.series.name + ': ' + numberFormat(this.y, 0, decimals_separator, thousands_separator) + ' (' + numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +' %)';
					},
					//pointFormat: '{series.name}: <b>{point.y}%</b>'
				},
				plotOptions: {
					column: {
						stacking: 'percent',
						dataLabels: {
							enabled: true,
							//format: "{y:,." + decimal_numbers + "f}",
							format: '{point.percentage:.' + decimal_numbers + 'f} %',
							/* formatter:function() {	// Para quitar los valores 0 de una columna
								if(this.y != 0) {
								return this.y;
								}
							} */
						},
					}
				},
				series: <?php echo json_encode($grafico_resumen_por_iga); ?>,
				legend: {
					enabled: true,
					itemStyle:{
						fontSize: "9px"
					}
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

			});

		<?php }else{ ?>
			$('#grafico_resumen_por_iga').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
		<?php }?>
		// FIN SECCIÓN GRÁFICO RESUMEN POR IGA


		// SECCIÓN GRÁFICO RESUMEN POR TIPO DE CUMPLIMIENTO
		<?php if(!empty(array_filter($compromisos_reportables))){ ?>
			$('#grafico_resumen_por_tipo_cumplimiento').highcharts({
				chart: {
					type: 'column',
					events: {
					/* load: function() {
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
						} */
					}
				},
				title: {
					text: '',
				},
				xAxis: {
					categories: <?php echo json_encode($array_compliance_types); ?>
				},
				yAxis: {
					min: 0,
					title: '',
					labels: {
						formatter: function () {
							return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' %';
						}
					},
					/* lineColor: '#FF0000',
					lineWidth: 1 */
				},
				credits: {
					enabled: false
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.x +'</b>: <br>' + this.series.name + ': ' + numberFormat(this.y, 0, decimals_separator, thousands_separator) + ' (' + numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +' %)';
					},
					//pointFormat: '{series.name}: <b>{point.y}%</b>'
				},
				plotOptions: {
					column: {
						stacking: 'percent',
						dataLabels: {
							enabled: true,
							//format: "{y:,." + decimal_numbers + "f}",
							format: '{point.percentage:.' + decimal_numbers + 'f} %'
							/* formatter:function() {	// Para quitar los valores 0 de una columna
								if(this.y != 0) {
								return this.y;
								}
							} */
						},
					}
				},
				series: <?php echo json_encode($grafico_resumen_por_tipo_cumplimiento); ?>,
				legend: {
					enabled: true,
					itemStyle:{
						fontSize: "9px"
					}
				},
				exporting: {
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("compliance").'_'.clean(lang("summary_by_compliance_type")).'_'.date("Y-m-d"); ?>
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

			});

		<?php }else{ ?>
			$('#grafico_resumen_por_tipo_cumplimiento').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
		<?php }?>
		// FIN SECCIÓN GRÁFICO RESUMEN POR TIPO DE CUMPLIMIENTO


		// SECCIÓN GRÁFICO RESUMEN POR TEMA AMBIENTAL
		<?php if( !empty(array_filter($compromisos_reportables)) || !empty($array_environmental_topic) ){ ?>
			$('#grafico_resumen_por_tema_ambiental').highcharts({
				chart: {
					type: 'column',
					events: {
					/* load: function() {
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
						} */
					}
				},
				title: {
					text: '',
				},
				xAxis: {
					categories: <?php echo json_encode($array_environmental_topic); ?>
				},
				yAxis: {
					min: 0,
					title: '',
					labels: {
						formatter: function () {
							return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' %';
						}
					},
					/* lineColor: '#FF0000',
					lineWidth: 1 */
				},
				credits: {
					enabled: false
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.x +'</b>: <br>' + this.series.name + ': ' + numberFormat(this.y, 0, decimals_separator, thousands_separator) + ' (' + numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +' %)';
					},
					//pointFormat: '{series.name}: <b>{point.y}%</b>'
				},
				plotOptions: {
					column: {
						stacking: 'percent',
						dataLabels: {
							enabled: true,
							//format: "{y:,." + decimal_numbers + "f}",
							format: '{point.percentage:.' + decimal_numbers + 'f} %'
							/* formatter:function() {	// Para quitar los valores 0 de una columna
								if(this.y != 0) {
								return this.y;
								}
							} */
						},
					}
				},
				series: <?php echo json_encode($grafico_resumen_por_tema_ambiental); ?>,
				legend: {
					enabled: true,
					itemStyle:{
						fontSize: "9px"
					}
				},
				exporting: {
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("compliance").'_'.clean(lang("summary_by_environmental_topic")).'_'.date("Y-m-d"); ?>
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

			});

		<?php }else{ ?>
			$('#grafico_resumen_por_tema_ambiental').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
		<?php }?>
		// FIN SECCIÓN GRÁFICO RESUMEN POR TEMA AMBIENTAL

		// SECCIÓN GRÁFICO RESUMEN POR ÁREA RESPONSABLE
		<?php if(!empty(array_filter($compromisos_reportables))){ ?>

			// GRÁFICO CON DRILLDOWN PARA LOS PROYECTOS QUE NO SEAN QALI NI CPC
			$('#grafico_resumen_por_area_responsable').highcharts({
				chart: {
					// zoomType: 'x',
					reflow: true,
					vresetZoomButton: {
						position: {
							align: 'left',
							x: 0
						}
					},
					type: 'column'
				},
				lang: {
					drillUpText: '<?php echo lang("go_back"); ?>'
				},
				title: {
					text: '',
				},
				credits: {
					enabled: false
				},
				exporting: {
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("compliance").'_'.clean(lang("summary_by_responsible_area")).'_'.date("Y-m-d"); ?>
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
				xAxis: {
					type: 'category'
				},
				yAxis: {
					min: 0,
					title: '',
					labels: {
						format: "{value:,." + decimal_numbers + "f} %",
						/*formatter: function () {
							return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' %';
						}*/
					},
					/* lineColor: '#FF0000',
					lineWidth: 1 */
				},
				legend: {
					enabled: true,
					itemStyle:{
						fontSize: "9px"
					}
				},
				tooltip: {
					formatter: function() {
						
						return '<b>'+ this.point.name +'</b>: <br>' + this.series.name + ': ' + numberFormat(this.y, 0, decimals_separator, thousands_separator) + ' (' + numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +' %)';
					},
					//pointFormat: '{series.name}: <b>{point.y}%</b>'
				},
				plotOptions: {
					series: {
						stacking: 'normal',
						dataLabels: {
							enabled: true,
							//format: "{y:,." + decimal_numbers + "f}",
							format: '{point.percentage:.' + decimal_numbers + 'f} %'
							/* formatter:function() {	// Para quitar los valores 0 de una columna
								if(this.y != 0) {
								return this.y;
								}
							} */
						},
					}
				},
				series: <?php echo json_encode($grafico_resumen_por_area_responsable['series']); ?>,
				drilldown: {
					allowPointDrilldown: false,
					drillUpButton: {
						position: {
							align: 'left'
						}
					},
					series:  <?php echo json_encode($grafico_resumen_por_area_responsable['drilldown']); ?>
				}

			});

		<?php }else{ ?>
			$('#grafico_resumen_por_area_responsable').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
		<?php }?>
		// FIN SECCIÓN GRÁFICO RESUMEN POR ÁREA RESPONSABLE
		
	});
</script>