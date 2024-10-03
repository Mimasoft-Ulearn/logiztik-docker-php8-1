<?php

class Users_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'users';
        parent::__construct($this->table);
    }

    function authenticate($email, $password) {
        $this->db->select("id,user_type,client_id");
        $result = $this->db->get_where($this->table, array('email' => $email, 'password' => md5($password), 'status' => 'active', 'deleted' => 0, 'disable_login' => 0));
        if ($result->num_rows() == 1) {
            $user_info = $result->row();

            //check client login settings
            if ($user_info->user_type === "client" && get_setting("disable_client_login")) {
                return false;
            } else if ($user_info->user_type === "client") {
                //user can't be loged in if client has deleted
                $clients_table = $this->db->dbprefix('clients');

                $sql = "SELECT $clients_table.id
                        FROM $clients_table
                        WHERE $clients_table.id= $user_info->client_id AND $clients_table.deleted=0
                        ";
                $client_result = $this->db->query($sql);
                if (!$client_result->num_rows()) {
                    return false;
                }
            }

            $this->session->set_userdata('user_id', $user_info->id);
            return true;
        }
    }

    function login_user_id() {
        $login_user_id = $this->session->user_id;
        return $login_user_id ? $login_user_id : false;
    }

    function sign_out() {
        $this->session->sess_destroy();
        redirect('signin');
    }

    function get_details($options = array()) {
        $users_table = $this->db->dbprefix('users');
		//$roles_table = $this->db->dbprefix('roles');
		$profiles_table = $this->db->dbprefix('profiles');
        $team_member_job_info_table = $this->db->dbprefix('team_member_job_info');

        $where = "";
        $id = get_array_value($options, "id");
		$first_name = get_array_value($options, "first_name");
        $status = get_array_value($options, "status");
        $user_type = get_array_value($options, "user_type");
        $client_id = get_array_value($options, "client_id");
        $exclude_user_id = get_array_value($options, "exclude_user_id");
		$is_admin = get_array_value($options, "is_admin");
		$id_profile = get_array_value($options, "id_profile");
		$id_client_context_profile = get_array_value($options, "id_client_context_profile");

        if ($id) {
            $where .= " AND $users_table.id=$id";
        }
		if ($first_name) {
            $where .= " AND $users_table.first_name=$first_name";
        }
        if ($status === "active") {
            $where .= " AND $users_table.status='active'";
        } else if ($status === "inactive") {
            $where .= " AND $users_table.status='inactive'";
        }

        if ($user_type) {
            $where .= " AND $users_table.user_type='$user_type'";
        }

        if ($client_id) {
            $where .= " AND $users_table.client_id=$client_id";
        }

        if ($exclude_user_id) {
            $where .= " AND $users_table.id!=$exclude_user_id";
        }
		
		if ($exclude_user_id) {
            $where .= " AND $users_table.id!=$exclude_user_id";
        }
		
		if ($is_admin) {
			if($is_admin == "administrador"){
				$where .= " AND $users_table.is_admin=1";
			}
			if($is_admin == "cliente"){
				$where .= " AND $users_table.is_admin=0";
			} 
        }
		
		if ($id_profile) {
            $where .= " AND $users_table.id_profile=$id_profile";
        }
		
		if ($id_client_context_profile) {
            $where .= " AND $users_table.id_client_context_profile=$id_client_context_profile";
        }

        $custom_field_type = "team_members";
        if ($user_type === "client") {
            $custom_field_type = "contacts";
        }

        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_query_info = $this->prepare_custom_field_query_string($custom_field_type, $custom_fields, $users_table);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");


        //prepare full query string
        $sql = "SELECT $users_table.*, $profiles_table.name AS perfil,
            $team_member_job_info_table.date_of_hire, $team_member_job_info_table.salary, $team_member_job_info_table.salary_term $select_custom_fieds
        FROM $users_table
        LEFT JOIN $team_member_job_info_table ON $team_member_job_info_table.user_id=$users_table.id
		LEFT JOIN $profiles_table ON $profiles_table.id = $users_table.id_profile
        $join_custom_fieds    
        WHERE $users_table.deleted=0 $where
        ORDER BY $users_table.first_name";
        return $this->db->query($sql);
    }

    function is_email_exists($email, $id = 0) {
        $result = $this->get_all_where(array("email" => $email, "deleted" => 0));
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }

    function get_job_info($user_id) {
        parent::use_table("team_member_job_info");
        return parent::get_one_where(array("user_id" => $user_id));
    }

    function save_job_info($data) {
        parent::use_table("team_member_job_info");

        //check if job info already exists
        $where = array("user_id" => get_array_value($data, "user_id"));
        $exists = parent::get_one_where($where);
        if ($exists->user_id) {
            //job info found. update the record
            return parent::update_where($data, $where);
        } else {
            //insert new one
            return parent::save($data);
        }
    }

    function get_team_members($member_ids = "") {
        $users_table = $this->db->dbprefix('users');
        $sql = "SELECT $users_table.*
        FROM $users_table
        WHERE $users_table.deleted=0 AND $users_table.user_type='staff' AND FIND_IN_SET($users_table.id, '$member_ids')
        ORDER BY $users_table.first_name";
        return $this->db->query($sql);
    }

    function get_access_info($user_id = 0) {
        $users_table = $this->db->dbprefix('users');
        $roles_table = $this->db->dbprefix('roles');
        $team_table = $this->db->dbprefix('team');

		 $sql = "SELECT $users_table.id, $users_table.user_type, $users_table.is_admin, $users_table.role_id, $users_table.email,
            $users_table.first_name, $users_table.last_name, $users_table.image, $users_table.message_checked_at, 
			$users_table.notification_checked_at, $users_table.alert_checked_at, $users_table.client_id,
            $users_table.is_primary_contact, $users_table.sticky_note,
            $roles_table.title as role_title, $roles_table.permissions,
            (SELECT GROUP_CONCAT(id) team_ids FROM $team_table WHERE FIND_IN_SET('$user_id', `members`)) as team_ids
        FROM $users_table
        LEFT JOIN $roles_table ON $roles_table.id = $users_table.role_id AND $roles_table.deleted = 0
        WHERE $users_table.deleted=0 AND $users_table.id=$user_id";
		
        return $this->db->query($sql)->row();
    }

    function get_team_members_and_clients($user_type = "", $user_ids = "", $exlclude_user = 0) {

        $users_table = $this->db->dbprefix('users');
        $clients_table = $this->db->dbprefix('clients');


        $where = "";
        if ($user_type) {
            $where.= " AND $users_table.user_type='$user_type'";
        }

        if ($user_ids) {
            $where.= "  AND FIND_IN_SET($users_table.id, '$user_ids')";
        }

        if ($exlclude_user) {
            $where.= " AND $users_table.id !=$exlclude_user";
        }

        $sql = "SELECT $users_table.id,$users_table.client_id, $users_table.user_type, $users_table.first_name, $users_table.last_name, $clients_table.company_name
        FROM $users_table
        LEFT JOIN $clients_table ON $clients_table.id = $users_table.client_id AND $clients_table.deleted=0
        WHERE $users_table.deleted=0 AND $users_table.status='active' $where
        ORDER BY $users_table.user_type, $users_table.first_name ASC";
        return $this->db->query($sql);
    }

    /* return comma separated list of user names */

    function user_group_names($user_ids = "") {
        $users_table = $this->db->dbprefix('users');

        $sql = "SELECT GROUP_CONCAT(' ', $users_table.first_name, ' ', $users_table.last_name) AS user_group_name
        FROM $users_table
        WHERE FIND_IN_SET($users_table.id, '$user_ids')";
        return $this->db->query($sql)->row();
    }
	
	/*Users list for multiselect*/
	function get_users_of_projects($project_id){
		

		$array_miembros = array();
		$array_miembros_form = array();
		$project_members_table = $this->db->dbprefix('project_members');		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $project_members_table.* from $project_members_table WHERE";
		$sql .= " $project_members_table.deleted=0";
		$sql .= " AND $project_members_table.project_id = $project_id";
		
		$query = $this->db->query($sql);

		foreach($query as $row => $innerArray){
			foreach($innerArray as $innerRow => $value){
				if($value != null){
					$array_miembros["id"] = $value["user_id"];
					$array_miembros["first_name"] =  $this->get_one($value["user_id"])->first_name;
					$array_miembros_project[$innerRow] = $array_miembros;
				}	
			}
		}
				
		return $array_miembros_project;
	}
	
	
	function get_users_of_project($project_id) {
        $users_table = $this->db->dbprefix('users');
		$project_members_table = $this->db->dbprefix('project_members');
        $project_table = $this->db->dbprefix('projects');
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		//SELECT c.* FROM campos c, campo_rel_formulario crf, formularios f WHERE c.id = crf.id_campo AND crf.id_formulario = f.id AND f.id = 
        $sql = "SELECT $users_table.* FROM $users_table, $project_members_table, $project_table WHERE";
		$sql .= " $users_table.deleted=0";
		$sql .= " AND $project_members_table.deleted=0";
		$sql .= " AND $users_table.user_type='client'";
		$sql .= " AND $users_table.id = $project_members_table.user_id";
		$sql .= " AND $project_members_table.project_id = $project_table.id";
		$sql .= " AND $project_table.id = $project_id";
		
        return $this->db->query($sql);
    }
	
	function get_responsables_dropdown($client_id = "", $submodule_id = "") {
		$output = array();
		
		$users_table = $this->db->dbprefix('users');
		$modules_rel_table = $this->db->dbprefix('client_context_modules_rel_profiles');
		$availability_rel_table = $this->db->dbprefix('client_module_availability_settings');
		
		$where = "";
		
		if ($submodule_id) {
			$where .= " AND $modules_rel_table.id_client_context_submodule = $submodule_id";
			$where .= " AND $modules_rel_table.ver = 1";
			
            $where .= " AND $availability_rel_table.id_modulo = 5"; // Acuerdos territorio (módulo externo Acuerdos)
            $where .= " AND $modules_rel_table.id_client_context_module = 7"; //Actividades (módulo interno/menu lateral izq).
        
		}
		
		if ($client_id) {
			$where .= " AND $users_table.client_id = $client_id";
		}
		
		$sql = "SELECT $users_table.id AS id, CONCAT(COALESCE($users_table.first_name, ''), ' ', COALESCE($users_table.last_name, ''))  AS name";
		$sql .= " FROM $users_table";
		$sql .= " JOIN $modules_rel_table ON $modules_rel_table.id_client_context_profile = $users_table.id_client_context_profile";
		$sql .= " JOIN $availability_rel_table ON $availability_rel_table.id_cliente = $users_table.client_id";
		$sql .= " WHERE $users_table.deleted = 0";
		$sql .= " AND $availability_rel_table.disponible = 1";
		$sql .= " AND $availability_rel_table.deleted = 0";
		$sql .= $where;
		
		foreach($this->db->query($sql)->result_array() as $user) {
			$output[$user["id"]] = $user["name"];
		}
		
		//var_dump($sql);
		
		return $output;
	}

}
