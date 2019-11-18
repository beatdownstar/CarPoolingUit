<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');
require ('sendMail.php');

$database = new Database_queries($db);
$permissions = $database->getPermissionsForUserType($_SESSION['userObject']->getUserTypeId());

if(!$permissions[8]->getIsChecked()){
    $title = 401;
    include('error.php');
    die();
}

$users = $database->showUsers();
$reasons = $database->getAllReasons();


if (isset($_POST['banUser'])) {

    $epost = trim(filter_input(INPUT_POST,'epostToBan', FILTER_SANITIZE_STRING));
    $reason = trim(filter_input(INPUT_POST,'reasonToBan', FILTER_SANITIZE_STRING));

        $to = $epost;
        $subject = "Blokkering på portalen";
        $body = "Hei, du ble blokkert på carpooling portalen på grunn av " . $reason . ", for mer detaljert informasjon svar på denne meldingen";

        sendMail($to,$subject,$body);
    $database->setUserBan($epost);
    $users = $database->showUsers();

    echo $twig->render('banUser.twig', ['users' => $users, 'epost' => $epost, 'ban' => true]);



}
elseif (isset($_POST['activateUser'])) {

    $epost = trim(filter_input(INPUT_POST,'epostToBan', FILTER_SANITIZE_STRING));

    $to = $epost;
    $subject = "Account aktivering";
    $body = "Hei, din account ble aktivert, du kan fortsette å bruke carpooling portalen";

    sendMail($to,$subject,$body);
    $database->setUserAktiv($epost);
    $users = $database->showUsers();

    echo $twig->render('banUser.twig', ['users' => $users, 'epost' => $epost, 'activate' => true]);



}


else {

    echo $twig->render('banUser.twig', ['users' => $users, 'reasons' => $reasons]);
}