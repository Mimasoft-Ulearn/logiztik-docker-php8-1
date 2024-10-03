<?php echo form_open(get_uri("communities_providers/save"), array("id" => "communities_providers-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("communities_providers/communities_providers_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#communities_providers-form").appForm({
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
                    $("#communities_providers-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });

        //$("#company_name").focus();

        $(document).on('click', '.table_delete a.delete', function() {
			$(this).each(function () {
				$.each(this.attributes, function () {
					if (this.specified && this.name.match("^data-")) {
						$("#confirmFileDeleteButton").attr(this.name, this.value);
					}

				});
			});
			$("#confirmationFileModal").modal('show');
		});
		
		//$('#confirmFileDeleteButton').click(function(e) {
		$('#confirmFileDeleteButton').unbind().click(function(e) {
			
			appLoader.show();
	
			var url = $(this).attr('data-action-url'),
					id = $(this).attr('data-id'),
					undo = $(this).attr('data-undo'),
					campo = $(this).attr('data-campo'),
					obligatorio = $(this).attr('data-obligatorio');
			
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {id: id, campo:campo, obligatorio:obligatorio},
				success: function (result) {
					if (result.success) {
						
						appAlert.warning(result.message, {duration: 20000});
						$('#table_delete_' + result.id_campo).html(result.new_field);
						
						//initScrollbar(".modal-body", {setHeight: 400});
						
						$('#communities_providers-form').append('<input type="hidden" name="id_campo_archivo_eliminar[]" value="' + result.id_campo + '" />');
						
					} else {
						appAlert.error(result.message);
					}
					appLoader.hide();
				}
			});
			
			e.preventDefault();
			
		});


    });
</script>    