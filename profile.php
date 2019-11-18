<?php

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});

require_once('config.php');
require ('sendMail.php');

$title = "UiT Samkjøringsportal";

$database = new Database_queries($db);

$profile = $database->showUserData($_SESSION['user']->getUserID());
$userHasJustDeletedCars = $database->userHasJustDeletedCars($_SESSION['user']->getUserID());

//echo $profile->getFeideUser();

if ($database->isUserCarExist($_SESSION['user']->getUserID()))
    $cars = $database->showUsersCarData($_SESSION['user']->getUserID());
else
    $cars = null;

$points = $database->showPoints();

if (isset($_POST['updateUserDataSubmit'])) {

    $updateUserData = new User();

    $tel_number = trim(filter_input(INPUT_POST,'telnumber', FILTER_SANITIZE_NUMBER_INT));
    $name = trim(filter_input(INPUT_POST,'name', FILTER_SANITIZE_STRING));
    $surname = trim(filter_input(INPUT_POST,'surname', FILTER_SANITIZE_STRING));
    $password = trim(filter_input(INPUT_POST,'password', FILTER_SANITIZE_STRING));
    $campus_id = filter_input(INPUT_POST,'campus_id', FILTER_SANITIZE_NUMBER_INT);

    $updateUserData->setUserId($_SESSION['user']->getUserID());
    $updateUserData->setTelNumber($tel_number);
    $updateUserData->setName($name);
    $updateUserData->setSurname($surname);
    $updateUserData->setCampusId($campus_id);
    $updateUserData->setFeideUser($profile->getFeideUser());

    if (($profile->getFeideUser()!=true)) {
        $updateUserData->setPassword(password_hash(trim(filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS)), PASSWORD_DEFAULT));
    }

    $errors = array();
    $maxsize = 2097152;
    $acceptable = array(
        'image/jpeg',
        'image/jpg',
        'image/gif',
        'image/png'
    );

    if (($_FILES['photo']['size'] >= $maxsize) ) {
        $errors[] = 'File too large. File must be less than 2 megabytes.';
    }

    if ((!in_array($_FILES['photo']['type'], $acceptable)) and (!empty($_FILES['photo']['type']))) {
        $errors[] = 'Invalid file type. Only PDF, JPG, GIF and PNG types are accepted.';
    }

    if ($_FILES['photo']['size'] > 0) {
        if (count($errors) == 0) {
            $updateUserData->setPhoto(file_get_contents($_FILES['photo']['tmp_name']));
            $database->updateUserPhoto($updateUserData);
        } else {
            foreach ($errors as $error) {
                echo '<script>alert("' . $error . '");</script>';
            }

            die(); //Ensure no more processing is done
        }
    }


    $database->updateUserData($updateUserData);

    $profile = $database->showUserData($_SESSION['user']->getUserID());

}

if (isset($_POST['updateUserCarSubmit'])) {

    $updateCar = new Car();

    $vehicle_id = filter_input(INPUT_POST,'vehicle_id', FILTER_SANITIZE_NUMBER_INT);
    $info = trim(filter_input(INPUT_POST,'info', FILTER_SANITIZE_STRING));
    $number_of_seats = filter_input(INPUT_POST,'number_of_seats', FILTER_SANITIZE_NUMBER_INT);


    $updateCar->setVehicleId($vehicle_id);
    $updateCar->setInfo($info);
    $updateCar->setNumberOfSeats($number_of_seats);

    $errors = array();
    $maxsize = 2097152;
    $acceptable = array(
        'image/jpeg',
        'image/jpg',
        'image/gif',
        'image/png'
    );

    if (($_FILES['photo']['size'] >= $maxsize) ) {
        $errors[] = 'File too large. File must be less than 2 megabytes.';
    }

    if ((!in_array($_FILES['photo']['type'], $acceptable)) and (!empty($_FILES['photo']['type']))) {
        $errors[] = 'Invalid file type. Only PDF, JPG, GIF and PNG types are accepted.';
    }

    if ($_FILES['photo']['size'] > 0) {
        if (count($errors) == 0) {
            $updateCar->setPhoto(file_get_contents($_FILES['photo']['tmp_name']));
            $database->updateUsersCarPhoto($updateCar);
        } else {
            foreach ($errors as $error) {
                echo '<script>alert("' . $error . '");</script>';
            }

            die(); //Ensure no more processing is done
        }
    }

    $database->updateUsersCarData($updateCar);

    $cars = $database->showUsersCarData($_SESSION['user']->getUserID());

}


if (isset ($_POST['newUserCarSubmit'])) {

    $newCar = new Car();

    $user_id = filter_input(INPUT_POST,'user_id', FILTER_SANITIZE_NUMBER_INT);
    $info = trim(filter_input(INPUT_POST,'info', FILTER_SANITIZE_STRING));
    $number_of_seats = filter_input(INPUT_POST,'number_of_seats', FILTER_SANITIZE_NUMBER_INT);

    $newCar->setUserId($user_id);
    $newCar->setInfo($info);
    $newCar->setNumberOfSeats($number_of_seats);

    $errors = array();
    $maxsize = 2097152;
    $acceptable = array(
        'image/jpeg',
        'image/jpg',
        'image/gif',
        'image/png'
    );

    if (($_FILES['photo']['size'] >= $maxsize) ) {
        $errors[] = 'File too large. File must be less than 2 megabytes.';
    }

    if ((!in_array($_FILES['photo']['type'], $acceptable)) and (!empty($_FILES['photo']['type']))) {
        $errors[] = 'Invalid file type. Only PDF, JPG, GIF and PNG types are accepted.';
    }

    if ($_FILES['photo']['size'] > 0) {
        if (count($errors) == 0) {
            $newCar->setPhoto(file_get_contents($_FILES['photo']['tmp_name']));

        } else {
            foreach ($errors as $error) {
                echo '<script>alert("' . $error . '");</script>';
            }

            die(); //Ensure no more processing is done
        }
    }
    else {
        $tmp_name = "img/no-image-icon-6.png";
        $photo = file_get_contents($tmp_name);
        $newCar->setPhoto($photo);
    }

    $database->createUsersCar($newCar);

    $cars = $database->showUsersCarData($_SESSION['user']->getUserID());
    $userHasJustDeletedCars = $database->userHasJustDeletedCars($_SESSION['user']->getUserID());

}

if (isset($_POST['hideUserCarSubmit'])) {

    $vehicle_id = filter_input(INPUT_POST,'vehicle_id', FILTER_SANITIZE_NUMBER_INT);

    $database->hideUsersCarData($vehicle_id);

    $cars = $database->showUsersCarData($_SESSION['user']->getUserID());
    $userHasJustDeletedCars = $database->userHasJustDeletedCars($_SESSION['user']->getUserID());

}

echo $twig->render('profile.twig', ['title' => $title, 'profile' => $profile, 'cars' => $cars, 'checkUserCars' => $userHasJustDeletedCars, 'points' => $points, 'email' => $_SESSION['user']->getUsername(), 'chats' => $_SESSION['chats']] );
