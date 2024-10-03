<?php

class Calculation_model extends Crud_model {

    private $table = null;

    function __construct() {
		$this->load->helper('database');
        $this->table = 'calculos';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
		
        $calculos_table = $this->db->dbprefix('calculos');
        $rules_table = $this->db->dbprefix('criterios');
        $clients_table = $this->db->dbprefix('clients');
        $projects_table = $this->db->dbprefix('projects');
		$fields_table= $this->db->dbprefix('campos');
		
		$materials_db = getFCBD();
		$category_table = $this->load->database(getFCBD(), TRUE)->dbprefix('categorias');
		$subcategory_table = $this->load->database(getFCBD(), TRUE)->dbprefix('subcategorias');
		$databases_table = $this->load->database(getFCBD(), TRUE)->dbprefix('bases_de_datos');
		
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $calculos_table.id=$id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where = " AND $calculos_table.id_cliente=$id_cliente";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where = " AND $calculos_table.id_proyecto=$id_proyecto";
        }
		
		$id_criterio = get_array_value($options, "id_criterio");
        if ($id_criterio) {
            $where = " AND $calculos_table.id_criterio=$id_criterio";
        }
		
		$id_bd = get_array_value($options, "id_bd");
        if ($id_bd) {
            $where = " AND $calculos_table.id_bd=$id_bd";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
		
		$sql = "SELECT $calculos_table.id, $calculos_table.id_campo_unidad, $clients_table.company_name, $projects_table.title, $rules_table.etiqueta, ";
		$sql .= "$calculos_table.criterio_fc, $databases_table.nombre AS nombre_bd, $category_table.nombre AS nombre_categoria, ";
		$sql .= "$subcategory_table.nombre AS nombre_subcategoria, $calculos_table.etiqueta AS etiqueta_calculo, $calculos_table.id_criterio, ";
		$sql .= "$calculos_table.created, $calculos_table.modified, $calculos_table.id_metodologia";
		$sql .= " FROM $clients_table, $projects_table, $rules_table, $calculos_table, 
				$materials_db.$databases_table, $materials_db.$category_table, $materials_db.$subcategory_table WHERE";
		$sql .= " $calculos_table.deleted = 0";
		$sql .= " AND $clients_table.id = $calculos_table.id_cliente";
		$sql .= " AND $projects_table.id = $calculos_table.id_proyecto";
		$sql .= " AND $rules_table.id = $calculos_table.id_criterio";
		//$sql .= " AND $fields_table.id = $calculos_table.id_campo_unidad";
		$sql .= " AND $databases_table.id = $calculos_table.id_bd";
		$sql .= " AND $category_table.id = $calculos_table.id_categoria";
		$sql .= " AND $subcategory_table.id = $calculos_table.id_subcategoria";
		$sql .= " $where";

        return $this->db->query($sql);

    }
	
	function get_calculations_field_ids_of_project($client_id, $project_id) {
		
		$rules_table = $this->db->dbprefix('criterios');
        $calculos_table = $this->db->dbprefix('calculos');
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $rules_table.id_formulario, $rules_table.id_material, $rules_table.id_campo_fc, $calculos_table.* ";
		$sql .= "FROM $rules_table, $calculos_table WHERE";
		$sql .= " $rules_table.deleted = 0 AND $calculos_table.deleted = 0";
		$sql .= " AND $calculos_table.id_criterio = $rules_table.id";
		$sql .= " AND $calculos_table.id_cliente = $client_id";
		$sql .= " AND $calculos_table.id_proyecto = $project_id";
		
        return $this->db->query($sql);

    }
	

