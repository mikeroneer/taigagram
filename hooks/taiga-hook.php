<?php
/**
 * Project: taigagram
 * May 2016
 * by Michael Rockenschaub
 * http://www.mikerocode.com
 */

require_once "../config.php";
require_once "../models/TaigagramBot.php";
require_once "../models/TaigaMessage.php";

/**
 * Verifies a sha1 signature.
 * @param $key
 * @param $data
 * @param $signature
 * @return bool True if the signature is verified, otherwise false.
 */
function signature_verified($key, $data, $signature)
{
    return hash_hmac("sha1", $data, $key) == $signature;
}

// proof for user identification in url
if (isset($_GET[PARAM_IDENTIFICATION])) {

    // instantiate a new bot object
    $bot = new TaigagramBot(TELEGRAM_API_TOKEN, $_GET[PARAM_IDENTIFICATION]);
    $bot->getTaigaSecretKeyFromDatabase();

    // get the taiga signature
    $signature = $_SERVER['HTTP_X_TAIGA_WEBHOOK_SIGNATURE'];

    // read post data
    $requestContent = file_get_contents("php://input");

    if (signature_verified($bot->taigaSecretKey, $requestContent, $signature)) {

        // try to json decode content
        if (($data = json_decode($requestContent, true)) != null) {
            // parse taiga message
            $message = TaigaMessage::parseJson($data);

            if ($message != null) {
                $bot->sendMessageHTML($bot->chatId, $message);
            }
        }
    } else {
        echo "I'm sorry, but this really looks like a wrong authentication...";
    }
}
