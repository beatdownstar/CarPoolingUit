<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 03.04.2019
 * Time: 14:53
 */

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');


if(isset($_GET['userCreated'])) {
    echo $twig->render('administratorTools.twig', array('userCreated' => true));
}
else
    echo $twig->render('administratorTools.twig');