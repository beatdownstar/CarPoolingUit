<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 02.05.2019
 * Time: 23:11
 */

class Campus {
    private $dep_id;
    private $dep_name;
    private $dep_address;

    function __construct() {

    }

    function getDep_id() {
        return $this->dep_id;
    }

    function getDep_name() {
        return $this->dep_name;
    }

    function getDep_address() {
        return $this->dep_address;
    }

    function setDep_id($dep_id) {
        $this->dep_id = $dep_id;
    }

    function setDep_name($dep_name) {
        $this->dep_name = $dep_name;
    }

    function setDep_address($dep_address) {
        $this->dep_address = $dep_address;
    }

}