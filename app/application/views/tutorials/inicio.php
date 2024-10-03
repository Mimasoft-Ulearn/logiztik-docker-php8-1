<div id="page-content" class="p20 clearfix">
	<!--Breadcrumb section-->
	<nav class="breadcrumb">
		<a class="breadcrumb-item"
			href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
		<a class="breadcrumb-item" href="<?php echo get_uri("tutorials"); ?>"><?php echo lang("tutorials"); ?></a>
	</nav>

	<div class="">
		<?php if (isset($page_type) && $page_type === "full") { ?>
		<div id="page-content" class="m20 clearfix">
			<?php } ?>

			<div class="row">
				<div class="col-md-12">
					<div class="page-title clearfix" style="background-color:#FFF;">
						<h1 style="font-size:20px"><?php echo lang("tutorials"); ?></h1>
					</div>
					<div class="panel panel-default">

						<div class="panel-group" id="accordion1">

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse1" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> 
												Inicio Sesión y cambio de contraseña
											</h4>
										</a>
									</h4>
								</div>
								<div id="collapse1" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/1. Inicio Sesión y cambio de contraseña - Kaufmann.mp4" type="video/mp4">   
											</video>
										</div>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse2" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> 
												Recuperación contraseña
											</h4>
										</a>
									</h4>
								</div>
								<div id="collapse2" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/2. Recuperación contraseña - Kaufmann.mp4" type="video/mp4">   
											</video>
										</div>

									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse3" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> 
												Acceso a proyectos
											</h4>
										</a>
									</h4>
								</div>
								<div id="collapse3" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/3. Acceso proyectos - Kaufmann.mp4" type="video/mp4">   
											</video>
										</div>

									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse4" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> 
                                                Módulo de Huellas
											</h4>
										</a>
									</h4>
								</div>
								<div id="collapse4" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/4. Modulo de huellas  - ACP.mp4" type="video/mp4">   
											</video>
										</div>

									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse5" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> 
                                                Módulo Cumplimiento
											</h4>
										</a>
									</h4>
								</div>
								<div id="collapse5" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/5. Modulo Cumplimiento  - ACP.mp4" type="video/mp4">   
											</video>
										</div>

									</div>
								</div>
							</div>
							
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse6" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> 
                                                Módulo Contingencias
											</h4>
										</a>
									</h4>
								</div>
								<div id="collapse6" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/6. Modulo Contingencias  - ACP.mp4" type="video/mp4">   
											</video>
										</div>

									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse7" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> 
                                                Registros + Residuos
											</h4>
										</a>
									</h4>
								</div>
								<div id="collapse7" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/7. Registros + Residuos - ACP.mp4" type="video/mp4">   
											</video>
										</div>

									</div>
								</div>
							</div>

							

						</div>
						
						
					</div>
				</div>
			</div>

			<?php if (isset($page_type) && $page_type === "full") { ?>
		</div>
		<?php } ?>
		<?php
        if (!isset($project_labels_dropdown)) {
            $project_labels_dropdown = "0";
        }
        ?>
		<script type="text/javascript">
			$(document).ready(function () {
				$(document).on('click', 'a.accordion-toggle', function () {
					$('a.accordion-toggle i').removeClass('fa fa-minus-circle font-16');
					$('a.accordion-toggle i').addClass('fa fa-plus-circle font-16');
					
					var icon = $(this).find('i');
					
					if($(this).hasClass('collapsed')){
						icon.removeClass('fa fa-minus-circle font-16');
						icon.addClass('fa fa-plus-circle font-16');
					} else {
						icon.removeClass('fa fa-plus-circle font-16');
						icon.addClass('fa fa-minus-circle font-16');
					}
				});
			});
		</script>
	</div>
</div>