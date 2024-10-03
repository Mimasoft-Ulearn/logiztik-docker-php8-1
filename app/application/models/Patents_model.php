<?php

class Patents_model extends Crud_model {
    private $table = null;

    function __construct() {
        $this->table = 'patentes';
        parent::__construct($this->table);
    }

}