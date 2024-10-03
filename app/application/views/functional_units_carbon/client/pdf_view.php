<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $proyecto->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo ucwords(lang("carbon_environmental_footprints"))." - ".lang("functional_units"); ?>
</h2>

<?php if($start_date && $end_date){ ?>
	<div align="center">
		<i><?php echo lang("corresponding_to_date_range")." ".$rango_fechas; ?></i>
	</div>
<?php } ?>

<div align="center">
	<?php $hora = convert_to_general_settings_time_format($proyecto->id, convert_date_utc_to_local(get_current_utc_time("H:i:s"), $format = "H:i:s", $proyecto->id));  ?>
	<?php echo lang("datetime_download") . ": " . get_date_format(date('Y-m-d'), $proyecto->id).' '.lang("at").' '.$hora; ?>
</div>

  <?php if($puede_ver == 1) { ?>

	<?php foreach($ids_uf as $index => $id_uf){ ?>
		<?php 
		$imagenes_graficos = $imagenes_graficos_por_uf[$id_uf];
		$grafico_impactos_por_huella = $imagenes_graficos['image_impactos_por_huella'];
		$grafico_proporcion_mensual = $imagenes_graficos['image_proporcion_mensual'];

		$unidad_funcional = $unidades_funcionales[$id_uf];
		?>
		<?php if($index > 0){ ?> 
			<br pagebreak="true">
		<?php } ?>

		<h2><?php echo lang("functional_unit") . ': ' . $unidad_funcional->nombre; ?></h2>
		<br>
	
		<br><br>
		<table cellspacing="0" cellpadding="4" border="0">
			<tr>
				<td align="center"><img src="<?php echo $grafico_impactos_por_huella; ?>" style="height:380px; width:570px;" /></td>
			</tr>
		</table>
		<br pagebreak="true">
		<table cellspacing="0" cellpadding="4" border="0">
			<tr>
				<td align="center"><img src="<?php echo $grafico_proporcion_mensual ?>" style="height:380px; width:570px;" /></td>
			</tr>
		</table>
		<br pagebreak="true">




		<h2><?php echo lang("environmental_impacts_by") . ' ' . $unidad_funcional->unidad. ' ' . lang("of") . ' ' . $unidad_funcional->nombre; ?></h2>
		<br>

        <?php
			
			$id_proyecto = $proyecto->id;
			
			$html = '';
			
			$html .= '<div style="width: 100%;">';
			$html .= '<table cellspacing="0" cellpadding="0" border="0">';
			
			$loop = 1;
			
			foreach($impacts_by_footprint[$id_uf]['huellas'] as $huella){

				if($loop % 4 == 1){
					$html .= '<tr>';
				}
				
				$html .= '<td style="text-align: center;">';

				$html .= '<table style="float: left;" border="0">';
				$html .= '<tr>';
				$html .= '<td style="text-align: center;">';

				$icono = $huella['icono'] ? "assets/images/impact-category/".$huella['icono'] : "assets/images/impact-category/empty.png";
				$html .= '<img src="'.$icono.'" style="height:50px; width:50px;" />';
				$html .= "<br>";

				$html .= $huella['valor'].'<br>';
				$html .= $huella['titulo'].'<br><br>';

				$html .= '</td>';
				$html .= '</tr>';
				$html .= '</table>';
				
				$html .= '</td>';
				
				if($loop % 4 == 0 || $loop == count($impacts_by_footprint[$id_uf]['huellas'])){
					$html .= '</tr>';
				}
				
				$loop++;
			}

			$html .= '</table>';
			$html .= '</div>';
			echo $html;
			
		?>

	<?php } ?>
	
  <?php } else { ?>
  
  <div style="width: 100%;"> 
  	<?php echo lang("content_disabled"); ?>
  </div>
  
  <?php } ?>

</body>