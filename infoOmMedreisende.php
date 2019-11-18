<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');

$title = "UiT Samkjøringsportal";

$database = new Database_queries($db);
$points = $database->showPoints();
$reasons = $database->getAllReasons();

    $userinfo = null;

if(isset($_GET['user_id']) && is_numeric($_GET['user_id'])){

    $userinfo = new User();
    $user_id = filter_input(INPUT_GET,'user_id', FILTER_SANITIZE_NUMBER_INT);
    $userinfo = $database->showUserData($user_id);
    $points = $database->showPoints();

    echo $twig->render('infoOmMedreisende.twig', ['title' => $title, 'userinfo' => $userinfo, 'points' => $points, 'user_id' => $user_id, 'reasons' => $reasons, 'email' => $_SESSION['user']->getUsername()] );

}
elseif (isset($_POST['sendRapportUser'])) {

    $reportedUserId = filter_input(INPUT_POST,'rapportedUserId', FILTER_SANITIZE_NUMBER_INT);

    $userinfo = new User();

    $userinfo = $database->showUserData($reportedUserId);
    $points = $database->showPoints();

    $rapport = new ReportedUsers();
    $senderUserId = $_SESSION['user']->getUserID();

    $reason = trim(filter_input(INPUT_POST, 'reasonToRapport', FILTER_SANITIZE_STRING));
    $description = trim(filter_input(INPUT_POST, 'userRapportComment', FILTER_SANITIZE_STRING));
    $reasons = $database->getAllReasons();
    $rapport->setSenderUserId($senderUserId);
    $rapport->setReportedUserId($reportedUserId);
    $rapport->setReason($reason);
    $rapport->setDescription($description);
    $database->sendRapportedUser($rapport);

         echo $twig->render('infoOmMedreisende.twig', ['title' => $title, 'userinfo' => $userinfo, 'points' => $points, 'user_id' => $reportedUserId, 'userRapported' => true, 'reasons' => $reasons, 'email' => $_SESSION['user']->getUsername()] );


}

