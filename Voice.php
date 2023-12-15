<?php

require_once "connect.php";

class Voice
{
    function getVoices()
    {
        global $connect;
        $sql = "SELECT * FROM voices";
        $result = $connect->query($sql);
        $voices = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $voices[] = $row;
            }
        }
        return $voices;
    }
}