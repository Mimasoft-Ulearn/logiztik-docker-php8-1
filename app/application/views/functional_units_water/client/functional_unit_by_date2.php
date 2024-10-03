<div class="col-sm-3 col-lg-2 hide">
<ul id="ul_menu_unidades_funcionales" class="nav nav-tabs vertical" role="tablist">
	<li class="active">
		<a data-toggle="tab" data-id_unidad_funcional="<?php echo $unidad_funcional->id; ?>" href="#<?php echo $unidad_funcional->id; ?>_unidad_funcional"></a>
	</li>
</ul>
</div>

<div role="tabpanel" class="tab-pane fade active in" id="graficos_procesos" style="min-height: 200px;">
	<div class="tab-content">

		<?php
		$uf_huella = array();
		foreach($calculos as $calculo){

			// $calculation = $this->Calculation_model->get_one($calculo->id_calculo);

			//var_dump($calculo);
			//exit();

			$total_elemento = 0;
			$datos_decoded = json_decode($calculo->datos, true);

			// VALORES Y UNIDAD FINAL
			$ides_campo_unidad = json_decode($calculo->id_campo_unidad, true);
			$array_unidades = array();
			$array_id_unidades = array();
			$array_id_tipo_unidades = array();
			
			foreach($ides_campo_unidad as $id_campo_unidad){
				
				if($id_campo_unidad == 0){
					$id_formulario = $calculo->id_formulario;
					$json_unidad_form = json_decode($calculo->formulario_unidad, true);
					
					$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
					$id_unidad = $json_unidad_form["unidad_id"];
					
					$array_unidades[] = $unidades[$id_unidad];;
					$array_id_unidades[] = $id_unidad;
					$array_id_tipo_unidades[] = $id_tipo_unidad;
				}else{
					$info_campo = json_decode($campos_unidad[$id_campo_unidad], true);
					$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
					$id_unidad = $info_campo[0]["id_unidad"];
					
					$array_unidades[] = $unidades[$id_unidad];;
					$array_id_unidades[] = $id_unidad;
					$array_id_tipo_unidades[] = $id_tipo_unidad;
				}
			}

			if(count($array_id_unidades) == 1){
				$id_unidad = $array_id_unidades[0];
			}elseif(count($array_id_unidades) == 2){
				if($array_id_unidades[0] == 18 && $array_id_unidades[1] != 18){
					$id_unidad = $array_id_unidades[1];
				}elseif($array_id_unidades[0] != 18 && $array_id_unidades[1] == 18){
					$id_unidad = $array_id_unidades[0];
				}elseif(in_array(9, $array_id_unidades) && in_array(1, $array_id_unidades)){
					$id_unidad = 5;
				}elseif(in_array(9, $array_id_unidades) && in_array(2, $array_id_unidades)){
					$id_unidad = 6;
				}elseif(in_array(3, $array_id_unidades) && in_array(14, $array_id_unidades)){// m3 x hectarea
					$id_unidad = 3;
				}else{
					$id_unidad = $array_id_unidades[0];
				}
			}elseif(count($array_id_unidades) == 3){
				
				if(in_array(18, $array_id_unidades) && in_array(9, $array_id_unidades) && in_array(1, $array_id_unidades)){
					$id_unidad = 5;
				}elseif(in_array(18, $array_id_unidades) && in_array(9, $array_id_unidades) && in_array(2, $array_id_unidades)){
					$id_unidad = 6;
				}else{
					
				}
			}else{
				
			}
			
			// MULTIPLICACIÓN DE MULTIPLES VALORES
			$mult = 1;
			foreach($ides_campo_unidad as $id_campo_unidad){
				if($id_campo_unidad == 0){
					$mult *= $calculo->valor;
				}else{
					$mult *= $datos_decoded[$id_campo_unidad];
				}
			}

			$id_campo_sp = $calculo->id_campo_sp;
			$id_campo_pu = $calculo->id_campo_pu;
			$id_campo_fc = $calculo->id_campo_fc;

			$criterio_sp = $calculo->criterio_sp;
			$tipo_asignacion_sp = $calculo->tipo_asignacion_sp;
			$sp_destino = $calculo->sp_destino;
			$porcentajes_sp = $calculo->porcentajes_sp;

			$criterio_pu = $calculo->criterio_pu;
			$tipo_asignacion_pu = $calculo->tipo_asignacion_pu;
			$pu_destino = $calculo->pu_destino;
			$porcentajes_pu = $calculo->porcentajes_pu;

			if(!$id_campo_sp && !$id_campo_pu && $tipo_asignacion_sp == "Total"){

				foreach($huellas as $huella){

					$valor_factor = 0;
					if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
					// echo "<br> <strong> ISSET 1 </strong> <br>";
						$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
					}

					$uf_huella[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);

					$valor_uf_mes = $uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
					$uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_mes"] += (((float)($mult * $valor_factor))/$valor_uf_mes) * $array_transformaciones[$huella->id];
					//$uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_mes"] += (float)($mult * $valor_factor);
				
					/* if($this->login_user->id == 4){
						echo "<b>TIPO ASIGNACIÓN SP </b>: Total"."<b> | ID CÁLCULO: </b>".$calculo->id_calculo."<b> | CÁLCULO: </b> ".$calculation->etiqueta."<b> | ID METODOLOGIA: </b>".$calculo->id_metodologia."<b> | ID UF: </b>".$sp_uf[$sp_destino]."<b> | ID CATEGORÍA: </b>".$calculo->id_categoria."<b> | ID SP DESTINO: </b>".$sp_destino."<b> | HUELLA: </b>".$huella->nombre."<b> | MES/AÑO: </b>".lang("short_".strtolower($calculo->month))."-".$calculo->year."<b> | VALOR MES: </b>".(((float)($mult * $valor_factor))/$valor_uf_mes) * $array_transformaciones[$huella->id]."<b> | VALOR ELEMENTO: </b>".$calculo->valor."<b> | VALOR FC USADO: </b>".$valor_factor;
						echo "<br><br>";
					} */
					
				}
			}

			if($id_campo_sp && !$id_campo_pu){

				if($id_campo_sp == "tipo_tratamiento"){
					$valor_campo_sp = $tipo_tratamiento[$datos_decoded[$id_campo_sp]];
				}elseif($id_campo_sp == "type_of_origin_matter"){
					$valor_campo_sp = lang($type_of_origin_matter[$datos_decoded[$id_campo_sp]]);
				}elseif($id_campo_sp == "type_of_origin"){
					$valor_campo_sp = lang($type_of_origin[$datos_decoded[$id_campo_sp]]);
				}elseif($id_campo_sp == "default_type"){
					$valor_campo_sp = lang($default_type[$datos_decoded[$id_campo_sp]]);
				}elseif($id_campo_sp == "month"){
					$valor_campo_sp = number_to_month($datos_decoded[$id_campo_sp]);
				}else{
					$valor_campo_sp = $datos_decoded[$id_campo_sp];
				}
					
				if($tipo_asignacion_sp == "Total" && $criterio_sp == $valor_campo_sp){
					
					foreach($huellas as $huella){

						$valor_factor = 0;
						if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
							// echo "<br> <strong> ISSET 2 </strong> <br>";
							$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
						}

						$uf_huella[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);

						$valor_uf_mes = $uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
						$uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_mes"] += (((float)($mult * $valor_factor))/$valor_uf_mes) * $array_transformaciones[$huella->id];
						//$uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_mes"] += (float)($mult * $valor_factor);
					
						/* if($this->login_user->id == 4){
							echo "<b>TIPO ASIGNACIÓN SP </b>: Total"."<b> | ID CÁLCULO: </b>".$calculo->id_calculo."<b> | CÁLCULO: </b> ".$calculation->etiqueta."<b> | ID METODOLOGIA: </b>".$calculo->id_metodologia."<b> | ID UF: </b>".$sp_uf[$sp_destino]."<b> | ID CATEGORÍA: </b>".$calculo->id_categoria."<b> | ID SP DESTINO: </b>".$sp_destino."<b> | HUELLA: </b>".$huella->nombre."<b> | MES/AÑO: </b>".lang("short_".strtolower($calculo->month))."-".$calculo->year."<b> | VALOR MES: </b>".(((float)($mult * $valor_factor))/$valor_uf_mes) * $array_transformaciones[$huella->id]."<b> | VALOR ELEMENTO: </b>".$calculo->valor."<b> | VALOR FC USADO: </b>".$valor_factor;
							echo "<br><br>";
						} */

					}
					
				}else if($tipo_asignacion_sp == "Porcentual" && $criterio_sp == $valor_campo_sp){
					
					foreach($huellas as $huella){

						$valor_factor = 0;
						if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
							// echo "<br> <strong> ISSET 3 </strong> <br>";
							$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
						}

						$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
						foreach($porcentajes_sp_decoded as $id_sp => $porcentaje_sp){
							if($porcentaje_sp != 0){
								$porcentaje_sp = ($porcentaje_sp/100);
							}

							$uf_huella[$sp_uf[$id_sp]][$huella->id][] = (float)(($mult * $valor_factor) * $porcentaje_sp);

							$valor_uf_mes = $uf_huella_mes[$sp_uf[$id_sp]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
							$uf_huella_mes[$sp_uf[$id_sp]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_mes"] += (((float)(($mult * $valor_factor) * $porcentaje_sp))/$valor_uf_mes) * $array_transformaciones[$huella->id];
							//$uf_huella_mes[$sp_uf[$id_sp]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_mes"] += (float)($mult * $valor_factor);
						
							$porcentajes_sp_decoded_print = "ID SP: ".$id_sp." - % SP: ".$porcentaje_sp;

							/* if($this->login_user->id == 4){
								echo "<b>TIPO ASIGNACIÓN SP </b>: PORCENTUAL"."<b> | % ASIGNACIONES: </b>".$porcentajes_sp_decoded_print."<b> | ID CÁLCULO: </b>".$calculo->id_calculo."<b> | CÁLCULO: </b> ".$calculation->etiqueta."<b> | ID METODOLOGIA: </b>".$calculo->id_metodologia."<b> | ID UF: </b>".$sp_uf[$id_sp]."<b> | ID CATEGORÍA: </b>".$calculo->id_categoria."<b> | ID SP DESTINO: </b>".$id_sp."<b> | HUELLA: </b>".$huella->nombre."<b> | MES/AÑO: </b>".lang("short_".strtolower($calculo->month))."-".$calculo->year."<b> | VALOR MES: </b>".(((float)($mult * $valor_factor) * $porcentaje_sp)/$valor_uf_mes) * $array_transformaciones[$huella->id]."<b> | VALOR ELEMENTO: </b>".$calculo->valor."<b> | VALOR FC USADO: </b>".$valor_factor;
								echo "<br><br>";
							} */

						}
					}
					
				}
			}
			
			if(!$id_campo_sp && $id_campo_pu){
				
				if($id_campo_pu == "tipo_tratamiento"){
					$valor_campo_pu = $tipo_tratamiento[$datos_decoded[$id_campo_pu]];
				}elseif($id_campo_pu == "type_of_origin_matter"){
					$valor_campo_pu = lang($type_of_origin_matter[$datos_decoded[$id_campo_pu]]);
				}elseif($id_campo_pu == "type_of_origin"){
					$valor_campo_pu = lang($type_of_origin[$datos_decoded[$id_campo_pu]]);
				}elseif($id_campo_pu == "default_type"){
					$valor_campo_pu = lang($default_type[$datos_decoded[$id_campo_pu]]);
				}else{
					$valor_campo_pu = $datos_decoded[$id_campo_pu];
				}
				
				if($criterio_pu == $valor_campo_pu){
					
					foreach($huellas as $huella){

						$valor_factor = 0;
						if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
							// echo "<br> <strong> ISSET 4 </strong> <br>";
							$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
						}

						$uf_huella[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);
						
						//if(isset($array_meses[lang("short_".strtolower($calculo->month))."-".$calculo->year])){
							$valor_uf_mes = $uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
							$uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_mes"] += (((float)($mult * $valor_factor))/$valor_uf_mes) * $array_transformaciones[$huella->id];
							//$uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_mes"] += (float)($mult * $valor_factor);
						//}

						/* if($this->login_user->id == 4){
							echo "<b>TIPO ASIGNACIÓN SP </b>: Total"."<b> | ID CÁLCULO: </b>".$calculo->id_calculo."<b> | CÁLCULO: </b> ".$calculation->etiqueta."<b> | ID METODOLOGIA: </b>".$calculo->id_metodologia."<b> | ID UF: </b>".$sp_uf[$sp_destino]."<b> | ID CATEGORÍA: </b>".$calculo->id_categoria."<b> | ID SP DESTINO: </b>".$sp_destino."<b> | HUELLA: </b>".$huella->nombre."<b> | MES/AÑO: </b>".lang("short_".strtolower($calculo->month))."-".$calculo->year."<b> | VALOR MES: </b>".(((float)($mult * $valor_factor))/$valor_uf_mes) * $array_transformaciones[$huella->id]."<b> | VALOR ELEMENTO: </b>".$calculo->valor."<b> | VALOR FC USADO: </b>".$valor_factor;
							echo "<br><br>";
						} */

					}
					
				}
			}
			
			
			if($id_campo_sp && $id_campo_pu){
				
				if($id_campo_sp == "tipo_tratamiento"){
					$valor_campo_sp = $tipo_tratamiento[$datos_decoded[$id_campo_sp]];
				}elseif($id_campo_sp == "type_of_origin_matter"){
					$valor_campo_sp = lang($type_of_origin_matter[$datos_decoded[$id_campo_sp]]);
				}elseif($id_campo_sp == "type_of_origin"){
					$valor_campo_sp = lang($type_of_origin[$datos_decoded[$id_campo_sp]]);
				}elseif($id_campo_sp == "default_type"){
					$valor_campo_sp = lang($default_type[$datos_decoded[$id_campo_sp]]);
				}elseif($id_campo_sp == "month"){
					$valor_campo_sp = number_to_month($datos_decoded[$id_campo_sp]);
				}else{
					$valor_campo_sp = $datos_decoded[$id_campo_sp];
				}

				if($id_campo_pu == "tipo_tratamiento"){
					$valor_campo_pu = $tipo_tratamiento[$datos_decoded[$id_campo_pu]];
				}elseif($id_campo_pu == "type_of_origin_matter"){
					$valor_campo_pu = lang($type_of_origin_matter[$datos_decoded[$id_campo_pu]]);
				}elseif($id_campo_pu == "type_of_origin"){
					$valor_campo_pu = lang($type_of_origin[$datos_decoded[$id_campo_pu]]);
				}elseif($id_campo_pu == "default_type"){
					$valor_campo_pu = lang($default_type[$datos_decoded[$id_campo_pu]]);
				}else{
					$valor_campo_pu = $datos_decoded[$id_campo_pu];
				}
				
				if($tipo_asignacion_sp == "Total"){
					
					if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
						
						foreach($huellas as $huella){

							$valor_factor = 0;
							if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
								// echo "<br> <strong> ISSET 5 </strong> <br>";
								$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
							}

							$uf_huella[$sp_uf[$sp_destino]][$huella->id][] = (float)($mult * $valor_factor);

							$valor_uf_mes = $uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
							$uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_mes"] += (((float)($mult * $valor_factor))/$valor_uf_mes) * $array_transformaciones[$huella->id];
							//$uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_mes"] += (float)($mult * $valor_factor);
						
							/* if($this->login_user->id == 4){
								echo "<b>TIPO ASIGNACIÓN SP </b>: Total"."<b> | ID CÁLCULO: </b>".$calculo->id_calculo."<b> | CÁLCULO: </b> ".$calculation->etiqueta."<b> | ID METODOLOGIA: </b>".$calculo->id_metodologia."<b> | ID UF: </b>".$sp_uf[$sp_destino]."<b> | ID CATEGORÍA: </b>".$calculo->id_categoria."<b> | ID SP DESTINO: </b>".$sp_destino."<b> | HUELLA: </b>".$huella->nombre."<b> | MES/AÑO: </b>".lang("short_".strtolower($calculo->month))."-".$calculo->year."<b> | VALOR MES: </b>".(((float)($mult * $valor_factor))/$valor_uf_mes) * $array_transformaciones[$huella->id]."<b> | VALOR ELEMENTO: </b>".$calculo->valor."<b> | VALOR FC USADO: </b>".$valor_factor;
								echo "<br><br>";
							} */

						}
					}
					
				}else if($tipo_asignacion_sp == "Porcentual"){

					if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
						
						foreach($huellas as $huella){

							$valor_factor = 0;
							if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
								// echo "<br> <strong> ISSET 6 </strong> <br>";
								$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
							}

							$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
							foreach($porcentajes_sp_decoded as $id_sp => $porcentaje_sp){
								if($porcentaje_sp != 0){
									$porcentaje_sp = ($porcentaje_sp/100);
								}

								$uf_huella[$sp_uf[$id_sp]][$huella->id][] = (float)(($mult * $valor_factor) * $porcentaje_sp);

								$valor_uf_mes = $uf_huella_mes[$sp_uf[$id_sp]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
								$uf_huella_mes[$sp_uf[$id_sp]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_mes"] += (((float)(($mult * $valor_factor) * $porcentaje_sp))/$valor_uf_mes) * $array_transformaciones[$huella->id];
								//$uf_huella_mes[$sp_uf[$id_sp]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_mes"] += (float)($mult * $valor_factor);
							
								
								$porcentajes_sp_decoded_print = "ID SP: ".$id_sp." - % SP: ".$porcentaje_sp;
								/* if($this->login_user->id == 4){
									echo "<b>TIPO ASIGNACIÓN SP </b>: PORCENTUAL"."<b> | % ASIGNACIONES: </b>".$porcentajes_sp_decoded_print."<b> | ID CÁLCULO: </b>".$calculo->id_calculo."<b> | CÁLCULO: </b> ".$calculation->etiqueta."<b> | ID METODOLOGIA: </b>".$calculo->id_metodologia."<b> | ID UF: </b>".$sp_uf[$id_sp]."<b> | ID CATEGORÍA: </b>".$calculo->id_categoria."<b> | ID SP DESTINO: </b>".$id_sp."<b> | HUELLA: </b>".$huella->nombre."<b> | MES/AÑO: </b>".lang("short_".strtolower($calculo->month))."-".$calculo->year."<b> | VALOR MES: </b>".(((float)($mult * $valor_factor) * $porcentaje_sp)/$valor_uf_mes) * $array_transformaciones[$huella->id]."<b> | VALOR ELEMENTO: </b>".$calculo->valor."<b> | VALOR FC USADO: </b>".$valor_factor;
									echo "<br><br>";
								} */

							}
						}

					}
					
				}
			}

		}
		

		$array_impactos_chart_data = array();
		foreach($uf_huella_mes as $id_uf => $huella_mes){
			foreach($huella_mes as $id_huella => $mes_valor){
				$array_mes_valor = array();
				foreach($mes_valor as $mes => $mes_valor){
					$array_mes_valor[] = $mes_valor['valor_mes'];
				}
				$array_impactos_chart_data[$id_uf][] = array(
					"name" => $nombre_huellas[$id_huella], 
					"data" => $array_mes_valor
				);
			}
		}
		?>
	
		<div id="<?php echo $unidad_funcional->id; ?>_unidad_funcional" class="">
			<div class="col-md-12 p0">
				<div class="panel">

					<div class="page-title clearfix">
						<h1><?php echo $unidad_funcional->nombre; ?></h1>
						<a href="#" class="btn btn-danger pull-right" id="functional_unit_pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a>
					</div>
					<div class="panel-body p0">

						<div class="panel panel-default">
							<div class="page-title clearfix panel-success">
								<h1>Impactos mensuales</h1>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12 text-center">
										<div id="impactos_uf_<?php echo $unidad_funcional->id?>" style="" class="chart">
											<div style="padding:20px;"><div class="circle-loader"></div></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="page-title clearfix panel-success">
								<h1>Impactos mensuales</h1>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12 text-center">
										<div id="proporcion_uf_<?php echo $unidad_funcional->id; ?>" style="" class="chart">
											<div style="padding:20px;"><div class="circle-loader"></div></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="page-title clearfix panel-success">
								<h1><?php echo lang("environmental_impacts_by") . ' ' . $unidad_funcional->unidad. ' ' . lang("of") . ' ' . $unidad_funcional->nombre; ?></h1>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12 text-center">
									<?php
									$id_proyecto = $proyecto->id;
									$nombre_uf = $unidad_funcional->nombre;
									$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
									$valor_uf = get_functional_unit_value($client_info->id, $proyecto->id, $unidad_funcional->id, $start_date, $end_date);

									$array_export_pdf = array(); // VARIABLE PARA RECOLECTAR DATOS DE SECCIÓN "IMPACTOS AMBIENTALES POR ..." Y EXPORTARLOS A PDF

									$html = '';
									foreach($huellas as $huella){
										$valor = (string)((array_sum($uf_huella[$unidad_funcional->id][$huella->id])/$valor_uf) * $array_transformaciones[$huella->id]);
										$icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");
										$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
										$html .= '<div class="text-center p15"><img src="'.$icono.'" alt="..." height="50" width="50" class="mCS_img_loaded"></div>';
										
										//$html .= '<div class="text-center p15">'.to_number_project_format((string)array_sum($uf_huella[$unidad_funcional->id][$huella->id]), $id_proyecto).'</div>';
										$html .= '<div class="text-center p15">'.to_number_project_format($valor, $id_proyecto).'</div>';
										$html .= '<div class="pt10 pb10 b-b"> '.$huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
										$html .= '</div>';



										// Carga de valores y datos de la huella para exportar a pdf
										$array_export_pdf[$unidad_funcional->id]['huellas'][$huella->id]['icono'] = $huella->icono;
										$array_export_pdf[$unidad_funcional->id]['huellas'][$huella->id]['valor'] = to_number_project_format($valor, $id_proyecto);
										$array_export_pdf[$unidad_funcional->id]['huellas'][$huella->id]['titulo'] = $huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.')';

										// PARA CALCULO DE HUELLAS FIJAS
										if($huella->id == 30){
											$total_huella_ud = array_sum($uf_huella[$unidad_funcional->id][$huella->id]);
										}
										if($huella->id == 31){
											$total_huella_ui = array_sum($uf_huella[$unidad_funcional->id][$huella->id]);
										}
										if($huella->id == 32){
											$total_huella_sl = array_sum($uf_huella[$unidad_funcional->id][$huella->id]);
										}
										if($huella->id == 33){
											$total_huella_se = array_sum($uf_huella[$unidad_funcional->id][$huella->id]);
										}
										//

									}
									
									// HUELLAS FIJAS

									// ALIMENTAR HUELLAS FIJAS EN GRAFICOS
									$array_impactos_data_ap = array();
									$array_impactos_data_ac = array();
									foreach ($array_meses as $month) {

										$fecha_inicio_mes = $array_periodos[$month]['start_date'];
										$fecha_termino_mes = $array_periodos[$month]['end_date'];
										//$valor_uf_mes = get_functional_unit_value($client_info->id, $proyecto->id, $unidad_funcional->id, $fecha_inicio_mes, $fecha_termino_mes);

										if(isset($uf_huella_mes[$unidad_funcional->id][30][$month])){
											$mes_huella_ud = (float)$uf_huella_mes[$unidad_funcional->id][30][$month]["valor_mes"];
											$valor_uf_mes = (float)$uf_huella_mes[$unidad_funcional->id][30][$month]["valor_uf"];
											// $mes_huella_ud = ($mes_huella_ud/$valor_uf_mes) * $array_transformaciones[30];
											// $mes_huella_ud = $mes_huella_ud * $array_transformaciones[30];
										}else{
											$mes_huella_ud = 0;
										}
										if(isset($uf_huella_mes[$unidad_funcional->id][31][$month])){
											$mes_huella_ui = (float)$uf_huella_mes[$unidad_funcional->id][31][$month]["valor_mes"];
											$valor_uf_mes = (float)$uf_huella_mes[$unidad_funcional->id][31][$month]["valor_uf"];
											// $mes_huella_ui = ($mes_huella_ui/$valor_uf_mes) * $array_transformaciones[31];
											// $mes_huella_ui = $mes_huella_ui * $array_transformaciones[31];
										}else{
											$mes_huella_ui = 0;
										}
										if(isset($uf_huella_mes[$unidad_funcional->id][32][$month])){
											$mes_huella_sl = (float)$uf_huella_mes[$unidad_funcional->id][32][$month]["valor_mes"];
											$valor_uf_mes = (float)$uf_huella_mes[$unidad_funcional->id][32][$month]["valor_uf"];
											// $mes_huella_sl = ($mes_huella_sl/$valor_uf_mes) * $array_transformaciones[32];
											// $mes_huella_sl = $mes_huella_sl * $array_transformaciones[32];
										}else{
											$mes_huella_sl = 0;
										}
										if(isset($uf_huella_mes[$unidad_funcional->id][33][$month])){
											$mes_huella_se = (float)$uf_huella_mes[$unidad_funcional->id][33][$month]["valor_mes"];
											$valor_uf_mes = (float)$uf_huella_mes[$unidad_funcional->id][33][$month]["valor_uf"];
											// $mes_huella_se = ($mes_huella_se/$valor_uf_mes) * $array_transformaciones[33];
											// $mes_huella_se = $mes_huella_se * $array_transformaciones[33];
										}else{
											$mes_huella_se = 0;
										}

										$data_ap = ($mes_huella_ud + $mes_huella_ui) - ($mes_huella_sl + $mes_huella_se);
										$array_impactos_data_ap[] = $data_ap;
										$array_impactos_data_ac[] = $data_ap + $mes_huella_se;
										
									}
									$array_impactos_chart_data[$unidad_funcional->id][] = array(
										"name" => lang("water_in_product"), 
										"data" => $array_impactos_data_ap
									);
									$array_impactos_chart_data[$unidad_funcional->id][] = array(
										"name" => lang("consumed_water"), 
										"data" => $array_impactos_data_ac
									);
									//

									$icono = $huella->icono ? base_url("assets/images/impact-category/18 huellas-04.png") : base_url("assets/images/impact-category/empty.png");

									$huella_ap = ($total_huella_ud + $total_huella_ui) - ($total_huella_sl + $total_huella_se);
									$valor_ap = (string)(($huella_ap/$valor_uf) * $array_transformaciones[$huella->id]);

									$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
									$html .= '<div class="text-center p15"><img src="'.$icono.'" alt="..." height="50" width="50" class="mCS_img_loaded"></div>';
									$html .= '<div class="text-center p15">'.to_number_project_format($valor_ap, $id_proyecto).'</div>';
									
									$html .= '<div class="pt10 pb10 b-b"> '.lang("water_in_product").' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
									$html .= '</div>';

									// Carga de valores y datos de la huella para exportar a pdf
									$array_export_pdf[$unidad_funcional->id]['huellas']['water_in_product']['icono'] = '18 huellas-04.png';
									$array_export_pdf[$unidad_funcional->id]['huellas']['water_in_product']['valor'] = to_number_project_format($valor_ap, $id_proyecto);
									$array_export_pdf[$unidad_funcional->id]['huellas']['water_in_product']['titulo'] = lang("water_in_product").' ('.$nombre_unidad_huella.' '.$huella->indicador.')';
			
									$huella_ac = ($huella_ap + $total_huella_se);
									$valor_ac = (string)(($huella_ac/$valor_uf) * $array_transformaciones[$huella->id]);

									$html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
									$html .= '<div class="text-center p15"><img src="'.$icono.'" alt="..." height="50" width="50" class="mCS_img_loaded"></div>';
									$html .= '<div class="text-center p15">'.to_number_project_format($valor_ac, $id_proyecto).'</div>';
									
									$html .= '<div class="pt10 pb10 b-b"> '.lang("consumed_water").' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
									$html .= '</div>';

									// Carga de valores y datos de la huella para exportar a pdf
									$array_export_pdf[$unidad_funcional->id]['huellas']['consumed_water']['icono'] = '18 huellas-04.png';
									$array_export_pdf[$unidad_funcional->id]['huellas']['consumed_water']['valor'] = to_number_project_format($valor_ac, $id_proyecto);
									$array_export_pdf[$unidad_funcional->id]['huellas']['consumed_water']['titulo'] = lang("consumed_water").' ('.$nombre_unidad_huella.' '.$huella->indicador.')';
			
			
									echo $html;

									?>
									</div>
								</div>
							</div>
						</div>



					</div>
				</div>
			</div>
		</div>
			
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

		// Se traspasan los datos recolectados a un json para enviarlos por ajax al server y generar el pdf
		// la función para gatillar el evento de exportación se encuentra en el archivo functional_units_water/client/index.php
		array_export_pdf = <?php echo json_encode($array_export_pdf); ?>;

		//General Settings
        var decimals_separator = AppHelper.settings.decimalSeparator;
        var thousands_separator = AppHelper.settings.thousandSeparator;
        var decimal_numbers = AppHelper.settings.decimalNumbers;

		<?php
			$nombre_grafico = $client_info->sigla.'_'.$project_info->sigla.'_FU_impacts_'.date("Y-m-d");

			$titulo_grafico = "Impacto por Huella de Agua - ";
			$subproyecto = $this->Subprojects_model->get_one($unidad_funcional->id_subproyecto);
			$titulo_grafico .= $subproyecto->id ? $subproyecto->nombre : $unidad_funcional->nombre;
		?>

		$('#impactos_uf_<?php echo $unidad_funcional->id; ?>').highcharts({
			chart: {
				type: 'line'
				/*plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
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
				}*/
			},
			title: {
				text: '<?php echo $titulo_grafico; ?>'
			},
			credits: {
				enabled: false
			},
			tooltip: {
				valueSuffix: ' <?php echo $unidad_volumen.' eq / '.$unidad_funcional->nombre; ?>',
				shared: true,
				headerFormat: "<small>{point.key}</small><table>",
				pointFormatter: function(){
					var valueSuffix = this.series.tooltipOptions.valueSuffix || "";
					return '<tr><td style="color:'+this.series.color+';padding:0">● <span style="color:#333333;">'+this.series.name+': </span> </td>'+'<td style="padding:0; font-weight:bold;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+' '+valueSuffix+'</b></td></tr>';
				},
				footerFormat:"</table>",
				useHTML: true
			},
			xAxis: {
				categories: <?php echo json_encode($array_meses); ?>
			},
			yAxis: {
				title: {
					text: "<?php echo $unidad_volumen.' eq / '.$unidad_funcional->nombre; ?>",
					useHTML: true,
				}
			},
			plotOptions: {
				line: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '{point.y:.' + decimal_numbers + 'f}',
						/*style: {
							//color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
							fontSize: "9px",
							distance: -30
						},*/
						crop: false
					},
					showInLegend: true
				}
			},
			exporting: {
				filename: "<?php echo $nombre_grafico; ?>",
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
			series: <?php echo json_encode($array_impactos_chart_data[$unidad_funcional->id]); ?>,
		});

		$('#proporcion_uf_<?php echo $unidad_funcional->id; ?>').highcharts({
			chart: {
				type: 'column',
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
				text: 'Proporción mensual',
			},
			xAxis: {
				categories: <?php echo json_encode($array_meses); ?>
			},
			yAxis: {
				min: 0,
				title: '',
				labels: {
					style: {
						fontSize:'11px'
					},
					format: "{value:,." + decimal_numbers + "f} %",
				},
			},
			credits: {
				enabled: false
			},
			tooltip: {
				formatter: function() {
					return '<b>'+ this.x +'</b>: <br>' + this.series.name + ': ' + numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator) + ' (' + numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +'%' + ')';
				},
			},
			plotOptions: {
				column: {
					stacking: 'percent',
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '{point.percentage:.' + decimal_numbers + 'f} %',
					},
				}
			},
			legend: {
				enabled: true,
			},
			exporting: {
				<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("compliance").'_'.clean(lang("summary_by_iga")).'_'.date("Y-m-d"); ?>
				filename: "<?php echo $filename; ?>",
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
			series: <?php echo json_encode($array_impactos_chart_data[$unidad_funcional->id]); ?>,
		});

	});
</script> 