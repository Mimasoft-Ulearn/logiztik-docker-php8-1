<div class="panel panel-default mb15">
    
    <div class="page-title clearfix">
        <h1><?php echo lang('summary'); ?></h1>
    </div>

    <div class="panel-body">

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
                    <div class="p10 pull-left"> <?php echo lang("compliance_1%_disability_and_TEA"). " " .$current_year; ?> </div>
                </div>
                <div class="panel-body">
                    <div id="grafico_resumen_discapacidad" style="height: 50vh"></div>
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
   

        // Resumen de Discapacidad basasdo en el ultimo año de registro                          
        <?php if(true){ ?>
            
            $('#grafico_resumen_discapacidad').highcharts({
                title: {
                    text: '',
                },
                xAxis: {
                    categories: <?php echo $names_lows; ?>
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
                }, 
                ],
                exporting:{
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    shared: true
                },
                series: <?php echo $disability_summary_data; ?>
            });
            
        <?php }else{?>
            $('#grafico_resumen_discapacidad').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
        <?php } ?>


    });
    
</script>