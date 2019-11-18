<?php


spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');

$database = new Database_queries($db);
$questions = $database->getAllQuestions();

if (isset($_POST['newQuestion'])) {

    $createNewQuestion = new Questions();

    $question = trim(filter_input(INPUT_POST,'questionText', FILTER_SANITIZE_STRING));
    $answer = trim(filter_input(INPUT_POST,'answerText', FILTER_SANITIZE_STRING));

    $createNewQuestion->setQuestion($question);
    $createNewQuestion->setAnswer($answer);

    $database->createQuestion($createNewQuestion);
    $questions = $database->getAllQuestions();
    echo $twig->render('createQuestion.twig', ['questions' => $questions]);
}

else {
    echo $twig->render('createQuestion.twig', ['questions' => $questions]);
}
