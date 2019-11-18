<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once 'config.php';
$database = new Database_queries($db);

include 'checklogin.php';

if (!isset($_SESSION['chats']))
    $_SESSION['chats'] = array();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $tripID = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $tripExists = $database->checkIfTripExists($tripID);



    if ($tripExists) {

        $trip = $database->getTrip($tripID);
        $prefs = $database->getPreferencesFromTrip($tripID);
        $allPrefs = $database->getAllPreferences();

        $userInTrip = $database->isUserInAtrip($_SESSION['user']->getUserID(), $tripID);
        echo $twig->render('tripdetails.twig', array('trip' => $trip, 'userAssignedToTrip' => $userInTrip, 'prefs' => $prefs, 'allPrefs' => $allPrefs, 'user' => $_SESSION['userObject'], 'chats' => $_SESSION['chats']));
    } else {
        $title = 404;
        include('error.php');
        die();
    }
}

if (isset($_GET['assign']) && is_numeric($_GET['assign'])) {
    $tripID = filter_input(INPUT_GET, 'assign', FILTER_SANITIZE_NUMBER_INT);
    $database->assignUserToTrip($tripID, $_SESSION['user']->getUserID());

}

if (isset($_GET['removeTrip'])) {
    $tripId = filter_input(INPUT_GET, 'removeTrip', FILTER_SANITIZE_NUMBER_INT);
    $userId = $_SESSION['user']->getUserId();
    //echo "removeTrip";
    $database->removeUserFromTrip($tripId, $userId);
}

if (isset($_GET['driver'])) {
    $tripID = filter_input(INPUT_GET, 'driver', FILTER_SANITIZE_NUMBER_INT);
    $CheckIfUserHasCar = $database->isUserCarExist($_SESSION['userObject']->getUserId());

    if($CheckIfUserHasCar) {
        echo $database->setDriverToTrip($tripID, $_SESSION['userObject']->getUserID());
    }
    else
        echo false;
}

if (isset($_GET['removeDriver'])){
    $tripID = filter_input(INPUT_GET, 'removeDriver', FILTER_SANITIZE_NUMBER_INT);
    echo $database->removeDriverFromTrip($tripID);
}

if (isset($_GET['deleteTrip'])) {
    $tripId = filter_input(INPUT_GET, 'deleteTrip', FILTER_SANITIZE_NUMBER_INT);
    echo "delete id: ".$tripId."<br>";
    $database->cancelTrip($tripId);
}
