<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<div>
<span style="float: left !important;"><img src="<?php echo $logo_cliente; ?>"></span>
</div>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $project_info->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo lang("report"); ?>
</h2>
<div align="center">
	<?php $hora = convert_to_general_settings_time_format($project_info->id, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $project_info->id));  ?>
	<?php echo lang("datetime_download") . ": " . get_date_format(date('Y-m-d'), $project_info->id).' '.lang("at").' '.$hora; ?>
</div>

<?php if($puede_ver_antecedentes_proyecto) { ?>
	<h2><?php echo lang("project_background"); ?></h2>
    <table cellspacing="0" cellpadding="4" border="1">
        <tr>
            <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("enterprise"); ?></td>
            <td width="160px"><?php echo $client_info->company_name; ?></td>
            <!-- <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("environmental_authorization"); ?></td> -->
            <!-- <td width="160px"><?php echo $autorizacion_ambiental; ?></td> -->
            <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("rut"); ?></td>
            <td width="160px"><?php echo $rut = (($project_info->client_label_rut)?$project_info->client_label_rut:'-'); ?></td>
        </tr>
        <tr>
            <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("production_site"); ?></td>
            <td width="160px"><?php echo $project_info->title; ?></td>
            <!-- <td style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("report_project_stage"); ?></td> -->
            <!-- <td><?php echo $etapa_proyecto; ?></td> -->
            <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("start_date_of_project"); ?></td>
            <td width="160px"><?php echo $project_info->start_date ? get_date_format($project_info->start_date, $project_info->id) : "-"; ?></td>
        </tr>
        <tr>
            <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("location"); ?></td>
            <td width="160px"><?php echo $ubicacion_proyecto; ?></td>
            <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("deadline_of_project"); ?></td>
            <td width="160px"><?php echo $project_info->deadline ? get_date_format($project_info->deadline, $project_info->id) : "-"; ?></td>
        </tr>
        <tr>
            <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("record_considerate_since"); ?></td>
            <td width="160px"><?php echo get_date_format($start_date, $project_info->id); ?></td>
            <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("record_considerate_until"); ?></td>
            <td width="160px"><?php echo get_date_format($end_date, $project_info->id); ?></td>
        </tr>
    </table>
    <table cellspacing="0" cellpadding="4" style="border: 1px solid white;">
        <tr>
            <td width="160px"></td>
            <td width="160px"></td>
            <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>; border: 1px solid black;"><?php echo lang("generate_by"); ?></td>
            <td width="160px" style="border: 1px solid black;"><?php echo $usuario_info->first_name.' '.$usuario_info->last_name; ?></td>
        </tr>
    </table>
<?php } ?>

