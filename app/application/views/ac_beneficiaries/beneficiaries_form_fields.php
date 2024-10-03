<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="id_nacionalidad" class="<?php echo $label_column; ?>"><?php echo lang('national_id'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "id_nacionalidad",
            "name" => "id_nacionalidad",
            "value" => $model_info->id_nacionalidad,
            "class" => "form-control",
            "placeholder" => lang('national_id'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
	<label for="sexo" class="<?php echo $label_column; ?>"><?php echo lang('sex'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_dropdown("sexo", $dropdown_sex, array($model_info->sexo), "id='sexo' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
</div>

<div class="form-group">
	<label for="fecha_nacimiento" class="<?php echo $label_column; ?>"><?php echo lang('birthdate'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "fecha_nacimiento",
			"name" => "fecha_nacimiento",
			"value" => $model_info->fecha_nacimiento,
			"class" => "form-control datepicker",
			"placeholder" => lang('birthdate'),
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="correo_organizativo" class="<?php echo $label_column; ?>"><?php echo lang('organizational_email'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "correo_organizativo",
			"name" => "correo_organizativo",
			"value" => $model_info->correo_organizativo,
			"class" => "form-control",
			"placeholder" => lang('organizational_email'),
			//"autofocus" => true,
			"data-rule-email" => true,
			"data-msg-email" => lang("enter_valid_email"),
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>

<div class="form-group">
    <label for="sociedad" class="<?php echo $label_column; ?>"><?php echo lang('society'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "sociedad",
            "name" => "sociedad",
            "value" => $model_info->sociedad,
            "class" => "form-control",
            "placeholder" => lang('society'),
			// "type" => "number",
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"data-rule-number" => true,
			"data-msg-number" => lang("enter_a_number"),
			"autocomplete"=> "off",
        ));
        ?>
    </div>
</div>

<div class="form-group">
	<label for="sociedad_desc" class="<?php echo $label_column; ?>"><?php echo lang('society_desc'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_dropdown("sociedad_desc", $dropdown_sociedad_desc, array($model_info->sociedad_desc), "id='sociedad_desc' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
</div>

<div class="form-group">
	<label for="centro_de_costo_desc" class="<?php echo $label_column; ?>"><?php echo lang('cost_center_desc'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "centro_de_costo_desc",
			"name" => "centro_de_costo_desc",
			"value" => $model_info->centro_de_costo_desc,
			"class" => "form-control",
			"placeholder" => lang('cost_center_desc'),
			"autofocus" => true,
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="posicion_desc" class="<?php echo $label_column; ?>"><?php echo lang('position_desc'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "posicion_desc",
			"name" => "posicion_desc",
			"value" => $model_info->posicion_desc,
			"class" => "form-control",
			"placeholder" => lang('position_desc'),
			"autofocus" => true,
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="division_desc" class="<?php echo $label_column; ?>"><?php echo lang('division_desc'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "division_desc",
			"name" => "division_desc",
			"value" => $model_info->division_desc,
			"class" => "form-control",
			"placeholder" => lang('division_desc'),
			"autofocus" => true,
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="subdivision_desc" class="<?php echo $label_column; ?>"><?php echo lang('subdivision_desc'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "subdivision_desc",
			"name" => "subdivision_desc",
			"value" => $model_info->subdivision_desc,
			"class" => "form-control",
			"placeholder" => lang('subdivision_desc'),
			"autofocus" => true,
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="sigla_sociedad" class="<?php echo $label_column; ?>"><?php echo lang('subdivision_society_acronym'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "sigla_sociedad",
			"name" => "sigla_sociedad",
			"value" => $model_info->sigla_sociedad,
			"class" => "form-control",
			"placeholder" => lang('subdivision_society_acronym'),
			"autofocus" => true,
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="sucursal" class="<?php echo $label_column; ?>"><?php echo lang('subdivisoin_branch_office'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "sucursal",
			"name" => "sucursal",
			"value" => $model_info->sucursal,
			"class" => "form-control",
			"placeholder" => lang('subdivisoin_branch_office'),
			"autofocus" => true,
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="cargo" class="<?php echo $label_column; ?>"><?php echo lang('subdivision_position'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "cargo",
			"name" => "cargo",
			"value" => $model_info->cargo,
			"class" => "form-control",
			"placeholder" => lang('subdivision_position'),
			"autofocus" => true,
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="fecha_inicio_contrato" class="<?php echo $label_column; ?>"><?php echo lang('contract_start_date'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "fecha_inicio_contrato",
			"name" => "fecha_inicio_contrato",
			"value" => $model_info->fecha_inicio_contrato,
			"class" => "form-control datepicker",
			"placeholder" => lang('contract_start_date'),
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="fecha_fin_contrato" class="<?php echo $label_column; ?>"><?php echo lang('contract_end_date'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "fecha_fin_contrato",
			"name" => "fecha_fin_contrato",
			"value" => $model_info->fecha_fin_contrato,
			"class" => "form-control datepicker",
			"placeholder" => lang('contract_end_date'),
			// "data-rule-required" => true,
			// "data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="estado" class="<?php echo $label_column; ?>"><?php echo lang('state'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_dropdown("estado", $dropdown_estado, array($model_info->estado), "id='estado' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
</div>

<div class="form-group">
	<label for="tipo_contrato" class="<?php echo $label_column; ?>"><?php echo lang('contract_type'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_dropdown("tipo_contrato", $dropdown_tipo_contrato, array($model_info->tipo_contrato), "id='tipo_contrato' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
</div>

<div class="form-group">
	<label for="division2_desc" class="<?php echo $label_column; ?>"><?php echo lang('division2_desc'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "division2_desc",
			"name" => "division2_desc",
			"value" => $model_info->division2_desc,
			"class" => "form-control",
			"placeholder" => lang('division2_desc'),
			"autofocus" => true,
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="posicion_jefe_desc" class="<?php echo $label_column; ?>"><?php echo lang('boss_position_desc'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "posicion_jefe_desc",
			"name" => "posicion_jefe_desc",
			"value" => $model_info->posicion_jefe_desc,
			"class" => "form-control",
			"placeholder" => lang('boss_position_desc'),
			"autofocus" => true,
			// "data-rule-required" => true,
			// "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="estado_civil" class="<?php echo $label_column; ?>"><?php echo lang('civil_status'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_dropdown("estado_civil", $dropdown_estado_civil, array($model_info->estado_civil), "id='estado_civil' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
</div>

<div class="form-group">
	<label for="area_de_personal" class="<?php echo $label_column; ?>"><?php echo lang('personnel_area'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_dropdown("area_de_personal", $dropdown_area_de_personal, array($model_info->area_de_personal), "id='estado_civil' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
</div>

<div class="form-group">
	<label for="departamento_desc" class="<?php echo $label_column; ?>"><?php echo lang('department_desc'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "departamento_desc",
			"name" => "departamento_desc",
			"value" => $model_info->departamento_desc,
			"class" => "form-control",
			"placeholder" => lang('department_desc'),
			"autofocus" => true,
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="job_code_desc" class="<?php echo $label_column; ?>"><?php echo lang('jobcode_desc'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "job_code_desc",
			"name" => "job_code_desc",
			"value" => $model_info->job_code_desc,
			"class" => "form-control",
			"placeholder" => lang('jobcode_desc'),
			"autofocus" => true,
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="nombre_completo" class="<?php echo $label_column; ?>"><?php echo lang('fullname'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "nombre_completo",
			"name" => "nombre_completo",
			"value" => $model_info->nombre_completo,
			"class" => "form-control",
			"placeholder" => lang('fullname'),
			"autofocus" => true,
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>

<div class="form-group">
	<label for="nacionalidad" class="<?php echo $label_column; ?>"><?php echo lang('nationality'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_dropdown("nacionalidad", $dropdown_nacionalidad, array($model_info->nacionalidad), "id='nacionalidad' class='select2 data-sigla=''");
		?>
	</div>
</div>

<div class="form-group">
	<label for="comuna" class="<?php echo $label_column; ?>"><?php echo lang('commune'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_input(array(
			"id" => "comuna",
			"name" => "comuna",
			"value" => $model_info->comuna,
			"class" => "form-control",
			"placeholder" => lang('commune'),
			"autofocus" => true,
			// "data-rule-required" => true,
			// "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
		));
		?>
	</div>
</div>


<div class="form-group">
	<label for="provincia" class="<?php echo $label_column; ?>"><?php echo lang('province'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_dropdown("provincia", $dropdown_provincia, array($model_info->provincia), "id='provincia' class='select2' data-sigla=''");
		?>
	</div>
</div>

<div class="form-group">
	<label for="discapacidad" class="<?php echo $label_column; ?>"><?php echo lang('disability'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_dropdown("discapacidad", $dropdown_discapacidad, array($model_info->discapacidad), "id='discapacidad' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
</div>

<div class="form-group">
	<label for="ley_tea" class="<?php echo $label_column; ?>"><?php echo lang('tea_law'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php		
		echo form_dropdown("ley_tea", $dropdown_ley_tea, array($model_info->ley_tea), "id='ley_tea' class='select2' data-sigla=''");
		?>
	</div>
</div>

<div class="form-group">
	<label for="pueblos_originarios" class="<?php echo $label_column; ?>"><?php echo lang('native_people'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_dropdown("pueblos_originarios", $dropdown_pueblos_originarios, array($model_info->pueblos_originarios), "id='pueblos_originarios' class='select2' data-sigla=''");
		?>
	</div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        
		setDatePicker("#beneficiaries-form .datepicker");

		///////////////////////////////
		$('[data-toggle="tooltip"]').tooltip();
		$('#beneficiaries-form .select2').select2();
		$("#rut_beneficiario, #client_n, #rut_representante").rut({
			formatOn: 'input',
			minimumLength: 8, // validar largo mínimo; default: 2
			validateOn: 'change', // si no se quiere validar, pasar null
			useThousandsSeparator : false
		});
		
		$("#rut_beneficiario").keyup(function(e){
			var rut = $(this).val();
			var id = "<?php echo $model_info->id; ?>";
			
			$('#rut_beneficiario_validacion').html("<?php echo lang('loading...'); ?>");
			
			$.ajax({
				url: '<?php echo_uri("AC_Beneficiaries/get_existing_rut"); ?>',
				type: 'post',
				data: {rut:rut, id:id},
				//dataType:'json',
				success: function(respuesta){
					$('#rut_beneficiario_validacion').html(respuesta);
				}
			});
		});
		
		$('#tipo_stakeholder').change(function(){		
			
			var tipo_stakeholder = $(this).val();
			
			// SI ES PERSONA JURIDICA: MUESTRO CAMPOS PERSONERIA Y 2 DE REPRESENTANTE
			if(tipo_stakeholder == "legal_person"){
				$.ajax({
					url:  '<?php echo_uri("AC_Beneficiaries/get_fields_of_legal_person_stakeholder"); ?>',
					type:  'post',
					//data: {tipo_stakeholder:tipo_stakeholder},
					//dataType:'json',
					success: function(respuesta){
						$('#legal_person_stakeholder_fields_group').html(respuesta);
						$('[data-toggle="tooltip"]').tooltip();
						
						$('#rut_representante').rut({
							formatOn: 'input',
							minimumLength: 8, // validar largo mínimo; default: 2
							validateOn: 'change', // si no se quiere validar, pasar null
							useThousandsSeparator : false
						});
					}
				});

			}
			
			// SI ES PERSONA NATURAL: DEJO OPCIONAL EL CAMPO TELEFONO Y MULTISELECT Beneficiarios jurídicos
			if(tipo_stakeholder == "natural_person"){
				
				$('#legal_person_stakeholder_fields_group').html("");
				$.ajax({
					url: '<?php echo_uri("AC_Beneficiaries/get_optional_phone_contact_field"); ?>',
					type: 'post',
					success: function(respuesta) {
						$('#telefono_contacto_container').html(respuesta);
					}
				})
				
				$('#grupo_juridicos').show();
				
			}else{
				$('#grupo_juridicos').hide();
			}

		});
		
		$('#id_macrozona').change(function(){		
			
			var id_macrozona = $(this).val();
			select2LoadingStatusOn($('#comuna'));
			
			$.ajax({
				url:  '<?php echo_uri("AC_Beneficiaries/get_communes_by_macrozone") ?>',
				type:  'post',
				data: {id_macrozona:id_macrozona},
				success: function(respuesta){
					$('#grupo_comuna').html(respuesta);
					$('#grupo_comuna select').select2();
					// Las siguientes reglas son aplicadas arriba pero las comenté para que se apliquen cuando el campo es cargado...
					//$('#responsable').rules('add', { required: false });
					//$('#responsable').rules('add', { required: true });
				}
			});

		});
		
		
		$(document).on('change','#comuna',function(event){
			
			var id_comuna = $(this).val();
			
			if($('#tipo_stakeholder').val() == "natural_person"){
				
				$.ajax({
					url: '<?php echo_uri("AC_Beneficiaries/get_legal_beneficiaries_by_comune"); ?>',
					type: 'post',
					data: {id_comuna:id_comuna},
					success: function(respuesta){
						$('#grupo_juridicos').html(respuesta);
						$('#organizacion').select2();
					}
				});
			}
			
			event.stopImmediatePropagation();

		});
		
		$('input[type=radio][name=comunidad_indigena]').change(function(){		
			
			var comunidad_indigena = $(this).val();
			if(comunidad_indigena == "si"){
				$.ajax({
					url:  '<?php echo_uri("AC_Beneficiaries/get_fields_of_comunidad_indigena"); ?>',
					type:  'post',
					//data: {comunidad_indigena:comunidad_indigena},
					//dataType:'json',
					success: function(respuesta){
						$('#comunidad_indigena_fields_group').html(respuesta);
					}
				});
			} else {
				$('#comunidad_indigena_fields_group').html("");
			}

		});
		
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

    });
</script>