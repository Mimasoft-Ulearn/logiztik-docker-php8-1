<?php

if (!defined('BASEPATH'))exit('No direct script access allowed');

require_once APPPATH."/third_party/PDFMerger/PDFMerger.php";

class Pdf_merger extends PDFMerger {
	
    public function __construct() {
        parent::__construct();
    }

}