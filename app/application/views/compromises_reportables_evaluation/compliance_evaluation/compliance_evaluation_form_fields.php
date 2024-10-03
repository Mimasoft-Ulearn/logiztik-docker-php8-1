<!-- <input type="hidden" name="id" value="<?php //echo $model_info->id; ?>" /> -->
<input type="hidden" name="id_compromiso" value="<?php echo $id_compromiso; ?>" />


<?php if($puede_editar == 3){ ?>
           
    <div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
        <?php echo lang("no_permission_to_evaluate_message"); ?>
    </div> 

<?php } elseif(($puede_editar == 2) && !$evaluaciones_propias) { ?>
	
    <div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
        <?php echo lang("no_own_evaluations_message"); ?>
    </div> 

<?php } else { ?>

    <div class="form-group">
    
        <label for="filtro-semestre" class="col-md-3"><?php echo lang('semester').'-'.lang('year').' '.lang('planning'); ?>
            <span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang('filter_year_semester_tooltip'); ?>"><i class="fa fa-question-circle"></i></span>
        </label>

        <div class="col-md-3">
            <select name="filtro-semestre" id="filtro-semestre" class="form-control">
                <option value="0"> <?php echo lang('semester'); ?> </option>
                <option value="1">1</option>
                <option value="2">2</option>
            </select>
            <!-- <input type="text" id="filtro-semestre" style="width: 6em;" class="form-control" placeholder="<?php echo lang('semester'); ?>"> -->
        </div>
        <div class="col-md-3">
            <?php echo form_dropdown("filtro-agno", $dropdown_filtro_agno, 0, "id='filtro-agno' class='select2' disabled"); ?>
        </div>
    </div>


    <!--DIV_PLANIFICACION -->
    <div id="div_planificacion">
        
    </div>
    <!-- FIN DIV_PLANIFICACION -->

    <!-- DIV_EVALUACION -->
    <div id="div_evaluacion">
    
    </div>
    <!-- FIN DIV_EVALUACION -->
    
<?php } ?>
<!--Script here--> 
<script type="text/javascript">
    $(document).ready(function () {

        $('[data-toggle="tooltip"]').tooltip();
	    $('#compliance_evaluation-form .select2').select2();
	    setDatePicker("#fecha_evaluacion");
	    setDatePicker("#plazo_cierre");
    
        $('textarea[maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 1990,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
        });
	   
        function format(state) {
			
			array = state.text.split('#');
			var color = array[array.length - 1]; //último elemento del array (color)
			
			var nombre_estado = state.text.substring(0, state.text.lastIndexOf("#"));
	
			if(state.text != '-'){
				return "<div class='pull-left' style='background-color: #" + color + "; border: 1px solid black; height:15px; width:15px; border-radius: 50%;'></div>" + "&nbsp;&nbsp;" + nombre_estado;
				//<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>
			}else{
				return state.text;
			}
			
		}
		
		$("#estado").select2({
			formatResult: format,
			formatSelection: format,
			escapeMarkup: function(m) { return m; }
		});

        
       
       $('#filtro-semestre').on('change', fn_select_semester);
       
        function fn_select_semester(){
            var semestre = $('#filtro-semestre option:selected').val();

            // Al seleccionar un semestre se desbloquea el input de año
            if(semestre != 0){
                $("#filtro-agno").removeAttr('disabled');

                if($("#filtro-agno option:selected").val() != 0){
                    fn_get_planification_of_year();
                }

                $('#div_evaluacion').html('');

            }else{
                $("#filtro-agno").attr('disabled', true);
                $('#div_planificacion').html('');
                $('#div_evaluacion').html('');
            }

        }

       
        $("#filtro-agno").on('change', fn_get_planification_of_year);
       
        function fn_get_planification_of_year(){
            var id_compromiso = '<?php echo $id_compromiso; ?>';
            var semestre = $('#filtro-semestre option:selected').val();
            var year = $("#filtro-agno option:selected").text();
            
            if($("#filtro-agno option:selected").val() != 0){
                appLoader.show();
                
                $.ajax({
                    url:  '<?php echo_uri("compromises_reportables_evaluation/get_planification_of_year") ?>',
                    type:  'post',
                    data: {id_compromiso:id_compromiso, year:year, semestre:semestre},
                    success: function(respuesta){
                        $('#div_planificacion').html(respuesta);
                        $("#evaluation").select2();
                        appLoader.hide();
                    }
                });

                $('#div_evaluacion').html('');
            }else{
                $('#div_planificacion').html('');
                $('#div_evaluacion').html('');
           }
        }
      
		$('#div_planificacion').on('change', '#evaluation', fn_get_form_fields_of_evaluation);
        
        function fn_get_form_fields_of_evaluation(){
			
			var id_compromiso = '<?php echo $id_compromiso; ?>';
			var id_plan = $(this).val();
			appLoader.show();
			
			$.ajax({
				url:  '<?php echo_uri("compromises_reportables_evaluation/get_form_fields_of_evaluation") ?>',
				type:  'post',
				data: {id_compromiso:id_compromiso, id_plan:id_plan},
				success: function(respuesta){
					$('#div_evaluacion').html(respuesta);
					$("#estado").select2({
						formatResult: format,
						formatSelection: format,
						escapeMarkup: function(m) { return m; }
					});
					setDatePicker("#fecha_evaluacion");
					$("#grupo_no_cumple #criticidad").select2();
					setDatePicker("#plazo_cierre");
					$('[data-toggle="tooltip"]').tooltip();
					appLoader.hide();
				}
			});
			
		}
		
		
		//$('#estado').on('change', function(){
		$(document).on("change", "#estado", function(event) {
			
			var id_estado = $(this).val();
			
			$.ajax({
				url: '<?php echo_uri("compromises_reportables_evaluation/get_fields_of_evaluation_status") ?>',
				type: 'post',
				data: {id_estado:id_estado},
				success: function(respuesta){
					$('#grupo_no_cumple').html(respuesta);
					$("#grupo_no_cumple #criticidad").select2();
					setDatePicker("#plazo_cierre");
				}
			});
			
			event.stopImmediatePropagation();
			
		});
	   
    });
</script>