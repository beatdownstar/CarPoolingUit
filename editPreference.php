<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');

$database = new Database_queries($db);
$permissions = $database->getPermissionsForUserType($_SESSION['userObject']->getUserTypeId());

if(!$permissions[6]->getIsChecked()){
    $title = 401;
    include('error.php');
    die();
}

$preferences = $database->getAllPreferences();

if (isset($_POST['editPreference'])) {

    $description = trim(filter_input(INPUT_POST, 'prefToEdit', FILTER_SANITIZE_STRING));
    $pref_id = trim(filter_input(INPUT_POST, 'pref_id', FILTER_SANITIZE_STRING));

    echo $twig->render('editPreference.twig', ['prefs' => $preferences, 'description' => $description, 'pref_id' => $pref_id]);

    echo "<script>$('#editPrefModal').modal('show')</script>";

}
elseif (isset($_POST['savePreference'])) {

    $description = trim(filter_input(INPUT_POST, 'editPrefDescription', FILTER_SANITIZE_STRING));
    $pref_id = trim(filter_input(INPUT_POST, 'prefIdModal', FILTER_SANITIZE_STRING));

    $database->setPrefDescription($description, $pref_id);
    $preferences = $database->getAllPreferences();
    echo $twig->render('editPreference.twig', ['prefs' => $preferences]);
}
elseif (isset($_POST['deletePreference'])) {

    $description = trim(filter_input(INPUT_POST, 'prefToEdit', FILTER_SANITIZE_STRING));
    $pref_id = trim(filter_input(INPUT_POST, 'pref_id', FILTER_SANITIZE_STRING));

    echo $twig->render('editPreference.twig', ['prefs' => $preferences, 'description' => $description, 'pref_id' => $pref_id]);

    echo "<script>$('#deletePrefModal').modal('show')</script>";

}
elseif (isset($_POST['saveDeletedPreference'])) {

    $pref_id = trim(filter_input(INPUT_POST, 'prefIdDeleteModal', FILTER_SANITIZE_STRING));
    $database->deletePreference($pref_id);
    $preferences = $database->getAllPreferences();
    echo $twig->render('editPreference.twig', ['prefs' => $preferences]);

}
elseif(isset($_POST['back'])){
    header("Location:administratorTools.php");
}
else {
    echo $twig->render('editPreference.twig', ['prefs' => $preferences]);
}




