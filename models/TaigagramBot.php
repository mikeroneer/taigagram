<?php
/**
 * Project: taigagram
 * May 2016
 * by Michael Rockenschaub
 * http://www.mikerocode.com
 */

require_once "TelegramBotv2.php";
require_once "../sql-connection.php";

class TaigagramBot extends TelegramBotv2
{
    public $taigaSecretKey;

    public function __construct($botToken, $chatId)
    {
        parent::__construct($botToken);
        $this->chatId = $chatId;
    }

    public function addToDatabase($chatTitle, $addedBy, $addedById, $addedByFullName, $chatType)
    {
        $sqlConnection = getSqlConnection();

        $sqlStatement = $sqlConnection->prepare("INSERT INTO " . DB_TABLE . " (taiga_secret_key, chat_id, chat_title, added_by, added_by_id, added_by_full_name, chat_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $sqlStatement->bind_param("sississ", $this->taigaSecretKey, $this->chatId, $chatTitle, $addedBy, $addedById, $addedByFullName, $chatType);
        $sqlStatement->execute();
        $sqlStatement->close();
        $sqlConnection->close();
    }

    public function getTaigaSecretKeyFromDatabase()
    {
        $sqlConnection = getSqlConnection();

        $sqlStatement = $sqlConnection->prepare("SELECT taiga_secret_key FROM " . DB_TABLE . " WHERE chat_id = ?");
        $sqlStatement->bind_param("i", $this->chatId);
        $sqlStatement->execute();
        $sqlStatement->bind_result($this->taigaSecretKey);
        $sqlStatement->fetch();
        $sqlStatement->close();
        $sqlConnection->close();
    }

    public function updateDatabase()
    {
        $sqlConnection = getSqlConnection();

        $sqlStatement = $sqlConnection->prepare("UPDATE " . DB_TABLE . "  SET last_access = CURRENT_TIMESTAMP, total_messages_sent = total_messages_sent + 1 WHERE chat_id = ?");
        $sqlStatement->bind_param("i", $this->chatId);
        $sqlStatement->execute();
        $sqlStatement->close();
        $sqlConnection->close();
    }

    public function updateChatTitleInDatabase($newChatTitle)
    {
        $sqlConnection = getSqlConnection();
        $sqlStatement = $sqlConnection->prepare("UPDATE " . DB_TABLE . "  SET chat_title = ? WHERE chat_id = ?");
        $sqlStatement->bind_param("si", $newChatTitle, $this->chatId);
        $sqlStatement->execute();
        $sqlStatement->close();
        $sqlConnection->close();
    }

    public function deleteInDatabase()
    {
        $sqlConnection = getSqlConnection();

        $sqlStatement = $sqlConnection->prepare("DELETE FROM " . DB_TABLE . "  WHERE chat_id = ?");
        $sqlStatement->bind_param("i", $this->chatId);
        $sqlStatement->execute();
        $sqlStatement->close();
        $sqlConnection->close();
    }

    public function sendMessageHTML($chat_id, $text)
    {
        parent::sendMessageHTML($chat_id, $text);
        $this->updateDatabase();
    }
}