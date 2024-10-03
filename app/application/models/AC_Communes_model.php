<?php

class AC_Communes_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_comunas';
        parent::__construct($this->table);
    }
	
	function get_communes_of_macrozone($id_macrozona){
		
		$macrozonas_rel_comunas_table = $this->db->dbprefix('ac_macrozonas_rel_comunas');
		$comunas_table = $this->db->dbprefix('ac_comunas');

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $comunas_table.* FROM $macrozonas_rel_comunas_table, $comunas_table WHERE";
		$sql .= " $comunas_table.deleted = 0";
		$sql .= " AND $macrozonas_rel_comunas_table.id_comuna = $comunas_table.id";
		$sql .= " AND $macrozonas_rel_comunas_table.id_macrozona = $id_macrozona";
		
        return $this->db->query($sql);
		
	}
	
	function get_communes_of_macrozone_by_client_area($client_area){
		
		$macrozonas_table = $this->db->dbprefix('ac_macrozonas');
		$macrozonas_rel_comunas_table = $this->db->dbprefix('ac_macrozonas_rel_comunas');
		$comunas_table = $this->db->dbprefix('ac_comunas');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $comunas_table.*";
		$sql .= " FROM $comunas_table";
		$sql .= " INNER JOIN $macrozonas_rel_comunas_table ON $comunas_table.id = $macrozonas_rel_comunas_table.id_comuna";
		$sql .= " INNER JOIN $macrozonas_table ON $macrozonas_rel_comunas_table.id_macrozona = $macrozonas_table.id";
		$sql .= " WHERE $macrozonas_table.client_area = '$client_area'";
		$sql .= " ORDER BY $comunas_table.nombre";

        return $this->db->query($sql);
		
	}

}
