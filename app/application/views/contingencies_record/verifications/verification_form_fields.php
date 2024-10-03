<input type="hidden" name="id_verificacion" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<div class="form-group">
    <label for="evento" class="<?php echo $label_column; ?>"><?php echo lang('event'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("evento", $evento_dropdown, $evento, "id='evento' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group">
    <label for="descripcion_verificacion" class="<?php echo $label_column; ?>"><?php echo lang('verification_description'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "descripcion_verificacion",
            "name" => "descripcion_verificacion",
            "value" => $model_info->descripcion_verificacion,
            "class" => "form-control",
			"placeholder" => lang('verification_description'),
            //"autofocus" => true,
            // "data-rule-required" => true,
            // "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>


<div class="form-group">
    <label for="responsable_verificacion" class="<?php echo $label_column; ?>"><?php echo lang('responsible_for_verification'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("responsable_verificacion", $responsable_verificacion_dropdown, $responsable_verificacion, "id='responsable_verificacion' class='select2 validate-hidden' data-rule-required='false', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>


<div class="form-group">
    <label for="fecha_verificacion" class="<?php echo $label_column; ?>"><?php echo lang('verification_date'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "fecha_verificacion",
            "name" => "fecha_verificacion",
            "value" => $model_info->fecha_verificacion,
            "class" => "form-control datepicker",
			"placeholder" => "YYYY-MM-DD",
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			//"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        
		$('[data-toggle="tooltip"]').tooltip();
		$('#verification-form .select2').select2();

        setDatePicker($('#fecha_verificacion'));

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

        <?php if(count($eventos_en_verificaciones) > 0){
            foreach($eventos_en_verificaciones as $evento){ 
                if($model_info->id_contingencia_evento != $evento){ ?>
                $('#evento option[value="' + <?php echo $evento; ?> + '"]').attr("disabled", "disabled");
        <?php   }
            }
        } ?>
        
    });
</script>