<div id="page-content" class="p20 clearfix">

    <!--Breadcrumb section-->
    <nav class="breadcrumb">
        <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
        <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$proyecto->id); ?>"><?php echo $proyecto->title; ?> /</a>
        <a class="breadcrumb-item" href="#"><?php echo lang("carbon_environmental_footprints"); ?> /</a>
        <a class="breadcrumb-item" href="<?php echo get_uri("functional_units"); ?>"><?php echo lang("functional_units"); ?></a>
    </nav>
	
    <div class="row">  
        <div class="col-md-12">
            <div class="page-title clearfix" style="background-color:#FFF;">
                <h1><i class="fa fa-th-large"></i> <?php echo $proyecto->title . " | " . lang("functional_units"); ?></h1>
            </div>
        </div>
    </div>
    
    <?php if($puede_ver == 1) { ?>

		<?php if(count($unidades_funcionales)) { ?>
        
            <?php echo form_open(get_uri("#"), array("id" => "functional_units-form", "class" => "general-form", "role" => "form")); ?>
                <div class="panel panel-default">
                
                    <div class="panel-body">

                        <div class="col-md-12">

                            <div class="col-md-4">
                                <div class="form-group multi-column">
                                    <label class="col-md-3" for="functional_unit"><?php echo lang("functional_unit");?></label>
                                    <div class="col-md-9">
                                        <?php
                                            echo form_dropdown("functional_unit", $dropdown_functional_units, "", "id='functional_unit' class='select2'");
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                            
                                <div class="form-group multi-column">
                            
                                    <label class="col-md-2" style="padding-right:0px;margin-right:0px;"><?php echo lang('date_range') ?></label>
                    
                                    <!--<label for="" class="col-md-2"><?php echo lang('since') ?></label>-->
                                    <div class="col-md-5">
                                        <?php 
                                            echo form_input(array(
                                                "id" => "start_date",
                                                "name" => "start_date",
                                                "value" => "",
                                                "class" => "form-control",
                                                "placeholder" => lang('since'),
                                                "data-rule-required" => true,
                                                "data-msg-required" => lang("field_required"),
                                                //"data-rule-greaterThanOrEqual" => 'end_date',
                                                //"data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
                                                "autocomplete" => "off",
                                            ));
                                        ?>
                                    </div>
                                
                                
                                    <!--<label for="" class="col-md-2"><?php echo lang('until') ?></label>-->
                                    <div class="col-md-5">
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

                        </div>

                        <div class="col-md-12">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">

                                <div class="pull-right">
                                    <div class="btn-group" role="group">
                                        <button id="btn_generar" type="submit" class="btn btn-primary"><span class="fa fa-eye"></span> <?php echo lang('generate'); ?></button>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <a href="#" class="btn btn-danger pull-right" id="functional_units_pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a>
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
            
                </div>        
            <?php echo form_close(); ?>

            <div id="functional_units_group"></div>

      <?php } else { ?>
      
      <div class="row"> 
        <div class="col-md-12 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div id="app-alert-d1via" class="app-alert alert alert-warning alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>-->
                        <div class="app-alert-message"><?php echo lang("no_information_available"); ?></div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger hide" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      
      <?php } ?>
  
	<?php } else {?>
    
        <div class="row"> 
            <div class="col-md-12 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="app-alert-d1via" class="app-alert alert alert-warning alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>-->
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
    
    // Esta variable se llena con datos para ser exportados a pdf, 
    // la información se obtiene en los archivos 
    // functional_units_carbon/client/functional_units_by_date2.php y functional_units_carbon/client/functional_unit_by_date2.php
    var array_export_pdf = [];

    $(document).ready(function () {

        //General Settings
        var decimals_separator = AppHelper.settings.decimalSeparator;
        var thousands_separator = AppHelper.settings.thousandSeparator;
        var decimal_numbers = AppHelper.settings.decimalNumbers;

		var maxHeight_uf = Math.max.apply(null, $("#page-content .huella").map(function (){
			return $(this).find("div.b-b").height();
		}));
		
		$("#page-content .huella > div.b-b").height(maxHeight_uf);

        $('#page-content a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('.chart').each(function() { 
                $(this).highcharts().reflow();
            });
        });
		
        $("#functional_unit").select2();
		setDatePicker("#start_date");
		setDatePicker("#end_date");

        $("#functional_units_pdf").attr('disabled', 'disabled');
        
        // Exportar a PDF los datos de la Unidad Funcional seleccionada en la pestaña lateral izquierda
        $(document).on("click", "#functional_unit_pdf", function(e) {	
			if( $("#functional_unit_pdf").attr('disabled') == undefined ){
                var id_unidad_funcional = $('ul#ul_menu_unidades_funcionales li.active a').attr("data-id_unidad_funcional");
                
                // var tabs_uf = $('ul#ul_menu_unidades_funcionales li a');

                var ids_uf = [];                
                ids_uf.push(id_unidad_funcional);
                
                // console.log(ids_uf); return false;

                exportar_pdf(ids_uf);

            }
		});
        
        // Exportar a PDF los datos de todas las Unidades Funcionales
		$(document).on("click", "#functional_units_pdf", function(e) {	
            if( $("#functional_units_pdf").attr('disabled') == undefined ){
                // var id_unidad_funcional = $('ul#ul_menu_unidades_funcionales li.active a').attr("data-id_unidad_funcional");
                
                var tabs_uf = $('ul#ul_menu_unidades_funcionales li a');

                var ids_uf = [];
                for( i = 0; i < tabs_uf.length; i++){
                    ids_uf.push(tabs_uf[i].attributes["data-id_unidad_funcional"].value);
                }
                // console.log(ids_uf); return false;

                exportar_pdf(ids_uf);

            }
		});
		
        function exportar_pdf(ids_uf){
            
            appLoader.show();

            var imagenes_graficos_por_uf = [];

            for(i = 0; i < ids_uf.length; i++){

                var id_unidad_funcional = ids_uf[i];

                // Gráfico Impactos por huella
                var chart = $('#impactos_uf_'+id_unidad_funcional).highcharts().options.chart;
                var title = $('#impactos_uf_'+id_unidad_funcional).highcharts().options.title;
                var subtitle = $('#impactos_uf_'+id_unidad_funcional).highcharts().options.subtitle;
                var xAxis = $('#impactos_uf_'+id_unidad_funcional).highcharts().options.xAxis;
                var yAxis = $('#impactos_uf_'+id_unidad_funcional).highcharts().options.yAxis;
                var series = $('#impactos_uf_'+id_unidad_funcional).highcharts().options.series;
                var plotOptions = $('#impactos_uf_'+id_unidad_funcional).highcharts().options.plotOptions;
                var colors = $('#impactos_uf_'+id_unidad_funcional).highcharts().options.colors;
                var exporting = $('#impactos_uf_'+id_unidad_funcional).highcharts().options.exporting;
                var credits = $('#impactos_uf_'+id_unidad_funcional).highcharts().options.credits;
                
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
                
                var image_impactos_por_huella = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';


                // Gráfico Proporción mensual
                var chart = $('#proporcion_uf_'+id_unidad_funcional).highcharts().options.chart;
                var title = $('#proporcion_uf_'+id_unidad_funcional).highcharts().options.title;
                var subtitle = $('#proporcion_uf_'+id_unidad_funcional).highcharts().options.subtitle;
                var xAxis = $('#proporcion_uf_'+id_unidad_funcional).highcharts().options.xAxis;
                var yAxis = $('#proporcion_uf_'+id_unidad_funcional).highcharts().options.yAxis;
                var series = $('#proporcion_uf_'+id_unidad_funcional).highcharts().options.series;
                var plotOptions = $('#proporcion_uf_'+id_unidad_funcional).highcharts().options.plotOptions;
                var colors = $('#proporcion_uf_'+id_unidad_funcional).highcharts().options.colors;
                var exporting = $('#proporcion_uf_'+id_unidad_funcional).highcharts().options.exporting;
                var credits = $('#proporcion_uf_'+id_unidad_funcional).highcharts().options.credits;
                
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
                
                var image_proporcion_mensual = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';  
                
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();

                var imagenes_graficos = {
                    image_impactos_por_huella: image_impactos_por_huella,
                    image_proporcion_mensual: image_proporcion_mensual
                };

                imagenes_graficos_por_uf[id_unidad_funcional] = imagenes_graficos;
            }
            // console.log(imagenes_graficos_por_uf); return false;
            // console.log(array_export_pdf); return false;
            $.ajax({
                url:  '<?php echo_uri("functional_units_carbon/get_pdf") ?>',
                type:  'post',
                data:{
                ids_uf: ids_uf,
                imagenes_graficos_por_uf: imagenes_graficos_por_uf,
                start_date: start_date,
                end_date: end_date,
                impacts_by_footprint: JSON.stringify(array_export_pdf) // Datos de sección "Impactos ambientales por ..." se envian al server para no tener que recalcularlos
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
        }
		
		function borrar_temporal(uri){
			
			$.ajax({
				url:  '<?php echo_uri("functional_units_carbon/borrar_temporal") ?>',
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

		
		$("#functional_units-form").appForm({
            ajaxSubmit: false
        });
		$("#functional_units-form").submit(function(e){
			e.preventDefault();
			return false;
		});
		
		$('#btn_generar').click(function(){
			
			$('#functional_units_pdf').attr('disabled', 'disabled');
			
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
            var id_unidad_funcional = $('#functional_unit').val();

			if(start_date && end_date){
				if((start_date < end_date) || (start_date == end_date)){
	
					$.ajax({
						url:'<?php echo_uri("functional_units_carbon/get_functional_units"); ?>',
						type:'post',
						data:{
							start_date: start_date,
							end_date: end_date,
                            id_unidad_funcional: id_unidad_funcional
						},beforeSend: function() {
					   		$('#functional_units_group').html('<div class="panel"><div style="padding:20px;"><div class="circle-loader"></div><div><div>');
						},
						success: function(respuesta){;	
                            $('#functional_units_group').html(respuesta);	
					        $('#functional_units_pdf').removeAttr('disabled');
						}
					});	
					
				}
			}
			
		});
		
		$('#btn_clean').click(function(){
			
			$('#functional_units_pdf').attr('disabled', 'disabled');
			$('#start_date').val("");
			$('#end_date').val("");
            $("#functional_unit").val("").trigger('change');
			
			$.ajax({
				url:'<?php echo_uri("functional_units_carbon/get_functional_units"); ?>',
				type:'post',
				beforeSend: function() {
					$('#functional_units_group').html('<div class="panel"><div style="padding:20px;"><div class="circle-loader"></div><div><div>');
				},
				success: function(respuesta){;	
                    $('#functional_units_group').html(respuesta);
					$('#functional_units_pdf').removeAttr('disabled');
				}
			});	
			
		});
	
    });
</script> 