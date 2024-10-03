<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('date'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo date(get_setting_client_mimasoft($model_info->id_cliente, "date_format"), strtotime($model_info->fecha));; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="tipo_actividad" class="<?php echo $label_column; ?>"><?php echo lang("activity"); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $tipo_actividad; ?>
        </div>
    </div>
        
    <div class="form-group">
        <label for="sociedad" class="<?php echo $label_column; ?>"><?php echo lang('society'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $sociedad; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="asistentes" class="<?php echo $label_column; ?>"><?php echo lang('attendees'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $asistentes; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="asistentes" class="<?php echo $label_column; ?>"><?php echo lang('benefited_sons_daughters'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->cant_hijos_beneficiados ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="asistentes" class="<?php echo $label_column; ?>"><?php echo lang('ac_inversion'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo to_number_client_format($model_info->inversion, $id_cliente); ?>
        </div>
    </div>
    
    <div class="form-group">
      <label for="registro" class="col-md-3"><?php echo lang('ac_record'); ?></label>
        <div id="dropzone_registro" class="col-md-9">
            <?php
            
                if($model_info->registro){
                    $html = '<div class="col-md-8">';
                    $html .= remove_file_prefix($model_info->registro);
                    $html .= '</div>';
                    $html .= '<div class="col-md-4">';
                    $html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
                    $html .= '<tbody><tr><td class="option text-center">';
                    $html .= anchor(get_uri("AC_Activities/download_file/".$model_info->id."/registro"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));	
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '</table>';
                    $html .= '</div>';
                } else {
					$html = "-";
				} 

				echo $html;
                
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="observaciones" class="<?php echo $label_column; ?>"><?php echo lang('ac_observations'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo ($model_info->observaciones) ? $model_info->observaciones : "-"; ?>
        </div>
    </div>
    
    <div class="form-group">
      <label for="otros_archivos" class="col-md-3"><?php echo lang('ac_other_files'); ?></label>
        <div id="dropzone_otros_archivos" class="col-md-9">
            <?php
            
                if($model_info->otros_archivos){
					$html = '<div class="col-md-8">';
					$html .= remove_file_prefix($model_info->otros_archivos);
					$html .= '</div>';
					$html .= '<div class="col-md-4">';
					$html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("AC_Activities/download_file/".$model_info->id."/otros_archivos"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));		
					$html .= '</td>';
					$html .= '</tr>';
					$html .= '</thead>';
					$html .= '</table>';
					$html .= '</div>';
                } else {
					$html = "-";    
                }
				
				echo $html;
                
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->created) ? format_to_datetime_clients($model_info->id_cliente, $model_info->created) : '-';
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->modified) ? format_to_datetime_clients($model_info->id_cliente, $model_info->modified) : '-';
            ?>
        </div>
    </div>
 
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

	$(document).ready(function () {
		
		
	});

</script> 