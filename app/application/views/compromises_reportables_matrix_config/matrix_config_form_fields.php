<input type="hidden" name="id" value="<?php echo $id_proyecto; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

    <div class="form-group">
		
		<?php
    
            $arraySelected = array();
            $arraySelected2 = array();
            $arrayCamposProyecto = array();
			
			$arraySelected[] = "fijo_1";
			$arraySelected2["fijo_1"] = lang("n_activity");
			$array_campos_ocupados[] = "fijo_1";

			$arraySelected[] = "fijo_2";
			$arraySelected2["fijo_2"] = lang("environmental_management_instrument");
			$array_campos_ocupados[] = "fijo_2";
			
			$arraySelected[] = "fijo_3";
			$arraySelected2["fijo_3"] = lang("compliance_type");
			$array_campos_ocupados[] = "fijo_3";

			$arraySelected[] = "fijo_4";
			$arraySelected2["fijo_4"] = lang("environmental_topic");
			$array_campos_ocupados[] = "fijo_4";

			$arraySelected[] = "fijo_5";
			$arraySelected2["fijo_5"] = lang("impact_on_the_environment_due_to_non_compliance");
			$array_campos_ocupados[] = "fijo_5";

			$arraySelected[] = "fijo_6";
			$arraySelected2["fijo_6"] = lang("action_type");
			$array_campos_ocupados[] = "fijo_6";
			
			$arraySelected[] = "fijo_7";
			$arraySelected2["fijo_7"] = lang("responsible_area");
			$array_campos_ocupados[] = "fijo_7";
			
			$arraySelected[] = "fijo_8";
			$arraySelected2["fijo_8"] = lang("commitment_description");
			$array_campos_ocupados[] = "fijo_8";
		

            foreach($campos_compromiso as $innerArray){
                $arraySelected[] = $innerArray["id"];
                $arraySelected2[(string)$innerArray["id"]] = $innerArray["nombre"];
            }

            foreach($campos_proyecto as $innerArray){
                if(array_search($innerArray["nombre"], $arraySelected2) === FALSE){
                    $arrayCamposProyecto[(string)$innerArray["id"]] = $innerArray["nombre"];
                }
			}

			/* $arraySelected[] = "fijo_8";
			$arraySelected2["fijo_8"] = lang("verification_planning_activity");
			$array_campos_ocupados[] = "fijo_8"; */
			
			/*$arraySelected[] = "fijo_7";
			$arraySelected2["fijo_7"] = lang("activity_planning");
			$array_campos_ocupados[] = "fijo_7";

			$arraySelected[] = "fijo_9";
			$arraySelected2["fijo_9"] = lang("planning_end_date");
			$array_campos_ocupados[] = "fijo_9";*/

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
		
		$('input[type="text"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$('#fields').multiSelect({
			selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_fields"); ?>" + "</div>",
			selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_fields"); ?>" + "</div>",
			keepOrder: true,
			afterSelect: function(value){
				var text = $('#fields option[value="'+value+'"]').text();
				$('#fields option[value="'+value+'"]').remove();
				$('#fields').append($("<option></option>").attr("value", value).attr('selected', 'selected'));
				/*($("<option>"+text+"</option>").attr("value", value).attr('selected', 'selected')).insertBefore($('#fields option[value="fijo_7"]'));
				
				$('#fields option[value="fijo_7"]').prop("disabled", false);
				$('#fields').multiSelect('refresh');
				$('#fields option[value="fijo_7"]').prop("disabled", true);
				$('#fields').multiSelect('refresh');
				
				$('#fields option[value="fijo_8"]').prop("disabled", false);
				$('#fields').multiSelect('refresh');
				$('#fields option[value="fijo_8"]').prop("disabled", true);
				$('#fields').multiSelect('refresh');*/
				
			},
			afterDeselect: function(value){ 
				$('#fields option[value="'+value+'"]').removeAttr('selected');
			}
			
		});


		
		
    });
</script>