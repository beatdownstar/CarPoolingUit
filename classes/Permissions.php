<?php

class Permissions {
    private $permission_id;
    private $permission_name;
    private $isChecked;

    public function __construct() {
    }

    public function getPermissionId() {
        return $this->permission_id;
    }

    public function setPermissionId($permission_id) {
        $this->permission_id = $permission_id;
    }

    public function getPermissionName() {
        return $this->permission_name;
    }

    public function setPermissionName($permission_name) {
        $this->permission_name = $permission_name;
    }

    public function getIsChecked() {
        return $this->isChecked;
    }

}