	function get_records_of_forms_for_calculation($project_id, $form_id, $id_campo_fc, $criterio_fc, $id_categoria, $start_date = NULL, $end_date = NULL) {
		
		$form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
        $form_values_table = $this->db->dbprefix('valores_formularios');
		
		$sql_criterio_fc = "";
		if($id_campo_fc && $criterio_fc){
			
			if(is_numeric($id_campo_fc) && is_numeric($criterio_fc)){// id de campo | valor numerico
			
				//$sql_criterio_fc .= " AND $form_values_table.datos->'$[$id_campo_fc]' = '$criterio_fc'";
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.\"".$id_campo_fc."\"' = '$criterio_fc'";
				
			}elseif(is_numeric($id_campo_fc) && !is_numeric($criterio_fc)){// id de campo | valor string
			
				//$criterio_fc_encoded = json_encode($criterio_fc);
				//$sql_criterio_fc .= " AND $form_values_table.datos->'$[$id_campo_fc]' = '$criterio_fc_encoded'";
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.\"".$id_campo_fc."\"' = '$criterio_fc'";
				
			}else{// campo fijo, ej: type_of_origin_matter, tipo_tratamiento, default_type, etc
			
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.".$id_campo_fc."' = '$criterio_fc'";
				
			}
		}
		
		$sql_categoria = "";
		if($id_categoria){
			$sql_categoria .= " AND $form_values_table.datos->'$.id_categoria' = $id_categoria";
		}
		
		$sql_rango_fechas = "";
		if($start_date && $end_date){
			$sql_rango_fechas .= " AND ($form_values_table.datos->'$.fecha' >= '$start_date'";
			$sql_rango_fechas .= " AND $form_values_table.datos->'$.fecha' <= '$end_date')";
		}

        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $form_values_table.* ";
		$sql .= "FROM $form_rel_project_table, $form_values_table WHERE";
		$sql .= " $form_values_table.deleted = 0 AND $form_rel_project_table.deleted = 0";
		$sql .= " AND $form_rel_project_table.id_proyecto = $project_id";
		$sql .= " AND $form_rel_project_table.id_formulario = $form_id";
		$sql .= " AND $form_values_table.id_formulario_rel_proyecto = $form_rel_project_table.id";
		$sql .= $sql_criterio_fc;
		$sql .= $sql_categoria;
		$sql .= $sql_rango_fechas;
		
        return $this->db->query($sql);

    }
	
	/* 
		Este método no está siendo utilizado, pero eventualmente podría. Se debe optimizar la consulta, 
		en el filtro que se hace para el rango de fechas.
	*/
	function get_records_of_forms_for_calculation_acv_report($project_id, $form_id, $id_campo_fc, $criterio_fc, $id_categoria, $start_date, $end_date) {
		
		$form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
        $form_values_table = $this->db->dbprefix('valores_formularios');
		
		$sql_criterio_fc = "";
		if($id_campo_fc && $criterio_fc){
			
			if(is_numeric($id_campo_fc) && is_numeric($criterio_fc)){// id de campo | valor numerico
			
				//$sql_criterio_fc .= " AND $form_values_table.datos->'$[$id_campo_fc]' = '$criterio_fc'";
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.\"".$id_campo_fc."\"' = '$criterio_fc'";
				
			}elseif(is_numeric($id_campo_fc) && !is_numeric($criterio_fc)){// id de campo | valor string
			
				//$criterio_fc_encoded = json_encode($criterio_fc);
				//$sql_criterio_fc .= " AND $form_values_table.datos->'$[$id_campo_fc]' = '$criterio_fc_encoded'";
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.\"".$id_campo_fc."\"' = '$criterio_fc'";
				
			}else{// campo fijo, ej: type_of_origin_matter, tipo_tratamiento, default_type, etc
			
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.".$id_campo_fc."' = '$criterio_fc'";
				
			}
			
		}
		
		$sql_categoria = "";
		if($id_categoria){
			$sql_categoria .= " AND $form_values_table.datos->'$.id_categoria' = $id_categoria";
		}
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $form_values_table.* ";
		$sql .= "FROM $form_rel_project_table, $form_values_table WHERE";
		$sql .= " $form_values_table.deleted = 0 AND $form_values_table.deleted = 0";
		$sql .= " AND $form_rel_project_table.id_proyecto = $project_id";
		$sql .= " AND $form_rel_project_table.id_formulario = $form_id";
		$sql .= " AND $form_values_table.id_formulario_rel_proyecto = $form_rel_project_table.id";
		$sql .= $sql_criterio_fc;
		$sql .= $sql_categoria;
		//$sql .= " AND SUBSTRING($form_values_table.datos, 11,10) BETWEEN '$start_date' AND '$end_date'";
		$sql .= " AND ($form_values_table.datos->'$.fecha' >= '$start_date'";
		$sql .= " AND $form_values_table.datos->'$.fecha' <= '$end_date')";
		
        return $this->db->query($sql);

    }
	
