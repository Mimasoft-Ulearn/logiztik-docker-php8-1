<?php

class Nisira_or_uf_by_groups_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'nisira_or_uf_by_groups';
        parent::__construct($this->table);
    }
    
}