<?php

include 'Telegram.php';
require_once 'User.php';
require_once 'Channel.php';
require_once 'University.php';
require_once 'Voice.php';

$bot_token = getenv("BOT_TOKEN");
$admin_chat_id = getenv("ADMIN_CHAT_ID");

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
        case "main":break;
    }
}

function showMainPage()
{
    global $chat_id, $telegram, $user;
    $user->setPage("main");

    $text = "Assalomu alaykum botimizga xush kelibsiz!";
    $channels = new Channel();
    $channels = $channels->getChannels();

    $check = checkMembership($channels, $chat_id);
    $text.="<pre>".var_dump($check)."</pre>";

//    $options = [
//        [$telegram->buildKeyboardButton("Darslar ro'yxati 📁"), $telegram->buildKeyboardButton("Darsni qidirish 🔎")],
//    ];
//    $keyboard = $telegram->buildKeyBoard($options, false, true);
    $content = [
        'chat_id' => $chat_id,
//        'reply_markup' => $keyboard,
        'text' => $text,
    ];
    $telegram->sendMessage($content);
}

function checkMembership($channels, $userId) {
    global $telegram;

    foreach ($channels as $channel) {

        $options = [
            "user_id" => $userId,
            "chat_id" => $channel,
        ];
        $result = $telegram->getChatMember($options);
//        $result = file_get_contents("https://api.telegram.org/bot$botToken/getChatMember?chat_id=$channel&user_id=$userId");

        $status = $result['result']['status'];
        if ($status == 'member' || $status == 'administrator' || $status == 'creator') {
            return true;
        }
    }

    return false;
}

?>