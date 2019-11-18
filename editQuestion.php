<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 03.04.2019
 * Time: 18:22
 */

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');
$database = new Database_queries($db);
$questions = $database->getAllQuestions();

if (isset($_POST['changeQuest'])) {
    $updateQuestion = new Questions();

    $question = trim(filter_input(INPUT_POST, 'questionText', FILTER_SANITIZE_STRING));
    $answer = trim(filter_input(INPUT_POST, 'answerText', FILTER_SANITIZE_STRING));
    $questionId = trim(filter_input(INPUT_POST, 'questionId', FILTER_SANITIZE_NUMBER_INT));

    $updateQuestion->setQuestionId($questionId);
    $updateQuestion->setQuestion($question);
    $updateQuestion->setAnswer($answer);

    $database->updateQuestion($updateQuestion);
    $questions = $database->getAllQuestions();
    echo $twig->render('editQuestion.twig', array('questions' => $questions));

}
elseif (isset($_POST['deleteQuestion'])) {

    $question = trim(filter_input(INPUT_POST, 'questionToEdit', FILTER_SANITIZE_STRING));
    $questionId = trim(filter_input(INPUT_POST, 'question_id', FILTER_SANITIZE_NUMBER_INT));

    echo $twig->render('editQuestion.twig', ['questions' => $questions, 'questionText' => $question, 'question_id' => $questionId]);
    echo "<script>$('#deleteQuestionModal').modal('show')</script>";
}
elseif (isset($_POST['saveDeletedQuestion'])) {

    $questionId = trim(filter_input(INPUT_POST, 'questionIdDeleteModal', FILTER_SANITIZE_STRING));
    $database->deleteQuestion($questionId);
    $questions = $database->getAllQuestions();

    echo $twig->render('editQuestion.twig', ['questions' => $questions]);

}
    else {
    $questions = $database->getAllQuestions();
    echo $twig->render('editQuestion.twig', array('questions' => $questions));
}
