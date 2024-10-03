<div id="page-content" class="p20 clearfix">

    <div id="view-container" class="view-container" style="margin-bottom: 50px;">
        <div class="tab-content">
            <?php echo form_open(get_uri("Public_forms/save_communities_providers"), array("id" => "communities_providers-form", "class" => "general-form dashed-row white", "role" => "form")); ?>
            <div class="panel">
                <div class="panel-default panel-heading">
                    <h4> <?php echo lang('add_provider'); ?></h4>
                </div>
                <div class="panel-body">

                    <div class="form-group">
                        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
                        <div class="<?php echo $field_column; ?>">
                            <?php
                            echo form_input(array(
                                "id" => "name",
                                "name" => "name",
                                "value" => "",
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
                                "value" => "",
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
                                "value" => "",
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
                        <label for="id_project" class="<?php echo $label_column; ?>"><?php echo lang('subsole_plant_you_work_with'); ?></label>
                        <div class="<?php echo $field_column; ?>">
                            <?php
                            echo form_dropdown("id_project", $dropdown_projects, array(), "id='id_project' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
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
                                "data-rule-required" => true,
                                "data-msg-required" => lang("field_required"),
                                    ), "yes", true);
                            ?>
                            <label for="eethical_social_audit_yes" class="mr15"><?php echo lang('yes'); ?></label> 
                            <?php
                            echo form_radio(array(
                                "id" => "ethical_social_audit_no",
                                "name" => "ethical_social_audit",
                                "data-rule-required" => true,
                                "data-msg-required" => lang("field_required"),
                                    ), "no", false);
                            ?>
                            <label for="ethical_social_audit_no" class=""><?php echo lang('no'); ?></label>
                        </div>
                    </div>

                    <div id="ethical_social_audit_file_group">
                        <div class="form-group">
                            <label for="ethical_social_audit_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_ethical_audit_social'); ?></label>
                            <div class="<?php echo $field_column; ?>">
                                <?php
                                    echo $this->load->view("includes/page_file_uploader", array(
                                        "upload_url" =>get_uri("Public_forms/upload_file"),
                                        "validation_url" =>get_uri("Public_forms/validate_file"),
                                        "html_name" => "ethical_social_audit_file",
                                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                                        "obligatorio" => "",
                                        "id_campo" => "ethical_social_audit_file",
                                    ),
                                    true);
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
                                    ), "yes", true);
                            ?>
                            <label for="non_discrimination_policy_yes" class="mr15"><?php echo lang('yes'); ?></label> 
                            <?php
                            echo form_radio(array(
                                "id" => "non_discrimination_policy_no",
                                "name" => "non_discrimination_policy",
                                "data-msg-required" => lang("field_required"),
                                    ), "no", false);
                            ?>
                            <label for="non_discrimination_policy_no" class=""><?php echo lang('no'); ?></label>
                        </div>
                    </div>

                    <div id="non_discrimination_policy_file_group">
                        <div class="form-group">
                            <label for="non_discrimination_policy_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_non_discrimination_policy'); ?></label>
                            <div class="<?php echo $field_column; ?>">
                                <?php
                                    echo $this->load->view("includes/page_file_uploader", array(
                                        "upload_url" =>get_uri("Public_forms/upload_file"),
                                        "validation_url" =>get_uri("Public_forms/validate_file"),
                                        "html_name" => "non_discrimination_policy_file",
                                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                                        "obligatorio" => "",
                                        "id_campo" => "non_discrimination_policy_file",
                                    ),
                                    true);
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
                                    ), "yes", true);
                            ?>
                            <label for="anti_corruption_and_transparency_policy_yes" class="mr15"><?php echo lang('yes'); ?></label> 
                            <?php
                            echo form_radio(array(
                                "id" => "anti_corruption_and_transparency_policy_no",
                                "name" => "anti_corruption_and_transparency_policy",
                                "data-msg-required" => lang("field_required"),
                                    ), "no", false);
                            ?>
                            <label for="anti_corruption_and_transparency_policy_no" class=""><?php echo lang('no'); ?></label>
                        </div>
                    </div>

                    <div id="anti_corruption_and_transparency_policy_file_group">
                        <div class="form-group">
                            <label for="anti_corruption_and_transparency_policy_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_anti_corruption_and_transparency_policy'); ?></label>
                            <div class="<?php echo $field_column; ?>">
                                <?php
                                    echo $this->load->view("includes/page_file_uploader", array(
                                        "upload_url" =>get_uri("Public_forms/upload_file"),
                                        "validation_url" =>get_uri("Public_forms/validate_file"),
                                        "html_name" => "anti_corruption_and_transparency_policy_file",
                                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                                        "obligatorio" => "",
                                        "id_campo" => "anti_corruption_and_transparency_policy_file",
                                    ),
                                    true);
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
                                    ), "yes", true);
                            ?>
                            <label for="environmental_policy_yes" class="mr15"><?php echo lang('yes'); ?></label> 
                            <?php
                            echo form_radio(array(
                                "id" => "environmental_policy_no",
                                "name" => "environmental_policy",
                                "data-msg-required" => lang("field_required"),
                                    ), "no", false);
                            ?>
                            <label for="environmental_policy_no" class=""><?php echo lang('no'); ?></label>
                        </div>
                    </div>

                    <div id="environmental_policy_file_group">
                        <div class="form-group">
                            <label for="environmental_policy_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_environmental_policy'); ?></label>
                            <div class="<?php echo $field_column; ?>">
                                <?php
                                    echo $this->load->view("includes/page_file_uploader", array(
                                        "upload_url" =>get_uri("Public_forms/upload_file"),
                                        "validation_url" =>get_uri("Public_forms/validate_file"),
                                        "html_name" => "environmental_policy_file",
                                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                                        "obligatorio" => "",
                                        "id_campo" => "environmental_policy_file",
                                    ),
                                    true);
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
                                    ), "yes", true);
                            ?>
                            <label for="promote_free_assoc_and_neg_rights_yes" class="mr15"><?php echo lang('yes'); ?></label> 
                            <?php
                            echo form_radio(array(
                                "id" => "promote_free_assoc_and_neg_rights_no",
                                "name" => "promote_free_assoc_and_neg_rights",
                                "data-msg-required" => lang("field_required"),
                                    ), "no", false);
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
                                    ), "yes", true);
                            ?>
                            <label for="comply_with_national_legislation_yes" class="mr15"><?php echo lang('yes'); ?></label> 
                            <?php
                            echo form_radio(array(
                                "id" => "comply_with_national_legislation_no",
                                "name" => "comply_with_national_legislation",
                                "data-msg-required" => lang("field_required"),
                                    ), "no", false);
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
                                    ), "yes", true);
                            ?>
                            <label for="workers_subjected_to_forced_labor_yes" class="mr15"><?php echo lang('yes'); ?></label> 
                            <?php
                            echo form_radio(array(
                                "id" => "workers_subjected_to_forced_labor_no",
                                "name" => "workers_subjected_to_forced_labor",
                                "data-msg-required" => lang("field_required"),
                                    ), "no", false);
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
                                "value" => "",
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
                                "value" => "",
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
                                "value" => "",
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
                                    ), "yes", true);
                            ?>
                            <label for="overtime_yes" class="mr15"><?php echo lang('yes'); ?></label> 
                            <?php
                            echo form_radio(array(
                                "id" => "overtime_no",
                                "name" => "overtime",
                                "data-msg-required" => lang("field_required"),
                                    ), "no", false);
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
                                    "value" => "",
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
                                    ), "yes", true);
                            ?>
                            <label for="employ_emmigrants_yes" class="mr15"><?php echo lang('yes'); ?></label> 
                            <?php
                            echo form_radio(array(
                                "id" => "employ_emmigrants_no",
                                "name" => "employ_emmigrants",
                                "data-msg-required" => lang("field_required"),
                                    ), "no", false);
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
                                    ), "yes", true);
                            ?>
                            <label for="ethical_policy_oit_yes" class="mr15"><?php echo lang('yes'); ?></label> 
                            <?php
                            echo form_radio(array(
                                "id" => "ethical_policy_oit_no",
                                "name" => "ethical_policy_oit",
                                "data-msg-required" => lang("field_required"),
                                    ), "no", false);
                            ?>
                            <label for="ethical_policy_oit_no" class=""><?php echo lang('no'); ?></label>
                        </div>
                    </div>

                    <div id="ethical_policy_oit_file_group">
                        <div class="form-group">
                            <label for="ethical_policy_oit_file" class="<?php echo $label_column; ?>"><?php echo lang('attach_ethical_policy_based_on_oit'); ?></label>
                            <div class="<?php echo $field_column; ?>">
                                <?php
                                    echo $this->load->view("includes/page_file_uploader", array(
                                        "upload_url" =>get_uri("Public_forms/upload_file"),
                                        "validation_url" =>get_uri("Public_forms/validate_file"),
                                        "html_name" => "ethical_policy_oit_file",
                                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                                        "obligatorio" => "",
                                        "id_campo" => "ethical_policy_oit_file",
                                    ),
                                    true);
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
                                    ), "yes", true);
                            ?>
                            <label for="comply_hygiene_and_safety_conditions_yes" class="mr15"><?php echo lang('yes'); ?></label> 
                            <?php
                            echo form_radio(array(
                                "id" => "comply_hygiene_and_safety_conditions_no",
                                "name" => "comply_hygiene_and_safety_conditions",
                                "data-msg-required" => lang("field_required"),
                                    ), "no", false);
                            ?>
                            <label for="comply_hygiene_and_safety_conditions_no" class=""><?php echo lang('no'); ?></label>
                        </div>
                    </div>

                    <div id="accident_report_file_group">
                        <div class="form-group">
                            <label for="attach_accident_report" class="<?php echo $label_column; ?>"><?php echo lang('attach_accident_report'); ?></label>
                            <div class="<?php echo $field_column; ?>">
                                <?php
                                    echo $this->load->view("includes/page_file_uploader", array(
                                        "upload_url" =>get_uri("Public_forms/upload_file"),
                                        "validation_url" =>get_uri("Public_forms/validate_file"),
                                        "html_name" => "accident_report_file",
                                        //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                                        "obligatorio" => "",
                                        "id_campo" => "accident_report_file",
                                    ),
                                    true);
                                ?>
                            </div>
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
                                    ), "yes", true);
                            ?>
                            <label for="risk_prevention_specialist_yes" class="mr15"><?php echo lang('yes'); ?></label> 
                            <?php
                            echo form_radio(array(
                                "id" => "risk_prevention_specialist_no",
                                "name" => "risk_prevention_specialist",
                                "data-msg-required" => lang("field_required"),
                                    ), "no", false);
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
                                "value" =>"",
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



                </div>
                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function () {

        Dropzone.autoDiscover = false;

        initScrollbar('#page-content');

        $("#communities_providers-form").appForm({
            isModal: false,
            onSuccess: function(result) {

                appAlert.success(result.message, {duration: 10000});
                
                $("#communities_providers-form").each(function(){
                    this.reset();
                });

                $("#id_project").select2().val("");
                
                $('#ethical_social_audit_yes').click();
                $('#non_discrimination_policy_yes').click();
                $('#anti_corruption_and_transparency_policy_yes').click();
                $('#environmental_policy_yes').click();
                $('#promote_free_assoc_and_neg_rights_yes').click();
                $('#comply_with_national_legislation_yes').click();
                $('#workers_subjected_to_forced_labor_yes').click();
                $('#overtime_yes').click();
                $('#employ_emmigrants_yes').click();
                $('#ethical_policy_oit_yes').click();
                $('#comply_hygiene_and_safety_conditions_yes').click();
                $('#risk_prevention_specialist_yes').click();

                $.ajax({
                    url:  '<?php echo_uri("Public_forms/get_fields_ethical_social_audit"); ?>',
                    type:  'post',
                    data: {ethical_social_audit: "yes"},
                    success: function(respuesta){
                        $('#ethical_social_audit_file_group').html(respuesta);
                    }
                });

                $.ajax({
                    url:  '<?php echo_uri("Public_forms/get_fields_non_discrimination_policy"); ?>',
                    type:  'post',
                    data: {non_discrimination_policy: "yes"},
                    success: function(respuesta){
                        $('#non_discrimination_policy_file_group').html(respuesta);
                    }
                });

                $.ajax({
                    url:  '<?php echo_uri("Public_forms/get_fields_anti_corruption_and_transparency_policy"); ?>',
                    type:  'post',
                    data: {anti_corruption_and_transparency_policy: "yes"},
                    success: function(respuesta){
                        $('#anti_corruption_and_transparency_policy_file_group').html(respuesta);
                    }
                });

                $.ajax({
                    url:  '<?php echo_uri("Public_forms/get_fields_environmental_policy"); ?>',
                    type:  'post',
                    data: {environmental_policy: "yes"},
                    success: function(respuesta){
                        $('#environmental_policy_file_group').html(respuesta);
                    }
                });

                $.ajax({
                    url:  '<?php echo_uri("Public_forms/get_fields_ethical_policy_oit"); ?>',
                    type:  'post',
                    data: {ethical_policy_oit: "yes"},
                    success: function(respuesta){
                        $('#ethical_policy_oit_file_group').html(respuesta);
                    }
                });

                $.ajax({
                    url:  '<?php echo_uri("Public_forms/get_fields_accident_report"); ?>',
                    type:  'post',
                    success: function(respuesta){
                        $('#accident_report_file_group').html(respuesta);
                    }
                });

            }
        });
		
		//$('[data-toggle="tooltip"]').tooltip();
		$('.select2').select2();
		
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
		
	    /*setDatePicker('.datepicker');
	    setTimePicker('.timepicker');*/

        $('input[type=radio][name=ethical_social_audit]').change(function(){		
			
			var ethical_social_audit = $(this).val();
            $.ajax({
                url:  '<?php echo_uri("Public_forms/get_fields_ethical_social_audit"); ?>',
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
                url:  '<?php echo_uri("Public_forms/get_fields_non_discrimination_policy"); ?>',
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
                url:  '<?php echo_uri("Public_forms/get_fields_anti_corruption_and_transparency_policy"); ?>',
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
                url:  '<?php echo_uri("Public_forms/get_fields_environmental_policy"); ?>',
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
                url:  '<?php echo_uri("Public_forms/get_fields_overtime"); ?>',
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
                url:  '<?php echo_uri("Public_forms/get_fields_ethical_policy_oit"); ?>',
                type:  'post',
                data: {ethical_policy_oit: ethical_policy_oit},
                success: function(respuesta){
                    $('#ethical_policy_oit_file_group').html(respuesta);
                }
            });

		});

    });
</script>