<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="fecha" class="<?php echo $label_column; ?>"><?php echo lang('date'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "fecha",
            "name" => "fecha",
            "value" => $model_info->fecha,
            "class" => "form-control datepicker",
			"placeholder" => "YYYY-MM-DD",
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="tipo_actividad" class="<?php echo $label_column; ?>"><?php echo lang("activity"); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("tipo_actividad", $activity_type_dropdown, array($model_info->id_feeder_tipo_actividad), "id='tipo_actividad' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div class="form-group">
    <label for="sociedad" class="<?php echo $label_column; ?>"><?php echo lang("society"); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("sociedad", $society_dropdown, array($model_info->id_feeder_sociedad), "id='sociedad' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div id="attendees_group">
	<div class="form-group">
		<label for="asistentes" class="col-md-3"><?php echo lang('attendees'); ?></label>
		<div class="col-md-9">
			<?php
				$obligatorio = "data-rule-required='true' data-msg-required='" . lang('field_required') . "'";
				echo form_multiselect(
					"asistentes[]", 
					$opciones_asistentes, 
					$opciones_asistentes_seleccionados,
					"id='asistentes' class='select2 validate-hidden' $obligatorio"
				);
			?>
		</div>
	</div>
</div>

<div class="form-group">
    <label for="cant_hijos_beneficiados" class="<?php echo $label_column; ?>"><?php echo lang('benefited_sons_daughters'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "cant_hijos_beneficiados",
            "name" => "cant_hijos_beneficiados",
            "value" => $model_info->cant_hijos_beneficiados,
            "class" => "form-control",
            "placeholder" => lang('benefited_sons_daughters'),
			"type" => "number",
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"data-rule-number" => true,
			"data-msg-number" => lang("enter_a_number"),
			"autocomplete"=> "off",
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="inversion" class="<?php echo $label_column; ?>"><?php echo lang('ac_inversion'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "inversion",
            "name" => "inversion",
            "value" => $model_info->inversion,
            "class" => "form-control",
            "placeholder" => lang('ac_inversion'),
            //"autofocus" => true,
			"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
			"data-msg-regex" => lang("number_or_decimal_required"),
            //"data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete"=> "off",
            "maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
  <label for="registro" class="col-md-3"><?php echo lang('ac_record'); ?></label>
    <div id="dropzone_registro" class="col-md-9">
        <?php
		
			if(!$model_info->registro){
				
				echo $this->load->view("includes/form_file_uploader", array(
					"upload_url" =>get_uri("AC_Activities/upload_file"),
					"validation_url" =>get_uri("AC_Activities/validate_file"),
					"html_name" => "registro",
					//"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
					"obligatorio" => "",
					"id_campo" => "registro",
					
				), true);
				
			} else {
				
				$html = '<div id="table_delete_registro">';
				
				$html .= '<div class="col-md-8">';
				$html .= remove_file_prefix($model_info->registro);
				$html .= '</div>';
				
				$html .= '<div class="col-md-4">';
				$html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
				$html .= '<tbody><tr><td class="option text-center">';
				$html .= anchor(get_uri("AC_Activities/download_file/".$model_info->id."/registro"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
				$html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => "", "data-id" => $model_info->id, "data-campo" => "registro", "data-action-url" => get_uri("AC_Activities/delete_file"), "data-action" => "delete-fileConfirmation"));
				$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';	
				$html .= '<input type="hidden" name="'.$name.'_unchange" value="1" />';			
				$html .= '</td>';
				$html .= '</tr>';
				$html .= '</thead>';
				$html .= '</table>';
				$html .= '</div>';
				
				$html .= '</div>';
				
				echo $html;
				
			}
			
        ?>
    </div>
</div>

<div class="form-group">
	<label for="observaciones" class="<?php echo $label_column; ?>"><?php echo lang('ac_observations'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_textarea(array(
			"id" => "observaciones",
			"name" => "observaciones",
			"value" => $model_info->observaciones,
			"class" => "form-control",
			"placeholder" => lang('ac_observations'),
			"style" => "height:150px;",
			//"data-rule-required" => true,
			//"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "2000"
		));
		?>
	</div>
</div>

<div class="form-group">
  <label for="otros_archivos" class="col-md-3"><?php echo lang('ac_other_files'); ?></label>
    <div id="dropzone_otros_archivos" class="col-md-9">
        <?php
		
			if(!$model_info->otros_archivos){
				
				echo $this->load->view("includes/form_file_uploader", array(
					"upload_url" =>get_uri("AC_Activities/upload_file"),
					"validation_url" =>get_uri("AC_Activities/validate_file"),
					"html_name" => "otros_archivos",
					//"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
					"obligatorio" => "",
					"id_campo" => "otros_archivos",
					
				), true);
				
			} else {
				
				$html = '<div id="table_delete_otros_archivos">';
				$html .= '<div class="col-md-8">';
				$html .= remove_file_prefix($model_info->otros_archivos);
				$html .= '</div>';
				
				$html .= '<div class="col-md-4">';
				$html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
				$html .= '<tbody><tr><td class="option text-center">';
				$html .= anchor(get_uri("AC_Activities/download_file/".$model_info->id."/otros_archivos"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
				$html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => "", "data-id" => $model_info->id, "data-campo" => "otros_archivos", "data-action-url" => get_uri("AC_Activities/delete_file"), "data-action" => "delete-fileConfirmation"));
				$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';	
				$html .= '<input type="hidden" name="'.$name.'_unchange" value="1" />';			
				$html .= '</td>';
				$html .= '</tr>';
				$html .= '</thead>';
				$html .= '</table>';
				$html .= '</div>';
				
				$html .= '</div>';
				
				echo $html;
				
			}
			
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        
		$('[data-toggle="tooltip"]').tooltip();
		$('#activities-form .select2').select2();
		setDatePicker("#activities-form .datepicker");
				
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

		// Llenar select Asistentes
		$('#sociedad').change(function(){
			let id_sociedad = $(this).val();
			
			if(id_sociedad){
				$.ajax({
					url: '<?php echo_uri("AC_Activities/get_attendees"); ?>',
					type: 'post',
					data: {id_sociedad: id_sociedad},
					success: function(respuesta){
						$("#attendees_group").html(respuesta);
						$("#asistentes").select2();
					}
				});
			}else{
				$("#asistentes").select2("val", "").empty();
			}
		});
		
    });
</script>