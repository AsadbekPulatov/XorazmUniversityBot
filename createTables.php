<?php

require_once 'connect.php';

global $connect;

$sql = "CREATE TABLE users (
    chat_id VARCHAR(30) NOT NULL,
    name VARCHAR(255) NOT NULL,
    page VARCHAR(30) NOT NULL
    )";

if ($connect->query($sql) === TRUE) {
    echo "Table users created successfully";
} else {
    echo "Error creating table: " . $connect->error;
}

$sql = "CREATE TABLE universities (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
    )";

if ($connect->query($sql) === TRUE) {
    echo "Table universities created successfully";
} else {
    echo "Error creating table: " . $connect->error;
}

$sql = "CREATE TABLE channels (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL
    )";

if ($connect->query($sql) === TRUE) {
    echo "Table channels created successfully";
} else {
    echo "Error creating table: " . $connect->error;
}

$sql = "CREATE TABLE voices (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(30) NOT NULL,
    university_id INT(6) NOT NULL
    )";

if ($connect->query($sql) === TRUE) {
    echo "Table voices created successfully";
} else {
    echo "Error creating table: " . $connect->error;
}