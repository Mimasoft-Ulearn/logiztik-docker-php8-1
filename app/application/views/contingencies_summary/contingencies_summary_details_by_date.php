<!-- INICIO SECCIÓN TIPOS DE EVENTO -->
<div class="panel panel-default mb15">
	<div class="page-title clearfix">
		<h1><?php echo lang('event_types'); ?></h1>
	</div>
	<div class="panel-body">

		<div class="col-md-6">
	
		<table class="table table-striped">
			<thead>
				<tr>
					<th class="text-center"><?php echo lang("event_categories"); ?></th>
					<th class="text-center">N°</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($array_cant_tipo_evento as $nombre => $cant_tipo_evento) { ?>
					<tr>
						<td class="text-left"><?php echo lang($nombre); ?></td>
						<td class="text-right"><?php echo to_number_project_format($cant_tipo_evento['cant'], $project_info->id); ?></td>
					</tr> 
				<?php } ?>
			</tbody>
		</table>
	
		</div>
	
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="page-title clearfix panel-success">
					<div class="pt10 pb10 text-center"> <?php echo lang("total_event_categories"); ?> </div>
				</div>
				<div class="panel-body">
					<div id="grafico_totales_tipo_evento" style="height: 240px;"></div>
				</div>
			</div>
			</div>
	
	</div> 
	
</div>
<!-- FIN SECCIÓN TIPOS DE EVENTO -->
	
<!-- INICIO EVENTOS POR RESPONSABLE -->
<div class="panel panel-default mb15">
	<div class="page-title clearfix">
		<h1><?php echo lang('event_by_responsible'); ?></h1>
	</div>

	<div class="panel-body">
			<div id="grafico_eventos_por_responsable" style="height: 480px;"></div>
	</div>
</div>
<!-- FIN EVENTOS POR RESPONSABLE -->

<!-- INICIO EVENTOS POR TIPO DE AFECTACIÓN -->
<div class="panel panel-default mb15">
	<div class="page-title clearfix">
		<h1><?php echo lang('event_by_affectation_type'); ?></h1>
	</div>

	<div class="panel-body">
			<div id="grafico_eventos_por_tipo_afectacion" style="height: 480px;"></div>
	</div>
</div>
<!-- FIN EVENTOS POR TIPO DE AFECTACIÓN -->

<script type="text/javascript">
	$(document).ready(function(){

		//General Settings
		var decimals_separator = AppHelper.settings.decimalSeparator;
		var thousands_separator = AppHelper.settings.thousandSeparator;
		var decimal_numbers = AppHelper.settings.decimalNumbers

		//INICIO SECCIÓN TIPOS DE EVENTO	
		<?php if(!empty(array_filter($array_cant_tipo_evento))){ ?>
		
			$('#grafico_totales_tipo_evento').highcharts({
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
						return '<b>'+ this.point.name +'</b>: '+ numberFormat(this.point.y, 0, decimals_separator, thousands_separator) + ' (' + numberFormat(Math.round(this.percentage), decimal_numbers, decimals_separator, thousands_separator) +' %)';
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
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("contingencies").'_'.clean(lang("total_event_categories")).'_'.date("Y-m-d"); ?>
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
				//colors: ['#398439', '#ac2925', '#d58512'],
				series: [{
					name: 'Porcentaje',
					colorByPoint: true,
					data: [
					<?php foreach($array_cant_tipo_evento as $nombre => $cant_tipo_evento) { ?>
						{
							name: '<?php echo lang($nombre); ?>',
							y: <?php echo $cant_tipo_evento['cant']; ?>,
                            color: '<?php echo $cant_tipo_evento['color']; ?>'
						},
					<?php } ?>
					
					]
				}]
			});
		
		<?php }else{ ?>
		
			$('#grafico_totales_tipo_evento').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
		
		<?php } ?>
		//FIN SECCIÓN TIPOS DE EVENTO

        // SECCIÓN GRÁFICO EVENTOS POR RESPONSABLE
		<?php if(!empty(array_filter($array_cant_tipo_evento))){ ?>

			$('#grafico_eventos_por_responsable').highcharts({
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
					categories: <?php echo json_encode($array_gerencia); ?>
				},
				yAxis: {
					min: 0,
					title: '',
					labels: {
						style: {
							fontSize:'11px'
						},
						format: "{value:,." + decimal_numbers + "f} %", 
						/* formatter: function () {
							return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
						} */
					},
					stackLabels: {
						enabled: true,
						format: "{total:,." + decimal_numbers + "f}",
						y: 0
						//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
						//format: "{total:." + decimal_numbers + "f}",
					}
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
				series: <?php echo json_encode($grafico_eventos_por_responsable); ?>,
				legend: {
					enabled: true,
					itemStyle:{
						fontSize: "11px"
					}
				},
				exporting: {
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("contingencies").'_'.clean(lang("event_by_responsible")).'_'.date("Y-m-d"); ?>
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
			$('#grafico_eventos_por_responsable').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
		<?php }?>
		// FIN SECCIÓN GRÁFICO EVENTOS POR RESPONSABLE
		
        // INICIO SECCIÓN GRÁFICO EVENTOS POR TIPO DE AFECTACIÓN
        <?php if(!empty(array_filter($array_cant_tipo_evento))){ ?>

			$('#grafico_eventos_por_tipo_afectacion').highcharts({
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
					categories: <?php echo json_encode($array_tipo_afectacion); ?>
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
					stackLabels: {
						enabled: true,
						format: "{total:,." + decimal_numbers + "f}",
						y: 0
						//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
						//format: "{total:." + decimal_numbers + "f}",
					}
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
				series: <?php echo json_encode($grafico_eventos_por_tipo_afectacion); ?>,
				legend: {
					enabled: true,
					itemStyle:{
						fontSize: "9px"
					}
				},
				exporting: {
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("contingencies").'_'.clean(lang("event_by_affectation_type")).'_'.date("Y-m-d"); ?>
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
			$('#grafico_eventos_por_tipo_afectacion').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
		<?php }?>
        // FIN SECCIÓN GRÁFICO EVENTOS POR TIPO DE AFECTACIÓN	
		
	});
</script>