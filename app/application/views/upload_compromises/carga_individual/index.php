<div class="panel">

    <div class="tab-title clearfix">
        <h4><?php echo lang('individual_load'); ?></h4>
        <div class="title-button-group">
            <?php
            echo modal_anchor(get_uri("upload_compromises/modal_form_carga_individual/".$id_compromiso_proyecto."/".$tipo_matriz), "<i class='fa fa-plus-circle'></i> " . lang('add_compromise'), array("class" => "btn btn-default", "title" => lang('add_compromise')));
            ?>
        </div>
    </div>

    <div class="table-responsive">
        <table id="individual_upload-table" class="display" width="100%">            
        </table>
    </div>   
     
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
		<?php if($id_compromiso_proyecto) { ?>
		
			$("#individual_upload-table").appTable({
				source: '<?php echo_uri("upload_compromises/list_data_carga_individual/".$id_compromiso_proyecto."/".$tipo_matriz); ?>',
				order: [[1, "asc"]],
				columns: [
					{title: "ID", "class": "text-right dt-head-center w10"},
					
					<?php if($tipo_matriz == "rca"){ ?>
						{title: "<?php echo lang("compromise_number"); ?>", "class": "text-right dt-head-center"},
						{title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
						{title: "<?php echo lang("phases"); ?>", "class": "text-left dt-head-center"},
						{title: "<?php echo lang("reportability"); ?>", "class": "text-center dt-head-center"}
						<?php echo $columnas_campos; ?>,
						{title: "<?php echo lang("compliance_action_control"); ?>", "class": "text-left dt-head-center"},
						{title: "<?php echo lang("execution_frequency"); ?>", "class": "text-left dt-head-center"},
					<?php }else{ ?>
							
						{title: "<?php echo lang("n_activity"); ?>", "class": "text-right dt-head-center"}, // nuevo
						{title: "<?php echo lang("environmental_management_instrument"); ?>", "class": "text-right dt-head-center"}, // nuevo
						{title: "<?php echo lang("phase_reportable"); ?>", "class": "text-left dt-head-center"}, 
						{title: "<?php echo lang("compliance_type"); ?>", "class": "text-left dt-head-center"}, 
						{title: "<?php echo lang("environmental_topic"); ?>", "class": "text-left dt-head-center"}, 
						{title: "<?php echo lang("impact_on_the_environment_due_to_non_compliance"); ?>", "class": "text-left dt-head-center"},  // nuevo
						{title: "<?php echo lang("action_type"); ?>", "class": "text-left dt-head-center"},
						{title: "<?php echo lang("responsible_area"); ?>", "class": "text-left dt-head-center"},
						{title: "<?php echo lang("environmental_commitment"); ?>", "class": "text-center dt-head-center w10"}
						<?php echo $columnas_campos; ?>,
						{title: "<?php echo lang("planning"); ?>", "class": "text-center dt-head-center"},

					<?php } ?>
					
					{title: '<i class="fa fa-bars"></i>', "class": "text-center option no_breakline"}
				],
				rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
					$(nRow).find('[data-toggle="tooltip"]').tooltip();
				},
				//printColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
				//xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
			});
		
		<? } ?>
    });
</script>