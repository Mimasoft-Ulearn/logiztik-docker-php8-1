<div class="panel panel-default mb15">
    
    <div class="page-title clearfix">
        <h1><?php echo lang('gender'); ?></h1>
    </div>

    <div class="panel-body">

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
                    <div class="p10 pull-left"> <?php echo lang("women_per_staffing"); ?> </div>
                </div>
                <div class="panel-body">
                    <div id="grafico_mujeres_por_dotacion" style="height: 50vh;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
                    <div class="p10 pull-left"> <?php echo lang("women_by_position"); ?> </div>
                </div>
                <div class="panel-body">
                    <div id="grafico_mujeres_por_cargo" style="height: 50vh"></div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
                    <div class="p10 pull-left"> <?php echo lang("women_by_area"); ?> </div>
                </div>
                <div class="panel-body">
                    <div id="grafico_mujeres_por_area" style="height: 50vh"></div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="page-title clearfix" style="color: #FFFFFF; background-color: #34a7d6;">
                    <div class="p10 pull-left"> <?php echo lang("women_by_branch"); ?> </div>
                </div>
                <div class="panel-body">
                    <div id="grafico_mujeres_por_sucursal" style="height: 50vh"></div>
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
            
            $('#grafico_mujeres_por_dotacion').highcharts({
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
                series: <?php echo $women_per_staffing_data; ?>
            });
            
        <?php }else{?>
            $('#grafico_mujeres_por_dotacion').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
        <?php } ?>

        
        <?php if(true){ ?>
            
            $('#grafico_mujeres_por_cargo').highcharts({
                
                title: {
                    text: ''
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                        text: ''
                    },
                    labels:{
                        formatter: function(){
                            return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator)+'%';
                        }
                    }
                },
                xAxis: {
                    categories: <?php echo $years; ?>
                },
                exporting:{
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    pointFormat: '{series.name}: {point.y}%'
                },
                series: <?php echo $women_by_personnel_area; ?>,

            });
            
        <?php }else{?>
            $('#grafico_mujeres_por_cargo').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
        <?php } ?>

                
        <?php if(true){ ?>
            
            $('#grafico_mujeres_por_area').highcharts({
                
                title: {
                    text: ''
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                        text: ''
                    },
                    labels:{
                        formatter: function(){
                            return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator)+'%';
                        }
                    }
                },
                xAxis: {
                    categories: <?php echo $cargos; ?>
                },
                exporting:{
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    pointFormat: '{series.name}: {point.y}%'
                },
                series: <?php echo $women_by_area_data; ?>
            });
            
        <?php }else{?>
            $('#grafico_mujeres_por_area').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
        <?php } ?>

                
        <?php if(true){ ?>
            
            $('#grafico_mujeres_por_sucursal').highcharts({
                
                title: {
                    text: ''
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                        text: ''
                    },
                    labels:{
                        formatter: function(){
                            return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator)+'%';
                        }
                    }
                },
                xAxis: {
                    categories: <?php echo $sucursales; ?>
                },
                exporting:{
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    pointFormat: '{series.name}: {point.y}%'
                },
                series: <?php echo $women_by_branch_data; ?>
            });
            
        <?php }else{?>
            $('#grafico_mujeres_por_sucursal').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
        <?php } ?>

    });
    
</script>