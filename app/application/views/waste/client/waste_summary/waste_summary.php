<div class="row" >
	<div class="col-md-12">
		<div class="panel">
			<div class="panel-body" style="padding: 0px 0px 0px 0px">
				<div class="row">
					<div id="vertical_stack_bar_container" class="panel-body">
						<div class="col-md-6">
							<div id="grafico_masa"></div>
						</div>
						<div class="col-md-6">
							<div id="grafico_volumen"></div>
						</div>
					</div
				></div>
			</div>
		</div>
	</div>
</div>	

<!--
<div class="row">
	<div class="col-md-12">
		<div class="panel">
			<div class="panel-body" style="padding: 0px 0px 0px 0px">
				<div class="row">
					<div id="fixed_placement_columns_container" class="panel-body">
						<div class="col-md-6">
							<div id="umbral_masa"></div>
						</div>
						<div class="col-md-6">
							<div id="umbral_volumen"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
-->

<div class="row">
	<div class="col-md-12">
		<div class="panel">
			<div class="panel-body" style="padding: 0px 0px 0px 0px">
				<div class="row">
					<div id="table_container" class="panel-body" style="padding-top: 0px;">
						<div class="page-title clearfix" style="background-color:white;">
							<h1><?php echo lang("last_withdrawals")?></h1>
							<div class="btn-group pull-right" role="group">
								<button type="button" class="btn btn-success" id="excel_ultimos_retiros"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
							</div>
						</div>
						<div class="table-responsive">
							<table id="detail-table" class="display" cellspacing="0" width="100%">
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function () {

	var decimals_separator = AppHelper.settings.decimalSeparator;
	var thousands_separator = AppHelper.settings.thousandSeparator;
	//var decimal_numbers = AppHelper.settings.decimalNumbers;
	var decimal_numbers = 1;

	var colores = [
			'#7CB5EC', 
			'#434348',
			'#90ED7D', 
			'#F7A35C', 
			'#8085E9',
			'#F15C80', 
			'#E4D354',
			'#2B908F', 
			'#F45B5B', 
			'#91E8E1',
			'#0070C0'
		]

	<?php if(!$start_date || !$end_date){ ?>

		// GRÁFICO RESIDUOS EN MASA
		$("#grafico_masa").highcharts({ 
			chart: { 
				zoomType: "x",
				reflow: true,
				vresetZoomButton: {
					position: {
						align: "left",x: 0
					}
				},
				type: "column",
				events: {
					load: function(event){
					}
				}
			}, 
			colors: colores,
			title: {
				text:"<?php echo lang('waste_in_bulk') ?>"
			},
			credits: {
				enabled: false 
			},
			
			<?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("summary").'_'.lang("treatment").'_'.$nombre_unidad_masa_config.'_'.date("Y-m-d"); ?>

			exporting: {
				filename: "<?php echo $nombre_exportacion; ?>",buttons: {
					contextButton: {
						menuItems: [{
							text: "<?php echo lang('export_to_png') ?>",
							onclick: function() {
								this.exportChart();
							},
							separator: false
						}]
					}
				}
			}, 
			xAxis: {
				categories: [
					<?php $html = '';
					foreach($array_data_grafico_masa as $key => $material){
								$html .='"'.$key.'",';
							}
							echo $html;
					?>] 
			},  
			yAxis: {
				min: 0,
				title: {
					text: "<?php echo $nombre_unidad_masa_config; ?>"	
				},
				labels:{ 
					format: "{value:,." + decimal_numbers + "f}",
					/*formatter: function(){
						return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
					},
					format: "{
						value:." + decimal_numbers + "f
					}",
				*/
				},
				stackLabels: {
					enabled: true,
					format: "{total:,." + decimal_numbers + "f}",
					//formatter: function(){
					// 	return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);
					// },
				}
			}, 
			legend: {
				align: "center",
				verticalAlign: "bottom",
				backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "white",
				shadow: false
			}, 
			tooltip: {
				headerFormat: "<span style='/*font-size:10px*/'>{point.key}</span><br>",
				pointFormatter: function(){
					return "<tr><td style='color:"+this.series.color+";padding:0'>"+this.series.name+":</td><td style='padding:0'><b>"+ numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator) + " <?php echo $nombre_unidad_masa_config; ?> </b></td></tr>"
				},
				footerFormat:"</table>",
				useHTML: true
			}, 
			plotOptions: {
				column: {
					grouping: false,
					shadow: false,
					//borderWidth: 0,
					//pointPadding: 0.2,
					stacking: "normal",
					pointPadding: 0.2,
					borderWidth: 0,
					dataLabels: {
						enabled: true,
						color: "#000000",
						align: "center",
						format: "{y:,." + decimal_numbers + "f}",
						//formatter: function(){
						// 	return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
						// },
						//format: "{y:." + decimal_numbers + "f}",
						style: {
							// fontSize: "10px",
							fontFamily: "Segoe ui, sans-serif"
						}
					}
				}
			}, 
			series: [ 				
				<?php
				$html = '';
				$tipos_tratamientos = $this->Tipo_tratamiento_model->get_details()->result_array();

				foreach($tipos_tratamientos as $tipo_tratamiento){
					$html .=' {';
					$html .=' name: "'.$tipo_tratamiento['nombre'].'",';
					$html .=' data:[';
					foreach($data_grafico_masa[$tipo_tratamiento['nombre']] as $value){
						$html .= ''.$value.',';
					}
					$html .=' ]';
					//$html .=' color: "#b3b3b3"';
					$html .=' },';
				} 
				echo $html; ?>
			]
		}); 
		// FIN GRÁFICO RESIDUOS EN MASA
		
		// GRÁFICO RESIDUOS EN VOLUMEN
		$("#grafico_volumen").highcharts({ 
			chart: { 
				zoomType: "x",
				reflow: true,
				vresetZoomButton: {
					position: {
						align: "left",x: 0
					}
				},
				type: "column",
				events: {
					load: function(event){
					}
				}
			}, 
			colors: colores,
			title: {
				text:"<?php echo lang('waste_in_volume') ?>"
			},
			credits: {
				enabled: false 
			},
			
			<?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("summary").'_'.lang("treatment").'_'.$nombre_unidad_volumen_config.'_'.date("Y-m-d"); ?>

			exporting: {
				filename: "<?php echo $nombre_exportacion; ?>",buttons: {
					contextButton: {
						menuItems: [{
							text: "<?php echo lang('export_to_png') ?>",
							onclick: function() {
								this.exportChart();
							},
							separator: false
						}]
					}
				}
			}, 
			xAxis: {
				categories: [
					<?php $html = '';
					foreach($array_data_grafico_volumen as $key => $material){
								$html .='"'.$key.'",';
							}
							echo $html;
					?>] 
			},  
			yAxis: {
				min: 0,
				title: {
					text: "<?php echo $nombre_unidad_volumen_config; ?>"	
				},
				labels:{ 
					format: "{value:,." + decimal_numbers + "f}",
					/*formatter: function(){
						return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
					},
					format: "{
						value:." + decimal_numbers + "f
					}",
				*/
				},
				stackLabels: {
					enabled: true,
					format: "{total:,." + decimal_numbers + "f}",
					//formatter: function(){
					// 	return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);
					// },
				}
			}, 
			legend: {
				align: "center",
				verticalAlign: "bottom",
				backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "white",
				shadow: false
			}, 
			tooltip: {
				headerFormat: "<span style='/*font-size:10px*/'>{point.key}</span><br>",
				pointFormatter: function(){
					return "<tr><td style='color:"+this.series.color+";padding:0'>"+this.series.name+":</td><td style='padding:0'><b>"+ numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator) + " <?php echo $nombre_unidad_volumen_config; ?> </b></td></tr>"
				},
				footerFormat:"</table>",
				useHTML: true
			}, 
			plotOptions: {
				column: {
					grouping: false,
					shadow: false,
					//borderWidth: 0,
					//pointPadding: 0.2,
					stacking: "normal",
					pointPadding: 0.2,
					borderWidth: 0,
					dataLabels: {
						enabled: true,
						color: "#000000",
						align: "center",
						format: "{y:,." + decimal_numbers + "f}",
						//formatter: function(){
						// 	return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
						// },
						//format: "{y:." + decimal_numbers + "f}",
						style: {
							// fontSize: "10px",
							fontFamily: "Segoe ui, sans-serif"
						}
					}
				}
			}, 
			series: [ 				
				<?php
				$html = '';
				$tipos_tratamientos = $this->Tipo_tratamiento_model->get_details()->result_array();

				foreach($tipos_tratamientos as $tipo_tratamiento){
					$html .=' {';
					$html .=' name: "'.$tipo_tratamiento['nombre'].'",';
					$html .=' data:[';
					foreach($data_grafico_volumen[$tipo_tratamiento['nombre']] as $value){
						$html .= ''.$value.',';
					}
					$html .=' ]';
					//$html .=' color: "#b3b3b3"';
					$html .=' },';
				} 
				echo $html; ?>
			]
		}); 
		// FIN GRÁFICO RESIDUOS EN VOLUMEN

	<?php } else { ?>

		// ---------- DRILLDOWN ----------

		// GRÁFICO RESIDUOS EN MASA
		$("#grafico_masa").highcharts({ 
			chart: { 
				zoomType: "x",
				reflow: true,
				vresetZoomButton: {
					position: {
						align: "left",x: 0
					}
				},
				type: "column",
				events: {
					load: function(event){
					}
				}
			},
			colors: colores, 
			lang: {
				drillUpText: '<?php echo lang("go_back"); ?>'
			},
			title: {
				text:"<?php echo lang('waste_in_bulk') ?>"
			},
			credits: {
				enabled: false 
			},
			
			<?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("summary").'_'.lang("treatment").'_'.$nombre_unidad_masa_config.'_'.date("Y-m-d"); ?>

			exporting: {
				filename: "<?php echo $nombre_exportacion; ?>",buttons: {
					contextButton: {
						menuItems: [{
							text: "<?php echo lang('export_to_png') ?>",
							onclick: function() {
								this.exportChart();
							},
							separator: false
						}]
					}
				}
			}, 
			xAxis: {
				type: 'category', 
			},  
			yAxis: {
				min: 0,
				title: {
					text: "<?php echo $nombre_unidad_masa_config; ?>"	
				},
				labels:{ 
					format: "{value:,." + decimal_numbers + "f}",
					/*formatter: function(){
						return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
					},
					format: "{
						value:." + decimal_numbers + "f
					}",
				*/
				},
				stackLabels: {
					enabled: true,
					format: "{total:,." + decimal_numbers + "f}",
					//formatter: function(){
					// 	return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);
					// },
				}
			}, 
			legend: {
				align: "center",
				verticalAlign: "bottom",
				backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "white",
				shadow: false
			}, 
			tooltip: {
				headerFormat: "<span style='/*font-size:10px*/'>{point.key}</span><br>",
				pointFormatter: function(){
					return "<tr><td style='color:"+this.series.color+";padding:0'>"+this.series.name+":</td><td style='padding:0'><b>"+ numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator) + " <?php echo $nombre_unidad_masa_config; ?> </b></td></tr>"
				},
				footerFormat:"</table>",
				useHTML: true
			}, 
			plotOptions: {
				column: {
					grouping: false,
					shadow: false,
					//borderWidth: 0,
					//pointPadding: 0.2,
					stacking: "normal",
					pointPadding: 0.2,
					borderWidth: 0,
					dataLabels: {
						enabled: true,
						color: "#000000",
						align: "center",
						format: "{y:,." + decimal_numbers + "f}",
						//formatter: function(){
						// 	return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
						// },
						//format: "{y:." + decimal_numbers + "f}",
						style: {
							// fontSize: "10px",
							fontFamily: "Segoe ui, sans-serif"
						}
					}
				}
			}, 
			series: [
				<?php foreach($array_data_grafico_masa as $tipo_tratamiento => $materiales){ ?>
					{
						name: "<?php echo $tipo_tratamiento; ?>",
						data: [
							<?php foreach ($materiales as $material => $valor){ ?>
								{
									name: "<?php echo $material; ?>",
									y: <?php echo $valor; ?>,
									drilldown: "<?php echo $tipo_tratamiento." - ".$material; ?>"
								},
							<?php } ?>
						]
					},
				<?php } ?>
			],
			drilldown: {
				series: [
					<?php foreach($data_grafico_masa_drilldown as $tipo_tratamiento => $materiales){ ?>
						<?php foreach($materiales as $material => $datos_meses){ ?>
							{
								name: "<?php echo $tipo_tratamiento; ?>",
								id: "<?php echo $tipo_tratamiento." - ".$material; ?>",
								data: [
									<?php foreach($datos_meses as $mes => $valor){ ?>
										["<?php echo $mes; ?>", <?php echo $valor; ?>],
									<?php } ?>
								],
							}, 
						<?php } ?>
					<?php } ?>
				]
			}
			/*series: [
				{
					name: "Disposición - Relleno sanitario",
					data: [{
						name: "Papel y Cartón",
						y: 62.74,
						drilldown: "Disposición - Relleno sanitario"
					},
					{
						name: "Plástico",
						y: 10.57,
						drilldown: "test2"
					}]
				},
				{
					name: "Disposición - Relleno de seguridad",
					data: [{
						name: "Papel y Cartón",
						y: 62.74,
						drilldown: "Disposición - Relleno de seguridad"
					},
					{
						name: "Plástico",
						y: 10.57,
						drilldown: "test6"
					}]
				},
				
			],*/
			/*drilldown: {
				series: [
					{
						name: "Disposición - Relleno sanitario",
						id: "Disposición - Relleno sanitario",
						data: [
							["asd", 0.1],
							["dsa", 1.3],
							["aaa", 53.02]
						]
					}, 
					{
						name: "Disposición - Relleno de seguridad",
						id: "Disposición - Relleno de seguridad",
						data: [
							["bbb", 23],
							["bbs", 12],
							["bbbb", 5],
						]
					},
				]
			}*/
		}); 
		// FIN GRÁFICO RESIDUOS EN MASA
		
		// GRÁFICO RESIDUOS EN VOLUMEN
		$("#grafico_volumen").highcharts({ 
			chart: { 
				zoomType: "x",
				reflow: true,
				vresetZoomButton: {
					position: {
						align: "left",x: 0
					}
				},
				type: "column",
				events: {
					load: function(event){
					}
				}
			}, 
			colors: colores,
			lang: {
				drillUpText: '<?php echo lang("go_back"); ?>'
			},
			title: {
				text:"<?php echo lang('waste_in_volume') ?>"
			},
			credits: {
				enabled: false 
			},
			
			<?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("summary").'_'.lang("treatment").'_'.$nombre_unidad_volumen_config.'_'.date("Y-m-d"); ?>

			exporting: {
				filename: "<?php echo $nombre_exportacion; ?>",buttons: {
					contextButton: {
						menuItems: [{
							text: "<?php echo lang('export_to_png') ?>",
							onclick: function() {
								this.exportChart();
							},
							separator: false
						}]
					}
				}
			}, 
			xAxis: {
				type: 'category', 
			},   
			yAxis: {
				min: 0,
				title: {
					text: "<?php echo $nombre_unidad_volumen_config; ?>"	
				},
				labels:{ 
					format: "{value:,." + decimal_numbers + "f}",
					/*formatter: function(){
						return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
					},
					format: "{
						value:." + decimal_numbers + "f
					}",
				*/
				},
				stackLabels: {
					enabled: true,
					format: "{total:,." + decimal_numbers + "f}",
					//formatter: function(){
					// 	return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);
					// },
				}
			}, 
			legend: {
				align: "center",
				verticalAlign: "bottom",
				backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "white",
				shadow: false
			}, 
			tooltip: {
				headerFormat: "<span style='/*font-size:10px*/'>{point.key}</span><br>",
				pointFormatter: function(){
					return "<tr><td style='color:"+this.series.color+";padding:0'>"+this.series.name+":</td><td style='padding:0'><b>"+ numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator) + " <?php echo $nombre_unidad_volumen_config; ?> </b></td></tr>"
				},
				footerFormat:"</table>",
				useHTML: true
			}, 
			plotOptions: {
				column: {
					grouping: false,
					shadow: false,
					//borderWidth: 0,
					//pointPadding: 0.2,
					stacking: "normal",
					pointPadding: 0.2,
					borderWidth: 0,
					dataLabels: {
						enabled: true,
						color: "#000000",
						align: "center",
						format: "{y:,." + decimal_numbers + "f}",
						//formatter: function(){
						// 	return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
						// },
						//format: "{y:." + decimal_numbers + "f}",
						style: {
							// fontSize: "10px",
							fontFamily: "Segoe ui, sans-serif"
						}
					}
				}
			}, 
			series: [
				<?php foreach($array_data_grafico_volumen as $tipo_tratamiento => $materiales){ ?>
					{
						name: "<?php echo $tipo_tratamiento; ?>",
						data: [
							<?php foreach ($materiales as $material => $valor){ ?>
								{
									name: "<?php echo $material; ?>",
									y: <?php echo $valor; ?>,
									drilldown: "<?php echo $tipo_tratamiento." - ".$material; ?>"
								},
							<?php } ?>
						]
					},
				<?php } ?>
			],
			drilldown: {
				series: [
					<?php foreach($data_grafico_volumen_drilldown as $tipo_tratamiento => $materiales){ ?>
						<?php foreach($materiales as $material => $datos_meses){ ?>
							{
								name: "<?php echo $tipo_tratamiento; ?>",
								id: "<?php echo $tipo_tratamiento." - ".$material; ?>",
								data: [
									<?php foreach($datos_meses as $mes => $valor){ ?>
										["<?php echo $mes; ?>", <?php echo $valor; ?>],
									<?php } ?>
								],
							}, 
						<?php } ?>
					<?php } ?>
				]
			}
		}); 
		// FIN GRÁFICO RESIDUOS EN VOLUMEN

		// ---------- END DRILLDOWN ----------
		
	<?php } ?>	

	

	// GRÁFICO RESIDUOS ALMACENADOS (MASA)
		
	$("#umbral_masa").highcharts({
		chart: {
			type: "column"
		},	
		colors: colores,
		title: {
			text: "<?php echo lang("waste_stored_mass") ?>"
		},	
		credits: {
			enabled: false
		},
		
		<?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("summary").'_'.lang("stored").'_'.$nombre_unidad_masa_config.'_'.date("Y-m-d"); ?>

		exporting: {
			chartOptions:{
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true,
						}
					}
				}
			},
			filename: "<?php echo $nombre_exportacion; ?>",
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
			categories: [
				<?php foreach($array_almacenados_umbrales as $key => $value){
					echo '"'.$key .'", ';
				} ?>
			]	
		},
		yAxis: [
			{	 
				min: 0, 
				title: {
					text: ""
				}
			},
			{
				title:{
					text: "<?php echo $nombre_unidad_masa_config; ?>"				  
				},
				//opposite: false,
				labels:{
					format: "{value:,." + decimal_numbers + "f}",
					/*formatter: function(){
						return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
					},*/
				},
				stackLabels: {
					enabled: true,
					format: "{total:,." + decimal_numbers + "f}",
					//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
					//format: "{total:." + decimal_numbers + "f}",
				}
			}
		],
		legend: {
			align: "center",
			verticalAlign: "bottom",
			backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "white",
			shadow: false
		},
		tooltip: {
			headerFormat: "<span style='/*font-size:10px*/'>{point.key}</span><br>",
			pointFormatter: function(){
				return "<tr><td style='color:"+this.series.color+";padding:0'>"+this.series.name+":</td>"+"<td style='padding:0'><b>" + numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator) + " <?php echo $nombre_unidad_masa_config; ?> </b></td></tr>"
			},
			footerFormat:"</table>",
			useHTML: true
		},	
		plotOptions: {
			column: {
				grouping: false,
				shadow: false,
				stacking: "normal",
				pointPadding: 0.2,
				borderWidth: 0,
				dataLabels: {
					enabled: true,
					color: "#000000",
					align: "center",
					format: "{y:,." + decimal_numbers + "f}",
					//formatter: function(){return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);},
					//format: "{y:." + decimal_numbers + "f}",
					style: {
						// fontSize: "10px",
						fontFamily: "Segoe ui, sans-serif"
					}
				}
			}
		},	
		series: [
			{
				name: "<?php echo lang("threshold"); ?>",
				//color: "rgba(248,161,63,1)",
				color: "#d9534f",
				data: [
				<?php 
					$html = '';
					foreach($array_almacenados_umbrales as $key => $value){
						$html .=''.$value["umbrales"].',';
					}
					echo $html; ?>
				],
				pointPadding: 0.3,
				pointPlacement: "center",
				yAxis: 1
			},
			{
				name: "<?php echo lang("stored"); ?>",
				//color: "rgba(186,60,61,.9)",
				color: "#90ed7d",
				data: [
				<?php
					$html = '';
					 foreach($array_almacenados_umbrales as $key => $value){
						$html .=''.$value["valor_total_form"].',';
					}
					echo $html; ?>
				],
				pointPadding: 0.4,
				pointPlacement: 0,
				yAxis: 1
			}
		]
	}); 
	// FIN GRÁFICO RESIDUOS ALMACENADOS (MASA)

	// GRÁFICO RESIDUOS ALMACENADOS (VOLUMEN)
	$("#umbral_volumen").highcharts({
		chart: {
			type: "column"
		},
		colors: colores,
		title: {
			text: "<?php echo lang("waste_stored_volume"); ?>"
		},
		credits: {
			enabled: false
		},

		<?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("summary").'_'.lang("stored").'_'.$unidad.'_'.date("Y-m-d"); ?>

		exporting: {
			chartOptions: {
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true,
						}
					}
				}
			},
			filename: "<?php echo $nombre_exportacion; ?>",
			buttons: {
				contextButton: {
					menuItems: [{
						text: "<?php echo lang('export_to_png') ?>",
						onclick: function() {
							this.exportChart();
						},
						separator: false
					}]
				}
			}
		},
		xAxis: {
			categories: [
				<?php foreach($array_almacenados_umbrales_volumen as $key => $value){
					echo '"'.$key .'", ';
				} ?>
			]
		},
		yAxis: [{
				min: 0,
				title: {
					text: ""
				}
			},
			{
				title: {
					text: "<? echo $nombre_unidad_volumen_config; ?>"
				},
				labels: {
					format: "{value:,." + decimal_numbers + "f}",
					/*formatter: function(){
						return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
					},*/
					//format: "{value:." + decimal_numbers + "f}",
				},
				stackLabels: {
					enabled: true,
					format: "{total:,." + decimal_numbers + "f}",
					//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
				}
			}
		],
		legend: {
			align: "center",
			verticalAlign: "bottom",
			backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "white",
			shadow: false
		},
		tooltip: {
			headerFormat: "<span style='/*font-size:10px*/'>{point.key}</span><br>",
			pointFormatter: function() {
				return "<tr><td style='color:" + this.series.color + ";padding:0'>" + this.series.name + ":</td>" + "<td style='padding:0'><b>" + numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator) + " <?php echo $nombre_unidad_volumen_config; ?> </b></td></tr>"
			},
			footerFormat: "</table>",
			useHTML: true
		},
		plotOptions: {
			column: {
				grouping: false,
				shadow: false,
				//borderWidth: 0,
				//pointPadding: 0.2,
				stacking: "normal",
				pointPadding: 0.2,
				borderWidth: 0,
				dataLabels: {
					enabled: true,
					color: "#000000",
					align: "center",
					format: "{y:,." + decimal_numbers + "f}",
					//formatter: function(){return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);},
					//format: "{y:." + decimal_numbers + "f}",
					style: {
						// fontSize: "10px",
						fontFamily: "Segoe ui, sans-serif"
					}
				}
			}
		},
		series: [
		{
			name: "<?php echo lang("threshold"); ?>",
			//color: "rgba(248,161,63,1)",
			color: "#d9534f",
			data: [
				<?php 
					$html = '';
					foreach($array_almacenados_umbrales_volumen as $key => $value){
						$html .=''.$value["umbrales"].',';
					}
					echo $html; ?>
			],
			pointPadding: 0.3,
			pointPlacement: "center",
			yAxis: 1
		},
		{
			name: "<?php echo lang("stored"); ?>",
			//color: "rgba(186,60,61,.9)",
			color: "#90ed7d",
			data: [
				<?php
					$html = '';
					 foreach($array_almacenados_umbrales_volumen as $key => $value){
						$html .=''.$value["valor_total_form"].',';
					}
					echo $html; ?>
			],
			pointPadding: 0.4,
			pointPlacement: 0,
			yAxis: 1
		}]
	});
	// FIN GRÁFICO RESIDUOS ALMACENADOS (VOLUMEN)

	var id_project = <?php echo $id_project ?>;
	var id_cliente = <?php echo $id_cliente ?>;


	$("#detail-table").appTable({
		source: '<?php echo_uri("waste_summary/list_data/".$id_project.'/'.$start_date.'/'.$end_date); ?>',
		filterDropdown: [
			{name: "id_tratamiento", class: "w200", options: <?php echo $tratamientos_dropdown; ?>},
			{name: "id_categoria", class: "w200", options: <?php echo $categorias_dropdown; ?>},
		],
		columns: [
			{title: "<?php echo lang("material"); ?>", "class": "text-left dt-head-center w50"},
			{title: "<?php echo lang("categorie"); ?>", "class": "text-left dt-head-center w50"},
			{title: "<?php echo lang("quantity"); ?>", "class": "text-right dt-head-center"},
			{title: "<?php echo lang("treatment"); ?>", "class": "text-left dt-head-center"},
			{title: "<?php echo lang("retirement_date"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
			{title: "<?php echo lang("retirement_evidence"); ?>","class": "text-center w100 no_breakline option"},
			{title: "<?php echo lang("reception_evidence"); ?>","class": "text-center w100 no_breakline option"},
		]
	});

	$('#excel_ultimos_retiros').click(function(){
		var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("waste_summary/get_excel_ultimos_retiros/".$start_date."/".$end_date)?>').attr('method','POST').attr('target', '_self').appendTo('body');
		//$form.append('<input type="hidden" name="id_material" value="' + id_material + '" />');
		$form.submit();
	});
	
});
</script>