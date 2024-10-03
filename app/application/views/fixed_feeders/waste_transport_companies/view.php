<?php echo form_open("", array("id" => "feeders-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

    <div class="form-group">
        <label for="company_name" class="col-md-3"><?php echo lang('company_name_2'); ?></label>
        <div class="col-md-9">
            <?php echo $model_info->company_name ? $model_info->company_name : "-"; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="company_rut" class="col-md-3"><?php echo lang('company_rut'); ?></label>
        <div class="col-md-9">
            <?php echo $model_info->company_rut ? $model_info->company_rut : "-"; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="company_registration_code" class="col-md-3"><?php echo lang('company_registration_code'); ?></label>
        <div class="col-md-9">
            <?php echo $model_info->company_registration_code ? $model_info->company_registration_code : "-"; ?>
        </div>
    </div>

    <?php 
        $patentes = $this->Patents_model->get_all_where(array(
            'id_waste_transport_company' => $model_info->id,
            'deleted' => 0
        ))->result();

        $html_patentes = !$patentes ? "-" : "";
        foreach($patentes as $patente){
           $html_patentes .= '&bull; ' . $patente->patent . '<br>';
        }
    ?>
    <div class="form-group">
        <label for="patent" class="col-md-3"><?php echo lang('patent_plate'); ?></label>
        <div class="col-md-9">
            <?php echo $html_patentes; ?>
        </div>
    </div>

	<div class="form-group">
        <label for="created_by" class="col-md-3"><?php echo lang('created_by'); ?></label>
        <div class="col-md-9">
            <?php
			echo $created_by;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_by" class="col-md-3"><?php echo lang('modified_by'); ?></label>
        <div class="col-md-9">
            <?php
            echo $modified_by;
            ?>
        </div>
    </div>

	<div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
			echo time_date_zone_format($model_info->created, $id_project);
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->modified)?time_date_zone_format($model_info->modified, $id_project):'-';
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