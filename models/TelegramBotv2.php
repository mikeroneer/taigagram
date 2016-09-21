<?php
/**
 * Project: taigagram
 * May 2016
 * by Michael Rockenschaub
 * http://www.mikerocode.com
 */

define('API_URL', 'https://api.telegram.org/bot');

abstract class TelegramBotv2
{
    public $botUsername;
    protected $botId;
    protected $botToken;
    public $chatId;

    public function __construct($botToken)
    {
        $this->botToken = $botToken;
        $this->init();
    }

    protected function init()
    {
        // get data of bot
        $botData = $this->getMe();
        $botData = json_decode($botData, true);

        // assign data of bot
        if ($botData["ok"] == "true") {
            $this->botId = $botData["result"]["id"];
            $this->botUsername = $botData["result"]["username"];
        }
    }

    /**
     * Gets some information about the bot from the Telegram Bot API
     * @return bool|string
     */
    public function getMe()
    {
        return $this->apiRequest('getMe', false);
    }

    /**
     * Sends a message to the given chat_id in HTML.
     * @param $chat_id
     * @param $text
     */
    public function sendMessageHTML($chat_id, $text)
    {
        $this->apiRequest('sendMessage', array('chat_id' => $chat_id, 'text' => $text, 'parse_mode' => 'HTML', 'disable_web_page_preview' => 'true'));
    }

    /**
     * Enables the webhook from telegram server.
     * @param $url
     */
    public function enableWebhook($url)
    {
        echo $this->apiRequest('setWebhook', array('url' => $url));
    }

    /**
     * Disables the webhook from telegram server.
     */
    public function disableWebhook()
    {
        echo $this->apiRequest('setWebhook', array('url' => ''));
    }

    /**
     * Perfoms a request to the Telegram Bot API
     * @param $method
     * @param $parameters
     * @return bool|string False if there is an error in the request.
     */
    private function apiRequest($method, $parameters)
    {
        if (!is_string($method)) {
            error_log("Method name must be a string\n");
            return false;
        }

        if (!$parameters) {
            $parameters = array();
        } else if (!is_array($parameters)) {
            error_log("Parameters must be an array\n");
            return false;
        }

        foreach ($parameters as $key => &$val) {
            // encoding to JSON array parameters
            if (!is_numeric($val) && !is_string($val)) {
                $val = json_encode($val);
            }
        }

        $url = API_URL . $this->botToken . '/' . $method . '?' . http_build_query($parameters);
        return file_get_contents($url);
    }
}