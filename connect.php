<?php

$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$servername = $url["host"] ?? null;
$username = $url["user"] ?? null;
$password = $url["pass"] ?? null;
$database = substr($url["path"], 1);

$connect = new mysqli($servername, $username, $password, $database);
