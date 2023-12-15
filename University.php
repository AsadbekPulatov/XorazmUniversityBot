<?php

require_once "connect.php";

class University
{
    function getUniversities()
    {
        global $connect;
        $sql = "SELECT * FROM universities";
        $result = $connect->query($sql);
        $universities = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $universities[] = $row;
            }
        }
        return $universities;
    }
}