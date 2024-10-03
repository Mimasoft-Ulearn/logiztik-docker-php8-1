<!-- <input type="hidden" name="id" value="<?php //echo $model_info->id; ?>" /> -->
<input type="hidden" name="tipo_unidad_residuo" value="<?php echo $tipo_unidad_residuo; ?>" />
<input type="hidden" name="unidad_residuo" value="<?php echo $unidad_residuo; ?>" />

<input type="hidden" name="id" value="<?php echo $add_type == "multiple" ? "" : $model_info->id; ?>" />
<input type="hidden" name="add_type" value="<?php echo $add_type; ?>" />

<!-- Fecha de registro datepicker -->
<div class="form-group">
  <label for="date_filed" class="col-md-3"><?php echo $label_storage_date; ?></label>
    <div class=" col-md-9">
        <?php
		$datos = json_decode($model_info->datos, true);
		$fecha_registro = $datos["fecha"];
		//$fecha_registro = get_date_format($datos["fecha"],$this->session->project_context);
        echo form_input(array(
            "id" => "date_filed",
            "name" => "date_filed",
            "value" => $fecha_registro,
            "class" => "form-control datepicker",
            "placeholder" => $label_storage_date,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete" => "off",
        ));
        ?>
    </div>
</div>

<?php if($flujo == "Residuo"){ ?>
	<!-- Mes datepicker -->
<div class="form-group">
  <label for="month" class="col-md-3"><?php echo lang('month'); ?></label>
    <div class=" col-md-9">
        <?php
		$datos = json_decode($model_info->datos, true);
		$month = number_to_month($datos["month"]);
		
        echo form_input(array(
            "id" => "month",
            "name" => "month",
            "value" => $month,
            "class" => "form-control datepicker",
            "placeholder" => lang('month'),
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete" => "off",
        ));
        ?>
    </div>
</div>
<?php } ?>


<!--Categorías dropdown-->
<?php
  //$datos = json_decode($model_info->datos, true);
  $id_categoria = $datos["id_categoria"];
  $info = ($count_cat == 1) ? '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_category_forms_form_field_info').'"><i class="fa fa-question-circle"></i></span>' : '';
