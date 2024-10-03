<?php echo form_open("", array("id" => "environmental_records-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
<?php 
$datos = json_decode($model_info->datos, true);
$id_categoria = $datos["id_categoria"]; 
$categoria_original = $this->Categories_model->get_one_where(array('id' => $id_categoria, "deleted" => 0));

$categoria_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));

if($categoria_alias->alias){
	$nombre_categoria = $categoria_alias->alias;
}else{
	$nombre_categoria = $categoria_original->nombre;
}
$fecha_registro = $datos["fecha"];
?>
<div class="form-group">
    <label for="storage_date" class="col-md-3"><?php echo $label_storage_date; ?></label>
    <div class="col-md-9">
        <?php
			echo get_date_format($fecha_registro, $id_proyecto);
        ?>
    </div>
</div>

<?php if($form_info->flujo == "Residuo"){ ?>
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('month'); ?></label>
        <div class="col-md-9">
            <?php
            $month = isset($datos['month']) ? number_to_month($datos['month']) : '-';
            echo $month;
            ?>
        </div>
    </div>    
<?php } ?>


<div class="form-group">
    <label for="name" class="col-md-3"><?php echo lang('category'); ?></label>
    <div class="col-md-9">
        <?php
        echo $nombre_categoria;
        ?>
    </div>
</div>
	
<div class="form-group">
	<label for="name" class="col-md-3"><?php echo /*$label_unidad*/lang("quantity"); ?></label>
	<div class="col-md-9">
		<?php
		echo to_number_project_format($unidad_residuo, $id_proyecto) . " " . $nombre_unidad;
		?>
	</div>
</div>

<!-- <div class="form-group">
    <label for="sucursal" class="col-md-3"><?php echo lang("branch_office"); ?></label>
    <div class="col-md-9">
        <?php
        echo $sucursal;
        ?>
    </div>
</div>
 -->
<?php if($form_info->flujo == "Residuo"){ ?>

    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('type_of_treatment'); ?></label>
        <div class="col-md-9">
            <?php
            echo $tipo_tratamiento;
            ?>
        </div>
    </div>

<?php } ?>


<?php if($form_info->flujo == "Consumo"){ ?>

	<div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('type'); ?></label>
        <div class="col-md-9">
            <?php
            echo $type_of_origin;
            ?>
        </div>
    </div>

<?php } ?>

<?php if($form_info->flujo == "No Aplica"){ ?>
	
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('type'); ?></label>
        <div class="col-md-9">
            <?php
            echo $default_type;
            ?>
        </div>
    </div>
    
<?php } ?>

<?php

    $html = '';
    foreach($campos as $campo){
        
        $html .= '<div class="form-group">';
        if(($campo->id_tipo_campo == 12)||($campo->id_tipo_campo == 11)){// si divisor
            $html .= '<div class="col-md-12">';
            $html .= '<div style="word-wrap: break-word;">';
            $html .= $campo->default_value;
            $html .= '</div>';
            $html .= '</div>';
        }else{
            $html .= '<label for="'.$campo->html_name.'" class="col-md-3">'.$campo->nombre.'</label>';
            $html .= '<div class="col-md-9">';
            $html .= $Environmental_records_controller->get_field_value($campo->id, $model_info->id, $id_proyecto);
            $html .= '</div>';
        }
        $html .= '</div>';
        
    }

    echo $html;
    
?>

<?php if($form_info->flujo == "Residuo"){ ?>
    
    <?php if($project_info->in_rm){ ?>
    <div class="form-group">
		<label for="carrier_rut" class="col-md-3"><?php echo lang('carrier_rut'); ?></label>
		<div class="col-md-9">
			<?php
                echo $datos["carrier_rut"] ? $datos["carrier_rut"] : "-";
			?>
		</div>
	</div>

    <div class="form-group">
		<label for="patent" class="col-md-3"><?php echo lang('patent'); ?></label>
		<div class="col-md-9">
			<?php
                echo $patent;
			?>
		</div>
	</div>
    <?php } ?>

    <div class="form-group">
        <label for="waste_transport_company" class="col-md-3"><?php echo lang('waste_transport_company'); ?></label>
        <div class="col-md-9">
            <?php
                echo $waste_transport_company_name;
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="waste_receiving_company" class="col-md-3"><?php echo lang('waste_receiving_company'); ?></label>
        <div class="col-md-9">
            <?php
                echo $waste_receiving_company_name;
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo $label_retirement_date; ?></label>
        <div class="col-md-9">
            <?php
            echo $fecha_retiro;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo $label_retirement_evidence; ?></label>
        <div class="col-md-9">
            <?php
            echo $html_archivo_retiro;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo $label_reception_evidence; ?></label>
        <div class="col-md-9">
            <?php
            echo $html_archivo_recepcion;
            ?>
        </div>
    </div>
        
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('waste_manifest'); ?></label>
        <div class="col-md-9">
            <?php
            echo $html_archivo_waste_manifest;
            ?>
        </div>
    </div>

<?php } ?>

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
			echo time_date_zone_format($model_info->created, $id_proyecto);
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->modified)?time_date_zone_format($model_info->modified, $id_proyecto):'-';
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