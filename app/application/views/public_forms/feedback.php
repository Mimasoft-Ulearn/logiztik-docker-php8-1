<div id="page-content" class="p20 clearfix">

    <div id="view-container" class="view-container">
        <div class="tab-content">
            <?php echo form_open(get_uri("Public_forms/save_feedback"), array("id" => "communities_feedback-form", "class" => "general-form dashed-row white", "role" => "form")); ?>
            <div class="panel">
                <div class="panel-default panel-heading">
                    <h4> <?php echo lang('add_feedback'); ?></h4>
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
                        <label for="email" class="<?php echo $label_column; ?>"><?php echo lang('email'); ?></label>
                        <div class="<?php echo $field_column; ?>">
                            <?php
                            echo form_input(array(
                                "id" => "email",
                                "name" => "email",
                                "value" => "",
                                "class" => "form-control",
                                "placeholder" => lang('email'),
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
                        <label for="phone" class="<?php echo $label_column; ?>"><?php echo lang('phone_number'); ?></label>
                        <div class="<?php echo $field_column; ?>">
                            <?php
                            echo form_input(array(
                                "id" => "phone",
                                "name" => "phone",
                                "value" => "",
                                "class" => "form-control",
                                "placeholder" => lang('phone'),
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
                        <label for="id_project" class="<?php echo $label_column; ?>"><?php echo lang('with_which_subsole_plant_do_you_want_to_communicate'); ?></label>
                        <div class="<?php echo $field_column; ?>">
                            <?php
                            echo form_dropdown("id_project", $dropdown_projects, array(), "id='id_project' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="type_of_stakeholder" class="<?php echo $label_column; ?>"><?php echo lang('what_interest_group_do_you_belong_to'); ?></label>
                        <div class="<?php echo $field_column; ?>">
                            <?php
                            echo form_dropdown("type_of_stakeholder", $dropdown_tipos_organizaciones, array(), "id='type_of_stakeholder' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="<?php echo $label_column; ?>"><?php echo lang('reason_for_contact'); ?></label>
                        <div class="<?php echo $field_column; ?>">
                            <?php
                                $propositos_visita_dropdown = array(
                                    "" => "-",
                                    "request_meeting" => lang("request_meeting"),
                                    "query" => lang("query"),
                                    "congratulation" => lang("congratulation"),
                                    "complain" => lang("complain"), 
                                    "comment" => lang("comment"),
                                );
                                echo form_dropdown("visit_purpose", $propositos_visita_dropdown, array(), "id='visit_purpose' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="comments" class="<?php echo $label_column; ?>"><?php echo lang('describe_the_reason_for_your_contact'); ?></label>
                        <div class="<?php echo $field_column; ?>">
                            <?php
                                echo form_textarea(array(
                                    "id" => "comments",
                                    "name" => "comments",
                                    "value" => "",
                                    "class" => "form-control",
                                    "placeholder" => lang('comments'),
                                    "style" => "height:150px;",
                                    "data-rule-required" => true,
                                    "data-msg-required" => lang("field_required"),
                                    "autocomplete"=> "off",
                                    "maxlength" => "2000"
                                ));
                            ?>
                        </div>
                    </div>   

                    <div class="form-group">
                        <label for="requires_monitoring" class="<?php echo $label_column; ?>"><?php echo lang('do_you_want_contact_with_subsole_team'); ?></label>
                        <div class="<?php echo $field_column; ?>">
                            <?php
                            echo form_radio(array(
                                "id" => "requires_monitoring_yes",
                                "name" => "requires_monitoring",
                                "data-rule-required" => true,
                                "data-msg-required" => lang("field_required"),
                                    ), "1", false);
                            ?>
                            <label for="erequires_monitoring_yes" class="mr15"><?php echo lang('yes'); ?></label> 
                            <?php
                            echo form_radio(array(
                                "id" => "requires_monitoring_no",
                                "name" => "requires_monitoring",
                                "data-rule-required" => true,
                                "data-msg-required" => lang("field_required"),
                                    ), "0", false);
                            ?>
                            <label for="requires_monitoring_no" class=""><?php echo lang('no'); ?></label>
                        </div>
                    </div>
                    
                    <div id="responsible_group">
                        <div class="form-group">
                            <label for="responsible" class="<?php echo $label_column; ?>"><?php echo lang('who_do_you_want_to_contact'); ?></label>
                            <div class="<?php echo $field_column; ?>">
                                <?php
                                    echo form_dropdown("responsible", array("" => "-"), array(), "id='responsible' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                                ?>
                            </div>
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

        $('#view-container').css('height', "1050px");

        $("#communities_feedback-form").appForm({
            isModal: false,
            onSuccess: function(result) {
                
                appAlert.success(result.message, {duration: 10000});

                $("#communities_feedback-form").each(function(){
                    this.reset();
                });
                
                $("#id_project").select2().val("");
                $("#type_of_stakeholder").select2().val("");
                $("#visit_purpose").select2().val("");
                $("#responsible").select2().val("");
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

        $('#id_project').on('change', function(){
            
            var id_project = $(this).val();	
            select2LoadingStatusOn($('#responsible'));
            
            $.ajax({
                url:  '<?php echo_uri("Public_forms/get_responsables_dropdown") ?>',
                type:  'post',
                data: {id_project: id_project},
                success: function(respuesta){
                    $('#responsible_group').html(respuesta);
                    $('#responsible').select2();
                }
            });
            
        });	

    });
</script>