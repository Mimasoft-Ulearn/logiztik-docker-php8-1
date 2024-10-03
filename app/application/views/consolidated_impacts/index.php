<div id="page-content" class="p20 clearfix">
	
	<!--Breadcrumb section-->
	<nav class="breadcrumb">
	<a class="breadcrumb-item" href="<?php echo get_uri("consolidated_impacts"); ?>"><?php echo lang("consolidated_impacts"); ?> </a>
	</nav>

	<div class="panel panel-default">
		<div class="panel-body">
			<div class="col-md-6">
				<div class="form-group general-form multi-column">
					<label for="selected_year" class="col-md-3 text-center"><?php echo lang('select_query_year'); ?></label>
					<div class="col-md-4">
						<?php 
							echo form_input(array(
								"id" => "selected_year",
								"name" => "selected_year",
								"value" => $selected_year,
								"class" => "form-control datepicker",
								"placeholder" => lang('year'),
								"data-rule-required" => "true",
								"data-msg-required" => lang('field_required'),
								"autocomplete" => "off",
							));
						?>
					</div>
				</div>			
			</div>
		</div>
	</div>

	<div id="div_content">
		<?php echo $this->load->view("consolidated_impacts/client_dashboard", true); ?>
	</div>

</div>


<script type="text/javascript">
	$(document).ready(function () {

		$("#selected_year").datepicker({
			format: "yyyy",
			viewMode: "years", 
			minViewMode: "years",
            autoclose: true,
		}).on("changeYear", function(e) {

            let date = new Date(e.date);
            let year = date.getFullYear()

            // $(this).attr('disabled', true);
            appLoader.show();
            $("#div_content").html("<div class='panel'><div class='panel-default'><div style='padding:20px;'><div class='circle-loader'></div></div></div></div>");
            $.ajax({
                url:  '<?php echo_uri("Consolidated_impacts/index") ?>',
                type:  'post',
                data: {
                    year_changed: true,
                    selected_year: year
                },
                success: function(response){
                    $("#div_content").fadeOut(300, function() {
                        $("#div_content").html(response).removeClass("p20").fadeIn(300);
                    });
                    appLoader.hide();
                    // $(this).attr('disabled', false);
                }
            });

        });

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