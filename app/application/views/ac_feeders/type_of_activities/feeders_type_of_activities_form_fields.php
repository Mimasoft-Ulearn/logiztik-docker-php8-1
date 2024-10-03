<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="tipo_actividad" class="<?php echo $label_column; ?>"><?php echo lang('ac_type_of_activity'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("id_tipo_actividad", $tipos_actividad, array($model_info->id_tipo_actividad), "id='id_tipo_actividad' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div class="form-group">
    <label for="actividad" class="<?php echo $label_column; ?>"><?php echo lang('activity'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "actividad",
            "name" => "actividad",
            "value" => $model_info->actividad,
            "class" => "form-control",
            "placeholder" => lang('activity'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
	<label for="descripcion_actividad" class="<?php echo $label_column; ?>"><?php echo lang('activity_description'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_textarea(array(
			"id" => "descripcion_actividad",
			"name" => "descripcion_actividad",
			"value" => $model_info->descripcion_actividad,
			"class" => "form-control",
			"placeholder" => lang('activity_description'),
			"style" => "height:150px;",
			//"data-rule-required" => true,
			//"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "2000"
		));
		?>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        
		//$('[data-toggle="tooltip"]').tooltip();
		$('#feeders_type_of_activities-form .select2').select2();

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

    });
</script>