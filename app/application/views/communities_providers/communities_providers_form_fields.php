<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
	<label for="date" class="<?php echo $label_column; ?>"><?php echo lang('date'); ?></label>
	<div class="<?php echo $field_column; ?>">
        <?php
		echo form_input(array(
			"id" => "date",
			"name" => "date",
			"value" => $model_info->date,
			"class" => "form-control datepicker",
			"placeholder" => lang('date'),
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
		));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "name",
            "name" => "name",
            "value" => $model_info->name,
            "class" => "form-control",
            "placeholder" => lang('name'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="responsible_name" class="<?php echo $label_column; ?>"><?php echo lang('responsible-name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "responsible_name",
            "name" => "responsible_name",
            "value" => $model_info->responsible_name,
            "class" => "form-control",
            "placeholder" => lang('responsible-name'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="responsible_email" class="<?php echo $label_column; ?>"><?php echo lang('responsible-email'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_input(array(
            "id" => "responsible_email",
            "name" => "responsible_email",
            "value" => $model_info->responsible_email,
            "class" => "form-control",
            "placeholder" => lang('responsible-email'),
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
    <label for="gender" class="<?php echo $label_column; ?>"><?php echo lang('do_you_have_an_ethical_social_audit'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "ethical_social_audit_yes",
            "name" => "ethical_social_audit",
            "data-msg-required" => lang("field_required"),
                ), "yes", ($model_info->ethical_social_audit == "no") ? false : true);
        ?>
        <label for="ethical_social_audit_yes" class="mr15"><?php echo lang('yes'); ?></label> 
		<?php
        echo form_radio(array(
            "id" => "ethical_social_audit_no",
            "name" => "ethical_social_audit",
            "data-msg-required" => lang("field_required"),
                ), "no", ($model_info->ethical_social_audit == "no") ? true : false);
        ?>
        <label for="ethical_social_audit_no" class=""><?php echo lang('no'); ?></label>
    </div>
</div>

<div id="ethical_social_audit_file_group">
    <div class="form-group">
        <label for="ethical_social_audit_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_ethical_audit_social'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php

                if(!$model_info->ethical_social_audit_file){
				
                    echo $this->load->view("includes/form_file_uploader", array(
                        "upload_url" =>get_uri("communities_providers/upload_file"),
                        "validation_url" =>get_uri("communities_providers/validate_file"),
                        "html_name" => "ethical_social_audit_file",
                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                        "obligatorio" => "",
                        "id_campo" => "ethical_social_audit_file",
                    ),
                    true);
                    
                } else {
                    
                    $html = '<div id="table_delete_ethical_social_audit_file">';
                    $html .= '<div class="col-md-8">';
                    $html .= remove_file_prefix($model_info->ethical_social_audit_file);
                    $html .= '</div>';
                    
                    $html .= '<div class="col-md-4">';
                    $html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
                    $html .= '<tbody><tr><td class="option text-center">';
                    $html .= anchor(get_uri("communities_providers/download_file/".$model_info->id."/ethical_social_audit_file"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
                    $html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => "", "data-id" => $model_info->id, "data-campo" => "ethical_social_audit_file", "data-action-url" => get_uri("communities_providers/delete_file"), "data-action" => "delete-fileConfirmation"));
                    //$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';	
                   // $html .= '<input type="hidden" name="'.$name.'_unchange" value="1" />';			
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '</table>';
                    $html .= '</div>';
                    
                    $html .= '</div>';
                    
                    echo $html;
                    
                }

            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="do_you_have_a_non-discrimination_policy" class="<?php echo $label_column; ?>"><?php echo lang('do_you_have_a_non-discrimination_policy'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "non_discrimination_policy_yes",
            "name" => "non_discrimination_policy",
            "data-msg-required" => lang("field_required"),
                ), "yes", ($model_info->non_discrimination_policy == "no") ? false : true);
        ?>
        <label for="non_discrimination_policy_yes" class="mr15"><?php echo lang('yes'); ?></label> 
		<?php
        echo form_radio(array(
            "id" => "non_discrimination_policy_no",
            "name" => "non_discrimination_policy",
            "data-msg-required" => lang("field_required"),
                ), "no", ($model_info->non_discrimination_policy == "no") ? true : false);
        ?>
        <label for="non_discrimination_policy_no" class=""><?php echo lang('no'); ?></label>
    </div>
</div>

<div id="non_discrimination_policy_file_group">
    <div class="form-group">
        <label for="non_discrimination_policy_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_non_discrimination_policy'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
               
                if(!$model_info->non_discrimination_policy_file){
				
                    echo $this->load->view("includes/form_file_uploader", array(
                        "upload_url" =>get_uri("communities_providers/upload_file"),
                        "validation_url" =>get_uri("communities_providers/validate_file"),
                        "html_name" => "non_discrimination_policy_file",
                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                        "obligatorio" => "",
                        "id_campo" => "non_discrimination_policy_file",
                    ),
                    true);
                    
                } else {
                    
                    $html = '<div id="table_delete_non_discrimination_policy_file">';
                    $html .= '<div class="col-md-8">';
                    $html .= remove_file_prefix($model_info->non_discrimination_policy_file);
                    $html .= '</div>';
                    
                    $html .= '<div class="col-md-4">';
                    $html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
                    $html .= '<tbody><tr><td class="option text-center">';
                    $html .= anchor(get_uri("communities_providers/download_file/".$model_info->id."/non_discrimination_policy_file"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
                    $html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => "", "data-id" => $model_info->id, "data-campo" => "non_discrimination_policy_file", "data-action-url" => get_uri("communities_providers/delete_file"), "data-action" => "delete-fileConfirmation"));
                    //$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';	
                   // $html .= '<input type="hidden" name="'.$name.'_unchange" value="1" />';			
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '</table>';
                    $html .= '</div>';
                    
                    $html .= '</div>';
                    
                    echo $html;
                    
                }

            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="do_you_have_an_anti_corruption_and_transparency_policy" class="<?php echo $label_column; ?>"><?php echo lang('do_you_have_an_anti_corruption_and_transparency_policy'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "anti_corruption_and_transparency_policy_yes",
            "name" => "anti_corruption_and_transparency_policy",
            "data-msg-required" => lang("field_required"),
                ), "yes", ($model_info->anti_corruption_and_transparency_policy == "no") ? false : true);
        ?>
        <label for="anti_corruption_and_transparency_policy_yes" class="mr15"><?php echo lang('yes'); ?></label> 
		<?php
        echo form_radio(array(
            "id" => "anti_corruption_and_transparency_policy_no",
            "name" => "anti_corruption_and_transparency_policy",
            "data-msg-required" => lang("field_required"),
                ), "no", ($model_info->anti_corruption_and_transparency_policy == "no") ? true : false);
        ?>
        <label for="anti_corruption_and_transparency_policy_no" class=""><?php echo lang('no'); ?></label>
    </div>
</div>

<div id="anti_corruption_and_transparency_policy_file_group">
    <div class="form-group">
        <label for="anti_corruption_and_transparency_policy_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_anti_corruption_and_transparency_policy'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
               
                if(!$model_info->anti_corruption_and_transparency_policy_file){
				
                    echo $this->load->view("includes/form_file_uploader", array(
                        "upload_url" =>get_uri("communities_providers/upload_file"),
                        "validation_url" =>get_uri("communities_providers/validate_file"),
                        "html_name" => "anti_corruption_and_transparency_policy_file",
                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                        "obligatorio" => "",
                        "id_campo" => "anti_corruption_and_transparency_policy_file",
                    ),
                    true);
                    
                } else {
                    
                    $html = '<div id="table_delete_anti_corruption_and_transparency_policy_file">';
                    $html .= '<div class="col-md-8">';
                    $html .= remove_file_prefix($model_info->anti_corruption_and_transparency_policy_file);
                    $html .= '</div>';
                    
                    $html .= '<div class="col-md-4">';
                    $html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
                    $html .= '<tbody><tr><td class="option text-center">';
                    $html .= anchor(get_uri("communities_providers/download_file/".$model_info->id."/anti_corruption_and_transparency_policy_file"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
                    $html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => "", "data-id" => $model_info->id, "data-campo" => "anti_corruption_and_transparency_policy_file", "data-action-url" => get_uri("communities_providers/delete_file"), "data-action" => "delete-fileConfirmation"));
                    //$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';	
                   // $html .= '<input type="hidden" name="'.$name.'_unchange" value="1" />';			
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '</table>';
                    $html .= '</div>';
                    
                    $html .= '</div>';
                    
                    echo $html;
                    
                }

            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="environmental_policy" class="<?php echo $label_column; ?>"><?php echo lang('do_you_have_an_environmental_policy'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "environmental_policy_yes",
            "name" => "environmental_policy",
            "data-msg-required" => lang("field_required"),
                ), "yes", ($model_info->environmental_policy == "no") ? false : true);
        ?>
        <label for="environmental_policy_yes" class="mr15"><?php echo lang('yes'); ?></label> 
		<?php
        echo form_radio(array(
            "id" => "environmental_policy_no",
            "name" => "environmental_policy",
            "data-msg-required" => lang("field_required"),
                ), "no", ($model_info->environmental_policy == "no") ? true : false);
        ?>
        <label for="environmental_policy_no" class=""><?php echo lang('no'); ?></label>
    </div>
</div>

<div id="environmental_policy_file_group">
    <div class="form-group">
        <label for="environmental_policy_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_environmental_policy'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
                
                if(!$model_info->environmental_policy_file){
				
                    echo $this->load->view("includes/form_file_uploader", array(
                        "upload_url" =>get_uri("communities_providers/upload_file"),
                        "validation_url" =>get_uri("communities_providers/validate_file"),
                        "html_name" => "environmental_policy_file",
                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                        "obligatorio" => "",
                        "id_campo" => "environmental_policy_file",
                    ),
                    true);
                    
                } else {
                    
                    $html = '<div id="table_delete_environmental_policy_file">';
                    $html .= '<div class="col-md-8">';
                    $html .= remove_file_prefix($model_info->environmental_policy_file);
                    $html .= '</div>';
                    
                    $html .= '<div class="col-md-4">';
                    $html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
                    $html .= '<tbody><tr><td class="option text-center">';
                    $html .= anchor(get_uri("communities_providers/download_file/".$model_info->id."/environmental_policy_file"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
                    $html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => "", "data-id" => $model_info->id, "data-campo" => "environmental_policy_file", "data-action-url" => get_uri("communities_providers/delete_file"), "data-action" => "delete-fileConfirmation"));
                    //$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';	
                   // $html .= '<input type="hidden" name="'.$name.'_unchange" value="1" />';			
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '</table>';
                    $html .= '</div>';
                    
                    $html .= '</div>';
                    
                    echo $html;
                    
                }

            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="promote_free_assoc_and_neg_rights" class="<?php echo $label_column; ?>"><?php echo lang('promote_free_association_and_negotiation_rights'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "promote_free_assoc_and_neg_rights_yes",
            "name" => "promote_free_assoc_and_neg_rights",
            "data-msg-required" => lang("field_required"),
                ), "yes", ($model_info->promote_free_assoc_and_neg_rights == "no") ? false : true);
        ?>
        <label for="promote_free_assoc_and_neg_rights_yes" class="mr15"><?php echo lang('yes'); ?></label> 
		<?php
        echo form_radio(array(
            "id" => "promote_free_assoc_and_neg_rights_no",
            "name" => "promote_free_assoc_and_neg_rights",
            "data-msg-required" => lang("field_required"),
                ), "no", ($model_info->promote_free_assoc_and_neg_rights == "no") ? true : false);
        ?>
        <label for="promote_free_assoc_and_neg_rights_no" class=""><?php echo lang('no'); ?></label>
    </div>
</div>

<div class="form-group">
    <label for="comply_with_national_legislation" class="<?php echo $label_column; ?>"><?php echo lang('comply_with_national_legislation'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "comply_with_national_legislation_yes",
            "name" => "comply_with_national_legislation",
            "data-msg-required" => lang("field_required"),
                ), "yes", ($model_info->comply_with_national_legislation == "no") ? false : true);
        ?>
        <label for="comply_with_national_legislation_yes" class="mr15"><?php echo lang('yes'); ?></label> 
		<?php
        echo form_radio(array(
            "id" => "comply_with_national_legislation_no",
            "name" => "comply_with_national_legislation",
            "data-msg-required" => lang("field_required"),
                ), "no", ($model_info->comply_with_national_legislation == "no") ? true : false);
        ?>
        <label for="comply_with_national_legislation_no" class=""><?php echo lang('no'); ?></label>
    </div>
