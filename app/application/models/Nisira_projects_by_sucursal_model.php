<?php

class Nisira_projects_by_sucursal_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'nisira_projects_by_sucursal';
        parent::__construct($this->table);
    }
    
}