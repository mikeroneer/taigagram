<?php
/**
 * Project: taigagram
 * May 2016
 * by Michael Rockenschaub
 * http://www.mikerocode.com
 */

// telegram bot api settings
define("TELEGRAM_API_TOKEN", "change-my_api_token_got_from_bot_father");
define("TELEGRAM_WEBHOOK_URL", "change-https://www.example.com/hooks/telegram-hook.php");

// database settings
define("DB_SERVER", "change-database.example.com");
define("DB_USERNAME", "change-my_db_username");
define("DB_PASSWORD", "change-my_db_password");
define("DB_DATABASE", "change-my_database");
define("DB_TABLE", "change-my_db_table");

// url parameter name for identifying the correct group-chat
define("PARAM_IDENTIFICATION", "id");

// base of the taiga webhook url, id will be concatenated programmatically (use rewrite to shorten this url)
define("TAIGA_WEBHOOK_BASE_URL", "change-http://www.example.com/hooks/taiga-hook.php?" . PARAM_IDENTIFICATION . "=");

// your chat id where debug messages can be sent to
define("DEBUG_CHAT_ID", change-00000000);
