<?php echo form_open(get_uri("AC_Feeders/save_type_of_activities"), array("id" => "feeders_type_of_activities-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("ac_feeders/type_of_activities/feeders_type_of_activities_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#feeders_type_of_activities-form").appForm({
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
                    $("#feeders-table").appTable({newData: result.data, dataId: result.id});
                }
				$('#fecha_modificacion').text(result.fecha_modificacion);
				$('#num_registros').text(result.num_registros);
            }
        });

        //$("#company_name").focus();
    });
</script>    