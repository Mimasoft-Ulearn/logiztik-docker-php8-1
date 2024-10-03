<?php

class AC_Types_of_activities_model extends Crud_model{

    private $table;

    function __construct() {
        $this->table = 'ac_tipo_actividades';
        parent::__construct($this->table);
    }

}