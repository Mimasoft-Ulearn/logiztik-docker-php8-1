	<div id="unit_processes_group">

		<div class="col-sm-3 col-lg-2 hide">
			<ul class="nav nav-tabs vertical" role="tablist">
				<li class="active"><a data-toggle="tab" href="#<?php echo $unidad_funcional->id; ?>_unidad_funcional"></a></li>
			</ul>
		</div>
       
       <div role="tabpanel" class="tab-pane fade active in" id="graficos_procesos" style="min-height: 200px;">
           <div class="tab-content">

		   <?php

			$array_pu_categorias = array();
			$uf_huella_pu = array();
			foreach($calculos as $calculo){

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
				//echo('console.log("'.$tipo_asignacion_sp.' - '.$sp_destino.'");');

				$criterio_pu = $calculo->criterio_pu;
				$tipo_asignacion_pu = $calculo->tipo_asignacion_pu;
				$pu_destino = $calculo->pu_destino;
				$porcentajes_pu = $calculo->porcentajes_pu;

				if(!$id_campo_sp && !$id_campo_pu && $tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Total"){

					foreach($huellas as $huella){

						if(!isset($uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino]['total_pu'])){
							$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino]['total_pu'] = 0;
						}
						if(!isset($uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'])){
							$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'] = 0;
						}

						$valor_factor = 0;
						if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
							$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
						}

						$valor_uf_mes = $uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
						if($valor_uf_mes == 0){
							$valor = ((float)($mult * $valor_factor)) * $array_transformaciones[$huella->id];
						}else{
							$valor = (((float)($mult * $valor_factor))/$valor_uf_mes) * $array_transformaciones[$huella->id];
						}
						$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino][$calculo->id_categoria][] = $valor;
						$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino]['total_pu'] += $valor;
						$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'] += $valor;
						$array_pu_categorias[$pu_destino][$calculo->id_categoria] = $categorias[$calculo->id_categoria];

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
							
							if(!isset($uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino]['total_pu'])){
								$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino]['total_pu'] = 0;
							}
							if(!isset($uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'])){
								$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'] = 0;
							}

							$valor_factor = 0;
							if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
								$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
							}

							$valor_uf_mes = $uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
							if($valor_uf_mes == 0){
								$valor = ((float)($mult * $valor_factor)) * $array_transformaciones[$huella->id];
							}else{
								$valor = (((float)($mult * $valor_factor))/$valor_uf_mes) * $array_transformaciones[$huella->id];
							}
							$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino][$calculo->id_categoria][] = $valor;
							$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino]['total_pu'] += $valor;
							$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'] += $valor;
							$array_pu_categorias[$pu_destino][$calculo->id_categoria] = $categorias[$calculo->id_categoria];
						}
						
					}else if($tipo_asignacion_sp == "Porcentual" && $criterio_sp == $valor_campo_sp){
						
						foreach($huellas as $huella){

							$valor_factor = 0;
							if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
								$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
							}

							$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
							foreach($porcentajes_sp_decoded as $id_sp => $porcentaje_sp){
								if($porcentaje_sp != 0){
									$porcentaje_sp = ($porcentaje_sp/100);
								}

								if(!isset($uf_huella_pu[$sp_uf[$id_sp]][$huella->id][$pu_destino]['total_pu'])){
									$uf_huella_pu[$sp_uf[$id_sp]][$huella->id][$pu_destino]['total_pu'] = 0;
								}
								if(!isset($uf_huella_pu[$sp_uf[$id_sp]][$huella->id]['total_huella'])){
									$uf_huella_pu[$sp_uf[$id_sp]][$huella->id]['total_huella'] = 0;
								}

								$valor_uf_mes = $uf_huella_mes[$sp_uf[$id_sp]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
								if($valor_uf_mes == 0){
									$valor = ((float)(($mult * $valor_factor) * $porcentaje_sp)) * $array_transformaciones[$huella->id];
								}else{
									$valor = (((float)(($mult * $valor_factor * $porcentaje_sp)))/$valor_uf_mes) * $array_transformaciones[$huella->id];
								}
								$uf_huella_pu[$sp_uf[$id_sp]][$huella->id][$pu_destino][$calculo->id_categoria][] = $valor;
								$uf_huella_pu[$sp_uf[$id_sp]][$huella->id][$pu_destino]['total_pu'] += $valor;
								$uf_huella_pu[$sp_uf[$id_sp]][$huella->id]['total_huella'] += $valor;
								$array_pu_categorias[$pu_destino][$calculo->id_categoria] = $categorias[$calculo->id_categoria];
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

					if($tipo_asignacion_pu == "Total" && $criterio_pu == $valor_campo_pu){

						foreach($huellas as $huella){

							if(!isset($uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino]['total_pu'])){
								$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino]['total_pu'] = 0;
							}
							if(!isset($uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'])){
								$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'] = 0;
							}

							$valor_factor = 0;
							if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
								$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
							}

							$valor_uf_mes = $uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
							if($valor_uf_mes == 0){
								$valor = ((float)($mult * $valor_factor)) * $array_transformaciones[$huella->id];
							}else{
								$valor = (((float)($mult * $valor_factor))/$valor_uf_mes) * $array_transformaciones[$huella->id];
							}
							$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino][$calculo->id_categoria][] = $valor;
							$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino]['total_pu'] += $valor;
							$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'] += $valor;
							$array_pu_categorias[$pu_destino][$calculo->id_categoria] = $categorias[$calculo->id_categoria];
						}

					}else if($tipo_asignacion_pu == "Porcentual" && $criterio_pu == $valor_campo_pu){

						foreach($huellas as $huella){

							$valor_factor = 0;
							if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
								$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
							}

							$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
							foreach($porcentajes_pu_decoded as $id_pu => $porcentaje_pu){
								if($porcentaje_pu != 0){
									$porcentaje_pu = ($porcentaje_pu/100);
								}

								if(!isset($uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$id_pu]['total_pu'])){
									$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$id_pu]['total_pu'] = 0;
								}
								if(!isset($uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'])){
									$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'] = 0;
								}

								$valor_uf_mes = $uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
								if($valor_uf_mes == 0){
									$valor = ((float)(($mult * $valor_factor) * $porcentaje_pu)) * $array_transformaciones[$huella->id];
								}else{
									$valor = (((float)(($mult * $valor_factor * $porcentaje_pu)))/$valor_uf_mes) * $array_transformaciones[$huella->id];
								}
								$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$id_pu][$calculo->id_categoria][] = $valor;
								$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$id_pu]['total_pu'] += $valor;
								$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'] += $valor;
								$array_pu_categorias[$id_pu][$calculo->id_categoria] = $categorias[$calculo->id_categoria];
							}
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

					if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Total" && $criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){

						foreach($huellas as $huella){

							if(!isset($uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino]['total_pu'])){
								$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino]['total_pu'] = 0;
							}
							if(!isset($uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'])){
								$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'] = 0;
							}

							$valor_factor = 0;
							if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
								$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
							}

							$valor_uf_mes = $uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
							if($valor_uf_mes == 0){
								$valor = ((float)($mult * $valor_factor)) * $array_transformaciones[$huella->id];
							}else{
								$valor = (((float)($mult * $valor_factor))/$valor_uf_mes) * $array_transformaciones[$huella->id];
							}
							$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino][$calculo->id_categoria][] = $valor;
							$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$pu_destino]['total_pu'] += $valor;
							$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'] += $valor;
							$array_pu_categorias[$pu_destino][$calculo->id_categoria] = $categorias[$calculo->id_categoria];
						}

					}else if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Porcentual" && $criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){

						foreach($huellas as $huella){

							$valor_factor = 0;
							if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
								$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
							}

							$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
							foreach($porcentajes_pu_decoded as $id_pu => $porcentaje_pu){
								if($porcentaje_pu != 0){
									$porcentaje_pu = ($porcentaje_pu/100);
								}

								if(!isset($uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$id_pu]['total_pu'])){
									$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$id_pu]['total_pu'] = 0;
								}
								if(!isset($uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'])){
									$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'] = 0;
								}

								$valor_uf_mes = $uf_huella_mes[$sp_uf[$sp_destino]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
								if($valor_uf_mes == 0){
									$valor = ((float)(($mult * $valor_factor) * $porcentaje_pu)) * $array_transformaciones[$huella->id];
								}else{
									$valor = (((float)(($mult * $valor_factor * $porcentaje_pu)))/$valor_uf_mes) * $array_transformaciones[$huella->id];
								}
								$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$id_pu][$calculo->id_categoria][] = $valor;
								$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id][$id_pu]['total_pu'] += $valor;
								$uf_huella_pu[$sp_uf[$sp_destino]][$huella->id]['total_huella'] += $valor;
								$array_pu_categorias[$id_pu][$calculo->id_categoria] = $categorias[$calculo->id_categoria];
							}
						}

					}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Total" && $criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){

						foreach($huellas as $huella){

							$valor_factor = 0;
							if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
								$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
							}

							$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
							foreach($porcentajes_sp_decoded as $id_sp => $porcentaje_sp){
								if($porcentaje_sp != 0){
									$porcentaje_sp = ($porcentaje_sp/100);
								}

								if(!isset($uf_huella_pu[$sp_uf[$id_sp]][$huella->id][$pu_destino]['total_pu'])){
									$uf_huella_pu[$sp_uf[$id_sp]][$huella->id][$pu_destino]['total_pu'] = 0;
								}
								if(!isset($uf_huella_pu[$sp_uf[$id_sp]][$huella->id]['total_huella'])){
									$uf_huella_pu[$sp_uf[$id_sp]][$huella->id]['total_huella'] = 0;
								}

								$valor_uf_mes = $uf_huella_mes[$sp_uf[$id_sp]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
								if($valor_uf_mes == 0){
									$valor = ((float)(($mult * $valor_factor) * $porcentaje_sp)) * $array_transformaciones[$huella->id];
								}else{
									$valor = (((float)(($mult * $valor_factor * $porcentaje_sp)))/$valor_uf_mes) * $array_transformaciones[$huella->id];
								}
								$uf_huella_pu[$sp_uf[$id_sp]][$huella->id][$pu_destino][$calculo->id_categoria][] = $valor;
								$uf_huella_pu[$sp_uf[$id_sp]][$huella->id][$pu_destino]['total_pu'] += $valor;
								$uf_huella_pu[$sp_uf[$id_sp]][$huella->id]['total_huella'] += $valor;
								$array_pu_categorias[$pu_destino][$calculo->id_categoria] = $categorias[$calculo->id_categoria];
							}
						}

					}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Porcentual" && $criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){

						foreach($huellas as $huella){

							$valor_factor = 0;
							if(isset($array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad])){
								$valor_factor = $array_factores[$calculo->id_bd][$calculo->id_metodologia][$huella->id][$calculo->id_material][$calculo->id_categoria][$calculo->id_subcategoria][$id_unidad];
							}

							$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
							foreach($porcentajes_sp_decoded as $id_sp => $porcentaje_sp){
								if($porcentaje_sp != 0){
									$porcentaje_sp = ($porcentaje_sp/100);
								}

								$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
								foreach($porcentajes_pu_decoded as $id_pu => $porcentaje_pu){
									if($porcentaje_pu != 0){
										$porcentaje_pu = ($porcentaje_pu/100);
									}

									if(!isset($uf_huella_pu[$sp_uf[$id_sp]][$huella->id][$id_pu]['total_pu'])){
										$uf_huella_pu[$sp_uf[$id_sp]][$huella->id][$id_pu]['total_pu'] = 0;
									}
									if(!isset($uf_huella_pu[$sp_uf[$id_sp]][$huella->id]['total_huella'])){
										$uf_huella_pu[$sp_uf[$id_sp]][$huella->id]['total_huella'] = 0;
									}
									
									$valor_uf_mes = $uf_huella_mes[$sp_uf[$id_sp]][$huella->id][lang("short_".strtolower($calculo->month))."-".$calculo->year]["valor_uf"];
									if($valor_uf_mes == 0){
										$valor = ((float)(($mult * $valor_factor) * $porcentaje_sp * $porcentaje_pu)) * $array_transformaciones[$huella->id];
									}else{
										$valor = (((float)(($mult * $valor_factor * $porcentaje_sp * $porcentaje_pu)))/$valor_uf_mes) * $array_transformaciones[$huella->id];
									}
									$uf_huella_pu[$sp_uf[$id_sp]][$huella->id][$id_pu][$calculo->id_categoria][] = $valor;
									$uf_huella_pu[$sp_uf[$id_sp]][$huella->id][$id_pu]['total_pu'] += $valor;
									$uf_huella_pu[$sp_uf[$id_sp]][$huella->id]['total_huella'] += $valor;
									$array_pu_categorias[$id_pu][$calculo->id_categoria] = $categorias[$calculo->id_categoria];
								}
							}

						}

					}

				}

			}


			// PRINT
			/*foreach($uf_huella_pu as $id_uf => $huella_valores){
				echo "UF: $id_uf:<br>";
				foreach($huella_valores as $id_huella => $pu_valores_cat){
					echo "<strong>Huella: $id_huella</strong><br>";
					foreach($pu_valores_cat as $id_pu => $cat_valores){
						if($id_pu != 'total_huella'){
							echo "PU: $id_pu<br>";
							foreach($cat_valores as $id_categoria => $valores){
								if($id_categoria == 'total_pu'){
									echo "TOTAL PU: ".$valores."<br>";
								}else{
									echo "id_categoria - $id_categoria: ".implode('|', $valores)." = ".array_sum($valores)."<br>";
								}
							}
						}
					}
					echo "TOTAL HUELLA: ".$uf_huella_pu[$id_uf][$id_huella]['total_huella']."<br>";
					echo "<br>";
				}
				echo "<br><br>";
			}*/

			// APPTABLE
			$final_uf = array();
			$result = array();
			foreach ($procesos_unitarios as $pu) {
				//$result[] = $this->_make_row($data, $uf_data, $start_date, $end_date);

				$row_data = array();

				$row_data[] = '<a href="#" class="details-control"><i class="fa fa-plus-circle font-16"></i></a>';
				//$row_data[] = $data->id;
				//$row_data[] = $data->id_rel;
				$row_data[] = $pu['id'];

				$icono_pu = base_url("assets/images/unit-processes/".$pu['icono']);
				$html_pu = '<div class="milestone pull-left p0">';
					$html_pu .= '<h1><img src="'.$icono_pu.'" alt="..." height="37" width="37" class="mCS_img_loaded"></h1>';
					$html_pu .= '<div class="pt10 pb10 b-t label-success proceso_unitario"> '.$pu['nombre'].' </div>';
				$html_pu .= '</div>';
				$row_data[] = $html_pu;

				$array_totales_categorias = array();
				$array_categorias_formatted = array();
				foreach($huellas as $huella){
					$array_totales_categorias[$huella->id] = array(0);

					// PROCESO LOS VALORES DEL DOBLE CLICK
					foreach($array_pu_categorias[$pu['id']] as $id_categoria => $nombre_categoria){

						if(isset($uf_huella_pu[$unidad_funcional->id][$huella->id][$pu['id']][$id_categoria])){
							$valor = array_sum($uf_huella_pu[$unidad_funcional->id][$huella->id][$pu['id']][$id_categoria]);
							$valor_final = to_number_project_format($valor, $project_info->id);
							$array_totales_categorias[$huella->id][$id_categoria] = $valor;
						}else{
							$valor_final = to_number_project_format(0, $project_info->id);
						}
						$array_categorias_formatted[$nombre_categoria][$huella->nombre] = $valor_final;
					}

					//$total_pu_final = to_number_project_format($uf_huella_pu[$unidad_funcional->id][$huella->id][$pu['id']]['total_pu'], $project_info->id);
					$row_data[] = to_number_project_format($uf_huella_pu[$unidad_funcional->id][$huella->id][$pu['id']]['total_pu'], $project_info->id);//$total_pu_final;
				}

				// PROCESO LOS VALORES A NIVEL DE PU-HUELLA, PARA DETECTAR EL MAYOR VALOR
				$array_categorias_mayores = array();
				foreach($huellas as $huella){
					$max_categoria = array_keys($array_totales_categorias[$huella->id], max($array_totales_categorias[$huella->id]));
					if(count($max_categoria) == 1){
						$array_categorias_mayores[$huella->nombre] = $categorias[$max_categoria[0]];
					}
				}

				$row_data['num_huellas'] = count($huellas);
				$row_data['categorias'] = $array_categorias_formatted;
				$row_data['categorias_mayores'] = $array_categorias_mayores;
				$result[] = $row_data;
			}

			$final_uf[$unidad_funcional->id] = $result;

			?>
          
               <div id="<?php echo $unidad_funcional->id; ?>_unidad_funcional" class="tab-pane fade in active">
                   <div class="col-md-12 p0">
                       <div class="panel">
                            <div class="panel-default panel-heading">
                                <h4><?php echo $unidad_funcional->nombre; ?></h4>
                            </div>
                            <div class="panel-body">
                            
                                <!-- START ROW -->
                                <div class="row">
                                      <?php foreach($huellas as $huella){ ?>
                                      <?php
                                      
                                      $id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
                                            "id_cliente" => $client_info->id, 
                                            "id_proyecto" => $project_info->id, 
                                            "id_tipo_unidad" => $huella->id_tipo_unidad, 
                                            "deleted" => 0
                                       ))->id_unidad;
                                            
                                       $nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;
                                                                        
                                       ?>
                                      
                                         <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 col-xl-2">
                                            <div class="panel panel-default">
                                               <div class="page-title clearfix panel-success">
                                                  <!--<h3>Cambio climático</h3> -->
                                                  <div class="pt10 pb10 text-center"> <?php echo $huella->nombre.'<br /> ('.$nombre_unidad_huella.' '.$huella->indicador.')'; ?> </div>
                                               </div>
                                               <div class="panel-body">
                                                  <div id="grafico_<?php echo $huella->id?>-uf_<?php echo $unidad_funcional->id?>" style="height: 240px;" class="chart"></div>
                                               </div>
                                            </div>
                                         </div>
                                       
                                       <?php } ?>
                                 </div>
                                 <!-- END ROW -->
                                 
                                 <div class="table-responsive">
                                     <table id="<?php echo $unidad_funcional->id; ?>_uf-table" class="display" cellspacing="0" width="100%">            
                                     </table>
                                 </div>
                                
                            </div>
                       </div>
                   </div>
               </div>
          
            </div>
        </div>
   
    <?php
        $id_proyecto = $project_info->id;
        //$id_metodologia = $project_info->id_metodologia;
		//$ids_metodologia = json_decode($project_info->id_metodologia);
    ?>

