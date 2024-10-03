<?php

class Nisira_projects_by_idconsumidor_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'nisira_projects_by_idconsumidor';
        parent::__construct($this->table);
    }
    
}