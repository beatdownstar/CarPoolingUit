<?php

class Car
{
    private $vehicle_id;
    private $user_id;
    private $info;
    private $number_of_seats;
    private $photo;
    private $hidden;

    function __construct() {

    }

    function getVehicleId() {
        return $this->vehicle_id;
    }

    function getUserId() {
        return $this->user_id;
    }

    function getNumberOfSeats() {
        return $this->number_of_seats;
    }

    function getInfo() {
        return $this->info;
    }

    function getPhoto() {
        return $this->photo;
    }

    function getHidden() {
        return $this->hidden;
    }

    function setVehicleId($vehicle_id) {
        $this->vehicle_id = $vehicle_id;
    }

    function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    function setNumberOfSeats($number_of_seats) {
        $this->number_of_seats = $number_of_seats;
    }

    function setInfo($info) {
        $this->info = $info;
    }

    function setPhoto($photo) {
        $this->photo = $photo;
    }

    function setHidden($hidden) {
        $this->hidden = $hidden;
    }

}