<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 07.04.2019
 * Time: 20:40
 */

class UserType
{
    private $type_id;
    private $type;

    function __construct() {

    }

    function getType_id() {
        return $this->type_id;
    }

    function getType() {
        return $this->type;
    }

    function setType_id($type_id) {
        $this->type_id = $type_id;
    }

    function setType($type) {
        $this->type = $type;
    }

}