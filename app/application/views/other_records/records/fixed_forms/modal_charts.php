<div class="modal-body clearfix" >

    <div class="panel panel-default">
        <!-- <div class="page-title clearfix">
            
            </div> -->
        <?php  //$width = count($estaciones) == 1 ? "100%" : "50%"; ?>
        <div class="col-md-12 text-center mt5" style="width: 100%<?php //echo $width; ?>;">
        <?php foreach($estaciones as  $idx => $estacion){ ?>
            <div id="grafico_<?php echo $idx; ?>"></div>
        <?php } ?>
        </div>

    </div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<style>
    #ajaxModal > .modal-dialog {
    width: 80% !important;
}
</style>

<script type="text/javascript">
    $(document).ready(function () {
        // $(document).on('click', '#modal_charts', function() {
        
        // $('#content').find('#modal_charts').on('click',function() {
        // $('#ajaxModal').on('show.bs.modal',function() {
        //     console.log("1");
        // });
        // $('#ajaxModal').on('shown.bs.modal',function() {
        //     console.log("2");
        // });

        var decimals_separator = AppHelper.settings.decimalSeparator;
		var thousands_separator = AppHelper.settings.thousandSeparator;
		var decimal_numbers = AppHelper.settings.decimalNumbers;	
        // Valor para darle un nombre unico a las lineas de referencia del gráfico
        <?php $ref_line_number = 0; ?>
        <?php foreach($estaciones as $idx => $estacion){ ?>
            
            setTimeout(function(){ 
                var chart = $("#grafico_<?php echo $idx; ?>").highcharts({
                    
                    chart: {
                        zoomType: 'xy'
                    },
                    title: {
                        useHTML: true,
                        text: '<?php echo lang('station').' '.$estacion . '. <br> <i style="color:#aaa">(' . lang('series_modal_chart').')'; ?>'
                    },
                    credits: {
                        enabled: false
                    },
                    exporting: {
                        enabled: true
                    },
                    xAxis: [{
                        categories: <?php echo json_encode($array_categories);?>,
                        crosshair: true
                    }],
                    yAxis: {
                        min: 0,
                        endOnTick: false,
                        labels: {
                            format: '{value} <?php echo $unidad_eje_y; ?>',
                            style: {
                                color: Highcharts.getOptions().colors[1]
                            }
                        },
                        title: {
                            text: '<?php echo $nombre_eje_y; ?>',
                            style: {
                                color: Highcharts.getOptions().colors[1]
                            }
                        }
                    },
                    tooltip: {
                        valueSuffix: ' <?php echo $unidad_eje_y; ?>',
                        shared: false,
                        headerFormat: "<small>{point.key}</small><table>",
                        pointFormatter: function(){
                            var valueSuffix = this.series.tooltipOptions.valueSuffix || "";
                            return '<tr><td style="color:'+this.series.color+';padding:0">● <span style="color:#333333;">'+this.series.name+': </span> </td>'+'<td style="padding:0; font-weight:bold;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+' '+valueSuffix+'</b></td></tr>';
                        },
                        footerFormat:"</table>",
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            // stacking: 'normal'
                        }
                    },
                    legend: {
                        backgroundColor:
                            Highcharts.defaultOptions.legend.backgroundColor || // theme
                            'rgba(255,255,255,0.25)'
                    },
                    <?php 
                    $series = '[';
                    
                    $station_level_data = $datos_graficos[$estacion]; 

                    // Creación dinamica del json que define las series del gráfico
                    foreach($station_level_data as $parameter_level_data){    
                        $series .= '{';
                            $series .= '"name": "'.$parameter_level_data['name'].'",';
                            $series .= '"data": '. json_encode($parameter_level_data['data']) .',';
                            $series .= '"visible": '. json_encode($parameter_level_data['visible']) .',';
                            
                            $reference_value = $parameter_level_data['reference_values'];
                            
                            // Creación de eventos para mostrar los valores de referencia en los gráficos, si el valor es por rango se crea un plotBand, si no se crea un plotLine.
                            if($reference_value['reference_type'] == 'single'){
    
                                // Cálculo del valor maximo del eje Y
                                $y_max_value = max(max($parameter_level_data['data']), $reference_value['value']);

                                $series .=  "events: {
                                    show: function() {
                                        // console.log(this.yAxis);
                                        this.yAxis.addPlotLine({
                                            value:". $reference_value['value'] .",
                                            color: 'red',
                                            width: 2,
                                            id: 'plot-line-".$ref_line_number."',
                                            label: {
                                                text: '". $reference_value['label'] ."',
                                                style: {
                                                    color: '#000000'
                                                }
                                            }
                                        });
                                        // Actualización del valor limite en el eje Y
                                        this.yAxis.update({
                                            max:". $y_max_value ."
                                        });
                                    },
                                    hide: function() {
                                      this.yAxis.removePlotLine('plot-line-".$ref_line_number."');
                                    }
                                }";
                            } elseif($reference_value['reference_type'] == 'by_range'){

                                // Cálculo del valor maximo del eje Y
                                $y_max_value = max(max($parameter_level_data['data']), $reference_value['value_max']);

                                $series .=  "events: {
                                    show: function() {
                                        // console.log(this.yAxis);
                                        this.yAxis.addPlotBand({
                                            from:". $reference_value['value_min'] .",
                                            to:". $reference_value['value_max'] .",
                                            color: 'red',
                                            width: 2,
                                            id: 'plot-line-".$ref_line_number."',
                                            label: {
                                                text: '". $reference_value['label'] ."',
                                                x: 30,
                                                style: {
                                                    color: '#ffffff',
                                                    fontWeight: 'bolder'
                                                }
                                            }
                                      });
                                      // Actualización del valor limite en el eje Y
                                      this.yAxis.update({
                                          max:". $y_max_value ."
                                      });
                                    },
                                    hide: function() {
                                      this.yAxis.removePlotBand('plot-line-".$ref_line_number."');
                                    }
                                }";
                                
                            } elseif($reference_value['reference_type'] == 'none'){
                                
                                // Cálculo del valor maximo del eje Y
                                $y_max_value = max($parameter_level_data['data']);

                                $series .=  "events: {
                                    show: function() {
                                        
                                        // Actualización del valor limite en el eje Y
                                        this.yAxis.update({
                                            max:". $y_max_value ."
                                        });
                                    },
                                    hide: function() {
                                      
                                    }
                                }";
                            }
                        $series .= '},';
                        
                        $ref_line_number++;
                    }
                    $series .= ']';
                    ?>
                    series: <?php echo $series; ?>,
                    responsive: true
                });
            }, 400);
            
        <?php } ?>

        
       

    });   
</script>