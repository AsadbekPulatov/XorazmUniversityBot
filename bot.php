<?php
include "Telegram.php";
require_once "connect.php";
$botToken = getenv("BOT_TOKEN"); // Botning tokeni
$adminChatId = getenv("ADMIN_CHAT_ID"); // Adminning IDsi

// Telegram'dan kelgan so'rovnoma
$telegram = new Telegram($botToken);

// Foydalanuvchi haqida ma'lumot
$chatType = $telegram['message']['chat']['type'];
$userId = $telegram['message']['from']['id'];

// Ovozlar bazaga saqlanadi
function saveVoiceToDatabase($userId, $voiceFileId) {
    global $botToken;

    // Ma'lumotlar bazasiga ulanish
    $dbHost = "DB_HOST"; // Ma'lumotlar bazasi serverining manzili
    $dbUser = "DB_USER"; // Ma'lumotlar bazasi foydalanuvchisi
    $dbPass = "DB_PASSWORD"; // Ma'lumotlar bazasi paroli
    $dbName = "telegram_bot"; // Ma'lumotlar bazasi nomi

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    // Bog'langanlikni tekshirish
    if ($conn->connect_error) {
        die("Bog'lanishda xatolik: " . $conn->connect_error);
    }

    // Ovozni bazaga saqlash
    $sql = "INSERT INTO voice_messages (user_id, voice_file_id) VALUES ($userId, '$voiceFileId')";
    $result = $conn->query($sql);

    // Ma'lumotlar bazasini yopish
    $conn->close();
}

// Ovozlarni bazadan olish
function getVoiceMessagesFromDatabase() {
    global $botToken, $adminChatId;

    // Ma'lumotlar bazasiga ulanish
    $dbHost = "DB_HOST"; // Ma'lumotlar bazasi serverining manzili
    $dbUser = "DB_USER"; // Ma'lumotlar bazasi foydalanuvchisi
    $dbPass = "DB_PASSWORD"; // Ma'lumotlar bazasi paroli
    $dbName = "telegram_bot"; // Ma'lumotlar bazasi nomi

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    // Bog'langanlikni tekshirish
    if ($conn->connect_error) {
        die("Bog'lanishda xatolik: " . $conn->connect_error);
    }

    // Ovozlarni bazadan olish
    $sql = "SELECT * FROM voices";
    $result = $conn->query($sql);

    $voiceMessages = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $voiceMessages[] = [
                'user_id' => $row['user_id'],
                'voice_file_id' => $row['voice_file_id'],
            ];
        }
    }

    // Ma'lumotlar bazasini yopish
    $conn->close();

    return $voiceMessages;
}

// Universitetlar ro'yxatini olish
function getUniversities() {
    return ['Universitet 1', 'Universitet 2', 'Universitet 3']; // Universitetlar ro'yxatini o'zingizga moslashtiring
}

// Universitetlar ro'yxati uchun inline keyboard yaratish
function sendUniversitiesKeyboard($universities) {
    global $botToken, $userId;

    $keyboard = [
        'inline_keyboard' => [
            // Har bir universitet uchun tugma yaratish
            array_map(function ($university) {
                return ['text' => $university, 'callback_data' => $university];
            }, $universities),
        ],
    ];

    $replyMarkup = json_encode($keyboard);

    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$userId&text=Universitetni tanlang:&reply_markup=$replyMarkup";
    file_get_contents($url);
}

// Xabarni yuborish
function sendMessage($message) {
    global $botToken, $userId;

    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$userId&text=$message";
    file_get_contents($url);
}

// Admin ga xabarnoma yuborish
function sendAdminNotification($message) {
    global $botToken, $adminChatId;

    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$adminChatId&text=$message";
    file_get_contents($url);
}

// Ovoz xabarnomasi admin ga yuborish
function sendVoiceMessageToAdmin($voiceMessageId, $userId) {
    global $botToken, $adminChatId;

    $url = "https://api.telegram.org/bot$botToken/sendVoice?chat_id=$adminChatId&voice=$voiceMessageId&caption=Ovoz Xabari - Foydalanuvchi IDsi: $userId";
    file_get_contents($url);
}

// Ovoz qabul qilingan funksiya
if (isset($update['voice'])) {
    $voiceFileId = $update['voice']['file_id'];

    // Ovozni bazaga saqlash
    saveVoiceToDatabase($userId, $voiceFileId);

    // Foydalanuvchiga javob qaytarish
    sendMessage("Ovoz muvaffaqiyatli qabul qilindi! Rahmat!");
}

// Botga yuborilgan so'rovni tekshirish
if ($chatType == 'private') {
    $text = $update['message']['text'];

    // /start komandasi uchun
    if ($text == '/start') {
        // Kanallar ro'yxatini olish va a'zo bo'lganlikni tekshirish
        $channels = getChannels();
        $isMember = checkMembership($channels, $userId);

        if ($isMember) {
            sendMessage("Salom! Siz kanalga muvaffaqiyatli a'zo bo'lgansiz. Endi /universitetlar buyrug'ini bosing.");
            sendAdminNotification("Foydalanuvchi IDsi: $userId kanalga a'zo bo'lgan!");
        } else {
            sendMessage("Siz kanalga a'zo bo'lgan emassiz. Iltimos, avval kanalga a'zo bo'ling.");
        }
    }

    // /universitetlar komandasi uchun
    if ($text == '/universitetlar') {
        // Universitetlar ro'yxatini olish va inline keyboard yaratish
        $universities = getUniversities();
        sendUniversitiesKeyboard($universities);
    }

    // Admin ovozlarni olish va yuborish
    if ($text == '/get_voice_messages') {
        $voiceMessages = getVoiceMessagesFromDatabase();
        sendVoiceMessagesToAdmin($voiceMessages);
    }
}

// Foydalanuvchi kanalda a'zo bo'lib bo'lganmi?
function checkMembership($channels, $userId) {
    global $botToken;

    foreach ($channels as $channel) {
        $result = file_get_contents("https://api.telegram.org/bot$botToken/getChatMember?chat_id=$channel&user_id=$userId");
        $result = json_decode($result, true);

        $status = $result['result']['status'];

        if ($status == 'member' || $status == 'administrator' || $status == 'creator') {
            return true;
        }
    }

    return false;
}

// Kanallar ro'yxatini olish
function getChannels() {
    return ['@channel_username']; // Kanallar ro'yxatini o'zingizga moslashtiring
}

// Ovozlarni admin ga yuborish
function sendVoiceMessagesToAdmin($voiceMessages) {

    foreach ($voiceMessages as $voiceMessage) {
        $userId = $voiceMessage['user_id'];
        $voiceFileId = $voiceMessage['voice_file_id'];

        // Ovoz xabarnomasi admin ga yuboriladi
        sendVoiceMessageToAdmin($voiceFileId, $userId);
    }
}
?>
