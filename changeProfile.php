<?php




if (isset($_POST['saveProfile'])) {

    $updateProfile = new User();
    $car = new Car();

    $updateProfile->setTelNumber(filter_input(INPUT_POST, "telnumber", FILTER_SANITIZE_SPECIAL_CHARS));

    $car->setInfo(filter_input(INPUT_POST, "info", FILTER_SANITIZE_SPECIAL_CHARS));
    $car->setPhoto("photo");
    $car->setNumberOfSeats("numberofseats");

    $blogblog = $blogdb->creatingBlog($newblog);
}

