<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 04.05.2019
 * Time: 19:46
 */

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
include 'config.php';

$database = new Database_queries($db);

if (isset($_SESSION['user'])) {
    header('Location: index');
}

if(isset($_GET['code']) && isset($_SESSION['state'])){
    $code = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);
    $state = filter_input(INPUT_GET, 'state', FILTER_SANITIZE_STRING);

    $_SESSION['feide']->checkState($state);
    $accessToken = $_SESSION['feide']->getAccessToken($code);
    $userData = $_SESSION['feide']->getUserData($accessToken);
    $userExists = $database->emailExists($userData['user']['email']);

    if($userExists){
        $userId = $database->getUserId($userData['user']['email']);
        $database->updateFeideUser($userId, $userData['user']['email'], $userData['extendedUserInfo']['givenName'], $userData['extendedUserInfo']['lastName']);


    }
    else{
        $user_id = $database->insertFeideUserToDatabase($userData['user']['userid'], $userData['user']['email'], $userData['extendedUserInfo']['givenName'], $userData['extendedUserInfo']['lastName']);
        // $_SESSION['Tempp'] = $userData;
        $lastPrefId = $database->selectMaxPrefId();
        for ($i = 1; $i <= $lastPrefId; $i++) {
            $database->createUserPreferenses($user_id, $i);
        }
    }

    $userId = $database->getUserId($userData['user']['email']);
    $userObject = $database->showUserData($userId);
    $userObject->setFeideUser(true);

    $_SESSION['user'] = new Session($userData['user']['email'], $userId);
    $_SESSION['userObject'] = $userObject;
    $_SESSION['chats'] = array();

    header('Location: index.php');
}
else {
    $client_id = '600fc951-98bc-4a9c-be3d-052a5ead66b1';
    $client_secret = '93422b48-2a1b-4926-ab3b-78c63312c3a1';
    $redirect_uri = 'http://localhost/Git/carpoolinguit/feideLogin.php';
    $auth = 'https://auth.dataporten.no/oauth/authorization?';
    $token = 'https://auth.dataporten.no/oauth/token';
    $_SESSION['state'] = uniqid('', true);

    $_SESSION['feide'] = new Feide($client_id, $client_secret, $redirect_uri, $auth, $token, 'code');

    $url = $auth . http_build_query(array('client_id' => $client_id, 'redirect_uri' => $redirect_uri, "response_type" => "code", 'state' => $_SESSION['state']));

    header("Location: $url");
}