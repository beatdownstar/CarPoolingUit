<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 03.04.2019
 * Time: 21:12
 */

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');
$database = new Database_queries($db);

$currentTrips = $database->countNumberOfCurrentTrips();
$drivers = $database->countNumberOfDrivers();
$previousTrips = $database->countNumberOfDeletedTrips();

$countNumberOfCampusUsers = array();
$countCampusPassengers = array();

for($i = 1; $i <= 10; $i++){
    $countNumberOfCampusUsers[$i - 1] = $database->countNumberOfCampusUsers($i);
    $countCampusPassengers[$i - 1] = $database->countNumberOfCampusPassengers($i);
}

echo $twig->render('travelStatistics.twig', array('trip' => $currentTrips, 'drivers' => $drivers, 'previousTrips' => $previousTrips));