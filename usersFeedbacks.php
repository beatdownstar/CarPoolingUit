<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');
require ('sendMail.php');

$database = new Database_queries($db);

$feedbacks = $database->showFeedbacks();

if (isset($_POST['answerToFeedback'])) {

    $epost = trim(filter_input(INPUT_POST,'senderEmail', FILTER_SANITIZE_STRING));
    $feedback_id = trim(filter_input(INPUT_POST,'feedbackID', FILTER_SANITIZE_NUMBER_INT));
    $answer = trim(filter_input(INPUT_POST,'answerText', FILTER_SANITIZE_STRING));

    $to = $epost;
    $subject = "Svar på tilbakemelding";
    $body = $answer;
    $database->setAnswered($feedback_id);
    $feedbacks = $database->showFeedbacks();
    sendMail($to,$subject,$body);
    echo $twig->render('usersFeedbacks.twig', ['feedbacks' => $feedbacks, 'answerSent' => true]);

}
else {
    echo $twig->render('usersFeedbacks.twig', ['feedbacks' => $feedbacks]);
}

