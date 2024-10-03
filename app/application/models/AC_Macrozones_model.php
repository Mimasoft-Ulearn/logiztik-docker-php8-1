<?php

class AC_Macrozones_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_macrozonas';
        parent::__construct($this->table);
    }
	
	function get_macrozones_of_client_area($client_area){
		
		$macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		
		if($client_area == "territory"){
			$where = " AND $macrozonas_table.id IN (1, 2, 3)";
		} elseif($client_area == "distribution") {
			$where = " AND $macrozonas_table.id = 4";
		} else {
			$where = "";
		}

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $macrozonas_table.* FROM $macrozonas_table WHERE";
		$sql .= " $macrozonas_table.deleted = 0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
	}

}
