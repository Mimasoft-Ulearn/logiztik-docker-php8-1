<div id="page-content" class="p20 clearfix">

	<!--Breadcrumb section-->
	<nav class="breadcrumb">
	<a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
	<a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
	<a class="breadcrumb-item" href="#"><?php echo lang("environmental_footprints"); ?> /</a>
	<a class="breadcrumb-item" href="<?php echo get_uri("unit_processes"); ?>"><?php echo lang("unit_processes"); ?></a>
	</nav>

	<div class="row">  
		<div class="col-md-12">
			<div class="page-title clearfix" style="background-color:#FFF;">
				<h1><i class="fa fa-th-large"></i> <?php echo $project_info->title . " | " . lang("unit_processes"); ?></h1>
			</div>
		</div>
	</div>
  
	<?php if($puede_ver == 1) { ?>
  
	<?php if(count($unidades_funcionales)) { ?>

		<?php echo form_open(get_uri("#"), array("id" => "unit_processes-form", "class" => "general-form", "role" => "form")); ?>
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
									<a href="#" class="btn btn-danger pull-right" id="unit_processes_pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a> 
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
	
		<div id="unit_processes_group"></div>

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

<?php } else { ?>

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

<!--<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script>-->
<script id="script_index" type="text/javascript">


$(document).ready(function () {
	
	//General Settings
	var decimals_separator = AppHelper.settings.decimalSeparator;
	var thousands_separator = AppHelper.settings.thousandSeparator;
	var decimal_numbers = AppHelper.settings.decimalNumbers;	

	$("#functional_unit").select2();
	setDatePicker("#start_date");
	setDatePicker("#end_date");
	
	$("#unit_processes-form").appForm({
            ajaxSubmit: false
	});
	$("#unit_processes-form").submit(function(e){
		e.preventDefault();
		return false;
	});
	
	$('#btn_generar').click(function(){

		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		var id_unidad_funcional = $('#functional_unit').val();
		
		if(start_date && end_date){
			if((start_date < end_date) || (start_date == end_date)){
				
				$('#unit_processes_pdf').attr('disabled', true);
				
				$.ajax({
					url:'<?php echo_uri("unit_processes/get_unit_processes"); ?>',
					type:'post',
					data:{
						start_date: start_date,
						end_date: end_date,
						id_unidad_funcional: id_unidad_funcional
					},beforeSend: function() {
						$('#unit_processes_group').html('<div class="panel"><div style="padding:20px;"><div class="circle-loader"></div><div><div>');
					},
					success: function(respuesta){;
						$('#unit_processes_group').html(respuesta);	
						$('#unit_processes_pdf').removeAttr('disabled');
					}
				});	
				
			}
		}
		
	});
	
	$('#btn_clean').click(function(){
		
		$('#unit_processes_pdf').attr('disabled', true);
		$('#start_date').val("");
		$('#end_date').val("");
		$("#functional_unit").val("").trigger('change');
		
		$.ajax({
			url:'<?php echo_uri("unit_processes/get_unit_processes"); ?>',
			type:'post',
			beforeSend: function() {
				$('#unit_processes_group').html('<div class="panel"><div style="padding:20px;"><div class="circle-loader"></div><div><div>');
			},
			success: function(respuesta){;
				$('#unit_processes_group').html(respuesta);	
				$('#unit_processes_pdf').removeAttr('disabled');
			}
		});	
		
	});
	
	startPage();
	function startPage(){
		$('#unit_processes_pdf').attr('disabled', true);

		var start_date = moment().subtract(11, 'months').format('YYYY-MM-')+'01';
		var end_date = moment().clone().endOf('month').format('YYYY-MM-DD');
				
		$.ajax({
			url:'<?php echo_uri("unit_processes/get_unit_processes"); ?>',
			type:'post',
			data:{
				start_date: start_date,
				end_date: end_date,
				id_unidad_funcional: ''
			},beforeSend: function() {
				$('#unit_processes_group').html('<div class="panel"><div style="padding:20px;"><div class="circle-loader"></div><div><div>');
			},
			success: function(respuesta){;
				$('#unit_processes_group').html(respuesta);	
				$('#unit_processes_pdf').removeAttr('disabled');
			}
		});	
	}
	
});
</script>