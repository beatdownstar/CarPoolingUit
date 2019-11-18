<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 20.02.2019
 * Time: 15:08
 */

class Session {
    private $username;
    private $userID;
    private $IPAddress;
    private $UserAgent;

    function __construct($username, $userID) {
        $this->IPAddress = $_SERVER["REMOTE_ADDR"];
        $this->UserAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->username = $username;
        $this->userID = $userID;
    }

    function getUsername() {
        return $this->username;
    }

    function getUserID() {
        return $this->userID;
    }

    function getIPAddress() {
        return $this->IPAddress;
    }

    function getUserAgent() {
        return $this->UserAgent;
    }

    public function verifyUser() {
        if(($this->IPAddress == $_SERVER["REMOTE_ADDR"]) && ($this->UserAgent == $_SERVER['HTTP_USER_AGENT'] )){
            return true;
        }
        else
            return false;
    }
}