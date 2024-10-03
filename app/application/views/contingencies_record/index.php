<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("contingencies"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("contingencies_record"); ?>"><?php echo lang("contingencies_record"); ?></a>
</nav>

<?php if($puede_ver != 3) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="page-title clearfix">
                <h1>
                    <?php echo lang("contingencies_record"); ?>   
                </h1>
            </div>
            <ul id="project-tabs" data-toggle="ajax-tab" class="nav nav-tabs classic" role="tablist">
            	<li>
                    <a id="tab_summary" role="presentation" href="<?php echo_uri("contingencies_record/summary/"); ?>" data-target="#summary"><?php echo lang('summary'); ?></a>
                </li>
                <li>
                <a id="tab_event" role="presentation" href="<?php echo_uri("contingencies_record/event/"); ?>" data-target="#event"><?php echo lang('event'); ?></a>
                </li>
                <li>
                    <a id="tab_correction" role="presentation" href="<?php echo_uri("contingencies_record/correction/"); ?>" data-target="#correction"><?php echo lang('correction'); ?></a>
                </li>
                <li>
                    <a id="tab_verification" role="presentation" href="<?php echo_uri("contingencies_record/verification/"); ?>" data-target="#verification"><?php echo lang('verification'); ?></a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade active" id="summary" style="min-height: 200px;"></div>
                <div role="tabpanel" class="tab-pane fade" id="event"></div>
                <div role="tabpanel" class="tab-pane fade" id="correction"></div>
                <div role="tabpanel" class="tab-pane fade" id="verification"></div>
            </div>


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
		
		$('#tab_summary').click(function(){
			if($("#summary-table").length){
				$("#summary-table").dataTable().fnReloadAjax();
			}
		});
		
		$('#tab_event').click(function(){
			if($("#event-table").length){
				$("#event-table").dataTable().fnReloadAjax();
			}
		});
		
		$('#tab_correction').click(function(){
			if($("#correction-table").length){
				$("#correction-table").dataTable().fnReloadAjax();
			}
		});
		
		$('#tab_verification').click(function(){
			if($("#verification-table").length){
				$("#verification-table").dataTable().fnReloadAjax();
			}
		});

    });
</script>