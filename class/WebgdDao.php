<?php

class WebgdDao {

    private $DB;
    private $CFG;

    function __construct() {
        global $DB, $CFG;
        $this->DB = $DB;
        $this->CFG = $CFG;
    }

}
