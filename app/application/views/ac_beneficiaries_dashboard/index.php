<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="<?php echo get_uri("client_agreements_dashboard/index/".$this->session->client_area); ?>"><?php echo lang("community"); ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("collaborators") ?></a> /
  <a class="breadcrumb-item" href="#"><?php echo lang("dashboard_eng") ?></a>
</nav>

<?php if($puede_ver != 3) { ?>
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('dashboard_eng'); ?></h1>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <ul data-toggle="ajax-tab" class="nav nav-tabs classic" role="tablist">
                <li class="active"><a role="presentation" href="#" data-target="#summary"><?php echo lang('summary'); ?></a></li>
                <li><a role="presentation" href="#" data-target="#gender"><?php echo lang('gender'); ?></a></li>
                <li><a role="presentation" href="#" data-target="#generations"><?php echo lang('generations'); ?></a></li>
                <li><a role="presentation" href="#" data-target="#disability"><?php echo lang('disability'); ?></a></li>
                <li><a role="presentation" href="#" data-target="#multiculturalism"><?php echo lang('multiculturalism'); ?></a></li>

                <?php if($puede_ver == 1) { ?>
                    <!-- <a href="#" class="btn btn-dark pull-right" id="beneficiaries_dashboard_pdf" style="margin: 5px 5px 0px 0px"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a> -->
                <?php } ?>
            </ul>
            
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="summary" style="min-height: 200px;">
                    <?php $this->load->view('ac_beneficiaries_dashboard/summary_tab'); ?>
                </div>

                <div role="tabpanel" class="tab-pane fade in " id="gender" style="min-height: 200px;">
                    <?php $this->load->view('ac_beneficiaries_dashboard/gender_tab'); ?>
                </div>

                <div role="tabpanel" class="tab-pane fade in" id="generations" style="min-height: 200px;">
                    <?php $this->load->view('ac_beneficiaries_dashboard/generation_tab'); ?>
                </div>

                <div role="tabpanel" class="tab-pane fade in" id="disability" style="min-height: 200px;">
                    <?php $this->load->view('ac_beneficiaries_dashboard/disability_tab'); ?>
                </div>

                <div role="tabpanel" class="tab-pane fade in" id="multiculturalism" style="min-height: 200px;">
                    <?php $this->load->view('ac_beneficiaries_dashboard/multiculturalism_tab'); ?>
                </div>
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
