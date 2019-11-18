<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');

$database = new Database_queries($db);
$reasons = $database->getAllReasons();

if (isset($_POST['editReason'])) {

    $description = trim(filter_input(INPUT_POST, 'reasonToEdit', FILTER_SANITIZE_STRING));
    $reason_id = trim(filter_input(INPUT_POST, 'reason_id', FILTER_SANITIZE_STRING));

    echo $twig->render('editReason.twig', ['reasons' => $reasons, 'description' => $description, 'reason_id' => $reason_id]);

    echo "<script>$('#editReasonModal').modal('show')</script>";

}
elseif (isset($_POST['saveReason'])) {

    $description = trim(filter_input(INPUT_POST, 'editReasonDescription', FILTER_SANITIZE_STRING));
    $reason_id = trim(filter_input(INPUT_POST, 'reasonIdModal', FILTER_SANITIZE_STRING));
    $database->setReasonDescription($description, $reason_id);
    $reasons = $database->getAllReasons();
    echo $twig->render('editReason.twig', ['reasons' => $reasons]);
}
elseif (isset($_POST['deleteReason'])) {

    $description = trim(filter_input(INPUT_POST, 'reasonToEdit', FILTER_SANITIZE_STRING));
    $reason_id = trim(filter_input(INPUT_POST, 'reason_id', FILTER_SANITIZE_STRING));

    echo $twig->render('editReason.twig', ['reasons' => $reasons, 'description' => $description, 'reason_id' => $reason_id]);

    echo "<script>$('#deleteReasonModal').modal('show')</script>";

}
elseif (isset($_POST['saveDeletedReason'])) {

    $reason_id = trim(filter_input(INPUT_POST, 'reasonIdDeleteModal', FILTER_SANITIZE_STRING));
    $database->deleteReason($reason_id);
    $reasons = $database->getAllReasons();
    echo $twig->render('editReason.twig', ['reasons' => $reasons]);

}
elseif(isset($_POST['back'])){
    header("Location:administratorTools.php");
}
else {
    echo $twig->render('editReason.twig', ['reasons' => $reasons]);
}




