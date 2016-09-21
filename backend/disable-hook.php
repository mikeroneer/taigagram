<?php
/**
 * Project: taigagram
 * May 2016
 * by Michael Rockenschaub
 * http://www.mikerocode.com
 */

require_once "../config.php";
require_once "../models/TaigagramBot.php";

if (isset($_GET["t"])) {
    if ($_GET["t"] == "onelinktorulethemall") {
        $bot = new TaigagramBot(TELEGRAM_API_TOKEN, null);
        $bot->disableWebhook();
    }
}