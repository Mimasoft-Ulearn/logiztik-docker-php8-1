<?php if($puede_ver != 3) { ?>
    <div class="panel">
            <div class="tab-title clearfix">
                <h4><?php echo lang('event'); ?></h4>
                <div class="title-button-group">
                	<?php 
						//if($puede_eliminar_zona_norte != 3 || $puede_eliminar_zona_centro != 3 || $puede_eliminar_zona_sur != 3){ // 3 = Perfil Eliminar Ninguno 

							/* echo '<span style="cursor: not-allowed;">'.js_anchor("<i class='fa fa-trash'></i> ".lang("delete_selected"), array('title' => lang('delete_informations'), "id" => "delete_selected_rows_information", "class" => "delete btn btn-danger", "data-action" => "delete-confirmation", "data-custom" => true, "disabled" => "disabled", "style" => "pointer-events: none;")).'</span>'; */
						//} 
					?>
                    <?php if($puede_agregar != 3){
                        echo modal_anchor(get_uri("contingencies_record/modal_form_event"), "<i class='fa fa-plus-circle'></i> " . lang('add_event'), array("id" => "agregar_info", "class" => "btn btn-default", "title" => lang('add_event')));
                    } ?>
                </div>
            </div>
        
            <div class="table-responsive">
                <table id="event-table" class="display" width="100%">            
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

<script type="text/javascript">

    $(document).ready(function () {
		
		$("#event-table").appTable({
			source: '<?php echo_uri("contingencies_record/list_data_event/"); ?>',
			//order: [[1, "asc"]],
			columns: [
				//{title: "ID", "class": "text-center w10"},
				{title: "<?php echo lang("identification_date"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("n_sacpa"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("management_2"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("environmental_management_instrument"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("event_type"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("description_of_non_conformity_and_or_finding"); ?>", "class": "text-center dt-head-center"},
				{title: '<i class="fa fa-bars"></i>', "class": "text-center option w5 no_breakline"}
			],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(nRow).find('[data-toggle="tooltip"]').tooltip();
			},
			//printColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
			//xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
		});

    });
</script>