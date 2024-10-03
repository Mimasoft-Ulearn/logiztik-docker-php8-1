<?php

class AC_Configuration_annual_program_files_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_configuracion_archivos_programa_anual';
        parent::__construct($this->table);
    }

}
