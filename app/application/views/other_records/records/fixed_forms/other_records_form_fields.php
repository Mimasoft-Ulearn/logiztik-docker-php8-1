<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

<?php $datos = json_decode($model_info->datos, true); ?>

<?php if($record_info->codigo_formulario_fijo != 'or_unidades_funcionales'){ ?>
	<div class="form-group">
	<label for="date_filed" class="col-md-3"><?php echo lang('year').'-'.lang('semester'); ?></label>
		<div class=" col-md-9">
			<?php 
				$array_year_semester = array( 
					"-" => "-",
					"2019-I" => "2019-I",
					"2019-II" => "2019-II",
					"2020-I" => "2020-I",
					"2020-II" => "2020-II",
					"2021-I" => "2021-I",
					"2021-II" => "2021-II",
					"2022-I" => "2022-I",
					"2022-II" => "2022-II"
				);

				echo form_dropdown('year_semester',$array_year_semester, $datos['year_semester'],"id='year_semester' class='select2 validate-hidden'");
			?>
		</div>
	</div>
	
	<div class="form-group">
	<label for="date" class="col-md-3"><?php echo lang('date'); ?></label>
		<div class=" col-md-9">
			<?php
			echo form_input(array(
				"id" => "date",
				"name" => "date",
				"value" => $datos["fecha"],
				"class" => "form-control datepicker",
				"placeholder" => "YYYY-MM-DD",
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"autocomplete" => "off",
			));
			?>
		</div>
	</div>
<?php } ?>

<?php
	$html = '';
	foreach($campos as $campo){
		
		$html .= '<div class="form-group multi-column">';
		if(($campo->id_tipo_campo == 12)||($campo->id_tipo_campo == 11)){// si divisor o texto fijo
			$html .= '<div class="col-md-12">';
			$html .= '<div style="word-wrap: break-word;">';
			$html .= $campo->default_value;
			$html .= '</div>';
			$html .= '</div>';
		}else{
			$html .= '<label for="'.$campo->html_name.'" class="col-md-3">'.$campo->nombre.'</label>';
			$html .= '<div class="col-md-9">';
			$html .= $Other_records_controller->get_field_fixed_form($campo->id, $model_info->id, NULL, $record_info->id);
			$html .= '</div>';
		}
		$html .= '</div>';
		
		
	}
	
	echo $html;

?>		

<script type="text/javascript">
    $(document).ready(function () {
		
		$('#other_records-form .select2').select2();
        $('[data-toggle="tooltip"]').tooltip();
		
		$('input[type="text"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$('textarea[maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 1990,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		setDatePicker("#other_records-form .datepicker");
		setTimePicker('#other_records-form .timepicker');
    });
</script>