<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 11.04.2019
 * Time: 23:04
 */

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');
$database = new Database_queries($db);

$permissions = $database->getPermissionsForUserType($_SESSION['userObject']->getUserTypeId());

 if(!$permissions[1]->getIsChecked()){
     $title = 401;
     include('error.php');
     die();
 }

if (isset($_GET['delTrip'])) {
    if(!$permissions[7]->getIsChecked()){
        echo false;
    }
    else {
        $ID = filter_input(INPUT_GET, 'delTrip', FILTER_SANITIZE_NUMBER_INT);
        $database->cancelTrip($ID);
        echo true;
    }
} else {
    $trips = $database->getAllTrips();
    echo $twig->render('editTrips.twig', array('trips' => $trips));
}