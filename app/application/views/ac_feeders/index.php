<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="<?php echo get_uri("client_agreements_dashboard/index/".$client_area); ?>"><?php echo lang("community"); ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("feeders") ?></a>
</nav>
    <div class="panel">
    
       <div class="page-title panel-sky clearfix">
          <h1><?php echo lang("feeders");?></h1>
       </div>
       
	<?php
		$html = '';
		
		$html .= '<div class="row">';
		
			$icono = get_file_uri("assets/images/icons/light-bulb-2.png");
			$html .= '<div class="col-md-6 col-sm-6 widget-container">';
			$html .= '<a href="'.get_uri("AC_Feeders/societies").'" class="white-link">';
			$html .= '<div class="panel panel-list">';
			$html .= '<div class="panel-body">';
			$html .= '<div class="col-md-2">';
			$html .= '<div class="media-left">';
			$html .= '<span class="avatar avatar-sm border-circle">';
			$html .= '<img src="'.$icono.'" alt="..." class="mCS_img_loaded">';
			$html .= '</span>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="col-md-10 col-sm-10">';
			$html .= '<h4>'.lang("societies").'</h4>';
			//$html .= '<p class="m0"><label class="label label-info large">'.$ma->codigo.'</label></p>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</a>';
			$html .= '</div>';
					 
		// 	$icono = get_file_uri("assets/images/icons/factory.png");
		// 	$html .= '<div class="col-md-6 col-sm-6 widget-container">';
		// 	$html .= '<a href="'.get_uri("AC_Feeders/centrals").'" class="white-link">';
		// 	$html .= '<div class="panel panel-list">';
		// 	$html .= '<div class="panel-body">';
		// 	$html .= '<div class="col-md-2">';
		// 	$html .= '<div class="media-left">';
		// 	$html .= '<span class="avatar avatar-sm border-circle">';
		// 	$html .= '<img src="'.$icono.'" alt="..." class="mCS_img_loaded">';
		// 	$html .= '</span>';
		// 	$html .= '</div>';
		// 	$html .= '</div>';
		// 	$html .= '<div class="col-md-10 col-sm-10">';
		// 	$html .= '<h4>'.lang("centrals").'</h4>';
		// 	//$html .= '<p class="m0"><label class="label label-info large">'.$ma->codigo.'</label></p>';
		// 	$html .= '</div>';
		// 	$html .= '</div>';
		// 	$html .= '</div>';
		// 	$html .= '</a>';
		// 	$html .= '</div>';
				 
		// $html .= '</div>';
		
		// $html .= '<div class="row">';
		
			$icono = get_file_uri("assets/images/icons/contract.png");
			$html .= '<div class="col-md-6 col-sm-6 widget-container">';
			$html .= '<a href="'.get_uri("AC_Feeders/type_of_activities").'" class="white-link">';
			$html .= '<div class="panel panel-list">';
			$html .= '<div class="panel-body">';
			$html .= '<div class="col-md-2">';
			$html .= '<div class="media-left">';
			$html .= '<span class="avatar avatar-sm border-circle">';
			$html .= '<img src="'.$icono.'" alt="..." class="mCS_img_loaded">';
			$html .= '</span>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="col-md-10 col-sm-10">';
			$html .= '<h4>'.lang("ac_type_of_activities").'</h4>';
			//$html .= '<p class="m0"><label class="label label-info large">'.$ma->codigo.'</label></p>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</a>';
			$html .= '</div>';
		
		$html .= '</div>';

		
		$html .= '<div class="row">';
		
			$icono = get_file_uri("assets/images/icons/earth-day-8.png");
			$html .= '<div class="col-md-6 col-sm-6 widget-container">';
			$html .= '<a href="'.get_uri("AC_Feeders/beneficiary_objectives").'" class="white-link">';
			$html .= '<div class="panel panel-list">';
			$html .= '<div class="panel-body">';
			$html .= '<div class="col-md-2">';
			$html .= '<div class="media-left">';
			$html .= '<span class="avatar avatar-sm border-circle">';
			$html .= '<img src="'.$icono.'" alt="..." class="mCS_img_loaded">';
			$html .= '</span>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="col-md-10 col-sm-10">';
			$html .= '<h4>'.lang("ac_beneficiary_objectives").'</h4>';
			//$html .= '<p class="m0"><label class="label label-info large">'.$ma->codigo.'</label></p>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</a>';
			$html .= '</div>';
		
			$icono = get_file_uri("assets/images/icons/earth-day.png");
			$html .= '<div class="col-md-6 col-sm-6 widget-container">';
			$html .= '<a href="'.get_uri("AC_Feeders/activity_objectives").'" class="white-link">';
			$html .= '<div class="panel panel-list">';
			$html .= '<div class="panel-body">';
			$html .= '<div class="col-md-2">';
			$html .= '<div class="media-left">';
			$html .= '<span class="avatar avatar-sm border-circle">';
			$html .= '<img src="'.$icono.'" alt="..." class="mCS_img_loaded">';
			$html .= '</span>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="col-md-10 col-sm-10">';
			$html .= '<h4>'.lang("ac_activity_objectives").'</h4>';
			//$html .= '<p class="m0"><label class="label label-info large">'.$ma->codigo.'</label></p>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</a>';
			$html .= '</div>';
		
		$html .= '</div>';
		
		echo $html;
    
    ?>
              
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function () {

        
    });
</script>