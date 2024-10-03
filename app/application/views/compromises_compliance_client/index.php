<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("compliance"); ?> /</a>
  <a class="breadcrumb-item" href=""><?php echo lang("compliance_details"); ?></a>
</nav>

<div class="row mb15">
	<div class="col-md-12">
		<div class="page-title clearfix">
			<h1><?php echo lang('compliance_details'); ?></h1>
		</div>
	</div>
</div>

<?php if($puede_ver == 1) { ?>

	<?php echo form_open(get_uri("#"), array("id" => "compromises_compliance_client-form", "class" => "general-form", "role" => "form")); ?>
	
	<div class="panel panel-default">
		<div class="panel-body">
		
			<div class="col-md-6">
			
				<div class="form-group multi-column">
			
					<label class="col-md-3" style="padding-right:0px;margin-right:0px;"><?php echo lang('date_range') ?></label>

					<div class="col-md-4">
						<?php 
							echo form_input(array(
								"id" => "default_date_field1",
								"name" => "default_date_field1",
								"value" => "",
								"class" => "form-control",
								"placeholder" => lang('since'),
								"data-rule-required" => true,
								"data-msg-required" => lang("field_required"),
								//"data-rule-greaterThanOrEqual" => 'default_date_field2',
								//"data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
								"autocomplete" => "off",
							));
						?>
					</div>
				
					<div class="col-md-4">
						<?php 
							echo form_input(array(
								"id" => "default_date_field2",
								"name" => "default_date_field2",
								"value" => "",
								"class" => "form-control",
								"placeholder" => lang('until'),
								"data-rule-required" => true,
								"data-msg-required" => lang("field_required"),
								"data-rule-greaterThanOrEqual" => "#default_date_field1",
								"data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
								"autocomplete" => "off",
							));
						?>
					</div>
					
				</div>
									
			</div>
			
			<div class="col-md-6">
				<div class="pull-right">
					<div class="btn-group" role="group">
						<button id="generar" type="submit" class="btn btn-primary"><span class="fa fa-eye"></span> <?php echo lang('generate'); ?></button>
					</div>
					<?php if($puede_ver == 1 && $id_compromiso_reportables) { ?>
						<div class="btn-group" role="group">
							<a href="#" class="btn btn-danger pull-right" id="compromises_compliance_pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a>
						</div>
					<?php } ?>
					<div class="btn-group" role="group">
						<button id="btn_clean" type="button" class="btn btn-default">
							<i class="fa fa-broom" aria-hidden="true"></i> <?php echo lang('clean_query'); ?>
						</button>
					</div>
				</div>
			</div>
			
		</div>

	</div>
	<?php echo form_close(); ?> 

	<div id="content_group">
	
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


			<!--
			<div class="panel panel-default mb15">
				<div class="page-title clearfix">
					<h1><?php echo lang('summary_by_evaluated'); ?></h1>
				</div>
				<div class="panel-body">


					<?php foreach($evaluados_rca as $evaluado) { ?>
						<div class="col-xs-12 col-sm-6 col-md-2 col-lg-2 col-xl-2">
							<div class="panel panel-default">
							<div class="page-title clearfix panel-success">
								<div class="pt10 pb10 text-center"> <?php echo $evaluado->nombre_evaluado; ?> </div>
							</div>
							<div class="panel-body">
								<div id="grafico_resumen_evaluado_<?php echo $evaluado->id; ?>" style="height: 240px;" class="grafico_resumen_evaluado" data-nombre_evaluado="<?php echo $evaluado->nombre_evaluado; ?>" data-tiene_evaluacion="1"></div>
							</div>
							</div>
						</div>
					<? } ?>
				</div>

				<div class="panel-body">

					<div class="table-responsive">
					<div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">

						<table id="tabla_resumen_por_evaluado" class="table table-striped">
							<thead>

								<tr>
									<th rowspan="2" class="text-center" style="vertical-align:middle;"><?php echo lang("compliance_status"); ?></th>

									<?php foreach($evaluados_rca as $evaluado) { ?>
										<th colspan="2" class="text-center"><?php echo $evaluado->nombre_evaluado; ?></th>
									<?php } ?>
								</tr>
								<tr>
									<?php foreach($evaluados_rca as $evaluado) { ?>
										<th class="text-center">N°</th>
										<th class="text-center">%</th>
									<?php } ?>
								</tr>

							</thead>
							<tbody>

							<tr>
								<th class="text-left"><?php echo lang("total_applicable_compromises"); ?></th>
								<?php foreach($evaluados_rca as $evaluado) { ?>
										<?php if($array_total_por_evaluado_rca[$evaluado->id]){ ?>
											<td class=" text-right"><?php echo to_number_project_format(array_sum($array_total_por_evaluado_rca[$evaluado->id]), $id_proyecto); ?></td>
										<?php } else { ?>
											<td class=" text-right"><?php echo to_number_project_format(0, $id_proyecto); ?></td>
										<?php } ?>
										<td class=" text-right"><?php echo to_number_project_format(100, $id_proyecto); ?> %</td>
								<?php } ?>
								</tr>

								<?php foreach($total_cantidades_estados_evaluados_rca as $estado_evaluado) { ?>

									<tr>
									<td class="text-left"><?php echo $estado_evaluado["nombre_estado"]; ?></td>
									<?php foreach($estado_evaluado["evaluados"] as $id_evaluado => $evaluado) { ?>

											<?php
											$total_evaluado = array_sum($array_total_por_evaluado_rca[$id_evaluado]);
											if($total_evaluado == 0){
												$porcentaje = 0;
											} else {
												$porcentaje = ($evaluado["cant"] * 100) / ($total_evaluado);
											}
											?>

											<td class="text-right"><?php echo to_number_project_format($evaluado["cant"], $id_proyecto); ?></td>
											<td class="text-right"><?php echo to_number_project_format($porcentaje, $id_proyecto); ?> %</td>
									<?php } ?>
									</tr>

								<?php } ?>

							</tbody>
						</table>

					</div>
					</div>

				</div>

			</div>
			-->

			<!--
			<div class="panel panel-default mb15">
				<div class="page-title clearfix">
					<h1><?php echo lang('compliance_status'); ?></h1>
					<div class="btn-group pull-right" role="group">
						<button type="button" class="btn btn-success" id="excel_compliance_status"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table id="compliance_status-table" class="display" cellspacing="0" width="100%">
						</table>
					</div>
				</div>
			</div>
			-->


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

	</div>

<?php } else { ?>

	<div class="row">
		<div class="col-md-12 col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div id="app-alert-d1via" class="app-alert alert alert-danger alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>-->
						<div class="app-alert-message"><?php echo lang("content_disabled"); ?></div>
						<div class="progress">
							<div class="progress-bar progress-bar-danger hide" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php } ?>

