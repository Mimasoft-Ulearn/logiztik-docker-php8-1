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
    <label for="ler_code" class="<?php echo $label_column; ?>"><?php echo lang('ler_code'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "ler_code",
            "name" => "ler_code",
            "value" => $model_info->ler_code,
            "class" => "form-control",
            "placeholder" => lang('ler_code'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>
 


<script type="text/javascript">
$(document).ready(function () {
	$('[data-toggle="tooltip"]').tooltip();
	$('#sinader_code-form .select2').select2();
	
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
            url:  '<?php echo_uri("sinader_code/get_projects_of_client") ?>',
            type:  'post',
            data: {id_client:id_client},
            //dataType:'json',
            success: function(respuesta){
                
                $('#proyectos_group').html(respuesta);
                $('#project').select2();
            }
        });

		$.ajax({
			url:  '<?php echo_uri("sinader_code/get_categories_of_client") ?>',
			type:  'post',
			data: {id_client: id_client},
			//dataType:'json',
			success: function(respuesta){
				$('#category_group').html(respuesta);
				$('#category_group .select2').select2();
			}
		});
		
	});
	
});
</script>