</div>

<style>
/*
table[id$=_uf-table] th { font-size: 12px; }
table[id$=_uf-table] td { font-size: 11px; }
*/
</style>
<!--<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script>-->
<script type="text/javascript">


$(document).ready(function () {
	
	adaptarAltura();
	
	function adaptarAltura(e){
		
		if(e){
			var id_tab = $(e.target).attr("href");
		}else{
			var id_tab = "#"+$("#graficos_procesos .tab-pane:first").attr("id");
		}
		
		// cabezera graficos
		var maxHeight = Math.max.apply(null, $(id_tab+" > div > div > div.panel-body > div > div > div > div.page-title.clearfix.panel-success").map(function (){
			return $(this).height();
		}).get());
		
		$(id_tab+" > div > div > div.panel-body > div > div > div > div.page-title.clearfix.panel-success").height(maxHeight);
		
		// contenido graficos
		var maxHeight2 = Math.max.apply(null, $(id_tab+" > div > div > div.panel-body > div > div > div.panel").map(function (){
			return $(this).height();
		}).get());
		
		$(id_tab+" > div > div > div.panel-body > div > div > div.panel").height(maxHeight2);
	}
	
	$('#page-content a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		adaptarAltura(e);
		$('.chart').each(function() { 
			$(this).highcharts().reflow();
		});
		$("table[id$='_uf-table']").each(function() {
			var table = $(this).DataTable();
			table.columns.adjust().draw();
		});
	});
	
	//General Settings
	var decimals_separator = AppHelper.settings.decimalSeparator;
	var thousands_separator = AppHelper.settings.thousandSeparator;
	var decimal_numbers = AppHelper.settings.decimalNumbers;	
	
	<?php
		
	$nombre_uf = $unidad_funcional->nombre;
	$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
	
	foreach($huellas as $huella){
	
		$id_huella = $huella->id;
		$total_huella = $uf_huella_pu[$unidad_funcional->id][$id_huella]['total_huella'];
		
		$array_data = array();
		$array_colores_pu = array();
		
		$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
			"id_cliente" => $client_info->id, 
			"id_proyecto" => $project_info->id, 
			"id_tipo_unidad" => $huella->id_tipo_unidad, 
			"deleted" => 0
		))->id_unidad;
		
		$nombre_unidad_huella = $unidades[$id_unidad_huella_config];
		
		foreach($procesos_unitarios as $pu){

			$nombre_pu = $pu["nombre"];
			$total_pu = $uf_huella_pu[$unidad_funcional->id][$id_huella][$pu['id']]['total_pu'];
			$porc_pu = ($total_pu == 0)?0:(($total_pu * 100) / $total_huella);

			$array_data[] = array("name" => $nombre_pu, "y" => $porc_pu);
			$array_colores_pu[] = ($pu["color"]) ? $pu["color"] : "#00b393";
		}
		
		$nombre_grafico = $client_info->sigla.'_'.$project_info->sigla.'_PU_'.$huella->abreviatura.'_'.$nombre_unidad_huella.'_'.date("Y-m-d");

		?>
		
		$('#grafico_<?php echo $huella->id; ?>-uf_<?php echo $unidad_funcional->id; ?>').highcharts({
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie',
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
				text: '',
			},
			credits: {
				enabled: false
			},
			tooltip: {
				formatter: function() {
					return '<b>'+ this.point.name +'</b>: '+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +' %';
				},
				// pointFormat: '{series.name}: <b>{point.y}%</b>'
			},
			plotOptions: {
				pie: {
					//size: 80,
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: false,
						format: '<b>{point.name}</b>: {point.percentage:.' + decimal_numbers + 'f} %',
						style: {
							color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
							// fontSize: "9px",
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
				// fontSize: "9px"
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
			colors: <?php echo json_encode($array_colores_pu);?>,
			series: [{
				name: 'Porcentaje',
				colorByPoint: true,
				
				data: <?php echo json_encode($array_data);?>
			}]
		});
	
	<?php } ?>

	var datos_<?php echo $unidad_funcional->id; ?> = <?php echo json_encode($final_uf[$unidad_funcional->id]); ?>;
	$("#<?php echo $unidad_funcional->id; ?>_uf-table").dataTable({
		data: datos_<?php echo $unidad_funcional->id; ?>,
		columns: [
			{title: "", "class": "text-center w50"},
			{title: "ID", "class": "text-center w50 hide"},
			{title: "<?php echo lang("unit_process") ?>", "class": "text-center w50"}
			<?php echo $columnas; ?>,
		],
		order: [[1, "asc"]],
	});
	
	/*$("#<?php echo $unidad_funcional->id; ?>_uf-table").appTable({
		<?php if ($start_date && $end_date){ ?>
		source: '<?php echo_uri("unit_processes_carbon/list_data/".$id_subproyecto_uf."/".$unidad_funcional->id."/".$start_date."/".$end_date); ?>',
		<?php } else { ?>
		source: '<?php echo_uri("unit_processes_carbon/list_data/".$id_subproyecto_uf."/".$unidad_funcional->id); ?>',
		<?php } ?>
		columns: [
			{title: "", "class": "text-center w50"},
			{title: "ID", "class": "text-center w50 hide"},
			{title: "<?php echo lang("unit_process") ?>", "class": "text-center w50"}
			<?php echo $columnas; ?>,
			//{title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
		],
		order: [[1, "asc"]],
		//printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5]),
		//xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5])
	});*/
	
	$("#<?php echo $unidad_funcional->id; ?>_uf-table").on('click', 'a.details-control', function () {
		var table = $("#<?php echo $unidad_funcional->id; ?>_uf-table").DataTable();
		var tr = $(this).closest('tr');
		var row = table.row(tr);
	
		if (row.child.isShown()) {
			// This row is already open - close it
			
			$('div.slider', row.child()).slideUp(function () {
				row.child.hide();
				tr.removeClass('shown');
			});
			$(this).html('<i class="fa fa-plus-circle font-16"></i>');
			
		}else{
			// Open this row
			row.child(format(row.data())).show();
			tr.addClass('shown');
			//$('div.slider', row.child()).slideDown('slow');
			
			row.child().find('td:first').css('padding', '0');
			row.child().find('td:first table > tbody tr:first td').each(function(index, td){
				$(td).css('width', (tr.children('td:eq('+(index+1)+')').width()));
			});
			
			$(this).html('<i class="fa fa-minus-circle font-16"></i>');
		}
	});

	function format(d){
		var html = '<div class="table-responsive slider"><table class="table">';
		
		html += '<thead><tr><th></th><th class=" text-center"><?php echo lang("category"); ?></th><th colspan="'+d.num_huellas+'"></th></tr></thead>';
		$.each(d.categorias, function(categoria, huellas){
			html += '<tr>';
			html += '<td class=" text-center"></td>';
			html += '<td class=" text-center">'+categoria+'</td>';
			$.each(huellas, function(huella, valor){
				var clase = 'text-right';
				if(typeof d.categorias_mayores[huella] !== 'undefined'){ 
					if(d.categorias_mayores[huella] == categoria){
						clase += " text-danger strong";
					}
				}
				html += '<td class="'+clase+'">'+valor+'</td>';
			});
			
			html += '</tr>';
		});
		html += '</table></div>';
		
		return html;
	}


	$("#unit_processes_pdf").off().on("click", function(e) {	
		
		appLoader.show();
		
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		var id_unidad_funcional = $('#functional_unit').val();
		
		//General Settings
		var decimals_separator = AppHelper.settings.decimalSeparator;
		var thousands_separator = AppHelper.settings.thousandSeparator;
		var decimal_numbers = AppHelper.settings.decimalNumbers;	
		
		var graficos_huellas_unidades_funcionales = {};

			
		var graficos_huellas = {};
	
		<?php foreach($huellas as $huella){ ?>

			var id = "grafico_<?php echo $huella->id; ?>-uf_<?php echo $unidad_funcional->id; ?>";

			$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = true;
			$('#' + id).highcharts().options.title.text = "<?php echo $huella->nombre.'<br /> ('.$nombre_unidad_huella.' '.$huella->indicador.')'; ?>";
			$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = true;
			$('#' + id).highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "15px";
			$('#' + id).highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
			$('#' + id).highcharts().options.plotOptions.pie.size = 150;
			$('#' + id).highcharts().options.legend.itemStyle.fontSize = "15px";
			$('#' + id).highcharts().options.title.style.fontSize = "23px";
			
			var chart = $('#' + id).highcharts().options.chart;
			var series = $('#' + id).highcharts().options.series;
			var title = $('#' + id).highcharts().options.title;
			var plotOptions = $('#' + id).highcharts().options.plotOptions;
			var colors = $('#' + id).highcharts().options.colors;
			var exporting = $('#' + id).highcharts().options.exporting;
			var credits = $('#' + id).highcharts().options.credits;
			var legend = $('#' + id).highcharts().options.legend;

			var obj = {};
			obj.options = JSON.stringify({
				"chart":chart,
				"title":title,
				"series":series,
				"plotOptions":plotOptions,
				"colors":colors,
				"exporting":exporting,
				"credits":credits,
				"legend":legend,
			});
			
			obj.type = 'image/png';
			obj.width = '1600';
			obj.scale = '2';
			obj.async = true;
			
			var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
			obj.globaloptions = JSON.stringify(globalOptions);
			
			var imagen_grafico = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			
			$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = false;
			$('#' + id).highcharts().options.plotOptions.pie.size = null;
			$('#' + id).highcharts().options.legend.itemStyle.fontSize = "9px;";
			
			graficos_huellas[<?php echo $huella->id; ?>] = imagen_grafico;

		<?php } ?>
		
		graficos_huellas_unidades_funcionales[<?php echo $unidad_funcional->id; ?>] = graficos_huellas;

		$.ajax({
			url:  '<?php echo_uri("unit_processes_carbon/get_pdf") ?>',
			type:  'post',
			data: {
				imagenes_graficos: graficos_huellas_unidades_funcionales,
				start_date: start_date,
				end_date: end_date,
				id_unidad_funcional: id_unidad_funcional
			},
			//dataType:'json',
			success: function(respuesta){
				
				var uri = '<?php echo get_setting("temp_file_path") ?>' + respuesta;
				var link = document.createElement("a");
				link.download = respuesta;
				link.href = uri;
				link.click();
				
				borrar_temporal(uri);
			}

		});

	});
	
	function borrar_temporal(uri){
		
		$.ajax({
			url:  '<?php echo_uri("unit_processes_carbon/borrar_temporal") ?>',
			type:  'post',
			data: {uri:uri},
			//dataType:'json',
			success: function(respuesta){
				appLoader.hide();
			}

		});

	}
	
	function getChartName(obj){
		var tmp = null;
		$.support.cors = true;
		$.ajax({
			async: false,
			type: 'post',
			dataType: 'text',
			url : AppHelper.highchartsExportUrl,
			data: obj,
			crossDomain:true,
			success: function (data) {
				tmp = data.replace(/files\//g,'');
				tmp = tmp.replace(/.png/g,'');
			}
		});
		return tmp;
	}
		
});
</script> 