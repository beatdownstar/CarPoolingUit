<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');

$database = new Database_queries($db);

if (isset($_POST['btnFeedback'])) {

    $newFeedback = new Feedback();

    $feedback_text = trim(filter_input(INPUT_POST,'feedbackText', FILTER_SANITIZE_STRING));
    $senderId = $_SESSION['user']->getUserID();

    $newFeedback->setFeedbackText($feedback_text);
    $newFeedback->setSenderId($senderId);

    $database->sendFeedback($newFeedback);

    echo $twig->render('sendFeedback.twig', ['feedbackSent' => true]);
}

else {
    echo $twig->render('sendFeedback.twig');
}
