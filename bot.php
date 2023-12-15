<?php

include 'Telegram.php';
require_once 'User.php';
require_once 'Channel.php';
require_once 'University.php';

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
    checkAndShowChannels();
} else {
    switch ($page) {
        case "main": break;
    }
}

function checkAndShowChannels()
{
    global $chat_id, $telegram, $user;

    $channels = new Channel();
    $channels = $channels->getChannels();

    $check = checkMembership($channels, $chat_id);

    if ($check) {
        showUniversities();
    } else {
        showChannelsInlineButtons($channels);
    }
}

function checkMembership($channels, $userId)
{
    global $telegram;

    foreach ($channels as $channel) {

        $options = [
            "user_id" => $userId,
            "chat_id" => $channel->username,
        ];
        $result = $telegram->getChatMember($options);

        $status = $result['result']['status'];
        if ($status == 'member' || $status == 'administrator' || $status == 'creator') {
            return true;
        }
    }

    return false;
}

function showUniversities()
{
    global $chat_id, $telegram, $user;

    $user->setPage("main");
    $text = "Siz quyidagi universitetlar ro'yxatidan birini tanlashingiz mumkin:";
    $universities = new University();
    $universities = $universities->getUniversities();

    $options = [];
    foreach ($universities as $university) {
        $options[] = [$telegram->buildInlineKeyboardButton($university, "", "/university $university")];
    }
    $keyboard = $telegram->buildInlineKeyBoard($options);
    $content = [
        'chat_id' => $chat_id,
        'reply_markup' => $keyboard,
        'text' => $text,
    ];
    $telegram->sendMessage($content);
}

function showChannelsInlineButtons($channels)
{
    global $chat_id, $telegram, $user;

    $text = "Siz quyidagi kanallarga obuna bo'lmagansiz. Iltimos, obuna bo'lish uchun pastdagi tugmalardan birini bosing:";
    $options = [];
    foreach ($channels as $channel) {
        $options[] = [$telegram->buildInlineKeyboardButton($channel->name, "", "/subscribe $channel->username")];
    }
    $keyboard = $telegram->buildInlineKeyBoard($options);
    $content = [
        'chat_id' => $chat_id,
        'reply_markup' => $keyboard,
        'text' => $text,
    ];
    $telegram->sendMessage($content);
}

// Other functions...

?>
