<?php

class Notifications {
    private $notification_id;
    private $notification_name;
    private $isChecked;

    public function __construct() {
    }

    public function getNotificationId() {
        return $this->notification_id;
    }

    public function setNotificationId($notification_id) {
        $this->notification_id = $notification_id;
    }

    public function getNotificationName() {
        return $this->notification_name;
    }

    public function setNotificationName($notification_name) {
        $this->notification_name = $notification_name;
    }

    public function getIsChecked() {
        return $this->isChecked;
    }

}