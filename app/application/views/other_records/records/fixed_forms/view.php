<?php echo form_open("", array("id" => "other_records-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    
	<?php if($year_semester){ ?>
		<div class="form-group">
			<label for="year_semester" class="col-md-3"><?php echo lang('year').'-'.lang('semester'); ?></label>
			<div class="col-md-9">
				<?php
				echo $year_semester;
				?>
			</div>
		</div>
	<? } ?>

	<?php if ($date) { ?>
		<div class="form-group">
			<label for="date" class="col-md-3"><?php echo lang('date'); ?></label>
			<div class="col-md-9">
				<?php
				echo $date;
				?>
			</div>
		</div>
	<?php } ?>
	
<?php
$html = '';
foreach($campos as $campo){
	
	$html .= '<div class="form-group">';
	if(($campo->id_tipo_campo == 12)||($campo->id_tipo_campo == 11)){// si divisor o texto fijo
		$html .= '<div class="col-md-12">';
		$html .= '<div style="word-wrap: break-word;">';
		$html .= $campo->default_value;
		$html .= '</div>';
		$html .= '</div>';
	}else{
		$html .= '<label for="'.$campo->html_name.'" class="col-md-3">'.$campo->nombre.'</label>';
		$html .= '<div class="col-md-9">';
		$html .= $Other_records_controller->get_field_value_fixed_forms($campo->id, $model_info->id, $id_proyecto);
		$html .= '</div>';
	}
	$html .= '</div>';
	
}

echo $html;

?>

	<div class="form-group">
        <label for="created_by" class="col-md-3"><?php echo lang('created_by'); ?></label>
        <div class="col-md-9">
            <?php
			echo $created_by;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_by" class="col-md-3"><?php echo lang('modified_by'); ?></label>
        <div class="col-md-9">
            <?php
            echo $modified_by;
            ?>
        </div>
    </div>

	<div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
			echo time_date_zone_format($model_info->created, $id_proyecto);
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->modified)?time_date_zone_format($model_info->modified, $id_proyecto):'-';
            ?>
        </div>
    </div>
    
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

	$(document).ready(function(){
		
		$('[data-toggle="tooltip"]').tooltip();
		$('#other_records-form .select2').select2();
		setDatePicker("#other_records-form .datepicker");
		setTimePicker('#other_records-form .timepicker');
		
	});

</script>    