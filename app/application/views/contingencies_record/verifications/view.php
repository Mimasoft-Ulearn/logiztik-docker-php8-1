<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

<div class="form-group">
    <label for="event" class="<?php echo $label_column; ?>"><?php echo lang('event'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo htmlspecialchars($evento, ENT_QUOTES); ?>
    </div>
</div>

<div class="form-group">
    <label for="descripcion_verificacion" class="<?php echo $label_column; ?>"><?php echo lang('verification_description'); ?></label>
    <div class="<?php echo $field_column; ?>">
    <?php echo htmlspecialchars($descripcion_verificacion, ENT_QUOTES); ?>
    </div>
</div>


<div class="form-group">
    <label for="responsable_verificacion" class="<?php echo $label_column; ?>"><?php echo lang('responsible_for_verification'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo $responsable_verificacion; ?>
    </div>
</div>

<div class="form-group">
    <label for="fecha_verificacion" class="<?php echo $label_column; ?>"><?php echo lang('verification_date'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo ($fecha_verificacion) ? date(get_setting_client_mimasoft($id_cliente, "date_format"), strtotime($fecha_verificacion)) : "-"; ?>
    </div>
</div>

<!-- 
<div class="form-group">
    <label for="evidencia_correccion" class="<?php // echo $label_column; ?>"><?php // echo lang('corrective_action_evidence'); ?></label>
    <div class="<?php // echo $field_column; ?>">
        <?php
        /* foreach($archivo_evidencias_accion_correctiva as $archivo){
            $html = '<div>';
            $html .= '<div class="col-md-8">';
            $html .= remove_file_prefix($archivo);
            $html .= '</div>';
            
            $html .= '<div class="col-md-4">';
            $html .= '<table class="table_delete"><thead><tr><th></th></tr></thead>';
            $html .= '<tbody><tr><td class="option text-center">';
            $html .= anchor(get_uri("contingencies_record/download_correction_evidence_file/".$model_info->id_contingencia_evento."/".$archivo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
            $html .='</td></tr></tbody></table></div></div>';
            echo $html; 
        } */ ?>
    </div>
</div>
 -->