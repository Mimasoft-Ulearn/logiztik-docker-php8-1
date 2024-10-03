<input type="hidden" name="id_contingencia" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="fecha_identificacion" class="<?php echo $label_column; ?>"><?php echo lang('identification_date'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "fecha_identificacion",
            "name" => "fecha_identificacion",
            "value" => $model_info->fecha_identificacion,
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
    <label for="n_sacpa" class="<?php echo $label_column; ?>"><?php echo lang('n_sacpa'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "n_sacpa",
            "name" => "n_sacpa",
            "value" => $model_info->n_sacpa,
            "class" => "form-control",
			"placeholder" => lang('n_sacpa'),
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

    <?php $label_management = lang('management_2'); ?>

    <label for="gerencia" class="<?php echo $label_column; ?>"><?php echo $label_management; ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("gerencia", $gerencia_dropdown, $gerencia, "id='gerencia' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group">
    <label for="instrumento_gestion_ambiental" class="<?php echo $label_column; ?>"><?php echo lang('environmental_management_instrument'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("instrumento_gestion_ambiental", $instrumento_gestion_ambiental_dropdown, $instrumento_gestion_ambiental, "id='instrumento_gestion_ambiental' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group">
    <label for="clausula_incumplimiento" class="<?php echo $label_column; ?>"><?php echo lang('non_compliance_clause'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "clausula_incumplimiento",
            "name" => "clausula_incumplimiento",
            "value" => $model_info->clausula_incumplimiento,
            "class" => "form-control",
			"placeholder" => lang('non_compliance_clause'),
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
    <label for="tipo_evento" class="<?php echo $label_column; ?>"><?php echo lang('event_type'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("tipo_evento", $tipo_evento_dropdown, $tipo_evento, "id='tipo_evento' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group">
    <label for="tipo_afectacion" class="<?php echo $label_column; ?>"><?php echo lang('affectation_type'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("tipo_afectacion", $tipo_afectacion_dropdown, $tipo_afectacion, "id='tipo_afectacion' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group">
    <label for="descripcion_no_conformidad" class="<?php echo $label_column; ?>"><?php echo lang('description_of_non_conformity_and_or_finding'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_textarea(array(
            "id" => "descripcion_no_conformidad",
            "name" => "descripcion_no_conformidad",
            "value" => $model_info->descripcion_no_conformidad,
            "class" => "form-control",
			"placeholder" => lang('description_of_non_conformity_and_or_finding'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "2000"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="evidencias_evento" class="<?php echo $label_column; ?>"><?php echo lang('event_evidence'); ?></label>
    <div id="dropzone_evidencias_evento" class="<?php echo $field_column; ?>">
        <?php

            echo $this->load->view("includes/multiple_files_uploader", array(
                "upload_url" =>get_uri("contingencies_record/upload_multiple_file"),
                "validation_url" =>get_uri("contingencies_record/validate_file"),
                "html_name" => "evidencias_evento",
                //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                //"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required").'"',
                "obligatorio" => "",
                "id_campo" => "evidencias_evento"
                
            ), true);
            
             if(count($archivo_evidencias_evento)){
					
                foreach($archivo_evidencias_evento as $key => $archivo){
                    //echo $archivo["nombre"]."<br>";
                    $html = '<div id="table_delete_evidencia_evento_'.$model_info->id.'_'.$key.'">';
                    $html .= '<div class="col-md-8">';
                    $html .= remove_file_prefix($archivo);
                    $html .= '</div>';
                    
                    $html .= '<div class="col-md-4">';
                    $html .= '<table class="table_delete"><thead><tr><th></th></tr></thead>';
                    $html .= '<tbody><tr><td class="option text-center">';
                    $html .= anchor(get_uri("contingencies_record/download_event_evidence_file/".$model_info->id."/".$archivo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));

                    $html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => "", "data-filename" => $archivo, "data-action-url" => get_uri("contingencies_record/delete_event_evidence_file"), "data-action" => "delete-fileConfirmation", "data-id_contingencia" => $model_info->id, "data-key_contingencia" => $key));
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
		$('#event-form .select2').select2();

        setDatePicker($('#fecha_identificacion'));

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