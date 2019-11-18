<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 25.02.2019
 * Time: 14:58
 */

require_once('vendor/autoload.php');
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);
@session_start();

if(isset($_SESSION['userObject']))
  $twig->addGlobal('session', $_SESSION['userObject']);

$host = "kark.uit.no";
$dbname = "stud_v19_norus";
$username = "stud_v19_norus";
$password = "norus.uit";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $e) {

    print($e->getMessage());
}

