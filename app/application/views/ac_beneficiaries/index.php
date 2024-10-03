<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="<?php echo get_uri("client_agreements_dashboard/index/".$this->session->client_area); ?>"><?php echo lang("community"); ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("collaborators") ?></a>
</nav>

<?php if($puede_ver != 3) { ?>
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('collaborators'); ?></h1>
            <div class="title-button-group">
            	<?php 
					if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
						echo '<span style="cursor: not-allowed;">'.js_anchor("<i class='fa fa-trash'></i> ".lang("delete_selected"), array('title' => lang('delete_beneficiaries'), "id" => "delete_selected_rows", "class" => "delete btn btn-danger", "data-action" => "delete-confirmation", "data-custom" => true, "disabled" => "disabled", "style" => "pointer-events: none;")).'</span>';
					} 
				?>
            	<div class="btn-group" role="group">
                    <button type="button" class="btn btn-success" id="excel"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
                </div> 
                <?php echo modal_anchor(get_uri("AC_Beneficiaries/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_beneficiary'), array("id" => "agregar_beneficiario", "class" => "btn btn-default", "title" => lang('add_beneficiary'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="beneficiaries-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
<?php } else { ?>

    <div class="row"> 
        <div class="col-md-12 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div id="app-alert-d1via" class="app-alert alert alert-danger alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>-->
                        <div class="app-alert-message"><?php echo lang("content_disabled"); ?></div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger hide" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>

<?php } ?>  

</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#beneficiaries-table").appTable({
            source: '<?php echo_uri("AC_Beneficiaries/list_data") ?>',
			filterDropdown: [
				{name: "sexo", class: "w200", options: <?php echo $sexo_dropdown; ?>},
				{name: "sociedad", class: "w200", options: <?php echo $sociedad_dropdown; ?>},
				{name: "discapacidad", class: "w200", options: <?php echo $discapacidad_dropdown; ?>},
				
			],
            columns: [
			
				<?php if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno ?>
			
					{
						checkboxes: {
							selectRow: true,
							selectCallback: function(){
								get_selected_rows();
							}
						},
						select: {
							style: 'multi'
						},
						render: function(data, type, row, meta){ 

							data = "";
							if(row[2] == 1){ // 1 = Perfil Eliminar Todos
								data = '<input type="checkbox" class="dt-checkboxes">'
							}
							if(row[2] == 2){ // 2 = Perfil Eliminar Propios
								if(row[1] == <?php echo $this->session->user_id; ?>){
									data = '<input type="checkbox" class="dt-checkboxes">'
								} else {
									data = '<input type="checkbox" class="dt-checkboxes" disabled>'
								}
							}
							if(row[2] == 3){ // 3 = Perfil Eliminar Ninguno
								data = '<input type="checkbox" class="dt-checkboxes" disabled>'
							}
							
							return data;
						   
						},
						createdCell:  function (td, cellData, rowData, row, col){

							if(rowData[2] == 2){ // 2 = Perfil Eliminar Propios
								if(rowData[1] != <?php echo $this->session->user_id; ?>){
									this.api().cell(td).checkboxes.disable();
								}
							}
							if(rowData[2] == 3){ // 3 = Perfil Eliminar Ninguno
								this.api().cell(td).checkboxes.disable();
							}
							
							this.api().cell(td).checkboxes.deselect();

						}
						
					},
				
				<?php } ?>
				
				{title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
				{title: "<?php echo lang("created_by"); ?>", "class": "text-center w50 hide"},

				{title: "<?php echo lang("national_id"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("sex"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("birthdate"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("organizational_email"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("society"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("society_desc"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("cost_center_desc"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("position_desc"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("division_desc"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("subdivision_desc"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("contract_start_date"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("contract_end_date"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("status"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("contract_type"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("division2_desc"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("boss_position_desc"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("civil_status"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("personnel_area"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("department_desc"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("jobcode_desc"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("fullname"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("nationality"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("commune"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("province"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("disability"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("tea_law"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("native_people"); ?>", "class": "text-left dt-head-center"},
				
				// {title: "<?php echo lang("created_date"); ?>", "class": "text-left dt-head-center", type:"extract-date"},
				// {title: "<?php echo lang("modified_date"); ?>", "class": "text-left dt-head-center", type:"extract-date"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150 no_breakline"}
            ],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            	$(nRow).find('[data-toggle="tooltip"]').tooltip();
			},
			//orderMulti: true,
			//order: [0 , "desc"],
			//columnDefs : [{targets: 1, type: "extract-date"}],
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
		
		$('#excel').click(function(){
			var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("AC_Beneficiaries/get_excel/")?>').attr('method','POST').attr('target', '_self').appendTo('body');
			$form.submit();
			
			// $("#gg").on("submit", function(e){
			// 	appLoader.show();
			// });
			// $form.submit();
			
			// $("#gg").on("submit", function(e){
			// 	console.log('dfdfdf');
			// 	appLoader.hide();
			// });

			
			// $.ajax({
			// 	url: "<?php echo_uri("AC_Beneficiaries/get_excel/")?>",
			// 	beforeSend: function(){appLoader.show();},
			// 	success: function(){appLoader.hide();},
			// });
		});
		
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
						$('#table_delete_' + result.id_campo).parent().parent().html(result.new_field);
						$('#beneficiaries-form').append('<input type="hidden" name="id_campo_archivo_eliminar[]" value="' + result.id_campo + '" />');
						
					} else {
						appAlert.error(result.message);
					}
					appLoader.hide();
				}
			});
			
			
		});
		
		<?php if($puede_agregar != 1) { ?>
			$('#agregar_beneficiario').removeAttr("data-action-url").attr('disabled','true');
		<?php } ?>
		
		<?php if($puede_ver == 3) { ?>
			$('#excel').attr('disabled','true');
		<?php } ?>
		
		// SELECCIÓN MÚLTIPLE DE FILAS DE APPTABLE
		var data_ids = [];
		function get_selected_rows(){
			
			var rows_selected = $("#beneficiaries-table").DataTable().column(0).checkboxes.selected();
			var ids = rows_selected.join(",");
			data_ids = ids.split(",");
							
			if(data_ids[0] !== ""){
				$('#delete_selected_rows').attr("disabled", false).css("pointer-events", "auto");
			} else {
				$('#delete_selected_rows').attr("disabled", "disabled").css("pointer-events", "none");
			}

		};

		$(document).on('click', '#delete_selected_rows', function() {
			$("#confirmMultipleDeleteButton").attr("data-ids", JSON.stringify(data_ids));
			$("#confirmMultipleDeleteButton").attr("data-action-url", "<?php echo get_uri("AC_Beneficiaries/delete_multiple/"); ?>");
			$('#confirmationMultipleModal').modal('show');
			
		});
		
		$(document).on('click', '#confirmMultipleDeleteButton', function() {
			
			var url = $(this).attr('data-action-url');
			var data_ids = $(this).attr('data-ids');

			appLoader.show();
			
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {data_ids: data_ids},
				success: function (result) {
					if (result.success) {
						appAlert.warning(result.message, {duration: 20000});
						$.each( JSON.parse(data_ids), function( index, id ){
							var tr = $('a.delete[data-id="'+id+'"]').closest('tr'),
							table = $("#beneficiaries-table").dataTable();
							table.fnDeleteRow($("#beneficiaries-table").DataTable().row(tr).index());
						});
						
						$('#delete_selected_rows').attr("disabled", "disabled").css("pointer-events", "none");
						
					} else {
						appAlert.error(result.message, {duration: 20000});
					}
					appLoader.hide();
				}
			});
			
		}); 
		
    });
</script>


<script type="text/javascript">
    $(document).ready(function () {
        // Acción en apptable para actualizar el campo Discapacidad0
		$('body').on('click', '[data-act=update-disability]', function () {
            $(this).appModifier({
                value: $(this).attr('data-value'),
                actionUrl: '<?php echo_uri("AC_Beneficiaries/save_disability") ?>/' + $(this).attr('data-id'),
                select2Option: {data: <?php echo json_encode($discapacidad_dropdown_apptable) ?>},
                onSuccess: function (result, newValue) {
                    if (result.success) {
                        $("#beneficiaries-table").appTable({newData: result.data, dataId: result.id});
                        // $("#beneficiaries-table").dataTable().fnReloadAjax();
                        
						appAlert.success(result.message, {duration: 10000});
                          
                    } else {
						appAlert.error(result.message, {duration: 10000});
					} 
                }
            });
            return false;
        });
    });
</script>
