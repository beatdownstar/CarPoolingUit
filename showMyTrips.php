<?php
/**
 * Created by PhpStorm.
 * User: Markus
 * Date: 26.02.2019
 * Time: 12:03
 */

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once 'config.php';

$dataQueries = new Database_queries($db);

$myTripsArray = $dataQueries->getMyTrips($_SESSION['user']->getUserId());
$myDeletedTripsArray = $dataQueries->getMyDeletedTrips($_SESSION['user']->getUserId());

echo $twig->render('myTrips.twig', ['myTrips' => $myTripsArray, 'myDelTrips' => $myDeletedTripsArray, 'chats' => $_SESSION['chats']]);




