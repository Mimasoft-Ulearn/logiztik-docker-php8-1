<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("records"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("sidrep_codes"); ?></a> 
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('sidrep_codes'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("sidrep_codes/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_sidrep_codes'), array("class" => "btn btn-default", "title" => lang('add_sidrep_codes'))); ?>
            </div>
        </div>
        <div class="table-responsive">
        <table id="sidrep_codes-table" class="display" cellspacing="0" width="100%"></table>
        </div>
    </div>
            
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#sidrep_codes-table").appTable({
            source: '<?php echo_uri("sidrep_codes/list_data"); ?>',
			filterDropdown: [
				{name: "id_category", class: "w200", options: <?php echo $categories_dropdown; ?>},
				{name: "id_project", class: "w200", options: <?php echo $proyectos_dropdown; ?>},
                {name: "id_client", class: "w200", options: <?php echo $clients_dropdown; ?>},
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("project"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("category"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("code_list_a"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("code_lists_i_ii_iii"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("dangerous_characteristic"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("physical_status"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
            //printColumns: combineCustomFieldsColumns([0, 1, 2]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2])
        });
    });
</script>