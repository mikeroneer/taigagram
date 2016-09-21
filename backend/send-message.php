<?php
/**
 * Project: taigagram
 * May 2016
 * by Michael Rockenschaub
 * http://www.mikerocode.com
 */

require_once "../config.php";
require_once "../models/TaigagramBot.php";

$bot = new TaigagramBot(TELEGRAM_API_TOKEN, null);
$bot->sendMessageHTML($_GET["chat_id"], $_GET["m"]);