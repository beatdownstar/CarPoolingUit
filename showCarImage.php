<?php

require_once('config.php');

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});

if (isset($_GET['vehicle_id'])) {

    $tripdb = new Database_queries($db);
    header("content-type: image/*");
    $photo = $tripdb->showCarPhoto($_GET['vehicle_id']);
    echo $photo['photo'];
}

