<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 20.02.2019
 * Time: 16:16
 */

class User {

    private $user_id;
    private $feide_id;
    private $name;
    private $surname;
    private $campus_id;
    private $epost;
    private $password;
    private $tel_number;
    private $user_rating;
    private $type_id;
  //  private $vehicle_id;
    private $photo;
    private $banned_user;
    private $id_verification;
    private $accept;
  //  private $user_preferences_id;
    private $vehicles;
    private $isFeideUser = false;


    function __construct() {

    }

    function getUserId() {
        return $this->user_id;
    }

    function getName() {
        return $this->name;
    }

    function getCampusId() {
        return $this->campus_id;
    }

    function getSurname() {
        return $this->surname;
    }

    function getFullName() {
        return $this->getName() . " " . $this->getSurname();
    }

    function getEpost() {
        return $this->epost;
    }

    function getTelNumber() {
        return $this->tel_number;
    }

    function getUserRating() {
        return $this->user_rating;
    }

    function getUserTypeId() {
        return $this->type_id;
    }

    function getPhoto() {
        return $this->photo;
    }

    function getBannedUser() {
        return $this->banned_user;
    }

    function getPassword() {
        return $this->password;
    }

    function getIdVerification() {
        return $this->id_verification;
    }

    function getAccept() {
        return $this->accept;
    }

    function getFeideUser() {
        if($this->feide_id != null)
           return true;
        else
           return $this->isFeideUser;
    }

    function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setSurname($surname) {
        $this->surname = $surname;
    }

    function setCampusId($campus_id) {
        $this->campus_id = $campus_id;
    }

    function setEpost($epost) {
        $this->epost = $epost;
    }

    function setTelNumber($tel_number) {
        $this->tel_number = $tel_number;
    }

    function setUserRating($user_rating) {
        $this->user_rating = $user_rating;
    }

    function setUserTypeId($type_id) {
        $this->type_id = $type_id;
    }

    function setPhoto($photo) {
        $this->photo = $photo;
    }

    function setBannedUser($banned_user) {
        $this->banned_user = $banned_user;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setIdVerification($id_verification) {
        $this->id_verification = $id_verification;
    }

    function setAccept($accept) {
        $this->accept = $accept;
    }

    public function getVehicles() {
        return $this->vehicles;
    }

    public function setVehicles($vehicles) {
        $this->vehicles = $vehicles;
    }

    public function setFeideUser($isFeideUser){
        $this->isFeideUser = $isFeideUser;
    }

    public function createUserTypeObject($type_id, $name){
        $this->type_id = new userType();
        $this->type_id->setType_id($type_id);
        $this->type_id->setType($name);
    }

    public function createCampusObject($campus_id, $name){
        $this->campus_id = new Campus();
        $this->campus_id->setDep_id($campus_id);
        $this->campus_id->setDep_name($name);
    }

}