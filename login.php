<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
include 'config.php';

$title = 'Innlogging';
$database = new Database_queries($db);

$redirect = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : 'index';

if (isset($_SESSION['user'])) {
    header('Location: index');
}


if (isset($_POST['enter']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $userExists = $database->emailExists($username);
    $feideUser = $database->checkIfFeideUser($username);

    $tablePassword = $database->getUserPassword($username);

    if ($userExists == true && !$feideUser && password_verify($password, $tablePassword)) {
        $userId = $database->getUserId($username);
        $accepted = $database->getAccepted($userId);
        $active = $database->isUserBanned($userId);
        $userObject = $database->showUserData($userId);

        if ($active==0 and $accepted==1) {
            $_SESSION['user'] = new Session($username, $userId);
            $_SESSION['userObject'] = $userObject;
            $_SESSION['chats'] = array();
            header('Location: ' . $redirect);
        } else {
            echo $twig->render('login.twig', ['hide_navbar' => true, 'title' => $title, 'banned' => true]);
        }
    } else {
        echo $twig->render('login.twig', ['hide_navbar' => true, 'title' => $title, 'error' => true]);
    }


} elseif (!isset($_POST['enter'])) {
    echo $twig->render('login.twig', ['hide_navbar' => true, 'title' => $title]);
} else
    echo "En feil oppsto, last inn siden på nytt og prøv igjen";