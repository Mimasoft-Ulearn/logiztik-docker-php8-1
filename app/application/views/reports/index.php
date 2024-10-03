<div id="page-content" class="p20 clearfix">   
<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("reports");?>"><?php echo lang("reports"); ?></a>
</nav>

<?php if($puede_ver == 1) { ?>

	<?php
		$id_proyecto = $this->session->project_context;
		$opciones = array("id_proyecto" => $id_proyecto, "id_tipo_formulario" => 1);		
	?>
<?php echo form_open(get_uri("reports/save"), array("id" => "report-form", "class" => "general-form", "role" => "form")); ?>
    <div class="row">
      <div class="col-md-12">
        <div class="page-title clearfix">
          <!--<h1> <i class="fa fa-th-large" title="Abierto"></i> <?php echo $project_info->title ?></h1>-->
          <h1><?php echo lang("reports");?></h1>
        </div>
      </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="col-md-6">
            
            	<div class="form-group multi-column">
            
                    <label class="col-md-3" style="padding-right:0px;margin-right:0px;"><?php echo lang('date_range') ?></label>
    
                   <!--<label for="" class="col-md-2"><?php echo lang('since') ?></label>-->
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
                
                
                    <!--<label for="" class="col-md-2"><?php echo lang('until') ?></label>-->
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
                        <button id="generar_reporte" type="submit" class="btn btn-primary"><span class="fa fa-eye"></span> <?php echo lang('generate_report'); ?></button>
                    </div>
                    
                    <div class="btn-group" role="group">
                     	<button type="button" class="btn btn-danger pull-right" id="export_pdf" disabled="disabled"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></button>
                    </div>
           		</div>
        	</div>
            
        </div>

    </div>        

<?php echo form_close(); ?>  
    <div class="panel">
    	<div class="panel-default">
			<div id="reports_group"></div>
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
<script type="text/javascript" src="https://rawgit.com/johnculviner/jquery.fileDownload/master/src/Scripts/jquery.fileDownload.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
		
		$("#report-form").appForm({
            ajaxSubmit: false
        });
		
		$("#report-form").submit(function(e){
			e.preventDefault();
			return false;
		});
		
		setDatePicker("#default_date_field1");
		setDatePicker("#default_date_field2");
		
		
		$('#generar_reporte').click(function(){
			
			var id_cliente = '<?php echo $client_info->id; ?>';
			var id_proyecto = '<?php echo $project_info->id; ?>';
			var start_date = $('#default_date_field1').val();
			var end_date = $('#default_date_field2').val();
			
			if(id_cliente && id_proyecto && start_date && end_date){
				if((start_date < end_date) || (start_date == end_date)){
	
					$.ajax({
						url:'<?php echo_uri("reports/generate"); ?>',
						type:'post',
						data:{
							id_proyecto: id_proyecto,
							id_cliente: id_cliente,
							start_date: start_date,
							end_date: end_date
						},beforeSend: function() {
					   		$('#reports_group').html('<div style="padding:20px;"><div class="circle-loader"></div><div>');
						},
						success: function(respuesta){;
							$('#reports_group').html(respuesta);	
							$('#export_pdf').removeAttr('disabled');
						}
					});	
					
				}
			}
			
		});
		
		$('#export_pdf').click(function(){
			
			appLoader.show();

			var thousands_separator = '<?php echo ($general_settings->thousands_separator == 1)? "." : ","; ?>';
			var decimals_separator = '<?php echo ($general_settings->decimals_separator == 1)? "." : ","; ?>';
			var start_date = $('#default_date_field1').val();
			var end_date = $('#default_date_field2').val();

			var graficos_consumo = {};
			$('.grafico_consumo').each(function(){
				
				var id = $(this).attr('id');
				
				var chart = $('#' + id).highcharts().options.chart;
				var title = $('#' + id).highcharts().options.title;
				var subtitle = $('#' + id).highcharts().options.subtitle;
				var xAxis = $('#' + id).highcharts().options.xAxis;
				var yAxis = $('#' + id).highcharts().options.yAxis;
				var series = $('#' + id).highcharts().options.series;
				var plotOptions = $('#' + id).highcharts().options.plotOptions;
				var colors = $('#' + id).highcharts().options.colors;
				var exporting = $('#' + id).highcharts().options.exporting;
				var credits = $('#' + id).highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				var image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				graficos_consumo[id] = image;
			});

			var graficos_residuo = {};
			$('.grafico_residuo').each(function(){
				
				var id = $(this).attr('id');
				
				var chart = $('#' + id).highcharts().options.chart;
				var title = $('#' + id).highcharts().options.title;
				var subtitle = $('#' + id).highcharts().options.subtitle;
				var xAxis = $('#' + id).highcharts().options.xAxis;
				var yAxis = $('#' + id).highcharts().options.yAxis;
				var series = $('#' + id).highcharts().options.series;
				var plotOptions = $('#' + id).highcharts().options.plotOptions;
				var colors = $('#' + id).highcharts().options.colors;
				var credits = $('#' + id).highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1800';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = "http://export.highcharts.com/"+getChartName(obj)+".png";
				var image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				graficos_residuo[id] = image;
			});

			// Compromisos ambientales
			var grafico_cumplimientos_totales = "";
			var image = "";
			if($('#grafico_cumplimientos_totales').highcharts()){
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_cumplimientos_totales').highcharts().options.title.text = '<?php echo lang("environmental_commitments"); ?>';
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "15px";
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.size = 150;
				$('#grafico_cumplimientos_totales').highcharts().options.legend.itemStyle.fontSize = "15px";
				$('#grafico_cumplimientos_totales').highcharts().options.title.style.fontSize = "23px";
				
				var chart = $('#grafico_cumplimientos_totales').highcharts().options.chart;
				var title = $('#grafico_cumplimientos_totales').highcharts().options.title;
				var subtitle = $('#grafico_cumplimientos_totales').highcharts().options.subtitle;
				var series = $('#grafico_cumplimientos_totales').highcharts().options.series;
				var plotOptions = $('#grafico_cumplimientos_totales').highcharts().options.plotOptions;
				var colors = $('#grafico_cumplimientos_totales').highcharts().options.colors;
				var credits = $('#grafico_cumplimientos_totales').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({"title":title,"subtitle":subtitle,"series":series,"plotOptions":plotOptions,"chart":chart,"colors":colors,"credits":credits});
				obj.type = 'image/png';
				obj.width = '1800';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//image = "http://export.highcharts.com/"+getChartName(obj)+".png";
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				console.log(image);
			}
			
			grafico_cumplimientos_totales = image;
			
			// Compromisos ambientales reportables
			var grafico_cumplimientos_reportables = "";
			var image = "";
			if($('#grafico_cumplimientos_reportables').highcharts()){
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_cumplimientos_reportables').highcharts().options.title.text = '<?php echo lang("environmental_reportable_commitments"); ?>';
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "15px";
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.size = 150;
				$('#grafico_cumplimientos_reportables').highcharts().options.legend.itemStyle.fontSize = "15px";
				$('#grafico_cumplimientos_reportables').highcharts().options.title.style.fontSize = "23px";
				
				var chart = $('#grafico_cumplimientos_reportables').highcharts().options.chart;
				var title = $('#grafico_cumplimientos_reportables').highcharts().options.title;
				var subtitle = $('#grafico_cumplimientos_reportables').highcharts().options.subtitle;
				var series = $('#grafico_cumplimientos_reportables').highcharts().options.series;
				var plotOptions = $('#grafico_cumplimientos_reportables').highcharts().options.plotOptions;
				var colors = $('#grafico_cumplimientos_reportables').highcharts().options.colors;
				var credits = $('#grafico_cumplimientos_reportables').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({"title":title,"subtitle":subtitle,"series":series,"plotOptions":plotOptions,"chart":chart,"colors":colors,"credits":credits});
				obj.type = 'image/png';
				obj.width = '1800';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//image = "http://export.highcharts.com/"+getChartName(obj)+".png";
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}
			
			grafico_cumplimientos_reportables = image;
			
			// Permisos ambientales
			var grafico_cumplimientos_totales_permisos = "";
			var image = "";
			if($('#grafico_cumplimientos_totales_permisos').highcharts()){
				$('#grafico_cumplimientos_totales_permisos').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_cumplimientos_totales_permisos').highcharts().options.title.text = '<?php echo lang("environmental_permittings"); ?>';
				$('#grafico_cumplimientos_totales_permisos').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_cumplimientos_totales_permisos').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "15px";
				$('#grafico_cumplimientos_totales_permisos').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_cumplimientos_totales_permisos').highcharts().options.plotOptions.pie.size = 150;
				$('#grafico_cumplimientos_totales_permisos').highcharts().options.legend.itemStyle.fontSize = "15px";
				$('#grafico_cumplimientos_totales_permisos').highcharts().options.title.style.fontSize = "23px";
				
				var chart = $('#grafico_cumplimientos_totales_permisos').highcharts().options.chart;
				var title = $('#grafico_cumplimientos_totales_permisos').highcharts().options.title;
				var subtitle = $('#grafico_cumplimientos_totales_permisos').highcharts().options.subtitle;
				var series = $('#grafico_cumplimientos_totales_permisos').highcharts().options.series;
				var plotOptions = $('#grafico_cumplimientos_totales_permisos').highcharts().options.plotOptions;
				var colors = $('#grafico_cumplimientos_totales_permisos').highcharts().options.colors;
				var credits = $('#grafico_cumplimientos_totales_permisos').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({"title":title,"subtitle":subtitle,"series":series,"plotOptions":plotOptions,"chart":chart,"colors":colors,"credits":credits});
				obj.type = 'image/png';
				obj.width = '1800';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//image = "http://export.highcharts.com/"+getChartName(obj)+".png";
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				
			}

			grafico_cumplimientos_totales_permisos = image;




			// Nuevas secciones MIMAire
			// INDICADORES - AGUA
			var grafico_ind_consumo_mensual_agua = "";
			var image = "";
			if($('#grafico_ind_consumo_mensual_agua').highcharts()){
				var chart = $('#grafico_ind_consumo_mensual_agua').highcharts().options.chart;
				var title = $('#grafico_ind_consumo_mensual_agua').highcharts().options.title;
				var subtitle = $('#grafico_ind_consumo_mensual_agua').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_consumo_mensual_agua').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_consumo_mensual_agua').highcharts().options.yAxis;
				var series = $('#grafico_ind_consumo_mensual_agua').highcharts().options.series;
				var plotOptions = $('#grafico_ind_consumo_mensual_agua').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_consumo_mensual_agua').highcharts().options.colors;
				var exporting = $('#grafico_ind_consumo_mensual_agua').highcharts().options.exporting;
				var credits = $('#grafico_ind_consumo_mensual_agua').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_consumo_mensual_agua = image;


			var grafico_ind_consumo_produccion_agua = "";
			var image = "";
			if($('#grafico_ind_consumo_produccion_agua').highcharts()){
				var chart = $('#grafico_ind_consumo_produccion_agua').highcharts().options.chart;
				var title = $('#grafico_ind_consumo_produccion_agua').highcharts().options.title;
				var subtitle = $('#grafico_ind_consumo_produccion_agua').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_consumo_produccion_agua').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_consumo_produccion_agua').highcharts().options.yAxis;
				var series = $('#grafico_ind_consumo_produccion_agua').highcharts().options.series;
				var plotOptions = $('#grafico_ind_consumo_produccion_agua').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_consumo_produccion_agua').highcharts().options.colors;
				var exporting = $('#grafico_ind_consumo_produccion_agua').highcharts().options.exporting;
				var credits = $('#grafico_ind_consumo_produccion_agua').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_consumo_produccion_agua = image;


			// INDICADORES - ENERGÍA
			var grafico_ind_consumo_mensual_electricidad = "";
			var image = "";
			if($('#grafico_ind_consumo_mensual_electricidad').highcharts()){
				var chart = $('#grafico_ind_consumo_mensual_electricidad').highcharts().options.chart;
				var title = $('#grafico_ind_consumo_mensual_electricidad').highcharts().options.title;
				var subtitle = $('#grafico_ind_consumo_mensual_electricidad').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_consumo_mensual_electricidad').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_consumo_mensual_electricidad').highcharts().options.yAxis;
				var series = $('#grafico_ind_consumo_mensual_electricidad').highcharts().options.series;
				var plotOptions = $('#grafico_ind_consumo_mensual_electricidad').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_consumo_mensual_electricidad').highcharts().options.colors;
				var exporting = $('#grafico_ind_consumo_mensual_electricidad').highcharts().options.exporting;
				var credits = $('#grafico_ind_consumo_mensual_electricidad').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_consumo_mensual_electricidad = image;


			var grafico_ind_consumo_produccion_electricidad = "";
			var image = "";
			if($('#grafico_ind_consumo_produccion_electricidad').highcharts()){
				var chart = $('#grafico_ind_consumo_produccion_electricidad').highcharts().options.chart;
				var title = $('#grafico_ind_consumo_produccion_electricidad').highcharts().options.title;
				var subtitle = $('#grafico_ind_consumo_produccion_electricidad').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_consumo_produccion_electricidad').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_consumo_produccion_electricidad').highcharts().options.yAxis;
				var series = $('#grafico_ind_consumo_produccion_electricidad').highcharts().options.series;
				var plotOptions = $('#grafico_ind_consumo_produccion_electricidad').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_consumo_produccion_electricidad').highcharts().options.colors;
				var exporting = $('#grafico_ind_consumo_produccion_electricidad').highcharts().options.exporting;
				var credits = $('#grafico_ind_consumo_produccion_electricidad').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_consumo_produccion_electricidad = image;


			var grafico_ind_consumo_mensual_combustible = "";
			var image = "";
			if($('#grafico_ind_consumo_mensual_combustible').highcharts()){
				var chart = $('#grafico_ind_consumo_mensual_combustible').highcharts().options.chart;
				var title = $('#grafico_ind_consumo_mensual_combustible').highcharts().options.title;
				var subtitle = $('#grafico_ind_consumo_mensual_combustible').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_consumo_mensual_combustible').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_consumo_mensual_combustible').highcharts().options.yAxis;
				var series = $('#grafico_ind_consumo_mensual_combustible').highcharts().options.series;
				var plotOptions = $('#grafico_ind_consumo_mensual_combustible').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_consumo_mensual_combustible').highcharts().options.colors;
				var exporting = $('#grafico_ind_consumo_mensual_combustible').highcharts().options.exporting;
				var credits = $('#grafico_ind_consumo_mensual_combustible').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_consumo_mensual_combustible = image;


			var grafico_ind_consumo_produccion_combustible = "";
			var image = "";
			if($('#grafico_ind_consumo_produccion_combustible').highcharts()){
				var chart = $('#grafico_ind_consumo_produccion_combustible').highcharts().options.chart;
				var title = $('#grafico_ind_consumo_produccion_combustible').highcharts().options.title;
				var subtitle = $('#grafico_ind_consumo_produccion_combustible').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_consumo_produccion_combustible').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_consumo_produccion_combustible').highcharts().options.yAxis;
				var series = $('#grafico_ind_consumo_produccion_combustible').highcharts().options.series;
				var plotOptions = $('#grafico_ind_consumo_produccion_combustible').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_consumo_produccion_combustible').highcharts().options.colors;
				var exporting = $('#grafico_ind_consumo_produccion_combustible').highcharts().options.exporting;
				var credits = $('#grafico_ind_consumo_produccion_combustible').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_consumo_produccion_combustible = image;


			var grafico_ind_consumo_mensual_gas = "";
			var image = "";
			if($('#grafico_ind_consumo_mensual_gas').highcharts()){
				var chart = $('#grafico_ind_consumo_mensual_gas').highcharts().options.chart;
				var title = $('#grafico_ind_consumo_mensual_gas').highcharts().options.title;
				var subtitle = $('#grafico_ind_consumo_mensual_gas').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_consumo_mensual_gas').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_consumo_mensual_gas').highcharts().options.yAxis;
				var series = $('#grafico_ind_consumo_mensual_gas').highcharts().options.series;
				var plotOptions = $('#grafico_ind_consumo_mensual_gas').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_consumo_mensual_gas').highcharts().options.colors;
				var exporting = $('#grafico_ind_consumo_mensual_gas').highcharts().options.exporting;
				var credits = $('#grafico_ind_consumo_mensual_gas').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_consumo_mensual_gas = image;


			var grafico_ind_consumo_produccion_gas = "";
			var image = "";
			if($('#grafico_ind_consumo_produccion_gas').highcharts()){
				var chart = $('#grafico_ind_consumo_produccion_gas').highcharts().options.chart;
				var title = $('#grafico_ind_consumo_produccion_gas').highcharts().options.title;
				var subtitle = $('#grafico_ind_consumo_produccion_gas').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_consumo_produccion_gas').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_consumo_produccion_gas').highcharts().options.yAxis;
				var series = $('#grafico_ind_consumo_produccion_gas').highcharts().options.series;
				var plotOptions = $('#grafico_ind_consumo_produccion_gas').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_consumo_produccion_gas').highcharts().options.colors;
				var exporting = $('#grafico_ind_consumo_produccion_gas').highcharts().options.exporting;
				var credits = $('#grafico_ind_consumo_produccion_gas').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_consumo_produccion_gas = image;
	
			
			// INDICADORES - PRODUCTOS FITOSANITARIOS
			var grafico_ind_consumo_mensual_pf = "";
			var image = "";
			if($('#grafico_ind_consumo_mensual_pf').highcharts()){
				var chart = $('#grafico_ind_consumo_mensual_pf').highcharts().options.chart;
				var title = $('#grafico_ind_consumo_mensual_pf').highcharts().options.title;
				var subtitle = $('#grafico_ind_consumo_mensual_pf').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_consumo_mensual_pf').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_consumo_mensual_pf').highcharts().options.yAxis;
				var series = $('#grafico_ind_consumo_mensual_pf').highcharts().options.series;
				var plotOptions = $('#grafico_ind_consumo_mensual_pf').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_consumo_mensual_pf').highcharts().options.colors;
				var exporting = $('#grafico_ind_consumo_mensual_pf').highcharts().options.exporting;
				var credits = $('#grafico_ind_consumo_mensual_pf').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_consumo_mensual_pf = image;


			var grafico_ind_consumo_produccion_pf = "";
			var image = "";
			if($('#grafico_ind_consumo_produccion_pf').highcharts()){
				var chart = $('#grafico_ind_consumo_produccion_pf').highcharts().options.chart;
				var title = $('#grafico_ind_consumo_produccion_pf').highcharts().options.title;
				var subtitle = $('#grafico_ind_consumo_produccion_pf').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_consumo_produccion_pf').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_consumo_produccion_pf').highcharts().options.yAxis;
				var series = $('#grafico_ind_consumo_produccion_pf').highcharts().options.series;
				var plotOptions = $('#grafico_ind_consumo_produccion_pf').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_consumo_produccion_pf').highcharts().options.colors;
				var exporting = $('#grafico_ind_consumo_produccion_pf').highcharts().options.exporting;
				var credits = $('#grafico_ind_consumo_produccion_pf').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_consumo_produccion_pf = image;


			// INDICADORES - REFRIGERANTES
			var grafico_ind_consumo_mensual_ref = "";
			var image = "";
			if($('#grafico_ind_consumo_mensual_ref').highcharts()){
				var chart = $('#grafico_ind_consumo_mensual_ref').highcharts().options.chart;
				var title = $('#grafico_ind_consumo_mensual_ref').highcharts().options.title;
				var subtitle = $('#grafico_ind_consumo_mensual_ref').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_consumo_mensual_ref').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_consumo_mensual_ref').highcharts().options.yAxis;
				var series = $('#grafico_ind_consumo_mensual_ref').highcharts().options.series;
				var plotOptions = $('#grafico_ind_consumo_mensual_ref').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_consumo_mensual_ref').highcharts().options.colors;
				var exporting = $('#grafico_ind_consumo_mensual_ref').highcharts().options.exporting;
				var credits = $('#grafico_ind_consumo_mensual_ref').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_consumo_mensual_ref = image;


			var grafico_ind_consumo_produccion_ref = "";
			var image = "";
			if($('#grafico_ind_consumo_produccion_ref').highcharts()){
				var chart = $('#grafico_ind_consumo_produccion_ref').highcharts().options.chart;
				var title = $('#grafico_ind_consumo_produccion_ref').highcharts().options.title;
				var subtitle = $('#grafico_ind_consumo_produccion_ref').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_consumo_produccion_ref').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_consumo_produccion_ref').highcharts().options.yAxis;
				var series = $('#grafico_ind_consumo_produccion_ref').highcharts().options.series;
				var plotOptions = $('#grafico_ind_consumo_produccion_ref').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_consumo_produccion_ref').highcharts().options.colors;
				var exporting = $('#grafico_ind_consumo_produccion_ref').highcharts().options.exporting;
				var credits = $('#grafico_ind_consumo_produccion_ref').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_consumo_produccion_ref = image;


			// FIN INDICADORES - RESIDUOS NO PELIGROSOS
			var grafico_ind_residuo_mensual_nhw = "";
			var image = "";
			if($('#grafico_ind_residuo_mensual_nhw').highcharts()){
				var chart = $('#grafico_ind_residuo_mensual_nhw').highcharts().options.chart;
				var title = $('#grafico_ind_residuo_mensual_nhw').highcharts().options.title;
				var subtitle = $('#grafico_ind_residuo_mensual_nhw').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_residuo_mensual_nhw').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_residuo_mensual_nhw').highcharts().options.yAxis;
				var series = $('#grafico_ind_residuo_mensual_nhw').highcharts().options.series;
				var plotOptions = $('#grafico_ind_residuo_mensual_nhw').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_residuo_mensual_nhw').highcharts().options.colors;
				var exporting = $('#grafico_ind_residuo_mensual_nhw').highcharts().options.exporting;
				var credits = $('#grafico_ind_residuo_mensual_nhw').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_residuo_mensual_nhw = image;


			var grafico_ind_residuo_produccion_nhw = "";
			var image = "";
			if($('#grafico_ind_residuo_produccion_nhw').highcharts()){
				var chart = $('#grafico_ind_residuo_produccion_nhw').highcharts().options.chart;
				var title = $('#grafico_ind_residuo_produccion_nhw').highcharts().options.title;
				var subtitle = $('#grafico_ind_residuo_produccion_nhw').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_residuo_produccion_nhw').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_residuo_produccion_nhw').highcharts().options.yAxis;
				var series = $('#grafico_ind_residuo_produccion_nhw').highcharts().options.series;
				var plotOptions = $('#grafico_ind_residuo_produccion_nhw').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_residuo_produccion_nhw').highcharts().options.colors;
				var exporting = $('#grafico_ind_residuo_produccion_nhw').highcharts().options.exporting;
				var credits = $('#grafico_ind_residuo_produccion_nhw').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_residuo_produccion_nhw = image;



			// INDICADORES - RESIDUOS PELIGROSOS
			var grafico_ind_residuo_mensual_hw = "";
			var image = "";
			if($('#grafico_ind_residuo_mensual_hw').highcharts()){
				var chart = $('#grafico_ind_residuo_mensual_hw').highcharts().options.chart;
				var title = $('#grafico_ind_residuo_mensual_hw').highcharts().options.title;
				var subtitle = $('#grafico_ind_residuo_mensual_hw').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_residuo_mensual_hw').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_residuo_mensual_hw').highcharts().options.yAxis;
				var series = $('#grafico_ind_residuo_mensual_hw').highcharts().options.series;
				var plotOptions = $('#grafico_ind_residuo_mensual_hw').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_residuo_mensual_hw').highcharts().options.colors;
				var exporting = $('#grafico_ind_residuo_mensual_hw').highcharts().options.exporting;
				var credits = $('#grafico_ind_residuo_mensual_hw').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_residuo_mensual_hw = image;


			var grafico_ind_residuo_produccion_hw = "";
			var image = "";
			if($('#grafico_ind_residuo_produccion_hw').highcharts()){
				var chart = $('#grafico_ind_residuo_produccion_hw').highcharts().options.chart;
				var title = $('#grafico_ind_residuo_produccion_hw').highcharts().options.title;
				var subtitle = $('#grafico_ind_residuo_produccion_hw').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_residuo_produccion_hw').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_residuo_produccion_hw').highcharts().options.yAxis;
				var series = $('#grafico_ind_residuo_produccion_hw').highcharts().options.series;
				var plotOptions = $('#grafico_ind_residuo_produccion_hw').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_residuo_produccion_hw').highcharts().options.colors;
				var exporting = $('#grafico_ind_residuo_produccion_hw').highcharts().options.exporting;
				var credits = $('#grafico_ind_residuo_produccion_hw').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_residuo_produccion_hw = image;

			/*
					: grafico_ind_residuo_mensual_lhw,
					: grafico_ind_residuo_produccion_lhw,
			*/

			var grafico_ind_residuo_mensual_lhw = "";
			var image = "";
			if($('#grafico_ind_residuo_mensual_lhw').highcharts()){
				var chart = $('#grafico_ind_residuo_mensual_lhw').highcharts().options.chart;
				var title = $('#grafico_ind_residuo_mensual_lhw').highcharts().options.title;
				var subtitle = $('#grafico_ind_residuo_mensual_lhw').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_residuo_mensual_lhw').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_residuo_mensual_lhw').highcharts().options.yAxis;
				var series = $('#grafico_ind_residuo_mensual_lhw').highcharts().options.series;
				var plotOptions = $('#grafico_ind_residuo_mensual_lhw').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_residuo_mensual_lhw').highcharts().options.colors;
				var exporting = $('#grafico_ind_residuo_mensual_lhw').highcharts().options.exporting;
				var credits = $('#grafico_ind_residuo_mensual_lhw').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_residuo_mensual_lhw = image;


			var grafico_ind_residuo_produccion_lhw = "";
			var image = "";
			if($('#grafico_ind_residuo_produccion_lhw').highcharts()){
				var chart = $('#grafico_ind_residuo_produccion_lhw').highcharts().options.chart;
				var title = $('#grafico_ind_residuo_produccion_lhw').highcharts().options.title;
				var subtitle = $('#grafico_ind_residuo_produccion_lhw').highcharts().options.subtitle;
				var xAxis = $('#grafico_ind_residuo_produccion_lhw').highcharts().options.xAxis;
				var yAxis = $('#grafico_ind_residuo_produccion_lhw').highcharts().options.yAxis;
				var series = $('#grafico_ind_residuo_produccion_lhw').highcharts().options.series;
				var plotOptions = $('#grafico_ind_residuo_produccion_lhw').highcharts().options.plotOptions;
				var colors = $('#grafico_ind_residuo_produccion_lhw').highcharts().options.colors;
				var exporting = $('#grafico_ind_residuo_produccion_lhw').highcharts().options.exporting;
				var credits = $('#grafico_ind_residuo_produccion_lhw').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"title":title,
					"subtitle":subtitle,
					"xAxis":xAxis,
					"yAxis":yAxis,
					"series":series,
					"plotOptions":plotOptions,
					"chart":chart,
					"colors":colors, 
					"exporting":exporting,
					"credits":credits
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				//var image = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				image = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			}

			grafico_ind_residuo_produccion_lhw = image;

			
			$.ajax({
				url:  '<?php echo_uri("reports/get_pdf") ?>',
				type:  'post',
				data: {
					start_date: start_date,
					end_date: end_date,
					graficos_consumo: graficos_consumo,
					graficos_residuo: graficos_residuo,
					grafico_cumplimientos_totales: grafico_cumplimientos_totales,
					grafico_cumplimientos_totales_permisos: grafico_cumplimientos_totales_permisos,
					grafico_cumplimientos_reportables: grafico_cumplimientos_reportables,
					grafico_ind_consumo_mensual_agua: grafico_ind_consumo_mensual_agua,
					grafico_ind_consumo_produccion_agua: grafico_ind_consumo_produccion_agua,
					grafico_ind_consumo_mensual_electricidad: grafico_ind_consumo_mensual_electricidad,
					grafico_ind_consumo_produccion_electricidad: grafico_ind_consumo_produccion_electricidad,
					grafico_ind_consumo_mensual_combustible: grafico_ind_consumo_mensual_combustible,
					grafico_ind_consumo_produccion_combustible: grafico_ind_consumo_produccion_combustible,
					grafico_ind_consumo_mensual_gas: grafico_ind_consumo_mensual_gas,
					grafico_ind_consumo_produccion_gas: grafico_ind_consumo_produccion_gas,
					grafico_ind_consumo_mensual_pf: grafico_ind_consumo_mensual_pf,
					grafico_ind_consumo_produccion_pf: grafico_ind_consumo_produccion_pf,
					grafico_ind_consumo_mensual_ref: grafico_ind_consumo_mensual_ref,
					grafico_ind_consumo_produccion_ref: grafico_ind_consumo_produccion_ref,
					grafico_ind_residuo_mensual_nhw: grafico_ind_residuo_mensual_nhw,
					grafico_ind_residuo_produccion_nhw: grafico_ind_residuo_produccion_nhw,
					grafico_ind_residuo_mensual_hw: grafico_ind_residuo_mensual_hw,
					grafico_ind_residuo_produccion_hw: grafico_ind_residuo_produccion_hw,
					grafico_ind_residuo_mensual_lhw: grafico_ind_residuo_mensual_lhw,
					grafico_ind_residuo_produccion_lhw: grafico_ind_residuo_produccion_lhw,
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
			url:  '<?php echo_uri("reports/borrar_temporal") ?>',
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
		
	
});   
</script>	