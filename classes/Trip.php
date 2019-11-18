<?php
/**
 * Created by PhpStorm.
 * User: Markus
 * Date: 19.02.2019
 * Time: 15:14
 */

class Trip
{
    private $trip_id;
    private $destinationPoint;  //Point object
    private $departurePoint;    // Point object
    private $driver = null;         // User object
    private $date_of_creation;      // DateTime object
    private $latest_arrival_date;   // DateTime object
    private $date_of_departure;     // DateTime object
    private $trip_info;
    private $travelTime;

    private $passengers = array();
    private $preferences = array();

    function __construct() {
    }

    function construct1($tripId, $dateOfCreation, $dateOfDeparture, $arrivalDate, $tripInfo) {
        $this->trip_id = $tripId;
        //$this->driver_id = $driverId;
        $this->date_of_creation = new DateTime($dateOfCreation);
        $this->date_of_departure = new DateTime($dateOfDeparture);
        $this->latest_arrival_date = new DateTime($arrivalDate);
        $this->trip_info = $tripInfo;
    }

    public static function fromArray($array) {
        $trip = new Trip();
        $trip->construct1($array['trip_id'], $array['date_of_creation'],
            $array['date_of_departure'], $array['latest_arrival_date'], $array['trip_info']);
        return $trip;
    }

    public function initTrip() {
        // set point name to destination point
        $destinationGoogleId = $this->getDestinationPoint()->getPointId();
        $json = file_get_contents('https://maps.googleapis.com/maps/api/place/details/json?placeid='.$destinationGoogleId.'&fields=name&key=AIzaSyBc21nTEu1wk_8q5DaaRv7metwRGhl3ZNg');
        $data = json_decode($json);

        if (isset($data->{'result'}) && $data->{'status'} == 'OK') {
            $this->getDestinationPoint()->setPointName($data->{'result'}->{'name'});
        }

        // set point name to departure point
        $departureGoogleId = $this->getDeparturePoint()->getPointId();
        $json = file_get_contents('https://maps.googleapis.com/maps/api/place/details/json?placeid='.$departureGoogleId.'&fields=name&key=AIzaSyBc21nTEu1wk_8q5DaaRv7metwRGhl3ZNg');
        $data = json_decode($json);

        if (isset($data->{'result'}) && $data->{'status'} == 'OK') {
            $this->getDeparturePoint()->setPointName($data->{'result'}->{'name'});
        }
    }

    function getTripId() {
        return $this->trip_id;
    }

    function getCreationDate(){
        return $this->date_of_creation;
    }

    function getDestinationPoint() {
        return $this->destinationPoint;
    }

    function getDeparturePoint() {
        return $this->departurePoint;
    }

    function getDriver() {
        return $this->driver;
    }

    function getPassengers() {
        return $this->passengers;
    }

    function getLatestArrivalDate() {
        return $this->latest_arrival_date;
    }

    function getDateOfDeparture() {
        return $this->date_of_departure;
    }

    /*
    function getTimeOfDeparture() {
        return $this->departureTime;
    }*/

    function getTravelTime() {
        return $this->travelTime;
    }

    function getPreferences() {
        return $this->preferences;
    }

    function setDestinationPoint($dest) {
        $this->destinationPoint = $dest;
    }

    function setDeparturePoint($depart) {
        $this->departurePoint = $depart;
    }

    function setDriver($driver) {
        $this->driver = $driver;
    }

    function setLatestArrivalDate($date) {
        $this->latest_arrival_date = $date;
    }

    function setDateOfDeparture($date) {
        $this->date_of_departure = $date;
    }

    function setTravelTime($travelTime) {
        $this->travelTime = $travelTime;
    }

    function setPassengers(array $passengers) {
        $this->passengers = $passengers;
    }

    function setPreferences(array $preferences) {
        $this->preferences = $preferences;
    }

    function addPassenger($userID) {
        array_push($this->passengers, $userID);
    }

    function createDeparturePoint($pointId, $pointAddress) {
        $this->departurePoint = new Point();
        $this->departurePoint->setPointId($pointId);
        $this->departurePoint->setAddress($pointAddress);
    }

    function createDestinationPoint($pointId, $pointAddress) {
        $this->destinationPoint = new Point();
        $this->destinationPoint->setPointId($pointId);
        $this->destinationPoint->setAddress($pointAddress);
    }

    function initTravelTime() {
        $departId = $this->getDeparturePoint()->getPointId();
        $destId = $this->getDestinationPoint()->getPointId();
        $json = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=place_id:'.$departId.'&destinations=place_id:'.$destId.'&key=AIzaSyBc21nTEu1wk_8q5DaaRv7metwRGhl3ZNg');
        $data = json_decode($json);
        $timeValue = $data->{'rows'}[0]->{'elements'}[0]->{'duration'}->{'value'};
        //echo "value: ".$timeValue."<br>";
        //$travelTime = $this->secondsToDateTime($timeValue);
        $this->setTravelTime($timeValue);
    }

    function getTraveltimeFormat() {
        $value = $this->getTravelTime();

        $secondsInAMinute = 60;
        $secondsInAHour = 60 * $secondsInAMinute;
        $secondsInADay = 24 * $secondsInAHour;

        $hourSeconds = $value % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAHour);
        $hourString = $hours." "."time".(($hours > 1) ? "r":"");
        $minuteSeconds = $hourSeconds % $secondsInAHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);
        $minuteString = $minutes." "."minutter";
        $seconds = $value % $secondsInAMinute;
        $timeString = $hourString. " og ".$minuteString;

        $time = date_interval_create_from_date_string($hours.':'.$minutes.':'.$seconds);
        return $timeString;

    }





}