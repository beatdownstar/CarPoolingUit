<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');
require ('sendMail.php');

$database = new Database_queries($db);

if (isset($_POST['userRegistration'])) {

    $createNewUser = new User();

    $name = trim(filter_input(INPUT_POST,'userNameReg', FILTER_SANITIZE_STRING));
    $surname = trim(filter_input(INPUT_POST,'userSurnameReg', FILTER_SANITIZE_STRING));
    $epost = trim(filter_input(INPUT_POST,'epostReg', FILTER_SANITIZE_STRING));

   // $createNewUser->setUserId(($_SESSION['user']->getUserID());
    $createNewUser->setName($name);
    $createNewUser->setSurname($surname);

        $createNewUser->setPassword(password_hash(trim(filter_input(INPUT_POST, "passwordReg", FILTER_SANITIZE_SPECIAL_CHARS)), PASSWORD_DEFAULT));

        if (!$database->emailExists($epost)) {
            $createNewUser->setEpost($epost);
            $id = md5(uniqid(rand(), 1));

            $to = $epost;
            $subject = "Account aktivering";
            $body = "Velkommen til vår carpooling service, for å begynne å bruke portalen må du aktivere account ved å trykke på denne lenken\n\n 
            http://localhost/carpoolinguit/registrationSuccess.php?id=" . $id;

            sendMail($to,$subject,$body);

            $createNewUser->setIdVerification($id);
            $user_id = $database->createUser($createNewUser, false);
            $lastPrefId = $database->selectMaxPrefId();
            for ($i = 1; $i <= $lastPrefId; $i++) {
                $database->createUserPreferenses($user_id, $i);
            }

            echo $twig->render('login.twig', ['hide_navbar' => true]);
            echo "<script>$('#regPlus').modal('show')</script>";
        } else {
            echo $twig->render('registration.twig', ['hide_navbar' => true, 'emailExists' => true]);
        }

}
elseif (!isset($_POST['userRegistration'])) {

    echo $twig->render('registration.twig', ['hide_navbar' => true]);
}



