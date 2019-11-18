<?php

class Point implements JsonSerializable
{
    private $google_id;
    private $address;
    private $point_name;
    private $point_id;

    function __construct() {

    }

    function getPointId() {
        return $this->point_id;
    }

    function setPointId($point_id) {
        $this->point_id = $point_id;
    }

    function getGoogleId() {
        return $this->google_id;
    }

    function setGoogleId($point_id) {
        $this->google_id = $point_id;
    }

    function getAddress() {
        return $this->address;
    }

    function setAddress($address) {
        $this->address = $address;
    }

    function getPointName() {
        return $this->point_name;
    }

    function setPointName($point_name) {
        $this->point_name = $point_name;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->google_id,
            'address' => $this->address,
            'name' => $this->point_name
        ];
    }

}