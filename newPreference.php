<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');

$database = new Database_queries($db);
$permissions = $database->getPermissionsForUserType($_SESSION['userObject']->getUserTypeId());

if(!$permissions[5]->getIsChecked()){
    $title = 401;
    include('error.php');
    die();
}

$preferences = $database->getAllPreferences();

if (isset($_POST['newPreference'])) {

    $createNewPreference = new Preference();

    $pref_description = trim(filter_input(INPUT_POST,'newPrefDescription', FILTER_SANITIZE_STRING));

    $createNewPreference->setPrefDescription($pref_description);

    $database->createPreference($createNewPreference);
    $preferences = $database->getAllPreferences();
    echo $twig->render('newPreference.twig', ['prefs' => $preferences]);
}
elseif(isset($_POST['back'])){
    header("Location:administratorTools.php");
}

else {
    echo $twig->render('newPreference.twig', ['prefs' => $preferences]);
}
