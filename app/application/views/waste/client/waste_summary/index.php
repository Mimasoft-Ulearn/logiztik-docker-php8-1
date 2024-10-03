<div id="page-content" class="p20 clearfix">

	<!--Breadcrumb section-->
    <nav class="breadcrumb">
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("waste"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("summary"); ?></a>
    </nav>

    <div class="panel panel-default">
		<div class="page-title clearfix">
			<h1><?php echo lang('summary') ?></h1>
        </div>
    </div>
    
	<?php if($puede_ver == 1) { ?>

		<?php echo form_open(get_uri("#"), array("id" => "summary-form", "class" => "general-form", "role" => "form")); ?>
            <div class="panel panel-default">
            
                <div class="panel-body">    
                    <div class="col-md-6">
                    
                        <div class="form-group multi-column">
                    
                            <label class="col-md-3" style="padding-right:0px;margin-right:0px;"><?php echo lang('date_range') ?></label>
            
                            <div class="col-md-4">
                                <?php 
                                    echo form_input(array(
                                        "id" => "start_date",
                                        "name" => "start_date",
                                        "value" => "",
                                        "class" => "form-control",
                                        "placeholder" => lang('since'),
                                        "data-rule-required" => true,
                                        "data-msg-required" => lang("field_required"),
                                        "autocomplete" => "off",
                                    ));
                                ?>
                            </div>
                        
                            <div class="col-md-4">
                                <?php 
                                    echo form_input(array(
                                        "id" => "end_date",
                                        "name" => "end_date",
                                        "value" => "",
                                        "class" => "form-control",
                                        "placeholder" => lang('until'),
                                        "data-rule-required" => true,
                                        "data-msg-required" => lang("field_required"),
                                        "data-rule-greaterThanOrEqual" => "#start_date",
                                        "data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
                                        "autocomplete" => "off",
                                    ));
                                ?>
                            </div>
                            
                        </div> 
                                        
                    </div>
                    
                    <div class="col-md-6">
                        <div class="pull-right">
							<!-- Boton Generar -->
                            <div class="btn-group" role="group">
                                <button id="generate" type="submit" class="btn btn-primary"><span class="fa fa-eye"></span> <?php echo lang('generate'); ?></button>
                            </div>

							<!-- Boton Exportar a pdf -->
						    <div class="btn-group" role="group">
                                <a href="#" class="btn btn-danger pull-right" id="waste_summary_pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a>
                            </div>
                            
							<!-- Boton Borrar consulta -->
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

		<div id="waste_summary_group">
			
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
$(document).ready(function () {

	$("#summary-form").appForm({
		ajaxSubmit: false
	});

	$("#summary-form").submit(function(e){
		e.preventDefault();
		return false;
	});

	setDatePicker("#start_date");
	setDatePicker("#end_date");

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

	// GRÁFICO RESIDUOS EN MASA
	$("#grafico_masa").highcharts(
	{ 
		chart: 
		{ 
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
	$("#grafico_volumen").highcharts(
	{ 
		chart: 
		{ 
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
						fontFamily: "Segoe ui, sans-serif"}
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
		source: '<?php echo_uri("waste_summary/list_data/"); ?>'+id_project,
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

	//  BOTÓN GENERAR
	$('#generate').click(function(e){
        // $('#waste_summary_pdf').off('click');
		// $('#waste_summary_pdf').attr('disabled',true);
		$('#summary-form').valid();
        
		
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		
		if(start_date && end_date){
			if((start_date < end_date) || (start_date == end_date)){
		
				$.ajax({
					url:  '<?php echo_uri("Waste_summary/get_waste_summary_report") ?>',
					type:  'post',
					data: {
						start_date: start_date,
						end_date: end_date
					},beforeSend: function() {
						$('#waste_summary_group').html('<div style="padding:20px;"><div class="circle-loader"></div><div>');
					},
					
					//dataType:'json',
					success: function(respuesta){;
						$('#waste_summary_group').html(respuesta);
						// $('#export_pdf').removeAttr('disabled');
					}
				});	
				
			}
		}
		e.preventDefault();
		e.stopPropagation();
		return false;
	});

    // BOTÓN LIMPIAR
    $('#btn_clean').click(function(){
        
        // $('#export_pdf').attr('disabled', true);
        $('#start_date').val("");
        $('#end_date').val("");	
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        
        $.ajax({
            url:'<?php echo_uri("waste_summary/get_waste_summary_report"); ?>',
            type:'post',
            /* data: {
                start_date : start_date,
                end_date : end_date
            }, */
            beforeSend: function() {
                $('#waste_summary_group').html('<div class="panel"><div style="padding:20px;"><div class="circle-loader"></div><div><div>');
            },
            success: function(respuesta){;
                $('#waste_summary_group').html(respuesta);
                // $('#export_pdf').removeAttr('disabled');
            }
        });	
        
    });
	
	$('#excel_ultimos_retiros').click(function(){
		var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("waste_summary/get_excel_ultimos_retiros")?>').attr('method','POST').attr('target', '_self').appendTo('body');
		//$form.append('<input type="hidden" name="id_material" value="' + id_material + '" />');
		$form.submit();
	});
	
	$("#waste_summary_pdf").on('click', function(e) {	

		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		
		appLoader.show();
		
		var decimal_numbers = '<?php echo $general_settings->decimal_numbers; ?>';
		var decimals_separator = '<?php echo ($general_settings->decimals_separator == 1) ? "." : ","; ?>';
		var thousands_separator = '<?php echo ($general_settings->thousands_separator == 1)? "." : ","; ?>';
		
		// Gráfico Residuos en Masa
		var chart = $('#grafico_masa').highcharts().options.chart;
		var title = $('#grafico_masa').highcharts().options.title;
		var subtitle = $('#grafico_masa').highcharts().options.subtitle;
		var xAxis = $('#grafico_masa').highcharts().options.xAxis;
		var yAxis = $('#grafico_masa').highcharts().options.yAxis;
		var series = $('#grafico_masa').highcharts().options.series;
		var plotOptions = $('#grafico_masa').highcharts().options.plotOptions;
		var colors = $('#grafico_masa').highcharts().options.colors;
		var exporting = $('#grafico_masa').highcharts().options.exporting;
		var credits = $('#grafico_masa').highcharts().options.credits;
		
		var obj = {};
		obj.options = JSON.stringify({
			"chart":chart,
			"title":title,
			"subtitle":subtitle,
			"xAxis":xAxis,
			"yAxis":yAxis,
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
		
		var image_residuos_masa = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';

		// Gráfico Residuos en Volumen
		var chart = $('#grafico_volumen').highcharts().options.chart;
		var title = $('#grafico_volumen').highcharts().options.title;
		var subtitle = $('#grafico_volumen').highcharts().options.subtitle;
		var xAxis = $('#grafico_volumen').highcharts().options.xAxis;
		var yAxis = $('#grafico_volumen').highcharts().options.yAxis;
		var series = $('#grafico_volumen').highcharts().options.series;
		var plotOptions = $('#grafico_volumen').highcharts().options.plotOptions;
		var colors = $('#grafico_volumen').highcharts().options.colors;
		var exporting = $('#grafico_volumen').highcharts().options.exporting;
		var credits = $('#grafico_volumen').highcharts().options.credits;
		
		var obj = {};
		obj.options = JSON.stringify({
			"chart":chart,
			"title":title,
			"subtitle":subtitle,
			"xAxis":xAxis,
			"yAxis":yAxis,
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
		
		var image_residuos_volumen = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
		
		/*
		// Gráfico Residuos almacenados (masa)
		$('#umbral_masa').highcharts().options.plotOptions.column.dataLabels.enabled = true;
		
		var chart = $('#umbral_masa').highcharts().options.chart;
		var title = $('#umbral_masa').highcharts().options.title;
		var subtitle = $('#umbral_masa').highcharts().options.subtitle;
		var xAxis = $('#umbral_masa').highcharts().options.xAxis;
		var yAxis = $('#umbral_masa').highcharts().options.yAxis;
		var series = $('#umbral_masa').highcharts().options.series;
		var exporting = $('#umbral_masa').highcharts().options.exporting;
		var plotOptions = $('#umbral_masa').highcharts().options.plotOptions;
		var colors = $('#umbral_masa').highcharts().options.colors;
		var credits = $('#umbral_masa').highcharts().options.credits;
		
		var obj = {};
		obj.options = JSON.stringify({
			"chart":chart,
			"title":title,
			"subtitle":subtitle,
			"xAxis":xAxis,
			"yAxis":yAxis,
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
		
		var image_residuos_almacenados_masa = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
		
		$('#umbral_masa').highcharts().options.plotOptions.column.dataLabels.enabled = false;
		*/

		/*
		// Gráfico Residuos almacenados (volumen)
		$('#umbral_volumen').highcharts().options.plotOptions.column.dataLabels.enabled = true;
		
		var chart = $('#umbral_volumen').highcharts().options.chart;
		var title = $('#umbral_volumen').highcharts().options.title;
		var subtitle = $('#umbral_volumen').highcharts().options.subtitle;
		var xAxis = $('#umbral_volumen').highcharts().options.xAxis;
		var yAxis = $('#umbral_volumen').highcharts().options.yAxis;
		var series = $('#umbral_volumen').highcharts().options.series;
		var plotOptions = $('#umbral_volumen').highcharts().options.plotOptions;
		var colors = $('#umbral_volumen').highcharts().options.colors;
		var exporting = $('#umbral_volumen').highcharts().options.exporting;
		var credits = $('#umbral_volumen').highcharts().options.credits;
		
		var obj = {};
		obj.options = JSON.stringify({
			"chart":chart,
			"title":title,
			"subtitle":subtitle,
			"xAxis":xAxis,
			"yAxis":yAxis,
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
		
		var image_residuos_almacenados_volumen = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
		
		$('#umbral_volumen').highcharts().options.plotOptions.column.dataLabels.enabled = false;
		*/
		
		var imagenes_graficos = {
			image_residuos_masa:image_residuos_masa,
			image_residuos_volumen:image_residuos_volumen,
			//image_residuos_almacenados_masa:image_residuos_almacenados_masa,
			//image_residuos_almacenados_volumen,image_residuos_almacenados_volumen
		};

		$.ajax({
			url:  '<?php echo_uri("waste_summary/get_pdf") ?>',
			type:  'post',
			data: {imagenes_graficos:imagenes_graficos, start_date:start_date, end_date:end_date},
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
			url:  '<?php echo_uri("waste_summary/borrar_temporal") ?>',
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
	
});
</script>