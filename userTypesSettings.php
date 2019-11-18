<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 03.04.2019
 * Time: 14:53
 */

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');

$database = new Database_queries($db);
$permissions = $database->getPermissionsForUserType($_SESSION['userObject']->getUserTypeId());

if(!$permissions[9]->getIsChecked() || !$permissions[10]->getIsChecked()){
    $title = 401;
    include('error.php');
    die();
}

$typeNames = $database->getAllTypesNames();

if (isset($_GET['userTypeId'])) {

    $typeNames = $database->getAllTypesNames();
    $permissions = $database->getPermissionsForUserType($_GET['userTypeId']);
    $brukerTypeNavn = $database->getUsertypeName($_GET['userTypeId']);

    echo $twig->render('userTypesSettings.twig', ['typeNames' => $typeNames, 'permissions' => $permissions, 'brukertypenavn' =>$brukerTypeNavn, 'permId' =>$_GET['userTypeId']]);
}

elseif (isset($_POST['savePermissions'])) {
    if(!$permissions[10]->getIsChecked()){
        $title = 401;
        include('error.php');
        die();
    }

    $permString = trim(filter_input(INPUT_POST, 'permValue', FILTER_SANITIZE_STRING));
    $parts = explode(',', $permString);
    $id = trim(filter_input(INPUT_POST, 'permId', FILTER_SANITIZE_NUMBER_INT));

    for ($i = 0; $i < sizeof($parts); $i++) {
        if ($parts[$i]==1)
            $database->setIsCheckedPerm($id, $i+1,1);
        else
            $database->setIsCheckedPerm($id, $i+1, 0);
    }

    header("Location:userTypesSettings.php?userTypeId=". $id);

}

elseif (isset($_POST['newBrukerType'])) {

    if(!$permissions[9]->getIsChecked()){
        $title = 401;
        include('error.php');
        die();
    }

    $newUserType = new UserType();

    $typeName = trim(filter_input(INPUT_POST, 'newBrukerType', FILTER_SANITIZE_STRING));

    $newUserType->setType($typeName);

    $lastPermId = $database->selectMaxPermId();
    $type_id = $database->createUserType($newUserType);

    for ($i = 1; $i <= $lastPermId; $i++) {
        $database->createUserTypePermissions($type_id, $i);
    }

    header("Location:userTypesSettings.php?userTypeId=1");

}



