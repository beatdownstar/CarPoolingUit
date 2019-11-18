<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');
require ('sendMail.php');

$database = new Database_queries($db);

if (isset($_POST['sendRecoverEmail'])) {

    $epost = trim(filter_input(INPUT_POST,'recoverPassEpost', FILTER_SANITIZE_STRING));
    if ($database->emailExists($epost)) {
        $id = md5(uniqid(rand(), 1));
        $database->setPasswordRecoveryId($id, $epost);
        $to = $epost;
        $subject = "Passord gjenopprettelse";
        $body = "For å gjenopprette passord, trykk på denne lenken\n\n 
            http://localhost/carpoolinguit/newPassword.php?id=" . $id;

        sendMail($to, $subject, $body);

        echo $twig->render('login.twig', ['hide_navbar' => true, 'recoverEpostSent' => true]);
    }
    else
        echo $twig->render('login.twig', ['hide_navbar' => true, 'epostNoExist' => true]);
}
