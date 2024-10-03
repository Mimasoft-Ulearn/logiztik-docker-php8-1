<?php

class Communities_providers_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'communities_providers';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
		
        $communities_providers_table = $this->db->dbprefix('communities_providers');

        $where = "";
        $id = get_array_value($options, "id");
        if($id) {
            $where .= " AND $communities_providers_table.id=$id";
        }

        $id_client = get_array_value($options, "id_client");
        if($id_client) {
            $where .= " AND $communities_providers_table.id_client=$id_client";
        }

        $id_project = get_array_value($options, "id_project");
        if($id_project) {
            $where .= " AND $communities_providers_table.id_project=$id_project";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $communities_providers_table.*";
        $sql .= " FROM $communities_providers_table";
		$sql .= " WHERE $communities_providers_table.deleted = 0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }

}
