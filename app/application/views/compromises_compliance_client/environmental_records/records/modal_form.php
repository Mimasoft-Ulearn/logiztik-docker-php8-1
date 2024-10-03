<?php echo form_open(get_uri("environmental_records/save/".$id_registro_ambiental), array("id" => "environmental_records-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("environmental_records/records/environmental_records_form_fields"); ?>
</div>

<div class="modal-footer">
    <div id="link-of-new-view" class="hide">
        <?php echo modal_anchor(get_uri("environmental_records/modal_form/".$id_registro_ambiental), "", array()); ?>
    </div>
    <?php if ($add_type == "multiple") { ?>
        <button id="save-and-add-button" type="button" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save_and_add_more'); ?></button>
    <?php } else { ?>
        <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
    <?php } ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {

        window.showAddNewModal = false;

        $("#save-and-add-button").click(function () {
            window.showAddNewModal = true;
            $(this).trigger("submit");
        });

        var viewRecordText = "<?php echo lang('view'); ?>";
        var addMultipleRecordsText = "<?php echo lang('add_various').' '.$form_info->nombre; ?>";
        var addType = "<?php echo $add_type; ?>";

        window.environmentalRecordsForm = $("#environmental_records-form").appForm({
            closeModalOnSuccess: (addType === "multiple") ? false : true,
            onSuccess: function(result) {
				
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {

                    $("#environmental_records-table").appTable({newData: result.data, dataId: result.id});

                    if (window.showAddNewModal) {
                        var $recordViewLink = $("#link-of-new-view").find("a");

                        if (addType === "multiple") {
                            $recordViewLink.attr("data-action-url", "<?php echo get_uri("environmental_records/modal_form/".$id_registro_ambiental); ?>");
                            $recordViewLink.attr("data-title", addMultipleRecordsText);
                            $recordViewLink.attr("data-post-last_id", result.id);
                            $recordViewLink.attr("data-post-flujo", "<?php echo $flujo; ?>");
                            $recordViewLink.attr("data-act", "ajax-modal");
                            $recordViewLink.attr("data-post-add_type", "multiple");
                        } else {
                            $recordViewLink.attr("data-action-url", "<?php echo get_uri("environmental_records/preview/".$id_registro_ambiental); ?>");
                            $recordViewLink.attr("data-title", viewRecordText + "#" + result.id);
                            $recordViewLink.attr("data-post-id", result.id);
                        }

                        $recordViewLink.trigger("click");

                    } 

                    appAlert.success(result.message, {duration: 10000});

                }
				
				$('#fecha_modificacion').text(result.fecha_modificacion);
				$('#num_registros').text(result.num_registros);
            }
        });
		$("#environmental_records-form").validate().settings.ignore = "";
        //$("#company_name").focus();
    });
</script>    