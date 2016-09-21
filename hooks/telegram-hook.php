<?php
/**
 * Project: taigagram
 * May 2016
 * by Michael Rockenschaub
 * http://www.mikerocode.com
 */

require_once "../config.php";
require_once "../models/TaigagramBot.php";
require_once "../sql-connection.php";

$bot = new TaigagramBot(TELEGRAM_API_TOKEN, null);

// read post data
$responseJson = file_get_contents("php://input");

// decode
$response = json_decode($responseJson, true);

$bot->chatId = $response["message"]["chat"]["id"];

$bot->sendMessageHTML(DEBUG_CHAT_ID, $response);

if ($response["message"]["new_chat_member"]["username"] == $bot->botUsername || $response["message"]["group_chat_created"] == true) {
    $chatType = $response["message"]["chat"]["type"];
    $addedBy = $response["message"]["from"]["username"];
    $chatTitle = $response["message"]["chat"]["title"];
    $addedById = $response["message"]["from"]["id"];
    $addedByFirstName = $response["message"]["from"]["first_name"];
    $addedByLastName = $response["message"]["from"]["last_name"];

    $bot->taigaSecretKey = md5(openssl_random_pseudo_bytes(32));

    $bot->sendMessageHTML($bot->chatId,
        "Hey " . $chatTitle . "-team, thanks for adding me to your chat!\n\n" .
        "Just add these two lines under <b>Admin >> Integrations >> Webhooks</b> to your Taiga-project and I'll handle that nobody misses anything.\n" .
        "<b>payload url:</b> " . TAIGA_WEBHOOK_BASE_URL . $bot->chatId .
        "\n<b>secret key:</b> " . $bot->taigaSecretKey .
        "\n\nRemember that I'm just getting born! If you're interested in how my life begins, have a look at taigagram.mikerocode.com regulary. ;-)");

    $bot->addToDatabase($chatTitle, $addedBy, $addedById, $addedByFirstName . " " . $addedByLastName, $chatType);

} else if ($response["message"]["left_chat_member"]["username"] == $bot->botUsername) {
    $bot->deleteInDatabase();
} else if (array_key_exists("new_chat_title", $response["message"])) {
    $bot->updateChatTitleInDatabase($response["message"]["new_chat_title"]);
} else if (strpos($response["message"]["text"], "/start") === 0) {
    $bot->sendMessageHTML($bot->chatId, "Please add me to you team-group-chat!");
}

