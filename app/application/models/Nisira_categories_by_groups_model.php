<?php

class Nisira_categories_by_groups_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'nisira_categories_by_groups';
        parent::__construct($this->table);
    }
    
}