<?php
/**
 * Created by PhpStorm.
 * User: Markus
 * Date: 21.02.2019
 * Time: 17:47
 */

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once 'config.php';
$database = new Database_queries($db);

if (isset($_POST['tripSubmit'])) {

    $destinationId = filter_input(INPUT_POST,'tripDestinationId', FILTER_SANITIZE_STRING);
    $departureId = filter_input(INPUT_POST,'tripDepartureId', FILTER_SANITIZE_STRING);
    $destinationName = filter_input(INPUT_POST,'tripDestinationName', FILTER_SANITIZE_STRING);
    $departureName = filter_input(INPUT_POST,'tripDepartureName', FILTER_SANITIZE_STRING);
    $arrivalDate = filter_input(INPUT_POST,'tripDate', FILTER_SANITIZE_STRING);
    $arrivalTime = filter_input(INPUT_POST,'tripTime', FILTER_SANITIZE_STRING);
    //echo "Destination name: " . $destinationName;

    $departurePoint = new Point();
    $departurePoint->setPointId($departureId);
    $departurePoint->setPointName($departureName);
    $destinationPoint = new Point();
    $destinationPoint->setPointId($destinationId);
    $destinationPoint->setPointName($destinationName);

    $trip = new Trip();
    $trip->setDestinationPoint($destinationPoint);
    $trip->setDeparturePoint($departurePoint);
    $tripArrivalDate = new DateTime($arrivalDate.' '.$arrivalTime);
    $trip->setLatestArrivalDate($tripArrivalDate);
    $trip->initTravelTime();
   // $trip->setDateOfDeparture(new DateTime($tripArrivalDate->format('Y-m-d H:i:s')));
    $trip->setDateOfDeparture(date_sub(new DateTime($tripArrivalDate->format('Y-m-d H:i:s')), date_interval_create_from_date_string($trip->getTravelTime()." seconds")));

    $_SESSION['trip'] = $trip;
    $userId = $_SESSION['user']->getUserId();

    $numberOfTrips = $database->countNumberOfTrips($trip, $interval);
    $alternativeTrips = $database->showAlternativeTrips($trip, 1, 180);

    $prefs = $database->getAllPreferences();

    echo $twig->render('search.twig', array('trips' => $alternativeTrips, 'showDeparture' => $departureName, 'showDestination' => $destinationName, 'numberOfTrips' => $numberOfTrips, 'currentPage' => 1, 'prefs' => $prefs));



    //$lastInsertId = $database->registerNewTrip($trip, $_SESSION['user']->getUserId());



}

if(isset($_GET['page'])){
    $alternativeTrips = $database->showAlternativeTrips($_SESSION['trip'], $_GET['page'], $_SESSION['interval']);
    $numberOfTrips = $database->countNumberOfTrips($_SESSION['trip'], $_SESSION['interval']);
    $template = $twig->loadTemplate('search.twig');
    echo $template->renderBlock('nextPage', array('trips' => $alternativeTrips, 'currentPage' => $_GET['page']));
}

if(isset($_POST['noThanks'])){
    $trip = $_SESSION['trip'];
    $userId = $_SESSION['user']->getUserId();
    $lastId = $database->registerNewTrip($trip, $userId);
    $database->newEventdeleteTrip($lastId, $trip->getLatestArrivalDate());
    header('Location: showMyTrips.php');


    //echo $twig->render('myTrips.twig');
}

if(isset($_GET['newTrip']) && is_numeric($_GET['newTrip'])){
    $tripID = filter_input(INPUT_GET,'newTrip', FILTER_SANITIZE_NUMBER_INT);

    echo $database->assignUserToTrip($tripID, $_SESSION['user']->getUserID());
}

if(isset($_GET['ajaxValidate'])) {
    $trip = $_SESSION['trip'];
    $userId = $_SESSION['user']->getUserId();
    echo $validation = $database->validateTrip($trip, $userId);
}

if (isset($_GET['validateJoin']) && is_numeric($_GET['validateJoin'])) {
    $tripId = filter_input(INPUT_GET,'validateJoin', FILTER_SANITIZE_NUMBER_INT);
    $trip = $database->getTrip($tripId);
    $userId = $_SESSION['user']->getUserId();
    echo $validate = $database->validateTrip($trip, $userId);
}
