<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

<div class="form-group">
    <label for="fecha_identificacion" class="<?php echo $label_column; ?>"><?php echo lang('identification_date'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo ($fecha_identificacion) ? date(get_setting_client_mimasoft($id_cliente, "date_format"), strtotime($fecha_identificacion)) : "-"; ?>
    </div>
</div>

<div class="form-group">
    <label for="n_sacpa" class="<?php echo $label_column; ?>"><?php echo lang('n_sacpa'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo htmlspecialchars($n_sacpa, ENT_QUOTES); ?>
    </div>
</div>

<div class="form-group">

    <?php $label_management = lang('management_2'); ?>

    <label for="gerencia" class="<?php echo $label_column; ?>"><?php echo $label_management; ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo lang($gerencia); ?>
    </div>
</div>

<div class="form-group">
    <label for="instrumento_gestion_ambiental" class="<?php echo $label_column; ?>"><?php echo lang('environmental_management_instrument'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo lang($instrumento_gestion_ambiental); ?>
    </div>
</div>

<div class="form-group">
    <label for="clausula_incumplimiento" class="<?php echo $label_column; ?>"><?php echo lang('non_compliance_clause'); ?></label>
    <div class="<?php echo $field_column; ?>">
    <?php echo htmlspecialchars($clausula_incumplimiento, ENT_QUOTES); ?>
    </div>
</div>

<div class="form-group">
    <label for="tipo_evento" class="<?php echo $label_column; ?>"><?php echo lang('event_type'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo lang($tipo_evento); ?>
    </div>
</div>

<div class="form-group">
    <label for="tipo_afectacion" class="<?php echo $label_column; ?>"><?php echo lang('affectation_type'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo lang($tipo_afectacion); ?>
    </div>
</div>

<div class="form-group">
    <label for="descripcion_no_conformidad" class="<?php echo $label_column; ?>"><?php echo lang('description_of_non_conformity_and_or_finding'); ?></label>
    <div class="<?php echo $field_column; ?>">
    <?php echo htmlspecialchars($descripcion_no_conformidad, ENT_QUOTES); ?>
    </div>
</div>

<div class="form-group">
    <label for="evidencia_evento" class="<?php echo $label_column; ?>"><?php echo lang('event_evidence'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        foreach($archivos_evidencia_evento as $archivo){
            $html = '<div>';
            $html .= '<div class="col-md-8">';
            $html .= remove_file_prefix($archivo);
            $html .= '</div>';
            
            $html .= '<div class="col-md-4">';
            $html .= '<table class="table_delete"><thead><tr><th></th></tr></thead>';
            $html .= '<tbody><tr><td class="option text-center">';
            $html .= anchor(get_uri("contingencies_record/download_event_evidence_file/".$model_info->id."/".$archivo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
            $html .='</td></tr></tbody></table></div></div>';
            echo $html; 
        } ?>
    </div>
</div>
