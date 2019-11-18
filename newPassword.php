<?php
//TODO fikse det at rec_id mistes (pga form submit og refresh som php fil uten recovery id) hvis inntastet passord er ikke de samme

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});
require_once('config.php');
$database = new Database_queries($db);

$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_SPECIAL_CHARS);

if (isset($_POST['saveNewPass'])) {

    $id = trim(filter_input(INPUT_POST, 'rec_id_pass', FILTER_SANITIZE_STRING));

            $pass_hash = password_hash(trim(filter_input(INPUT_POST, "passChange", FILTER_SANITIZE_SPECIAL_CHARS)), PASSWORD_DEFAULT);

            $database->setNewPassword($id, $pass_hash);

            echo $twig->render('login.twig', ['hide_navbar' => true, 'newPassword' => true]);

    }

else {
    echo $twig->render('newPassword.twig', ['hide_navbar' => true, 'rec_id' => $id]);

}


