<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

<div class="form-group">
    <label for="company_name" class="<?php echo $label_column; ?>"><?php echo lang('company_name_2'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "company_name",
            "name" => "company_name",
            "value" => $model_info->company_name,
            "class" => "form-control",
            "placeholder" => lang('company_name_2'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="company_rut" class="<?php echo $label_column; ?>"><?php echo lang('company_rut'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "company_rut",
            "name" => "company_rut",
            "value" => $model_info->company_rut,
            "class" => "form-control",
            "placeholder" => lang('company_rut'),
            //"autofocus" => true,
            //"data-rule-required" => true,
            //"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="company_code" class="<?php echo $label_column; ?>"><?php echo lang('company_code'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "company_code",
            "name" => "company_code",
            "value" => $model_info->company_code,
            "class" => "form-control",
            "placeholder" => lang('company_code'),
            //"autofocus" => true,
            //"data-rule-required" => true,
            //"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
	<label for="receiving_establishment_treatment" class="<?php echo $label_column; ?>"><?php echo lang('receiving_establishment_treatment'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
			echo form_dropdown("receiving_establishment_treatment", $receiving_establishment_treatment_dropdown, array($model_info->id_treatment_sinader), "id='receiving_establishment_treatment' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
</div>

<div class="form-group">
    <label for="address" class="<?php echo $label_column; ?>"><?php echo lang('address'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "address",
            "name" => "address",
            "value" => $model_info->address,
            "class" => "form-control",
            "placeholder" => lang('address'),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="city" class="<?php echo $label_column; ?>"><?php echo lang('region'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "city",
            "name" => "city",
            "value" => $model_info->city,
            "class" => "form-control",
            "placeholder" => lang('region'),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="province" class="<?php echo $label_column; ?>"><?php echo lang('province'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "province",
            "name" => "province",
            "value" => $model_info->province,
            "class" => "form-control",
            "placeholder" => lang('province'),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="commune" class="<?php echo $label_column; ?>"><?php echo lang('commune'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "commune",
            "name" => "commune",
            "value" => $model_info->commune,
            "class" => "form-control",
            "placeholder" => lang('commune'),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
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
		
		$('#feeders-form .select2').select2({
			/*sortResults: function(data) {
				return data.sort(function (a, b) {
					a = a.text.toLowerCase();
					b = b.text.toLowerCase();
					if (a > b) {
						return 1;
					} else if (a < b) {
						return -1;
					}
					return 0;
				});
			}*/
		});

		//setDatePicker("#feeders-form .datepicker");
		//setTimePicker('#feeders-form .timepicker');
    });
</script>