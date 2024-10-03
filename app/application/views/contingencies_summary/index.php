<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("contingencies"); ?> /</a>
  <a class="breadcrumb-item" href=""><?php echo lang("contingencies_summary"); ?></a>
</nav>

<div class="panel panel-default mb15">
    <div class="page-title clearfix">
        <h1><?php echo lang('contingencies_summary'); ?></h1>
    </div>
</div>

<?php if($puede_ver == 1) { ?>

	<?php echo form_open(get_uri("#"), array("id" => "contingencies_summary-form", "class" => "general-form", "role" => "form")); ?>
	
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
					<div class="btn-group" role="group">
						<a href="#" class="btn btn-danger pull-right" id="contingencies_summary_pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a>
					</div>
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
<style>

#tabla_estado_tramitacion_length, #tabla_estado_actividades_length, #tabla_estado_financiero_length{
	display: none;
}

</style>

<script type="text/javascript">

	$(document).ready(function(){
	
    //General Settings
    var decimals_separator = AppHelper.settings.decimalSeparator;
    var thousands_separator = AppHelper.settings.thousandSeparator;
    var decimal_numbers = AppHelper.settings.decimalNumbers;
				
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

		
		$("#contingencies_summary_pdf").on('click', function(e) {
		
			appLoader.show();
			
			var decimal_numbers = '<?php echo $general_settings->decimal_numbers; ?>';
			var decimals_separator = '<?php echo ($general_settings->decimals_separator == 1) ? "." : ","; ?>';
			var thousands_separator = '<?php echo ($general_settings->thousands_separator == 1)? "." : ","; ?>';
			
			// Gráfico Totales Tipo Evento
			<?php if(!empty(array_filter($array_cant_tipo_evento))){ ?>
				$('#grafico_totales_tipo_evento').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_totales_tipo_evento').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "12px";
				$('#grafico_totales_tipo_evento').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_totales_tipo_evento').highcharts().options.plotOptions.pie.size = 150;
				
				var chart = $('#grafico_totales_tipo_evento').highcharts().options.chart;
				var title = $('#grafico_totales_tipo_evento').highcharts().options.title;
				var series = $('#grafico_totales_tipo_evento').highcharts().options.series;
				var plotOptions = $('#grafico_totales_tipo_evento').highcharts().options.plotOptions;
				var colors = $('#grafico_totales_tipo_evento').highcharts().options.colors;
				var exporting = $('#grafico_totales_tipo_evento').highcharts().options.exporting;
				var credits = $('#grafico_totales_tipo_evento').highcharts().options.credits;
	
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
				
				//var image_categorias_totales_sh = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				var grafico_totales_tipo_evento = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				
				$('#grafico_totales_tipo_evento').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
				$('#grafico_totales_tipo_evento').highcharts().options.plotOptions.pie.size = null;
			
            <?php } ?>
			// Fin Gráfico Totales Tipo Evento
			
			// Gráfico Eventos por Responsable
			var grafico_eventos_por_responsable;
			<?php if(!empty(array_filter($grafico_eventos_por_responsable))){ ?>

				var chart = $('#grafico_eventos_por_responsable').highcharts().options.chart;
				var title = $('#grafico_eventos_por_responsable').highcharts().options.title;
				var subtitle = $('#grafico_eventos_por_responsable').highcharts().options.subtitle;
				var xAxis = $('#grafico_eventos_por_responsable').highcharts().options.xAxis;
				var yAxis = $('#grafico_eventos_por_responsable').highcharts().options.yAxis;
				var series = $('#grafico_eventos_por_responsable').highcharts().options.series;
				var plotOptions = $('#grafico_eventos_por_responsable').highcharts().options.plotOptions;
				var colors = $('#grafico_eventos_por_responsable').highcharts().options.colors;
				var exporting = $('#grafico_eventos_por_responsable').highcharts().options.exporting;

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

				grafico_eventos_por_responsable = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';

			<?php } ?>
			// FIN Gráfico Eventos por Responsable
			
			// Gráfico Eventos por tipo afectación
			var grafico_eventos_por_tipo_afectacion;
			<?php if(!empty(array_filter($grafico_eventos_por_tipo_afectacion))){ ?>

				var chart = $('#grafico_eventos_por_tipo_afectacion').highcharts().options.chart;
				var title = $('#grafico_eventos_por_tipo_afectacion').highcharts().options.title;
				var subtitle = $('#grafico_eventos_por_tipo_afectacion').highcharts().options.subtitle;
				var xAxis = $('#grafico_eventos_por_tipo_afectacion').highcharts().options.xAxis;
				var yAxis = $('#grafico_eventos_por_tipo_afectacion').highcharts().options.yAxis;
				var series = $('#grafico_eventos_por_tipo_afectacion').highcharts().options.series;
				var plotOptions = $('#grafico_eventos_por_tipo_afectacion').highcharts().options.plotOptions;
				var colors = $('#grafico_eventos_por_tipo_afectacion').highcharts().options.colors;
				var exporting = $('#grafico_eventos_por_tipo_afectacion').highcharts().options.exporting;

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

				grafico_eventos_por_tipo_afectacion = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';

			<?php } ?>
			// FIN Gráfico Eventos por tipo afectación
			
			var imagenes_graficos = {
                grafico_totales_tipo_evento : grafico_totales_tipo_evento,
                grafico_eventos_por_responsable : grafico_eventos_por_responsable,
                grafico_eventos_por_tipo_afectacion : grafico_eventos_por_tipo_afectacion
			};

			var start_date = $('#default_date_field1').val();
			var end_date = $('#default_date_field2').val();
			
			$.ajax({
				url:  '<?php echo_uri("contingencies_summary/get_pdf") ?>',
				type:  'post',
				data: {
					imagenes_graficos:imagenes_graficos,
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
				url:  '<?php echo_uri("contingencies_summary/borrar_temporal") ?>',
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
				//url :'http://export.highcharts.com/',
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

		$("#contingencies_summary-form").appForm({
            ajaxSubmit: false
        });
		
		$("#contingencies_summary-form").submit(function(e){
			e.preventDefault();
			return false;
		});
		
		setDatePicker("#default_date_field1");
		setDatePicker("#default_date_field2");

		$('#generar').click(function(){
			
			$('#contingencies_summary_pdf').attr('disabled', true);

			
			var id_cliente = '<?php echo $id_cliente; ?>';
			var id_proyecto = '<?php echo $id_proyecto; ?>';
			var start_date = $('#default_date_field1').val();
			var end_date = $('#default_date_field2').val();
			
			if(id_cliente && id_proyecto && start_date && end_date){
				if((start_date < end_date) || (start_date == end_date)){
	
					$.ajax({
						url:'<?php echo_uri("contingencies_summary/get_contingencies_summary_details"); ?>',
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
							$('#contingencies_summary_pdf').removeAttr('disabled');
						}
					});	
					
				}
			}
			
		});

		$('#btn_clean').click(function(){
			
			$('#contingencies_summary_pdf').attr('disabled', true);
			$('#default_date_field1').val("");
			$('#default_date_field2').val("");
			
			$.ajax({
				url:'<?php echo_uri("contingencies_summary/get_contingencies_summary_details"); ?>',
				type:'post',
				beforeSend: function() {
					$('#content_group').html('<div class="panel"><div style="padding:20px;"><div class="circle-loader"></div><div><div>');
				},
				success: function(respuesta){;
					$('#content_group').html(respuesta);	
					$('#contingencies_summary_pdf').removeAttr('disabled');
				}
			});	
			
		});
		 
	});

</script>