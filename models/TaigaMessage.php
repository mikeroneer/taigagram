<?php
/**
 * Project: taigagram
 * May 2016
 * by Michael Rockenschaub
 * http://www.mikerocode.com
 */

define("MAX_LENGTH_DESCRIPTION", 80);

class TaigaMessage
{
    /**
     * Parses a string for taiga messages.
     * @param $data String to be parsed.
     * @return null|string
     */
    public static function parseJson($data)
    {
        $message = null;

        switch ($data["action"]) {
            case "create":
                $message = self::getCreateMessage($data["type"], $data);
                break;

            case "change":
                $message = self::getChangeMessage($data);
                break;

            case "delete":
                $message = self::getDeleteMessage($data);
                break;

            case "test":
                $message = self::getTestMessage();
                break;
        }

        if ($message != null) {
            if ($data["by"]["username"] != null) {
                $message = "<b>" . $data["by"]["username"] . "</b> " . $message;
            } else {
                $message = "<b>" . $data["by"]["full_name"] . "</b> " . $message;
            }
        }

        return $message;
    }

    private static function getCreateMessage($type, $data)
    {
        $createMessage = "created" . self::getRefStringWithLink($data);

        switch ($type) {
            case "wikipage":
                $createMessage .= ": " . self::shortenInfoText($data["data"]["content"]);
                break;

            case "issue":
                $createMessage .= ": \n"
                    . $data["data"]["type"]["name"] . " | Priority: " . $data["data"]["priority"]["name"]
                    . self::shortenInfoText($data["data"]["description"]);
                break;

            case "userstory":
                $createMessage .= ": " . self::shortenInfoText($data["data"]["description"]);
                break;

            case "task":
                $createMessage .= ": " . self::shortenInfoText($data["data"]["description"]);
                break;
        }

        return $createMessage;
    }

    private static function getChangeMessage($data)
    {
        $changeMessage = null;

        foreach ($data["change"]["diff"] as $key => $value) {
            if ($changeMessage != null) {
                $changeMessage .= "\n";
            }

            switch ($key) {
                case "assigned_to":
                    if ($value["to"] == $data["by"]["full_name"]) {
                        $changeMessage .= "assigned" . self::getRefStringWithLink($data) . " to " . "him-|her-|itself.";

                    } else if ($value["from"] == null) {
                        $changeMessage .= "assigned" . self::getRefStringWithLink($data) . " to " . "\"" . $value["to"] . "\"";

                    } else if ($value["to"] == null) {
                        return null;
                    } else {
                        $changeMessage .= "changed assignment of"
                            . self::getRefStringWithLink($data)
                            . self::getChangeFromToString($value);
                    }
                    break;

                case "description_diff":
                    $description = $data["data"]["description"];

                    if (strlen($description) <= 0) {
                        $changeMessage .= "removed description of" . self::getRefStringWithLink($data);
                        break;
                    } else {
                        $changeMessage .= "changed description of" . self::getRefStringWithLink($data) . ": ";
                    }

                    $changeMessage .= self::shortenInfoText($description);
                    break;

                case "attachments":
                    $changeMessage .= self::getDefaultChangeMessage($key, $value, $data);
                    break;

                case "points":
                    $changeMessage .= self::getDefaultChangeMessage($key, $value, $data);
                    break;

                case "type":
                    $changeMessage .= self::getDefaultChangeMessage($key, $value, $data);
                    break;

                case "priority":
                    $changeMessage .= self::getDefaultChangeMessage($key, $value, $data);
                    break;

                case "severity":
                    $changeMessage .= self::getDefaultChangeMessage($key, $value, $data);
                    break;

                case "status":
                    $changeMessage .= self::getDefaultChangeMessage($key, $value, $data);
                    break;

                case "subject":
                    $changeMessage .= self::getDefaultChangeMessage($key, $value, $data);
                    break;

                case "milestone":
                    $changeMessage .= self::getDefaultChangeMessage($key, $value, $data);
                    break;

                case "estimated_start":
                    if ($value["from"] != $value["to"]) {
                        $changeMessage .= "changed estimated start of" . self::getRefStringWithLink($data) . self::getChangeFromToString($value) . ".";
                    }
                    break;

                case "estimated_finish":
                    if ($value["from"] != $value["to"]) {
                        $changeMessage .= "changed estimated finish of" . self::getRefStringWithLink($data) . self::getChangeFromToString($value) . ".";
                    }
                    break;

                case "tags":
                    $changeMessage .= "changed " . $key . " of" . self::getRefStringWithLink($data) . ".";
                    break;

                case "name":
                    $changeMessage .= self::getDefaultChangeMessage($key, $value, $data);
                    break;

                case "backlog_order":
                    break;

                case "team_requirement":
                    break;

                case "client_requirement":
                    break;

                case "is_locaine":
                    break;
            }
        }

        return $changeMessage;
    }

    private static function getDeleteMessage($data)
    {
        return "deleted " . self::getRefString($data) . ".";
    }

    private static function getTestMessage()
    {
        return "is happy that I'm configured correctly. :)";
    }

    private static function getDefaultChangeMessage($key, $value, $data)
    {
        $defaultChangeMessage = "changed " . $key . " of" . self::getRefStringWithLink($data);
        $defaultChangeMessage .= self::getChangeFromToString($value) . ".";

        return $defaultChangeMessage;
    }

    private static function getRefStringWithLink($data)
    {
        return " <a href=\"" . $data["data"]["permalink"] . "\">" . self::getRefString($data) . "</a>";
    }

    private static function getRefString($data)
    {
        $refString = $data["type"] . " ";

        if ($data["data"]["ref"] != null) {
            $refString .= "#" . $data["data"]["ref"] . " ";
        }

        if ($data["data"]["subject"] != null) {
            $refString .= $data["data"]["subject"];
        } else if ($data["data"]["slug"] != null) {
            $refString .= $data["data"]["slug"];
        }

        return $refString;
    }

    private static function getChangeFromToString($value)
    {
        $message = "";

        if ($value["from"] != $value["to"]) {
            if ($value["from"] != null) {
                $message .= " from " . "\"" . $value["from"] . "\"";
            }

            if ($value["to"] != null) {
                $message .= " to " . "\"" . $value["to"] . "\"";
            }
        }

        return $message;
    }

    private static function shortenInfoText($infoText)
    {
        if (strlen($infoText) > MAX_LENGTH_DESCRIPTION) {
            $infoText = substr($infoText, 0, MAX_LENGTH_DESCRIPTION) . "...";
        }

        if ($infoText != null) {
            $infoText = "\n<i>\"" . $infoText . "\"</i>";
        }

        return $infoText;
    }
}