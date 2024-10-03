<?php
class Fixed_feeder_management_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'fixed_feeder_management';
        parent::__construct($this->table);
    }

}
