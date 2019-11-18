<?php
/**
 * Created by PhpStorm.
 * User: Markus
 * Date: 20.02.2019
 * Time: 14:58
 */

$host = "kark.uit.no";
$dbname = "databasenavn";
$username = "placeholder";
$password = "placeholder";

try
{
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
}
catch(PDOException $e)
{
    print($e->getMessage());
}

?>