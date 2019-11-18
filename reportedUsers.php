<?php

spl_autoload_register(function ($class_name) {
require_once 'classes/' . $class_name . '.php';
});

require_once('config.php');

$database = new Database_queries($db);
$raports = $database->showReportedUsers();

if (isset($_POST['rapportFixed'])) {

    $rapport_id = trim(filter_input(INPUT_POST,'raportID', FILTER_SANITIZE_NUMBER_INT));

    $database->setSolved($rapport_id);
    $raports = $database->showReportedUsers();

    echo $twig->render('reportedUsers.twig', ['raports' => $raports, 'solved' => true]);

}

else {

    echo $twig->render('reportedUsers.twig', ['raports' => $raports]);
}

