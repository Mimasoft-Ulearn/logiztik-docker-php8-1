<input type="hidden" name="id_correccion" value="<?php echo $model_info->id; ?>" />
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
    <label for="descripcion_accion_correctiva" class="<?php echo $label_column; ?>"><?php echo lang('description_of_corrective_or_preventive_action'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "descripcion_accion_correctiva",
            "name" => "descripcion_accion_correctiva",
            "value" => $model_info->descripcion_accion_correctiva,
            "class" => "form-control",
			"placeholder" => lang('description_of_corrective_or_preventive_action'),
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
    <label for="responsable_correccion" class="<?php echo $label_column; ?>"><?php echo lang('responsible_for_correction'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("responsable_correccion", $responsable_correccion_dropdown, $responsable_correccion, "id='responsable_correccion' class='select2 validate-hidden' data-rule-required='false', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>


<div class="form-group">
    <label for="fecha_correccion" class="<?php echo $label_column; ?>"><?php echo lang('correction_date'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "fecha_correccion",
            "name" => "fecha_correccion",
            "value" => $model_info->fecha_correccion,
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

<div class="form-group">
    <label for="evidencias_accion_correctiva" class="<?php echo $label_column; ?>"><?php echo lang('corrective_action_evidence'); ?></label>
    <div id="dropzone_evidencias_accion_correctiva" class="<?php echo $field_column; ?>">
        <?php

            echo $this->load->view("includes/multiple_files_uploader", array(
                "upload_url" =>get_uri("contingencies_record/upload_multiple_file"),
                "validation_url" =>get_uri("contingencies_record/validate_file"),
                "html_name" => "evidencias_accion_correctiva",
                //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                //"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required").'"',
                "obligatorio" => "",
                "id_campo" => "evidencias_accion_correctiva"
                
            ), true);
            
             if(count($archivo_evidencias_accion_correctiva)){
					
                foreach($archivo_evidencias_accion_correctiva as $key => $archivo){
                    //echo $archivo["nombre"]."<br>";
                    $html = '<div id="table_delete_evidencia_accion_correctiva_'.$model_info->id.'_'.$key.'">';
                    $html .= '<div class="col-md-8">';
                    $html .= remove_file_prefix($archivo);
                    $html .= '</div>';
                    
                    $html .= '<div class="col-md-4">';
                    $html .= '<table class="table_delete"><thead><tr><th></th></tr></thead>';
                    $html .= '<tbody><tr><td class="option text-center">';
                    $html .= anchor(get_uri("contingencies_record/download_correction_evidence_file/".$model_info->id_contingencia_evento."/".$archivo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));

                    $html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => "", "data-filename" => $archivo, "data-action-url" => get_uri("contingencies_record/delete_correction_evidence_file"), "data-action" => "delete-fileConfirmation", "data-id_correccion" => $model_info->id, "data-key_correccion" => $key));
                    $html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';	
                    $html .= '<input type="hidden" name="'.$name.'_unchange" value="1" />';			
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '</tbody>';
                    $html .= '</table>';
                    $html .= '</div>';
                    $html .= '</div>';
                    
                    echo $html;
                    
                }
                
            }
			
			// echo $archivos_evidencia_evento;

        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        
		$('[data-toggle="tooltip"]').tooltip();
		$('#correction-form .select2').select2();

        setDatePicker($('#fecha_correccion'));

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

        <?php if(count($eventos_en_correcciones) > 0){
            foreach($eventos_en_correcciones as $evento){ 
                if($model_info->id_contingencia_evento != $evento){ ?>
                $('#evento option[value="' + <?php echo $evento; ?> + '"]').attr("disabled", "disabled");
        <?php   }
            }
        } ?>
        
    });
</script>