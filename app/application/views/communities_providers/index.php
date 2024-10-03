<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("providers") ?></a>
</nav>

<?php if($puede_ver != 3) { ?>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('providers'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("communities_providers/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_provider'), array("id"=> "add_provider", "class" => "btn btn-default", "title" => lang('add_provider'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="communities_providers-table" class="display" cellspacing="0" width="100%">            
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
		
        $("#communities_providers-table").appTable({
            source: '<?php echo_uri("communities_providers/list_data") ?>',
			/*filterDropdown: [
				{name: "id_proyecto", class: "w200", options: <?php echo $proyectos_dropdown; ?>},
				{name: "id_cliente", class: "w200", options: <?php echo $clientes_dropdown; ?>},
			],*/
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
                {title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("provider"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("responsible-name"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("responsible-email"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            	$(nRow).find('[data-toggle="tooltip"]').tooltip();
			}
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });

        <?php if($puede_agregar != 1) { ?>
			$('#add_provider').removeAttr("data-action-url").attr('disabled','true');
		<?php } ?>
		
    });
</script>