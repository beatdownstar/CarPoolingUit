<?php
/**
 * Created by PhpStorm.
 * User: Markus
 * Date: 18.02.2019
 * Time: 15:05
 */
require_once('vendor/autoload.php');
require_once('config.php');
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);
spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
/*
$date = new DateTime('2019-03-20 14:00:00');

echo $date->format('Y-m-d');
echo '</br>';
echo $date->format('H:i:s');
*/

$database = new Database_queries($db);

$trip = $database->getTrip(3);
$cars = $database->showUsersCarData($trip->getDriver()->getUserId());

$boolTest = $database->isPassengerInLogTable(10, 7);
if ($boolTest == false) {
    echo "Is false";
}
echo "<br>" . "<br>" . "<br>";

echo "Trip ID: " . $trip->getTripId() . "<br>";
echo "Avreise " . $trip->getDeparturePoint()->getPointName() . "<br>";
echo "Destinasjon: " . $trip->getDestinationPoint()->getPointName() . "<br>";
echo "sjåfør: " . $trip->getDriver()->getFullName() . "<br>";
echo "Bil: " . $cars[0]->getInfo() . "<br>";
echo "Passengers: "  . "<br>";
foreach($trip->getPassengers() as $pass) {
    echo $pass->getName() . " " . $pass->getSurname() . "<br>";
}






