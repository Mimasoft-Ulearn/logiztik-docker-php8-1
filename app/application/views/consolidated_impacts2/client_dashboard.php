<div id="page-content" class="p20 clearfix">
	
	<!--Breadcrumb section-->
	<nav class="breadcrumb">
	<a class="breadcrumb-item" href="<?php echo get_uri("consolidated_impacts"); ?>"><?php echo lang("consolidated_impacts"); ?> </a>
	</nav>

	<!-- SECCIÓN CONSUMOS -->
	<?php echo $this->load->view("consolidated_impacts2/consumption", true); ?>

	<!-- SECCIÓN RESIDUOS -->
	<?php echo $this->load->view("consolidated_impacts2/waste", true); ?>

	<!-- SECCIONES: iMPACTOS TOTALES | IMPACTOS POR UNIDAD(ES) FUNCIONAL(ES) -->
	<?php echo $this->load->view("consolidated_impacts2/impacts", true); ?>

</div>


<script type="text/javascript">
	$(document).ready(function () {

		$(document).on('click', 'a.accordion-toggle', function () {
			
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