<?php if($puede_ver_compromisos_rca && $id_compromiso_rca) { ?>
	<br pagebreak="true">
	<h2><?php echo lang("environmental_commitments").' - '.$autorizacion_ambiental; ?></h2>
    <!-- Tabla Compromisos RCA -->
    <table cellspacing="0" cellpadding="4" border="1">
        <thead>
            <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                <th rowspan="2" style="vertical-align:middle; text-align: center;"><?php echo lang("compliance_status"); ?></th>
                <?php foreach($evaluados_matriz_compromiso as $evaluado) { ?>
                    <th colspan="2" style="text-align: center;"><?php echo $evaluado->nombre_evaluado; ?></th>
                <?php } ?>
            </tr>
            <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                <?php foreach($evaluados_matriz_compromiso as $evaluado) { ?>
                    <th style="text-align: center;">N°</th>
                    <th style="text-align: center;">%</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th style="text-align: left;"><?php echo lang("total_applicable_compromises"); ?></th>
                <?php foreach($evaluados_matriz_compromiso as $evaluado) { ?>
                    <td style="text-align: right;"><?php echo to_number_project_format(array_sum($array_total_por_evaluado[$evaluado->id]), $project_info->id); ?></td>
                    <td style="text-align: right;"><?php echo to_number_project_format(100, $project_info->id).' %'; ?></td>
                <?php } ?>
            </tr>
            <?php foreach($array_estados_evaluados_rca as $estado_evaluado){ ?>
                <tr>
                    <td style="text-align: left;"><?php echo $estado_evaluado["nombre_estado"]; ?></td>
                    <?php foreach($estado_evaluado["evaluados"] as $id_evaluado => $evaluado) { ?>
                        <?php
                            $total_evaluado = array_sum($array_total_por_evaluado[$id_evaluado]);
							if($total_evaluado == 0){
								$porcentaje = 0;
							} else {
								$porcentaje = ($evaluado["cant"] * 100) / ($total_evaluado); 
							}
                        ?>
                        <td style="text-align: right;"><?php echo to_number_project_format($evaluado["cant"], $project_info->id); ?></td> 
                        <td style="text-align: right;"><?php echo to_number_project_format($porcentaje, $project_info->id).' %'; ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <br><br>
    
    <!-- Gráfico Compromisos RCA -->
    <table border="0">
        <tr>
            <td align="center" width="45%">
            	<?php if($grafico_cumplimientos_totales){ ?>
                	<div style="font-size:20px">&nbsp;</div>
            		<img src="<?php echo $grafico_cumplimientos_totales; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <div style="font-size:20px">&nbsp;</div>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
            <td width="55%">
                <table cellspacing="0" cellpadding="4" border="1">
                <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                    <th style="text-align: center;"><?php echo lang("compromise"); ?></th>
                    <th style="text-align: center;"><?php echo lang("critical_level"); ?></th>
                    <th style="text-align: center;"><?php echo lang("responsible"); ?></th>
                    <th style="text-align: center;"><?php echo lang("closing_term"); ?></th>
                </tr>
                
                <?php if($array_compromisos_evaluaciones_no_cumple){ ?>
                
					<?php foreach($array_compromisos_evaluaciones_no_cumple as $row){ ?>
                        <tr>
                            <td style="text-align: left;"><?php echo $row->nombre_compromiso; ?></td>
                            <td style="text-align: left;"><?php echo $row->criticidad; ?></td>
                            <td style="text-align: left;"><?php echo $row->responsable_reporte; ?></td>
                            <td style="text-align: left;"><?php echo get_date_format($row->plazo_cierre, $project_info->id); ?></td>
                        </tr>
                    <?php } ?>
                    
                <?php } else { ?>
                	<tr>
                        <td colspan="4" style="text-align: center;"><?php echo lang("no_information_available"); ?></td>
                    </tr>
                <?php } ?>
                </table>
            </td>
        </tr>
    </table>
        
<?php } ?>


<?php if($puede_ver_compromisos_reportables) { ?>
	<br pagebreak="true">
	<h2><?php echo lang("environmental_reportable_commitments").' - '.$autorizacion_ambiental; ?></h2>
	<!-- Tabla Compromisos Reportables -->
    <table cellspacing="0" cellpadding="4" border="1">
        <thead>
            <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                <th rowspan="2" style="vertical-align:middle; text-align: center;"><?php echo lang("general_compliance_status"); ?></th>
                <th colspan="2" style="vertical-align:middle; text-align: center;"><?php echo lang("sub_total"); ?></th>
            </tr>
            <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                <th style="text-align: center;">N°</th>
                <th style="text-align: center;">%</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($array_estados_evaluados_reportables as $estado_evaluado) { ?>
            	<?php 
					if($total_evaluado == 0){
						$porcentaje = 0;
					} else {
						$porcentaje = ($estado_evaluado["cant"] * 100) / ($total_evaluado);
					}
				?>
                <tr>
                    <th style="text-align: left;"><?php echo $estado_evaluado["nombre_estado"]; ?></th>
                    <td style="text-align: right;"><?php echo to_number_project_format($estado_evaluado["cant"], $project_info->id); ?></td>
                    <td style="text-align: right;"><?php echo to_number_project_format($porcentaje, $project_info->id).' %'; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <br><br>
    
    <!-- Gráfico Compromisos Reportables -->
    <table border="0">
        <tr>
            <td align="center" width="45%">
            	<?php if($grafico_cumplimientos_reportables){ ?>
                	<div style="font-size:20px">&nbsp;</div>
            		<img src="<?php echo $grafico_cumplimientos_reportables; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                	<div style="font-size:20px">&nbsp;</div>
                	<?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
            <td width="55%">
                <table cellspacing="0" cellpadding="4" border="1">
                <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                    <th style="text-align: center;"><?php echo lang("compromise"); ?></th>
                    <th style="text-align: center;"><?php echo lang("critical_level"); ?></th>
                    <th style="text-align: center;"><?php echo lang("responsible"); ?></th>
                    <th style="text-align: center;"><?php echo lang("closing_term"); ?></th>
                </tr>
                
                <?php if($array_compromisos_reportables_evaluaciones_no_cumple){ ?>
                
					<?php foreach($array_compromisos_reportables_evaluaciones_no_cumple as $row){ ?>
                        <tr>
                            <td style="text-align: left;"><?php echo $row->nombre_compromiso; ?></td>
                            <td style="text-align: left;"><?php echo $row->criticidad; ?></td>
                            <td style="text-align: left;"><?php echo $row->responsable_reporte; ?></td>
                            <td style="text-align: left;"><?php echo get_date_format($row->plazo_cierre, $project_info->id); ?></td>
                        </tr>
                    <?php } ?>
                    
                <?php } else { ?>
                	<tr>
                        <td colspan="4" style="text-align: center;"><?php echo lang("no_information_available"); ?></td>
                    </tr>
                <?php } ?>
                
                </table>
            </td>
        </tr>
    </table>
        
<?php } ?>

<?php if($puede_ver_consumos) { ?>
	<br pagebreak="true">
    <h2><?php echo lang("consumptions").' - '.lang("totals"); ?></h2>
    <!-- Tabla Consumos -->
    <table cellspacing="0" cellpadding="4" border="1">
    	<tr style="background-color:<?php echo $client_info->color_sitio; ?>;">
        	<th colspan="3" style="text-align: center;"><?php echo lang("consumptions"); ?></th>
        </tr>
        <tr>
			<th style="text-align: center;"><?php echo lang("categories"); ?></th>
			<th style="text-align: center;"><?php echo lang("Reported_in_period"); ?></th>
			<th style="text-align: center;"><?php echo lang("accumulated"); ?></th>
		</tr>
        <?php foreach ($tabla_consumo_volumen_reportados as $id_categoria => $arreglo_valores){ ?>
        	
            <?php 
				$arreglo_valores_acumulados = $tabla_consumo_volumen_acumulados[$id_categoria];

				$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $client_info->id, 'deleted' => 0));
				if($row_alias->alias){
					$nombre_categoria = $row_alias->alias;
				}else{
					$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
					$nombre_categoria = $row_categoria->nombre;
                }
                
				$reportado = to_number_project_format(array_sum($arreglo_valores), $project_info->id);
				$acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id);
			?>
            
            <tr>
            	<td><?php echo $nombre_categoria.' ('.$unidad_volumen.')'; ?></td>
                <td style="text-align: right;"><?php echo $reportado; ?></td>
                <td style="text-align: right;"><?php echo $acumulado; ?></td>
            </tr>
            
        <?php } ?>
        
        <?php foreach ($tabla_consumo_masa_reportados as $id_categoria => $arreglo_valores){ ?>
        	
            <?php 
				$arreglo_valores_acumulados = $tabla_consumo_masa_acumulados[$id_categoria];

				$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $client_info->id, 'deleted' => 0));
				if($row_alias->alias){
					$nombre_categoria = $row_alias->alias;
				}else{
					$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
					$nombre_categoria = $row_categoria->nombre;
				}
				
				$reportado = to_number_project_format(array_sum($arreglo_valores), $project_info->id);
				$acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id);
			?>
            
            <tr>
            	<td><?php echo $nombre_categoria.' ('.$unidad_masa.')'; ?></td>
                <td style="text-align: right;"><?php echo $reportado; ?></td>
                <td style="text-align: right;"><?php echo $acumulado; ?></td>
            </tr>
            
        <?php } ?>
        
        
        <?php foreach ($tabla_consumo_potencia_reportados as $id_categoria => $arreglo_valores){ ?>
        	
            <?php 
                $arreglo_valores_acumulados = $tabla_consumo_potencia_acumulados[$id_categoria];

                $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $client_info->id, 'deleted' => 0));
                if($row_alias->alias){
                    $nombre_categoria = $row_alias->alias;
                }else{
                    $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                    $nombre_categoria = $row_categoria->nombre;
                }

                $reportado = to_number_project_format(array_sum($arreglo_valores), $project_info->id);
                $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id);
			?>
            
            <tr>
            	<td><?php echo $nombre_categoria.' ('.$unidad_potencia.')'; ?></td>
                <td style="text-align: right;"><?php echo $reportado; ?></td>
                <td style="text-align: right;"><?php echo $acumulado; ?></td>
            </tr>
            
        <?php } ?>

        <?php foreach ($tabla_consumo_volumen_especies_reportados as $id_categoria => $arreglo_valores){ ?>
                        
            <?php
                $arreglo_valores_acumulados = $tabla_consumo_volumen_especies_acumulados[$id_categoria];
                $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                if($row_alias->alias){
                    $nombre_categoria = $row_alias->alias;
                }else{
                    $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                    $nombre_categoria = $row_categoria->nombre;
                }

                $options = array(
                    'id_client' => $client_info->id, 
                    'id_project' => $project_info->id,
                    'id_client_module' => $id_modulo_consumos,
                    'alert_config' => array(
                        'id_categoria' => $id_categoria,
                        'id_tipo_unidad' => 2,
                    )
                );
                $reportado = to_number_project_format(array_sum($arreglo_valores), $project_info->id);
                $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id);
            ?>

            <tr>
                <td><?php echo $nombre_categoria.' ('.$unidad_volumen.')'; ?></td>
                <td style="text-align: right;"><?php echo $reportado; ?></td>
                <td style="text-align: right;"><?php echo $acumulado; ?></td>
            </tr>

        <?php } ?>

        <?php
            $total_especies_reportados_masa_consumo = 0;
            $total_especies_acumulados_masa_consumo = 0;
        ?>
        <?php foreach ($tabla_consumo_masa_especies_reportados as $id_categoria => $arreglo_valores){ ?>
            
            <?php
                $arreglo_valores_acumulados = $tabla_consumo_masa_especies_acumulados[$id_categoria];
                $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                if($row_alias->alias){
                    $nombre_categoria = $row_alias->alias;
                }else{
                    $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                    $nombre_categoria = $row_categoria->nombre;
                }

                $options = array(
                    'id_client' => $client_info->id, 
                    'id_project' => $project_info->id,
                    'id_client_module' => $id_modulo_consumos,
                    'alert_config' => array(
                        'id_categoria' => $id_categoria,
                        'id_tipo_unidad' => 2,
                    )
                );
                $reportado = to_number_project_format(array_sum($arreglo_valores), $project_info->id);
                $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id);
            ?>

            <tr>
                <td><?php echo $nombre_categoria.' ('.$unidad_masa.')'; ?></td>
                <td style="text-align: right;"><?php echo $reportado; ?></td>
                <td style="text-align: right;"><?php echo $acumulado; ?></td>
            </tr>

            <?php
                $total_especies_reportados_masa_consumo += array_sum($arreglo_valores);;
                $total_especies_acumulados_masa_consumo += array_sum($arreglo_valores_acumulados);
            ?>

        <?php } ?>


        <?php foreach ($tabla_consumo_potencia_especies_reportados as $id_categoria => $arreglo_valores){ ?>
            
            <?php
                $arreglo_valores_acumulados = $tabla_consumo_potencia_especies_acumulados[$id_categoria];
                $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                if($row_alias->alias){
                    $nombre_categoria = $row_alias->alias;
                }else{
                    $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                    $nombre_categoria = $row_categoria->nombre;
                }

                $options = array(
                    'id_client' => $client_info->id, 
                    'id_project' => $project_info->id,
                    'id_client_module' => $id_modulo_consumos,
                    'alert_config' => array(
                        'id_categoria' => $id_categoria,
                        'id_tipo_unidad' => 2,
                    )
                );
                $reportado = to_number_project_format(array_sum($arreglo_valores), $project_info->id);
                $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id);
            ?>

            <tr>
                <td><?php echo $nombre_categoria.' ('.$unidad_potencia.')'; ?></td>
                <td style="text-align: right;"><?php echo $reportado; ?></td>
                <td style="text-align: right;"><?php echo $acumulado; ?></td>
            </tr>

        <?php } ?>

            <tr>
                <td><?php echo lang("total_species_produced").' ('.$unidad_masa.')'; ?></td>
                <td style="text-align: right;"><?php echo to_number_project_format($total_especies_reportados_masa_consumo, $project_info->id); ?></td>
                <td style="text-align: right;"><?php echo to_number_project_format($total_especies_acumulados_masa_consumo, $project_info->id); ?></td>
            </tr>
        
    </table>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_consumo_volumen; ?>" style="height:380px; width:570px;" /></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_consumo_masa; ?>" style="height:380px; width:570px;" /></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_consumo_potencia; ?>" style="height:380px; width:570px;" /></td>
        </tr>
    </table>
    