	function get_records_of_forms_for_unit_processes($project_id, $form_id, $id_campo_fc, $criterio_fc, $id_categoria, $id_campo_sp, $criterio_sp, $id_campo_pu, $criterio_pu) {
		
		$form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
        $form_values_table = $this->db->dbprefix('valores_formularios');
		
		$sql_criterio_fc = "";
		if($id_campo_fc && $criterio_fc){
			
			if(is_numeric($id_campo_fc) && is_numeric($criterio_fc)){// id de campo | valor numerico
			
				//$sql_criterio_fc .= " AND $form_values_table.datos->'$[$id_campo_fc]' = '$criterio_fc'";
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.\"".$id_campo_fc."\"' = '$criterio_fc'";
				
			}elseif(is_numeric($id_campo_fc) && !is_numeric($criterio_fc)){// id de campo | valor string
			
				//$criterio_fc_encoded = json_encode($criterio_fc);
				//$sql_criterio_fc .= " AND $form_values_table.datos->'$[$id_campo_fc]' = '$criterio_fc_encoded'";
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.\"".$id_campo_fc."\"' = '$criterio_fc'";
				
			}else{// campo fijo, ej: type_of_origin_matter, tipo_tratamiento, default_type, etc
			
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.".$id_campo_fc."' = '$criterio_fc'";
				
			}
		}
		
		$sql_categoria = "";
		if($id_categoria){
			$sql_categoria .= " AND $form_values_table.datos->'$.id_categoria' = $id_categoria";
		}
		
		$sql_campo_sp = "";
		if($id_campo_sp){
			
			if(is_numeric($id_campo_sp) && is_numeric($criterio_sp)){// id de campo | valor numerico
			
				//$sql_campo_sp .= " AND $form_values_table.datos->'$[$id_campo_sp]' = '$criterio_sp'";
				$sql_campo_sp .= " AND $form_values_table.datos->'$.\"".$id_campo_sp."\"' = '$criterio_sp'";
				
			}elseif(is_numeric($id_campo_sp) && !is_numeric($criterio_sp)){// id de campo | valor string
			
				//$criterio_sp_encoded = json_encode($criterio_sp);
				//$sql_campo_sp .= " AND $form_values_table.datos->'$[$id_campo_sp]' = '$criterio_sp_encoded'";
				$sql_campo_sp .= " AND $form_values_table.datos->'$.\"".$id_campo_sp."\"' = '$criterio_sp_encoded'";
				
			}else{// campo fijo, ej: type_of_origin_matter, tipo_tratamiento, default_type, etc
			
				$sql_campo_sp .= " AND $form_values_table.datos->'$.".$id_campo_sp."' = '$criterio_sp'";
				
			}
		}
		
		$sql_campo_pu = "";
		if($id_campo_pu){
			
			if(is_numeric($id_campo_pu) && is_numeric($criterio_pu)){// id de campo | valor numerico
			
				//$sql_campo_pu .= " AND $form_values_table.datos->'$[$id_campo_pu]' = '$criterio_pu'";
				$sql_campo_pu .= " AND $form_values_table.datos->'$.\"".$id_campo_pu."\"' = '$criterio_pu'";
				
			}elseif(is_numeric($id_campo_pu) && !is_numeric($criterio_sp)){// id de campo | valor string
			
				//$criterio_pu_encoded = json_encode($criterio_pu);
				//$sql_campo_pu .= " AND $form_values_table.datos->'$[$id_campo_pu]' = '$criterio_pu_encoded'";
				$sql_campo_pu .= " AND $form_values_table.datos->'$.\"".$sql_campo_pu."\"' = '$criterio_pu_encoded'";
				
			}else{// campo fijo, ej: type_of_origin_matter, tipo_tratamiento, default_type, etc
			
				$sql_campo_pu .= " AND $form_values_table.datos->'$.".$id_campo_pu."' = '$criterio_pu'";
				
			}
		}
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $form_values_table.* ";
		$sql .= "FROM $form_rel_project_table, $form_values_table WHERE";
		$sql .= " $form_values_table.deleted = 0 AND $form_values_table.deleted = 0";
		$sql .= " AND $form_rel_project_table.id_proyecto = $project_id";
		$sql .= " AND $form_rel_project_table.id_formulario = $form_id";
		$sql .= " AND $form_values_table.id_formulario_rel_proyecto = $form_rel_project_table.id";
		$sql .= $sql_criterio_fc;
		$sql .= $sql_categoria;
		$sql .= $sql_campo_sp;
		$sql .= $sql_campo_pu;
		
        return $this->db->query($sql);

    }
	
