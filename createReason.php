<?php


spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');

$database = new Database_queries($db);
$reasons = $database->getAllReasons();

if (isset($_POST['newReason'])) {

    $createNewReason = new Reasons();

    $reason_description = trim(filter_input(INPUT_POST,'newReasonDescription', FILTER_SANITIZE_STRING));

    $createNewReason->setReasonDescription($reason_description);

    $database->createReason($createNewReason);
    $reasons = $database->getAllReasons();
    echo $twig->render('createReason.twig', ['reasons' => $reasons]);
}
elseif(isset($_POST['back'])){
    header("Location:administratorTools.php");
}

else {
    echo $twig->render('createReason.twig', ['reasons' => $reasons]);
}
