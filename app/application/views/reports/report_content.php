<div id="contenido">

    <!--ANTECEDENTES DEL PROYECTO-->
    <?php if($report_config->project_data){ ?>
        <?php if ($puede_ver_antecedentes_proyecto) { ?>
            <div class="panel panel-default">
                <div class="page-title clearfix">
                    <h1><?php echo lang("project_background"); ?></h1>
                </div>
                <div class="panel-body" style="padding-bottom:0px;">
                    <div class="form-group">
                        <div class="col-md-6" style="padding-left:0px">	
                            <table class="table table-bordered">
                                <tr>
                                    <th style="background-color:<?php echo $color_sitio; ?>;"><?php echo lang("enterprise"); ?></th>
                                    <td><?php echo $client_info->company_name; ?></td>
                                </tr>
                                <tr>
                                    <th style="background-color:<?php echo $color_sitio; ?>;"><?php echo lang("production_site"); ?></th>
                                    <td><?php echo $nombre_proyecto; ?></td>
                                </tr>
                                <tr>
                                    <th style="background-color:<?php echo $color_sitio; ?>;"><?php echo lang("location"); ?></th>
                                    <td><?php echo $ubicacion_proyecto; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6" style="padding-right:0px;">
                            <table class="table table-bordered">
                
                                <tr>
                                    <th style="background-color:<?php echo $color_sitio; ?>;"><?php echo lang("rut"); ?></th>
                                    <td><?php echo $rut; ?></td>
                                </tr>
                                <tr>
                                    <th style="background-color:<?php echo $color_sitio; ?>;"><?php echo lang("report_start_date"); ?></th>
                                    <td><?php echo get_date_format($inicio_consulta, $id_proyecto); ?></td>
                                </tr>
                                <tr>
                                    <th style="background-color:<?php echo $color_sitio; ?>;"><?php echo lang("report_end_date"); ?></th>
                                    <td><?php echo get_date_format($termino_consulta, $id_proyecto); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
    <!-- FIN ANTECEDENTES DEL PROYECTO -->



    <!-- COMPROMISOS AMBIENTALES - RCA -->
    <?php if($puede_ver_compromisos_rca && $id_compromiso_rca) { ?>
        <div class="panel panel-default">
            <div class="page-title clearfix">
                <h1><?php echo lang("environmental_commitments").' - '.$environmental_authorization; ?></h1>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">
                        <table id="tabla_resumen_por_evaluado" class="table table-striped">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center" style="vertical-align:middle;"><?php echo lang("compliance_status"); ?></th>
                                    <?php foreach($evaluados_rca as $evaluado) { ?>
                                        <th colspan="2" class="text-center"><?php echo $evaluado->nombre_evaluado; ?></th>
                                    <?php } ?>
                                    </tr>
                                <tr>
                                    <?php foreach($evaluados_rca as $evaluado) { ?>
                                        <th class="text-center">N°</th>
                                        <th class="text-center">%</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="text-left"><?php echo lang("total_applicable_compromises"); ?></th>
                                    <?php foreach($evaluados_rca as $evaluado) { ?>
                                        <td class=" text-right"><?php echo to_number_project_format(array_sum($array_total_por_evaluado_rca[$evaluado->id]), $id_proyecto); ?></td>
                                        <td class=" text-right"><?php echo to_number_project_format(100, $id_proyecto); ?>%</td>
                                    <?php } ?>
                                </tr>
                                <?php foreach($array_estados_evaluados as $estado_evaluado) { ?>
                                    <tr>
                                        <td class="text-left"><?php echo $estado_evaluado["nombre_estado"]; ?></td>
                                        <?php foreach($estado_evaluado["evaluados"] as $id_evaluado => $evaluado) { ?>
                                            <?php
                                                $total_evaluado = array_sum($array_total_por_evaluado_rca[$id_evaluado]);
                                                if($total_evaluado == 0){
                                                    $porcentaje = 0;
                                                } else {
                                                    $porcentaje = ($evaluado["cant"] * 100) / ($total_evaluado); 
                                                }
                                            ?>
                                            <td class="text-right"><?php echo to_number_project_format($evaluado["cant"], $id_proyecto); ?></td>
                                            <td class="text-right"><?php echo to_number_project_format($porcentaje, $id_proyecto); ?>%</td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- LISTA DE COMPROMISOS CON EVALUACIONES NO CUMPLE -->
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="col-md-6">
                    <div class="grafico_torta" id="grafico_cumplimientos_totales" style="height: 240px;"></div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive">
                        <div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">
                            <table id="tabla_evaluaciones_rca_no_cumple" class="table table-striped">
                                <thead>
                                    <tr>
                                    <th class="text-center"><?php echo lang("compromise"); ?></th>
                                    <th class="text-center"><?php echo lang("critical_level"); ?></th>
                                    <th class="text-center"><?php echo lang("responsible"); ?></th>
                                    <th class="text-center"><?php echo lang("closing_term"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($array_compromisos_evaluaciones_no_cumple as $row) { ?>
                                    <tr>
                                        <td class="text-left"><?php echo $row->nombre_compromiso; ?></td>
                                        <td class="text-left"><?php echo $row->criticidad; ?></td>
                                        <td class="text-left"><?php echo $row->responsable_reporte; ?></td>
                                        <td class="text-left"><?php echo get_date_format($row->plazo_cierre, $id_proyecto); ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- FIN COMPROMISOS AMBIENTALES - RCA -->




    <!-- COMPROMISOS AMBIENTALES - REPORTABLES -->
    <?php if($puede_ver_compromisos_reportables && $id_compromiso_reportables) { ?>
        <div class="panel panel-default">

            <div class="page-title clearfix">
                <h1><?php echo lang("environmental_reportable_commitments").' - '.$environmental_authorization; ?></h1>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">
                        <table id="tabla_resumen_por_estado" class="table table-striped">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center" style="vertical-align:middle;"><?php echo lang("general_compliance_status"); ?></th>
                                    <th colspan="2" class="text-center"><?php echo lang("sub_total"); ?></th>
                                </tr>
                                <tr>
                                    <th class="text-center">N°</th>
                                    <th class="text-center">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($array_estados_evaluados_reportables as $estado_evaluado){ ?>
                                    <?php
                                        if($total_evaluado == 0){
                                            $porcentaje = 0;
                                        } else {
                                            $porcentaje = ($estado_evaluado["cant"] * 100) / ($total_evaluado);
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-left"><?php echo $estado_evaluado["nombre_estado"]; ?></td>
                                        <td class="text-right"><?php echo to_number_project_format($estado_evaluado["cant"], $id_proyecto); ?></td>
                                        <td class="text-right"><?php echo to_number_project_format($porcentaje, $id_proyecto); ?>%</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- LISTA DE COMPROMISOS CON EVALUACIONES NO CUMPLE -->
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="col-md-6">
                    <div class="grafico_torta" id="grafico_cumplimientos_reportables" style="height: 240px;"></div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive">
                        <div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">
                            <table id="tabla_evaluaciones_reportables_no_cumple" class="table table-striped">
                                <thead>
                                    <tr>
                                    <th class="text-center"><?php echo lang("compromise"); ?></th>
                                    <th class="text-center"><?php echo lang("critical_level"); ?></th>
                                    <th class="text-center"><?php echo lang("responsible"); ?></th>
                                    <th class="text-center"><?php echo lang("closing_term"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($array_compromisos_reportables_evaluaciones_no_cumple as $row) { ?>
                                    <tr>
                                    <td class="text-left"><?php echo $row->nombre_compromiso; ?></td>
                                    <td class="text-left"><?php echo $row->criticidad; ?></td>
                                    <td class="text-left"><?php echo $row->responsable_reporte; ?></td>
                                    <td class="text-left"><?php echo get_date_format($row->plazo_cierre, $id_proyecto); ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php } ?>
    <!-- FIN COMPROMISOS AMBIENTALES - REPORTABLES -->




    <!-- CONSUMOS -->
    <?php if($puede_ver_consumos && $report_config->consumptions) { ?>
        <div class="panel panel-default">

            <div class="page-title clearfix">
                <h1><?php echo lang("consumptions").' - '.lang("totals"); ?></h1>
            </div>

            <div class="panel-body">
                <table class="table table-bordered" id="tabla_consumo">
                    <tr>
                        <th colspan="4" style="text-align:center; background-color:<?php echo $color_sitio; ?>;"><?php echo lang("consumptions"); ?></th>
                    </tr>
                    <tr>
                        <th class="text-center"><?php echo lang("categories"); ?></th>
                        <th class="text-center"><?php echo lang("Reported_in_period"); ?></th>
                        <th class="text-center"><?php echo lang("accumulated"); ?></th>
                    </tr>

                    <?php foreach ($tabla_consumo_volumen_reportados as $id_categoria => $arreglo_valores){ ?>
                        
                        <?php
                            $arreglo_valores_acumulados = $tabla_consumo_volumen_acumulados[$id_categoria];
                            $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                            if($row_alias->alias){
                                $nombre_categoria = $row_alias->alias;
                            }else{
                                $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                                $nombre_categoria = $row_categoria->nombre;
                            }

                            $reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
                            $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
                        ?>

                        <tr>
                            <td><?php echo $nombre_categoria.' ('.$unidad_volumen.')'; ?></td>
                            <td class="text-right"><?php echo $reportado; ?></td>
                            <td class="text-right"><?php echo $acumulado; ?></td>
                        </tr>

                    <?php } ?>


                    <?php foreach ($tabla_consumo_masa_reportados as $id_categoria => $arreglo_valores){ ?>
                        
                        <?php
                            $arreglo_valores_acumulados = $tabla_consumo_masa_acumulados[$id_categoria];
                            $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                            if($row_alias->alias){
                                $nombre_categoria = $row_alias->alias;
                            }else{
                                $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                                $nombre_categoria = $row_categoria->nombre;
                            }

                            $reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
                            $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
                        ?>

                        <tr>
                            <td><?php echo $nombre_categoria.' ('.$unidad_masa.')'; ?></td>
                            <td class="text-right"><?php echo $reportado; ?></td>
                            <td class="text-right"><?php echo $acumulado; ?></td>
                        </tr>

                    <?php } ?>
                    

                    <?php /*foreach ($tabla_consumo_energia_reportados as $id_categoria => $arreglo_valores){ ?>
                        
                        <?php
                            $arreglo_valores_acumulados = $tabla_consumo_energia_acumulados[$id_categoria];
                            $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                            if($row_alias->alias){
                                $nombre_categoria = $row_alias->alias;
                            }else{
                                $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                                $nombre_categoria = $row_categoria->nombre;
                            }
                            
                            $reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
                            $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
                        ?>

                        <tr>
                            <td><?php echo $nombre_categoria.' ('.$unidad_energia.')'; ?></td>
                            <td class="text-right"><?php echo $reportado; ?></td>
                            <td class="text-right"><?php echo $acumulado; ?></td>
                        </tr>

                    <?php }*/ ?>


                    <?php foreach ($tabla_consumo_potencia_reportados as $id_categoria => $arreglo_valores){ ?>
                        
                        <?php
                            $arreglo_valores_acumulados = $tabla_consumo_potencia_acumulados[$id_categoria];
                            $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                            if($row_alias->alias){
                                $nombre_categoria = $row_alias->alias;
                            }else{
                                $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                                $nombre_categoria = $row_categoria->nombre;
                            }
                            
                            $reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
                            $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
                        ?>

                        <tr>
                            <td><?php echo $nombre_categoria.' ('.$unidad_potencia.')'; ?></td>
                            <td class="text-right"><?php echo $reportado; ?></td>
                            <td class="text-right"><?php echo $acumulado; ?></td>
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

                            $reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
                            $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
                        ?>

                        <tr>
                            <td><?php echo $nombre_categoria.' ('.$unidad_volumen.')'; ?></td>
                            <td class="text-right"><?php echo $reportado; ?></td>
                            <td class="text-right"><?php echo $acumulado; ?></td>
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

                            $reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
                            $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
                        ?>

                        <tr>
                            <td><?php echo $nombre_categoria.' ('.$unidad_masa.')'; ?></td>
                            <td class="text-right"><?php echo $reportado; ?></td>
                            <td class="text-right"><?php echo $acumulado; ?></td>
                        </tr>

                        <?php
                            $total_especies_reportados_masa_consumo += array_sum($arreglo_valores);
                            $total_especies_acumulados_masa_consumo += array_sum($arreglo_valores_acumulados);
                        ?>

                    <?php } ?>


                    <?php /*foreach ($tabla_consumo_energia_especies_reportados as $id_categoria => $arreglo_valores){ ?>
                        
                        <?php
                            $arreglo_valores_acumulados = $tabla_consumo_energia_especies_acumulados[$id_categoria];
                            $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                            if($row_alias->alias){
                                $nombre_categoria = $row_alias->alias;
                            }else{
                                $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                                $nombre_categoria = $row_categoria->nombre;
                            }

                            $reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
                            $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
                        ?>

                        <tr>
                            <td><?php echo $nombre_categoria.' ('.$unidad_energia.')'; ?></td>
                            <td class="text-right"><?php echo $reportado; ?></td>
                            <td class="text-right"><?php echo $acumulado; ?></td>
                        </tr>

                    <?php }*/ ?>

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

                            $reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
                            $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
                        ?>

                        <tr>
                            <td><?php echo $nombre_categoria.' ('.$unidad_potencia.')'; ?></td>
                            <td class="text-right"><?php echo $reportado; ?></td>
                            <td class="text-right"><?php echo $acumulado; ?></td>
                        </tr>

                    <?php } ?>

                        <tr>
                            <td><?php echo lang("total_species_produced").' ('.$unidad_masa.')'; ?></td>
                            <td class="text-right"><?php echo to_number_project_format($total_especies_reportados_masa_consumo, $id_proyecto); ?></td>
                            <td class="text-right"><?php echo to_number_project_format($total_especies_acumulados_masa_consumo, $id_proyecto); ?></td>
                        </tr>

                </table>
        
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="grafico_consumo" id="consumo_volumen"></div> <!-- m3 -->
                        </div>
                        <div class="col-md-12">
                            <div class="grafico_consumo" id="consumo_masa"></div> <!-- ton -->
                        </div>
                        <div class="col-md-12">
                            <!-- <div class="grafico_consumo" id="consumo_energia"></div> -->
                            <div class="grafico_consumo" id="consumo_potencia"></div>
                        </div>
                    </div>
                </div>
            </div>
        
        </div>
    <?php } ?>
    <!-- FIN CONSUMOS -->


    
    
    <!--RESIDUOS -->
    <?php if ($puede_ver_residuos && $report_config->waste) { ?>

        <div class="panel panel-default">
            <div class="page-title clearfix">
                <h1><?php echo lang("waste").' - '.lang("totals"); ?></h1>
            </div>

            <div class="panel-body">
                <table class="table table-bordered" id="tabla_residuo">
                    <tr>
                        <th colspan="4" style="text-align:center; background-color:<?php echo $color_sitio; ?>;"><?php echo lang("waste"); ?></th>
                    </tr>
                    <tr>
                        <th class="text-center"><?php echo lang("categories"); ?></th>
                        <th class="text-center"><?php echo lang("Reported_in_period"); ?></th>
                        <th class="text-center"><?php echo lang("accumulated"); ?></th>
                    </tr>

                    <?php foreach ($tabla_residuo_volumen_reportados as $id_categoria => $arreglo_valores){ ?>

                        <?php
                            $arreglo_valores_acumulados = $tabla_residuo_volumen_acumulados[$id_categoria];
                            $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                            if($row_alias->alias){
                                $nombre_categoria = $row_alias->alias;
                            }else{
                                $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                                $nombre_categoria = $row_categoria->nombre;
                            }
                            
                            $alerta = (array_sum($arreglo_valores_acumulados) > array_sum($arreglo_valores))?"warning":"";
                            $reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
                            $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
                        ?>

                       	<tr>
                       		<td class="text-left"><?php echo $nombre_categoria.' ('.$unidad_volumen.')'; ?></td>
                       		<td class="text-right"><?php echo $reportado; ?></td>
                       		<td class="text-right"><?php echo $acumulado; ?></td>
                       	</tr>

                    <?php } ?>


                    <?php foreach ($tabla_residuo_masa_reportados as $id_categoria => $arreglo_valores){ ?>

                        <?php
                            $arreglo_valores_acumulados = $tabla_residuo_masa_acumulados[$id_categoria];
                            $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                            if($row_alias->alias){
                                $nombre_categoria = $row_alias->alias;
                            }else{
                                $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                                $nombre_categoria = $row_categoria->nombre;
                            }
                            
                            $alerta = (array_sum($arreglo_valores_acumulados) > array_sum($arreglo_valores))?"warning":"";
                            $reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
                            $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
                        ?>
                        
                        <tr>
                            <td class="text-left"><?php echo $nombre_categoria.' ('.$unidad_masa.')'; ?></td>
                            <td class="text-right"><?php echo $reportado; ?></td>
                            <td class="text-right"><?php echo $acumulado; ?></td>
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
                            $reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
                            $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
                        ?>

                        <tr>
                            <td class="text-left"><?php echo $nombre_categoria.' ('.$unidad_volumen.')'; ?></td>
                            <td class="text-right"><?php echo $reportado; ?></td>
                            <td class="text-right"><?php echo $acumulado; ?></td>
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
                            $reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
                            $acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
                        ?>

                        <tr>
                            <td class="text-left"><?php echo $nombre_categoria.' ('.$unidad_masa.')'; ?></td>
                            <td class="text-right"><?php echo $reportado; ?></td>
                            <td class="text-right"><?php echo $acumulado; ?></td>
                        </tr>

                        <?php
                            $total_especies_reportados_masa_residuo += array_sum($arreglo_valores);
                            $total_especies_acumulados_masa_residuo += array_sum($arreglo_valores_acumulados);
                        ?>

				    <?php } ?>

                	<tr>
						<td><?php echo lang("total_species_produced").' ('.$unidad_masa.')'; ?></td>
						<td class="text-right"><?php echo to_number_project_format($total_especies_reportados_masa_residuo, $id_proyecto); ?></td>
				 		<td class="text-right"><?php echo to_number_project_format($total_especies_acumulados_masa_residuo, $id_proyecto); ?></td>
					</tr>

                </table>

            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="grafico_residuo" id="residuo_volumen"></div> <!-- m3 -->
                        </div>
                        <div class="col-md-6">
                            <div class="grafico_residuo" id="residuo_masa"></div> <!-- ton -->
                        </div>
                    </div>
                </div>
            </div>

        </div>

    <?php } ?>
    <!-- FIN RESIDUOS -->




    <!-- PERMISOS -->
    <?php if($puede_ver_permittings && $report_config->permittings){ ?>

        <div class="panel panel-default">
            <div class="page-title clearfix">
                <h1><?php echo lang("environmental_permittings").' - '.$environmental_authorization; ?></h1>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">
                        <table id="tabla_resumen_por_evaluado" class="table table-striped">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center" style="vertical-align:middle;"><?php echo lang("general_procedure_status"); ?></th>
                                    <?php foreach($evaluados_permisos as $evaluado) { ?>
                                        <th colspan="2" class="text-center"><?php echo $evaluado->nombre_evaluado; ?></th>
                                    <?php } ?>
                                    </tr>
                                <tr>
                                    <?php foreach($evaluados_permisos as $evaluado) { ?>
                                        <th class="text-center">N°</th>
                                        <th class="text-center">%</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="text-left"><?php echo lang("total_applicable_permittings"); ?></th>
                                    <?php foreach($evaluados_permisos as $evaluado) { ?>
                                        <td class=" text-right"><?php echo to_number_project_format(array_sum($array_total_por_evaluado_permisos[$evaluado->id]), $id_proyecto); ?></td>
                                        <td class=" text-right"><?php echo to_number_project_format(100, $id_proyecto); ?>%</td>
                                    <?php } ?>
                                </tr>
                                <?php foreach($array_estados_evaluados_permisos as $estado_evaluado) { ?>
                                    <tr>
                                        <td class="text-left"><?php echo $estado_evaluado["nombre_estado"]; ?></td>
                                        <?php foreach($estado_evaluado["evaluados"] as $id_evaluado => $evaluado) { ?>
                                            <?php
                                                $total_evaluado = array_sum($array_total_por_evaluado_permisos[$id_evaluado]);
                                                if($total_evaluado == 0){
                                                    $porcentaje = 0;
                                                } else {
                                                    $porcentaje = ($evaluado["cant"] * 100) / ($total_evaluado); 
                                                }
                                            ?>
                                            <td class="text-right"><?php echo to_number_project_format($evaluado["cant"], $id_proyecto); ?></td>
                                            <td class="text-right"><?php echo to_number_project_format($porcentaje, $id_proyecto); ?>%</td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



        <div class="panel panel-default">
            <div class="panel-body">
                <div class="col-md-6">
                    <div class="grafico_torta" id="grafico_cumplimientos_totales_permisos" style="height: 240px;"></div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive">
                        <div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">
                            <table id="tabla_permisos_no_cumple" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?php echo lang("permission"); ?></th>
                                        <th class="text-center"><?php echo lang("critical_level"); ?></th>
                                        <th class="text-center"><?php echo lang("report_responsible"); ?></th>
                                        <th class="text-center"><?php echo lang("closing_term"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($array_permisos_evaluaciones_no_cumple as $row) { ?>
                                        <tr>
                                            <td class="text-left"><?php echo $row->nombre_permiso; ?></td>
                                            <td class="text-left"><?php echo $row->criticidad; ?></td>
                                            <td class="text-left"><?php echo $row->responsable_reporte; ?></td>
                                            <td class="text-left"><?php echo get_date_format($row->plazo_cierre, $id_proyecto); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <?php } ?>




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
            $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $categoria["id"], 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
            if($row_alias->alias){
                $nombre_categoria = $row_alias->alias;
            }else{
                $row_categoria = $this->Categories_model->get_one_where(array('id' => $categoria["id"], 'deleted' => 0));
                $nombre_categoria = $row_categoria->nombre;
            }
            $array_nombre_categorias[$cat]["nombre"] = $nombre_categoria;
        }
    ?>
    
    <!-- INDICADORES - AGUA -->
    <div class="panel panel-default">

        <div class="page-title clearfix">
            <h1><?php echo lang("indicators").' - '.lang("water"); ?></h1>
        </div>

        <div class="panel-body">

            <div class="col-md-12">

                <table class="table table-bordered" id="">
                    <tr>
                        <th colspan="4" style="text-align:center; background-color:<?php echo $color_sitio; ?>;"><?php echo lang("monthly_levels")." - ".lang("water"); ?></th>
                    </tr>
                    <tr>
                        <th class="text-center"><?php echo lang("months"); ?></th>
                        <th class="text-center"><?php echo $array_nombre_categorias["agua_industrial"]["nombre"]." (".$unidad_volumen.")"; ?></th>
                        <th class="text-center"><?php echo $array_nombre_categorias["agua_potable"]["nombre"]." (".$unidad_volumen.")"; ?></th>
                    </tr>

                    <?php foreach($rango_fechas as $anio => $meses) { ?>
                        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                            <tr>
                                <th class="text-center"><?php echo $nombre_mes." - ".$anio; ?></th>
                                <td class="text-center">
                                    <?php 
                                        $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                            "id_cliente" => $id_cliente,
                                            "id_proyecto" => $id_proyecto,
                                            "id_material" => 6, // Agua
                                            "id_categoria" => 15, // Agua industrial
                                            "id_tipo_unidad" => 2, // Volumen
                                            "flujo" => "Consumo",
                                            "mes" => $numero_mes,
                                            "anio" => $anio
                                        ));
                                        echo to_number_project_format($valor_categoria["valor_categoria"], $id_proyecto);
                                    ?> 
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                            "id_cliente" => $id_cliente,
                                            "id_proyecto" => $id_proyecto,
                                            "id_material" => 6, // Agua
                                            "id_categoria" => 16, // Agua potable
                                            "id_tipo_unidad" => 2, // Volumen
                                            "flujo" => "Consumo",
                                            "mes" => $numero_mes,
                                            "anio" => $anio
                                        ));
                                        echo to_number_project_format($valor_categoria["valor_categoria"], $id_proyecto);
                                    ?> 
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </table>

            </div>

            <div class="col-md-6">
                <div id="grafico_ind_consumo_mensual_agua"></div>
            </div>
            <div class="col-md-6">
                <div id="grafico_ind_consumo_produccion_agua"></div>
            </div>

        </div>

    </div>
    <!-- FIN INDICADORES - AGUA -->



    <!-- INDICADORES - ENERGÍA -->
    <div class="panel panel-default">

        <div class="page-title clearfix">
            <h1><?php echo lang("indicators").' - '.lang("energy"); ?></h1>
        </div>

        <div class="panel-body">

            <div class="col-md-12">

                <table class="table table-bordered" id="">
                    <tr>
                        <th colspan="4" style="text-align:center; background-color:<?php echo $color_sitio; ?>;"><?php echo lang("monthly_levels")." - ".lang("energy"); ?></th>
                    </tr>
                    <tr>
                        <th class="text-center"><?php echo lang("months"); ?></th>
                        <!--<th class="text-center"><?php echo lang("electricity")." (".$unidad_potencia.")"; ?></th>
                        <th class="text-center"><?php echo lang("generator_fuel")." (".$unidad_volumen.")"; ?></th>
                        <th class="text-center"><?php echo lang("liquid_gas")." (".$unidad_masa.")"; ?></th>-->
                        <th class="text-center"><?php echo $array_nombre_categorias["electricidad"]["nombre"]." (".$unidad_potencia.")"; ?></th>
                        <th class="text-center"><?php echo $array_nombre_categorias["equipos_generacion_electrica"]["nombre"]." (".$unidad_volumen.")"; ?></th>
                        <th class="text-center"><?php echo $array_nombre_categorias["gas_licuado"]["nombre"]." (".$unidad_masa.")"; ?></th>
                    </tr>

                    <?php foreach($rango_fechas as $anio => $meses) { ?>
                        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                            <tr>
                                <th class="text-center"><?php echo $nombre_mes." - ".$anio; ?></th>
                                <td class="text-center">
                                    <?php 
                                        $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                            "id_cliente" => $id_cliente,
                                            "id_proyecto" => $id_proyecto,
                                            "id_material" => 2, // Electricidad
                                            "id_categoria" => 250, // Adquisición + pérdidas de electricidad
                                            "id_tipo_unidad" => 6, // Potencia
                                            "flujo" => "Consumo",
                                            "mes" => $numero_mes,
                                            "anio" => $anio
                                        ));
                                        echo to_number_project_format($valor_categoria["valor_categoria"], $id_proyecto);
                                    ?> 
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                            "id_cliente" => $id_cliente,
                                            "id_proyecto" => $id_proyecto,
                                            "id_material" => 35, // Maquinaria
                                            "id_categoria" => 131, // Equipos Generación Eléctrica
                                            "id_tipo_unidad" => 2, // Volumen
                                            "flujo" => "Consumo",
                                            "mes" => $numero_mes,
                                            "anio" => $anio
                                        ));
                                        echo to_number_project_format($valor_categoria["valor_categoria"], $id_proyecto);
                                    ?> 
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                            "id_cliente" => $id_cliente,
                                            "id_proyecto" => $id_proyecto,
                                            "id_material" => 14, // Combustible en base a petróleo
                                            "id_categoria" => 158, // Gas licuado
                                            "id_tipo_unidad" => 1, // Masa
                                            "flujo" => "Consumo",
                                            "mes" => $numero_mes,
                                            "anio" => $anio
                                        ));
                                        echo to_number_project_format($valor_categoria["valor_categoria"], $id_proyecto);
                                    ?> 
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </table>

            </div>

            <div class="col-md-6">
                <div id="grafico_ind_consumo_mensual_electricidad"></div>
            </div>
            <div class="col-md-6">
                <div id="grafico_ind_consumo_produccion_electricidad"></div>
            </div>
            <div class="col-md-6">
                <div id="grafico_ind_consumo_mensual_combustible"></div>
            </div>
            <div class="col-md-6">
                <div id="grafico_ind_consumo_produccion_combustible"></div>
            </div>
            <div class="col-md-6">
                <div id="grafico_ind_consumo_mensual_gas"></div>
            </div>
            <div class="col-md-6">
                <div id="grafico_ind_consumo_produccion_gas"></div>
            </div>

        </div>

    </div>
    <!-- FIN INDICADORES - ENERGÍA -->



    <!-- INDICADORES - PRODUCTOS FITOSANITARIOS -->
    <div class="panel panel-default">

        <div class="page-title clearfix">
            <h1><?php echo lang("indicators").' - '.lang("phytosanitary_products"); ?></h1>
        </div>

        <div class="panel-body">

            <div class="col-md-12">

                <table class="table table-bordered" id="">
                    <tr>
                        <th colspan="<?php echo count($array_cat_prod_fito) + 1; ?>" style="text-align:center; background-color:<?php echo $color_sitio; ?>;"><?php echo lang("monthly_levels")." - ".lang("phytosanitary_products"); ?></th>
                    </tr>
                    <tr>
                        <th class="text-center"><?php echo lang("months"); ?></th>
                        <?php foreach($array_cat_prod_fito as $id_cat => $nombre_cat){ ?>
                            <?php 
                                $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                                if($row_alias->alias){
                                    $nombre_categoria = $row_alias->alias;
                                }else{
                                    $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                                    $nombre_categoria = $row_categoria->nombre;
                                }
                            ?>
                            <th class="text-center"><?php echo $nombre_categoria." (".$unidad_volumen.")"; ?></th>
                        <?php } ?>
                    </tr>

                    <?php foreach($rango_fechas as $anio => $meses) { ?>
                        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                            <tr>
                            <th class="text-center"><?php echo $nombre_mes." - ".$anio; ?></th>
                                <?php foreach($array_cat_prod_fito as $id_cat => $nombre_cat) { ?>
                                    <td class="text-center">
                                        <?php 
                                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                                "id_cliente" => $id_cliente,
                                                "id_proyecto" => $id_proyecto,
                                                "id_material" => 65, // Productos fitosanitarios
                                                "id_categoria" => $id_cat,
                                                "id_tipo_unidad" => 2, // Volumen
                                                "flujo" => "Consumo",
                                                "mes" => $numero_mes,
                                                "anio" => $anio
                                            ));
                                            echo to_number_project_format($valor_categoria["valor_categoria"], $id_proyecto);
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </table>

            </div>
            
            <div class="col-md-6">
                <div id="grafico_ind_consumo_mensual_pf"></div>
            </div>
            <div class="col-md-6">
                <div id="grafico_ind_consumo_produccion_pf"></div>
            </div>
            
        </div>

    </div>
    <!-- FIN INDICADORES - PRODUCTOS FITOSANITARIOS -->



    <!-- INDICADORES - REFRIGERANTES -->
    <div class="panel panel-default">

        <div class="page-title clearfix">
            <h1><?php echo lang("indicators").' - '.lang("refrigerants"); ?></h1>
        </div>

        <div class="panel-body">

            <div class="col-md-12">

                <table class="table table-bordered" id="">
                    <tr>
                        <th colspan="<?php echo count($array_cat_prod_ref) + 1; ?>" style="text-align:center; background-color:<?php echo $color_sitio; ?>;"><?php echo lang("monthly_levels")." - ".lang("refrigerants"); ?></th>
                    </tr>
                    <tr>
                        <th class="text-center"><?php echo lang("months"); ?></th>
                        <?php foreach($array_cat_prod_ref as $id_cat => $nombre_cat){ ?>
                            <?php 
                                $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                                if($row_alias->alias){
                                    $nombre_categoria = $row_alias->alias;
                                }else{
                                    $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                                    $nombre_categoria = $row_categoria->nombre;
                                }
                            ?>
                            <th class="text-center"><?php echo $nombre_categoria." (".$unidad_masa.")"; ?></th>
                        <?php } ?>
                    </tr>

                    <?php foreach($rango_fechas as $anio => $meses) { ?>
                        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                            <tr>
                            <th class="text-center"><?php echo $nombre_mes." - ".$anio; ?></th>
                                <?php foreach($array_cat_prod_ref as $id_cat => $nombre_cat) { ?>
                                    <td class="text-center">
                                        <?php 
                                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                                "id_cliente" => $id_cliente,
                                                "id_proyecto" => $id_proyecto,
                                                "id_material" => 40, // Refrigerante
                                                "id_categoria" => $id_cat,
                                                "id_tipo_unidad" => 1, // Masa
                                                "flujo" => "Consumo",
                                                "mes" => $numero_mes,
                                                "anio" => $anio
                                            ));
                                            echo to_number_project_format($valor_categoria["valor_categoria"], $id_proyecto);
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </table>

            </div>
            
            <div class="col-md-6">
                <div id="grafico_ind_consumo_mensual_ref"></div>
            </div>
            <div class="col-md-6">
                <div id="grafico_ind_consumo_produccion_ref"></div>
            </div>
            
        </div>

    </div>
    <!-- FIN INDICADORES - REFRIGERANTES -->




    <!-- INDICADORES - RESIDUOS NO PELIGROSOS -->
    <div class="panel panel-default">

        <div class="page-title clearfix">
            <h1><?php echo lang("indicators").' - '.lang("non_hazardous_waste"); ?></h1>
        </div>

        <div class="panel-body">

            <div class="col-md-12">

                <table class="table table-bordered" id="">
                    <tr>
                        <th colspan="<?php echo count($array_cat_prod_rsd) + count($array_cat_prod_rinp) + 1; ?>" style="text-align:center; background-color:<?php echo $color_sitio; ?>;"><?php echo lang("monthly_levels")." - ".lang("non_hazardous_waste"); ?></th>
                    </tr>
                    <tr>
                        <th class="text-center"><?php echo lang("months"); ?></th>
                        <?php foreach($array_cat_prod_rsd as $id_cat => $nombre_cat){ ?>
                            <?php 
                                $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                                if($row_alias->alias){
                                    $nombre_categoria = $row_alias->alias;
                                }else{
                                    $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                                    $nombre_categoria = $row_categoria->nombre;
                                }
                            ?>
                            <th class="text-center"><?php echo $nombre_categoria." (".$unidad_masa.")"; ?></th>
                        <?php } ?>
                        <?php foreach($array_cat_prod_rinp as $id_cat => $nombre_cat){ ?>
                            <?php 
                                $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                                if($row_alias->alias){
                                    $nombre_categoria = $row_alias->alias;
                                }else{
                                    $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                                    $nombre_categoria = $row_categoria->nombre;
                                }
                            ?>
                            <th class="text-center"><?php echo $nombre_categoria." (".$unidad_masa.")"; ?></th>
                        <?php } ?>
                    </tr>

                    <?php foreach($rango_fechas as $anio => $meses) { ?>
                        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                            <tr>
                                <th class="text-center"><?php echo $nombre_mes." - ".$anio; ?></th>
                                <?php foreach($array_cat_prod_rsd as $id_cat => $nombre_cat) { ?>
                                    <td class="text-center">
                                        <?php 
                                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                                "id_cliente" => $id_cliente,
                                                "id_proyecto" => $id_proyecto,
                                                "id_material" => 29, // Residuos sólidos domiciliarios
                                                "id_categoria" => $id_cat,
                                                "id_tipo_unidad" => 1, // Masa
                                                "flujo" => "Residuo",
                                                "mes" => $numero_mes,
                                                "anio" => $anio
                                            ));
                                            echo to_number_project_format($valor_categoria["valor_categoria"], $id_proyecto);
                                        ?>
                                    </td>
                                <?php } ?>
                                <?php foreach($array_cat_prod_rinp as $id_cat => $nombre_cat) { ?>
                                    <td class="text-center">
                                        <?php 
                                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                                "id_cliente" => $id_cliente,
                                                "id_proyecto" => $id_proyecto,
                                                "id_material" => 30, // Residuos Industriales no Peligrosos
                                                "id_categoria" => $id_cat,
                                                "id_tipo_unidad" => 1, // Masa
                                                "flujo" => "Residuo",
                                                "mes" => $numero_mes,
                                                "anio" => $anio
                                            ));
                                            echo to_number_project_format($valor_categoria["valor_categoria"], $id_proyecto);
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </table>

            </div>
            
            <div class="col-md-6">
                <div id="grafico_ind_residuo_mensual_nhw"></div>
            </div>
            <div class="col-md-6">
                <div id="grafico_ind_residuo_produccion_nhw"></div>
            </div>
            
        </div>

    </div>
    <!-- FIN INDICADORES - RESIDUOS NO PELIGROSOS -->




    <!-- INDICADORES - RESIDUOS PELIGROSOS -->
    <div class="panel panel-default">

        <div class="page-title clearfix">
            <h1><?php echo lang("indicators").' - '.lang("hazardous_waste"); ?></h1>
        </div>

        <div class="panel-body">

            <div class="col-md-12">

                <table class="table table-bordered" id="">
                    <tr>
                        <th colspan="<?php echo count($array_cat_prod_rip_masa) + 1; ?>" style="text-align:center; background-color:<?php echo $color_sitio; ?>;"><?php echo lang("monthly_levels")." - ".lang("solid_hazardous_waste"); ?></th>
                    </tr>
                    <tr>
                        <th class="text-center"><?php echo lang("months"); ?></th>
                        <?php foreach($array_cat_prod_rip_masa as $id_cat => $nombre_cat){ ?>
                            <?php 
                                $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                                if($row_alias->alias){
                                    $nombre_categoria = $row_alias->alias;
                                }else{
                                    $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                                    $nombre_categoria = $row_categoria->nombre;
                                }
                            ?>
                            <th class="text-center"><?php echo $nombre_categoria." (".$unidad_masa.")"; ?></th>
                        <?php } ?>
                    </tr>

                    <?php foreach($rango_fechas as $anio => $meses) { ?>
                        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                            <tr>
                                <th class="text-center"><?php echo $nombre_mes." - ".$anio; ?></th>
                                <?php foreach($array_cat_prod_rip_masa as $id_cat => $nombre_cat) { ?>
                                    <td class="text-center">
                                        <?php 
                                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                                "id_cliente" => $id_cliente,
                                                "id_proyecto" => $id_proyecto,
                                                "id_material" => 33, // Residuos industriales peligrosos
                                                "id_categoria" => $id_cat,
                                                "id_tipo_unidad" => 1, // Masa
                                                "flujo" => "Residuo",
                                                "mes" => $numero_mes,
                                                "anio" => $anio
                                            ));
                                            echo to_number_project_format($valor_categoria["valor_categoria"], $id_proyecto);
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </table>

            </div>
            
            <div class="col-md-6">
                <div id="grafico_ind_residuo_mensual_hw"></div>
            </div>
            <div class="col-md-6">
                <div id="grafico_ind_residuo_produccion_hw"></div>
            </div>


            <div class="col-md-12">

                <table class="table table-bordered" id="">
                    <tr>
                        <th colspan="<?php echo count($array_cat_prod_rip_volumen) + count($array_cat_prod_rli_volumen) + 1; ?>" style="text-align:center; background-color:<?php echo $color_sitio; ?>;"><?php echo lang("monthly_levels")." - ".lang("liquid_hazardous_waste"); ?></th>
                    </tr>
                    <tr>
                        <th class="text-center"><?php echo lang("months"); ?></th>
                        <?php foreach($array_cat_prod_rip_volumen as $id_cat => $nombre_cat){ ?>
                            <?php 
                                $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                                if($row_alias->alias){
                                    $nombre_categoria = $row_alias->alias;
                                }else{
                                    $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                                    $nombre_categoria = $row_categoria->nombre;
                                }
                            ?>
                            <th class="text-center"><?php echo $nombre_categoria." (".$unidad_volumen.")"; ?></th>
                        <?php } ?>
                         <?php foreach($array_cat_prod_rli_volumen as $id_cat => $nombre_cat){ ?>
                            <?php 
                                $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                                if($row_alias->alias){
                                    $nombre_categoria = $row_alias->alias;
                                }else{
                                    $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                                    $nombre_categoria = $row_categoria->nombre;
                                }
                            ?>
                            <th class="text-center"><?php echo $nombre_categoria." (".$unidad_volumen.")"; ?></th>
                        <?php } ?>
                    </tr>

                    <?php foreach($rango_fechas as $anio => $meses) { ?>
                        <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                            <tr>
                                <th class="text-center"><?php echo $nombre_mes." - ".$anio; ?></th>
                                <?php foreach($array_cat_prod_rip_volumen as $id_cat => $nombre_cat) { ?>
                                    <td class="text-center">
                                        <?php 
                                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                                "id_cliente" => $id_cliente,
                                                "id_proyecto" => $id_proyecto,
                                                "id_material" => 33, // Residuos industriales peligrosos
                                                "id_categoria" => $id_cat,
                                                "id_tipo_unidad" => 2, // Volumen
                                                "flujo" => "Residuo",
                                                "mes" => $numero_mes,
                                                "anio" => $anio
                                            ));
                                            echo to_number_project_format($valor_categoria["valor_categoria"], $id_proyecto);
                                        ?>
                                    </td>
                                <?php } ?>
                                <?php foreach($array_cat_prod_rli_volumen as $id_cat => $nombre_cat) { ?>
                                    <td class="text-center">
                                        <?php 
                                            $valor_categoria = $Reports_controller->get_data_tablas_indicadores(array(
                                                "id_cliente" => $id_cliente,
                                                "id_proyecto" => $id_proyecto,
                                                "id_material" => 31, // Residuos Líquidos Industriales
                                                "id_categoria" => $id_cat,
                                                "id_tipo_unidad" => 2, // Volumen
                                                "flujo" => "Residuo",
                                                "mes" => $numero_mes,
                                                "anio" => $anio
                                            ));
                                            echo to_number_project_format($valor_categoria["valor_categoria"], $id_proyecto);
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </table>

            </div>
            
            <div class="col-md-6">
                <div id="grafico_ind_residuo_mensual_lhw"></div>
            </div>
            <div class="col-md-6">
                <div id="grafico_ind_residuo_produccion_lhw"></div>
            </div>

        </div>

    </div>
    <!-- FIN INDICADORES - RESIDUOS PELIGROSOS -->






</div><!--Fin div contenido -->


<script type="text/javascript">

    var decimals_separator = AppHelper.settings.decimalSeparator;
    var thousands_separator = AppHelper.settings.thousandSeparator;
    var decimal_numbers = AppHelper.settings.decimalNumbers;

    // Compromisos RCA
    <?php if ($puede_ver_compromisos_rca) { ?>

        <?php if(!empty(array_filter($total_cantidades_estados_evaluados))){ ?>

                $("#grafico_cumplimientos_totales").highcharts({
					chart: {
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: "pie",
						events: {
						   load: function() {
							   if (this.options.chart.forExport) {
								   Highcharts.each(this.series, function (series) {
									   series.update({
										   dataLabels: {
											   enabled: true,
											}
										}, false);
									});
									this.redraw();
								}
							}
						}
					},
					title: {
						text: "",
					},
					credits: {
						enabled: false
					},
					tooltip: {
						formatter: function() {
							return "<b>"+ this.point.name +"</b>: "+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +" %";
						},
					},
					plotOptions: {
						pie: {
						allowPointSelect: true,
						cursor: "pointer",
						dataLabels: {
							enabled: false,
							format: "<b>{point.name}</b>: {point.percentage:." + decimal_numbers + "f} %",
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black",
								fontSize: "9px",
								distance: -30
							},
							crop: false
						},
						showInLegend: true
						}
					},
					legend: {
						enabled: true,
						itemStyle:{
							fontSize: "9px"
						}
					},
					exporting: {
					    <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("compromises").'_'.clean(lang("total_compliances")).'_'.date("Y-m-d"); ?>
						filename: "<?php echo $nombre_exportacion; ?>",
						buttons: {
							contextButton: {
								menuItems: [{
									text: "<?php echo lang('export_to_png'); ?>",
									onclick: function() {
										this.exportChart();
									},
									separator: false
								}]
							}
						}
					},
					colors: [
                        <?php
                            foreach($total_cantidades_estados_evaluados as $estado) { 
                                echo "'".$estado["color"]."', ";
                            }
                        ?>
					],
					series: [{
						name: "Porcentaje",
						colorByPoint: true,
						data: [
						<?php foreach($total_cantidades_estados_evaluados as $estado) { ?>
							{
								name: "<?php echo $estado["nombre_estado"]; ?>",
								<?php $y = (($estado["cantidad_categoria"] * 100) / $total_compromisos_aplicables); ?>
								y: <?php echo $y; ?>
							},
						<?php } ?>
						]
					}]
				});

        <?php } else { ?>

            $("#grafico_cumplimientos_totales").html("<?php echo "<strong>".lang("no_information_available")."</strong>"; ?>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
        
        <?php } ?>

    <?php } ?>
    // FIN COMPROMISOS RCA



    // COMPROMISOS REPORTABLES
    <?php if ($puede_ver_compromisos_reportables) { ?>

        <?php if(!empty(array_filter($array_grafico_reportables))){ ?>

            $("#grafico_cumplimientos_reportables").highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: "pie",
                    events: {
                        load: function() {
                            if (this.options.chart.forExport) {
                                Highcharts.each(this.series, function (series) {
                                    series.update({
                                        dataLabels: {
                                            enabled: true,
                                        }
                                    }, false);
                                });
                                this.redraw();
                            }
                        }
                    }
                },
                title: {
                    text: "",
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    formatter: function() {
                        return "<b>"+ this.point.name +"</b>: "+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +" %";
                    },
                },
                plotOptions: {
                    pie: {
                    allowPointSelect: true,
                    cursor: "pointer",
                    dataLabels: {
                        enabled: false,
                        format: "<b>{point.name}</b>: {point.percentage:." + decimal_numbers + "f} %",
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black",
                            fontSize: "9px",
                            distance: -30
                        },
                        crop: false
                    },
                    showInLegend: true
                    }
                },
                legend: {
                    enabled: true,
                    itemStyle:{
                        fontSize: "9px"
                    }
                },
                exporting: {
                <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("compromises").'_'.clean(lang("reportable_compliances")).'_'.date("Y-m-d"); ?>
                    filename: "<?php echo $nombre_exportacion; ?>",
                    buttons: {
                        contextButton: {
                            menuItems: [{
                                text: "<?php echo lang('export_to_png'); ?>",
                                onclick: function() {
                                    this.exportChart();
                                },
                                separator: false
                            }]
                        }
                    }
                },
                colors: [
                    <?php
                        foreach($array_grafico_reportables as $estado) { 
                            echo "'".$estado["color"]."', ";
                        }
                    ?>
                ],
                series: [{
                    name: "Porcentaje",
                    colorByPoint: true,
                    data: [
                    <?php foreach($array_grafico_reportables as $estado) { ?>
                        {
                            name: "<?php echo $estado["nombre_estado"]; ?>",
                            <?php $y = $estado["porcentaje"]; ?>
                            y: <?php echo $y; ?>
                        },
                    <?php } ?>
                    ]
                }]
            });

        <?php } else { ?>

            $("#grafico_cumplimientos_reportables").html("<?php echo "<strong>".lang("no_information_available")."</strong>"; ?>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
        
        <?php } ?>

    <?php } ?>
    // FIN COMPROMISOS REPORTABLES



    // COMSUMOS
    <?php if ($puede_ver_consumos) { ?>

        $("#consumo_volumen").highcharts({
			chart:{
				zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
				type: "column",
				events: {load: function(event){}}
			},
			title: {
                text: "<?php echo lang('consumptions').' ('.$unidad_volumen.')'; ?>"
            },
			subtitle: {
                text: ""
            },

			<?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("consumption").'_'.$unidad_volumen.'_'.date("Y-m-d"); ?>

            exporting: {
                filename: "<?php echo $nombre_exportacion; ?>",
                buttons: {
                    contextButton: {
                        menuItems: [{
                            text: "<?php echo lang('export_to_png'); ?>",
                            onclick: function() {
                                this.exportChart();
                            },
                            separator: false
                        }]
                    }
                }
            },
			xAxis: {
			    min: 0,
			    categories: [
							<?php foreach ($Reports_controller->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date, $especies = false)->array_grafico_consumos_volumen_categories as $index => $value){ ?>
								<?php echo "'".$value."', "; ?>
							<?php } ?>
							<?php foreach ($Reports_controller->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_consumos_volumen_categories as $index => $value){ ?>
								<?php echo "'".$value."', "; ?>
                            <?php } ?>
			    ],							
			    crosshair: true
			},
            yAxis: {
                min: 0,
                title: {
                    text: "<?php echo $unidad_volumen_nombre_real.' ('.$unidad_volumen.')'; ?>"
                },
                labels:{
                    formatter: function(){
                        return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                    }
                },
            },
            credits: {
                enabled: false
            },
            tooltip: {
                headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
                pointFormatter: function(){
                    return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                    + "<td style='padding:0'><b>" 
                    + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                    + " <?php echo $unidad_volumen; ?> </b></td></tr>"
                },
                footerFormat:"</table>",
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        color: "#000000",
                        align: "center",
                        formatter: function(){
                            return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                        },
                        style: {
                            fontSize: "10px",
                            fontFamily: "Segoe ui, sans-serif"
                        },
                        format: "{y:,." + decimal_numbers + "f}",
                    }
                }
            },
            colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
            series: [
                {
                    name: "<?php echo lang('reported'); ?>",
                    data: [
                            <?php foreach($Reports_controller->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date, $especies = false)->array_grafico_consumos_volumen_data as $categoria_valor){ ?>
                                <?php echo $categoria_valor.", "; ?>
                            <?php } ?>
                            <?php foreach($Reports_controller->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_consumos_volumen_data as $categoria_valor){ ?>
                                <?php echo $categoria_valor.", "; ?>
                            <?php } ?>
                    ]
                },
                {
                    name: "<?php echo lang('accumulated'); ?>",
                    data: [
                            <?php foreach($Reports_controller->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date, $especies = false)->array_grafico_consumos_volumen_data_a as $categoria_valor){ ?>
                                <?php echo $categoria_valor.", "; ?>
                            <?php } ?>
                            <?php foreach($Reports_controller->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_consumos_volumen_data_a as $categoria_valor){ ?>
                                <?php echo $categoria_valor.", "; ?>
                            <?php } ?>
                    ]
                },
            ]
		});

        $("#consumo_masa").highcharts({

            chart:{
                zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
                type: "column",
                events: {load: function(event){}}
            },
            title: {
                text: "<?php echo lang('consumptions').' ('.$unidad_masa.')'; ?>"
            },
            subtitle: {
                text: ""
            },

            <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("consumption").'_'.$unidad_masa.'_'.date("Y-m-d"); ?>

            exporting: {
                filename: "<?php echo $nombre_exportacion; ?>",
                buttons: {
                    contextButton: {
                        menuItems: [{
                            text: "<?php echo lang('export_to_png'); ?>",
                            onclick: function() {
                                this.exportChart();
                            },
                            separator: false
                        }]
                    }
                }
            },

            xAxis: {
                min: 0,
                categories: [
                    <?php foreach ($Reports_controller->get_datos_grafico_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_masa_categories as $index => $value){ ?>
                        <?php echo "'".$value."', "; ?>
                    <?php } ?>
                    <?php foreach ($Reports_controller->get_datos_grafico_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_consumos_masa_categories as $index => $value){ ?>
                        <?php echo "'".$value."', "; ?>
                    <?php } ?>
                ],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: "<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.')'; ?>"
                },
                labels:{ 
                    formatter: function(){
                        return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                    } 
                },
            },
            credits: {
                enabled: false
            },
           	tooltip: {
           		headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
                pointFormatter: function(){
                    return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                    + "<td style='padding:0'><b>" 
                    + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                    + " <?php echo $unidad_masa; ?> </b></td></tr>"
                },
           		footerFormat:"</table>",
           		shared: true,
           		useHTML: true
           	},
           	plotOptions: {
           		column: {
           			pointPadding: 0.2,
           			borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        color: "#000000",
                        align: "center",
                        formatter: function(){
                            return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                        },
                        style: {
                            fontSize: "10px",
                            fontFamily: "Segoe ui, sans-serif"
                        },
                        format: "{y:,." + decimal_numbers + "f}",
                    }
           		}
           	},
            colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
            series: [
                {
                    name: "<?php echo lang('reported'); ?>",
                    data: [
                            <?php foreach($Reports_controller->get_datos_grafico_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_masa_data as $categoria_valor){ ?>
                                <?php echo $categoria_valor.", "; ?>
                            <?php } ?>
                            <?php foreach($Reports_controller->get_datos_grafico_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_consumos_masa_data as $categoria_valor){ ?>
                                <?php echo $categoria_valor.", "; ?>
                            <?php } ?>
                    ]
                },
                {
                    name: "<?php echo lang('accumulated'); ?>",
                    data: [

                        <?php foreach($Reports_controller->get_datos_grafico_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_masa_data_a as $categoria_valor){ ?>
                            <?php echo $categoria_valor.", "; ?>
                        <?php } ?>
                        <?php foreach($Reports_controller->get_datos_grafico_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_consumos_masa_data_a as $categoria_valor){ ?>
                            <?php echo $categoria_valor.", "; ?>
                        <?php } ?>
                    ]
                },
            ]
        });

        //$("#consumo_energia").highcharts({
        $("#consumo_potencia").highcharts({
			chart:{
				zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
				type: "column",
				events: {load: function(event){}}
			},

            title: {
                text: "<?php echo lang('consumptions').' ('.$unidad_potencia.')'; ?>"
            },
            subtitle: {
                text: ""
            },

			<?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("consumption").'_'.$unidad_potencia.'_'.date("Y-m-d"); ?>

            exporting: {
                filename: "<?php echo $nombre_exportacion; ?>",
                buttons: {
                    contextButton: {
                        menuItems: [{
                            text: "<?php echo lang('export_to_png'); ?>",
                            onclick: function() {
                                this.exportChart();
                            },
                            separator: false
                        }]
                    }
                }
            },

            xAxis: {
                min: 0,
                categories: [
                    <?php foreach ($Reports_controller->get_datos_grafico_consumo_potencia($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_potencia_categories as $index => $value){ ?>
                        <?php echo "'".$value."', "; ?>
                    <?php } ?>
                    <?php foreach ($Reports_controller->get_datos_grafico_consumo_potencia($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_consumos_potencia_categories as $index => $value){ ?>
                        <?php echo "'".$value."', "; ?>
                    <?php } ?>
                ],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: "<?php echo $unidad_potencia_nombre_real.' ('.$unidad_potencia.')'; ?>"
                },
                labels:{ 
                    formatter: function(){
                        return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                    } 
                },
            },
            credits: {
                enabled: false
            },
           	tooltip: {
           		headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
                pointFormatter: function(){
                    return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                    + "<td style='padding:0'><b>" 
                    + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                    + " <?php echo $unidad_potencia; ?> </b></td></tr>"
                },
           		footerFormat:"</table>",
           		shared: true,
           		useHTML: true
           	},
            plotOptions: {
           		column: {
           			pointPadding: 0.2,
           			borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        color: "#000000",
                        align: "center",
                        formatter: function(){
                            return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                        },
                        style: {
                            fontSize: "10px",
                            fontFamily: "Segoe ui, sans-serif"
                        },
                        format: "{y:,." + decimal_numbers + "f}",
                    }
           		}
           	},
            colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
            series: [
                {
                    name: "<?php echo lang('reported'); ?>",
                    data: [
                            <?php foreach($Reports_controller->get_datos_grafico_consumo_potencia($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_potencia_data as $categoria_valor){ ?>
                                <?php echo $categoria_valor.", "; ?>
                            <?php } ?>
                            <?php foreach($Reports_controller->get_datos_grafico_consumo_potencia($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_consumos_potencia_data as $categoria_valor){ ?>
                                <?php echo $categoria_valor.", "; ?>
                            <?php } ?>
                    ]
                },
                {
                    name: "<?php echo lang('accumulated'); ?>",
                    data: [

                        <?php foreach($Reports_controller->get_datos_grafico_consumo_potencia($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_potencia_data_a as $categoria_valor){ ?>
                            <?php echo $categoria_valor.", "; ?>
                        <?php } ?>
                        <?php foreach($Reports_controller->get_datos_grafico_consumo_potencia($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_consumos_potencia_data_a as $categoria_valor){ ?>
                            <?php echo $categoria_valor.", "; ?>
                        <?php } ?>
                    ]
                },
            ]
        });

    <?php } ?>
    // FIN CONSUMOS



    // RESIDUOS
    <?php if ($puede_ver_residuos) { ?>

        $("#residuo_volumen").highcharts({

            chart:{
                zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
                type: "column",
                events: {load: function(event){}}
            },

            title: {
                text: "<?php echo lang('waste').' ('.$unidad_volumen.')'; ?>"
            },
            subtitle: {
                text: ""
            },
        
            <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("waste").'_'.$unidad_volumen.'_'.date("Y-m-d"); ?>
        
            exporting: {
                filename: "<?php echo $nombre_exportacion; ?>",
                buttons: {
                    contextButton: {
                        menuItems: [{
                            text: "<?php echo lang('export_to_png'); ?>",
                            onclick: function() {
                                this.exportChart();
                            },
                            separator: false
                        }]
                    }
                }
            },

            xAxis: {
                min: 0,
                categories: [
                    <?php foreach ($Reports_controller->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_volumen_categories as $index => $value){ ?>
                        <?php echo "'".$value."', "; ?>
                    <?php } ?>
                    <?php foreach ($Reports_controller->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_residuos_volumen_categories as $index => $value){ ?>
                        <?php echo "'".$value."', "; ?>
                    <?php } ?>
                ],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: "<?php echo $unidad_volumen_nombre_real.' ('.$unidad_volumen.')'; ?>"
                },
                labels:{ 
                    formatter: function(){
                        return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                    } 
                },
            },
            credits: {
                enabled: false
            },
           	tooltip: {
           		headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
                pointFormatter: function(){
                    return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                    + "<td style='padding:0'><b>" 
                    + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                    + " <?php echo $unidad_volumen; ?> </b></td></tr>"
                },
           		footerFormat:"</table>",
           		shared: true,
           		useHTML: true
           	},
            plotOptions: {
           		column: {
           			pointPadding: 0.2,
           			borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        color: "#000000",
                        align: "center",
                        formatter: function(){
                            return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                        },
                        style: {
                            fontSize: "10px",
                            fontFamily: "Segoe ui, sans-serif"
                        },
                        format: "{y:,." + decimal_numbers + "f}",
                    }
           		}
           	},
            colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
            series: [
                {
                    name: "<?php echo lang('reported'); ?>",
                    data: [
                            <?php foreach($Reports_controller->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_volumen_data as $categoria_valor){ ?>
                                <?php echo $categoria_valor.", "; ?>
                            <?php } ?>
                            <?php foreach($Reports_controller->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_residuos_volumen_data as $categoria_valor){ ?>
                                <?php echo $categoria_valor.", "; ?>
                            <?php } ?>
                    ]
                },
                {
                    name: "<?php echo lang('accumulated'); ?>",
                    data: [

                        <?php foreach($Reports_controller->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_volumen_data_a as $categoria_valor){ ?>
                            <?php echo $categoria_valor.", "; ?>
                        <?php } ?>
                        <?php foreach($Reports_controller->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_residuos_volumen_data_a as $categoria_valor){ ?>
                            <?php echo $categoria_valor.", "; ?>
                        <?php } ?>
                    ]
                },
            ]
        });
        

        $("#residuo_masa").highcharts({

            chart:{
                zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
                type: "column",
                events: {load: function(event){}}
            },

            title: {
                text: "<?php echo lang('waste').' ('.$unidad_masa.')'; ?>"
            },
            subtitle: {
                text: ""
            },
        
            <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("waste").'_'.$unidad_masa.'_'.date("Y-m-d"); ?>

            exporting: {
                filename: "<?php echo $nombre_exportacion; ?>",
                buttons: {
                    contextButton: {
                        menuItems: [{
                            text: "<?php echo lang('export_to_png'); ?>",
                            onclick: function() {
                                this.exportChart();
                            },
                            separator: false
                        }]
                    }
                }
            },

            xAxis: {
                min: 0,
                categories: [
                    <?php foreach ($Reports_controller->get_datos_grafico_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_masa_categories as $index => $value){ ?>
                        <?php echo "'".$value."', "; ?>
                    <?php } ?>
                    <?php foreach ($Reports_controller->get_datos_grafico_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_residuos_masa_categories as $index => $value){ ?>
                        <?php echo "'".$value."', "; ?>
                    <?php } ?>
                ],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: "<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.')'; ?>"
                },
                labels:{ 
                    formatter: function(){
                        return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                    } 
                },
            },
            credits: {
                enabled: false
            },
           	tooltip: {
           		headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
                pointFormatter: function(){
                    return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                    + "<td style='padding:0'><b>" 
                    + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                    + " <?php echo $unidad_masa; ?> </b></td></tr>"
                },
           		footerFormat:"</table>",
           		shared: true,
           		useHTML: true
           	},
            plotOptions: {
           		column: {
           			pointPadding: 0.2,
           			borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        color: "#000000",
                        align: "center",
                        formatter: function(){
                            return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                        },
                        style: {
                            fontSize: "10px",
                            fontFamily: "Segoe ui, sans-serif"
                        },
                        format: "{y:,." + decimal_numbers + "f}",
                    }
           		}
           	},
            colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
            series: [
                {
                    name: "<?php echo lang('reported'); ?>",
                    data: [
                            <?php foreach($Reports_controller->get_datos_grafico_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_masa_data as $categoria_valor){ ?>
                                <?php echo $categoria_valor.", "; ?>
                            <?php } ?>
                            <?php foreach($Reports_controller->get_datos_grafico_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_residuos_masa_data as $categoria_valor){ ?>
                                <?php echo $categoria_valor.", "; ?>
                            <?php } ?>
                    ]
                },
                {
                    name: "<?php echo lang('accumulated'); ?>",
                    data: [

                        <?php foreach($Reports_controller->get_datos_grafico_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_masa_data_a as $categoria_valor){ ?>
                            <?php echo $categoria_valor.", "; ?>
                        <?php } ?>
                        <?php foreach($Reports_controller->get_datos_grafico_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date, $especies = true)->array_grafico_residuos_masa_data_a as $categoria_valor){ ?>
                            <?php echo $categoria_valor.", "; ?>
                        <?php } ?>
                    ]
                },
            ]
        });
       
    <?php } ?>
    // FIN RESIDUOS



    // PERMISOS
    <?php if ($puede_ver_permittings) { ?>

        <?php if(!empty(array_filter($total_cantidades_estados_evaluados_permisos))){ ?>
            

            $("#grafico_cumplimientos_totales_permisos").highcharts({
					chart: {
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: "pie",
						events: {
						   load: function() {
							   if (this.options.chart.forExport) {
								   Highcharts.each(this.series, function (series) {
									   series.update({
										   dataLabels: {
											   enabled: true,
											}
										}, false);
									});
									this.redraw();
								}
							}
						}
					},
					title: {
						text: "",
					},
					credits: {
						enabled: false
					},
					tooltip: {
						formatter: function() {
							return "<b>"+ this.point.name +"</b>: "+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +" %";
						},
					},
					plotOptions: {
						pie: {
						allowPointSelect: true,
						cursor: "pointer",
						dataLabels: {
							enabled: false,
							format: "<b>{point.name}</b>: {point.percentage:." + decimal_numbers + "f} %",
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black",
								fontSize: "9px",
								distance: -30
							},
							crop: false
						},
						showInLegend: true
						}
					},
					legend: {
						enabled: true,
						itemStyle:{
							fontSize: "9px"
						}
					},
					exporting: {

					    <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("permittings").'_'.clean(lang("total_procedures")).'_'.date("Y-m-d"); ?>
						
                        filename: "<?php echo $nombre_exportacion; ?>",
						buttons: {
							contextButton: {
								menuItems: [{
									text:  "<?php echo lang('export_to_png'); ?>",
									onclick: function() {
										this.exportChart();
									},
									separator: false
								}]
							}
						}
					},
					colors: [
					<?php foreach($total_cantidades_estados_evaluados_permisos as $estado) { ?>
						<?php echo "'".$estado["color"]."', "; ?>
					<?php } ?>
					],
					series: [{
						name: "Porcentaje",
						colorByPoint: true,
						data: [
						<?php foreach($total_cantidades_estados_evaluados_permisos as $estado) { ?>
                            {
                                name: "<?php echo $estado["nombre_estado"]; ?>",
                                <?php $y = (($estado["cantidad_categoria"] * 100) / $total_permisos_aplicables); ?>
                                y: <?php echo $y; ?>
                            },
						<?php } ?>
						]
					}]
				});


        <?php } else { ?>

            $("#grafico_cumplimientos_totales_permisos").html("<?php echo "<strong>".lang("no_information_available")."</strong>"; ?>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
        
        <?php } ?>

    <?php } ?>
    // FIN PERMISOS



    // INDICADORES - AGUA
    $("#grafico_ind_consumo_mensual_agua").highcharts({
        chart:{
            zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
            type: "column",
            events: {load: function(event){}}
        },
        title: {
            text: "<?php echo lang('monthly_water_consumption').' ('.$unidad_volumen.')'; ?>"
        },
        subtitle: {
            text: ""
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("monthly_water_consumption").'_'.$unidad_volumen.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            }
        },
        xAxis: {
            //min: 0,
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
            //crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_volumen_nombre_real.' ('.$unidad_volumen.')'; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_volumen; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            {
                <?php 
                    $data = $Reports_controller->get_data_graficos_indicadores(array(
                        "id_cliente" => $id_cliente, 
                        "id_proyecto" => $id_proyecto,
                        "id_material" => 6, // Agua
                        "id_categoria" => 15, // Agua industrial
                        "id_tipo_unidad" => 2, // Volumen
                        "flujo" => "Consumo",
                        "rango_fechas" => $rango_fechas
                    ))["array_data"];
                ?>
                name: "<?php echo lang('industrial_water'); ?>",
                data: [ 
                    <?php foreach($data as $anio => $numero_mes){ ?>
                        <?php foreach($numero_mes as $valor){ ?>
                            <?php echo $valor.", "; ?>
                        <?php } ?>
                    <?php } ?>
                ]
            },
            {
                <?php 
                    $data = $Reports_controller->get_data_graficos_indicadores(array(
                        "id_cliente" => $id_cliente, 
                        "id_proyecto" => $id_proyecto,
                        "id_material" => 6, // Agua
                        "id_categoria" => 16, // Agua potable
                        "id_tipo_unidad" => 2, // Volumen
                        "flujo" => "Consumo",
                        "rango_fechas" => $rango_fechas
                    ))["array_data"];
                ?>
                name: "<?php echo lang('drinking_water'); ?>",
                data: [
                    <?php foreach($data as $anio => $numero_mes){ ?>
                        <?php foreach($numero_mes as $valor){ ?>
                            <?php echo $valor.", "; ?>
                        <?php } ?>
                    <?php } ?>
                ]
            },
        ]
    });

    
    $('#grafico_ind_consumo_produccion_agua').highcharts({
        chart: {
            type: 'area',
            zoomType: 'x',
            panning: true,
            panKey: 'shift',
            /*scrollablePlotArea: {
                minWidth: 600
            },*/
            events: {
                load: function(){
                    if (this.options.chart.forExport) {
                        Highcharts.each(this.series, function (series) {
                            series.update({
                                dataLabels: {
                                    enabled: true,
                                }
                            }, false);
                        });
                        this.redraw();
                    }
                }
            }
        },
        title: {
            text: "<?php echo lang("consumption_by_production") . " (".$unidad_volumen.") / t ".lang("totals_produced"); ?>"
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_volumen_nombre_real.' ('.$unidad_volumen.') '. lang("by")." ".lang("ton")." (t)"; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_volumen; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("consumption_by_production").'_'.$unidad_volumen.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            },
            
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            {
                <?php 
                    $data = $Reports_controller->get_data_graficos_indicadores(array(
                        "id_cliente" => $id_cliente, 
                        "id_proyecto" => $id_proyecto,
                        "id_material" => 6, // Agua
                        "id_categoria" => 15, // Agua industrial
                        "id_tipo_unidad" => 2, // Volumen
                        "flujo" => "Consumo",
                        "rango_fechas" => $rango_fechas
                    ))["array_data_a"];
                ?>
                name: "<?php echo $array_nombre_categorias["agua_industrial"]["nombre"]; ?>",
                data: [ 
                    <?php foreach($data as $anio => $numero_mes){ ?>
                        <?php foreach($numero_mes as $valor){ ?>
                            <?php echo $valor.", "; ?>
                        <?php } ?>
                    <?php } ?>
                ],
                fillOpacity: 0
            },
            {
                <?php 
                    $data = $Reports_controller->get_data_graficos_indicadores(array(
                        "id_cliente" => $id_cliente, 
                        "id_proyecto" => $id_proyecto,
                        "id_material" => 6, // Agua
                        "id_categoria" => 16, // Agua potable
                        "id_tipo_unidad" => 2, // Volumen
                        "flujo" => "Consumo",
                        "rango_fechas" => $rango_fechas
                    ))["array_data_a"];
                ?>
                name: "<?php echo $array_nombre_categorias["agua_potable"]["nombre"]; ?>",
                data: [
                    <?php foreach($data as $anio => $numero_mes){ ?>
                        <?php foreach($numero_mes as $valor){ ?>
                            <?php echo $valor.", "; ?>
                        <?php } ?>
                    <?php } ?>
                ],
                fillOpacity: 0
            },
        ]

    });
    // FIN INDICADORES - AGUA



    // INDICADORES - ENERGÍA
    $("#grafico_ind_consumo_mensual_electricidad").highcharts({
        chart:{
            zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
            type: "column",
            events: {load: function(event){}}
        },
        title: {
            text: "<?php echo lang('monthly_consumption').' ('.$unidad_potencia.')'; ?>"
        },
        subtitle: {
            text: ""
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("monthly_consumption").'_'.$unidad_potencia.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            }
        },
        xAxis: {
            //min: 0,
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
            //crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_potencia_nombre_real.' ('.$unidad_potencia.')'; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_potencia; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            {
                <?php 
                    $data = $Reports_controller->get_data_graficos_indicadores(array(
                        "id_cliente" => $id_cliente, 
                        "id_proyecto" => $id_proyecto,
                        "id_material" => 2, // Electricidad
                        "id_categoria" => 250, // Adquisición + pérdidas de electricidad
                        "id_tipo_unidad" => 6, // Potencia
                        "flujo" => "Consumo",
                        "rango_fechas" => $rango_fechas
                    ))["array_data"];
                ?>
                name: "<?php echo $array_nombre_categorias["electricidad"]["nombre"]; ?>",
                data: [ 
                    <?php foreach($data as $anio => $numero_mes){ ?>
                        <?php foreach($numero_mes as $valor){ ?>
                            <?php echo $valor.", "; ?>
                        <?php } ?>
                    <?php } ?>
                ]
            }
        ]
    });

    $("#grafico_ind_consumo_mensual_combustible").highcharts({
        chart:{
            zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
            type: "column",
            events: {load: function(event){}}
        },
        title: {
            text: "<?php echo lang('monthly_consumption').' ('.$unidad_volumen.')'; ?>"
        },
        subtitle: {
            text: ""
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("monthly_consumption").'_'.$unidad_volumen.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            }
        },
        xAxis: {
            //min: 0,
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
            //crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_volumen_nombre_real.' ('.$unidad_volumen.')'; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_volumen; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            {
                <?php 
                    $data = $Reports_controller->get_data_graficos_indicadores(array(
                        "id_cliente" => $id_cliente, 
                        "id_proyecto" => $id_proyecto,
                        "id_material" => 35, // Maquinaria
                        "id_categoria" => 131, // Equipos Generación Eléctrica
                        "id_tipo_unidad" => 2, // Volumen
                        "flujo" => "Consumo",
                        "rango_fechas" => $rango_fechas
                    ))["array_data"];
                ?>
                name: "<?php echo $array_nombre_categorias["equipos_generacion_electrica"]["nombre"]; ?>",
                data: [ 
                    <?php foreach($data as $anio => $numero_mes){ ?>
                        <?php foreach($numero_mes as $valor){ ?>
                            <?php echo $valor.", "; ?>
                        <?php } ?>
                    <?php } ?>
                ]
            }
        ]
    });

    $("#grafico_ind_consumo_mensual_gas").highcharts({
        chart:{
            zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
            type: "column",
            events: {load: function(event){}}
        },
        title: {
            text: "<?php echo lang('monthly_consumption').' ('.$unidad_masa.')'; ?>"
        },
        subtitle: {
            text: ""
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("monthly_consumption").'_'.$unidad_masa.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            }
        },
        xAxis: {
            //min: 0,
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
            //crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.')'; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_masa; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            {
                <?php 
                    $data = $Reports_controller->get_data_graficos_indicadores(array(
                        "id_cliente" => $id_cliente, 
                        "id_proyecto" => $id_proyecto,
                        "id_material" => 14, // Combustible en base a petróleo
                        "id_categoria" => 158, // Gas licuado,
                        "id_tipo_unidad" => 1, // Masa
                        "flujo" => "Consumo",
                        "rango_fechas" => $rango_fechas
                    ))["array_data"];
                ?>
                name: "<?php echo $array_nombre_categorias["gas_licuado"]["nombre"]; ?>",
                data: [ 
                    <?php foreach($data as $anio => $numero_mes){ ?>
                        <?php foreach($numero_mes as $valor){ ?>
                            <?php echo $valor.", "; ?>
                        <?php } ?>
                    <?php } ?>
                ]
            }
        ]
    });

    $('#grafico_ind_consumo_produccion_electricidad').highcharts({
        chart: {
            type: 'area',
            zoomType: 'x',
            panning: true,
            panKey: 'shift',
            /*scrollablePlotArea: {
                minWidth: 600
            },*/
            events: {
                load: function(){
                    if (this.options.chart.forExport) {
                        Highcharts.each(this.series, function (series) {
                            series.update({
                                dataLabels: {
                                    enabled: true,
                                }
                            }, false);
                        });
                        this.redraw();
                    }
                }
            }
        },
        title: {
            text: "<?php echo lang("consumption_by_production") . " (".$unidad_potencia.") / t ".lang("totals_produced"); ?>"
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_potencia_nombre_real.' ('.$unidad_potencia.') '. lang("by")." ".lang("ton")." (t)"; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_potencia; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("consumption_by_production").'_'.$unidad_potencia.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            },
            
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            {
                <?php 
                    $data = $Reports_controller->get_data_graficos_indicadores(array(
                        "id_cliente" => $id_cliente, 
                        "id_proyecto" => $id_proyecto,
                        "id_material" => 2, // Electricidad
                        "id_categoria" => 250, // Adquisición + pérdidas de electricidad
                        "id_tipo_unidad" => 6, // Potencia
                        "flujo" => "Consumo",
                        "rango_fechas" => $rango_fechas
                    ))["array_data_a"];
                ?>
                name: "<?php echo $array_nombre_categorias["electricidad"]["nombre"]; ?>",
                data: [ 
                    <?php foreach($data as $anio => $numero_mes){ ?>
                        <?php foreach($numero_mes as $valor){ ?>
                            <?php echo $valor.", "; ?>
                        <?php } ?>
                    <?php } ?>
                ],
                fillOpacity: 0
            }
        ]

    });

    $('#grafico_ind_consumo_produccion_combustible').highcharts({
        chart: {
            type: 'area',
            zoomType: 'x',
            panning: true,
            panKey: 'shift',
            /*scrollablePlotArea: {
                minWidth: 600
            },*/
            events: {
                load: function(){
                    if (this.options.chart.forExport) {
                        Highcharts.each(this.series, function (series) {
                            series.update({
                                dataLabels: {
                                    enabled: true,
                                }
                            }, false);
                        });
                        this.redraw();
                    }
                }
            }
        },
        title: {
            text: "<?php echo lang("consumption_by_production") . " (".$unidad_volumen.") / t ".lang("totals_produced"); ?>"
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_volumen_nombre_real.' ('.$unidad_volumen.') '. lang("by")." ".lang("ton")." (t)"; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_volumen; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("consumption_by_production").'_'.$unidad_volumen.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            },
            
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            {
                <?php 
                    $data = $Reports_controller->get_data_graficos_indicadores(array(
                        "id_cliente" => $id_cliente, 
                        "id_proyecto" => $id_proyecto,
                        "id_material" => 35, // Maquinaria
                        "id_categoria" => 131, // Equipos Generación Eléctrica
                        "id_tipo_unidad" => 2, // Volumen
                        "flujo" => "Consumo",
                        "rango_fechas" => $rango_fechas
                    ))["array_data_a"];
                ?>
                name: "<?php echo $array_nombre_categorias["equipos_generacion_electrica"]["nombre"]; ?>",
                data: [ 
                    <?php foreach($data as $anio => $numero_mes){ ?>
                        <?php foreach($numero_mes as $valor){ ?>
                            <?php echo $valor.", "; ?>
                        <?php } ?>
                    <?php } ?>
                ],
                fillOpacity: 0
            }
        ]

    });

    $('#grafico_ind_consumo_produccion_gas').highcharts({
        chart: {
            type: 'area',
            zoomType: 'x',
            panning: true,
            panKey: 'shift',
            /*scrollablePlotArea: {
                minWidth: 600
            },*/
            events: {
                load: function(){
                    if (this.options.chart.forExport) {
                        Highcharts.each(this.series, function (series) {
                            series.update({
                                dataLabels: {
                                    enabled: true,
                                }
                            }, false);
                        });
                        this.redraw();
                    }
                }
            }
        },
        title: {
            text: "<?php echo lang("consumption_by_production") . " (".$unidad_masa.") / t ".lang("totals_produced"); ?>"
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.') '. lang("by")." ".lang("ton")." (t)"; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_masa; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("consumption_by_production").'_'.$unidad_masa.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            },
            
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            {
                <?php 
                    $data = $Reports_controller->get_data_graficos_indicadores(array(
                        "id_cliente" => $id_cliente, 
                        "id_proyecto" => $id_proyecto,
                        "id_material" => 14, // Combustible en base a petróleo
                        "id_categoria" => 158, // Gas licuado
                        "id_tipo_unidad" => 1, // Masa
                        "flujo" => "Consumo",
                        "rango_fechas" => $rango_fechas
                    ))["array_data_a"];
                ?>
                name: "<?php echo $array_nombre_categorias["gas_licuado"]["nombre"]; ?>",
                data: [ 
                    <?php foreach($data as $anio => $numero_mes){ ?>
                        <?php foreach($numero_mes as $valor){ ?>
                            <?php echo $valor.", "; ?>
                        <?php } ?>
                    <?php } ?>
                ],
                fillOpacity: 0
            }
        ]

    });
    // FIN INDICADORES - ENERGÍA




    // INDICADORES - PRODUCTOS FITOSANITARIOS
    $("#grafico_ind_consumo_mensual_pf").highcharts({
        chart:{
            zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
            type: "column",
            events: {load: function(event){}}
        },
        title: {
            text: "<?php echo lang('monthly_consumption').' ('.$unidad_volumen.')'; ?>"
        },
        subtitle: {
            text: ""
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("monthly_consumption").'_'.$unidad_volumen.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            }
        },
        xAxis: {
            //min: 0,
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
            //crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_volumen_nombre_real.' ('.$unidad_volumen.')'; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_volumen; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        //colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            <?php foreach($array_cat_prod_fito as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 65, // Productos fitosanitarios
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 2, // Volumen
                            "flujo" => "Consumo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ]
                },
            <?php } ?>
        ]
    });

    $('#grafico_ind_consumo_produccion_pf').highcharts({
        chart: {
            type: 'area',
            zoomType: 'x',
            panning: true,
            panKey: 'shift',
            /*scrollablePlotArea: {
                minWidth: 600
            },*/
            events: {
                load: function(){
                    if (this.options.chart.forExport) {
                        Highcharts.each(this.series, function (series) {
                            series.update({
                                dataLabels: {
                                    enabled: true,
                                }
                            }, false);
                        });
                        this.redraw();
                    }
                }
            }
        },
        title: {
            text: "<?php echo lang("consumption_by_production") . " (".$unidad_volumen.") / t ".lang("totals_produced"); ?>"
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_volumen_nombre_real.' ('.$unidad_volumen.') '. lang("by")." ".lang("ton")." (t)"; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_volumen; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("consumption_by_production").'_'.$unidad_volumen.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            },
            
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        //colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            <?php foreach($array_cat_prod_fito as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 65, // Productos fitosanitarios
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 2, // Volumen
                            "flujo" => "Consumo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data_a"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ],
                    fillOpacity: 0
                },
                
            <?php } ?>
        ]

    });
    // FIN INDICADORES - PRODUCTOS FITOSANITARIOS




    // INDICADORES - REFRIGERANTES
    $("#grafico_ind_consumo_mensual_ref").highcharts({
        chart:{
            zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
            type: "column",
            events: {load: function(event){}}
        },
        title: {
            text: "<?php echo lang('monthly_consumption').' ('.$unidad_masa.')'; ?>"
        },
        subtitle: {
            text: ""
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("monthly_consumption").'_'.$unidad_masa.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            }
        },
        xAxis: {
            //min: 0,
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
            //crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.')'; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_masa; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        //colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            <?php foreach($array_cat_prod_ref as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 40, // Refrigerante
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 1, // Masa
                            "flujo" => "Consumo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ]
                },
            <?php } ?>
        ]
    });


    $('#grafico_ind_consumo_produccion_ref').highcharts({
        chart: {
            type: 'area',
            zoomType: 'x',
            panning: true,
            panKey: 'shift',
            /*scrollablePlotArea: {
                minWidth: 600
            },*/
            events: {
                load: function(){
                    if (this.options.chart.forExport) {
                        Highcharts.each(this.series, function (series) {
                            series.update({
                                dataLabels: {
                                    enabled: true,
                                }
                            }, false);
                        });
                        this.redraw();
                    }
                }
            }
        },
        title: {
            text: "<?php echo lang("consumption_by_production") . " (".$unidad_masa.") / t ".lang("totals_produced"); ?>"
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.') '. lang("by")." ".lang("ton")." (t)"; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_masa; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("consumption_by_production").'_'.$unidad_masa.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            },
            
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        //colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            <?php foreach($array_cat_prod_ref as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 40, // Refrigerante
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 1, // Masa
                            "flujo" => "Consumo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data_a"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ],
                    fillOpacity: 0
                },
                
            <?php } ?>
        ]

    });
    // FIN INDICADORES - REFRIGERANTES




    // INDICADORES - RESIDUOS NO PELIGROSOS
    $("#grafico_ind_residuo_mensual_nhw").highcharts({
        chart:{
            zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
            type: "column",
            events: {load: function(event){}}
        },
        title: {
            text: "<?php echo lang('monthly_waste_generation').' ('.$unidad_masa.')'; ?>"
        },
        subtitle: {
            text: ""
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("monthly_waste").'_'.$unidad_masa.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            }
        },
        xAxis: {
            //min: 0,
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
            //crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.')'; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_masa; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        //colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            <?php foreach($array_cat_prod_rsd as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 29, // Residuos sólidos domiciliarios
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 1, // Masa
                            "flujo" => "Residuo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ]
                },
            <?php } ?>
            <?php foreach($array_cat_prod_rinp as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 30, // Residuos Industriales no Peligrosos
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 1, // Masa
                            "flujo" => "Residuo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ]
                },
            <?php } ?>
        ]
    });

    $('#grafico_ind_residuo_produccion_nhw').highcharts({
        chart: {
            type: 'area',
            zoomType: 'x',
            panning: true,
            panKey: 'shift',
            /*scrollablePlotArea: {
                minWidth: 600
            },*/
            events: {
                load: function(){
                    if (this.options.chart.forExport) {
                        Highcharts.each(this.series, function (series) {
                            series.update({
                                dataLabels: {
                                    enabled: true,
                                }
                            }, false);
                        });
                        this.redraw();
                    }
                }
            }
        },
        title: {
            text: "<?php echo lang("generation_of_waste_by_production") . " (".$unidad_masa.") / t ".lang("totals_produced"); ?>"
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.') '. lang("by")." ".lang("ton")." (t)"; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_masa; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("generation_of_waste_by_production").'_'.$unidad_masa.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            },
            
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        //colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            <?php foreach($array_cat_prod_rsd as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 29, // Residuos sólidos domiciliarios
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 1, // Masa
                            "flujo" => "Residuo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data_a"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ],
                    fillOpacity: 0
                },
                
            <?php } ?>
            <?php foreach($array_cat_prod_rinp as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 30, // Residuos Industriales no Peligrosos
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 1, // Masa
                            "flujo" => "Residuo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data_a"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ],
                    fillOpacity: 0
                },
                
            <?php } ?>
        ]

    });
    // FIN INDICADORES - RESIDUOS NO PELIGROSOS




    // INDICADORES - RESIDUOS PELIGROSOS
    // SÓLIDOS
    $("#grafico_ind_residuo_mensual_hw").highcharts({
        chart:{
            zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
            type: "column",
            events: {load: function(event){}}
        },
        title: {
            text: "<?php echo lang('monthly_waste_generation').' ('.$unidad_masa.')'; ?>"
        },
        subtitle: {
            text: ""
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("monthly_waste").'_'.$unidad_masa.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            }
        },
        xAxis: {
            //min: 0,
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
            //crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.')'; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_masa; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        //colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            <?php foreach($array_cat_prod_rip_masa as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 33, // Residuos industriales peligrosos
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 1, // Masa
                            "flujo" => "Residuo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ]
                },
            <?php } ?>
        ]
    });

    $('#grafico_ind_residuo_produccion_hw').highcharts({
        chart: {
            type: 'area',
            zoomType: 'x',
            panning: true,
            panKey: 'shift',
            /*scrollablePlotArea: {
                minWidth: 600
            },*/
            events: {
                load: function(){
                    if (this.options.chart.forExport) {
                        Highcharts.each(this.series, function (series) {
                            series.update({
                                dataLabels: {
                                    enabled: true,
                                }
                            }, false);
                        });
                        this.redraw();
                    }
                }
            }
        },
        title: {
            text: "<?php echo lang("generation_of_waste_by_production") . " (".$unidad_masa.") / t ".lang("totals_produced"); ?>"
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.') '. lang("by")." ".lang("ton")." (t)"; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_masa; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("generation_of_waste_by_production").'_'.$unidad_masa.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            },
            
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        //colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            <?php foreach($array_cat_prod_rip_masa as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 33, // Residuos industriales peligrosos
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 1, // Masa
                            "flujo" => "Residuo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data_a"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ],
                    fillOpacity: 0
                },
                
            <?php } ?>
        ]

    });

    // LÍQUIDOS
    $("#grafico_ind_residuo_mensual_lhw").highcharts({
        chart:{
            zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},
            type: "column",
            events: {load: function(event){}}
        },
        title: {
            text: "<?php echo lang('monthly_waste_generation').' ('.$unidad_volumen.')'; ?>"
        },
        subtitle: {
            text: ""
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("monthly_waste").'_'.$unidad_volumen.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            }
        },
        xAxis: {
            //min: 0,
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
            //crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_volumen_nombre_real.' ('.$unidad_volumen.')'; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_volumen; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        //colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            <?php foreach($array_cat_prod_rip_volumen as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 33, // Residuos industriales peligrosos
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 2, // Volumen
                            "flujo" => "Residuo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ]
                },
            <?php } ?>
            <?php foreach($array_cat_prod_rli_volumen as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 31, // Residuos Líquidos Industriales
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 2, // Volumen
                            "flujo" => "Residuo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ]
                },
            <?php } ?>
        ]
    });

    $('#grafico_ind_residuo_produccion_lhw').highcharts({
        chart: {
            type: 'area',
            zoomType: 'x',
            panning: true,
            panKey: 'shift',
            /*scrollablePlotArea: {
                minWidth: 600
            },*/
            events: {
                load: function(){
                    if (this.options.chart.forExport) {
                        Highcharts.each(this.series, function (series) {
                            series.update({
                                dataLabels: {
                                    enabled: true,
                                }
                            }, false);
                        });
                        this.redraw();
                    }
                }
            }
        },
        title: {
            text: "<?php echo lang("generation_of_waste_by_production") . " (".$unidad_masa.") / t ".lang("totals_produced"); ?>"
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: [
            <?php foreach($rango_fechas as $anio => $meses) { ?>
                <?php foreach($meses as $numero_mes => $nombre_mes) { ?>
                    '<?php echo $nombre_mes . " - " . $anio; ?>',
                <?php } ?>
            <?php } ?>
            ],
        },
        yAxis: {
            min: 0,
            title: {
                text: "<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.') '. lang("by")." ".lang("ton")." (t)"; ?>"
            },
            labels:{
                formatter: function(){
                    return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
                }
            },
        },
        tooltip: {
            headerFormat: "<span style='font-size:10px'>{point.key}</span><table>",
            pointFormatter: function(){
                return "<tr><td style='color:" + this.series.color + "; padding:0'>" + this.series.name + ":</td>" 
                + "<td style='padding:0'><b>" 
                + (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))
                + " <?php echo $unidad_masa; ?> </b></td></tr>"
            },
            footerFormat:"</table>",
            shared: true,
            useHTML: true
        },

        <?php $nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("generation_of_waste_by_production").'_'.$unidad_masa.'_'.date("Y-m-d"); ?>

        exporting: {
            filename: "<?php echo $nombre_exportacion; ?>",
            buttons: {
                contextButton: {
                    menuItems: [{
                        text: "<?php echo lang('export_to_png'); ?>",
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }]
                }
            },
            
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: "#000000",
                    align: "center",
                    formatter: function(){
                        return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
                    },
                    style: {
                        fontSize: "10px",
                        fontFamily: "Segoe ui, sans-serif"
                    },
                    format: "{y:,." + decimal_numbers + "f}",
                }
            }
        },
        //colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],
        series: [
            <?php foreach($array_cat_prod_rip_volumen as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 33, // Residuos industriales peligrosos
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 2, // Volumen
                            "flujo" => "Residuo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data_a"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ],
                    fillOpacity: 0
                },
                
            <?php } ?>
            <?php foreach($array_cat_prod_rli_volumen as $id_cat => $nombre_cat){ ?>
                <?php 
                    $row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_cat, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                    if($row_alias->alias){
                        $nombre_categoria = $row_alias->alias;
                    }else{
                        $row_categoria = $this->Categories_model->get_one_where(array('id' => $id_cat, 'deleted' => 0));
                        $nombre_categoria = $row_categoria->nombre;
                    }
                ?>
                {
                    <?php 
                        $data = $Reports_controller->get_data_graficos_indicadores(array(
                            "id_cliente" => $id_cliente, 
                            "id_proyecto" => $id_proyecto,
                            "id_material" => 31, // Residuos Líquidos Industriales
                            "id_categoria" => $id_cat,
                            "id_tipo_unidad" => 2, // Volumen
                            "flujo" => "Residuo",
                            "rango_fechas" => $rango_fechas
                        ))["array_data"];
                    ?>
                    name: "<?php echo $nombre_categoria; ?>",
                    data: [ 
                        <?php foreach($data as $anio => $numero_mes){ ?>
                            <?php foreach($numero_mes as $valor){ ?>
                                <?php echo $valor.", "; ?>
                            <?php } ?>
                        <?php } ?>
                    ],
                    fillOpacity: 0
                },
            <?php } ?>
        ]

    });

    // FIN INDICADORES - RESIDUOS PELIGROSOS

</script>