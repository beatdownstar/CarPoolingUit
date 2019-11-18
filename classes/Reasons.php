<?php

class Reasons {
    private $reason_id;
    private $reason_description;

    public function __construct() {
    }

    public function getReasonId() {
        return $this->reason_id;
    }

    public function setReasonId($reason_id) {
        $this->reason_id = $reason_id;
    }

    public function getReasonDescription() {
        return $this->reason_description;
    }

    public function setReasonDescription($reason_description) {
        $this->reason_description = $reason_description;
    }

}