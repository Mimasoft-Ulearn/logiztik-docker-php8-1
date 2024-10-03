<?php

class AC_Beneficiaries_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_beneficiarios';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
		
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $beneficiarios_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $beneficiarios_table.id_cliente = $id_cliente";
        }
		
		$sexo = get_array_value($options, "sexo");
        if ($sexo) {
            $where .= " AND $beneficiarios_table.sexo = '$sexo'";
        }
		
		$sociedad = get_array_value($options, "sociedad");
        if ($sociedad) {
            $where .= " AND $beneficiarios_table.sociedad = '$sociedad'";
        }
		
		$discapacidad = get_array_value($options, "discapacidad");
        if ($discapacidad) {
            $where .= " AND $beneficiarios_table.discapacidad = '$discapacidad'";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $beneficiarios_table.* FROM $beneficiarios_table WHERE";
		$sql .= " $beneficiarios_table.deleted=0";
		$sql .= " $where";
		$sql .= " ORDER BY $beneficiarios_table.created DESC";
        
        return $this->db->query($sql);
		
    }

    // Cantidad de colaboradores por genero que tenga contrato vigente para el año $year
    function amount_per_gender($year){
		
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $sql = "SELECT count(*) AS cantidad, sexo FROM $beneficiarios_table 
        WHERE deleted = 0 
        AND year(fecha_inicio_contrato) <= $year
        AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)  
        GROUP BY sexo";

        return $this->db->query($sql);
    }

    // Porcentaje de mujeres por area de personal
    function percentage_by_personnel_area($year, $area){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $sql = "SELECT COUNT(*) AS cantidad FROM $beneficiarios_table 
        WHERE deleted = 0 
        AND area_de_personal = '$area'
        AND year(fecha_inicio_contrato) <= $year
        AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)  
        ";

        $todos = (int)$this->db->query($sql)->result()[0]->cantidad;

        $sql = "SELECT COUNT(*) AS cantidad FROM $beneficiarios_table 
        WHERE deleted = 0 
        AND  area_de_personal = '$area' AND  sexo = 'F'
        AND year(fecha_inicio_contrato) <= $year
        AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)";

        $mujeres = (int)$this->db->query($sql)->result()[0]->cantidad;
        
        $porcentaje = $todos != 0 ? (($mujeres * 100) / $todos) : 0;

        return $porcentaje;
    }

    // Obtener cargos (parte de campo subdivision)
    function get_cargos_subdivision(){
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $sql = "SELECT DISTINCT(cargo) FROM $beneficiarios_table WHERE cargo IS NOT NULL";

        return $this->db->query($sql)->result();
    }

    // Obtener el porcentaje de mujeres por cargo por año
    function percentage_by_cargo($years){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $cargos = $this->get_cargos_subdivision();

        $sql = '';
        $cant_elements = count($years) * count($cargos);
        $cont = 0;

        if($cant_elements > 0){ // Si no se han ingresado cargos no se debe executar la query

            foreach ($years as $year) {
                
                foreach ($cargos as $cargo) {
                    
                    $sql .= "SELECT '$year' AS year, '$cargo->cargo' AS cargo,
                    cant_mujeres, total, ((cantidades.cant_mujeres * 100) / cantidades.total) AS porcentaje
                    FROM (
                        SELECT (
                            SELECT COUNT(*)
                            FROM $beneficiarios_table  
                            WHERE deleted = 0
                            AND year(fecha_inicio_contrato) <= $year
                            AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
                            AND cargo = '$cargo->cargo'
                            AND sexo = 'F'
                        ) AS cant_mujeres,
                        (
                            SELECT COUNT(*)
                            FROM $beneficiarios_table  
                            WHERE deleted = 0
                            AND year(fecha_inicio_contrato) <= $year 
                            AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
                            AND cargo = '$cargo->cargo'
                        ) AS total
                    ) AS cantidades";

                    $cont++;
                    if($cont < $cant_elements){
                        $sql .= " UNION ALL ";
                    }
                    
                }
            }
            return $this->db->query($sql)->result();
            // $result = $this->db->query($sql)->result();
            // echo '<pre>';var_dump($result);exit;
            // echo $sql;exit;
        }else{
            return null;
        }
    }

    // Obtener sucursales (parte de campo subdivision)
    function get_sucursales_subdivision(){
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $sql = "SELECT DISTINCT(sucursal) FROM $beneficiarios_table WHERE sucursal IS NOT NULL";

        return $this->db->query($sql)->result();
    }

    // Obtener el porcentaje de mujeres por sucursal por año
    function percentage_by_branch($years){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $sucursales = $this->get_sucursales_subdivision();

        $sql = '';
        $cant_elements = count($years) * count($sucursales);
        $cont = 0;

        if($cant_elements > 0){ // Si no se han ingresado sucursales no se debe executar la query

            foreach ($years as $year) {
                
                foreach ($sucursales as $sucursal) {
                    
                    $sql .= "SELECT '$year' AS year, '$sucursal->sucursal' AS sucursal,
                    cant_mujeres, total, ((cantidades.cant_mujeres * 100) / cantidades.total) AS porcentaje
                    FROM (
                        SELECT (
                            SELECT COUNT(*)
                            FROM $beneficiarios_table  
                            WHERE deleted = 0
                            AND year(fecha_inicio_contrato) <= $year
                            AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
                            AND sucursal = '$sucursal->sucursal'
                            AND sexo = 'F'
                        ) AS cant_mujeres,
                        (
                            SELECT COUNT(*)
                            FROM $beneficiarios_table  
                            WHERE deleted = 0
                            AND year(fecha_inicio_contrato) <= $year 
                            AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
                            AND sucursal = '$sucursal->sucursal'
                        ) AS total
                    ) AS cantidades";

                    $cont++;
                    if($cont < $cant_elements){
                        $sql .= " UNION ALL ";
                    }
                    
                }
            }
            // echo $sql;exit;
            return $this->db->query($sql)->result();
        }else{
            return null;
        }


    }

    // Retorna la cantidad de colaboradores que pertenecen a una generación que tienen contrato para el año $year
    function amount_by_generation($year){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $sql = "SELECT (
            SELECT COUNT(*) FROM $beneficiarios_table 
            WHERE YEAR(fecha_nacimiento) > 1946 AND  YEAR(fecha_nacimiento) < 1964
            AND year(fecha_inicio_contrato) <= $year
            AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
        ) AS baby_boomers,
        (
            SELECT COUNT(*) FROM $beneficiarios_table 
            WHERE YEAR(fecha_nacimiento) > 1965 AND  YEAR(fecha_nacimiento) < 1980
            AND year(fecha_inicio_contrato) <= $year
            AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
        ) AS generation_x,
        (
            SELECT COUNT(*) FROM $beneficiarios_table 
            WHERE YEAR(fecha_nacimiento) > 1981 AND  YEAR(fecha_nacimiento) < 1996
            AND year(fecha_inicio_contrato) <= $year
            AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
        ) AS millenials,
        (
            SELECT COUNT(*) FROM $beneficiarios_table 
            WHERE YEAR(fecha_nacimiento) > 1997 AND  YEAR(fecha_nacimiento) < 2013
            AND year(fecha_inicio_contrato) <= $year
            AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
        ) AS generation_z";
        
        // echo $sql; exit;
        return $this->db->query($sql)->result();
    }

    // Porcentaje de personas cercanas a la edad de jubilación para el año $year
    function posible_retirement($year){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $sql = "SELECT cantidades.cant_prox_jubilar, cantidades.total, ((cantidades.cant_prox_jubilar * 100)/cantidades.total) AS porcentaje
        FROM(
            SELECT
            (
                SELECT COUNT(*)
                FROM $beneficiarios_table  
                WHERE deleted = 0
                AND  ($year - Year(fecha_nacimiento)) >= 59
                AND year(fecha_inicio_contrato) <= $year AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL) 
            ) AS cant_prox_jubilar,
            (
                SELECT COUNT(*)
                FROM $beneficiarios_table  
                WHERE deleted = 0
                AND year(fecha_inicio_contrato) <= $year AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL) 
            ) AS total
        ) AS cantidades;";
        // echo $sql;exit;
        return $this->db->query($sql)->result();
    }

    // Obtener numero de personas contratadas sobre 45 vs numero objetivo
    function hired_over_45($year){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $sql = "SELECT COUNT(*) as cant
        FROM $beneficiarios_table  
        WHERE deleted = 0
        AND  ($year - Year(fecha_nacimiento)) >= 45
        AND year(fecha_inicio_contrato) <= $year AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL) ";

        $cant = $this->db->query($sql)->result()[0]->cant;

        // Se obienten el valor objetivo del año $year para el grafico
        $id_cliente = $this->login_user->client_id;

        $options = array(
            'id_cliente' => $id_cliente,
            'grafico' => 'hired_over_45',
            'deleted' => 0
        );

        $objetivos_data = $this->AC_Feeders_beneficiary_objectives_model->get_all_where($options)->result();
        $objetivo = json_decode($objetivos_data[0]->objetivos);

        $cant_objetivo = $objetivo->$year ? (int) $objetivo->$year : 0; 

        // Calculo del porcentaje
        $porcentaje = $cant_objetivo != 0 ? ($cant * 100)/$cant_objetivo : 0; // Evitar division por cero

        return array(
            'cant' => $cant, 
            'cant_objetivo' => $cant_objetivo,
            'porcentaje' => $porcentaje
        );
    }

    // Obtener discapacidades
    function get_discapacidades(){
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $sql = "SELECT DISTINCT(discapacidad) FROM $beneficiarios_table WHERE discapacidad IS NOT NULL";

        return $this->db->query($sql)->result();
    }

    // Obtener numero de colaboradores con discapacidad separados por sucursal y año.
    function get_disability_by_branch($years){
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $cant_elements = count($years);
        $cont = 0;
        $sql = '';

        if($cant_elements > 0){ // Si no se han ingresado discapacidades no se debe executar la query
            foreach ($years as $year) {    
            
                $sql .= "SELECT COUNT(*) AS cantidad, $year AS year, discapacidad, sucursal
                        FROM $beneficiarios_table
                        WHERE deleted = 0
                        AND sucursal IS NOT NULL
                        AND discapacidad != 'No indica'
                        AND year(fecha_inicio_contrato) <= $year
                        AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
                        GROUP BY sucursal, discapacidad";

                $cont++;
                if($cont < $cant_elements){
                    $sql .= " UNION ALL ";
                }
            }
            return $this->db->query($sql)->result();
            // $res = $this->db->query($sql)->result();
            // echo '<pre>'; var_dump($res);exit;
            // echo $sql;exit;
        }else{
            return null;
        }
    }

    // Obtener numero de colaboradores con discapacidad pertenecientes a sociedades "Comercial Kaufmann" y "Kaufmann S.A. Vehículos Motorizados"
    // IDs 1 y 2 "Comercial Kaufmann" y "Kaufmann S.A. Vehículos Motorizados"
    function law_ck_ksa($year){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');
        
        $sql = "SELECT COUNT(*) as cant
        FROM $beneficiarios_table  
        WHERE deleted = 0
        AND discapacidad != 'No indica'
        AND sociedad_desc IN (1, 2)
        AND year(fecha_inicio_contrato) <= $year AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL) ";

        $cant = $this->db->query($sql)->result()[0]->cant;
       
        // Se obienten el valor objetivo del año $year para el grafico
        $id_cliente = $this->login_user->client_id;

        $options = array(
            'id_cliente' => $id_cliente,
            'grafico' => 'CK_KSA_law',
            'deleted' => 0
        );

        $objetivos_data = $this->AC_Feeders_beneficiary_objectives_model->get_all_where($options)->result();
        $objetivo = json_decode($objetivos_data[0]->objetivos);

        $cant_objetivo = $objetivo->$year ? (int) $objetivo->$year : 0; 

        // Calculo del porcentaje
        $porcentaje = $cant_objetivo != 0 ? ($cant * 100)/$cant_objetivo : 0; // Evitar division por cero

        return array(
            'cant' => $cant, 
            'cant_objetivo' => $cant_objetivo,
            'porcentaje' => $porcentaje
        );
    }

    // Obtener numero de colaboradores con discapacidad pertenecientes a sociedad "Comercial Motores de los Andes S.A."
    // ID 4 "Comercial Motores de los Andes S.A."
    function law_comercial_andes_motor($year){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');
        
        $sql = "SELECT COUNT(*) as cant
        FROM $beneficiarios_table  
        WHERE deleted = 0
        AND discapacidad != 'No indica'
        AND sociedad_desc = 4
        AND year(fecha_inicio_contrato) <= $year AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL) ";

        $cant = $this->db->query($sql)->result()[0]->cant;

        
        // Se obienten el valor objetivo del año $year para el grafico
        $id_cliente = $this->login_user->client_id;

        $options = array(
            'id_cliente' => $id_cliente,
            'grafico' => 'comercial_andes_motor_law',
            'deleted' => 0
        );

        $objetivos_data = $this->AC_Feeders_beneficiary_objectives_model->get_all_where($options)->result();
        $objetivo = json_decode($objetivos_data[0]->objetivos);
        $cant_objetivo = $objetivo->$year ? (int) $objetivo->$year : 0; 
        
        // Calculo del porcentaje
        $porcentaje = $cant_objetivo != 0 ? ($cant * 100)/$cant_objetivo : 0; // Evitar division por cero

        return array(
            'cant' => $cant, 
            'cant_objetivo' => $cant_objetivo,
            'porcentaje' => $porcentaje
        );
    }

    // Obtener numero de colaboradores con discapacidad pertenecientes a sociedad "Comercial Motores de los Andes S.A."
    // ID 5 "Motores de Los Andes Vehículos Motorizados SPA"
    function law_andes_motor($year){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');
        
        $sql = "SELECT COUNT(*) as cant
        FROM $beneficiarios_table  
        WHERE deleted = 0
        AND discapacidad != 'No indica'
        AND sociedad_desc = 5
        AND year(fecha_inicio_contrato) <= $year AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL) ";

        $cant = $this->db->query($sql)->result()[0]->cant;

         // Se obienten el valor objetivo del año $year para el grafico
         $id_cliente = $this->login_user->client_id;

         $options = array(
             'id_cliente' => $id_cliente,
             'grafico' => 'andes_motor_law',
             'deleted' => 0
         );
 
         $objetivos_data = $this->AC_Feeders_beneficiary_objectives_model->get_all_where($options)->result();
         $objetivo = json_decode($objetivos_data[0]->objetivos);
         $cant_objetivo = $objetivo->$year ? (int) $objetivo->$year : 0; 
         
         // Calculo del porcentaje
        $porcentaje = $cant_objetivo != 0 ? ($cant * 100)/$cant_objetivo : 0; // Evitar division por cero

        return array(
            'cant' => $cant, 
            'cant_objetivo' => $cant_objetivo,
            'porcentaje' => $porcentaje
        );
    }

    // Obtener numero de colaboradores con discapacidad "Intelectual"
    function law_tea($year){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');
        
        $sql = "SELECT COUNT(*) as cant
        FROM $beneficiarios_table  
        WHERE deleted = 0
        AND discapacidad = 'Intelectual'
        AND year(fecha_inicio_contrato) <= $year AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL) ";

        $cant = $this->db->query($sql)->result()[0]->cant;

         // Se obienten el valor objetivo del año $year para el grafico
         $id_cliente = $this->login_user->client_id;

         $options = array(
             'id_cliente' => $id_cliente,
             'grafico' => 'tea_law',
             'deleted' => 0
         );
 
         $objetivos_data = $this->AC_Feeders_beneficiary_objectives_model->get_all_where($options)->result();
         $objetivo = json_decode($objetivos_data[0]->objetivos);
         $cant_objetivo = $objetivo->$year ? (int) $objetivo->$year : 0; 
         
         // Calculo del porcentaje
        $porcentaje = $cant_objetivo != 0 ? ($cant * 100)/$cant_objetivo : 0; // Evitar division por cero

        return array(
            'cant' => $cant, 
            'cant_objetivo' => $cant_objetivo,
            'porcentaje' => $porcentaje
        );
    }

    // Devuelve la cantidad de colaboradores en las Provincias de Santiago vs las de fuera de Santiago
    function cant_regiones_stgo($year){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $sql = "SELECT 
            (
                SELECT COUNT(*)
                FROM $beneficiarios_table 
                WHERE deleted = 0
                AND provincia IN ('Chacabuco', 'Cordillera', 'Maipo', 'Melipilla', 'Santiago', 'Talagante')
                AND year(fecha_inicio_contrato) <= $year
                AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
            ) AS cant_santiago,
            (
                SELECT COUNT(*)
                FROM $beneficiarios_table 
                WHERE deleted = 0
                AND provincia NOT IN ('Chacabuco', 'Cordillera', 'Maipo', 'Melipilla', 'Santiago', 'Talagante')
                AND year(fecha_inicio_contrato) <= $year
                AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
            ) AS cant_regiones;";
        
        return $this->db->query($sql)->result();
    }

    // Se obtienen la cantidad de chilenos, extranjeros y el porcentaje de extranjeros. (Datos usados en visualización principal de un gráfico)
    // Tambien se obtiene el numero de extranjeros por cada pais.(Datos para drilldown usados en visualización interior de un gráfico)
    function cant_nacionalidad($year){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $sql = "SELECT 
        (
            SELECT COUNT(*)
            FROM $beneficiarios_table 
            WHERE deleted = 0
            AND nacionalidad = 'Chile'
            AND year(fecha_inicio_contrato) <= $year
            AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
        ) AS cant_chilenos,
        (
            SELECT COUNT(*)
            FROM $beneficiarios_table 
            WHERE deleted = 0
            AND nacionalidad != 'Chile'
            AND year(fecha_inicio_contrato) <= $year
            AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
        ) AS cant_extranjeros,
        (
            SELECT COUNT(*)
            FROM $beneficiarios_table 
            WHERE deleted = 0
            AND year(fecha_inicio_contrato) <= $year
            AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
        ) AS todos";


        $result = $this->db->query($sql)->result()[0];
        
        $cant_chilenos = $result->cant_chilenos;
        $cant_extranjeros = $result->cant_extranjeros;
        $todos = $result->todos;

        $porc_extranjeros = $todos != 0 ?  ($cant_extranjeros * 100) / $todos : 0;
        
        // Datos drilldown
        $nacionalidades = $this->AC_Beneficiaries_model->get_dropdown_nacionalidad();
        unset($nacionalidades['Chile']);
		array_shift($nacionalidades);

        $array_nacionalidades = array_values($nacionalidades);
      
        $cant_nac = count($array_nacionalidades);
        $cont = 0;
        $sql = '';
        foreach ($array_nacionalidades as $nacionalidad) {
            
            $sql .= "SELECT COUNT(*) AS cant, '$nacionalidad' AS nacion
            FROM $beneficiarios_table
            WHERE deleted = 0
            AND nacionalidad = '$nacionalidad'
            AND year(fecha_inicio_contrato) <= $year
            AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)";

            $cont++;
            if($cont < $cant_nac){
                $sql .= " UNION ALL ";
            }
        }

        $res_extranjeros = $this->db->query($sql)->result();

        $drilldown = array();
        foreach ($res_extranjeros as $res) {
            $drilldown[] = array("$res->nacion", (int)$res->cant);
        }
                
        return array(
            'cant_chilenos' => $cant_chilenos,
            'cant_extranjeros' => $cant_extranjeros,
            'porc_extranjeros' => $porc_extranjeros,
            'drilldown' => $drilldown
        );

    }

    // Se obtiene el porcentaje de extranjeros trabajando en cada area para el año especificado
    function cant_nacionalidad_por_area_personal($year, $areas){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $cant_areas = count($areas);
        $cont = 0;
        foreach ($areas as $area) {    
            
            $sql .= "SELECT $year AS year, '$area' AS area,
            (
                SELECT COUNT(*) AS cantidad
                FROM $beneficiarios_table
                WHERE deleted = 0
                AND area_de_personal = '$area'
                AND year(fecha_inicio_contrato) <= $year
                AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
            ) AS todos,
            (
                SELECT COUNT(*) AS cantidad
                FROM $beneficiarios_table
                WHERE deleted = 0
                AND nacionalidad != 'Chile'
                AND area_de_personal = '$area'
                AND year(fecha_inicio_contrato) <= $year
                AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
            ) AS extranjeros";
        
            $cont++;
            if($cont < $cant_areas){
                $sql .= " UNION ALL ";
            }
        }

        $results = $this->db->query($sql)->result();
        // echo '<pre>'; var_dump($results);exit;

        $array_data = array();
        foreach ($results as $result) {
            $todos = $result->todos;
            $extranjeros = $result->extranjeros;
            $porcentaje = $todos != 0 ? ($extranjeros * 100) / $todos : 0;
           
            $array_data[] = array(
                "year" => $result->year,
                'area' => $result->area,
                'porcentaje' => $porcentaje
            );
        }

        return $array_data;

    }

    // Se obtiene el porcentaje de extranjeros trabajando en cada cargo para el año especificado
    function cant_nacionalidad_por_cargo_subdiv($year, $cargos){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $cant_areas = count($cargos);
        $cont = 0;
        $sql = '';

        if(count($cargos)){
            foreach ($cargos as $cargo) {    
                
                $sql .= "SELECT $year AS year, '$cargo->cargo' AS cargo,
                (
                    SELECT COUNT(*) AS cantidad
                    FROM $beneficiarios_table
                    WHERE deleted = 0
                    AND cargo = '$cargo->cargo'
                    AND year(fecha_inicio_contrato) <= $year
                    AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
                ) AS todos,
                (
                    SELECT COUNT(*) AS cantidad
                    FROM $beneficiarios_table
                    WHERE deleted = 0
                    AND nacionalidad != 'Chile'
                    AND cargo = '$cargo->cargo'
                    AND year(fecha_inicio_contrato) <= $year
                    AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
                ) AS extranjeros";
            
                $cont++;
                if($cont < $cant_areas){
                    $sql .= " UNION ALL ";
                }
            }

            $results = $this->db->query($sql)->result();
            // echo '<pre>'; var_dump($results);exit;
        }

        $array_data = array();
        foreach ($results as $result) {
            $todos = $result->todos;
            $extranjeros = $result->extranjeros;
            $porcentaje = $todos != 0 ? ($extranjeros * 100) / $todos : 0;
           
            $array_data[] = array(
                "year" => $result->year,
                'area' => $result->cargo,
                'porcentaje' => $porcentaje
            );
        }

        return $array_data;

    }

     // Se obtiene el porcentaje de extranjeros trabajando en cada sucursal para el año especificado
     function cant_nacionalidad_por_sucursal($year, $sucursales){
        
        $beneficiarios_table = $this->db->dbprefix('ac_beneficiarios');

        $cant_areas = count($sucursales);
        $cont = 0;

        if(count($sucursales)){
            foreach ($sucursales as $sucursal) {    
                
                $sql .= "SELECT $year AS year, '$sucursal->sucursal' AS sucursal,
                (
                    SELECT COUNT(*) AS cantidad
                    FROM $beneficiarios_table
                    WHERE deleted = 0
                    AND sucursal = '$sucursal->sucursal'
                    AND year(fecha_inicio_contrato) <= $year
                    AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
                ) AS todos,
                (
                    SELECT COUNT(*) AS cantidad
                    FROM $beneficiarios_table
                    WHERE deleted = 0
                    AND nacionalidad != 'Chile'
                    AND sucursal = '$sucursal->sucursal'
                    AND year(fecha_inicio_contrato) <= $year
                    AND ( year(fecha_fin_contrato) >= $year OR year(fecha_fin_contrato) IS NULL)
                ) AS extranjeros";
            
                $cont++;
                if($cont < $cant_areas){
                    $sql .= " UNION ALL ";
                }
            }
            $results = $this->db->query($sql)->result();
        }

        // echo '<pre>'; var_dump($results);exit;

        $array_data = array();
        foreach ($results as $result) {
            $todos = $result->todos;
            $extranjeros = $result->extranjeros;
            $porcentaje = $todos != 0 ? ($extranjeros * 100) / $todos : 0;
           
            $array_data[] = array(
                "year" => $result->year,
                'area' => $result->sucursal,
                'porcentaje' => $porcentaje
            );
        }

        return $array_data;

    }


    // SEXO
    function get_dropdown_sexos(){
        return array(
            "" => "-",
            "M" => "M",
            "F" => "F",
            "Otro" => lang('other')
        );
    }

    // SOCIEDAD DESC
    function get_dropdown_sociedades($id_cliente){
        $sociedades = $this->AC_Feeders_societies_model->get_all_where(array('id_cliente' => $id_cliente))->result();

        $dropdown_sociedad_desc = array("" => "-");
        foreach($sociedades as $sociedad){
            $dropdown_sociedad_desc[$sociedad->id] = $sociedad->nombre_sociedad;
        }
        return $dropdown_sociedad_desc;
    }

    // ESTADO
    function get_dropdown_status(){
        return array(
            "" => "-",
            "Activo" => lang('active'),
            "Inactivo" => lang('inactive')
        );
    }


    // TIPO CONTRATO
    function get_dropdown_tipo_contrato(){
        return array(
        "" => "-",
        "Plazo fijo" => lang('fixed_term'),
        "Plazo indefinido" => lang('indefinite_term')
        );
    }

    // ESTADO CIVIL
    function get_dropdown_estado_civil(){
        return array(
            "" => "-",
            "Casado/a" => lang("married"),
            "Conviviente" => lang("cohabitant"),
            "Divorciado/a" => lang("divorced"),
            "Soltero/a" => lang("single"),
            "Unión Civil" => lang("civil_union"),
            "Viudo/a" => lang("widower")
        );
    }

    // ÁREA DE PERSONAL
    function get_dropdown_area_de_personal(){
        return array(
            "" => "-",
            "Jefaturas" => lang("headquarters"),
            "Operativos" => lang("operatives"),
            "Administrativos" => lang("administrative"),
            "Profesionales" => lang("professionals"),
            "Ventas" => lang("sales"),
            "Ejecutivos" => lang("executives")
        );
    }

    // NACIONALIDAD
    function get_dropdown_nacionalidad(){
        return array(
            "" => "-",
            "Chile" => "Chile",
            "Argentina" => "Argentina",
            "Brasil" => "Brasil",
            "Bolivia (Plurinational State of)" => "Bolivia (Plurinational State of)",
            "China" => "China",
            "Colombia" => "Colombia",
            "Cuba" => "Cuba",
            "Netherlands" => "Netherlands",
            "Perú" => "Perú",
            "Uruguay" => "Uruguay",
            "Venezuela" => "Venezuela"
        );
    }

    // DISCAPACIDAD
    function get_dropdown_discapacidad(){
        return array(
            "No indica" => "No indica",
            "Física" => "Física",
            "Sensorial auditiva" => "Sensorial auditiva",
            "Sensorial visual" => "Sensorial visual",
            "Intelectual" => "Intelectual",
            "Visceral" => "Visceral",
            "Psíquica" => "Psíquica"
        );
    }


    // PROVINCIA
    function get_dropdown_provincia(){
        return array(
            "" => "-",
            "Santiago" => "Santiago",
            "Talca" => "Talca",
            "Concepción" => "Concepción",
            "Bío- Bío" => "Bío- Bío",
            "Ñuble" => "Ñuble",
            "Osorno" => "Osorno",
            "Antofagasta" => "Antofagasta",
            "Curicó" => "Curicó",
            "Maipo" => "Maipo",
            "El Loa" => "El Loa",
            "Arauco" => "Arauco",
            "Cachapoal" => "Cachapoal",
            "Valparaíso" => "Valparaíso",
            "Talagante" => "Talagante",
            "Copiapó" => "Copiapó",
            "Iquique" => "Iquique",
            "Magallanes" => "Magallanes",
            "Linares" => "Linares",
            "Cautín" => "Cautín",
            "Elqui" => "Elqui",
            "Cordillera" => "Cordillera",
            "Valdivia" => "Valdivia",
            "Chacabuco" => "Chacabuco",
            "Colchagua" => "Colchagua",
            "Choapa" => "Choapa",
            "San Felipe" => "San Felipe",
            "Llanquihue" => "Llanquihue",
            "San Antonio" => "San Antonio",
            "Arica" => "Arica",
            "Melipilla" => "Melipilla",
            "Quillota" => "Quillota",
            "Los Andes" => "Los Andes",
            "Chiloé" => "Chiloé",
            "Coyhaique" => "Coyhaique",
            "Ranco" => "Ranco",
            "Marga Marga" => "Marga Marga",
            "Ultima Esperanza" => "Ultima Esperanza",
            "Limari" => "Limari",
            "Huasco" => "Huasco",
            "Malleco" => "Malleco",
            "Cauquenes" => "Cauquenes",
        );
    }


}
