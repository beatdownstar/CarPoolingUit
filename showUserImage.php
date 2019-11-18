<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');

if (isset($_GET['user_id'])) {

$userdb = new Database_queries($db);
header("content-type: image/*");
$photo = $userdb->showUserPhoto($_GET['user_id']);
echo $photo['photo'];

}
