<?php

require_once "connect.php";

class Channel
{
    function getChannels()
    {
        global $connect;
        $sql = "SELECT * FROM channels";
        $result = $connect->query($sql);
        $channels= [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $channels[] = $row;
            }
        }
        return $channels;
    }
}