<?php } ?>


<?php if($puede_ver_residuos) { ?>
	<br pagebreak="true">
    <h2><?php echo lang("waste").' - '.lang("totals"); ?></h2>
    <!-- Tabla Residuos -->
    <table cellspacing="0" cellpadding="4" border="1">
    	<tr style="background-color:<?php echo $client_info->color_sitio; ?>;">
        	<th colspan="3" style="text-align: center;"><?php echo lang("waste"); ?></th>
        </tr>
        <tr>
			<th style="text-align: center;"><?php echo lang("categories"); ?></th>
			<th style="text-align: center;"><?php echo lang("Reported_in_period"); ?></th>
			<th style="text-align: center;"><?php echo lang("accumulated"); ?></th>
		</tr>
        
        <?php foreach($tabla_residuo_volumen_reportados as $id_categoria => $arreglo_valores){ ?>
        	
            <?php
            
			$arreglo_valores_acumulados = $tabla_residuo_volumen_acumulados[$id_categoria];
			$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $client_info->id, 'deleted' => 0));
			if($row_alias->alias){
				$nombre_categoria = $row_alias->alias;
			}else{
				$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
				$nombre_categoria = $row_categoria->nombre;
			}
			
			?>
            
            <tr>
            	<td><?php echo $nombre_categoria.' ('.$unidad_volumen.')'; ?></td>
                <td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores), $project_info->id); ?></td>
                <td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id); ?></td>
            </tr>
            
        <?php } ?>
        
        <?php foreach($tabla_residuo_masa_reportados as $id_categoria => $arreglo_valores){ ?>
        
        	<?php
            
			$arreglo_valores_acumulados = $tabla_residuo_masa_acumulados[$id_categoria];
			$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
			if($row_alias->alias){
				$nombre_categoria = $row_alias->alias;
			}else{
				$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
				$nombre_categoria = $row_categoria->nombre;
			}
			
			?>
        	
            <tr>
            	<td><?php echo $nombre_categoria.' ('.$unidad_masa.')'; ?></td>
                <td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores), $project_info->id); ?></td>
                <td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id); ?></td>
            </tr>
            
        <?php } ?>

        <?php foreach ($tabla_residuo_volumen_especies_reportados as $id_categoria => $arreglo_valores){ ?>
                        
            <?php
                $arreglo_valores_acumulados = $tabla_residuo_volumen_especies_acumulados[$id_categoria];
                $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                if($row_alias->alias){
                    $nombre_categoria = $row_alias->alias;
                }else{
                    $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                    $nombre_categoria = $row_categoria->nombre;
                }
                
                $alerta = (array_sum($arreglo_valores_acumulados) > array_sum($arreglo_valores))?"warning":"";
                $reportado = to_number_project_format(array_sum($arreglo_valores), $project_info->id);
                $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id);
            ?>

            <tr>
                <td class="text-left"><?php echo $nombre_categoria.' ('.$unidad_volumen.')'; ?></td>
                <td style="text-align: right;"><?php echo $reportado; ?></td>
                <td style="text-align: right;"><?php echo $acumulado; ?></td>
            </tr>

        <?php } ?>


        <?php
            $total_especies_reportados_masa_residuo = 0;
            $total_especies_acumulados_masa_residuo = 0;
        ?>
        <?php foreach ($tabla_residuo_masa_especies_reportados as $id_categoria => $arreglo_valores){ ?>

            <?php
                $arreglo_valores_acumulados = $tabla_residuo_masa_especies_acumulados[$id_categoria];
                $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                if($row_alias->alias){
                    $nombre_categoria = $row_alias->alias;
                }else{
                    $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                    $nombre_categoria = $row_categoria->nombre;
                }
                
                $alerta = (array_sum($arreglo_valores_acumulados) > array_sum($arreglo_valores))?"warning":"";
                $reportado = to_number_project_format(array_sum($arreglo_valores), $project_info->id);
                $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id);
            ?>

            <tr>
                <td class="text-left"><?php echo $nombre_categoria.' ('.$unidad_masa.')'; ?></td>
                <td style="text-align: right;"><?php echo $reportado; ?></td>
                <td style="text-align: right;"><?php echo $acumulado; ?></td>
            </tr>

            <?php
                $total_especies_reportados_masa_residuo += array_sum($arreglo_valores);
                $total_especies_acumulados_masa_residuo += array_sum($arreglo_valores_acumulados);
            ?>

        <?php } ?>

        <tr>
            <td><?php echo lang("total_species_produced").' ('.$unidad_masa.')'; ?></td>
            <td style="text-align: right;"><?php echo to_number_project_format($total_especies_reportados_masa_residuo, $project_info->id); ?></td>
            <td style="text-align: right;"><?php echo to_number_project_format($total_especies_acumulados_masa_residuo, $project_info->id); ?></td>
        </tr>

    </table>
        
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_residuo_volumen; ?>" style="height:380px; width:570px;" /></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_residuo_masa; ?>" style="height:380px; width:570px;" /></td>
        </tr>
    </table>

