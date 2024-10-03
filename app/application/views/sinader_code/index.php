<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("records"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("sinader_code"); ?></a> 
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('sinader_code'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("sinader_code/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_sinader_code'), array("class" => "btn btn-default", "title" => lang('add_sinader_code'))); ?>
            </div>
        </div>
        <div class="table-responsive">
        <table id="sinader_code-table" class="display" cellspacing="0" width="100%"></table>
        </div>
    </div>
            
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#sinader_code-table").appTable({
            source: '<?php echo_uri("sinader_code/list_data"); ?>',
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
                {title: "<?php echo lang("ler_code"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
            //printColumns: combineCustomFieldsColumns([0, 1, 2]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2])
        });
    });
</script>