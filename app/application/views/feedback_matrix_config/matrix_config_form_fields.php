<input type="hidden" name="id" value="<?php echo $id_proyecto; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
     
<div class="form-group">
    
    <?php

        $arraySelected = array();
        $arraySelected2 = array();
        $arrayCamposProyecto = array();
		
		$arraySelected[] = "fijo_1";
		$arraySelected2["fijo_1"] = lang("date");
		$array_campos_ocupados[] = "fijo_1";
		
		$arraySelected[] = "fijo_2";
		$arraySelected2["fijo_2"] = lang("name");
		$array_campos_ocupados[] = "fijo_2";

		$arraySelected[] = "fijo_3";
		$arraySelected2["fijo_3"] = lang("email"); 
		$array_campos_ocupados[] = "fijo_3";

		$arraySelected[] = "fijo_4";
		$arraySelected2["fijo_4"] = lang("phone_number"); 
		$array_campos_ocupados[] = "fijo_4";
		
		$arraySelected[] = "fijo_5";
		$arraySelected2["fijo_5"] = lang("type_of_interest_group"); // lang("type_of_stakeholder");
		$array_campos_ocupados[] = "fijo_5";
		
		$arraySelected[] = "fijo_6";
		$arraySelected2["fijo_6"] = lang("reason_for_contact"); //  lang("visit_purpose");
		$array_campos_ocupados[] = "fijo_6";

		$arraySelected[] = "fijo_7";
		$arraySelected2["fijo_7"] = lang("comments"); //lang("describe_the_reason_for_your_contact"); 
		$array_campos_ocupados[] = "fijo_7";
		

        foreach($campos_feedback_matrix_config as $innerArray){
            $arraySelected[] = $innerArray["id"];
            $arraySelected2[(string)$innerArray["id"]] = $innerArray["nombre"];
        }

        foreach($campos_proyecto as $innerArray){
            if(array_search($innerArray["nombre"], $arraySelected2) === FALSE){
                $arrayCamposProyecto[(string)$innerArray["id"]] = $innerArray["nombre"];
            }
            
		}

		$arraySelected[] = "fijo_8";
		$arraySelected2["fijo_8"] = lang("requires_monitoring");
		$array_campos_ocupados[] = "fijo_8";
		
		$arraySelected[] = "fijo_9";
		$arraySelected2["fijo_9"] = lang("responsible");
		$array_campos_ocupados[] = "fijo_9";

        $array_final = $arraySelected2 + $arrayCamposProyecto;
        
        $info = (count($array_campos_ocupados) > 0) ? '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_compromise_fields_info').'"><i class="fa fa-question-circle"></i></span>' : '';
        $html = '';
        $html .= '<div class="form-group">';
            $html .= '<label for="fields" class="col-md-3">'.lang('fields').' '.$info.'</label>';
            $html .= '<div class="col-md-9">';
            $html .= '<div id="mensaje_validacion_campos" class="pb10"></div>';
            $html .= form_multiselect("fields[]", $array_final, $arraySelected, "id='fields' class='multiple' multiple='multiple' data-rule-required='false', data-msg-required='" . lang('field_required') . "'", $array_campos_ocupados);
            $html .= '</div>';
        $html .= '</div>';
        
        echo $html;

    ?>
        
</div>

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
		//$('#clientes').select2();	
		
		$('#fields').multiSelect({
			selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
			selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
			keepOrder: true,
			afterSelect: function(value){
				var text = $('#fields option[value="'+value+'"]').text();
				$('#fields option[value="'+value+'"]').remove();
				//$('#fields').append($("<option></option>").attr("value", value).attr('selected', 'selected'));
				($("<option>"+text+"</option>").attr("value", value).attr('selected', 'selected')).insertBefore($('#fields option[value="fijo_8"]'));
				
				$('#fields option[value="fijo_8"]').prop("disabled", false);
				$('#fields').multiSelect('refresh');
				$('#fields option[value="fijo_8"]').prop("disabled", true);
				$('#fields').multiSelect('refresh');
				
				$('#fields option[value="fijo_9"]').prop("disabled", false);
				$('#fields').multiSelect('refresh');
				$('#fields option[value="fijo_9"]').prop("disabled", true);
				$('#fields').multiSelect('refresh');
				
			},
			afterDeselect: function(value){ 
				$('#fields option[value="'+value+'"]').removeAttr('selected');
			}
		});
		
		$('#guardar_matriz').click(function(event){
			
			event.preventDefault();

			var campos_seleccionados = true;
			$('#fields option').each(function() {
				if ($(this).is(':selected')) {
					//alert("si");
					campos_seleccionados = true;
					return false;
				} else {
					//alert("no");
					campos_seleccionados = false;
				}
			});
			
			if(!campos_seleccionados){
				$('#mensaje_validacion_campos').html('<span style="color: #ec5855;">' + '<?php echo lang("field_required"); ?>' + '</span>');
			} else {
				$('#mensaje_validacion_campos').html('');
				$('#matrix_config-form').submit();
			}

		});

    });
</script>