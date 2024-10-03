<?php

class Sinader_code_model extends Crud_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
        $this->table = 'sinader_code';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
		$clients_table = $this->db->dbprefix('clients');
		$sinader_code_table = $this->db->dbprefix('sinader_code');
		
		$categories_db = getFCBD();
		$categories_table = $this->load->database(getFCBD(), TRUE)->dbprefix('categorias');
		
		$where = "";
        $id = get_array_value($options, "id");
        if($id) {
            $where .= " AND $sinader_code_table.id=$id";
        }
		
		$id_client = get_array_value($options, "id_client");
        if ($id_client) {
            $where .= " AND $sinader_code_table.id_client = $id_client";
        }

        $id_project = get_array_value($options, "id_project");
        if ($id_project) {
            $where .= " AND $sinader_code_table.id_project = $id_project";
        }
		
		$id_category = get_array_value($options, "id_category");
        if ($id_category) {
            $where .= " AND $sinader_code_table.id_category=$id_category";
        }
		    
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $sinader_code_table.*, $clients_table.company_name AS client_name, $categories_table.nombre AS category_name";
		$sql .= " FROM $clients_table, $categories_db.$categories_table, $sinader_code_table WHERE";
		$sql .= " $clients_table.deleted=0";
		$sql .= " AND $categories_table.deleted=0";
		$sql .= " AND $sinader_code_table.deleted=0";
		$sql .= " AND $clients_table.id = $sinader_code_table.id_client";
		$sql .= " AND $categories_table.id = $sinader_code_table.id_category";
		$sql .= " $where ";
		return $this->db->query($sql);
		
	}
	
	function is_code_exists($data, $id = 0) {
        $result = $this->get_all_where($data);
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }
	
}
