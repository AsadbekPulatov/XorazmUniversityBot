<?php

include 'Telegram.php';
require_once 'User.php';
require_once 'Lesson.php';

$bot_token = "6254704543:AAFYOPGWTzIvM-MUlMModckcpuy1Sz4F80s";
$telegram = new Telegram($bot_token);

$chat_id = $telegram->ChatID();
$text = $telegram->Text();
$first_name = $telegram->FirstName();

$user = new User($chat_id);
$page = $user->getPage();

if ($text == "/start") {
    $user->createUser($chat_id, $first_name);
    showMainPage();
} else {
    switch ($page) {
        case "main":
            switch ($text) {
                case "Darslar ro'yxati 📁":
//                    showList();
                    break;
                case "Darsni qidirish 🔎":
//                    askDrug();
                    break;
            }
            break;
    }
}

function showMainPage()
{
    global $chat_id, $telegram, $user;
    $user->setPage("main");

    $text = "Bulutli texnologiyani o'rgatadigan botga xush kelibsiz!";

    $options = [
        [$telegram->buildKeyboardButton("Darslar ro'yxati 📁"), $telegram->buildKeyboardButton("Darsni qidirish 🔎")],
    ];
    $keyboard = $telegram->buildKeyBoard($options, false, true);
    $content = [
        'chat_id' => $chat_id,
        'reply_markup' => $keyboard,
        'text' => $text,
    ];
    $telegram->sendMessage($content);
}

?>