</div>

<div class="form-group">
    <label for="workers_subjected_to_forced_labor" class="<?php echo $label_column; ?>"><?php echo lang('are_workers_subjected_to_forced_labor'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "workers_subjected_to_forced_labor_yes",
            "name" => "workers_subjected_to_forced_labor",
            "data-msg-required" => lang("field_required"),
                ), "yes", ($model_info->workers_subjected_to_forced_labor == "no") ? false : true);
        ?>
        <label for="workers_subjected_to_forced_labor_yes" class="mr15"><?php echo lang('yes'); ?></label> 
		<?php
        echo form_radio(array(
            "id" => "workers_subjected_to_forced_labor_no",
            "name" => "workers_subjected_to_forced_labor",
            "data-msg-required" => lang("field_required"),
                ), "no", ($model_info->workers_subjected_to_forced_labor == "no") ? true : false);
        ?>
        <label for="workers_subjected_to_forced_labor_no" class=""><?php echo lang('no'); ?></label>
    </div>
</div>

<div class="form-group">
    <label for="workers_minimum_age" class="<?php echo $label_column; ?>"><?php echo lang('minimum_age_of_your_workers'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "workers_minimum_age",
            "name" => "workers_minimum_age",
            "value" => $model_info->workers_minimum_age,
            "class" => "form-control",
            "placeholder" => lang('minimum_age_of_your_workers'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="workers_lower_remuneration" class="<?php echo $label_column; ?>"><?php echo lang('what_is_the_lower_remuneration_of_your_workers'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "workers_lower_remuneration",
            "name" => "workers_lower_remuneration",
            "value" => $model_info->workers_lower_remuneration,
            "class" => "form-control",
            "placeholder" => lang('what_is_the_lower_remuneration_of_your_workers'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="max_hours_worked_per_week" class="<?php echo $label_column; ?>"><?php echo lang('maximum_hours_worked_per_week'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "max_hours_worked_per_week",
            "name" => "max_hours_worked_per_week",
            "value" => $model_info->max_hours_worked_per_week,
            "class" => "form-control",
            "placeholder" => lang('maximum_hours_worked_per_week'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="overtime" class="<?php echo $label_column; ?>"><?php echo lang('it_does_overtime'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "overtime_yes",
            "name" => "overtime",
            "data-msg-required" => lang("field_required"),
                ), "yes", ($model_info->overtime == "no") ? false : true);
        ?>
        <label for="overtime_yes" class="mr15"><?php echo lang('yes'); ?></label> 
		<?php
        echo form_radio(array(
            "id" => "overtime_no",
            "name" => "overtime",
            "data-msg-required" => lang("field_required"),
                ), "no", ($model_info->overtime == "no") ? true : false);
        ?>
        <label for="overtime_no" class=""><?php echo lang('no'); ?></label>
    </div>
</div>

<div id="max_overtime_hours_per_week_group">
    <div class="form-group">
        <label for="max_overtime_hours_per_week" class="<?php echo $label_column; ?>"><?php echo lang('indicate_max_overtime_hours_per_week'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "max_overtime_hours_per_week",
                "name" => "max_overtime_hours_per_week",
                "value" => $model_info->max_overtime_hours_per_week,
                "class" => "form-control",
                "placeholder" => lang('indicate_max_overtime_hours_per_week'),
                //"autofocus" => true,
                //"data-rule-required" => true,
                //"data-msg-required" => lang("field_required"),
                "autocomplete"=> "off",
                "maxlength" => "255"
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="employ_emmigrants" class="<?php echo $label_column; ?>"><?php echo lang('do_you_employ_emmigrants'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "employ_emmigrants_yes",
            "name" => "employ_emmigrants",
            "data-msg-required" => lang("field_required"),
                ), "yes", ($model_info->employ_emmigrants == "no") ? false : true);
        ?>
        <label for="employ_emmigrants_yes" class="mr15"><?php echo lang('yes'); ?></label> 
		<?php
        echo form_radio(array(
            "id" => "employ_emmigrants_no",
            "name" => "employ_emmigrants",
            "data-msg-required" => lang("field_required"),
                ), "no", ($model_info->employ_emmigrants == "no") ? true : false);
        ?>
        <label for="employ_emmigrants_no" class=""><?php echo lang('no'); ?></label>
    </div>
</div>

<div class="form-group">
    <label for="ethical_policy_oit" class="<?php echo $label_column; ?>"><?php echo lang('do_you_have_ethical_policy_based_on_oit'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "ethical_policy_oit_yes",
            "name" => "ethical_policy_oit",
            "data-msg-required" => lang("field_required"),
                ), "yes", ($model_info->ethical_policy_oit == "no") ? false : true);
        ?>
        <label for="ethical_policy_oit_yes" class="mr15"><?php echo lang('yes'); ?></label> 
		<?php
        echo form_radio(array(
            "id" => "ethical_policy_oit_no",
            "name" => "ethical_policy_oit",
            "data-msg-required" => lang("field_required"),
                ), "no", ($model_info->ethical_policy_oit == "no") ? true : false);
        ?>
        <label for="ethical_policy_oit_no" class=""><?php echo lang('no'); ?></label>
    </div>
</div>

<div id="ethical_policy_oit_file_group">
    <div class="form-group">
        <label for="ethical_policy_oit_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_ethical_policy_based_on_oit'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php

                if(!$model_info->ethical_policy_oit_file){
				
                    echo $this->load->view("includes/form_file_uploader", array(
                        "upload_url" =>get_uri("communities_providers/upload_file"),
                        "validation_url" =>get_uri("communities_providers/validate_file"),
                        "html_name" => "ethical_policy_oit_file",
                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                        "obligatorio" => "",
                        "id_campo" => "ethical_policy_oit_file",
                    ),
                    true);
                    
                } else {
                    
                    $html = '<div id="table_delete_ethical_policy_oit_file">';
                    $html .= '<div class="col-md-8">';
                    $html .= remove_file_prefix($model_info->ethical_policy_oit_file);
                    $html .= '</div>';
                    
                    $html .= '<div class="col-md-4">';
                    $html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
                    $html .= '<tbody><tr><td class="option text-center">';
                    $html .= anchor(get_uri("communities_providers/download_file/".$model_info->id."/ethical_policy_oit_file"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
                    $html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => "", "data-id" => $model_info->id, "data-campo" => "ethical_policy_oit_file", "data-action-url" => get_uri("communities_providers/delete_file"), "data-action" => "delete-fileConfirmation"));
                    //$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';	
                   // $html .= '<input type="hidden" name="'.$name.'_unchange" value="1" />';			
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '</table>';
                    $html .= '</div>';
                    
                    $html .= '</div>';
                    
                    echo $html;
                    
                }

            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="comply_hygiene_and_safety_conditions" class="<?php echo $label_column; ?>"><?php echo lang('do_you_comply_with_hygiene_and_safety_conditions_at_work'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "comply_hygiene_and_safety_conditions_yes",
            "name" => "comply_hygiene_and_safety_conditions",
            "data-msg-required" => lang("field_required"),
                ), "yes", ($model_info->comply_hygiene_and_safety_conditions == "no") ? false : true);
        ?>
        <label for="comply_hygiene_and_safety_conditions_yes" class="mr15"><?php echo lang('yes'); ?></label> 
		<?php
        echo form_radio(array(
            "id" => "comply_hygiene_and_safety_conditions_no",
            "name" => "comply_hygiene_and_safety_conditions",
            "data-msg-required" => lang("field_required"),
                ), "no", ($model_info->comply_hygiene_and_safety_conditions == "no") ? true : false);
        ?>
        <label for="comply_hygiene_and_safety_conditions_no" class=""><?php echo lang('no'); ?></label>
    </div>
</div>

<div class="form-group">
    <label for="attach_accident_report" class="<?php echo $label_column; ?>"><?php echo lang('attach_accident_report'); ?></label>
    <div class="<?php echo $field_column; ?>">
		<?php

            if(!$model_info->accident_report_file){
				
                echo $this->load->view("includes/form_file_uploader", array(
                    "upload_url" =>get_uri("communities_providers/upload_file"),
                    "validation_url" =>get_uri("communities_providers/validate_file"),
                    "html_name" => "accident_report_file",
                    //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                    "obligatorio" => "",
                    "id_campo" => "accident_report_file",
                ),
                true);
                
            } else {
                
                $html = '<div id="table_delete_accident_report_file">';
                $html .= '<div class="col-md-8">';
                $html .= remove_file_prefix($model_info->accident_report_file);
                $html .= '</div>';
                
                $html .= '<div class="col-md-4">';
                $html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
                $html .= '<tbody><tr><td class="option text-center">';
                $html .= anchor(get_uri("communities_providers/download_file/".$model_info->id."/accident_report_file"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
                $html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => "", "data-id" => $model_info->id, "data-campo" => "accident_report_file", "data-action-url" => get_uri("communities_providers/delete_file"), "data-action" => "delete-fileConfirmation"));
                //$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';	
               // $html .= '<input type="hidden" name="'.$name.'_unchange" value="1" />';			
                $html .= '</td>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '</table>';
                $html .= '</div>';
                
                $html .= '</div>';
                
                echo $html;
                
            }

		?>
	</div>
</div>

<div class="form-group">
    <label for="risk_prevention_specialist" class="<?php echo $label_column; ?>"><?php echo lang('do_you_have_risk_prevention_specialist'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "risk_prevention_specialist_yes",
            "name" => "risk_prevention_specialist",
            "data-msg-required" => lang("field_required"),
                ), "yes", ($model_info->risk_prevention_specialist == "no") ? false : true);
        ?>
        <label for="risk_prevention_specialist_yes" class="mr15"><?php echo lang('yes'); ?></label> 
		<?php
        echo form_radio(array(
            "id" => "risk_prevention_specialist_no",
            "name" => "risk_prevention_specialist",
            "data-msg-required" => lang("field_required"),
                ), "no", ($model_info->risk_prevention_specialist == "no") ? true : false);
        ?>
        <label for="risk_prevention_specialist_no" class=""><?php echo lang('no'); ?></label>
    </div>
</div>

<div class="form-group">
    <label for="mention_measures_taken_to_prevent_covid_19" class="<?php echo $label_column; ?>"><?php echo lang('mention_measures_taken_to_prevent_covid_19'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "mention_measures_taken_to_prevent_covid_19",
            "name" => "mention_measures_taken_to_prevent_covid_19",
            "value" => $model_info->mention_measures_taken_to_prevent_covid_19,
            "class" => "form-control",
            "placeholder" => lang('mention_measures_taken_to_prevent_covid_19'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        
		$('[data-toggle="tooltip"]').tooltip();
		$('#communities_providers-form .select2').select2();
		setDatePicker("#communities_providers-form .datepicker");
		
		$('input[type="text"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});

        $('input[type=radio][name=ethical_social_audit]').change(function(){		
			
			var ethical_social_audit = $(this).val();
            $.ajax({
                url:  '<?php echo_uri("communities_providers/get_fields_ethical_social_audit"); ?>',
                type:  'post',
                data: {ethical_social_audit: ethical_social_audit},
                success: function(respuesta){
                    $('#ethical_social_audit_file_group').html(respuesta);
                }
            });

		});

        $('input[type=radio][name=non_discrimination_policy]').change(function(){		
			
			var non_discrimination_policy = $(this).val();
            $.ajax({
                url:  '<?php echo_uri("communities_providers/get_fields_non_discrimination_policy"); ?>',
                type:  'post',
                data: {non_discrimination_policy: non_discrimination_policy},
                success: function(respuesta){
                    $('#non_discrimination_policy_file_group').html(respuesta);
                }
            });

		});

        $('input[type=radio][name=anti_corruption_and_transparency_policy]').change(function(){		
			
			var anti_corruption_and_transparency_policy = $(this).val();
            $.ajax({
                url:  '<?php echo_uri("communities_providers/get_fields_anti_corruption_and_transparency_policy"); ?>',
                type:  'post',
                data: {anti_corruption_and_transparency_policy: anti_corruption_and_transparency_policy},
                success: function(respuesta){
                    $('#anti_corruption_and_transparency_policy_file_group').html(respuesta);
                }
            });

		});

        $('input[type=radio][name=environmental_policy]').change(function(){		
			
			var environmental_policy = $(this).val();
            $.ajax({
                url:  '<?php echo_uri("communities_providers/get_fields_environmental_policy"); ?>',
                type:  'post',
                data: {environmental_policy: environmental_policy},
                success: function(respuesta){
                    $('#environmental_policy_file_group').html(respuesta);
                }
            });

		});

        $('input[type=radio][name=overtime]').change(function(){		
			
			var overtime = $(this).val();
            $.ajax({
                url:  '<?php echo_uri("communities_providers/get_fields_overtime"); ?>',
                type:  'post',
                data: {overtime: overtime},
                success: function(respuesta){
                    $('#max_overtime_hours_per_week_group').html(respuesta);
                }
            });

		});

        $('input[type=radio][name=ethical_policy_oit]').change(function(){		
			
			var ethical_policy_oit = $(this).val();
            $.ajax({
                url:  '<?php echo_uri("communities_providers/get_fields_ethical_policy_oit"); ?>',
                type:  'post',
                data: {ethical_policy_oit: ethical_policy_oit},
                success: function(respuesta){
                    $('#ethical_policy_oit_file_group').html(respuesta);
                }
            });

		});

        <?php if($model_info->ethical_social_audit == "no"){ ?>
            $("#ethical_social_audit_file_group").html("");
        <?php } ?>
        <?php if($model_info->non_discrimination_policy == "no"){ ?>
            $("#non_discrimination_policy_file_group").html("");
        <?php } ?>
        <?php if($model_info->anti_corruption_and_transparency_policy == "no"){ ?>
            $("#anti_corruption_and_transparency_policy_file_group").html("");
        <?php } ?>
        <?php if($model_info->environmental_policy == "no"){ ?>
            $("#environmental_policy_file_group").html("");
        <?php } ?>
        <?php if($model_info->overtime == "no"){ ?>
            $("#max_overtime_hours_per_week_group").html("");
        <?php } ?>
        <?php if($model_info->ethical_policy_oit == "no"){ ?>
            $("#ethical_policy_oit_file_group").html("");
        <?php } ?>
        <?php if($model_info->accident_report == "no"){ ?>
            $("#accident_report_file_file_group").html("");
        <?php } ?>


		
    });
</script>