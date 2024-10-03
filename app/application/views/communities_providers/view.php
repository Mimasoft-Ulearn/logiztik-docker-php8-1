<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

    <div class="form-group">
        <label for="date" class="<?php echo $label_column; ?>"><?php echo lang('date'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo get_date_format($model_info->date, $model_info->id_project); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->name; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="responsible_name" class="<?php echo $label_column; ?>"><?php echo lang('responsible-name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->responsible_name; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="responsible_email" class="<?php echo $label_column; ?>"><?php echo lang('responsible-email'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->responsible_email; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="ethical_social_audit" class="<?php echo $label_column; ?>"><?php echo lang('do_you_have_an_ethical_social_audit'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->ethical_social_audit); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="ethical_social_audit_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_ethical_audit_social'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
                if($model_info->ethical_social_audit_file) { 
					$html = '<div class="col-md-8">';
					$html .= remove_file_prefix($model_info->ethical_social_audit_file);
					$html .= '</div>';
					$html .= '<div class="col-md-4">';
					$html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
                    $html .= anchor(get_uri("communities_providers/download_file/".$model_info->id."/ethical_social_audit_file"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));		
					$html .= '</td>';
					$html .= '</tr>';
					$html .= '</thead>';
					$html .= '</table>';
					$html .= '</div>';
				} else {
					$html = "-";
                } 
                
                echo $html;
            ?>
        </div>
    </div>


    <div class="form-group">
        <label for="do_you_have_a_non-discrimination_policy" class="<?php echo $label_column; ?>"><?php echo lang('do_you_have_a_non-discrimination_policy'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->non_discrimination_policy); ?>
        </div>
    </div>

    <div id="non_discrimination_policy_file_group">
        <div class="form-group">
            <label for="non_discrimination_policy_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_non_discrimination_policy'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                    if($model_info->non_discrimination_policy_file) { 
                        $html = '<div class="col-md-8">';
                        $html .= remove_file_prefix($model_info->non_discrimination_policy_file);
                        $html .= '</div>';
                        $html .= '<div class="col-md-4">';
                        $html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
                        $html .= '<tbody><tr><td class="option text-center">';
                        $html .= anchor(get_uri("communities_providers/download_file/".$model_info->id."/non_discrimination_policy_file"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
                        $html .= '</td>';
                        $html .= '</tr>';
                        $html .= '</thead>';
                        $html .= '</table>';
                        $html .= '</div>';
                    } else {
                        $html = "-";
                    } 
                    
                    echo $html;
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="do_you_have_an_anti_corruption_and_transparency_policy" class="<?php echo $label_column; ?>"><?php echo lang('do_you_have_an_anti_corruption_and_transparency_policy'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->anti_corruption_and_transparency_policy); ?>
        </div>
    </div>

    <div id="anti_corruption_and_transparency_policy_file_group">
        <div class="form-group">
            <label for="anti_corruption_and_transparency_policy_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_anti_corruption_and_transparency_policy'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                    if($model_info->anti_corruption_and_transparency_policy_file) { 
                        $html = '<div class="col-md-8">';
                        $html .= remove_file_prefix($model_info->anti_corruption_and_transparency_policy_file);
                        $html .= '</div>';
                        $html .= '<div class="col-md-4">';
                        $html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
                        $html .= '<tbody><tr><td class="option text-center">';
                        $html .= anchor(get_uri("communities_providers/download_file/".$model_info->id."/anti_corruption_and_transparency_policy_file"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
                        $html .= '</td>';
                        $html .= '</tr>';
                        $html .= '</thead>';
                        $html .= '</table>';
                        $html .= '</div>';
                    } else {
                        $html = "-";
                    } 
                    
                    echo $html;
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="environmental_policy" class="<?php echo $label_column; ?>"><?php echo lang('do_you_have_an_environmental_policy'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->environmental_policy); ?>
        </div>
    </div>

    <div id="environmental_policy_file_group">
        <div class="form-group">
            <label for="environmental_policy_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_environmental_policy'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                    if($model_info->environmental_policy_file) { 
                        $html = '<div class="col-md-8">';
                        $html .= remove_file_prefix($model_info->environmental_policy_file);
                        $html .= '</div>';
                        $html .= '<div class="col-md-4">';
                        $html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
                        $html .= '<tbody><tr><td class="option text-center">';
                        $html .= anchor(get_uri("communities_providers/download_file/".$model_info->id."/environmental_policy_file"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
                        $html .= '</td>';
                        $html .= '</tr>';
                        $html .= '</thead>';
                        $html .= '</table>';
                        $html .= '</div>';
                    } else {
                        $html = "-";
                    } 
                    
                    echo $html;
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="promote_free_assoc_and_neg_rights" class="<?php echo $label_column; ?>"><?php echo lang('promote_free_association_and_negotiation_rights'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->promote_free_assoc_and_neg_rights); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="comply_with_national_legislation" class="<?php echo $label_column; ?>"><?php echo lang('comply_with_national_legislation'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->comply_with_national_legislation); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="workers_subjected_to_forced_labor" class="<?php echo $label_column; ?>"><?php echo lang('are_workers_subjected_to_forced_labor'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->workers_subjected_to_forced_labor); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="workers_minimum_age" class="<?php echo $label_column; ?>"><?php echo lang('minimum_age_of_your_workers'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->workers_minimum_age ? $model_info->workers_minimum_age : "-"; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="workers_lower_remuneration" class="<?php echo $label_column; ?>"><?php echo lang('what_is_the_lower_remuneration_of_your_workers'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->workers_lower_remuneration ? $model_info->workers_lower_remuneration : "-"; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="max_hours_worked_per_week" class="<?php echo $label_column; ?>"><?php echo lang('maximum_hours_worked_per_week'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->max_hours_worked_per_week ? $model_info->max_hours_worked_per_week : "-"; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="overtime" class="<?php echo $label_column; ?>"><?php echo lang('it_does_overtime'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->overtime); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="max_overtime_hours_per_week" class="<?php echo $label_column; ?>"><?php echo lang('indicate_max_overtime_hours_per_week'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->max_overtime_hours_per_week ? $model_info->max_overtime_hours_per_week : "-"; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="employ_emmigrants" class="<?php echo $label_column; ?>"><?php echo lang('do_you_employ_emmigrants'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->employ_emmigrants); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="ethical_policy_oit" class="<?php echo $label_column; ?>"><?php echo lang('do_you_have_ethical_policy_based_on_oit'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->ethical_policy_oit); ?>
        </div>
    </div>

    <div id="ethical_policy_oit_file_group">
        <div class="form-group">
            <label for="ethical_policy_oit_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_ethical_policy_based_on_oit'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                    if($model_info->ethical_policy_oit_file) { 
                        $html = '<div class="col-md-8">';
                        $html .= remove_file_prefix($model_info->ethical_policy_oit_file);
                        $html .= '</div>';
                        $html .= '<div class="col-md-4">';
                        $html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
                        $html .= '<tbody><tr><td class="option text-center">';
                        $html .= anchor(get_uri("communities_providers/download_file/".$model_info->id."/ethical_policy_oit_file"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
                        $html .= '</td>';
                        $html .= '</tr>';
                        $html .= '</thead>';
                        $html .= '</table>';
                        $html .= '</div>';
                    } else {
                        $html = "-";
                    } 
                    
                    echo $html;
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="comply_hygiene_and_safety_conditions" class="<?php echo $label_column; ?>"><?php echo lang('do_you_comply_with_hygiene_and_safety_conditions_at_work'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->comply_hygiene_and_safety_conditions); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="attach_accident_report" class="<?php echo $label_column; ?>"><?php echo lang('attach_accident_report'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
                if($model_info->accident_report_file) { 
                    $html = '<div class="col-md-8">';
                    $html .= remove_file_prefix($model_info->accident_report_file);
                    $html .= '</div>';
                    $html .= '<div class="col-md-4">';
                    $html .= '<table id="" class="table_delete"><thead><tr><th></th></tr></thead>';
                    $html .= '<tbody><tr><td class="option text-center">';
                    $html .= anchor(get_uri("communities_providers/download_file/".$model_info->id."/accident_report_file"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '</table>';
                    $html .= '</div>';
                } else {
                    $html = "-";
                } 
                
                echo $html;
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="risk_prevention_specialist" class="<?php echo $label_column; ?>"><?php echo lang('do_you_have_risk_prevention_specialist'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->risk_prevention_specialist); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="mention_measures_taken_to_prevent_covid_19" class="<?php echo $label_column; ?>"><?php echo lang('mention_measures_taken_to_prevent_covid_19'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->mention_measures_taken_to_prevent_covid_19 ? $model_info->mention_measures_taken_to_prevent_covid_19 : "-"; ?>
        </div>
    </div>
    

    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->modified)?$model_info->modified:'-';
            ?>
        </div>
    </div>
	
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

</script> 