<?php } ?>

<?php if($puede_ver_permittings && $report_config->permittings && count($evaluados_matriz_permiso)) { ?>
<br pagebreak="true">
    <h2><?php echo lang("environmental_permittings"); ?></h2>
	<!-- Tabla Permisos -->
    <table cellspacing="0" cellpadding="4" border="1">
        <thead>
            <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                <th rowspan="2" style="vertical-align:middle; text-align: center;"><?php echo lang("general_procedure_status"); ?></th>
                <?php foreach($evaluados_matriz_permiso as $evaluado) { ?>
                    <th colspan="2" style="text-align: center;"><?php echo $evaluado->nombre_evaluado; ?></th>
                <?php } ?>
            </tr>
            <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                <?php foreach($evaluados_matriz_permiso as $evaluado) { ?>
                    <th style="text-align: center;">N°</th>
                    <th style="text-align: center;">%</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th style="text-align: left;"><?php echo lang("total_applicable_permittings"); ?></th>
                <?php foreach($evaluados_matriz_permiso as $evaluado) { ?>
                    <td style="text-align: right;"><?php echo to_number_project_format(array_sum($array_total_por_evaluado_permiso[$evaluado->id]), $project_info->id); ?></td>
                    <td style="text-align: right;"><?php echo to_number_project_format(100, $project_info->id).' %'; ?></td>
                <?php } ?>
            </tr>
            <?php foreach($array_estados_evaluados_permiso as $estado_evaluado){ ?>
                <tr>
                    <td style="text-align: left;"><?php echo $estado_evaluado["nombre_estado"]; ?></td>
                    <?php foreach($estado_evaluado["evaluados"] as $id_evaluado => $evaluado) { ?>
                        <?php
                            $total_evaluado = array_sum($array_total_por_evaluado[$id_evaluado]);
							if($total_evaluado == 0){
								$porcentaje = 0;
							} else {
								$porcentaje = ($evaluado["cant"] * 100) / ($total_evaluado); 
							}
                        ?>
                        <td style="text-align: right;"><?php echo to_number_project_format($evaluado["cant"], $project_info->id); ?></td> 
                        <td style="text-align: right;"><?php echo to_number_project_format($porcentaje, $project_info->id).' %'; ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <br><br>
    
    <!-- Gráfico Permisos -->
    <table border="0">
        <tr>
            <td align="center" width="45%">
            	<?php if($grafico_cumplimientos_totales_permisos){ ?>
                	<div style="font-size:20px">&nbsp;</div>
            		<img src="<?php echo $grafico_cumplimientos_totales_permisos; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                	<div style="font-size:20px">&nbsp;</div>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
            <td width="55%">
                <table cellspacing="0" cellpadding="4" border="1">
                <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                    <th style="text-align: center;"><?php echo lang("compromise"); ?></th>
                    <th style="text-align: center;"><?php echo lang("critical_level"); ?></th>
                    <th style="text-align: center;"><?php echo lang("responsible"); ?></th>
                    <th style="text-align: center;"><?php echo lang("closing_term"); ?></th>
                </tr>

                <?php if($array_permisos_evaluaciones_no_cumple){ ?>
                
					<?php foreach($array_permisos_evaluaciones_no_cumple as $row){ ?>
                        <tr>
                            <td style="text-align: left;"><?php echo $row->nombre_permiso; ?></td>
                            <td style="text-align: left;"><?php echo $row->criticidad; ?></td>
                            <td style="text-align: left;"><?php echo $row->responsable_reporte; ?></td>
                            <td style="text-align: left;"><?php echo get_date_format($row->plazo_cierre, $project_info->id); ?></td>
                        </tr>
                    <?php } ?>
                    
                <?php } else { ?>
                	<tr>
                        <td colspan="4" style="text-align: center;"><?php echo lang("no_information_available"); ?></td>
                    </tr>
                <?php } ?>
                
                </table>
            </td>
        </tr>
    </table>

<?php } ?>

<br pagebreak="true">

<!-- Nuevas secciones MIMAire -->

<?php
    // OBTENER NOMBRES O ALIAS DE CATEGORIAS DE MANERA DINAMICA 
    $array_nombre_categorias = array(
        "agua_industrial" => array("id" => 15),
        "agua_potable" => array("id" => 16),
        "electricidad" => array("id" => 250),
        "equipos_generacion_electrica" => array("id" => 131),
        "gas_licuado" => array("id" => 158),
    );
    
    foreach($array_nombre_categorias as $cat => $categoria){
        $row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $categoria["id"], 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
        if($row_alias->alias){
            $nombre_categoria = $row_alias->alias;
        }else{
            $row_categoria = $Categories_model->get_one_where(array('id' => $categoria["id"], 'deleted' => 0));
            $nombre_categoria = $row_categoria->nombre;
        }
        $array_nombre_categorias[$cat]["nombre"] = $nombre_categoria;
    }
?>

<!-- INDICADORES - AGUA -->
<h2><?php echo lang("indicators").' - '.lang("water"); ?></h1>

<table cellspacing="0" cellpadding="4" border="1">
    <tr style="background-color:<?php echo $client_info->color_sitio; ?>;">
        <th colspan="3" style="text-align: center;"><?php echo lang("monthly_levels")." - ".lang("water"); ?></th>
    </tr>
    <tr>
        <th style="text-align: center;"><?php echo lang("months"); ?></th>
        <!--<th style="text-align: center;"><?php echo lang("industrial_water")." (".$unidad_volumen.")"; ?></th>
        <th style="text-align: center;"><?php echo lang("drinking_water")." (".$unidad_volumen.")"; ?></th>-->
        <th style="text-align: center;"><?php echo $array_nombre_categorias["agua_industrial"]["nombre"]." (".$unidad_volumen.")"; ?></th>
        <th style="text-align: center;"><?php echo $array_nombre_categorias["agua_potable"]["nombre"]." (".$unidad_volumen.")"; ?></th>
    </tr>

    <?php foreach($rango_fechas as $anio => $meses) { ?>
        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
            <tr>
                <th style="text-align: center;"><?php echo $nombre_mes." - ".$anio; ?></th>
                <td style="text-align: center;">
                    <?php 
                        $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                            "id_cliente" => $client_info->id,
                            "id_proyecto" => $project_info->id,
                            "id_material" => 6, // Agua
                            "id_categoria" => 15, // Agua industrial
                            "id_tipo_unidad" => 2, // Volumen
                            "flujo" => "Consumo",
                            "mes" => $numero_mes,
                            "anio" => $anio
                        ));
                        echo to_number_project_format($valor_categoria["valor_categoria"], $project_info->id);
                    ?> 
                </td>
                <td style="text-align: center;">
                    <?php 
                        $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                            "id_cliente" => $client_info->id,
                            "id_proyecto" => $project_info->id,
                            "id_material" => 6, // Agua
                            "id_categoria" => 16, // Agua potable
                            "id_tipo_unidad" => 2, // Volumen
                            "flujo" => "Consumo",
                            "mes" => $numero_mes,
                            "anio" => $anio
                        ));
                        echo to_number_project_format($valor_categoria["valor_categoria"], $project_info->id);
                    ?> 
                </td>
            </tr>
        <?php } ?>
    <?php } ?>
