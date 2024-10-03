<?php echo form_open(get_uri("contingencies_record/save_correction"), array("id" => "correction-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("contingencies_record/corrections/correction_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#correction-form").appForm({
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
					// console.log(result);
                    $("#correction-table").appTable({newData: result.data, dataId: result.id});
					
                }
            }
        });
		//$("#information-form").validate().settings.ignore = "";
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

        
		$('#confirmFileDeleteButton').click(function() {
			
			appLoader.show();
	
			var url = $(this).attr('data-action-url'),
					filename = $(this).attr('data-filename'),
                    id_correccion = $(this).attr('data-id_correccion'),
                    key_correccion = $(this).attr('data-key_correccion'),
					undo = $(this).attr('data-undo'),
					campo = $(this).attr('data-campo'),
					obligatorio = $(this).attr('data-obligatorio');
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {filename: filename, id_correccion: id_correccion, key_correccion : key_correccion, campo:campo, obligatorio:obligatorio},
				success: function (result) {
					if (result.success) {
							appAlert.warning(result.message, {duration: 20000});
							$('#table_delete_' + result.nombre_campo).html(result.new_field);
							//initScrollbar(".modal-body", {setHeight: 400});
							$('#correction-form').append('<input type="hidden" name="nombre_archivos_evidencia_eliminar[]" value="' + result.nombre_archivo + '" />');
						// console.log(result.nombre_archivo);
					} else {
						appAlert.error(result.message);
					}
					
					appLoader.hide();
				}
			});
			
			
			
		});
    });
</script>    