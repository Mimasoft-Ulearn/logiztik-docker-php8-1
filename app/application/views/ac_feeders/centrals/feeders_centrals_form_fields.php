<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="nombre_central" class="<?php echo $label_column; ?>"><?php echo lang('central_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "nombre_central",
            "name" => "nombre_central",
            "value" => $model_info->nombre_central,
            "class" => "form-control",
            "placeholder" => lang('central_name'),
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
    <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('macrozone'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("id_macrozona", $macrozonas, array($model_info->id_macrozona), "id='id_macrozona' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div class="form-group">
	<label for="observaciones" class="<?php echo $label_column; ?>"><?php echo lang('observations'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_textarea(array(
			"id" => "observaciones",
			"name" => "observaciones",
			"value" => $model_info->observaciones,
			"class" => "form-control",
			"placeholder" => lang('observations'),
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
		$('#feeders_centrals-form .select2').select2();

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