	function get_records_of_project($id_proyecto, $flujo_formulario) {
		
		$form_table = $this->db->dbprefix('formularios');
        $form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
		$form_values_table = $this->db->dbprefix('valores_formularios');
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $form_values_table.* ";
		$sql .= "FROM $form_table, $form_rel_project_table, $form_values_table WHERE";
		$sql .= " $form_table.deleted = 0 AND $form_rel_project_table.deleted = 0 AND $form_values_table.deleted = 0";
		$sql .= " AND $form_rel_project_table.id_formulario = $form_table.id";
		$sql .= " AND $form_values_table.id_formulario_rel_proyecto = $form_rel_project_table.id";
		$sql .= " AND $form_table.id_tipo_formulario = 1";
		$sql .= " AND $form_table.flujo = '$flujo_formulario'";
		$sql .= " AND $form_rel_project_table.id_proyecto = $id_proyecto";
		
        return $this->db->query($sql);

    }
	
	function get_records_of_category_of_form($id_categoria, $id_formulario, $flujo_formulario) {
		
		$form_table = $this->db->dbprefix('formularios');
        $form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
		$form_values_table = $this->db->dbprefix('valores_formularios');
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $form_values_table.* ";
		$sql .= "FROM $form_table, $form_rel_project_table, $form_values_table WHERE";
		$sql .= " $form_table.deleted = 0 AND $form_rel_project_table.deleted = 0 AND $form_values_table.deleted = 0";
		$sql .= " AND $form_rel_project_table.id_formulario = $form_table.id";
		$sql .= " AND $form_values_table.id_formulario_rel_proyecto = $form_rel_project_table.id";
		$sql .= " AND $form_table.id_tipo_formulario = 1";
		$sql .= " AND $form_table.flujo = '$flujo_formulario'";
		$sql .= " AND $form_rel_project_table.id_formulario = $id_formulario";
		//$sql .= " AND $form_values_table.datos LIKE '%\"id_categoria\":\"$id_categoria\"%'";
		$sql .= " AND $form_values_table.datos->'$.id_categoria' = $id_categoria";
        return $this->db->query($sql);

    }
	
	function get_records_for_each_category($id_categoria) {
		
		$form_table = $this->db->dbprefix('formularios');
        $form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
		$form_values_table = $this->db->dbprefix('valores_formularios');
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $form_values_table.* ";
		$sql .= "FROM $form_table, $form_rel_project_table, $form_values_table WHERE";
		$sql .= " $form_table.deleted = 0 AND $form_rel_project_table.deleted = 0 AND $form_values_table.deleted = 0";
		$sql .= " AND $form_rel_project_table.id_formulario = $form_table.id";
		$sql .= " AND $form_values_table.id_formulario_rel_proyecto = $form_rel_project_table.id";
		$sql .= " AND $form_table.flujo = 'Consumo'";
		//$sql .= " AND $form_values_table.datos = LIKE '%\"id_categoria\":\"$id_categoria\"%'";
		$sql .= " AND $form_values_table.datos->'$.id_categoria' = $id_categoria";
		
        return $this->db->query($sql);

    }
	
