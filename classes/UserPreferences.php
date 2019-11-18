<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 25.02.2019
 * Time: 21:48
 */

class UserPreferences
{
    private $user_id;
    private $smoking;
    private $pets;
    private $music;
    private $talk;

    function __construct() {

    }

    function getUser_id() {
        return $this->user_id;
    }

    function getSmoking() {
        return $this->smoking;
    }

    function getPets() {
        return $this->pets;
    }

    function getMusic() {
        return $this->music;
    }

    function getTalk() {
        return $this->talk;
    }

    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    function setSmoking($smoking) {
        $this->smoking = $smoking;
    }

    function setPets($pets) {
        return $this->pets = $pets;
    }

    function setMusic($music) {
        return $this->music = $music;
    }

    function setTalk($talk) {
        return $this->talk = $talk;
    }

}