?>
<div class="form-group">
  <label for="category" class=" col-md-3"><?php echo lang('category') . " " . $info; ?></label>
  <div class=" col-md-9">
	
	<?php if ($count_cat == 1) {?>
    	<input type="hidden" name="category" value="<?php echo key($categorias); ?>" />
        <?php
            echo form_dropdown("category", $categorias, $categorias, "id='clienteCH' class='select2 validate-hidden' data-rule-required='true', disabled='disabled', data-msg-required='" . lang('field_required') . "'");
        ?>
        <?php } else { ?>
        <?php
            echo form_dropdown("category", array("" => "-") + $categorias, $id_categoria, "id='clienteCH' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    <?php } ?>
    
  </div>
</div>

<div class="form-group">
	<label for="id_sucursal" class=" col-md-3"><?php echo lang("branch_office"); ?></label>
	<div class=" col-md-9">
		<?php
			echo form_dropdown("id_sucursal", array("" => "-") + $array_sucursales_dropdown, $datos["id_sucursal"], "id='id_sucursal' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
</div>

<?php if($flujo == "Residuo"){ ?>

	<div id="waste_unit_group">
		<div class="form-group">
		<label for="waste_unit" class="col-md-3"><?php echo /*$nombre_unidad_residuo." (".$tipo_unidad_residuo.")"*/lang("quantity"); ?></label>
			<div class=" col-md-9">
				<div class="col-md-10 p0">
				<?php
				$unidad = $datos["unidad_residuo"];
				echo form_input(array(
					"id" => "waste_unit",
					"name" => "waste_unit",
					"value" => $add_type == "multiple" ? "" : $unidad,
					"class" => "form-control",
					"placeholder" => $nombre_unidad_residuo,
					"data-rule-required" => true,
					"data-msg-required" => lang("field_required"),
					"data-rule-number" => true,
					"data-msg-number" => lang("enter_a_number"),
					"autocomplete" => "off",
				));
				?>
				</div>
				<div class="col-md-2">
					<?php echo $unidad_residuo ?>
				</div>
			</div>
		</div>
	</div>

	<!--Tipo de tratamiento-->
	<div id="type_of_treatment_group">
		<div class="form-group">
			<label for="type_of_treatment" class="col-md-3"><?php echo lang('type_of_treatment'); ?></label>
			<div class="col-md-9">
				<?php
				$disabled = ($disabled_field)?"disabled='disabled'":"";
				echo form_dropdown("type_of_treatment", array("" => "-") + $tipo_tratamiento, ($model_info->id)?$datos["tipo_tratamiento"]:$tipo_tratamiento_default, "id='type_of_treatment' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".$disabled);
				?>
			</div>
		</div>
	</div>

<?php } ?>



<?php if(($flujo == "Consumo") || ($flujo == "No Aplica")) { ?>
<div id="waste_unit_group">
	<div class="form-group">
	  <label for="waste_unit" class="col-md-3"><?php echo $nombre_unidad_residuo." (".$tipo_unidad_residuo.")" ?></label>
		<div class=" col-md-9">
			<div class="col-md-10 p0">
			<?php
			$unidad = $datos["unidad_residuo"];
			echo form_input(array(
				"id" => "waste_unit",
				"name" => "waste_unit",
				"value" => $add_type == "multiple" ? "" : $unidad,
				"class" => "form-control",
				"placeholder" => $nombre_unidad_residuo,
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-number" => true,
				"data-msg-number" => lang("enter_a_number"),
				"autocomplete" => "off",
			));
			?>
			</div>
			<div class="col-md-2">
				<?php echo $unidad_residuo ?>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<?php if($flujo == "Consumo") { ?>
	
	<?php if($type_of_origin == "1"){ // id 1: matter ?>
	
		<div id="matter_group">
			<div class="form-group">
				<label for="matter" class="col-md-3"><?php echo lang('type'); ?></label>
				<div class="col-md-9">
					<input type="hidden" name="type_of_origin" value="<?php echo $type_of_origin; ?>" />
					<?php
						$disabled = ($disabled_field)?"disabled='disabled'":"";
						echo form_dropdown("type_of_origin_matter", $array_tipos_origen_materia, $default_matter, "id='type_of_origin_matter' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".$disabled);
					?>
					<?php if($disabled_field){ ?>
						<input type="hidden" name="type_of_origin_matter" value="<?php echo $default_matter; ?>" />
					<?php } ?>
				</div>
			</div>
		</div>
		
	<?php } ?>
	
	<?php if($type_of_origin == "2"){ // id 2: energy ?>
	
		<div id="matter_group">
			<div class="form-group">
				<label for="matter" class="col-md-3"><?php echo lang('type'); ?></label>
				<div class="col-md-9">
					<?php
					$disabled = ($disabled_field)?"disabled='disabled'":"";
					echo form_dropdown("type_of_origin", $array_tipos_origen, $type_of_origin, "id='type_of_origin' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".$disabled);
					?>
					<?php if($disabled){ ?>
					<input type="hidden" name="type_of_origin" value="<?php echo $type_of_origin; ?>" />
					<?php } ?>
				</div>
			</div>
		</div>
		
	<?php } ?>
	
<?php } ?>


<?php if($flujo == "No Aplica") { ?>
	
    <div id="default_type_group">
		<div class="form-group">
			<label for="default_type" class="col-md-3"><?php echo lang('type'); ?></label>
			<div class="col-md-9">
				<?php
				$disabled = ($disabled_default_type)?"disabled='disabled'":"";
				echo form_dropdown("default_type", array("" => "-") + $array_tipos_por_defecto, ($model_info->id)?$datos["default_type"]:$tipo_por_defecto_default, "id='default_type' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' $disabled");
				?>
                <?php if($disabled){ ?>
                <input type="hidden" name="default_type" value="<?php echo $tipo_por_defecto_default; ?>" />
                <?php } ?>
			</div>
		</div>
	</div>
    
<?php } ?>


<?php 
	$html = '';
	foreach($campos as $campo){

		$html .= '<div class="form-group multi-column">';
		if(($campo->id_tipo_campo == 12)||($campo->id_tipo_campo == 11)){// si divisor y texto fijo
			$html .= '<div class="col-md-12">';
			$html .= '<div style="word-wrap: break-word;">';
			$html .= $campo->default_value;
			$html .= '</div>';
			$html .= '</div>';
		}else{
			$html .= '<label for="'.$campo->html_name.'" class="col-md-3">'.$campo->nombre.'</label>';
			$html .= '<div class="col-md-9">';
			$html .= $Environmental_records_controller->get_field($campo->id, $model_info->id, NULL, $add_type);
			$html .= '</div>';
			
		}
		$html .= '</div>';
		
	}

	echo $html;

?>

<?php if($flujo == "Residuo") { ?>

	<?php if($project_info->in_rm){ ?>

		<div class="form-group">
			<label for="carrier_rut" class="col-md-3"><?php echo lang('carrier_rut'); ?></label>
			<div class="col-md-9">
				<?php
					$datos = json_decode($model_info->datos, true);
					$carrier_rut = $datos["carrier_rut"];
					echo form_input(array(
						"id" => "carrier_rut",
						"name" => "carrier_rut",
						"value" => $carrier_rut,
						"class" => "form-control",
						"placeholder" => lang('carrier_rut'),
						//"data-rule-required" => true,
						//"data-msg-required" => lang("field_required"),
						//"data-rule-number" => true,
						//"data-msg-number" => lang("enter_a_number"),
						"autocomplete" => "off",
						"maxlength" => "255"
					));
				?>
			</div>
		</div>

		<div class="form-group">
			<label for="id_patent" class="col-md-3"><?php echo lang('patent'); ?></label>
			<div class="col-md-9">
				<?php
					$datos = json_decode($model_info->datos, true);
					$id_patent = $datos["id_patent"];
					echo form_dropdown("id_patent", $patents_dropdown, $id_patent, "id='id_patent' class='select2'");
				?>
			</div>
		</div>

	<?php } ?>

	<div class="form-group">
		<label for="id_waste_transport_company" class="col-md-3"><?php echo lang('waste_transport_company'); ?></label>
		<div class="col-md-9">
			<?php
				$datos = json_decode($model_info->datos, true);
				$id_waste_transport_company = $datos["id_waste_transport_company"];
				echo form_dropdown("id_waste_transport_company", $waste_transport_companies_dropdown, $id_waste_transport_company, "id='id_waste_transport_company' class='select2'");
			?>
		</div>
	</div>

	<div class="form-group">
		<label for="id_waste_receiving_company" class="col-md-3"><?php echo lang('waste_receiving_company'); ?></label>
		<div class="col-md-9">
			<?php
				$datos = json_decode($model_info->datos, true);
				$id_waste_receiving_company = $datos["id_waste_receiving_company"];
				echo form_dropdown("id_waste_receiving_company", $waste_receiving_companies_dropdown, $id_waste_receiving_company, "id='id_waste_receiving_company' class='select2'");
			?>
		</div>
	</div>

    <div id="retirement_date_group">
        <div class="form-group">
          <label for="retirement_date" class="col-md-3"><?php echo $label_retirement_date; ?></label>
            <div class=" col-md-9">
                <?php
                $fecha_retiro = $datos["fecha_retiro"];
                echo form_input(array(
                    "id" => "retirement_date",
                    "name" => "retirement_date",
                    "value" => $fecha_retiro,
                    "class" => "form-control datepicker",
                    "placeholder" => $label_retirement_date,
                    //"data-rule-required" => true,
                    //"data-msg-required" => lang("field_required"),
                    "autocomplete" => "off",
                ));
                ?>
            </div>
        </div>
    </div>

	<?php if($archivo_retiro) { ?>
		
        <div class="form-group">
          <label for="retirement_evidence" class="col-md-3"><?php echo $label_retirement_evidence; ?></label>
            <div id="dropzone_retirement_evidence" class="col-md-9">
        		<?php echo $html_archivo_retiro; ?>
        	</div>
        </div>
        
    <?php } else { ?>
    
    	<div class="form-group">
          <label for="retirement_evidence" class="col-md-3"><?php echo $label_retirement_evidence; ?></label>
            <div id="dropzone_retirement_evidence" class="col-md-9">
                <?php
                    echo $this->load->view("includes/retirement_evidence_uploader", array(
                        "upload_url" => get_uri("fields/upload_file"),
                        "validation_url" =>get_uri("fields/validate_file_pdf")
                    ), true);
                ?>
            </div>
        </div>
        
    <?php } ?>
    
    <?php if($archivo_recepcion) { ?>
   		
        <div class="form-group">
          <label for="reception_evidence" class="col-md-3"><?php echo $label_reception_evidence; ?></label>
            <div id="dropzone_reception_evidence" class="col-md-9">
        		<?php echo $html_archivo_recepcion; ?>
         	</div>
        </div>
        
	<?php } else { ?>
    	
        <div class="form-group">
          <label for="reception_evidence" class="col-md-3"><?php echo $label_reception_evidence; ?></label>
            <div id="dropzone_reception_evidence" class="col-md-9">
                <?php
                    echo $this->load->view("includes/reception_evidence_uploader", array(
                        "upload_url" => get_uri("fields/upload_file"),
                        "validation_url" =>get_uri("fields/validate_file_pdf")
                    ), true);
                ?>
            </div>
        </div>

    <?php } ?>


	<?php if($archivo_waste_manifest) { ?>
	
		<div class="form-group">
			<label for="waste_manifest" class="col-md-3"><?php echo lang('waste_manifest'); ?></label>
			<div id="dropzone_waste_manifest" class="col-md-9">
				<?php echo $html_archivo_waste_manifest; ?>
			</div>
		</div>
		
	<?php } else { ?>
		
		<div class="form-group">
			<label for="waste_manifest" class="col-md-3"><?php echo lang('waste_manifest'); ?></label>
			<div id="dropzone_waste_manifest" class="col-md-9">
				<?php
					echo $this->load->view("includes/waste_manifest_uploader", array(
						"upload_url" => get_uri("fields/upload_file"),
						"validation_url" =>get_uri("fields/validate_file_pdf")
					), true);
				?>
			</div>
		</div>

	<?php } ?>
    
<?php }?>

<script type="text/javascript">
    $(document).ready(function () {

		// Quitar las commas ingresadas como separadoras de miles
		$("#environmental_records-form").submit(function(){
            let waste_unit = $("#waste_unit").val();
            let no_comma_value =  waste_unit.replace(/,/g , "");

            $("#waste_unit").val(no_comma_value);
        });

        $('[data-toggle="tooltip"]').tooltip();
		
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
	
		
		$('#month').datepicker( {
			format: "MM",
			viewMode: "months", 
			minViewMode: "months",
			maxViewMode: "months",
			autoclose: true,
			language: "es"
		});

		$('#environmental_records-form .select2').select2();
		setDatePicker("#environmental_records-form .datepicker");
		setTimePicker('#environmental_records-form .timepicker');

    });
</script>