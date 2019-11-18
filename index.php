<?php
/**
 * Created by PhpStorm.
 * User: ronny
 * Date: 20.02.2019
 * Time: 13:04
 */

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
include 'config.php';
include 'checklogin.php';

if (isset($_POST['destinationName'])) {
    $destinationName = filter_input(INPUT_POST,'tripDestination', FILTER_SANITIZE_STRING);
    echo $destinationName;
}

$title = "UiT Samkjøringsportal";

$dbQueries = new Database_queries($db);
$prefs = $dbQueries->getAllPreferences();

//echo $_SESSION['user']->getUserID();

echo $twig->render('registertrip.twig', ['title' => $title, 'pointsJson' => $dbQueries->getAllPoints(), 'prefs' => $prefs, 'chats' => $_SESSION['chats']]);