</table>

<br pagebreak="true">

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_consumo_mensual_agua; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_consumo_produccion_agua; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>
<!-- FIN INDICADORES - AGUA -->

<br pagebreak="true">

<!-- INDICADORES - ENERGÍA -->
<h2><?php echo lang("indicators").' - '.lang("energy"); ?></h1>

<table cellspacing="0" cellpadding="4" border="1">
    <tr style="background-color:<?php echo $client_info->color_sitio; ?>;">
        <th colspan="4" style="text-align: center;"><?php echo lang("monthly_levels")." - ".lang("energy"); ?></th>
    </tr>
    <tr>
        <th style="text-align: center;"><?php echo lang("months"); ?></th>
        <!--<th style="text-align: center;"><?php echo lang("electricity")." (".$unidad_potencia.")"; ?></th>
        <th style="text-align: center;"><?php echo lang("generator_fuel")." (".$unidad_volumen.")"; ?></th>
        <th style="text-align: center;"><?php echo lang("liquid_gas")." (".$unidad_masa.")"; ?></th>-->
        <th style="text-align: center;"><?php echo $array_nombre_categorias["electricidad"]["nombre"]." (".$unidad_potencia.")"; ?></th>
        <th style="text-align: center;"><?php echo $array_nombre_categorias["equipos_generacion_electrica"]["nombre"]." (".$unidad_volumen.")"; ?></th>
        <th style="text-align: center;"><?php echo $array_nombre_categorias["gas_licuado"]["nombre"]." (".$unidad_masa.")"; ?></th>
    </tr>

    <?php foreach($rango_fechas as $anio => $meses) { ?>
        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
            <tr>
                <th style="text-align: center;"><?php echo $nombre_mes." - ".$anio; ?></th>
                <td style="text-align: center;">
                    <?php 
                        $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                            "id_cliente" => $client_info->id,
                            "id_proyecto" => $project_info->id,
                            "id_material" => 2, // Electricidad
                            "id_categoria" => 250, // Adquisición + pérdidas de electricidad
                            "id_tipo_unidad" => 6, // Potencia
                            "flujo" => "Consumo",
                            "mes" => $numero_mes,
                            "anio" => $anio
                        ));
                        echo to_number_project_format($valor_categoria["valor_categoria"], $project_info->id);
                    ?> 
                </td>
                <td style="text-align: center;">
                    <?php 
                        $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                            "id_cliente" => $client_info->id,
                            "id_proyecto" => $project_info->id,
                            "id_material" => 35, // Maquinaria
                            "id_categoria" => 131, // Equipos Generación Eléctrica
                            "id_tipo_unidad" => 2, // Volumen
                            "flujo" => "Consumo",
                            "mes" => $numero_mes,
                            "anio" => $anio
                        ));
                        echo to_number_project_format($valor_categoria["valor_categoria"], $project_info->id);
                    ?> 
                </td>
                <td style="text-align: center;">
                    <?php 
                        $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                            "id_cliente" => $client_info->id,
                            "id_proyecto" => $project_info->id,
                            "id_material" => 14, // Combustible en base a petróleo
                            "id_categoria" => 158, // Gas licuado
                            "id_tipo_unidad" => 1, // Masa
                            "flujo" => "Consumo",
                            "mes" => $numero_mes,
                            "anio" => $anio
                        ));
                        echo to_number_project_format($valor_categoria["valor_categoria"], $project_info->id);
                    ?> 
                </td>
            </tr>
        <?php } ?>
    <?php } ?>
