<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<!--
<div class="form-group">
  <label for="date_filed" class="col-md-3"><?php //echo lang('id'); ?></label>
    <div class=" col-md-9">
        <?php //echo $id_compromiso_proyecto; ?>
    </div>
</div>
-->

<?php if ($tipo_matriz == "rca"){ ?>

    <div class="form-group">
    <label for="numero_compromiso" class="col-md-3"><?php echo lang('compromise_number'); ?></label>
        <div class=" col-md-9">
            <?php       
                echo form_input(array(
                    "id" => "numero_compromiso",
                    "name" => "numero_compromiso",
                    "value" => $model_info->numero_compromiso,
                    "class" => "form-control",
                    "placeholder" => lang('compromise_number'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "data-rule-regex" => "^[1-9][0-9]*$",
                    "data-msg-regex" => lang("integer_greater_than_zero"),
                    "autocomplete"=> "off",
                    //"maxlength" => "255"
                ));		
            ?>
        </div>
    </div>

    <div class="form-group">
    <label for="nombre_compromiso" class="col-md-3"><?php echo lang('name'); ?></label>
        <div class=" col-md-9">
            <?php       
                echo form_input(array(
                    "id" => "nombre_compromiso",
                    "name" => "nombre_compromiso",
                    "value" => $model_info->nombre_compromiso,
                    "class" => "form-control",
                    "placeholder" => lang('name'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "autocomplete"=> "off",
                    "maxlength" => "255"
                ));		
            ?>
        </div>
    </div>

    <div id="phases">
        <div class="form-group">
            <label for="phases" class="col-md-3"><?php echo lang('phases'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_multiselect("phases[]", $fases_disponibles, $fases_compromiso, "id='phases' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
    <label for="reportability" class="col-md-3"><?php echo lang('reportability'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_checkbox("reportability", "1", ($model_info->reportabilidad == 1) ? true : false, "id='reportability'");
            ?>
        </div>
    </div>

<?php } ?>

<?php if ($tipo_matriz == "reportable"){ ?>
    
    <div class="form-group">
        <label for="n_activity" class="col-md-3"><?php echo lang('n_activity'); ?></label>
        <div class=" col-md-9">
            <?php       
                echo form_input(array(
                    "id" => "n_activity",
                    "name" => "n_activity",
                    "value" => $model_info->numero_actividad,
                    "class" => "form-control",
                    "placeholder" => lang('n_activity'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "autocomplete"=> "off",
                    "maxlength" => "255"
                ));		
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="environmental_management_instrument" class="col-md-3"><?php echo lang('environmental_management_instrument'); ?></label>
        <div class=" col-md-9">
            <?php    
                echo form_dropdown("environmental_management_instrument",$array_environmental_management_instrument, array($model_info->instrumento_gestion_ambiental), "id='environmental_management_instrument' class = 'select2 validate-hidden' data-rule-required = 'true' data-msg-required='" . lang('field_required') . "'" );
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="phase" class="col-md-3"><?php echo lang('phase_reportable'); ?></label>
        <div class=" col-md-9">
            <?php    
                echo form_dropdown("phase",$array_phase, array($model_info->etapa), "id='phase' class = 'select2 validate-hidden' data-rule-required = 'true' data-msg-required='" . lang('field_required') . "'" );
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="compliance_type" class="col-md-3"><?php echo lang('compliance_type'); ?></label>
        <div class=" col-md-9">
            <?php
                echo form_dropdown("compliance_type", $array_compliance_types, array($model_info->tipo_cumplimiento), "id='compliance_type' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="environmental_topic" class="col-md-3"><?php echo lang('environmental_topic'); ?></label>
        <div class=" col-md-9">
            <?php
                echo form_dropdown("environmental_topic", $array_environmental_topic, array($model_info->tema_ambiental), "id='environmental_topic' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="impact_on_the_environment_due_to_non_compliance" class="col-md-3"><?php echo lang('impact_on_the_environment_due_to_non_compliance'); ?></label>
        <div class=" col-md-9">
            <?php
                echo form_dropdown("impact_on_the_environment_due_to_non_compliance", $array_impact_on_the_environment_due_to_non_compliance, array($model_info->afectacion_medio_por_incumplimiento), "id='impact_on_the_environment_due_to_non_compliance' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="action_type" class="col-md-3"><?php echo lang('action_type'); ?></label>
        <div class=" col-md-9">
            <?php       
                echo form_input(array(
                    "id" => "action_type",
                    "name" => "action_type",
                    "value" => $model_info->tipo_accion,
                    "class" => "form-control",
                    "placeholder" => lang('action_type'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "autocomplete"=> "off",
                    "maxlength" => "255"
                ));		
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="responsible_area" class="col-md-3"><?php echo lang('responsible_area'); ?></label>
        <div class=" col-md-9">
            <?php
                echo form_multiselect("responsible_area[]", $array_responsible_area, $responsible_area, "id='responsible_area' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="commitment_description" class="col-md-3"><?php echo lang('environmental_commitment'); ?></label>
        <div class="col-md-9">
        <?php
            echo form_textarea(array(
                "id" => "commitment_description",
                "name" => "commitment_description",
                "value" => $model_info->descripcion_compromiso,
                "class" => "form-control",
                "placeholder" => lang('environmental_commitment'),
                "autofocus" => false,
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
                "autocomplete"=> "off",
                "maxlength" => "2000"
            ));
            ?>
        </div>
    </div>
    
<?php } ?>

<?php 
	
	$html = '';
	foreach($campos_compromiso as $campo){
		
		// 11 = texto fijo | 12 = divisor
		if($campo["id_tipo_campo"] == 11 || $campo["id_tipo_campo"] == 12){
			
			$html .= '<div class="form-group">';
				$html .= '<div class="col-md-12">';
				$html .= $Upload_compromises_controller->get_field($campo["id_campo"], $model_info->id, NULL, $tipo_matriz);
				$html .= '</div>';
			$html .= '</div>';
			//$html.= htmlspecialchars($Upload_compromises_controller->get_field($campo["id_campo"], $model_info->id, NULL, $tipo_matriz));
			
		} else {
			
			//echo $campo["nombre_campo"]."<br>";
			$html .= '<div class="form-group multi-column">';
				$html .= '<label for="'.$campo["html_name"].'" class="col-md-3">'.$campo["nombre_campo"].'</label>';
				$html .= '<div class="col-md-9">';
				$html .= $Upload_compromises_controller->get_field($campo["id_campo"], $model_info->id, NULL, $tipo_matriz);
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
                echo form_input(array(
                    "id" => "compliance_action_control",
                    "name" => "compliance_action_control",
                    "value" => $model_info->accion_cumplimiento_control,
                    "class" => "form-control",
                    "placeholder" => lang('compliance_action_control'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "autocomplete"=> "off",
                    "maxlength" => "255"
                ));		
            ?>
        </div>
    </div>

    <div class="form-group">
    <label for="execution_frequency" class="col-md-3"><?php echo lang('execution_frequency'); ?></label>
        <div class=" col-md-9">
            <?php       
                echo form_input(array(
                    "id" => "execution_frequency",
                    "name" => "execution_frequency",
                    "value" => $model_info->frecuencia_ejecucion,
                    "class" => "form-control",
                    "placeholder" => lang('execution_frequency'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "autocomplete"=> "off",
                    "maxlength" => "255"
                ));		
            ?>
        </div>
    </div>
<?php } ?>


<?php if ($tipo_matriz == "reportable"){ ?>
    <div class="form-group" id="modelo" style="display:none;">
        <label for="description" class="col-md-3 control-label"></label>
        <div class="col-md-4">
            <input type="text" class="form-control plan_description" name="description[]" maxlength="100" placeholder="<?php echo lang('commitment_description'); ?>" autocomplete="off">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control" name="term_date[]" maxlength="255" placeholder="<?php echo lang('term_date'); ?>" autocomplete="off">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeOption($(this));"><i class="fa fa-trash-o"></i></button>
        </div>
    </div>
    
    <div class="form-group">
        <label for="planning" class="col-md-3"><?php echo lang('planning'); ?></label>
        <div class="col-md-9">
        	
            <button type="button" id="agregar_planificacion" class="btn btn-xs btn-success col-sm-1" onclick="addOptions();"><i class="fa fa-plus"></i></button>
            <button type="button" id="eliminar_planificacion" class="btn btn-xs btn-danger col-sm-offset-1 col-sm-1" onclick="removeOptions();"><i class="fa fa-minus"></i></button>
        </div>
    </div>
        
    <div id="grupo_planificacion">
        
        <div class="form-group">
            <label for="description" class="col-md-3"></label>
            <div class="col-md-4">
			<?php
                $form_input = array(
                    "id" => "description",
                    "name" => "description[]",
                    "value" => $array_planificaciones[0]["descripcion"],
                    "class" => "form-control plan_description",
                    "placeholder" => lang('commitment_description'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "autocomplete"=> "off",
                    "maxlength" => "100"
                );
				if(count($array_planificaciones)){
					$form_input["disabled"] = true;
				}
				
				echo form_input($form_input);
            ?>
            </div>
            <div class="col-md-4">
			<?php
                $form_input = array(
                    "id" => "term_date",
                    "name" => "term_date[]",
                    "value" => $array_planificaciones[0]["planificacion"],
                    "class" => "form-control datepicker",
                    "placeholder" => lang('term_date'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "autocomplete"=> "off",
                    "maxlength" => "255"
                );
				if(count($array_planificaciones)){
					$form_input["disabled"] = true;
				}
				
				echo form_input($form_input);
            ?>
            </div>
        </div>
        
        <?php
        if(count($array_planificaciones)){
			
			$html_planificaciones = '';
			
			foreach($array_planificaciones as $index => $planificacion){
				
				if($index == 0){continue;}
				
				$html_planificaciones .= '<div class="form-group">';
					$html_planificaciones .= '<label for="description" class="col-md-3"></label>';
					$html_planificaciones .= '<div class="col-md-4">';
					$html_planificaciones .= '<input type="text" class="form-control plan_description" name="description[]" maxlength="100" placeholder="'.lang('description').'" value="'.$planificacion["descripcion"].'" autocomplete="off" disabled="disabled">';
					$html_planificaciones .= '</div>';
					$html_planificaciones .= '<div class="col-md-4">';
					$html_planificaciones .= '<input type="text" class="form-control datepicker" name="term_date[]" maxlength="255" placeholder="'.lang('term_date').'" value="'.$planificacion["planificacion"].'" autocomplete="off" disabled="disabled">';
					$html_planificaciones .= '</div>';
					$html_planificaciones .= '<div class="col-md-1">';
					//$html_planificaciones .= '<button type="button" class="btn btn-sm btn-danger" onclick="removeOption($(this));"><i class="fa fa-trash-o"></i></button>';
					$html_planificaciones .= '</div>';
				$html_planificaciones .= '</div>';
			}
			
			echo $html_planificaciones;

            foreach($planificaciones as $p){
                echo "<input type='hidden' name='ids_planificaciones[]' value='$p->id' />";
            }
        
        }
		?>
        
    </div>
    
    
<?php } ?>



<style>
	.multiselect-header{
	  text-align: center;
	  padding: 3px;
	  background: #7988a2;
	  color: #fff;
	}
</style> 

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();

        $('#responsible_area').multiSelect({
			selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_fields"); ?>" + "</div>",
			selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_fields"); ?>" + "</div>",
			keepOrder: true,
			afterSelect: function(value){
				var text = $('#responsible_area option[value="'+value+'"]').text();
				$('#responsible_area option[value="'+value+'"]').remove();
				$('#responsible_area').append($("<option></option>").attr("value", value).attr('selected', 'selected'));
			},
			afterDeselect: function(value){ 
				$('#responsible_area option[value="'+value+'"]').removeAttr('selected');
			}
			
		});
		
		$('input[type="text"][maxlength]').maxlength({
            //alwaysShow: true,
            //threshold: 245,
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
		
		$('#individual_upload-form .select2').select2({
			/*sortResults: function(data) {
				return data.sort(function (a, b) {
					a = a.text.toLowerCase();
					b = b.text.toLowerCase();
					if (a > b) {
						return 1;
					} else if (a < b) {
						return -1;
					}
					return 0;
				});
			}*/
		
		});
		setDatePicker("#individual_upload-form .datepicker");
		setTimePicker('#individual_upload-form .timepicker');
		
    });
	
	function addOptions(){
		$('#individual_upload-form #grupo_planificacion').append($("<div/>").addClass('form-group planificacion').html($('#individual_upload-form #modelo').html()));
		$('#individual_upload-form .planificacion').last().find('input').attr('data-rule-required', true);
		$('#individual_upload-form .planificacion').last().find('input').attr('data-msg-required', '<?php echo lang("field_required"); ?>');
		$('#individual_upload-form .planificacion').last().find('input').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		setDatePicker($('#individual_upload-form .planificacion').last().find('input[name="term_date[]"]'));
	}
	
	function removeOptions(){
		$('#individual_upload-form .planificacion').last().remove();
	}
	function removeOption(element){
		element.closest('#individual_upload-form .planificacion').remove();
	}
	
</script>