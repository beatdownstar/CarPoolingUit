<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 03.04.2019
 * Time: 18:22
 */

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');
$database = new Database_queries($db);

$permissions = $database->getPermissionsForUserType($_SESSION['userObject']->getUserTypeId());

if(!$permissions[4]->getIsChecked()){
    $title = 401;
    include('error.php');
    die();
}

if (isset($_POST['enter'])) {
    $updateUserData = new User();

    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING));
    $firstName = trim(filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING));
    $surName = trim(filter_input(INPUT_POST, 'surName', FILTER_SANITIZE_STRING));
    $userType = trim(filter_input(INPUT_POST, 'userType', FILTER_SANITIZE_NUMBER_INT));
    $userId = trim(filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_NUMBER_INT));

    $feideUser = $database->checkIfFeideUser($email);

    if($feideUser){
        $database->updateFeideUserAdmin($userType, $userId);
    }
    else {
        $updateUserData->setUserId($userId);
        $updateUserData->setName($firstName);
        $updateUserData->setSurname($surName);
        $updateUserData->setEpost($email);
        $updateUserData->setUserTypeId($userType);

        /* if (isset($_POST['passcheck']) && !empty($_POST['password']) && !empty($_POST['rePassword'])){
             $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
             $rePassword = trim(filter_input(INPUT_POST, 'rePassword', FILTER_SANITIZE_STRING));
             if ($password == $rePassword) {
                 $updateUserData->setPassword(password_hash($password, PASSWORD_DEFAULT));
                 $database->setPassword($updateUserData);
             }
         }*/

        if (isset($_POST['passcheck']) && !empty($_POST['password']) && !empty($_POST['rePassword'])) {
            $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));

            $updateUserData->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $database->setPassword($updateUserData);
        }

        $database->updateUserDataAdmin($updateUserData);
    }

    $userTypes = $database->getUserTypes();
    $users = $database->showUsers();
    echo $twig->render('editUser.twig', array('userTypes' => $userTypes, 'users' => $users));

}elseif (isset($_GET['delUser'])){
    $userId = filter_input(INPUT_GET, 'delUser', FILTER_SANITIZE_NUMBER_INT);
    $database->deleteUser($userId);
    echo true;

} else {
    $userTypes = $database->getUserTypes();
    $users = $database->showUsers();
    echo $twig->render('editUser.twig', array('userTypes' => $userTypes, 'users' => $users));
}