</table>

<br pagebreak="true">

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_consumo_mensual_electricidad; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_consumo_produccion_electricidad; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>

<br pagebreak="true">

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_consumo_mensual_combustible; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_consumo_produccion_combustible; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>

<br pagebreak="true">

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_consumo_mensual_gas; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_consumo_produccion_gas; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>
<!-- FIN INDICADORES - ENERGÍA -->


<br pagebreak="true">


<!-- INDICADORES - PRODUCTOS FITOSANITARIOS -->
<h2><?php echo lang("indicators").' - '.lang("phytosanitary_products"); ?></h1>

<table cellspacing="0" cellpadding="4" border="1">
    <tr style="background-color:<?php echo $client_info->color_sitio; ?>;">
        <th colspan="<?php echo count($array_cat_prod_fito) + 1; ?>" style="text-align: center;"><?php echo lang("monthly_levels")." - ".lang("phytosanitary_products"); ?></th>
    </tr>
    <tr>
        <th style="text-align: center;"><?php echo lang("months"); ?></th>
        <?php foreach($array_cat_prod_fito as $id_cat => $nombre_cat){ ?>
            <?php 
                $row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                if($row_alias->alias){
                    $nombre_categoria = $row_alias->alias;
                }else{
                    $row_categoria = $Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                    $nombre_categoria = $row_categoria->nombre;
                }
            ?>
            <th style="text-align: center;"><?php echo $nombre_categoria." (".$unidad_volumen.")"; ?></th>
        <?php } ?>
    </tr>

    <?php foreach($rango_fechas as $anio => $meses) { ?>
        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
            <tr>
                <th style="text-align: center;"><?php echo $nombre_mes." - ".$anio; ?></th>
                <?php foreach($array_cat_prod_fito as $id_cat => $nombre_cat) { ?>
                    <td style="text-align: center;">
                        <?php 
                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                "id_cliente" => $client_info->id,
                                "id_proyecto" => $project_info->id,
                                "id_material" => 65, // Productos fitosanitarios
                                "id_categoria" => $id_cat,
                                "id_tipo_unidad" => 2, // Volumen
                                "flujo" => "Consumo",
                                "mes" => $numero_mes,
                                "anio" => $anio
                            ));
                            echo to_number_project_format($valor_categoria["valor_categoria"], $project_info->id);
                        ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    <?php } ?>
