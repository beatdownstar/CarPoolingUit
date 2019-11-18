<?php

class Feedback
{

    private $feedback_id;
    private $sender_id;
    private $feedback_text;
    private $date;
    private $answered;
    private $senderName;
    private $senderSurname;
    private $epost;


    function __construct()
    {
    }


    function getFeedbackId()
    {
        return $this->feedback_id;
    }

    function setFeedbackId($feedback_id)
    {
        $this->feedback_id = $feedback_id;
    }

    function getSenderId()
    {
        return $this->sender_id;
    }

    function setSenderId($sender_id)
    {
        $this->sender_id = $sender_id;
    }

    function getFeedbackText()
    {
        return $this->feedback_text;
    }

    function setFeedbackText($feedback_text)
    {
        $this->feedback_text = $feedback_text;
    }


    function getSenderName()
    {
        return $this->senderName;
    }

    function getSenderSurname()
    {
        return $this->senderSurname;
    }

    function getDate()
    {
        return $this->date;
    }

    function getAnswered()
    {
        return $this->answered;
    }

    function setEpost($epost)
    {
        $this->epost = $epost;
    }

    function getEpost()
    {
        return $this->epost;
    }

    function setAnswered($answered)
    {
        $this->answered = $answered;
    }

}