<?php

class AC_Execution_records_files_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_registros_ejecucion_archivos';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
        $registros_ejecucion_archivos_table = $this->db->dbprefix('ac_registros_ejecucion_archivos');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $registros_ejecucion_archivos_table.id = $id";
        }

		$id_registro_ejecucion = get_array_value($options, "id_registro_ejecucion");
        if ($id_registro_ejecucion) {
            $where .= " AND $registros_ejecucion_archivos_table.id_registro_ejecucion = $id_registro_ejecucion";
        }
		
		$nombre_archivo = get_array_value($options, "nombre_archivo");
        if ($nombre_archivo) {
            $where .= " AND $registros_ejecucion_archivos_table.nombre_archivo = '$nombre_archivo'";
        }
		
		$tipo_archivo = get_array_value($options, "tipo_archivo");
        if ($tipo_archivo) {
            $where .= " AND $registros_ejecucion_archivos_table.tipo_archivo = '$tipo_archivo'";
        }

		$client_area = get_array_value($options, "client_area");
        if ($client_area) {
            $where .= " AND $registros_ejecucion_archivos_table.client_area = '$client_area'";
        }
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $registros_ejecucion_archivos_table.* FROM $registros_ejecucion_archivos_table WHERE";
		$sql .= " $registros_ejecucion_archivos_table.deleted = 0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }

}
