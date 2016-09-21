<?php
/**
 * Project: taigagram
 * May 2016
 * by Michael Rockenschaub
 * http://www.mikerocode.com
 */

require_once "config.php";

function getSqlConnection()
{
    $sqlConnection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    if ($sqlConnection->connect_error) {
        echo "connection to database failed - " . $sqlConnection->connect_error;
    }

    return $sqlConnection;
}
