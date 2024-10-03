<?php

class Nisira_uploaded_data_log_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'nisira_uploaded_data_log';
        parent::__construct($this->table);
    }
    
}