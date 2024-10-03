<?php
class Fixed_feeder_waste_receiving_companies_values_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'fixed_feeder_waste_receiving_companies_values';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $fixed_feeder_waste_receiving_companies_values_table = $this->db->dbprefix('fixed_feeder_waste_receiving_companies_values');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $fixed_feeder_waste_receiving_companies_values_table.id=$id";
        }

        $id_client = get_array_value($options, "id_client");
        if ($id_client) {
            $where .= " AND $fixed_feeder_waste_receiving_companies_values_table.id_client = $id_client";
        }
		
		$id_project = get_array_value($options, "id_project");
        if ($id_project) {
            $where .= " AND $fixed_feeder_waste_receiving_companies_values_table.id_project = $id_project";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $fixed_feeder_waste_receiving_companies_values_table.created_by = $created_by";
        }
		
		$modified_by = get_array_value($options, "modified_by");
        if ($modified_by) {
            $where .= " AND $fixed_feeder_waste_receiving_companies_values_table.modified_by = $modified_by";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fixed_feeder_waste_receiving_companies_values_table.*";
        $sql .= " FROM $fixed_feeder_waste_receiving_companies_values_table";
		$sql .= " WHERE $fixed_feeder_waste_receiving_companies_values_table.deleted = 0";
		$sql .= " $where";
		
        return $this->db->query($sql);
    }

}
