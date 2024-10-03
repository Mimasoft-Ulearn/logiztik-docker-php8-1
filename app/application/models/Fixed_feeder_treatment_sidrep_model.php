<?php
class Fixed_feeder_treatment_sidrep_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'fixed_feeder_treatment_sidrep';
        parent::__construct($this->table);
    }

}
