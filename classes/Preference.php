<?php
/**
 * Created by PhpStorm.
 * User: Markus
 * Date: 27.03.2019
 * Time: 17:27
 */

class Preference {
    private $pref_id;
    private $pref_description;
    private $is_active;


    public function __construct() {
    }

    public function getPrefId() {
        return $this->pref_id;
    }

    public function getPrefDescription() {
        return $this->pref_description;
    }

    public function getIsActive() {
        return $this->is_active;
    }

    public function setPrefId($pref_id) {
        $this->pref_id = $pref_id;
    }

    public function setPrefDescription($pref_description) {
        $this->pref_description = $pref_description;
    }

    public function setIsActive($is_active) {
        $this->is_active = $is_active;
    }

}