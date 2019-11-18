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

$database = new Database_queries($db);

$notifications = $database->getNotificationsForUser($_SESSION['user']->getUserID());

if (isset($_POST['saveAlerts'])) {

    $alertString = trim(filter_input(INPUT_POST, 'alertValue', FILTER_SANITIZE_STRING));
    $parts = explode(',', $alertString);
    $id = trim(filter_input(INPUT_POST, 'alertId', FILTER_SANITIZE_NUMBER_INT));

    for ($i = 0; $i < sizeof($parts); $i++) {
        if ($parts[$i]==1)
            $database->setIsCheckedAlert($id, $i+1,1);
        else
            $database->setIsCheckedAlert($id, $i+1, 0);
    }

    $notifications = $database->getNotificationsForUser($_SESSION['user']->getUserID());

    echo $twig->render('varslingSettings.twig', ['alerts' => $notifications, 'alertId' =>$_SESSION['user']->getUserID(), 'isSaved' => true]);

}

else {
    echo $twig->render('varslingSettings.twig', ['alerts' => $notifications, 'alertId' =>$_SESSION['user']->getUserID()]);
}
