<div id="page-content" class="p20 clearfix">

	<!--Breadcrumb section-->
	<nav class="breadcrumb">
		<a class="breadcrumb-item" href="<?php echo get_uri("client_agreements_dashboard/index/".$client_area); ?>"><?php echo lang('community'); ?> </a> /
		<a class="breadcrumb-item" href="#"><?php echo lang('activities'); ?></a> /
		<a class="breadcrumb-item" href="<?php echo get_uri("AC_Activities_dashboard"); ?>"><?php echo lang('dashboard_eng'); ?></a> 
	</nav>

	<div class="panel panel-default mb15">
		<div class="page-title clearfix">
			<h1><?php echo lang("activities")." | ".lang("dashboard_eng"); ?></h1>
		</div>
	</div>

	<?php if($puede_ver == 1) { ?>
		
		<?php if($client_area == "territory"){ ?>

			<div class="row">
				<div class="col-md-12">

					<ul data-toggle="ajax-tab" class="nav nav-tabs classic" role="tablist">
						<li class="active"><a role="presentation" href="#" data-target="#summary"><?php echo lang('summary'); ?></a></li>
						<?php foreach($tipos_actividad as $tipo_actividad){ ?>
							<li><a role="presentation" href="#" data-target="#tipo_actividad-<?php echo $tipo_actividad->id?>"><?php echo lang($tipo_actividad->name); ?></a></li>
						<?php } ?>		
						<!-- <a href="#" class="btn btn-danger pull-right" id="activities_dashboard_pdf" style="margin: 5px 5px 0px 0px"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a> -->
					</ul>

					<div class="tab-content">

						<div role="tabpanel" class="tab-pane fade in active" id="summary" style="min-height: 200px;">

							<div class="panel panel-default mb15">
								<div class="page-title clearfix">
									<h1><?php echo lang('summary'); ?></h1>
								</div>
								<div class="panel-body">

									TBD
								
								</div> 
							</div>

						</div>

						<?php foreach($tipos_actividad as $tipo_actividad){ ?>

							<div role="tabpanel" class="tab-pane fade" id="tipo_actividad-<?php echo $tipo_actividad->id?>" style="min-height: 200px;">

								<div class="page-title clearfix">
									<h1><?php echo lang($tipo_actividad->name); ?></h1>
								</div>

								<?php if(count($array_activities_by_type[$tipo_actividad->id]["activities"])){ ?>

									<div class="panel-group" id="accordion-<?php echo $tipo_actividad->id; ?>">

										<?php foreach($array_activities_by_type[$tipo_actividad->id]["activities"] as $actividad){ ?>

											<div class="panel panel-white">

												<div class="panel-heading">
													<h4 class="panel-title">
														<a data-toggle="collapse" href="#collapse-<?php echo $actividad->id; ?>" data-parent="#" class="accordion-toggle" style="color: #34a7d6;">

															<div class="row">
																<div class="col-md-8">
																	<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> 
																		<?php echo $actividad->actividad; ?>
																	</h4>
																</div>
															</div>

														</a>
													</h4>
												</div>
												
												<div id="collapse-<?php echo $actividad->id; ?>" class="panel-collapse collapse">

													<div class="panel panel-default">
														<!-- <div class="page-title clearfix" style="color: #34a7d6;">
															<h4><?php echo $actividad->actividad; ?></h4>
														</div> -->
														<div class="panel-body">

															<div class="col-md-6">
																<div class="panel panel-default">
																	<div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
																		<div class="p10 pull-left"> <?php echo lang("activities_executed"); ?> </div>
																	</div>
																	<div class="panel-body">
																		<div id="chart_activities_executed-<?php echo $actividad->id; ?>" style="height: 50vh;"></div>
																	</div>
																</div>
															</div>

															<div class="col-md-6">
																<div class="panel panel-default">
																	<div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
																		<div class="p10 pull-left"> <?php echo lang("benefited_collaborators"); ?> </div>
																	</div>
																	<div class="panel-body">
																		<div id="chart_benefited_collaborators-<?php echo $actividad->id; ?>" style="height: 50vh;"></div>
																	</div>
																</div>
															</div>

															<div class="col-md-6">
																<div class="panel panel-default">
																	<div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
																		<div class="p10 pull-left"> <?php echo lang("executed_amount"); ?> </div>
																	</div>
																	<div class="panel-body">
																		<div id="chart_executed_amount-<?php echo $actividad->id; ?>" style="height: 50vh;"></div>
																	</div>
																</div>
															</div>

															<div class="col-md-6">
																<div class="panel panel-default">
																	<div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
																		<div class="p10 pull-left"> <?php echo lang("activities_by_society"); ?> </div>
																	</div>
																	<div class="panel-body">
																		<div id="chart_activities_by_society-<?php echo $actividad->id; ?>" style="height: 50vh;"></div>
																	</div>
																</div>
															</div>

														</div>
													</div>

												</div>

											</div>

										<?php } ?>

									</div>

								<?php } else { ?>
									
									<div class="panel panel-default">
										<div class="panel-body">
											<div class="app-alert alert alert-warning alert-dismissible m0" role="alert">
												<?php echo sprintf($this->lang->line('type_of_activities_without_activities_msj'), lang($tipo_actividad->name)); ?>
											</div>
										</div>
									</div>

								<?php } ?>

							</div>

						<?php } ?>

					</div>

				</div>
			</div>
		
		<?php } ?> 
			
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
	
	var decimals_separator = AppHelper.settings.decimalSeparatorClient;
	var thousands_separator = AppHelper.settings.thousandSeparatorClient;
	var decimal_numbers = AppHelper.settings.decimalNumbersClient;	
	
	<?php if($client_area == "territory"){ ?>

		<?php foreach($tipos_actividad as $tipo_actividad){ ?>

			<?php if(count($array_activities_by_type[$tipo_actividad->id]["activities"])){ ?>

				<?php foreach($array_activities_by_type[$tipo_actividad->id]["activities"] as $actividad){ ?>

					$('#chart_activities_executed-<?php echo $actividad->id; ?>').highcharts({
						title: {
							text: '',
						},
						xAxis: {
							categories: <?php echo $years; ?>
						},
						yAxis: [{ // Eje izquierda
							title: {
								text: 'Actual'
							},
							labels: {
								format: '{value}',
								style: {
									color: Highcharts.getOptions().colors[1]
								}
							},
							allowDecimals: false,
						}, { // Eje derecha
							title: {
								text: '% objetivo'
							},
							labels:{
								formatter: function(){
									return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator)+' %';
								},
								style: {
									color: Highcharts.getOptions().colors[1]
								}
							},
							opposite: true
						}],
						exporting:{
							enabled: false
						},
						credits: {
							enabled: false
						},
						tooltip: {
							shared: true
						},
						series: <?php echo $chart_data_activities_executed[$tipo_actividad->id][$actividad->id]; ?>
					});


					$('#chart_benefited_collaborators-<?php echo $actividad->id; ?>').highcharts({
						title: {
							text: '',
						},
						xAxis: {
							categories: <?php echo $years; ?>
						},
						yAxis: [{ // Eje izquierda
							title: {
								text: 'Actual'
							},
							labels: {
								format: '{value}',
								style: {
									color: Highcharts.getOptions().colors[1]
								}
							},
							allowDecimals: false,
						}, { // Eje derecha
							title: {
								text: '% objetivo'
							},
							labels:{
								formatter: function(){
									return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator)+' %';
								},
								style: {
									color: Highcharts.getOptions().colors[1]
								}
							},
							opposite: true
						}],
						exporting:{
							enabled: false
						},
						credits: {
							enabled: false
						},
						tooltip: {
							shared: true
						},
						series: <?php echo $chart_data_benefited_collaborators[$tipo_actividad->id][$actividad->id]; ?>
					});


					$('#chart_executed_amount-<?php echo $actividad->id; ?>').highcharts({
						title: {
							text: '',
						},
						xAxis: {
							categories: <?php echo $years; ?>
						},
						yAxis: [{ // Eje izquierda
							title: {
								text: 'Actual'
							},
							labels:{
								formatter: function(){
									return '$ '+numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
								},
								style: {
									color: Highcharts.getOptions().colors[1]
								}
							},
							allowDecimals: false,
						}, { // Eje derecha
							title: {
								text: '% objetivo'
							},
							labels:{
								formatter: function(){
									return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator)+' %';
								},
								style: {
									color: Highcharts.getOptions().colors[1]
								}
							},
							opposite: true
						}],
						exporting:{
							enabled: false
						},
						credits: {
							enabled: false
						},
						tooltip: {
							shared: true
						},
						series: <?php echo $chart_data_executed_amount[$tipo_actividad->id][$actividad->id]; ?>
					});

					
					$('#chart_activities_by_society-<?php echo $actividad->id; ?>').highcharts({
						
						title: {
							text: ''
						},
						yAxis: {
							// min: 0,
							// max: 100,
							title: {
								text: 'N°'
							},
							labels:{
								formatter: function(){
									// return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
									return this.value;
								}
							}
						},
						xAxis: {
							categories: <?php echo $years; ?>
						},
						exporting:{
							enabled: false
						},
						credits: {
							enabled: false
						},
						// tooltip: {
						// 	shared: true
						// },
						tooltip: {
							headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
							pointFormatter: function(){
								var symbol = '';
								if ( this.graphic && this.graphic.symbolName ) {
									switch ( this.graphic.symbolName ) {
										case 'circle':
											symbol = '●';
											break;
										case 'diamond':
											symbol = '♦';
											break;
										case 'square':
											symbol = '■';
											break;
										case 'triangle':
											symbol = '▲';
											break;
										case 'triangle-down':
											symbol = '▼';
											break;
									}
								}
								return '<tr><td style="color:'+this.series.color+';padding:0;">'+symbol+' '+this.series.name+':</td>'+'<td style="padding:0;"><b>'+this.y+'</b></td></tr>';
							},
							footerFormat: '</table>',
							useHTML: true,
							shared: true
						},
						series: <?php echo $chart_data_activities_by_society[$tipo_actividad->id][$actividad->id]; ?>
					});

				<?php } ?>

			<?php } ?>

		<?php } ?>
	
	<?php } ?>
	

	
	$("#activities_dashboard_pdf").on('click', function(e) {
		
		appLoader.show();

		<?php if($client_area == "territory"){ ?>
		
			// Gráfico Beneficiarios por Macrozona
			var image_beneficiarios_por_macrozona;
			
			$('#grafico_beneficiarios_organizaciones').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
			$('#grafico_beneficiarios_organizaciones').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
			$('#grafico_beneficiarios_organizaciones').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
			$('#grafico_beneficiarios_organizaciones').highcharts().options.plotOptions.pie.size = 150;
			$('#grafico_beneficiarios_organizaciones').highcharts().options.legend.itemStyle.fontSize = "15px";
			
			var chart = $('#grafico_beneficiarios_organizaciones').highcharts().options.chart;
			var title = $('#grafico_beneficiarios_organizaciones').highcharts().options.title;
			var series = $('#grafico_beneficiarios_organizaciones').highcharts().options.series;
			var plotOptions = $('#grafico_beneficiarios_organizaciones').highcharts().options.plotOptions;
			var colors = $('#grafico_beneficiarios_organizaciones').highcharts().options.colors;
			var exporting = $('#grafico_beneficiarios_organizaciones').highcharts().options.exporting;
			var credits = $('#grafico_beneficiarios_organizaciones').highcharts().options.credits;
			var legend = $('#grafico_beneficiarios_organizaciones').highcharts().options.legend;
			
			var obj = {};
			obj.options = JSON.stringify({
				"chart":chart,
				"title":title,
				"series":series,
				"plotOptions":plotOptions,
				"colors":colors,
				"exporting":exporting,
				"credits":credits,
				"legend":legend
			});
			
			obj.type = 'image/png';
			obj.width = '1600';
			obj.scale = '2';
			obj.async = true;
			
			//var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
			//obj.globaloptions = JSON.stringify(globalOptions);

			image_beneficiarios_por_macrozona = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			
			$('#grafico_beneficiarios_organizaciones').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
			$('#grafico_beneficiarios_organizaciones').highcharts().options.plotOptions.pie.size = null;
			$('#grafico_beneficiarios_organizaciones').highcharts().options.legend.itemStyle.fontSize = "9px";
			
			
			var imagenes_graficos = {
				image_beneficiarios_por_macrozona: image_beneficiarios_por_macrozona,
				// image_beneficiarios_asistentes: image_beneficiarios_asistentes,
				// image_beneficiarios_actividad: image_beneficiarios_actividad,
				// graficos_actividades_macrozona: graficos_actividades_macrozona,
				// image_tipo_beneficiario: image_tipo_beneficiario
			};
			
			$.ajax({
				url:  '<?php echo_uri("AC_Activities_dashboard/get_pdf") ?>',
				type:  'post',
				data: {imagenes_graficos:imagenes_graficos},
				//dataType:'json',
				success: function(respuesta){
					
					var uri = '<?php echo get_setting("temp_file_path") ?>' + respuesta;
					console.log(uri);
					var link = document.createElement("a");
					link.download = respuesta;
					link.href = uri;
					link.click();
					
					borrar_temporal(uri);
				}
	
			});
			
		<?php } ?>
		
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

	$(document).on('click', 'a.accordion-toggle', function () {
		// $('a.accordion-toggle i').removeClass('fa fa-minus-circle font-16');
		// $('a.accordion-toggle i').addClass('fa fa-plus-circle font-16');
		
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