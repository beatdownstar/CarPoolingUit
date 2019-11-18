<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');
$database = new Database_queries($db);

if (!empty($_GET['id'])) {

    $database->setAccept(filter_input(INPUT_GET, "id", FILTER_SANITIZE_SPECIAL_CHARS));

}

echo $twig->render('registrationSuccess.twig', ['hide_navbar' => true]);