<?php

if (!isset($_SESSION['user']) || !$_SESSION['user']->verifyUser()) {
    session_destroy();
    header("Location: login.php");
}