</div>
<script type="text/javascript">
$(document).ready(function(){

		//General Settings
		var decimals_separator = AppHelper.settings.decimalSeparator;
		var thousands_separator = AppHelper.settings.thousandSeparator;
		var decimal_numbers = AppHelper.settings.decimalNumbers;

		/* $("#compliance_status-table").appTable({
            source: '<?php echo_uri("compromises_compliance_client/list_data/".$id_compromiso_rca); ?>',
			filterDropdown: [
				{name: "reportabilidad", class: "w200", <?php if($reportabilidad_dropdown){ ?>options: <?php echo $reportabilidad_dropdown; ?><?php } ?>}
			],
            columns: [
                {title: "<?php echo lang("compromise_number"); ?>", "class": "text-right dt-head-center w50"},
				{title: "<?php echo lang("reportability"); ?>", "class": "text-center dt-head-center w50"}
				<?php echo $columnas;  ?>,
				//{title: '<i class="fa fa-bars" style="padding: 0px 70px"; ></i>', "class": "text-center option w150p"}
            ],
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
		}); */

		$("#compliance_status_reportables-table").appTable({
            source: '<?php echo_uri("compromises_compliance_client/list_data_reportables/".$id_compromiso_reportables); ?>',
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
			var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("compromises_compliance_client/get_excel_compliance_status_reportables")?>').attr('method','POST').attr('target', '_self').appendTo('body');
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
		<?php if( !empty(array_filter($compromisos_reportables)) && !empty($array_environmental_topic) ){ ?>
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



		$('#excel_compliance_status').click(function(){
			var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("compromises_compliance_client/get_excel_compliance_status")?>').attr('method','POST').attr('target', '_self').appendTo('body');
			$form.submit();
		});


		$("#compromises_compliance_pdf").on('click', function(e) {

			appLoader.show();

			var decimal_numbers = '<?php echo $general_settings->decimal_numbers; ?>';
			var decimals_separator = '<?php echo ($general_settings->decimals_separator == 1) ? "." : ","; ?>';
			var thousands_separator = '<?php echo ($general_settings->thousands_separator == 1)? "." : ","; ?>';



			/*

			// Gráfico Cumplimientos Totales
			var image_cumplimientos_totales;
			<?php if($total_compromisos_aplicables_rca){ ?>

				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "12px";
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.size = 150;

				var chart = $('#grafico_cumplimientos_totales').highcharts().options.chart;
				var title = $('#grafico_cumplimientos_totales').highcharts().options.title;
				var series = $('#grafico_cumplimientos_totales').highcharts().options.series;
				var plotOptions = $('#grafico_cumplimientos_totales').highcharts().options.plotOptions;
				var colors = $('#grafico_cumplimientos_totales').highcharts().options.colors;
				var exporting = $('#grafico_cumplimientos_totales').highcharts().options.exporting;
				var credits = $('#grafico_cumplimientos_totales').highcharts().options.credits;

				var obj = {};
				obj.options = JSON.stringify({
					"chart":chart,
					"title":title,
					"series":series,
					"plotOptions":plotOptions,
					"colors":colors,
					"exporting":exporting,
					"credits":credits,
				});

				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;

				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);

				image_cumplimientos_totales = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';

				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.size = null;

			<?php } ?>

			// Gráficos Resumen por Evaluado
			var graficos_resumen_evaluados = {};
			$('.grafico_resumen_evaluado').each(function(){

				var id = $(this).attr('id');
				var nombre_evaluado = $(this).attr("data-nombre_evaluado");
				var tiene_evaluacion = $(this).attr("data-tiene_evaluacion");

				if(tiene_evaluacion == "1"){

					$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = true;
					$('#' + id).highcharts().options.title.text = nombre_evaluado;
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

					var image_resumen_evaluado = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';

					graficos_resumen_evaluados[id] = image_resumen_evaluado;

					$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = false;
					$('#' + id).highcharts().options.plotOptions.pie.size = null;
					$('#' + id).highcharts().options.legend.itemStyle.fontSize = "9px;";

				} else {

					var image = id;
					graficos_resumen_evaluados[id] = image;

				}

			});

			*/

			// Gráfico Resumen Cumplimiento
			var grafico_resumen_cumplimiento;
			<?php if(!empty(array_filter($compromisos_reportables))){ ?>

				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "12px";
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.size = 150;

				var chart = $('#grafico_cumplimientos_reportables').highcharts().options.chart;
				var title = $('#grafico_cumplimientos_reportables').highcharts().options.title;
				var series = $('#grafico_cumplimientos_reportables').highcharts().options.series;
				var plotOptions = $('#grafico_cumplimientos_reportables').highcharts().options.plotOptions;
				var colors = $('#grafico_cumplimientos_reportables').highcharts().options.colors;
				var exporting = $('#grafico_cumplimientos_reportables').highcharts().options.exporting;
				var credits = $('#grafico_cumplimientos_reportables').highcharts().options.credits;

				var obj = {};
				obj.options = JSON.stringify({
					"chart":chart,
					"title":title,
					"series":series,
					"plotOptions":plotOptions,
					"colors":colors,
					"exporting":exporting,
					"credits":credits,
				});

				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;

				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);

				grafico_resumen_cumplimiento = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';

				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.size = null;

			<?php } ?>
			// FIN Gráfico Resumen Cumplimiento


			// Gráfico Resumen por IGA
			var grafico_resumen_por_iga;
			<?php if(!empty(array_filter($compromisos_reportables))){ ?>

				var chart = $('#grafico_resumen_por_iga').highcharts().options.chart;
				var title = $('#grafico_resumen_por_iga').highcharts().options.title;
				var subtitle = $('#grafico_resumen_por_iga').highcharts().options.subtitle;
				var xAxis = $('#grafico_resumen_por_iga').highcharts().options.xAxis;
				var yAxis = $('#grafico_resumen_por_iga').highcharts().options.yAxis;
				var series = $('#grafico_resumen_por_iga').highcharts().options.series;
				var plotOptions = $('#grafico_resumen_por_iga').highcharts().options.plotOptions;
				var colors = $('#grafico_resumen_por_iga').highcharts().options.colors;
				var exporting = $('#grafico_resumen_por_iga').highcharts().options.exporting;

				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors,
					"exporting":exporting,
					credits: {enabled:false}
				});

				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;

				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);

				grafico_resumen_por_iga = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';

			<?php } ?>
			// FIN Gráfico Resumen por IGA

			// Gráfico Resumen por Tipo de Cumplimiento
			var grafico_resumen_por_tipo_cumplimiento;
			<?php if(!empty(array_filter($compromisos_reportables))){ ?>

				var chart = $('#grafico_resumen_por_tipo_cumplimiento').highcharts().options.chart;
				var title = $('#grafico_resumen_por_tipo_cumplimiento').highcharts().options.title;
				var subtitle = $('#grafico_resumen_por_tipo_cumplimiento').highcharts().options.subtitle;
				var xAxis = $('#grafico_resumen_por_tipo_cumplimiento').highcharts().options.xAxis;
				var yAxis = $('#grafico_resumen_por_tipo_cumplimiento').highcharts().options.yAxis;
				var series = $('#grafico_resumen_por_tipo_cumplimiento').highcharts().options.series;
				var plotOptions = $('#grafico_resumen_por_tipo_cumplimiento').highcharts().options.plotOptions;
				var colors = $('#grafico_resumen_por_tipo_cumplimiento').highcharts().options.colors;
				var exporting = $('#grafico_resumen_por_tipo_cumplimiento').highcharts().options.exporting;

				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors,
					"exporting":exporting,
					credits: {enabled:false}
				});

				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;

				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);

				grafico_resumen_por_tipo_cumplimiento = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';

			<?php } ?>
			// FIN Gráfico Resumen por Tipo de Cumplimiento

			// Gráfico Resumen por Tema ambiental
			var grafico_resumen_por_tema_ambiental;
			<?php if( !empty(array_filter($compromisos_reportables)) && !empty($array_environmental_topic) ){ ?>

				var chart = $('#grafico_resumen_por_tema_ambiental').highcharts().options.chart;
				var title = $('#grafico_resumen_por_tema_ambiental').highcharts().options.title;
				var subtitle = $('#grafico_resumen_por_tema_ambiental').highcharts().options.subtitle;
				var xAxis = $('#grafico_resumen_por_tema_ambiental').highcharts().options.xAxis;
				var yAxis = $('#grafico_resumen_por_tema_ambiental').highcharts().options.yAxis;
				var series = $('#grafico_resumen_por_tema_ambiental').highcharts().options.series;
				var plotOptions = $('#grafico_resumen_por_tema_ambiental').highcharts().options.plotOptions;
				var colors = $('#grafico_resumen_por_tema_ambiental').highcharts().options.colors;
				var exporting = $('#grafico_resumen_por_tema_ambiental').highcharts().options.exporting;

				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors,
					"exporting":exporting,
					credits: {enabled:false}
				});

				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;

				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);

				grafico_resumen_por_tema_ambiental = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';


			<?php } ?>
			// FIN Gráfico Resumen por Tema ambiental

			// Gráfico Resumen por Área responsable
			var grafico_resumen_por_area_responsable;
			<?php if(!empty(array_filter($compromisos_reportables))){ ?>

				$('#grafico_resumen_por_area_responsable').highcharts().options.chart.height = 400;

				var chart = $('#grafico_resumen_por_area_responsable').highcharts().options.chart;
				var title = $('#grafico_resumen_por_area_responsable').highcharts().options.title;
				var subtitle = $('#grafico_resumen_por_area_responsable').highcharts().options.subtitle;
				var xAxis = $('#grafico_resumen_por_area_responsable').highcharts().options.xAxis;
				var yAxis = $('#grafico_resumen_por_area_responsable').highcharts().options.yAxis;
				var series = $('#grafico_resumen_por_area_responsable').highcharts().options.series;
				var plotOptions = $('#grafico_resumen_por_area_responsable').highcharts().options.plotOptions;
				var colors = $('#grafico_resumen_por_area_responsable').highcharts().options.colors;
				var exporting = $('#grafico_resumen_por_area_responsable').highcharts().options.exporting;

				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors,
					"exporting":exporting,
					credits: {enabled:false}
				});

				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;

				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);

				grafico_resumen_por_area_responsable = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				$('#grafico_resumen_por_area_responsable').highcharts().options.chart.height = "";


			<?php } ?>
			// FIN Gráfico Resumen por Área responsable

			var imagenes_graficos = {
				grafico_resumen_cumplimiento: grafico_resumen_cumplimiento,
				grafico_resumen_por_iga: grafico_resumen_por_iga,
				grafico_resumen_por_tipo_cumplimiento: grafico_resumen_por_tipo_cumplimiento,
				grafico_resumen_por_tema_ambiental: grafico_resumen_por_tema_ambiental,
				grafico_resumen_por_area_responsable: grafico_resumen_por_area_responsable
			};

			var start_date = $('#default_date_field1').val();
			var end_date = $('#default_date_field2').val();

			$.ajax({
				url:  '<?php echo_uri("compromises_compliance_client/get_pdf") ?>',
				type:  'post',
				data: {
					imagenes_graficos: imagenes_graficos,
					start_date: start_date,
					end_date: end_date
				},
				//dataType:'json',
				success: function(respuesta){

					var uri = '<?php echo get_setting("temp_file_path") ?>' + respuesta;
					var link = document.createElement("a");
					link.download = respuesta;
					link.href = uri;
					link.click();

					borrar_temporal(uri);
				}

			});

		});

		function borrar_temporal(uri){

			$.ajax({
				url:  '<?php echo_uri("compromises_compliance_client/borrar_temporal") ?>',
				type:  'post',
				data: {uri:uri},
				//dataType:'json',
				success: function(respuesta){
					appLoader.hide();
				}

			});

		}

		function getChartName(obj){
			var tmp = null;
			$.support.cors = true;
			$.ajax({
				async: false,
				type: 'post',
				dataType: 'text',
				url : AppHelper.highchartsExportUrl,
				data: obj,
				crossDomain:true,
				success: function (data) {
					tmp = data.replace(/files\//g,'');
					tmp = tmp.replace(/.png/g,'');
				}
			});
			return tmp;
		}

		$("#compromises_compliance_client-form").appForm({
            ajaxSubmit: false
        });
		
		$("#compromises_compliance_client-form").submit(function(e){
			e.preventDefault();
			return false;
		});
		
		setDatePicker("#default_date_field1");
		setDatePicker("#default_date_field2");

		$('#generar').click(function(){
			
			$('#compromises_compliance_pdf').attr('disabled', true);

			
			var id_cliente = '<?php echo $id_cliente; ?>';
			var id_proyecto = '<?php echo $id_proyecto; ?>';
			var start_date = $('#default_date_field1').val();
			var end_date = $('#default_date_field2').val();
			
			if(id_cliente && id_proyecto && start_date && end_date){
				if((start_date < end_date) || (start_date == end_date)){
	
					$.ajax({
						url:'<?php echo_uri("compromises_compliance_client/get_compliance_details"); ?>',
						type:'post',
						data:{
							id_proyecto: id_proyecto,
							id_cliente: id_cliente,
							start_date: start_date,
							end_date: end_date
						},
						beforeSend: function(){
					   		//$('#content_group').html('<div style="padding:20px;"><div class="circle-loader"></div><div>');
							$('#content_group').html('<div class="panel"><div style="padding:20px;"><div class="circle-loader"></div><div><div>');
						},
						success: function(respuesta){
							$('#content_group').html(respuesta);	
							//$('#content_group').html(respuesta);
							$('#compromises_compliance_pdf').removeAttr('disabled');
						}
					});	
					
				}
			}
			
		});

		$('#btn_clean').click(function(){
			
			$('#compromises_compliance_pdf').attr('disabled', true);
			$('#default_date_field1').val("");
			$('#default_date_field2').val("");
			
			$.ajax({
				url:'<?php echo_uri("compromises_compliance_client/get_compliance_details"); ?>',
				type:'post',
				beforeSend: function() {
					$('#content_group').html('<div class="panel"><div style="padding:20px;"><div class="circle-loader"></div><div><div>');
				},
				success: function(respuesta){;
					$('#content_group').html(respuesta);	
					$('#compromises_compliance_pdf').removeAttr('disabled');
				}
			});	
			
		});

	});

</script>
