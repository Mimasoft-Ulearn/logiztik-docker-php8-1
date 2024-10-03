<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="tipo_actividad" class="<?php echo $label_column; ?>"><?php echo lang('ac_type_of_activity'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("id_tipo_actividad", $type_of_activities_dropdown, array($model_info->id_tipo_actividad), "id='id_tipo_actividad' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div id="actividad_group">
    <div class="form-group">
        <label for="actividad" class="<?php echo $label_column; ?>"><?php echo lang('activity'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("id_actividad", $activities_dropdown, array($model_info->id_actividad), "id='id_actividad' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="grafico" class="<?php echo $label_column; ?>"><?php echo lang('ac_chart'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("grafico", $charts_dropdown, array($model_info->grafico), "id='id_grafico' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div class="form-group">
    <label for="objectives" class="col-md-3"><?php echo lang('objectives'); ?></label>
    <div class="col-md-9">
        
        <button type="button" id="agregar_objetivo" class="btn btn-xs btn-success col-sm-1"><i class="fa fa-plus"></i></button>
        <button type="button" id="eliminar_objetivo" class="btn btn-xs btn-danger col-sm-offset-1 col-sm-1"><i class="fa fa-minus"></i></button>
    </div>
</div>

<div id="objectives_group">
<?php if(count($array_objetivos)){ ?>

    <?php foreach ($array_objetivos as $year => $objetivo) { ?>
        
        <div class="form-group modelo">
            <label class="col-md-3"></label>

            <div class="col-md-4">
            <?php
                $form_input = array(
                    "id" => "year",
                    "name" => "year[]",
                    "value" => $year,
                    "class" => "form-control datepicker",
                    "placeholder" => lang('year'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "autocomplete"=> "off"
                );            
                echo form_input($form_input);
            ?>
            </div>

            <div class="col-md-4">
            <?php
                $form_input = array(
                    "id" => "objetivo",
                    "name" => "objetivo[]",
                    "type" => "number",
                    "value" => $objetivo,
                    "class" => "form-control",
                    "placeholder" => lang('objective_value'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "autocomplete"=> "off"
                );        
                echo form_input($form_input);
            ?>    
            </div>

            <div class="col-md-1">
                <button type="button" class="btn_borrar_objetivo btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></button>
            </div>
        </div>

    <?php } ?>

<?php }else{ ?>
    
    <div class="form-group modelo">
        <label class="col-md-3"></label>

        <div class="col-md-4">
        <?php
            $form_input = array(
                "id" => "year",
                "name" => "year[]",
                "value" => '',
                "class" => "form-control datepicker",
                "placeholder" => lang('year'),
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
                "autocomplete"=> "off"
            );            
            echo form_input($form_input);
        ?>
        </div>

        <div class="col-md-4">
        <?php
            $form_input = array(
                "id" => "objetivo",
                "name" => "objetivo[]",
                "type" => "number",
                "value" => "",
                "class" => "form-control",
                "placeholder" => lang('objective_value'),
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
                "autocomplete"=> "off"
            );        
            echo form_input($form_input);
        ?>    
        </div>

        <div class="col-md-1">
            <button type="button" class="btn_borrar_objetivo btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></button>
        </div>
    </div>

<?php }?>
</div>

<!-- <div class="form-group" id="modelo" style="display:none;">
    <label for="year" class="col-md-3 control-label"></label>
    <div class="col-md-4">
        <input type="text" name="year[]" value="" id="year" class="form-control datepicker" placeholder="<?php echo lang('year'); ?>">
    </div>
    <div class="col-md-4">
        <input type="number" class="form-control" name="objetivo[]" placeholder="<?php echo lang('objective_value'); ?>" autocomplete="off">
    </div>
    <div class="col-md-1">
        <button type="button" class="btn btn-sm btn-danger" onclick="removeOption($(this));"><i class="fa fa-trash-o"></i></button>
    </div>
</div> -->


<script type="text/javascript">
    $(document).ready(function () {
        
		//$('[data-toggle="tooltip"]').tooltip();

		$('#feeders_activity_objectives-form .select2').select2();
        
        $(".datepicker").datepicker({
            format: "yyyy",
            viewMode: "years", 
            minViewMode: "years"
        });
        
        $('#id_tipo_actividad').change(function(){
            let id_tipo_actividad = $(this).val();

            $.ajax({
                url: '<?php echo_uri("AC_Feeders/get_activities_for_objectives"); ?>',
                type: 'post',
                data: { id_tipo_actividad: id_tipo_actividad },
                success: function(respuesta){
                    $('#actividad_group').html(respuesta);
                    $('#id_actividad').select2();
                }
            });
        });


        $('#agregar_objetivo').click(function(){
            
            // Se hace una copia del primer "modelo" (elementos año y objetivo) y se inserta al final
            $( $('.modelo').first() ).clone().insertAfter($('.modelo').last());

            $('.modelo').last().find('input[name="year[]"]').val('');
            $('.modelo').last().find('input[name="objetivo[]"]').val('');

            $('.modelo').last().find('input[name="objetivo[]"]').attr('data-rule-required', true);
            $('.modelo').last().find('input[name="objetivo[]"]').attr('data-msg-required', '<?php echo lang("field_required"); ?>');

            $('.modelo').last().find('.datepicker').datepicker({
                format: "yyyy",
                viewMode: "years", 
                minViewMode: "years"
            });

            // Se le debe agregar el evento borrar al boton borrar del nuevo elemento
            $('.modelo').last().find('.btn_borrar_objetivo').on('click',function(){
                borrar_objetivo(this);
            });
        });

        $('#eliminar_objetivo').click(function(){
            if($('.modelo').length > 1){ // Debe existir al menos un "modelo"
                $('.modelo').last().remove();
            }
        });

        $('.btn_borrar_objetivo').click(function(){
            borrar_objetivo(this);
        });

        function borrar_objetivo(element){
            if($('.modelo').length > 1){ // Debe existir al menos un par año-objetivo
                element.closest('.modelo').remove();
            }
        }

        
    });
</script>