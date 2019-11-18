<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');

$database = new Database_queries($db);

$questions = $database->getAllQuestions();

echo $twig->render('infoAboutSystem.twig', ['email' => $_SESSION['user']->getUsername(), 'questions' => $questions]);