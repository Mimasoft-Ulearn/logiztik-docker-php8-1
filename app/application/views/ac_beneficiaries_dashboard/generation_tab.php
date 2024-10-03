<div class="panel panel-default mb15">
    
    <div class="page-title clearfix">
        <h1><?php echo lang('generations'); ?></h1>
    </div>

    <div class="panel-body">

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
                    <div class="p10 pull-left"> <?php echo lang("generations"); ?> </div>
                </div>
                <div class="panel-body">
                    <div id="grafico_generaciones" style="height: 50vh"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
                    <div class="p10 pull-left"> <?php echo lang("possible_retirements"); ?> </div>
                </div>
                <div class="panel-body">
                    <div id="grafico_posibles_jubilaciones" style="height: 50vh;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
                    <div class="p10 pull-left"> <?php echo lang("hired_over_45_years"); ?> </div>
                </div>
                <div class="panel-body">
                    <div id="grafico_mayores_45" style="height: 50vh"></div>
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
        
        <?php if(true){ ?>
            
            $('#grafico_generaciones').highcharts({
                chart: {
                    type: 'column'
                },
                title: {
                    text: '',
                },
                xAxis: {
                    categories: <?php echo $years; ?>
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: ''
                    },
                    labels:{
                        formatter: function(){
                            return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator)+'%';
                        }
                    },
                    stackLabels: {
                        enabled: true
                    }
                },
                exporting:{
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                },
                plotOptions: {
                    column: {
                        stacking: 'percent',
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                series: <?php echo $generation_data; ?>
            });
            
        <?php }else{?>
            $('#grafico_mujeres_por_dotacion').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
        <?php } ?>


        
        <?php if(true){ ?>
            
            $('#grafico_posibles_jubilaciones').highcharts({
                title: {
                    text: '',
                },
                xAxis: {
                    categories: <?php echo $years; ?>
                },
                yAxis: [{ // Eje izquierda
                    title: {
                        text: 'N° personas'
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
                        text: 'Porcentaje'
                    },
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
                series: <?php echo $posible_retirement_data; ?>
            });
            
        <?php }else{?>
            $('#grafico_posibles_jubilaciones').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
        <?php } ?>


        
        <?php if(true){ ?>
            
            $('#grafico_mayores_45').highcharts({
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
                series: <?php echo $hired_over_45_data; ?>
            });
            
        <?php }else{?>
            $('#grafico_mayores_45').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
        <?php } ?>


    });
    
</script>