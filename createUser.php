<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 04.04.2019
 * Time: 18:22
 */

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');

$database = new Database_queries($db);
$permissions = $database->getPermissionsForUserType($_SESSION['userObject']->getUserTypeId());

if(!$permissions[3]->getIsChecked()){
    $title = 401;
    include('error.php');
    die();
}

$userTypes = $database->getUserTypes();

if(isset($_POST['enter'])){
    $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
    $surName = filter_input(INPUT_POST, 'surName', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $reapetPassword = filter_input(INPUT_POST, 'rePassword', FILTER_SANITIZE_STRING);
    $userType = filter_input(INPUT_POST, 'userType', FILTER_SANITIZE_NUMBER_INT);

    $createNewUser = new User();

    $createNewUser->setName($firstName);
    $createNewUser->setSurname($surName);
    $createNewUser->setUserTypeId($userType);

    if ($password == $reapetPassword) {
        $createNewUser->setPassword(password_hash($password, PASSWORD_DEFAULT));
        if (!$database->emailExists($email)) {
            $createNewUser->setEpost($email);
            $createNewUser->setAccept(1);

            $user_id = $database->createUser($createNewUser, true);
            $lastPrefId = $database->selectMaxPrefId();
            for ($i = 1; $i <= $lastPrefId; $i++) {
                $database->createUserPreferenses($user_id, $i);
            }
            header( 'Location: administratorTools.php?userCreated=true' );
        } else {
           // echo $twig->render('createUser.twig');
            echo 'feil';
        }

    }

    else {
        echo 'feil';
       // echo $twig->render('registration.twig', ['hide_navbar' => true, 'feilPassord' => true]);
    }

}
else {
    echo $twig->render('createUser.twig', array('userTypes' => $userTypes));
}