<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $info_proyecto->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo lang("compromises")." - ".lang("compromises_compliance"); ?>
</h2>
<div align="center">
    <?php $hora = convert_to_general_settings_time_format($info_proyecto->id, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $info_proyecto->id));  ?>
	<?php echo lang("datetime_download") . ": " . get_date_format(date('Y-m-d'), $info_proyecto->id).' '.lang("at").' '.$hora; ?>
</div>

<br>
<?php if($puede_ver == 1) { ?>

    <?php if($id_compromiso_reportables){ ?>

        <!-- Sección Compromisos Reportables -->
        <h2><?php echo lang('reportable_compromises'); ?></h2>
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td align="center"><img src="<?php echo $grafico_resumen_cumplimiento; ?>" style="height:300px; width:450px;" /></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="4" border="1">
            <thead>
                <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    <th rowspan="2" style="text-align: center;"><?php echo lang("general_compliance_status"); ?></th>
                    <th colspan="2" style="text-align: center;"><?php echo lang("sub_total"); ?></th>
                </tr>
                <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    <th style="text-align: center;">N°</th>
                    <th style="text-align: center;">%</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($compromisos_reportables as $cr) { ?>
                <?php
                    if($total_reportables == 0){
                        $porcentaje = 0;
                    } else {
                        $porcentaje = ($cr["cant"] * 100) / ($total_reportables);
                    }
                ?>
                <tr>
                    <td style="text-align: left;"><?php echo $cr["nombre_estado"]; ?></td>
                    <td style="text-align: right;"><?php echo to_number_project_format($cr["cant"], $info_proyecto->id); ?></td>
                    <td style="text-align: right;"><?php echo to_number_project_format($porcentaje, $info_proyecto->id); ?> %</td>
                </tr>
            <?php } ?>    
            </tbody>
        </table>
        
        <!-- Fin Sección Compromisos Reportables -->

        <br pagebreak="true">

        <!-- Sección Resumen por IGA -->
        <h2><?php echo lang('summary_by_iga'); ?></h2>
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td align="center"><img src="<?php echo $grafico_resumen_por_iga; ?>" style="height:300px; width:450px;" /></td>
            </tr>
        </table>
        <!-- Fin Sección Resumen por IGA -->

        <!-- Sección Resumen por Tipo de Cumplimiento-->
        <h2><?php echo lang('summary_by_compliance_type'); ?></h2>
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td align="center"><img src="<?php echo $grafico_resumen_por_tipo_cumplimiento; ?>" style="height:300px; width:450px;" /></td>
            </tr>
        </table>
        <!-- Fin Sección Resumen por Tipo de Cumplimiento-->
        
        <br pagebreak="true">

        <!-- Sección Resumen por Tema Ambiental-->
        <h2><?php echo lang('summary_by_environmental_topic'); ?></h2>
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td align="center"><img src="<?php echo $grafico_resumen_por_tema_ambiental; ?>" style="height:300px; width:450px;" /></td>
            </tr>
        </table>
      
        <!-- Fin Sección Resumen por Tema Ambiental-->

        <!-- Sección Resumen por Área Responsable-->
        <h2><?php echo lang('summary_by_responsible_area'); ?></h2>
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td align="center"><img src="<?php echo $grafico_resumen_por_area_responsable; ?>" style="height:300px; width:450px;" /></td>
            </tr>
        </table>
        <!-- Fin Sección Resumen por Tema Ambiental-->
        
        <!--<br pagebreak="true">-->

        <!-- Sección Estados de Cumplimiento -->
        <!--<h2><?php echo lang('compliance_status'); ?></h2>
        <table cellspacing="0" cellpadding="4" border="1">
            <thead>
                <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    <th style="text-align: center;"><?php echo lang("n_activity"); ?></th>
                    <th style="text-align: center;"><?php echo lang("environmental_management_instrument"); ?></th>
                    <th style="text-align: center;"><?php echo lang("compliance_type"); ?></th>
                    <th style="text-align: center;"><?php echo lang("environmental_topic"); ?></th>
                    <th style="text-align: center;"><?php echo lang("impact_on_the_environment_due_to_non_compliance"); ?></th>
                    <th style="text-align: center;"><?php echo lang("commitment_description"); ?></th>
                    <th style="text-align: center;"><?php echo lang("responsible_area"); ?></th>
                    <th style="text-align: center;"><?php echo lang("status"); ?></th>
                    <th style="text-align: center;"><?php echo lang("observations"); ?></th>
                </tr>
            </thead>
            
            <tbody>
                <?php foreach($result_reportables as $res){ ?>
                <tr>
                    <td style="text-align: center;"><?php echo $res["n_activity"]; ?></td>
                    <td style="text-align: center;"><?php echo $res["environmental_management_instrument"]; ?></td>
                    <td style="text-align: center;"><?php echo $res["compliance_type"]; ?></td>
                    <td style="text-align: center;"><?php echo $res["environmental_topic"]; ?></td>
                    <td style="text-align: center;"><?php echo $res["impact_on_the_environment_due_to_non_compliance"]; ?></td>
                    <td style="text-align: center;"><?php echo $res["commitment_description"]; ?></td>
                    <td style="text-align: center;"><?php echo $res["area_responsible"]; ?></td>
                    <td style="text-align: center;">
                        <?php
                            $estado = $res["html_estado"];
                            if($estado != "-"){
                                $array_estado = explode("nombre_estado:", $estado);
                                $pdf->SetFont($fontawesome, '', 9, '', false);
                                echo '<span style="font-family:'.$fontawesome.'">'.$array_estado[0].'</span><br>';
                                $pdf->SetFont('helvetica', '', 9);
                                echo $array_estado[1];
                            } else {
                                echo $estado;
                            }
                        
                        ?>
                    </td>
                    <td style="text-align: center;"><?php echo $res["observaciones"]; ?></td>
                    
                   
                </tr>
                <?php } ?>
            </tbody>
        </table>-->
        
        <!-- Fin Sección Estados de Cumplimiento -->

    <?php } else { ?>
    
        <?php echo lang('the_project').' "'.$nombre_proyecto.'" '.lang('compromise_matrix_not_enabled'); ?>
    
    <?php } ?>

<?php } else { ?>
    
	<?php echo lang('content_disabled'); ?>

<?php } ?>
</body>