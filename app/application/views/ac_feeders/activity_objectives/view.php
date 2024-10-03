<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

    <div class="form-group">
        <label for="tipo_actividad" class="<?php echo $label_column; ?>"><?php echo lang('ac_type_of_activity'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $tipo_actividad; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="tipo_actividad" class="<?php echo $label_column; ?>"><?php echo lang('activity'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $actividad; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="tipo_actividad" class="<?php echo $label_column; ?>"><?php echo lang('ac_chart'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $grafico; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="tipo_actividad" class="<?php echo $label_column; ?>"><?php echo lang('objectives'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $objetivos; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="created" class="col-md-3"><?php echo lang('created_by'); ?></label>
        <div class="col-md-9">
            <?php
            echo $creado_por;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified" class="col-md-3"><?php echo lang('modified_by'); ?></label>
        <div class="col-md-9">
            <?php
            echo $modificado_por;
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

</script> 