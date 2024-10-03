<?php

class Sidrep_codes_model extends Crud_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
        $this->table = 'sidrep_codes';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
		$clients_table = $this->db->dbprefix('clients');
		$sidrep_codes_table = $this->db->dbprefix('sidrep_codes');
		
		$categories_db = getFCBD();
		$categories_table = $this->load->database(getFCBD(), TRUE)->dbprefix('categorias');
		
		$where = "";
        $id = get_array_value($options, "id");
        if($id) {
            $where .= " AND $sidrep_codes_table.id=$id";
        }
		
		$id_client = get_array_value($options, "id_client");
        if ($id_client) {
            $where .= " AND $sidrep_codes_table.id_client = $id_client";
        }

        $id_project = get_array_value($options, "id_project");
        if ($id_project) {
            $where .= " AND $sidrep_codes_table.id_project = $id_project";
        }
		
		$id_category = get_array_value($options, "id_category");
        if ($id_category) {
            $where .= " AND $sidrep_codes_table.id_category=$id_category";
        }
		    
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $sidrep_codes_table.*, $clients_table.company_name AS client_name, $categories_table.nombre AS category_name";
		$sql .= " FROM $clients_table, $categories_db.$categories_table, $sidrep_codes_table WHERE";
		$sql .= " $clients_table.deleted=0";
		$sql .= " AND $categories_table.deleted=0";
		$sql .= " AND $sidrep_codes_table.deleted=0";
		$sql .= " AND $clients_table.id = $sidrep_codes_table.id_client";
		$sql .= " AND $categories_table.id = $sidrep_codes_table.id_category";
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
