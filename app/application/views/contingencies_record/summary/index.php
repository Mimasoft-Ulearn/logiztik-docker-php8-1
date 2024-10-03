<?php if($puede_ver != 3) { ?>
    <div class="panel">
            <div class="tab-title clearfix">
                <h4><?php echo lang('summary'); ?></h4>
                <div class="title-button-group">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" id="excel"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
                    </div>  
                </div>  
            </div>
        
            <div class="table-responsive">
                <table id="summary-table" class="display" width="100%">            
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
	
        $('[data-toggle="tooltip"]').tooltip();
		
		$("#summary-table").appTable({
			source: '<?php echo_uri("contingencies_record/list_data_summary/"); ?>',
			//order: [[1, "asc"]],
			columns: [
				//{title: "id", "class": "text-right dt-head-center w50 hide"},
				{title: "<?php echo lang("n_sacpa"); ?>", "class": "text-right dt-head-center"},
                {title: "<?php echo lang("identification_date"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("management_2"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("environmental_management_instrument"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("event_type"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("affectation_type"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("corrective_action"); ?>", "class": "text-center dt-head-center"},
                {title: "<?php echo lang("verification"); ?>", "class": "text-center dt-head-center"},
				{title: '<i class="fa fa-bars"></i>', "class": "text-center option w5 no_breakline"}
			],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(nRow).find('[data-toggle="tooltip"]').tooltip();
			},
			//printColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
			//xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
		});

        $('#excel').click(function(){
			var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("Contingencies_record/get_excel")?>').attr('method','POST').attr('target', '_self').appendTo('body');
			$form.submit();
		});

    });
</script>