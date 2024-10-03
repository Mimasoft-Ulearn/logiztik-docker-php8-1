<div id="page-content" class="p20 clearfix">
<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("client_agreements_dashboard/index/".$this->session->client_area); ?>"><?php echo lang("community"); ?> </a>
</nav>

	<div class="row">
    	<div class="col-md-12">
        
        	<div class="page-title clearfix">
            	<h1><i class="fa fa-th-large" title="Abierto"></i> <?php echo lang("community"); ?></h1>
            </div>
            
            <?php $icono = get_file_uri("assets/images/icons/".$client_agreements_info->icono); ?>
            
            <div class="row" style="background-color:#E5E9EC;">                
                <div class="col-md-4">
                
                	<div class="row">
                    	<div class="col-md-12 col-sm-12">
                            <div class="panel">
                                <div class="panel-heading bg-info p30"></div>
                                <div class="clearfix text-center">
                                    <span class="mt-50 avatar avatar-md chart-circle">
                                        <img src="<?php echo $icono; ?>" alt="..." style="background-color:#FFF;" class="mCS_img_loaded">
                                    </span>
                                </div>    

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                           <div class="panel no-border">
                              <div class="tab-title clearfix">
                                 <h4><?php echo lang("collaborators") ?></h4>
                                 <div class="title-button-group">
                                 </div>
                              </div>
                              <div class="table-responsive">
                                 <div id="project-member-table_wrapper" class="dataTables_wrapper no-footer"></div>
                                 <table id="project-member-table" class="b-b-only no-thead dataTable no-footer" width="100%">
                                    <thead>
                                       <tr role="row">
                                          <th class="sorting_asc" tabindex="0" aria-controls="project-member-table" rowspan="1" colspan="1" aria-label=": activate to sort column descending" aria-sort="ascending"></th>
                                          <th class="text-center option w100 sorting" tabindex="0" aria-controls="project-member-table" rowspan="1" colspan="1" aria-label=": activate to sort column ascending"></th>
                                       </tr>
                                    </thead>
                                    <tbody>

                                    	<?php if(count($ejecutores)) { ?>                                      
											<?php foreach($ejecutores as $ejecutor){?>
                                                <tr role="row" class="even project_member_box">
                                                    <td>
                                                        <div class="pull-left">
                                                            <!-- <a href="<?php echo get_uri("project_info/view_user_profile/".$ejecutor["id"]); ?>"> -->
																<?php
                                                                    $logo = $ejecutor["image"] ? get_file_uri("files/profile_images/".$ejecutor["image"]): get_file_uri("assets/images/avatar.jpg");
                                                                ?>
                                                                <span class="avatar avatar-xs mr10"><img src="<?php echo $logo; ?>" alt="..." class="mCS_img_loaded"></span> 
                                                                <?php echo $ejecutor["first_name"] . ' ' . $ejecutor["last_name"] ?> 
                                                            <!-- </a> -->
                                                         </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>

                                    </tbody>
                                 </table>
                                <a href="#" class="load_more pull-left p15"></a>
                                <a href="#" class="load_less pull-right p15"><?php echo lang("view_less");?></a>
                              </div>
                           </div>                
                        </div>                        
                    </div>
                </div>
                <div class="col-md-8">
                
                	<div class="row">
                    
                        <div class="col-md-12 col-sm-12">
                        	<div class="panel">
                        		<div class="p15" align="justify">
                                	<?php echo $client_agreements_info->descripcion; ?>
                       			</div>
                        	</div>
                        </div>
                        
                        <div class="col-md-12 col-sm-12">
                        	<div class="panel">
                                <div class="tab-title clearfix">
                                    <h4><?php echo lang("content");?></h4>
                                </div>
                        		<div class="p15" align="justify">
                        			<?php echo $client_agreements_info->contenido; ?>
                       			</div>
                        	</div>
                        </div>

                    </div>
                </div>
            </div>

        </div>       
    </div>

</div>

<style>

    .project_member_box{
        display:none;
    }

</style>


<script type="text/javascript">

    $(document).ready(function(){

        $('.load_more').hide();
        $('.load_less').hide();

        let max_users = 5; // Cantidad maximas de colaboradores a mostrar
        let cant_users = <?php echo count($ejecutores); ?> // Cantidad de colaboradores

        $('.project_member_box:lt(' + max_users + ')').show(); // Mostrar las filas que esten por debajo de la cantidad máxima


        if(cant_users > max_users){
            $('.load_more').text('<?php echo lang("view");?>  ' + (cant_users - max_users) + '  <?php echo strtolower(lang("more")); ?>');
            $('.load_more').show();
        }

        $('.load_more').click(function () {
            $('.load_less').show();
            $('.load_more').hide();
            $('.project_member_box:lt('+cant_users+')').show();
        });

        $('.load_less').click(function () {
            $('.load_more').show();
            $('.load_less').hide();
            $('.project_member_box').not(':lt('+max_users+')').hide();
        });
        
    });

</script>