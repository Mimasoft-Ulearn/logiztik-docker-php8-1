<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("client", $clientes, array($model_info->id_client), "id='client' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div id="proyectos_group">
    <div class="form-group">
        <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("project", $proyectos, array($model_info->id_project), "id='project' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>

<div id="category_group">
    <div class="form-group">
        <label for="category" class="<?php echo $label_column; ?>"><?php echo lang('category'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("category", $categories_dropdown, $model_info->id_category, "id='category' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="code_list_a" class="<?php echo $label_column; ?>"><?php echo lang('code_list_a'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "code_list_a",
            "name" => "code_list_a",
            "value" => $model_info->code_list_a,
            "class" => "form-control",
            "placeholder" => lang('code_list_a'),
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
    <label for="code_lists_i_ii_iii" class="<?php echo $label_column; ?>"><?php echo lang('code_lists_i_ii_iii'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "code_lists_i_ii_iii",
            "name" => "code_lists_i_ii_iii",
            "value" => $model_info->code_lists_i_ii_iii,
            "class" => "form-control",
            "placeholder" => lang('code_lists_i_ii_iii'),
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
    <?php
	
		$html = '';
        $html .= '<div class="form-group">';
            $html .= '<label for="dangerous_characteristic" class="col-md-3">'.lang('dangerous_characteristic').'</label>';
            $html .= '<div class="col-md-9">';
			$html .= form_multiselect(
						"dangerous_characteristic[]", 
						$available_dangerous_characteristic, 
						$sidrep_dangerous_characteristic, 
						"id='dangerous_characteristic' class='multiple validate-hidden' multiple='multiple'"
						//NULL,
						//$array_stakeholders_usados_seguimiento
					);
			$html .= '</div>';
		$html .= '</div>';

        echo $html;
    ?>
</div>

<div class="form-group">
    <label for="physical_status" class="<?php echo $label_column; ?>"><?php echo lang('physical_status'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("physical_status", $physical_status_dropdown, array($model_info->physical_status), "id='physical_status' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>
 
<style>
    .multiselect-header{
        text-align: center;
        padding: 3px;
        background: #7988a2;
        color: #fff;
    }
</style>

<script type="text/javascript">
$(document).ready(function () {
	$('[data-toggle="tooltip"]').tooltip();
	$('#sidrep_codes-form .select2').select2();
	
	$('input[type="text"][maxlength]').maxlength({
		//alwaysShow: true,
		threshold: 245,
		warningClass: "label label-success",
		limitReachedClass: "label label-danger",
		appendToParent:true
	});

    $('#client').on('change', function(){
		
		var id_client = $(this).val();
        select2LoadingStatusOn($('#project'));
		select2LoadingStatusOn($('#category'));

        $.ajax({
            url:  '<?php echo_uri("sidrep_codes/get_projects_of_client") ?>',
            type:  'post',
            data: {id_client:id_client},
            //dataType:'json',
            success: function(respuesta){
                
                $('#proyectos_group').html(respuesta);
                $('#project').select2();
            }
        });
		
		$.ajax({
			url:  '<?php echo_uri("sidrep_codes/get_categories_of_client") ?>',
			type:  'post',
			data: {id_client: id_client},
			//dataType:'json',
			success: function(respuesta){
				$('#category_group').html(respuesta);
				$('#category_group .select2').select2();
			}
		});
		
	});

    $('#dangerous_characteristic').multiSelect({
		selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
		selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
		keepOrder: true,
		afterSelect: function(value){
			$('#dangerous_characteristic option[value="'+value+'"]').remove();
			$('#dangerous_characteristic').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
		},
		afterDeselect: function(value){ 
			$('#dangerous_characteristic option[value="'+value+'"]').removeAttr('selected'); 
		}
	 });
	
});
</script>