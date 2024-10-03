<?php

class AC_Information_closing_files_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_informacion_cierres_archivos';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
        $ac_informacion_cierres_archivos_table = $this->db->dbprefix('ac_informacion_cierres_archivos');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $ac_informacion_cierres_archivos_table.id = $id";
        }

		$id_informacion_cierre = get_array_value($options, "id_informacion_cierre");
        if ($id_informacion_cierre) {
            $where .= " AND $ac_informacion_cierres_archivos_table.id_informacion_cierre = $id_informacion_cierre";
        }
		
		$nombre_archivo = get_array_value($options, "nombre_archivo");
        if ($nombre_archivo) {
            $where .= " AND $ac_informacion_cierres_archivos_table.nombre_archivo = '$nombre_archivo'";
        }
		
		$tipo_archivo = get_array_value($options, "tipo_archivo");
        if ($tipo_archivo) {
            $where .= " AND $ac_informacion_cierres_archivos_table.tipo_archivo = '$tipo_archivo'";
        }
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $ac_informacion_cierres_archivos_table.* FROM $ac_informacion_cierres_archivos_table WHERE";
		$sql .= " $ac_informacion_cierres_archivos_table.deleted = 0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }

}
