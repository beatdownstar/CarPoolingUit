<?php

class ReportedUsers
{

    private $rapport_id;
    private $reported_user_id;
    private $sender_user_id;
    private $reason;
    private $description;
    private $date;
    private $solved;

    private $reportedUserName;
    private $reportedUserSurname;
    private $senderUserName;
    private $senderUserSurname;

    function __construct()
    {
}


    function getRapportId()
    {
        return $this->rapport_id;
    }

    function setRapportId($rapport_id)
    {
        $this->rapport_id = $rapport_id;
    }

    function getReportedUserId()
    {
        return $this->reported_user_id;
    }

    function setReportedUserId($reported_user_id)
    {
        $this->reported_user_id = $reported_user_id;
    }

    function getSenderUserId()
    {
        return $this->sender_user_id;
    }

    function setSenderUserId($sender_user_id)
    {
        $this->sender_user_id = $sender_user_id;
    }

    function getReason()
    {
        return $this->reason;
    }

    function setReason($reason)
    {
        $this->reason = $reason;
    }

    function getDescription()
    {
        return $this->description;
    }

    function setDescription($description)
    {
        $this->description = $description;
    }

    function getReportedUserName()
    {
        return $this->reportedUserName;
    }

    function getReportedUserSurname()
    {
        return $this->reportedUserSurname;
    }

    function getSenderUserName()
    {
        return $this->senderUserName;
    }

    function getSenderUserSurname()
    {
        return $this->senderUserSurname;
    }

    function getDate()
    {
        return $this->date;
    }

    function getSolved()
    {
        return $this->solved;
    }

    function setSolved($solved)
    {
        $this->solved = $solved;
    }

}