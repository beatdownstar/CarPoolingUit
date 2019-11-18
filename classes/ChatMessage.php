<?php
/**
 * Created by PhpStorm.
 * User: ronny
 * Date: 01.04.2019
 * Time: 19:31
 */

class ChatMessage {
    private
        $author_id,
        $author,
        $author_name,
        $id,
        $message,
        $timestamp;

    /**
     * ChatMessage constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getAuthorId()
    {
        return $this->author_id;
    }

    /**
     * @param mixed $author_id
     */
    public function setAuthorId($author_id): void
    {
        $this->author_id = $author_id;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getFormattedTimestamp() {
        return $this->timeago(strtotime($this->timestamp));
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getAuthorName()
    {
        return $this->author_name;
    }

    /**
     * @param mixed $author_name
     */
    public function setAuthorName($author_name): void
    {
        $this->author_name = $author_name;
    }


    private function timeago($timestamp, $full = false) {
        $datetime = new DateTime;
        $datetime->setTimestamp($timestamp);
        $now = new DateTime;
        $ago = $datetime;
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'år',
            'm' => 'måned',
            'w' => 'uke',
            'd' => 'dag',
            'h' => 'tim',
            'i' => 'minutt',
            's' => 'sekund',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                if ($v != 'år') {
                    $plural = $v == 'uke' ? 'r' : 'er';
                    $v = $diff->$k . ' ' . $v . ($diff->$k == 1 ? '' : $plural);
                }
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' siden' : 'nettopp';
    }




}