<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php //echo $info_proyecto->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo lang("activities")." - ".lang($client_area); ?>
</h2>
<div align="center">
	<?php
		$hoy = date('d-m-Y');
		$fecha = date(get_setting_client_mimasoft($info_cliente->id, "date_format"), strtotime($hoy));
		$hora = format_to_time_clients($info_cliente->id, get_current_utc_time("H:i:s"));
		
		echo lang("datetime_download") . ": " . $fecha.' '.lang("at").' '.$hora;
	?>
</div>

<br>
<?php if($puede_ver == 1) { ?>

	<?php if($client_area == "territory") { ?>

        <!-- Sección Beneficiarios por Macrozona -->
        <h2><?php echo lang('beneficiaries_by_macrozone'); ?></h2>

		<table style="padding-top:40px;">
        	<tr>
            	<td>
                    <table cellspacing="0" cellpadding="4" border="0">
                        <tr>
                            <td align="center"><img src="<?php echo $grafico_beneficiarios_por_macrozona; ?>" style="height:300px; width:450px;" /></td>
                        </tr>
                    </table>
            	</td>
                <td>
                    <table cellspacing="0" cellpadding="4" border="1">
                        <thead>
                            <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                                <th style="vertical-align:middle;"><?php echo lang("macrozone"); ?></th>
                                <th style="vertical-align:middle;"><?php echo lang("n_beneficiaries_organizations"); ?></th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($array_beneficiarios_organizaciones as $nombre_macrozona => $cantidad_beneficiarios){ ?>
                                <tr>
                                    <td><?php echo $nombre_macrozona; ?></td>
                                    <td><?php echo to_number_client_format($cantidad_beneficiarios, $info_cliente->id); ?></td>
                                    <td><?php echo ($total_beneficiarios == 0) ? to_number_client_format(0, $info_cliente->id) : to_number_client_format((($cantidad_beneficiarios * 100) / $total_beneficiarios), $info_cliente->id); ?> %</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
            	</td>
        	</tr>
        </table>
                   
         <table style="padding-top:30px;">
        	<tr>
            	<td> 
                    <table cellspacing="0" cellpadding="4" border="0">
                        <tr>
                            <td align="center"><img src="<?php echo $grafico_beneficiarios_asistentes; ?>" style="height:300px; width:450px;" /></td>
                        </tr>
                    </table>
				</td>
                <td>
                    <table cellspacing="0" cellpadding="4" border="1">
                        <thead>
                            <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                                <th style="vertical-align:middle;"><?php echo lang("macrozone"); ?></th>
                                <th style="vertical-align:middle;"><?php echo lang("n_beneficiaries_assistants"); ?></th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($array_beneficiarios_asistentes as $nombre_macrozona => $cantidad_participantes){ ?>
                                <tr>
                                    <td><?php echo $nombre_macrozona?></td>
                                    <td><?php echo to_number_client_format($cantidad_participantes, $info_cliente->id); ?></td>
                                    <td><?php echo ($total_participantes == 0) ? to_number_client_format(0, $info_cliente->id) : to_number_client_format((($cantidad_participantes * 100) / $total_participantes), $info_cliente->id); ?> %</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
				</td>
        	</tr>
        </table>
        <!-- Fin Sección Beneficiarios por Macrozona -->   
        
		 <br pagebreak="true">
         
         <!-- Sección Beneficiarios por Actividad -->
         <h2><?php echo lang('beneficiaries_by_activity'); ?></h2>
         
         <table cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td align="center"><img src="<?php echo $grafico_beneficiarios_actividad; ?>" style="height:300px; width:450px;" /></td>
            </tr>
        </table>
         <table cellspacing="0" cellpadding="4" border="1">
            <thead>
                <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    <th style="vertical-align:middle;"><?php echo lang("macrozone"); ?></th>
                    <th style="vertical-align:middle;"><?php echo lang("n_beneficiaries_assistants"); ?></th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($array_beneficiarios_tipo_actividad as $tipo_acuerdo => $cantidad_asistentes){ ?>
                    <tr>
                        <td><?php echo $tipo_acuerdo; ?></td>
                        <td><?php echo to_number_client_format($cantidad_asistentes, $info_cliente->id); ?></td>
                        <td><?php echo ($total_asistentes == 0) ? to_number_client_format(0, $info_cliente->id) : to_number_client_format((($cantidad_asistentes * 100) / $total_asistentes), $info_cliente->id); ?> %</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <!-- Fin Sección Beneficiarios por Actividad -->
        
        <br pagebreak="true">
        
        <!-- Sección Actividades por Macrozona-->
        
        <h2><?php echo lang('activities_by_macrozone'); ?></h2>
        
        <table cellspacing="0" cellpadding="4" border="0">
        
        <?php $loop = 1; ?>
        <?php foreach($graficos_actividades_macrozona as $grafico) { ?>
            
            <?php if($loop % 2 == 1){ ?>
                <tr>
            <?php } ?>
            
            <?php if(strpos($grafico, 'grafico_actividad_macrozona_') !== false){ ?>
            
                <?php 
                    $grafico_actividad_macrozona = str_split($grafico, 25);		
                    $id_actividad = $grafico_actividad_macrozona[1];
                    $actividad = $this->AC_Activities_model->get_one($id_actividad);
					$tipo_acuerdo = $this->AC_Feeders_types_agreements_model->get_one($actividad->id_feeder_tipo_acuerdo)->tipo_acuerdo;
                ?>
                <td align="center">
                    <div style="font-size:20px">&nbsp;</div>
                    <?php echo $tipo_acuerdo . " - " . lang("no_information_available"); ?>
                </td>			
                
            <?php } else { ?>
                <td align="center"><img src="<?php echo $grafico; ?>"/></td>
            <?php } ?>    
        
            <?php if($loop % 2 == 0 || $loop == count($graficos_actividades_macrozona)) {?>
                </tr>
            <?php } ?>
            
            <?php $loop++; ?>
        
        <? } ?>
        </table>
        <!--
        <br pagebreak="true">
        -->
        <div style="font-size:20px">&nbsp;</div>
        
        <?php foreach($tipos_acuerdo_actividades as $tipo_acuerdo) { ?>
        
            <table cellspacing="0" cellpadding="4" border="1">
                <thead>
                    <tr>
                        <th rowspan="2" style="text-align: center; background-color: <?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("macrozone"); ?></th>
                        <th colspan="2" style="text-align: center; background-color: <?php echo $info_cliente->color_sitio; ?>;"><?php echo $tipo_acuerdo["tipo_acuerdo"]; ?></th>                                            
                    </tr>
                    <tr>
                        <th style="text-align: center; background-color: <?php echo $info_cliente->color_sitio; ?>;">N°</th>
                        <th style="text-align: center; background-color: <?php echo $info_cliente->color_sitio; ?>;">%</th>                                             
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($array_macrozonas as $id_macrozona => $nombre_macrozona) { ?>         
                        <tr>
                           <td style="text-align: left;"><?php echo $nombre_macrozona; ?></td>
                           <?php $cantidad = $AC_Activities_dashboard->get_cantidad_macrozona_tipo_acuerdo($id_macrozona, $tipo_acuerdo["id_feeder_tipo_acuerdo"]); ?>
                           <?php $porcentaje = $AC_Activities_dashboard->get_porcentaje_macrozona_tipo_acuerdo($cantidad, $tipo_acuerdo["id_feeder_tipo_acuerdo"]); ?>
                           <td style="text-align: right;"><?php echo to_number_client_format($cantidad, $info_cliente->id); ?></td>
                           <td style="text-align: right;"><?php echo to_number_client_format($porcentaje, $info_cliente->id); ?> %</td>
                        </tr>
                    <?php } ?>
                </tbody>       
            </table>
            <div style="font-size:20px">&nbsp;</div>
        
        <?php } ?>
        
        <!-- Fin Sección Actividades por Macrozona -->

	<?php } ?> 
    
    
    <?php if($client_area == "distribution") { ?>
    	
    	<!-- Sección Beneficiarios por Comuna -->
        <h2><?php echo lang('beneficiaries_by_commune'); ?></h2>

		<table style="padding-top:40px;">
        	<tr>
            	<td>
                    <table cellspacing="0" cellpadding="4" border="0">
                        <tr>
                            <td align="center"><img src="<?php echo $grafico_beneficiarios_por_comuna; ?>" style="height:300px; width:450px;" /></td>
                        </tr>
                    </table>
            	</td>
                <td>
                    <table cellspacing="0" cellpadding="4" border="1">
                        <thead>
                            <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                                <th style="vertical-align:middle;"><?php echo lang("macrozone"); ?></th>
                                <th style="vertical-align:middle;"><?php echo lang("n_beneficiaries_organizations"); ?></th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($array_beneficiarios_comuna as $nombre_comuna => $cantidad_beneficiarios){ ?>
                                <tr>
                                    <td><?php echo $nombre_comuna; ?></td>
                                    <td><?php echo to_number_client_format($cantidad_beneficiarios["op"], $info_cliente->id); ?></td>
                                    <td><?php echo ($total_beneficiarios_comuna == 0) ? to_number_client_format(0, $info_cliente->id) : to_number_client_format((($cantidad_beneficiarios["op"] * 100) / $total_beneficiarios_comuna), $info_cliente->id); ?> %</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
            	</td>
        	</tr>
        </table>
                   
         <table style="padding-top:30px;">
        	<tr>
            	<td> 
                    <table cellspacing="0" cellpadding="4" border="0">
                        <tr>
                            <td align="center"><img src="<?php echo $grafico_participantes_por_comuna; ?>" style="height:300px; width:450px;" /></td>
                        </tr>
                    </table>
				</td>
                <td>
                    <table cellspacing="0" cellpadding="4" border="1">
                        <thead>
                            <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                                <th style="vertical-align:middle;"><?php echo lang("macrozone"); ?></th>
                                <th style="vertical-align:middle;"><?php echo lang("n_beneficiaries_assistants"); ?></th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($array_participantes_comuna as $nombre_comuna => $cantidad_participantes){ ?>
                                <tr>
                                    <td><?php echo $nombre_comuna; ?></td>
                                    <td><?php echo to_number_client_format($cantidad_participantes["op"], $info_cliente->id); ?></td>
                                    <td><?php echo ($total_participantes_comuna == 0) ? to_number_client_format(0, $info_cliente->id) : to_number_client_format((($cantidad_participantes["op"] * 100) / $total_participantes_comuna), $info_cliente->id); ?> %</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
				</td>
        	</tr>
        </table>
        <!-- Fin Sección Beneficiarios por Macrozona -->
        
        <br pagebreak="true">
        
        <!-- Sección Beneficiarios por Actividad -->
        <h2><?php echo lang('beneficiaries_by_activity'); ?></h2>
         
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td align="center"><img src="<?php echo $grafico_beneficiarios_tipo_actividad; ?>" style="height:300px; width:450px;" /></td>
            </tr>
        </table>
         <table cellspacing="0" cellpadding="4" border="1">
            <thead>
                <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    <th style="vertical-align:middle;"><?php echo lang("macrozone"); ?></th>
                    <th style="vertical-align:middle;"><?php echo lang("n_beneficiaries_assistants"); ?></th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($array_beneficiarios_actividad as $tipo_acuerdo => $cantidad_participantes){ ?>
                    <tr>
                        <td><?php echo $tipo_acuerdo; ?></td>
                        <td><?php echo to_number_client_format($cantidad_participantes, $info_cliente->id); ?></td>
                        <td><?php echo ($total_beneficiarios_actividad == 0) ? to_number_client_format(0, $info_cliente->id) : to_number_client_format((($cantidad_participantes * 100) / $total_beneficiarios_actividad), $info_cliente->id); ?> %</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <!-- Fin Sección Beneficiarios por Actividad -->
        
        <br pagebreak="true">
        
        <!-- Sección Actividades por Comuna -->
        <h2><?php echo lang('activities_by_commune'); ?></h2>
        
        <table cellspacing="0" cellpadding="4" border="0">
        
        <?php $loop = 1; ?>
        <?php foreach($graficos_actividades_comuna as $grafico) { ?>
            
            <?php if($loop % 2 == 1){ ?>
                <tr>
            <?php } ?>
            
            <?php if(strpos($grafico, 'grafico_actividad_comuna_') !== false){ ?>
            
                <?php 
                    $grafico_actividad_comuna = str_split($grafico, 25);		
                    $id_actividad = $grafico_actividad_comuna[1];
                    $actividad = $this->AC_Activities_model->get_one($id_actividad);
					$tipo_acuerdo = $this->AC_Feeders_types_agreements_model->get_one($actividad->id_feeder_tipo_acuerdo)->tipo_acuerdo;
                ?>
                <td align="center">
                    <div style="font-size:20px">&nbsp;</div>
                    <?php echo $tipo_acuerdo . " - " . lang("no_information_available"); ?>
                </td>			
                
            <?php } else { ?>
                <td align="center"><img src="<?php echo $grafico; ?>"/></td>
            <?php } ?>    
        
            <?php if($loop % 2 == 0 || $loop == count($grafico_actividad_macrozona)) {?>
                </tr>
            <?php } ?>
            
            <?php $loop++; ?>
        
        <? } ?>
        </table>
        <!--
        <br pagebreak="true">
        -->
        <div style="font-size:20px">&nbsp;</div>
        
        <?php foreach($actividades_comuna as $actividad_comuna) { ?>
        
            <table cellspacing="0" cellpadding="4" border="1">
                <thead>
                    <tr>
                        <th rowspan="2" style="text-align: center; background-color: <?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("macrozone"); ?></th>
                        <th colspan="2" style="text-align: center; background-color: <?php echo $info_cliente->color_sitio; ?>;"><?php echo $actividad_comuna["tipo_acuerdo"]; ?></th>                                            
                    </tr>
                    <tr>
                        <th style="text-align: center; background-color: <?php echo $info_cliente->color_sitio; ?>;">N°</th>
                        <th style="text-align: center; background-color: <?php echo $info_cliente->color_sitio; ?>;">%</th>                                             
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($comunas as $id_comuna => $nombre_comuna) { ?>         
                        <tr>
                           <td style="text-align: left;"><?php echo $nombre_comuna; ?></td>
                           <?php $cantidad = $AC_Activities_dashboard->get_cantidad_actividades_comuna($id_comuna, $actividad_comuna["id_feeder_tipo_acuerdo"]); ?>
                           <?php $porcentaje = $AC_Activities_dashboard->get_porcentaje_actividades_comuna($cantidad, $actividad_comuna["id_feeder_tipo_acuerdo"]); ?>
                           <td style="text-align: right;"><?php echo to_number_client_format($cantidad, $info_cliente->id); ?></td>
                           <td style="text-align: right;"><?php echo to_number_client_format($porcentaje, $info_cliente->id); ?> %</td>
                        </tr>
                    <?php } ?>
                </tbody>       
            </table>
            <div style="font-size:20px">&nbsp;</div>
        
        <?php } ?>
        <!-- Fin Sección Actividades por Comuna -->
        
    <?php } ?> 
    
    <br pagebreak="true">
    
    <!-- Sección Beneficiarios Nuevos (para territorio y distribución) -->
    <h2><?php echo lang('new_beneficiaries'); ?></h2>
               
     <table style="padding-top:30px;">
        <tr>
            <td> 
                <table cellspacing="0" cellpadding="4" border="0">
                    <tr>
                        <td align="center"><img src="<?php echo $grafico_tipo_beneficiario; ?>" style="height:300px; width:450px;" /></td>
                    </tr>
                </table>
            </td>
            <td>
                <table cellspacing="0" cellpadding="4" border="1">
                    <thead>
                        <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                            <th style="vertical-align:middle; text-align: center;"><?php echo lang("type_of_beneficiarie"); ?></th>
                            <th style="vertical-align:middle; text-align: center;">N°</th>
                            <th style="text-align: center;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="text-align: left;"><?php echo lang("total"); ?></td>
                        <td style="text-align: right;"><?php echo to_number_client_format($beneficiarios_total, $info_cliente->id); ?></td>
                        <td style="text-align: right;"><?php echo to_number_client_format(100, $info_cliente->id); ?> %</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;"><?php echo lang("new_plural"); ?></td>
                        <td style="text-align: right;"><?php echo to_number_client_format($beneficiarios_nuevos, $info_cliente->id); ?></td>
                        <td style="text-align: right;"><?php echo to_number_client_format($porc_beneficiarios_nuevos, $info_cliente->id); ?> %</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;"><?php echo lang("olds"); ?></td>
                        <td style="text-align: right;"><?php echo to_number_client_format($beneficiarios_antiguos, $info_cliente->id); ?></td>
                        <td style="text-align: right;"><?php echo to_number_client_format($porc_beneficiarios_antiguos, $info_cliente->id); ?> %</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <!-- Fin Sección Beneficiarios Nuevos -->
    
    
    
<?php } else { ?>
    
	<?php echo lang('content_disabled'); ?>

<?php } ?>
</body>