	function delete_calculation($id){
		
		$calculos = $this->db->dbprefix('calculos');
        $sql = "UPDATE $calculos SET $calculos.deleted=1 WHERE $calculos.id=$id; ";
        $this->db->query($sql);
		
	}


	function get_calculos($id_proyecto = 0, $id_cliente = 0, $id_sp = NULL, $from = NULL, $to = NULL) {
		
		$criterios = $this->db->dbprefix('criterios');
        $calculos = $this->db->dbprefix('calculos');
		$formularios = $this->db->dbprefix('formularios');
		$valores_formularios = $this->db->dbprefix('valores_formularios');
		$asignaciones = $this->db->dbprefix('asignaciones');
		$asignaciones_combinaciones = $this->db->dbprefix('asignaciones_combinaciones');
		$formulario_rel_proyecto = $this->db->dbprefix('formulario_rel_proyecto');
        
		//$this->load->database("mimaacp_test", TRUE);
        $this->db->query('SET SQL_BIG_SELECTS=1');

		$where_date = "";
		if($from && $to){
			$where_date = "$valores_formularios.datos->>'$.fecha' BETWEEN '$from' AND '$to' AND ";
		}

		$where_sp = "";
		if($id_sp){
			$where_sp = "AND $asignaciones_combinaciones.sp_destino = $id_sp OR $asignaciones_combinaciones.sp_destino IS NULL ";
			//$where_sp .= " AND $asignaciones.deleted=0 AND $asignaciones_combinaciones.deleted=0";
		}

		$sql = "SELECT 
			ca.*, 
			$asignaciones_combinaciones.*
		FROM 
		(SELECT
			$criterios.id AS id_criterio,
			$criterios.id_formulario,
			$criterios.id_material,
			COALESCE($criterios.id_campo_fc, IF($criterios.tipo_by_criterio->>'$.id_campo_fc' = 'null', NULL, $criterios.tipo_by_criterio->>'$.id_campo_fc')) AS id_campo_fc,
			COALESCE($criterios.id_campo_sp, IF($criterios.tipo_by_criterio->>'$.id_campo_sp' = 'null', NULL, $criterios.tipo_by_criterio->>'$.id_campo_sp')) AS id_campo_sp,
			COALESCE($criterios.id_campo_pu, IF($criterios.tipo_by_criterio->>'$.id_campo_pu' = 'null', NULL, $criterios.tipo_by_criterio->>'$.id_campo_pu')) AS id_campo_pu,
			$criterios.tipo_by_criterio,
			$calculos.id AS id_calculo,
			$calculos.id_bd,
			$calculos.id_metodologia,
			$calculos.id_subcategoria,
			$calculos.id_categoria AS calculo_id_categoria,
			$calculos.criterio_fc,
			$calculos.id_campo_unidad,
			$formularios.unidad AS formulario_unidad,
			$valores_formularios.id AS id_valor,
			$valores_formularios.datos->>'$.id_categoria' AS id_categoria,
			$valores_formularios.datos->>'$.unidad_residuo' AS valor,
			MONTHNAME($valores_formularios.datos->>'$.fecha') AS month,
			RIGHT(YEAR($valores_formularios.datos->>'$.fecha'), 2) AS year,
			$valores_formularios.datos 
		FROM $criterios
		LEFT JOIN $calculos ON $calculos.id_criterio = $criterios.id
		LEFT JOIN $formulario_rel_proyecto ON $formulario_rel_proyecto.id_proyecto = $criterios.id_proyecto AND $formulario_rel_proyecto.id_formulario = $criterios.id_formulario 
		LEFT JOIN $formularios ON $formularios.id = $criterios.id_formulario
		LEFT JOIN $valores_formularios ON $valores_formularios.id_formulario_rel_proyecto = $formulario_rel_proyecto.id 
		WHERE
			$criterios.id_cliente = $id_cliente AND
			$criterios.id_proyecto = $id_proyecto AND
			$criterios.deleted = 0 AND
			$calculos.deleted = 0 AND
			$formulario_rel_proyecto.deleted = 0 AND
			$valores_formularios.deleted = 0 AND 
			$formularios.deleted = 0 AND 

			$where_date
		
			$valores_formularios.datos->>'$.id_categoria' = $calculos.id_categoria AND 
		
			(($criterios.id_campo_fc IS NOT NULL AND JSON_EXTRACT($valores_formularios.datos, CONCAT('$.\"', $criterios.id_campo_fc, '\"')) = $calculos.criterio_fc) OR 
			
			($criterios.tipo_by_criterio->>'$.id_campo_fc' != 'null' AND JSON_EXTRACT($valores_formularios.datos, CONCAT('$.\"', $criterios.tipo_by_criterio->>'$.id_campo_fc', '\"')) = $calculos.criterio_fc) OR 
		
			($criterios.id_campo_fc IS NULL AND $criterios.tipo_by_criterio->>'$.id_campo_fc' = 'null') OR 
			
			($criterios.id_campo_fc IS NULL AND $criterios.tipo_by_criterio IS NULL)) 
		
		ORDER BY $criterios.id, $calculos.id, $valores_formularios.id) AS ca 
		LEFT JOIN $asignaciones ON $asignaciones.id_criterio = ca.id_criterio
		LEFT JOIN $asignaciones_combinaciones ON $asignaciones_combinaciones.id_asignacion = $asignaciones.id 
		WHERE $asignaciones.deleted=0 AND $asignaciones_combinaciones.deleted=0
		$where_sp";
		
        return $this->db->query($sql);

    }

