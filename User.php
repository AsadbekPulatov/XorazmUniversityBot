<?php

require_once "connect.php";

class User
{
    private $chat_id;

    function __construct($chat_id)
    {
        $this->chat_id = $chat_id;
    }

    function createUser($chat_id, $name){
        global $connect;
        $this->chat_id = $chat_id;
        $sql = "DELETE FROM users WHERE chat_id = '$chat_id'";
        $connect->query($sql);
        $sql = "INSERT INTO users(chat_id, name) values('$chat_id', '$name')";
        $connect->query($sql);
    }

    function setPage($page)
    {
        global $connect;
        $sql = "UPDATE users SET page = '$page' WHERE chat_id = '$this->chat_id'";
        $connect->query($sql);
    }

    function getPage()
    {
        global $connect;
        $sql = "SELECT * FROM users WHERE chat_id = '$this->chat_id'";
        $result = $connect->query($sql);

        $row = $result->fetch_assoc();
        return $row['page'];
    }
}