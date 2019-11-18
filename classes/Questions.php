<?php

class Questions {
    private $question_id;
    private $question;
    private $answer;

    public function __construct() {
    }

    public function getQuestionId() {
        return $this->question_id;
    }

    public function setQuestionId($question_id) {
        $this->question_id = $question_id;
    }

    public function getQuestion() {
        return $this->question;
    }

    public function setQuestion($question) {
        $this->question = $question;
    }

    public function getAnswer() {
        return $this->answer;
    }

    public function setAnswer($answer) {
        $this->answer = $answer;
    }

}