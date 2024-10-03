<?php echo form_open("", array("id" => "environmental_records-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
	
   
    
    <?php if ($tipo_matriz == "rca"){ ?>

        <div class="form-group">
            <label for="date_filed" class="col-md-3"><?php echo lang('compromise_number'); ?></label>
            <div class="col-md-9">
                <?php echo $model_info->numero_compromiso; ?>
            </div>
        </div>
    
        <div class="form-group">
            <label for="name" class="col-md-3"><?php echo lang('name'); ?></label>
            <div class="col-md-9">
                <?php echo $model_info->nombre_compromiso; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="name" class="col-md-3"><?php echo lang('phases'); ?></label>
            <div class="col-md-9">
                <?php
                    echo $html_fases;
                ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="name" class="col-md-3"><?php echo lang('reportability'); ?></label>
            <div class="col-md-9">
                <?php echo ($model_info->reportabilidad == 1) ? '<i class="fa fa-check" aria-hidden="true"></i>' : '<i class="fa fa-times" aria-hidden="true"></i>'; ?>
            </div>
        </div>
    
    <?php } ?>
    
    <?php if ($tipo_matriz == "reportable"){ ?>

        <div class="form-group">
            <label for="n_activity" class="col-md-3"><?php echo lang('n_activity'); ?></label>
            <div class="col-md-9">
                <?php echo $model_info->numero_actividad; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="environmental_management_instrument" class="col-md-3"><?php echo lang('environmental_management_instrument'); ?></label>
            <div class="col-md-9">
                <?php echo lang($model_info->instrumento_gestion_ambiental); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="phase" class="col-md-3"><?php echo lang('phase_reportable'); ?></label>
            <div class="col-md-9">
                <?php echo $model_info->etapa ? lang($model_info->etapa) : '-'; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="compliance_type" class="col-md-3"><?php echo lang('compliance_type'); ?></label>
            <div class=" col-md-9">
                <?php echo lang($model_info->tipo_cumplimiento); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="environmental_topic" class="col-md-3"><?php echo lang('environmental_topic'); ?></label>
            <div class=" col-md-9">
                <?php echo lang($model_info->tema_ambiental); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="impact_on_the_environment_due_to_non_compliance" class="col-md-3"><?php echo lang('impact_on_the_environment_due_to_non_compliance'); ?></label>
            <div class=" col-md-9">
                <?php echo lang($model_info->afectacion_medio_por_incumplimiento); ?>
            </div>
        </div>
    
        <div class="form-group">
            <label for="action_type" class="col-md-3"><?php echo lang('action_type'); ?></label>
            <div class="col-md-9">
                <?php echo $model_info->tipo_accion; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="responsible_area" class="col-md-3"><?php echo lang('responsible_area'); ?></label>
            <div class="col-md-9">
                <?php echo $html_responsible_area; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="commitment_description" class="col-md-3"><?php echo lang('environmental_commitment'); ?></label>
            <div class="col-md-9">
                <?php echo $model_info->descripcion_compromiso; ?>
            </div>
        </div>
    
    <?php } ?>
    
	<?php 
        
        $html = '';
        foreach($campos_compromiso as $campo){
			
			if($campo["id_tipo_campo"] == 11 || $campo["id_tipo_campo"] == 12){

				$html .= '<div class="form-group">';
					$html .= '<div class="col-md-12">';
					$html .= $Upload_compromises_controller->get_field_value($campo["id_campo"], $model_info->id, $tipo_matriz);
					$html .= '</div>';
				$html .= '</div>';
				
			} else {
				
				//echo $campo["nombre_campo"]."<br>";
				$html .= '<div class="form-group">';
					$html .= '<label for="'.$campo["html_name"].'" class="col-md-3">'.$campo["nombre_campo"].'</label>';
					$html .= '<div class="col-md-9">';
					$html .= $Upload_compromises_controller->get_field_value($campo["id_campo"], $model_info->id, $tipo_matriz);
					$html .= '</div>';
				$html .= '</div>';
				
			}
 
        }
        
        echo $html;
    
    ?>
    
    <?php if ($tipo_matriz == "rca"){ ?>

        <div class="form-group">
        <label for="compliance_action_control" class="col-md-3"><?php echo lang('compliance_action_control'); ?></label>
            <div class=" col-md-9">
                <?php
                    echo $model_info->accion_cumplimiento_control;
                ?>
            </div>
        </div>
        
        <div class="form-group">
        <label for="execution_frequency" class="col-md-3"><?php echo lang('execution_frequency'); ?></label>
            <div class=" col-md-9">
                <?php
                    echo $model_info->frecuencia_ejecucion;
                ?>
            </div>
        </div>
    
    <?php } ?>
    
    <?php if ($tipo_matriz == "reportable"){ ?>

        <div class="form-group">
            <label for="planning" class="col-md-3"><?php echo lang('planning'); ?></label>
            <div class="col-md-9">
                <?php
                    echo $html_planes;
                ?>
            </div>
        </div>
        
    <?php } ?>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->modified)?$model_info->modified:'-';
            ?>
        </div>
    </div>
    
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

	$(document).ready(function(){
		
		$('[data-toggle="tooltip"]').tooltip();
		$('#environmental_records-form .select2').select2();
		setDatePicker("#environmental_records-form .datepicker");
		setTimePicker('#environmental_records-form .timepicker');
		
	});

</script>    