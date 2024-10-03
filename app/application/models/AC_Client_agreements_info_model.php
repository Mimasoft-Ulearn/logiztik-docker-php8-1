<?php

class AC_Client_agreements_info_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_client_agreements_info';
        parent::__construct($this->table);
    }
	
    function get_details($options = array()){

        $ac_client_agreements_info_table = $this->db->dbprefix('ac_client_agreements_info');
        // $enel_icons_table = $this->db->dbprefix('enel_icons');

        $client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $ac_client_agreements_info_table.client_area = '$client_area'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
        
		$sql = "SELECT $ac_client_agreements_info_table.*";
        // $sql .= ", $enel_icons_table.html as html_enel_icon, $enel_icons_table.class as class_enel_icon";
		$sql .= " FROM $ac_client_agreements_info_table";
		// $sql .= " INNER JOIN $enel_icons_table ON $ac_client_agreements_info_table.id_enel_icon = $enel_icons_table.id";
		$sql .= " WHERE $ac_client_agreements_info_table.deleted = 0";
		// $sql .= " AND $enel_icons_table.deleted = 0";

        $sql .= " $where";

		return $this->db->query($sql);

    }
}
