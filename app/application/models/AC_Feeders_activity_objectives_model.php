<?php

class AC_Feeders_activity_objectives_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_feeders_objetivos_actividades';
        parent::__construct($this->table);
    }

}