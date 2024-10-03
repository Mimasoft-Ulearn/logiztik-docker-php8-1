<?php

class Api_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'users_api_session';
        parent::__construct($this->table);
    }

    // RECIBE EL ID DE USUARIO CLIENTE POR PARAMETRO Y RETORNA LOS DATOS DE LOS PROYECTOS EN LOS QUE ES MIEMBRO
    function get_projects_of_member($id_usuario = NULL) {
        $clients_table = $this->db->dbprefix('clients');
        $projects_table = $this->db->dbprefix('projects');
        $projects_members_table = $this->db->dbprefix('project_members');
        $users_table = $this->db->dbprefix('users');
        
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        
        $sql = "SELECT $projects_table.* 
        FROM $projects_table 
        LEFT JOIN $projects_members_table ON $projects_table.id = $projects_members_table.project_id 
        LEFT JOIN $users_table ON $projects_members_table.user_id = $users_table.id 
        WHERE $projects_members_table.user_id = $id_usuario AND 
        $projects_table.deleted = 0 AND 
        $projects_members_table.deleted = 0 AND 
        $users_table.deleted = 0
		ORDER BY $projects_table.id ";

        //echo $sql;
        return $this->db->query($sql);
    }

    function get_values_of_form($options = array()/*, $page = 1*/) {
        $valores_formularios_table = $this->db->dbprefix('valores_formularios');
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');
		
		// $end_page = ($page * 5);
		// $start_page = $end_page - 5;
		// $limit = "LIMIT $start_page, $end_page";

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $valores_formularios_table.id=$id";
        }
		
		$id_formulario = get_array_value($options, "id_formulario");
        if ($id_formulario) {
            $where .= " AND $formulario_rel_proyecto_table.id_formulario=$id_formulario";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $formulario_rel_proyecto_table.id_proyecto=$id_proyecto";
        }
		
		$datos = get_array_value($options, "datos");
        if ($datos) {
            $where .= " AND $valores_formularios_table.datos='$datos'";
        }

        $first_date = get_array_value($options, "first_date");
        $last_date = get_array_value($options, "last_date");

        if ($first_date && $last_date) {
            $where .= " AND $valores_formularios_table.datos->'$.fecha' >= '$first_date' AND $valores_formularios_table.datos->'$.fecha' <= '$last_date'";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $valores_formularios_table.created_by=$created_by";
        }
		
		$modified_by = get_array_value($options, "modified_by");
        if ($modified_by) {
            $where .= " AND $valores_formularios_table.modified_by=$modified_by";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $formulario_rel_proyecto_table.id_formulario, $formulario_rel_proyecto_table.id_proyecto, $valores_formularios_table.* FROM $valores_formularios_table, $formulario_rel_proyecto_table WHERE";
		$sql .= " $valores_formularios_table.deleted=0 AND $formulario_rel_proyecto_table.deleted=0";
		$sql .= " AND $valores_formularios_table.id_formulario_rel_proyecto = $formulario_rel_proyecto_table.id";
        $sql .= " $where";
        // $sql .= " ORDER BY $valores_formularios_table.id DESC $limit";
        $sql .= " ORDER BY $valores_formularios_table.id DESC";
		
        return $this->db->query($sql);
    }

    function get_total_form_values($options = array()){

		$projects_table = $this->db->dbprefix('projects');
		$formularios_table = $this->db->dbprefix('formularios');
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');
        $valores_formularios_table = $this->db->dbprefix('valores_formularios');

        $fc_db = getFCBD();
		$materials_table = $this->load->database($fc_db, TRUE)->dbprefix('materiales');
		$categories_table = $this->load->database($fc_db, TRUE)->dbprefix('categorias');
        $material_rel_categoria_table = $this->load->database($fc_db, TRUE)->dbprefix('material_rel_categoria');

        $where = "";
        $id_formulario = get_array_value($options, "id_formulario");
        if($id_formulario){
            $where .= " AND $formulario_rel_proyecto_table.id_formulario=$id_formulario";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if($id_proyecto){
            $where .= " AND $formulario_rel_proyecto_table.id_proyecto=$id_proyecto";
        }

        $first_date = get_array_value($options, "first_date");
        $last_date = get_array_value($options, "last_date");

        if($first_date && $last_date){
            $where .= " AND $valores_formularios_table.datos->'$.fecha' >= '$first_date' AND $valores_formularios_table.datos->'$.fecha' <= '$last_date'";
        }

        $group_by = "";
        $group_by_option = get_array_value($options, "group_by"); // EJ: zone || form || material || category
        if($group_by_option){
            if($group_by_option == "zone"){
                $group_by = " GROUP BY $projects_table.id ";
            } elseif($group_by_option == "form"){
                $group_by = " GROUP BY $formularios_table.id ";
            } elseif($group_by_option == "material"){
                $group_by = " GROUP BY $fc_db.$materials_table.id ";
            } elseif($group_by_option == "category"){
                $group_by = " GROUP BY $fc_db.$categories_table.id ";
            }
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $projects_table.id AS id_project, $projects_table.title AS project_name, $formularios_table.id AS id_form, $formularios_table.nombre AS form_name, $formularios_table.flujo AS form_flujo, $fc_db.$materials_table.id AS id_material, $fc_db.$materials_table.nombre AS material_name, $fc_db.$categories_table.id AS id_category, $fc_db.$categories_table.nombre AS category_name, SUM($valores_formularios_table.datos->'$.unidad_residuo') AS total";
        
        $sql .= " FROM $projects_table";
       
        $sql .= " INNER JOIN $formulario_rel_proyecto_table ON $projects_table.id = $formulario_rel_proyecto_table.id_proyecto";
        $sql .= " INNER JOIN $formularios_table ON $formulario_rel_proyecto_table.id_formulario = $formularios_table.id";
        $sql .= " INNER JOIN $valores_formularios_table ON $formulario_rel_proyecto_table.id = $valores_formularios_table.id_formulario_rel_proyecto";
        // $sql .= " INNER JOIN $fc_db.$categories_table ON $valores_formularios_table.datos->'$.id_categoria' = $fc_db.$categories_table.id";
        $sql .= " INNER JOIN $fc_db.$categories_table ON $valores_formularios_table.id_categoria = $fc_db.$categories_table.id";
        $sql .= " INNER JOIN $fc_db.$material_rel_categoria_table ON $fc_db.$categories_table.id = $fc_db.$material_rel_categoria_table.id_categoria";
        $sql .= " INNER JOIN $fc_db.$materials_table ON $fc_db.$material_rel_categoria_table.id_material = $fc_db.$materials_table.id";

        $sql .= " WHERE $projects_table.deleted = 0";
        $sql .= " AND $formulario_rel_proyecto_table.deleted = 0";
        $sql .= " AND $formularios_table.deleted = 0";
        $sql .= " AND $valores_formularios_table.deleted = 0";
        $sql .= " AND $fc_db.$categories_table.deleted = 0";
        $sql .= " AND $fc_db.$material_rel_categoria_table.deleted = 0";
        $sql .= " AND $fc_db.$materials_table.deleted = 0";
        $sql .= " AND $formularios_table.id_tipo_formulario = 1"; // REGISTRO AMBIENTAL
        $sql .= " $where";
        $sql .= " $group_by";
        $sql .= " ORDER BY $projects_table.id, $formularios_table.id";

        return $this->db->query($sql);
		
    }
	
	public function user_exist_in_api($email){
		$sql .= "SELECT api.user_id , api.login_date";
		$sql .= "FROM `users` u, users_api_session api";
		$sql .= "WHERE email='$email'";
		$sql .= "AND api.user_id = u.id";
		return $this->db->query($sql)-result();
	}

}
