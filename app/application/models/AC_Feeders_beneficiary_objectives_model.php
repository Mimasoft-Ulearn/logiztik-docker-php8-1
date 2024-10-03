<?php

class AC_Feeders_beneficiary_objectives_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ac_feeders_objetivos_beneficiarios';
        parent::__construct($this->table);
    }

}