<?php
/**
 * Created by PhpStorm.
 * User: ronny
 * Date: 01.04.2019
 * Time: 19:21
 */

class Chat {
    private
        $tripId,
        $title,
        $isVisible = false,
        $isOpen = false;

    public function __construct($tripId) {
        $this->tripId = $tripId;
        $this->title = "Reise #" . $tripId;
    }


    function getTripId() {
        return $this->tripId;
    }

    function getTitle() {
        return $this->title;
    }

    function isVisible() {
        return $this->isVisible;
    }

    function isOpen() {
        return $this->isOpen;
    }

    public function setTripId($tripId): void
    {
        $this->tripId = $tripId;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function setIsVisible($isVisible): void
    {
        $this->isVisible = $isVisible;
    }

    public function setIsOpen($isOpen): void
    {
        $this->isOpen = $isOpen;
    }

    public function close() {
        $this->setIsOpen(false);
        $this->setIsVisible(false);
    }

    public function minimize() {
        $this->setIsOpen(false);
    }

    public function open() {
        $this->setIsOpen(true);
        $this->setIsVisible(true);
    }

    public function maximize() {
        $this->setIsOpen(true);
    }

}