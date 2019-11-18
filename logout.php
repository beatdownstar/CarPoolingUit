<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});

require_once 'config.php';

$feideUser = $_SESSION['userObject']->getFeideUser();
session_destroy();

if($feideUser)
    header('Location: https://auth.dataporten.no/logout');
else
    header('Location: login.php');

exit;
?>