</table>

<br pagebreak="true">

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_consumo_mensual_pf; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_consumo_produccion_pf; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>
<!-- FIN INDICADORES - PRODUCTOS FITOSANITARIOS -->


<br pagebreak="true">


<!-- INDICADORES - REFRIGERANTES -->
<h2><?php echo lang("indicators").' - '.lang("refrigerants"); ?></h1>

<table cellspacing="0" cellpadding="4" border="1">
    <tr style="background-color:<?php echo $client_info->color_sitio; ?>;">
        <th colspan="<?php echo count($array_cat_prod_ref) + 1; ?>" style="text-align: center;"><?php echo lang("monthly_levels")." - ".lang("refrigerants"); ?></th>
    </tr>
    <tr>
        <th style="text-align: center;"><?php echo lang("months"); ?></th>
        <?php foreach($array_cat_prod_ref as $id_cat => $nombre_cat){ ?>
            <?php 
                $row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                if($row_alias->alias){
                    $nombre_categoria = $row_alias->alias;
                }else{
                    $row_categoria = $Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                    $nombre_categoria = $row_categoria->nombre;
                }
            ?>
            <th style="text-align: center;"><?php echo $nombre_categoria." (".$unidad_masa.")"; ?></th>
        <?php } ?>
    </tr>

    <?php foreach($rango_fechas as $anio => $meses) { ?>
        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
            <tr>
                <th style="text-align: center;"><?php echo $nombre_mes." - ".$anio; ?></th>
                <?php foreach($array_cat_prod_ref as $id_cat => $nombre_cat) { ?>
                    <td style="text-align: center;">
                        <?php 
                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                "id_cliente" => $client_info->id,
                                "id_proyecto" => $project_info->id,
                                "id_material" => 40, // Refrigerante
                                "id_categoria" => $id_cat,
                                "id_tipo_unidad" => 1, // Masa
                                "flujo" => "Consumo",
                                "mes" => $numero_mes,
                                "anio" => $anio
                            ));
                            echo to_number_project_format($valor_categoria["valor_categoria"], $project_info->id);
                        ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    <?php } ?>
</table>

<br pagebreak="true">

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_consumo_mensual_ref; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_consumo_produccion_ref; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>
<!-- FIN INDICADORES - PRODUCTOS REFRIGERANTES -->


<br pagebreak="true">


<!-- INDICADORES - RESIDUOS NO PELIGROSOS -->
<h2><?php echo lang("indicators").' - '.lang("non_hazardous_waste"); ?></h1>

<table cellspacing="0" cellpadding="4" border="1">
    <tr style="background-color:<?php echo $client_info->color_sitio; ?>;">
        <th colspan="<?php echo count($array_cat_prod_rsd) + count($array_cat_prod_rinp) + 1; ?>" style="text-align: center;"><?php echo lang("monthly_levels")." - ".lang("non_hazardous_waste"); ?></th>
    </tr>
    <tr>
        <th style="text-align: center;"><?php echo lang("months"); ?></th>
        <?php foreach($array_cat_prod_rsd as $id_cat => $nombre_cat){ ?>
            <?php 
                $row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                if($row_alias->alias){
                    $nombre_categoria = $row_alias->alias;
                }else{
                    $row_categoria = $Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                    $nombre_categoria = $row_categoria->nombre;
                }
            ?>
            <th style="text-align: center;"><?php echo $nombre_categoria." (".$unidad_masa.")"; ?></th>
        <?php } ?>
        <?php foreach($array_cat_prod_rinp as $id_cat => $nombre_cat){ ?>
            <th style="text-align: center;"><?php echo $nombre_cat." (".$unidad_masa.")"; ?></th>
        <?php } ?>
    </tr>

    <?php foreach($rango_fechas as $anio => $meses) { ?>
        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
            <tr>
                <th style="text-align: center;"><?php echo $nombre_mes." - ".$anio; ?></th>
                <?php foreach($array_cat_prod_rsd as $id_cat => $nombre_cat) { ?>
                    <td style="text-align: center;">
                        <?php 
                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                "id_cliente" => $client_info->id,
                                "id_proyecto" => $project_info->id,
                                "id_material" => 29, // Residuos sólidos domiciliarios
                                "id_categoria" => $id_cat,
                                "id_tipo_unidad" => 1, // Masa
                                "flujo" => "Residuo",
                                "mes" => $numero_mes,
                                "anio" => $anio
                            ));
                            echo to_number_project_format($valor_categoria["valor_categoria"], $project_info->id);
                        ?>
                    </td>
                <?php } ?>
                <?php foreach($array_cat_prod_rinp as $id_cat => $nombre_cat) { ?>
                    <td style="text-align: center;">
                        <?php 
                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                "id_cliente" => $client_info->id,
                                "id_proyecto" => $project_info->id,
                                "id_material" => 30, // Residuos Industriales no Peligrosos
                                "id_categoria" => $id_cat,
                                "id_tipo_unidad" => 1, // Masa
                                "flujo" => "Residuo",
                                "mes" => $numero_mes,
                                "anio" => $anio
                            ));
                            echo to_number_project_format($valor_categoria["valor_categoria"], $project_info->id);
                        ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    <?php } ?>

