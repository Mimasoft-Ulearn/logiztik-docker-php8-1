<div class="panel panel-default mb15">
    
    <div class="page-title clearfix">
        <h1><?php echo lang('disability'); ?></h1>
    </div>

    <div class="panel-body">

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
                    <div class="p10 pull-left"> <?php echo lang("disability_by_branch"); ?> </div>
                </div>
                <div class="panel-body">
                    <div id="grafico_discapacidad_por_sucursal" style="height: 50vh"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
                    <div class="p10 pull-left"> <?php echo lang("law_1%_ck_y_ksa"); ?> </div>
                </div>
                <div class="panel-body">
                    <div id="grafico_ley_ck_y_ksa" style="height: 50vh;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
                    <div class="p10 pull-left"> <?php echo lang("law_1%_commercial_andes_motor"); ?> </div>
                </div>
                <div class="panel-body">
                    <div id="grafico_ley_comercial_andes_motor" style="height: 50vh"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
                    <div class="p10 pull-left"> <?php echo lang("law_1%_andes_motor"); ?> </div>
                </div>
                <div class="panel-body">
                    <div id="grafico_ley_andes_motor" style="height: 50vh"></div>
                </div>
            </div>
        </div>   

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
                    <div class="p10 pull-left"> <?php echo lang("TEA_law"); ?> </div>
                </div>
                <div class="panel-body">
                    <div id="grafico_ley_tea" style="height: 50vh"></div>
                </div>
            </div>
        </div>           
    
    </div> 
</div>

<script type="text/javascript">
  
    $(document).ready(function(){

        //General Settings
        var decimals_separator = AppHelper.settings.decimalSeparator;
        var thousands_separator = AppHelper.settings.thousandSeparator;
        var decimal_numbers = AppHelper.settings.decimalNumbers;	
   

        
        //DISCAPACIDAD POR SUCURSAL
        <?php if(true){ ?>
            
            $('#grafico_discapacidad_por_sucursal').highcharts({
                chart: {
			        type: 'column'
                },
                title: {
                    text: '',
                },
                xAxis: {
			        type: 'category',
                    labels: {
                        y: 50,
                    },
                    min: 0
                },
                yAxis: {
                    title: {
                        text: ''
                    },
                    
                    min: 0,
                    stackLabels: {
                        enabled: true,
                        verticalAlign: 'bottom',
                        crop: false,
                        overflow: 'none',
                        y: 20,
                        rotation: -90,
                        formatter: function() {
                            return this.stack; // año que aparece bajo la columna
                        },
                        style: {
                            fontSize: '9px'
                        }
                    },
                },
                exporting:{
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    shared: true
                },
                plotOptions: {
                    column: {
     			        stacking: 'normal',
                        pointPadding: 0.2,
                        borderWidth: 0,
                        // minPointLength: 12, //altura minima para las columnas, incluye los valores 0
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            align: 'center',
                            //format: '{point.y:.0f}', // one decimal
                            formatter: function(){
                                return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
                            },
                            //y: -2, // 10 pixels down from the top
                            style: {
                                fontSize: '10px',
                                fontFamily: 'Segoe ui, sans-serif'
                            }
                        }
                    }
                },
                series: <?php echo $disability_by_branch['data']; ?>,
                drilldown:{
			        allowPointDrilldown: true,
			        series: <?php echo $disability_by_branch['drilldown']; ?>
                }
            });
            
        <?php }else{?>
            $('#grafico_discapacidad_por_sucursal').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
        <?php } ?>


                
        // 1% LEY CK Y KSA
        <?php if(true){ ?>

            $('#grafico_ley_ck_y_ksa').highcharts({
                title: {
                    text: '',
                },
                xAxis: {
                    categories: <?php echo $years; ?>
                },
                yAxis: [{ // Eje izquierda
                    title: {
                        text: 'Actual'
                    },
                    labels: {
                        format: '{value}',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    allowDecimals: false,
                }, { // Eje derecha
                    title: {
                        text: '% objetivo'
                    },
                    min: 0,
                    max: 100,
                    labels: {
                        format: '{value} %',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    opposite: true
                }],
                exporting:{
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    shared: true
                },
                series: <?php echo $ley_CK_KSA_data; ?>
            });
            
        <?php }else{?>
            $('#grafico_ley_ck_y_ksa').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
        <?php } ?>


        // 1% LEY COMERCIAL ANDES MOTOR       
        <?php if(true){ ?>
            
            $('#grafico_ley_comercial_andes_motor').highcharts({
                title: {
                    text: '',
                },
                xAxis: {
                    categories: <?php echo $years; ?>
                },
                yAxis: [{ // Eje izquierda
                    title: {
                        text: 'Actual'
                    },
                    labels: {
                        format: '{value}',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    allowDecimals: false,
                }, { // Eje derecha
                    title: {
                        text: '% objetivo'
                    },
                    min: 0,
                    max: 100,
                    labels: {
                        format: '{value} %',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    opposite: true
                }],
                exporting:{
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    shared: true
                },
                series: <?php echo $ley_comercial_andes_motor_data; ?>
            });
            
        <?php }else{?>
            $('#grafico_ley_comercial_andes_motor').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
        <?php } ?>


        // 1% LEY ANDES MOTOR              
        <?php if(true){ ?>
            
            $('#grafico_ley_andes_motor').highcharts({
                title: {
                    text: '',
                },
                xAxis: {
                    categories: <?php echo $years; ?>
                },
                yAxis: [{ // Eje izquierda
                    title: {
                        text: 'Actual'
                    },
                    labels: {
                        format: '{value}',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    allowDecimals: false,
                }, { // Eje derecha
                    title: {
                        text: '% objetivo'
                    },
                    min: 0,
                    max: 100,
                    labels: {
                        format: '{value} %',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    opposite: true
                }],
                exporting:{
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    shared: true
                },
                series: <?php echo $ley_andes_motor_data; ?>
            });
            
        <?php }else{?>
            $('#grafico_ley_andes_motor').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
        <?php } ?>


        // LEY TEA                            
        <?php if(true){ ?>
            
            $('#grafico_ley_tea').highcharts({
                title: {
                    text: '',
                },
                xAxis: {
                    categories: <?php echo $years; ?>
                },
                yAxis: [{ // Eje izquierda
                    title: {
                        text: 'Actual'
                    },
                    labels: {
                        format: '{value}',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    allowDecimals: false,
                }, { // Eje derecha
                    title: {
                        text: '% objetivo'
                    },
                    min: 0,
                    max: 100,
                    labels: {
                        format: '{value} %',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    opposite: true
                }],
                exporting:{
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    shared: true
                },
                series: <?php echo $ley_tea_data; ?>
            });
            
        <?php }else{?>
            $('#grafico_ley_tea').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
        <?php } ?>


    });
    
</script>