<?php

class AYN_Notif_historical_users_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ayn_notif_historical_users';
        parent::__construct($this->table);
    }

}