</table>

<br pagebreak="true">

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_residuo_mensual_nhw; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_residuo_produccion_nhw; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>
<!-- FIN INDICADORES - RESIDUOS NO PELIGROSOS -->


<br pagebreak="true">


<!-- INDICADORES - RESIDUOS PELIGROSOS -->
<h2><?php echo lang("indicators").' - '.lang("hazardous_waste"); ?></h1>

<table cellspacing="0" cellpadding="4" border="1">
    <tr style="background-color:<?php echo $client_info->color_sitio; ?>;">
        <th colspan="<?php echo count($array_cat_prod_rip_masa) + 1; ?>" style="text-align: center;"><?php echo lang("monthly_levels")." - ".lang("solid_hazardous_waste"); ?></th>
    </tr>
    <tr>
        <th style="text-align: center;"><?php echo lang("months"); ?></th>
        <?php foreach($array_cat_prod_rip_masa as $id_cat => $nombre_cat){ ?>
            <?php 
                $row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                if($row_alias->alias){
                    $nombre_categoria = $row_alias->alias;
                }else{
                    $row_categoria = $Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                    $nombre_categoria = $row_categoria->nombre;
                }
            ?>
            <th style="text-align: center;"><?php echo $nombre_categoria." (".$unidad_masa.")"; ?></th>
        <?php } ?>
    </tr>

    <?php foreach($rango_fechas as $anio => $meses) { ?>
        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
            <tr>
                <th style="text-align: center;"><?php echo $nombre_mes." - ".$anio; ?></th>
                <?php foreach($array_cat_prod_rip_masa as $id_cat => $nombre_cat) { ?>
                    <td style="text-align: center;">
                        <?php 
                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                "id_cliente" => $client_info->id,
                                "id_proyecto" => $project_info->id,
                                "id_material" => 33, // Residuos industriales peligrosos
                                "id_categoria" => $id_cat,
                                "id_tipo_unidad" => 1, // Masa
                                "flujo" => "Residuo",
                                "mes" => $numero_mes,
                                "anio" => $anio
                            ));
                            echo to_number_project_format($valor_categoria["valor_categoria"], $project_info->id);
                        ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    <?php } ?>

</table>

<br pagebreak="true">

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_residuo_mensual_hw; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_residuo_produccion_hw; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>

<br pagebreak="true">

<table cellspacing="0" cellpadding="4" border="1">
    <tr style="background-color:<?php echo $client_info->color_sitio; ?>;">
        <th colspan="<?php echo count($array_cat_prod_rip_volumen) + count($array_cat_prod_rli_volumen) + 1; ?>" style="text-align: center;"><?php echo lang("monthly_levels")." - ".lang("liquid_hazardous_waste"); ?></th>
    </tr>
    <tr>
        <th style="text-align: center;"><?php echo lang("months"); ?></th>
        <?php foreach($array_cat_prod_rip_volumen as $id_cat => $nombre_cat){ ?>
            <?php 
                $row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                if($row_alias->alias){
                    $nombre_categoria = $row_alias->alias;
                }else{
                    $row_categoria = $Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                    $nombre_categoria = $row_categoria->nombre;
                }
            ?>
            <th style="text-align: center;"><?php echo $nombre_categoria." (".$unidad_volumen.")"; ?></th>
        <?php } ?>
        <?php foreach($array_cat_prod_rli_volumen as $id_cat => $nombre_cat){ ?>
            <?php 
                $row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                if($row_alias->alias){
                    $nombre_categoria = $row_alias->alias;
                }else{
                    $row_categoria = $Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                    $nombre_categoria = $row_categoria->nombre;
                }
            ?>
        <th style="text-align: center;"><?php echo $nombre_categoria." (".$unidad_volumen.")"; ?></th>
        <?php } ?>
    </tr>

    <?php foreach($rango_fechas as $anio => $meses) { ?>
        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
            <tr>
                <th style="text-align: center;"><?php echo $nombre_mes." - ".$anio; ?></th>
                <?php foreach($array_cat_prod_rip_volumen as $id_cat => $nombre_cat) { ?>
                    <td style="text-align: center;">
                        <?php 
                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                "id_cliente" => $client_info->id,
                                "id_proyecto" => $project_info->id,
                                "id_material" => 33, // Residuos industriales peligrosos
                                "id_categoria" => $id_cat,
                                "id_tipo_unidad" => 2, // Volumen
                                "flujo" => "Residuo",
                                "mes" => $numero_mes,
                                "anio" => $anio
                            ));
                            echo to_number_project_format($valor_categoria["valor_categoria"], $project_info->id);
                        ?>
                    </td>
                <?php } ?>
                <?php foreach($array_cat_prod_rli_volumen as $id_cat => $nombre_cat) { ?>
                    <td style="text-align: center;">
                        <?php 
                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                "id_cliente" => $client_info->id,
                                "id_proyecto" => $project_info->id,
                                "id_material" => 31, // Residuos Líquidos Industriales
                                "id_categoria" => $id_cat,
                                "id_tipo_unidad" => 2, // Volumen
                                "flujo" => "Residuo",
                                "mes" => $numero_mes,
                                "anio" => $anio
                            ));
                            echo to_number_project_format($valor_categoria["valor_categoria"], $project_info->id);
                        ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    <?php } ?>

</table>

<br pagebreak="true">

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_residuo_mensual_lhw; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="4" border="0">
    <tr>
        <td align="center"><img src="<?php echo $grafico_ind_residuo_produccion_lhw; ?>" style="height:380px; width:570px;" /></td>
    </tr>
</table>

<!-- FIN INDICADORES - RESIDUOS PELIGROSOS -->


</body>