	function get_factores($id_proyecto) {
		
		$proyectos = $this->db->dbprefix('projects');
        $proyecto_rel_huellas = $this->db->dbprefix('proyecto_rel_huellas');
		$materiales_proyecto = $this->db->dbprefix('materiales_proyecto');
		$fc_db = getFCBD();
        
        $this->db->query('SET SQL_BIG_SELECTS=1');

		$sql = "SELECT * 
		FROM $fc_db.factores 
		WHERE 
		FIND_IN_SET(id_formato_huella, (SELECT REPLACE(REPLACE(REPLACE(id_formato_huella, '[', ''), ']', ''), '\"', '') AS id_formato_huella FROM $proyectos WHERE id = $id_proyecto)) AND 
		id_huella IN (SELECT id_huella FROM $proyecto_rel_huellas WHERE id_proyecto = $id_proyecto AND deleted = 0) AND 
		id_material IN (SELECT id_material FROM $materiales_proyecto WHERE id_proyecto = $id_proyecto AND deleted = 0 ORDER BY id_material) AND
		deleted = 0";
		
		return $this->db->query($sql);

	}

	function get_transformaciones($id_proyecto = 0) {
		
		//$form_table = $this->db->dbprefix('formularios');
        //$form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
		//$form_values_table = $this->db->dbprefix('valores_formularios');
        
		//$this->load->database("mimaacp_test", TRUE);

		$fc_db = getFCBD();

        $this->db->query('SET SQL_BIG_SELECTS=1');

		$sql = "SELECT 
		huellas.id,
		huellas.nombre,
		huellas.id_tipo_unidad,
		huellas.id_unidad,
		module_footprint_units.id_unidad AS id_unidad_destino,
		conversion.transformacion 
		FROM proyecto_rel_huellas 
		LEFT JOIN $fc_db.huellas ON huellas.id = proyecto_rel_huellas.id_huella 
		LEFT JOIN module_footprint_units ON module_footprint_units.id_proyecto = proyecto_rel_huellas.id_proyecto AND module_footprint_units.id_tipo_unidad = huellas.id_tipo_unidad 
		LEFT JOIN $fc_db.conversion ON conversion.id_tipo_unidad = huellas.id_tipo_unidad AND conversion.id_unidad_origen = huellas.id_unidad AND conversion.id_unidad_destino = module_footprint_units.id_unidad 
		WHERE 
		proyecto_rel_huellas.id_proyecto = $id_proyecto AND 
		proyecto_rel_huellas.deleted = 0 AND
		huellas.deleted = 0 AND 
		module_footprint_units.deleted = 0;";
		
		return $this->db->query($sql);

	}

}
