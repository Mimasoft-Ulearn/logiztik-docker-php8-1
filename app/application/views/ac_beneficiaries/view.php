<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('nationality'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->id_nacionalidad; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('sex'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->sexo; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('birthdate'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->fecha_nacimiento; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('organizational_email'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->correo_organizativo; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('society'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->sociedad; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('society_desc'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $sociedad_desc; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('cost_center_desc'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->centro_de_costo_desc; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('position_desc'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->posicion; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('division_desc'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->division_desc; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('subdivision_desc'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->subdivision_desc; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('contract_start_date'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->fecha_inicio_contrato; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('contract_end_date'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->fecha_fin_contrato; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('status'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->estado; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('contract_type'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->tipo_contrato; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('division2_desc'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->division2_desc; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('boss_position_desc'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->posicion_jefe_desc; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('civil_status'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->estado_civil; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('personnel_area'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->area_de_personal; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('department_desc'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->departamento_desc; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('jobcode_desc'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->job_code_desc; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('fullname'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_completo; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('nationality'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nacionalidad; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('commune'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->comuna; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('province'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->provincia; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('disability'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->discapacidad; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('tea_law'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $ley_tea; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="<?php echo $label_column; ?>"><?php echo lang('native_people'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $pueblos_originarios; ?>
        </div>
    </div>
	
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

</script> 