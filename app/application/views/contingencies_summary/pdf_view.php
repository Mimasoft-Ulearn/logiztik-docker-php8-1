<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $info_proyecto->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo lang("compromises")." - ".lang("contingencies"); ?>
</h2>
<div align="center">
    <?php $hora = convert_to_general_settings_time_format($info_proyecto->id, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $info_proyecto->id));  ?>
	<?php echo lang("datetime_download") . ": " . get_date_format(date('Y-m-d'), $info_proyecto->id).' '.lang("at").' '.$hora; ?>
</div>

<?php if($puede_ver == 1) { ?>

 <!-- Sección Tipos de evento -->
 <h2><?php echo lang('event_types'); ?></h2>
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td align="center"><img src="<?php echo $grafico_totales_tipo_evento; ?>" style="height:300px; width:450px;" /></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="4" border="1">
            <thead>
                <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    <th style="text-align: center;"><?php echo lang("event_categories"); ?></th>
                    <th style="text-align: center;">N°</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($array_cant_tipo_evento as $nombre => $cant_tipo_evento) { ?>
                <tr>
                    <td style="text-align: left;"><?php echo lang($nombre); ?></td>
                    <td style="text-align: right;"><?php echo to_number_project_format($cant_tipo_evento['cant'], $info_proyecto->id); ?></td>
                </tr> 
            <?php } ?> 
            </tbody>
        </table>
        
<!-- Fin Sección Tipos de evento -->

<br pagebreak="true">

<!-- Sección Eventos por responsable -->
<h2><?php echo lang('event_by_responsible'); ?></h2>
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td align="center"><img src="<?php echo $grafico_eventos_por_responsable; ?>" style="height:300px; width:450px;" /></td>
            </tr>
        </table>
<!-- Fin Sección Eventos por responsable -->

<!-- Sección Eventos por tipo afectación -->
<h2><?php echo lang('event_by_affectation_type'); ?></h2>
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td align="center"><img src="<?php echo $grafico_eventos_por_tipo_afectacion; ?>" style="height:300px; width:450px;" /></td>
            </tr>
        </table>
<!-- Fin Sección Eventos por tipo afectación -->


<?php } else { ?>
    
	<?php echo lang('content_disabled'); ?>

<?php } ?>
</body>