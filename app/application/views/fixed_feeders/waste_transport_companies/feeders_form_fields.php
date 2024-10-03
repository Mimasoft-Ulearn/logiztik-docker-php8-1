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
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="company_registration_code" class="<?php echo $label_column; ?>"><?php echo lang('company_registration_code'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "company_registration_code",
            "name" => "company_registration_code",
            "value" => $model_info->company_registration_code,
            "class" => "form-control",
            "placeholder" => lang('company_registration_code'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group" id="modelo" style="display:none;">
    <div class="col-md-3"></div>
    <div class="col-md-8">
            <input type="text" class="form-control" name="new_patents[]" maxlength="255" placeholder="<?php echo lang('patent_plate'); ?>" autocomplete="off">
        </div>
    <div class="col-md-1">
        <button type="button" class="btn btn-sm btn-danger" onclick="removeOption($(this));"><i class="fa fa-minus"></i></button>
    </div>
</div>

<div class="form-group">
    <?php $info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('patent_info').'"><i class="fa fa-question-circle"></i></span>'; ?>
    
    <label for="" class="col-md-3"><?php echo lang('patent_plate').' '.$info; ?></label>
    <div class="col-md-9">    
        <button type="button" id="agregar_patente" class="btn btn-xs btn-success col-sm-1" onclick="addOptions();"><i class="fa fa-plus"></i></button>
        <button type="button" id="eliminar_patente" class="btn btn-xs btn-danger col-sm-offset-1 col-sm-1" onclick="removeOptions();"><i class="fa fa-minus"></i></button>
    </div>
</div>


<div id="grupo_patentes">
    
    <div class="form-group patente">
        <div class="col-md-3"></div>

        <?php if(count($array_patentes)){ ?>
            <div class = "col-md-8">
                <?php
                echo form_input(array(
                    "id" => "patent",
                    "name" => "old_patents[]",
                    "value" => $array_patentes[0]->patent,
                    "class" => "form-control",
                    "placeholder" => lang('patent_plate'),
                    //"autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "autocomplete"=> "off",
                    "maxlength" => "255"
                ));
                ?>
            </div>
            <input type="hidden" name="patents_id[]" value="<?php echo $array_patentes[0]->id; ?>">
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeOption($(this));"><i class="fa fa-minus"></i></button>
            </div>

        <?php }else{ ?>

            <div class = "col-md-8">
                <?php
                echo form_input(array(
                    "id" => "patent",
                    "name" => "new_patents[]",
                    "value" => '',
                    "class" => "form-control",
                    "placeholder" => lang('patent_plate'),
                    //"autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "autocomplete"=> "off",
                    "maxlength" => "255"
                ));
                ?>
            </div>

            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeOption($(this));"><i class="fa fa-minus"></i></button>
            </div>

        <?php } ?>
    </div>


<?php
if(count($array_patentes)){
    
    $html_patentes = '';
    
    foreach($array_patentes as $index => $patente){
        
        if($index == 0){continue;}
        
        $html_patentes .= '<div class="form-group patente">';
            $html_patentes .= '<div class="col-md-3"></div>';
            $html_patentes .= '<div class="col-md-8">';
                $html_patentes .= '<input type="text" class="form-control" name="old_patents[]" maxlength="255" placeholder="'.lang('patent_plate').'" value="'.$patente->patent.'" autocomplete="off">';
            $html_patentes .= '</div>';
            $html_patentes .= '<input type="hidden" name="patents_id[]" value="'. $patente->id .'">';
            $html_patentes .= '<div class="col-md-1">';
                $html_patentes .= '<button type="button" class="btn btn-sm btn-danger" onclick="removeOption($(this));"><i class="fa fa-minus"></i></button>';
            $html_patentes .= '</div>';
        $html_patentes .= '</div>';
    }
    
    echo $html_patentes;

}
?>

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

    function addOptions(){
        $('#feeders-form #grupo_patentes').append($("<div/>").addClass('form-group patente').html($('#feeders-form #modelo').html()));
        $('#feeders-form .patente').last().find('input').attr('data-rule-required', true);
        $('#feeders-form .patente').last().find('input').attr('data-msg-required', '<?php echo lang("field_required"); ?>');
        $('#feeders-form .patente').last().find('input').maxlength({
            //alwaysShow: true,
            threshold: 245,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            appendToParent:true
        });
    }
    
    function removeOptions(){
        $('#feeders-form .patente').last().remove();
    }
    function removeOption(element){
        element.closest('#feeders-form .patente').remove();
    }
</script>